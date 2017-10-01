<?php
global $anderson_makiyama, $wpdb, $wp_roles;

global $user_level;

get_currentuserinfo();

if ($user_level < 10) { //Limita acesso para somente administradores

	return;

}		

$table_name_produtos = $wpdb->prefix . self::CLASS_NAME . "_produtos"; 
$table_name_usuarios = $wpdb->prefix . self::CLASS_NAME . "_usuarios"; 

$admin_url = get_admin_url();
$admin_url.= 'admin.php?page=' . self::CLASS_NAME . "_Usuarios";

if(isset($_POST["sub_usuario"])){
	

	//Verifica se já não existe o Acesso
	$total_acesso = $wpdb->get_row( $wpdb->prepare( 
		"
			SELECT count(*) as total FROM $table_name_usuarios
			where id_usuario = %d and id_produto = %d
		", 
		$_POST["usuario"],
		$_POST["produto"]
	), ARRAY_A );
	
	if(	$total_acesso["total"] > 0){
		
		echo '<div id="message" class="error">';
		echo '<p><strong>'. 'Acesso já Cadastrado! Ação Cancelada!' . '</strong></p>';
		echo '</div>';	
						
	}else{
		
		$data = date('Y-m-d');
		
		$data_select = $_POST["data_cad"];
		
		switch($data_select){
		
			case "1":
				$data = self::makeData($data, 0,0,-10);
			break;
			case "2":
				$data = self::makeData($data, 0,0,-20);
			break;
			case "3":
				$data = self::makeData($data, 0,0,-30);
			break;
			case "4":
				$data = self::makeData($data, 0,-2,0);
			break;
			case "5":
				$data = self::makeData($data, 0,-3,0);
			break;
			case "6":
				$data = self::makeData($data, 0,-6,0);
			break;
			case "7":
				$data = self::makeData($data, -1,0,0);
			break;	
			
		}
		
		$wpdb->query( $wpdb->prepare( 
			"
				INSERT INTO $table_name_usuarios
				(id_usuario, id_produto, data_acesso )
				VALUES ( %d, %d, %s )
			", 
			$_POST['usuario'],
			$_POST['produto'],
			$data 
		) );	
		

		echo '<div id="message" class="updated">';
		echo '<p><strong>'. 'Acesso Cadastrado com Sucesso!' . '</strong></p>';
		echo '</div>';	
				
	}
	
}elseif(isset($_GET["udel"]) && isset($_GET["pdel"])){


	$wpdb->query( $wpdb->prepare( 
		"
			DELETE from $table_name_usuarios
			where id_usuario = %d and id_produto = %d
		", 
		$_GET['udel'],
		$_GET['pdel'] 
	) );
		
	echo '<div id="message" class="updated">';
	echo '<p><strong>'. 'Acesso Excluído com Sucesso!' . '</strong></p>';
	echo '</div>';	
	
}


//Pega Produtos
$produtos = $wpdb->get_results( 
"
	SELECT * FROM $table_name_produtos
	order by nome_produto ASC
",ARRAY_A
);
	

//Pega Acessos
$acessos = $wpdb->get_results( 
"
	SELECT tb_usu.*, tb_prod.* FROM $table_name_usuarios tb_usu inner join $table_name_produtos tb_prod on tb_prod.id_produto = tb_usu.id_produto
	order by tb_prod.nome_produto ASC
",ARRAY_A
);

//$wpdb->show_errors();	

//$wpdb->print_error();
			
			
//echo "<pre>";print_r($acessos);exit;

//Pega Usuarios				
$users = get_users( 'orderby=nicenamer' );

?>
<div class="wrap">
<div class="icon32"><img src='<?php echo plugins_url('/images/icon-32.png', dirname(__FILE__))?>' /></div>
        
