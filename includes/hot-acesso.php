<?php
global $anderson_makiyama, $wpdb; 

$options = get_option(self::CLASS_NAME . "_options");

$nivel = $options["tipo_usuario"];

$table_name_usuarios = $wpdb->prefix . self::CLASS_NAME . "_usuarios";
$table_name_produtos = $wpdb->prefix . self::CLASS_NAME . "_produtos";
$table_name_acessos_automaticos = $wpdb->prefix . self::CLASS_NAME . "_acessos_automaticos";

$url = $_SERVER["REQUEST_URI"];

if(strpos($url,'/hotlogin.php') === false) return;



$msg_erro = "<html><head><meta charset='utf-8' /><style>body, ol li{font-size:24px;text-align:center;margin:10px;}</style></head><body><p><h1>Houve um erro no Login! Veja os Principais Motivos abaixo:</h1></p>
<p>
<ol>
<li>
	Verifique se você está fazendo login utilizando a mesma conta em que efetuou a compra!<br>
	Para ter certeza, faça login no <a href='http://hotmart.com.br' target='_blank'>Hotmart.com.br</a> e Verifique se aparece a compra na sua conta.
</li>
<li>
	Tente Acessar usando outro navegador Web, como Firefox ou Chrome<br>
	Ás vezes pode ser algum conflito de cache, e usando outro navegador pode resolver<br>
</li>
</ol>
</p>
<h2>Após Verificar que você está entrando com a conta Correta, refaça o Login</h2>
<p><a href='". $my_url ."hotlogin.php'>Tentar fazer Login Novamente</a></p></body></html>
";


	
$hotmart_server = "http://api.hotmart.com.br";
$app_id = $options["app_id"]; //Codigo que identifica sua aplicacao para o hotmart (fornecida pela hotmart)
$app_secret = $options["app_secret"]; //Senha gerada para sua aplicacao (fornecida pela hotmart)
$site_url = get_site_url(); $my_url = $site_url . "/hotlogin.php"; //URL de redirecionamento apos autenticacao no login hotmart e a geracao do code 

session_start("hotmart_Session");
//Este codigo pode ser utilizando como um include ou filtro onde eh necessario estar autenticado
//verifica se o code (codigo de acesso) esta na requisicao (ou sessao caso prefira manter o controle do code)
$code = isset($_REQUEST["code"])?$_REQUEST["code"]:'';

if(empty($code)) {
	//caso nao possua o code, chama a autorizacao para obter novo code
	//Essa requisicao deve ser via GET
	$_SESSION['state'] = md5(uniqid(rand(), TRUE)); //CSRF protection (http://pt.wikipedia.org/wiki/Cross-site_request_forgery)
	//echo($_SESSION['state']);
	$dialog_url = $hotmart_server . "/oauth/authorize?client_id=" 
	. $app_id . "&redirect_uri=" . urlencode($my_url) . "&state="
	. $_SESSION['state'] . "&response_type=code";
	
	wp_logout();
	
	echo("<script> top.location.href='" . $dialog_url . "'</script>");exit;
	
}


