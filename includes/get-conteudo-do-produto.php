<?php
global $wpdb;

$table_name_produtos = $wpdb->prefix . self::CLASS_NAME . "_produtos";
$table_name_secoes = $wpdb->prefix . self::CLASS_NAME . "_secoes";
$table_name_paginas_restritas = $wpdb->prefix . self::CLASS_NAME . "_paginas_restritas";

$conteudo = "";
		
 //Pega as seções do produto
 $secoes = $wpdb->get_results( $wpdb->prepare( 
	"
		SELECT * FROM $table_name_secoes
		where id_produto = %d
		ORDER BY ordem
	", 
	$produto["id_produto"] 
 ), ARRAY_A );
 
 
$conteudo .= '

<div class="my-art-content-layout">
	<div class="my-art-content-layout-row">
	<div class="my-art-layout-cell my-layout-item-2" style="width: 100%" >
		<div class="my-art-postmetadataheader"><h1 class="my-art-postheader">'. $produto["nome_produto"] .'</h1>
		<div class="my-desc-secao">'. $produto["desc_produto"] .'</div>
		</div> 
	</div>
	</div>
</div>

<div class="my-art-content-layout">
';
$cont_secoes = 0;

foreach($secoes as $secao){
	$cont_secoes++;
	$len_ordem = strlen($secao["ordem"]);
	
	$percent = '35%';
	switch($len_ordem){
	
		case 2:
			$percent = '52%';
		break;
		case 3:
			$percent = '68%';
		break;	
	}
	
	if($cont_secoes % 2 !=0){
		$conteudo.= '    <div class="my-art-content-layout-row">

				<div class="my-art-layout-cell-link my-layout-item-0" style="width: 50%" >
			<a href="'. $this_page_url . '?sec=' . $secao["id_secao"] .'" class="anc-secoes">
					<h2 style="border-bottom: 1px solid #E0E0E0; padding-bottom: 5px">'. $secao["nome_secao"] .'</h2><div class="image-caption-wrapper" style="width: '. $percent .'; align: left"><p style="padding-right:29px;padding-bottom:40px;padding-top:42px;float:left; font-size:120px;color: #302f2f;text-shadow: 2px 2px 5px #777777;margin-top: -7px"><span class="destaque-produto">'. $secao["ordem"] .'</span></p></div><p class="my-secao-desc" >'. $secao["desc_secao"] .'</p>
			</a>
				</div>';

	}else{

		$conteudo.= '<div class="my-art-layout-cell-link my-layout-item-1" style="width: 50%" >
	<a href="'. $this_page_url . '?sec=' . $secao["id_secao"] .'" class="anc-secoes">
			<h2 style="border-bottom: 1px solid #E0E0E0; padding-bottom: 5px">'. $secao["nome_secao"] .'</h2><div class="image-caption-wrapper" style="width: '. $percent .'; align: left"><p style="padding-right:29px;padding-bottom:40px;padding-top:42px;float:left; font-size:120px;color: #302f2f;text-shadow: 2px 2px 5px #777777;margin-top: -7px"><span class="destaque-produto">'. $secao["ordem"] .'</span></p></div><p class="my-secao-desc" style="display:box;">'. $secao["desc_secao"] .' </p>
	</a>
		</div>
	
	
		</div>';
	}

}


if($cont_secoes % 2 != 0){ //Completa o codigo

	$conteudo.= '<div class="my-art-layout-cell my-layout-item-1" style="width: 50%" >
			<h2 ></h2><div class="image-caption-wrapper" style="width: 35%; align: left"><p style="padding-right:29px;padding-bottom:40px;padding-top:42px;float:left; font-size:120px;color: #A82110;text-shadow: 2px 2px 5px #777777;margin-top: -7px"></p></div><p></p>
	</a>
		</div>
		</div>';			
	
}


$conteudo.= '</div>';


return $conteudo;
?>