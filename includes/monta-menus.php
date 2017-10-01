<?php
global $wpdb;


$table_name_produtos = $wpdb->prefix . self::CLASS_NAME . "_produtos";
$table_name_secoes = $wpdb->prefix . self::CLASS_NAME . "_secoes";
$table_name_paginas_restritas = $wpdb->prefix . self::CLASS_NAME . "_paginas_restritas";


$sec = isset($_GET["sec"])?(int)$_GET["sec"]:0;
$prod = '';
$pagina_inicial = '';

if($sec==0){
	
	//Pega url da pagina atual e verifica se é página inicial de algum produto
	$this_page_url = get_permalink($post->ID);
	
	
	$produto = $wpdb->get_row( $wpdb->prepare( 
		"
			SELECT id_produto,nome_produto,desc_produto FROM $table_name_produtos
			where pagina_inicial = %s
		", 
		$this_page_url
	), ARRAY_A );
	
	if($produto){
	
		$prod = $produto["id_produto"];	
		$pagina_inicial = $produto["pagina_inicial"];
	}
	

}else{ //Pega ID do produto da Seção
	
	$produto = $wpdb->get_row( $wpdb->prepare( 
		"
			SELECT tb_sec.id_produto, tb_prod.pagina_inicial FROM $table_name_secoes tb_sec 
			inner join $table_name_produtos tb_prod on tb_prod.id_produto = tb_sec.id_produto
			where tb_sec.id_secao = %d
		", 
		$sec
	), ARRAY_A );
	
	if($produto){
	
		$prod = $produto["id_produto"];	
		$pagina_inicial = $produto["pagina_inicial"];
	}
	
}

if(empty($prod)) return "";


 //Monta Menus
 $secoes = $wpdb->get_results( $wpdb->prepare( 
	"
		SELECT * FROM $table_name_secoes
		where id_produto = %d
		ORDER BY ordem
	", 
	$prod
 ), ARRAY_A );

$menus = '<div class="'.$classe.'">
<ul id="my-menu-topo" class="'. $classe .'">
<li class="menu-item menu-inicio">
	<a href="'. $pagina_inicial .'">Início</a>
</li>
';


foreach($secoes as $sec){

	$menus.= '<li class="menu-item">
		<a href="'. $pagina_inicial . '?sec=' . $sec["id_secao"] .'">'. $sec["nome_secao"] .'</a>';
		
	$menus .= self::get_submenus($sec["id_secao"]);
	
	$menus .= '</li>';			
}
	 
$menus.= '	
</ul>
</div>
';	

return $menus;
?>