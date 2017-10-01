<?php
global $wpdb, $user_level, $user_ID, $post;

get_currentuserinfo();

//Da acesso aos admins
//if($user_level >= 10) return $content; 


$table_name_produtos = $wpdb->prefix . self::CLASS_NAME . "_produtos";
$table_name_usuarios = $wpdb->prefix . self::CLASS_NAME . "_usuarios";
$table_name_secoes = $wpdb->prefix . self::CLASS_NAME . "_secoes";
$table_name_paginas_restritas = $wpdb->prefix . self::CLASS_NAME . "_paginas_restritas";

 //Pega tipo de liberação desse conteúdo, se houver 2 produtos com o conteúdo, considera
 //aquele que libera primeiro 
 $liberacoes = $wpdb->get_results( $wpdb->prepare( 
	"
		SELECT tb_page.liberacao, tb_usu.data_acesso FROM $table_name_paginas_restritas tb_page 
		inner join $table_name_secoes tb_sec on tb_sec.id_secao = tb_page.id_secao
		inner join $table_name_usuarios tb_usu on tb_usu.id_produto = tb_sec.id_produto
		where tb_usu.id_usuario = %d and tb_page.id_pagina = %d
		ORDER BY tb_page.liberacao
	", 
	$user_ID, $post->ID
 ), ARRAY_A );

if(!$liberacoes) return $content;


						
if($liberacoes[0]["liberacao"] == 0) return $content;	//Liberar imediatamente	
	
//Se não retornou, precisa verificar a data de acesso do usuário
 $data_acesso = $liberacoes[0]["data_acesso"];
			
	
$hoje = date('Y-m-d');

$message_befor = "<div class='my-awaiting'>Este Conteúdo Será Liberado para Você no Dia ";

$message_after = "<br><br>Seja Paciente!</div>";


$data_liberacao = self::makeData($data_acesso,0,0,$liberacoes[0]["liberacao"]);
	
if($data_liberacao > $hoje){ 
	return $message_befor . self::get_data_pt($data_liberacao). $message_after; 
}else{
	return $content;	
}


?>