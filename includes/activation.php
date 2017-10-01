<?php
global $wpdb;

$options = get_option(self::CLASS_NAME . "_options");
if(!isset($options["codigo"])){
	
	$options["codigo"] = "";
}

$options_2 = get_option('Anderson_Makiyama_Hot_Login_options');

//Tenta pegar códigos da versão anterior
if(!isset($options['app_id']) || empty($options['app_id']) ){

	$options['app_id'] = isset($options_2['app_id'])?$options_2['app_id']:'';
	$options['app_secret'] = isset($options_2['app_secret'])?$options_2['app_secret']:'';
	$options['restricoes'] = isset($options_2['restricoes'])?$options_2['restricoes']:3;
	$options['tipo_usuario'] = isset($options_2['tipo_usuario'])?$options_2['tipo_usuario']:'subscriber';
	$options["last_100_logins"] = (!isset($options["last_100_logins"]) || count($options["last_100_logins"])==0)?(isset($options_2["last_100_logins"])?$options_2["last_100_logins"]:array()):$options["last_100_logins"];
	//if(!isset($options['tipo_usuario'])) $options['tipo_usuario'] = 'subscriber';
}


update_option(self::CLASS_NAME . "_options",$options);

	
$table_name = $wpdb->prefix . self::CLASS_NAME . "_secoes";
$table_name_produtos = $wpdb->prefix . self::CLASS_NAME . "_produtos"; 
$table_name_paginas_restritas = $wpdb->prefix . self::CLASS_NAME . "_paginas_restritas"; 
$table_name_usuarios = $wpdb->prefix . self::CLASS_NAME . "_usuarios"; 
$table_acessos_automaticos = $wpdb->prefix . self::CLASS_NAME . "_acessos_automaticos";
$table_precos_alternativos = $wpdb->prefix . self::CLASS_NAME . "_precos_alternativos";

//Cria Tabela Secoes	
$sql = "CREATE TABLE $table_name (
  id_secao mediumint(9) NOT NULL AUTO_INCREMENT,
  ordem int(11) NOT NULL,
  id_produto int(11) NOT NULL,
  nome_secao tinytext NOT NULL,
  desc_secao tinytext NOT NULL,
  UNIQUE KEY id_secao (id_secao)
);";

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

dbDelta( $sql );
	

//Cria Tabela Produtos
$sql = "CREATE TABLE $table_name_produtos (
  id_produto int(11) NOT NULL,
  nome_produto tinytext NOT NULL,
  pagina_inicial tinytext NOT NULL,
  desc_produto mediumtext,
  UNIQUE KEY id_produto (id_produto)
);";

dbDelta( $sql );


//Cria Tabela Paginas Restritas
$sql = "CREATE TABLE $table_name_paginas_restritas (
  id_secao int(11) NOT NULL,
  id_pagina int(11) NOT NULL,
  ordem int(11) NOT NULL,
  liberacao int(11) NOT NULL
);";

dbDelta( $sql );


//Cria Tabela Usuários
$sql = "CREATE TABLE $table_name_usuarios (
  id_produto int(11) NOT NULL,
  id_usuario int(11) NOT NULL,
  data_acesso date NOT NULL
);";

dbDelta( $sql );

//Cria Tabela Acessos Automáticos
$sql = "CREATE TABLE $table_acessos_automaticos (
  id_produto_master int(11) NOT NULL,
  id_produto_slave int(11) NOT NULL,
  validade mediumint(9) NOT NULL
);";

dbDelta( $sql );

//Cria Tabela Preços Alternativos
$sql = "CREATE TABLE $table_precos_alternativos (
  id_produto_principal int(11) NOT NULL,
  vl_alternativo varchar(25) NOT NULL,
  id_produto_alternativo int(11) NOT NULL
);";

dbDelta( $sql );

				
//Verifica existencia de Produtos
$produtos = $wpdb->get_row(  
"
	SELECT count(*) as total FROM $table_name_produtos
", ARRAY_A );


//Adiciona Produtos
if($produtos["total"]<1){


	//Verifica se Existe produto da Versão do Hotlogin Anterior
	
	
	if(isset($options_2['id_produto']) && !empty($options_2['id_produto'])){
	
		$wpdb->query( $wpdb->prepare( 
			"
			INSERT INTO $table_name_produtos
			(id_produto, nome_produto, pagina_inicial, desc_produto )
			VALUES ( %d, %s, %s, %s )
			", 
			$options_2['id_produto'], 
			'Produto 01', 
			$options_2['pagina_inicial'], 
			'Descritivo do Produto' 
		) );
		

		//Verifica existencia de usuarios
		$usuarios = $wpdb->get_row(  
		"
			SELECT count(*) as total FROM $table_name_usuarios
		", ARRAY_A );	
		
		if($usuarios["total"] <1){ //Nenhum usuário foi adicionado ainda
			
			//Tenta cadastrar todos os usuários _hot
			$users = get_users();
			
			foreach ( $users as $user ): 
			
				$login = $user->data->user_login;
				$id = $user->ID;
				$part_login = substr($login,0,4);
				
				if("hot_" == $part_login){ //É usuário Adicionado pelo Hotconnect
				
					$wpdb->query( $wpdb->prepare( 
						"
							INSERT INTO $table_name_usuarios
							(id_usuario, id_produto )
							VALUES ( %d, %d )
						", 
						$id,
						$options_2['id_produto']
					) );	
		
			
				}
				
			endforeach;
			
		}
	
						
		
	}
	//$id = $wpdb->insert_id;
	

																
}

?>