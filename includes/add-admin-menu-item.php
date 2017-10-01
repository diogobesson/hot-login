<?php
global $wp_admin_bar, $user_level, $wpdb, $user_ID, $anderson_makiyama;


get_currentuserinfo();

if ($user_level >= 10) {

	return;

}	

$plugin_url = $anderson_makiyama[self::PLUGIN_ID]->plugin_url;
$img_url = $plugin_url . "images/checkmark.png";

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



//-----------Monta Menu Sub Profile
$menu_id = 'produtos-sub';
$wp_admin_bar->add_menu( array(
	'id'    => $menu_id,
	'title' => 'Seus Produtos ' . "<img src='". $img_url ."' class='avatar avatar-26 photo' height='26' width='26'> ",
	'parent' =>'user-actions',
	'href' => '#'
) );

foreach($paginas as $pagina){ //Monta Menu
	 $wp_admin_bar->add_menu(array('id' => 'new-hotlogin-'. $pagina["id_produto"],'parent' => $menu_id, 'title' => $pagina["nome_produto"], 'href' => $pagina["pagina_inicial"], 'meta' => array('target' => '_self')));
	 
}
//----------------------------------


$parente = '';
if(strpos($_SERVER['HTTP_USER_AGENT'], "Mobile") ===false){ //Não é Celular
	$parente = 'top-secondary'; //Muda alinhamento para direita
}	

$sair_url = wp_logout_url();

$menu_id = 'produtos';

$wp_admin_bar->add_menu( array(
	'id'    => $menu_id,
	'title' => 'Acesse seus Produtos ' . "<img src='". $img_url ."' class='avatar avatar-26 photo' height='26' width='26'> ",
	'parent' =>$parente,
	'href' => '#'
) );
	 
foreach($paginas as $pagina){ //Monta Menu
	
	 $wp_admin_bar->add_menu(array('parent' => $menu_id, 'title' => $pagina["nome_produto"], 'id' => 'hotlogin-'.$pagina["id_produto"], 'href' => $pagina["pagina_inicial"], 'meta' => array('target' => '_self')));
	 
}
//Adicionar Sair ao Menu
$wp_admin_bar->add_menu(array('parent' => $menu_id, 'title' => 'Sair', 'id' => 'hotlogin-sair', 'href' => $sair_url, 'meta' => array('target' => '_self')));

?>