if($_REQUEST['state'] == $_SESSION['state']) {
	//De posse do code, agora obtem o token. Deve-se requisitar o token via POST
	//http://wezfurlong.org/blog/2006/nov/http-post-from-php-without-curl/ (Referencia para requisicoes via post)
	$token_url = $hotmart_server . "/oauth/access_token";
	//conforme referencia? caso apresente problemas com post onde parece nao ter passado os parametros, utilizar a url com '/' no final: $token_url = "http://api.hotmart.com.br/oauth/access_token/";
	$post_params =   array("client_id" => $app_id ,"redirect_uri" => urlencode($my_url),"client_secret" => $app_secret, "code" => $code);
   
	//referencia json: http://php.net/manual/pt_BR/function.json-decode.php
	
	
	$response_token = json_decode(self::do_post_request($token_url,$post_params));
	
	/**
	Exemplo retorno requisicao de Token
	- {"TokenResponse":{"access_token":"d949f8f614a1793f178c4395c67d508e","expires_in":7200000}}
	*/
	$token = $response_token->{'TokenResponse'}->{'access_token'};
	
	//Com o token agora eh possivel consultar os servicos hotmart para obter informacoes sobre o usuario
	 
	 
	 $post_params =   array("access_token" => $token);
	 $url = 'http://api.hotmart.com.br/user_info';
	 $response_user_info = json_decode(self::do_post_request($url,$post_params));
	 
	 $id_comprador = $response_user_info->UserInfoResponse->id;
	 $nome_comprador = $response_user_info->UserInfoResponse->name;
	 
	
	//Verifica status da compra
	 
	 $post_params =   array("access_token" => $token);
	 $url = 'http://api.hotmart.com.br/purchases';
	 $response_compra_info = json_decode(self::do_post_request($url,$post_params));
	 
	 $status = "started";
	 $email_comprador = "";
	 
	 
	 /*
	 //Pega todos os ids de produtos que o usuário tem Acesso
	 $paginas = $wpdb->get_results( $wpdb->prepare( 
		"
			SELECT * FROM $table_name_usuarios
			where id_usuario = %d
			ORDER BY 'ordem' ASC
		", 
		$_REQUEST["pid"] 
	 ), ARRAY_A );
	 */
				 
	 $id_produto = '';
	 
	 $produtos_comprados = array();
	 $produtos_comprados_so_produtos = array();
	 
	 foreach($response_compra_info->Purchases as $purchase){
	 
		 if(is_array($purchase->PurchaseResponse)){
			 
			 $email_comprador = $purchase->PurchaseResponse[0]->email;
			 
			 foreach($purchase->PurchaseResponse as $response){
				 
				 if($response->status == 'approved' || $response->status == 'completed'){
					 
					$produtos_comprados[$response->idProduct] = $response->paymentReceived;
					$produtos_comprados_so_produtos[] = $response->idProduct;
					 
				 }
				 
			 }
			 
		 }elseif($purchase->PurchaseResponse->status == 'approved' || $purchase->PurchaseResponse->status == 'completed'){
			
			$produtos_comprados[$purchase->PurchaseResponse->idProduct] = $purchase->PurchaseResponse->paymentReceived; 
			$email_comprador = $purchase->PurchaseResponse->email;
			$produtos_comprados_so_produtos[] = $purchase->PurchaseResponse->idProduct;
			
		 }
	 
	 }
	 
		 
	 if(count($produtos_comprados) > 0){
		 
		 $in = implode(",",$produtos_comprados_so_produtos);
		 
		 //Verifica se o produto foi cadastrado no site pelo Administrador 
		 $produtos_cadastrados = $wpdb->get_results( 
			"
				SELECT * FROM $table_name_produtos
				where id_produto in(". $in .")
			"
			, ARRAY_A );	 
		 
		 $link = "";
		 

		 //Cadastra usuario
		 if(!username_exists( 'hot_' . $id_comprador )){
			 
			 $id_usuario = self::cadastra_usuario($id_comprador,$nome_comprador, $email_comprador, $nivel);
			
		 }else{
			
			$id_usuario = get_user_by( 'login', 'hot_' . $id_comprador );
			$id_usuario = $id_usuario->ID;
			 
		 }
		 //
		
	
		
		$quantos_produtos_tem_acesso = 0;
		
		foreach($produtos_cadastrados as $produto_cadastrado){
			
			$quantos_produtos_tem_acesso++;
			
			$id_produto = self::cadastra_acesso($id_usuario,$produto_cadastrado["id_produto"],$produtos_comprados[$produto_cadastrado["id_produto"]]); //Da acesso aos produtos
			
			if($id_produto != $produto_cadastrado["id_produto"]){//pula acessos automaticos

				if(count($produtos_cadastrados)==1){//Pega Nova Página Inicial
					$nova_pagina_inicial = $wpdb->get_row( $wpdb->prepare( 
						"
							SELECT pagina_inicial FROM $table_name_produtos
							where id_produto = %d
						", 
						$id_produto
					), ARRAY_A );
							
					$link = $nova_pagina_inicial["pagina_inicial"];
				}
			}else{
			
				$link = $produto_cadastrado["pagina_inicial"];
			
				self::da_acessos_programados($produto_cadastrado["id_produto"], $id_usuario); //Cadastra Acessos Automaticos
			
			}
			
		}
		 

		//Envia email para o admin
		$email_admin = get_option("admin_email");
		$site_name = get_option("blogname");
		
		$mensagem = '<p>Usuário Fez Login no site: '. $site_name  . '</p>';
		$mensagem.= "<strong>Nome:</strong> " . $nome_comprador . "<br>";
		$mensagem.= "<strong>Email:</strong> " . $email_comprador . "<br>";
		$mensagem.= "<strong>Data:</strong> " . date('d/m/Y H:i:s');
		$mensagem.= "<p><hr>Mensagem enviada pelo plugin <strong>Hot Login Plus</strong></p>";
		
		$subject = 'Usuário Fez Login no site: '. $site_name;
		$headers = array('Content-Type: text/html; charset=UTF-8');
		wp_mail( $email_admin, $subject, $mensagem, $headers);
		
		//
		
		//Guarda Histórico de login
		$anderson_makiyama[self::PLUGIN_ID]->log_logins($nome_comprador,'Sucesso no Login',$options);
		//
		
		if(!session_id()) session_start();
		//
		$_SESSION["Anderson_Makiyama_Captcha_On_Login_code"] = "123";
		$_POST['codigo'] = '123';
		//
		
		
		
		//Faz Login no WP			
		if(self::programmatic_login('hot_' . $id_comprador)){
			
			if($quantos_produtos_tem_acesso>1){//Mais de um Produto Comprado
				
				$link = get_admin_url();
			
			}
			
			$pagina = '
			<!DOCTYPE html>
			<html>
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title>Aguarde...</title>
			<meta name="description" content="Por favor, aguarde...">
			';
			
			$pagina .= '<meta http-equiv="refresh" content="0;url='. $link .'">
			</head>
			<body>
			<p>
			<center>
			<img src="'. $site_url .'images/ajax-loader.gif">
			</center>
			</p>
			<p>
			<center>
			<h2>Efetuando Login! Aguarde...</h2>
			</center>
			</p>
			</body>
			</html>
			';	
			
			echo $pagina;			
			exit;						
		
		}else{
			
			echo $msg_erro;
			exit;
			
		}
		
		
	 }else{


		//Envia email para o admin avisando de erro no login
		$email_admin = get_option("admin_email");
		$site_name = get_option("blogname");
		
		$retorno = serialize($response_compra_info);
		
		$mensagem = '<p>Um usuario tentou fazer Login no site: '. $site_name  . '</p>';
		$mensagem.= "<strong>Nome:</strong> " . $nome_comprador . "<br>";
		$mensagem.= "<p>Usuário não conseguiu fazer login, pois parece que não adquiriu o produto ou o pgto ainda não foi aprovado!</p><br>";
		$mensagem.= "<hr><strong>Retorno:</strong> " . $retorno . "<br>";
		$mensagem.= "<p><hr>Mensagem enviada pelo plugin <strong>Hot Login Plus</strong></p>";
		
		$subject = 'Alguém Tentou Fazer Login no site: '. $site_name;
		$headers = array('Content-Type: text/html; charset=UTF-8');
		wp_mail( $email_admin, $subject, $mensagem, $headers);
		
		//	
		
		//Guarda Histórico de login
		$anderson_makiyama[self::PLUGIN_ID]->log_logins($nome_comprador,'Falha no Login',$options);
		//
				 
		echo $msg_erro;
		exit;
		 
	 }


}
else {
	echo $msg_erro . 'The state does not match. You may be a victim of CSRF.';
	exit;
}

?>