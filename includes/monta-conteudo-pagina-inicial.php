<?php
global $wpdb, $user_level, $post, $anderson_makiyama;


$options = get_option(self::CLASS_NAME . "_options");
$restricao = $options["restricoes"];

if($restricao == 3) return $content;
	
$table_name_produtos = $wpdb->prefix . self::CLASS_NAME . "_produtos";
$table_name_secoes = $wpdb->prefix . self::CLASS_NAME . "_secoes";
$table_name_paginas_restritas = $wpdb->prefix . self::CLASS_NAME . "_paginas_restritas";

get_currentuserinfo();


if ($user_level >= 10) { //Usuário é administrador

	//return $content;

}

//Pega url da pagina atual e verifica se é página inicial de algum produto
$this_page_url = get_permalink($post->ID);

$this_page_url2 = explode("/",$this_page_url);

$this_page_url2 = end($this_page_url2);

$this_page_url2 = "" == $this_page_url2?substr($this_page_url, 0, strlen($this_page_url)-1):$this_page_url . "/";



$produto = $wpdb->get_row( $wpdb->prepare( 
	"
		SELECT id_produto,nome_produto,desc_produto FROM $table_name_produtos
		where pagina_inicial = %s OR pagina_inicial = %s
	", 
	$this_page_url,
	$this_page_url2
), ARRAY_A );

$sec = isset($_REQUEST["sec"])?(int)$_REQUEST["sec"]:0;



if(count($produto) >0 || $sec != 0){//Só entra se for conteúdo Restrito

	$conteudo = '';
	$conteudo_secao = '';

	if(!is_single() && !is_page()) return "<h2>Conteúdo Restrito, Faça login para ter acesso!</h2>";
	
	if($sec != 0 ){ //É página de seção ou página de conteúdo.
	
		if(count($produto) ==0){ //É página de conteúdo, pois a página da seção é montada na página do produto
				
			 $conteudo .= self::get_content($content); //Pega conteúdo conforme data da liberação
			 //$conteudo .= "<br><hr><br>" . self::pega_paginas_da_secao($sec); //Junta as seções abaixo do conteúdo
			 
			 return $conteudo;
		}
		
		//É página de Seção
		$conteudo_secao = self::pega_paginas_da_secao($sec);
				
		return  $conteudo_secao;
	}
	
	//Se entrou aqui é Página Inicial e $sec é igual a 0, logo, id produto existe
	return $content . '<br><hr><br>'. self::get_conteudo_do_produto($produto);
	
}

return self::get_content($content);
?>