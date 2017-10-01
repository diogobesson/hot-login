<?php

global $anderson_makiyama;

global $user_level;

get_currentuserinfo();


if (function_exists('add_options_page')) { //Adiciona pagina na seção Configurações
	
	add_options_page(self::PLUGIN_NAME, self::PLUGIN_NAME, 1, self::CLASS_NAME, array(self::CLASS_NAME,'produtos_page'));

}
if (function_exists('add_submenu_page')){ //Adiciona pagina na seção plugins
	
	add_submenu_page( "plugins.php",self::PLUGIN_NAME,self::PLUGIN_NAME,1, self::CLASS_NAME, array(self::CLASS_NAME,'options_page'));			  
}

 add_menu_page(self::PLUGIN_NAME, self::PLUGIN_NAME,1, self::CLASS_NAME,array(self::CLASS_NAME,'options_page'),$anderson_makiyama[self::PLUGIN_ID]->plugin_url .'/images/icon.png');
 
 add_submenu_page(self::CLASS_NAME, self::PLUGIN_NAME,'Produtos',1, self::CLASS_NAME . "_Produtos", array(self::CLASS_NAME,'produtos_page'));
  
 add_submenu_page(self::CLASS_NAME, self::PLUGIN_NAME,'Usuários',1, self::CLASS_NAME . "_Usuarios", array(self::CLASS_NAME,'usuarios_page'));
 
 add_submenu_page(self::CLASS_NAME, self::PLUGIN_NAME,'Acessos Programados',1, self::CLASS_NAME . "_Acessos_Automaticos", array(self::CLASS_NAME,'acessos_automaticos_page'));
 
 add_submenu_page(self::CLASS_NAME, self::PLUGIN_NAME,'Preços Alternativos',1, self::CLASS_NAME . "_Precos_Alternativos", array(self::CLASS_NAME,'precos_alternativos_page'));
 
 add_submenu_page(self::CLASS_NAME, self::PLUGIN_NAME,'Opções do Tema',1, self::CLASS_NAME . "_Opcoes_Tema", array(self::CLASS_NAME,'options_tema_page'));
 
 add_submenu_page(self::CLASS_NAME, self::PLUGIN_NAME,'Relatório Logins',1, self::CLASS_NAME . "_Relatorio", array(self::CLASS_NAME,'relatorio_logins'));

//Remove Jetpack para não administradores
if ( class_exists( 'Jetpack' ) && $user_level <10 ) {
	remove_menu_page( 'jetpack' );
}
?>