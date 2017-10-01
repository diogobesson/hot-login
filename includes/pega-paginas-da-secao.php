<?php
global $wpdb, $anderson_makiyama;

$table_name_secoes = $wpdb->prefix . self::CLASS_NAME . "_secoes";
$table_name_paginas_restritas = $wpdb->prefix . self::CLASS_NAME . "_paginas_restritas";

$conteudo_secao = '';

  //Pega dados da Seção
  
	$esta_secao = $wpdb->get_row( $wpdb->prepare( 
		"
			SELECT nome_secao, desc_secao FROM $table_name_secoes
			where id_secao = %d
		", 
		$sec
	), ARRAY_A );

 //Pega as páginas da Seção
 $paginas_secoes = $wpdb->get_results( $wpdb->prepare( 
	"
		SELECT * FROM $table_name_paginas_restritas
		where id_secao = %d
		ORDER BY ordem
	", 
	$sec 
 ), ARRAY_A );

$conteudo_secao .= '<div class="my-art-content-layout">
					<div class="my-art-content-layout-row">
					<div class="my-art-layout-cell my-layout-item-2" style="width: 100%" >
						<div class="my-art-postmetadataheader-secao"><h1 class="my-art-postheader">'. $esta_secao["nome_secao"] .'</h1>
						<div class="my-desc-secao">'. $esta_secao["desc_secao"] .'</div>
						</div> 
					</div>
					</div>
				</div>';
$conteudo_secao .= '<div class="my-art-content-layout">

<div class="my-art-content-layout-row">

		
<div class="my-art-layout-cell my-layout-item-1" style="width: 100%" >

	<h2 style="padding-bottom: 5px">Conteúdo:</h2><div class="my-image-caption-wrapper2" style="width: 100%;text-align:left;"><p>
	
	';
					
$plugin_url = $anderson_makiyama[self::PLUGIN_ID]->plugin_url;
//$img_url = $plugin_url . "images/checkmarkblue.png";
//$img = "<img src='". $img_url . "' style='vertical-align:middle;'>";


foreach($paginas_secoes as $pagina){

	$wp_page = get_post( $pagina["id_pagina"] ); 
	
	$page_link = get_permalink($wp_page->ID);
	
	$span = "<span class='destaque'>" . $pagina["ordem"] . "</span>";
	
	$conteudo_secao .= '
	
	<p>
	<a href="'. $page_link .'?sec='. $sec .'" class="my-buttons" style="font-size:20px;color:#333;" >'.$span. ' '. $wp_page->post_title .'</a>
	</p>
	
	';

}

$conteudo_secao .= '
				</p></div>
			
				</div>
			
			
				</div>
		
</div><br><br>';	

return $conteudo_secao;	

?>