<h2>Usuários <?php echo self::PLUGIN_NAME?>:</h2>
    
  		<table width="100%"><tr>
        <td style="vertical-align:top">
        

  		<form action="" method="post">
        
        <div class="metabox-holder">         
		<div class="postbox" >
        
        	<h3>Dar acesso a um usuário a um Produto</h3>
        
        	<div class="inside">
                           
                
              <p>
              Selecione o Usuário 
              <select name="usuario">
				<?php 
				foreach ( $users as $user ): 
				
					if($user->data->ID == 1) continue;
				?>
                <option value="<?php echo $user->data->ID; ?>"><?php echo $user->data->user_nicename; ?></option>
                <?php endforeach; ?>
                </select>
              
              </p>
 
               <p>
              Selecione o Produto 
              <select name="produto">
				<?php 
				

				
				foreach ( $produtos as $produto ): 
				?>
                <option value="<?php echo $produto["id_produto"]; ?>" ><?php echo $produto["nome_produto"]; ?></option>
                <?php endforeach; ?>
                </select>
               </p>
               
              <p>
              Data do Acesso 
              <select name="data_cad">

                <option value="0">Hoje</option>
                <option value="1">10 dias atrás</option>
                <option value="2">20 dias atrás</option>
                <option value="3">30 dias atrás</option>
                <option value="4">2 meses atrás</option>
                <option value="5">3 meses atrás</option>
                <option value="6">6 meses atrás</option>
                <option value="7">1 ano atrás</option>

                </select>
              
              </p>
              
                             
               <p>
                 <input type="submit" name="sub_usuario" value="Dar Acesso" class="myButton" />
				</p>
                               
   			  </div>
		</div>
        </div>
        
 		</form>
<form action="" method="post">
 		<div class="metabox-holder">
 		<div class="postbox">
        
        	<h3>Acessos já Cadastrados</h3>
        
        	<div class="inside">
            	<p>
                <table id="listagem-tables" class="display" cellspacing="0" width="100%">
                <thead>
				<tr>
				<th>
				Usuário
				</th>          
				<th>
				Produto
				</th>
                <th>
				Data
				</th>
                <th>
                </th>               
				</tr>  
                </thead>         
                <tbody>                         
				<?php
				
					
				foreach($acessos as $acesso){
					
					$usuario_nm = get_userdata($acesso["id_usuario"]);
					$usuario_nm = $usuario_nm->user_nicename;
					
					echo "<tr>";
					echo "<td class='center'>";
					echo $usuario_nm;
					echo "</td>";						
					echo "<td class='center'>";
					echo $acesso["nome_produto"];
					echo "</td>";
					echo "<td class='center'>";
					echo self::get_data_pt($acesso["data_acesso"]);
					echo "</td>";					
					echo "<td class='center'>";
					echo "<a href='".$admin_url."&udel=".$acesso["id_usuario"]."&pdel=".$acesso["id_produto"]."' class='button-secondary'>Remover Acesso</a>";	
					echo "</td>";
					echo "</tr>";
					
				}
				
				?>
                </tbody>
                <tfoot> 
				<tr>
				<th>
				Usuário
				</th>           
				<th>
				Produto
				</th>
               <th>
				Data
				</th> 
                <th>
                </th>               
				</tr>  
                </tfoot>                 
                </table>
                </p>

			</div>
		</div>
        </div>
        
                
        </form>
        
                 
   		</td>
        <td width="320" style="vertical-align:top; width:320px">

        

        <div class="metabox-holder">

		<div class="postbox" >


        	<div class="inside my-fundo-black">
            
           <center>
            <a href="http://hotplus.net.br" target="_blank"><img src="<?php echo $anderson_makiyama[self::PLUGIN_ID]->plugin_url?>images/hotlinks.jpg" /></a>
			</center>



			</div>

 
 		</div>
        </div>      

       </td>

       </tr>

       </table>





<hr />





<table>

<tr>

<td>

<img src="<?php echo $anderson_makiyama[self::PLUGIN_ID]->plugin_url?>images/anderson-makiyama.png" />

</td>

<td>

<ul>

<li>Autor: <strong>Anderson Makiyama</strong>

</li>

<li>Email do Autor: <a href="mailto:andersonmaki@gmail.com" target="_blank">andersonmaki@gmail.com</a>

</li>

<li>Visite a Página do Plugin: <a href="<?php echo self::PLUGIN_PAGE?>" target="_blank"><?php echo self::PLUGIN_PAGE?></a>


</li>

<li>

Visite o Site do Autor: <a href="http://<?php echo self::AUTHOR_SITE;?>" target="_blank"><?php echo self::AUTHOR_SITE;?></a>

</li>

</ul>

</td>

</tr>

</table>



</div>
<script>

jQuery(document).ready(function($) {
     $('#listagem-tables').dataTable( {
        "order": []
    } );
});
</script>