<?php
global $anderson_makiyama, $wpdb, $wp_roles;

global $user_level;

get_currentuserinfo();

if ($user_level < 10) { //Limita acesso para somente administradores

	return;

}		

$table_name_acessos_automaticos = $wpdb->prefix . self::CLASS_NAME . "_acessos_automaticos";
$table_name_produtos = $wpdb->prefix . self::CLASS_NAME . "_produtos";

$admin_url = get_admin_url();
$admin_url.= 'admin.php?page=' . self::CLASS_NAME . "_Acessos_Automaticos";

$hoje = date('Y-m-d');

if(isset($_POST["sub_acesso"])){
	

	if($_POST["produto_master"] == $_POST["produto_slave"]){
		
		echo '<div id="message" class="error">';
		echo '<p><strong>'. 'Você selecionou o Mesmo Produto! Ação Cancelada!' . '</strong></p>';
		echo '</div>';	
				
	}else{
		
		//Verifica se já não existe o Acesso
		$total_acesso = $wpdb->get_row( $wpdb->prepare( 
			"
				SELECT count(*) as total FROM $table_name_acessos_automaticos
				where id_produto_master = %d AND id_produto_slave = %d
			", 
			$_POST["produto_master"],
			$_POST["produto_slave"]
		), ARRAY_A );
		
		if(	$total_acesso["total"] > 0){
			
			echo '<div id="message" class="error">';
			echo '<p><strong>'. 'Já existe uma Programação para Esses Produtos! Ação Cancelada!' . '</strong></p>';
			echo '</div>';	
							
		}else{
			
			$validade = $_POST["data_cad"];
			
			$wpdb->query( $wpdb->prepare( 
				"
					INSERT INTO $table_name_acessos_automaticos
					(id_produto_master, id_produto_slave, validade )
					VALUES ( %d, %d, %d )
				", 
				$_POST['produto_master'],
				$_POST['produto_slave'],
				$validade 
			) );	
			
	
			echo '<div id="message" class="updated">';
			echo '<p><strong>'. 'Acesso Programado com Sucesso!' . '</strong></p>';
			echo '</div>';	
					
		}
	
	}
	
}elseif(isset($_GET["master"]) && isset($_GET["slave"])){

	$wpdb->query( $wpdb->prepare( 
		"
			DELETE from $table_name_acessos_automaticos
			where id_produto_master = %d and id_produto_slave = %d
		", 
		$_GET['master'],
		$_GET['slave'] 
	) );
		
	echo '<div id="message" class="updated">';
	echo '<p><strong>'. 'Acesso Programado Excluído com Sucesso!' . '</strong></p>';
	echo '</div>';	
	
}


//Pega Produtos
$produtos = $wpdb->get_results( 
"
	SELECT * FROM $table_name_produtos
	order by nome_produto ASC
",ARRAY_A
);
	

//Pega Acessos Automaticos
$acessos = $wpdb->get_results( 
"
	SELECT (select nome_produto from $table_name_produtos where id_produto=id_produto_master) as mestre, id_produto_master, (select nome_produto from $table_name_produtos where id_produto=id_produto_slave) as escravo, id_produto_slave, validade FROM $table_name_acessos_automaticos
",ARRAY_A
);

$data = array();
$data[0] = 'Indiferente (Dar Acesso Sempre)';
$data[1] = $hoje;
$data[2] = self::makeData($hoje, 0,0,1);
$data[3] = self::makeData($hoje, 0,0,2);
$data[4] = self::makeData($hoje, 0,0,3);
$data[5] = self::makeData($hoje, 0,0,4);
$data[6] = self::makeData($hoje, 0,0,5);
$data[7] = self::makeData($hoje, 0,0,6);
$data[8] = self::makeData($hoje, 0,0,9);
$data[9] = self::makeData($hoje, 0,0,14);
$data[10] = self::makeData($hoje, 0,0,19);
$data[11] = self::makeData($hoje, 0,0,29);

?>
<div class="wrap">
<div class="icon32"><img src='<?php echo plugins_url('/images/icon-32.png', dirname(__FILE__))?>' /></div>
        
<h2>Acessos Programados:</h2>
    
  		<table width="100%"><tr>
        <td style="vertical-align:top">
        

  		<form action="" method="post">
        
        <div class="metabox-holder">         
		<div class="postbox" >
        
        	<h3>Configuração de Acessos Programados</h3>
        
        	<div class="inside">
                           
                
              <p>
              Quem Comprar Este Produto
              <select name="produto_master">
				<?php 
				
				
				
				foreach ( $produtos as $produto ): 
				?>
                <option value="<?php echo $produto["id_produto"]; ?>" ><?php echo $produto["nome_produto"]; ?></option>
                <?php endforeach; ?>
                </select>
                
              Terá Acesso Automático a este 
              <select name="produto_slave">
				<?php 
				

				
				foreach ( $produtos as $produto ): 
				?>
                <option value="<?php echo $produto["id_produto"]; ?>" ><?php echo $produto["nome_produto"]; ?></option>
                <?php endforeach; ?>
                </select>
               </p>
               
              <p>
              Se a Compra for Feita
              <select name="data_cad">

                <option value="0">Indiferente (Dar Acesso Sempre)</option>
                <option value="1">Hoje (<?php echo self::get_data_pt($hoje);?>)</option>
                <option value="2">em até 2 dias (Hoje e Amanhã)</option>
                <option value="3">em até 3 dias</option>
                <option value="4">em até 4 dias</option>
                <option value="5">em até 5 dias</option>
                <option value="6">em até 6 dias</option>
                <option value="7">em até 7 dias</option>
                <option value="8">em até 10 dias</option>
                <option value="9">em até 15 dias</option>
                <option value="10">em até 20 dias</option>
                <option value="11">em até 30 dias</option>

                </select><br />
              </p>
              
                             
               <p>
                 <input type="submit" name="sub_acesso" value="Programar Acesso" class="myButton" />
				</p>
                               
   			  </div>
		</div>
        </div>
        
 		</form>
<form action="" method="post">
 		<div class="metabox-holder">
 		<div class="postbox">
        
        	<h3>Acessos já Programados</h3>
        
        	<div class="inside">
            	<p>
                <table id="listagem-tables" class="display" cellspacing="0" width="100%">
                <thead>
				<tr>
				<th>
				Quem Comprar Esse Produto
				</th>          
				<th>
				Terá Acesso também a Esse Produto
				</th>
                <th>
				Se Comprar até
				</th>
                <th>
                </th>               
				</tr>  
                </thead>         
                <tbody>                         
				<?php
				
					
				foreach($acessos as $acesso){
					$validade = $acesso["validade"]==0?$data[$acesso["validade"]]:self::get_data_pt($data[$acesso["validade"]]);
					echo "<tr>";
					echo "<td>";
					echo $acesso["mestre"];
					echo "</td>";						
					echo "<td>";
					echo $acesso["escravo"];
					echo "</td>";
					echo "<td>";
					echo $validade;
					echo "</td>";					
					echo "<td>";
					echo "<a href='".$admin_url."&master=".$acesso["id_produto_master"]."&slave=".$acesso["id_produto_slave"]."' class='button-secondary'>Remover Acesso Programado</a>";	
					echo "</td>";
					echo "</tr>";
					
				}
				
				?>
                </tbody>
                <tfoot> 
				<tr>
				<th>
				Quem Comprar Esse Produto
				</th>           
				<th>
				Terá Acesso também a Esse Produto
				</th>
               <th>
				Se Comprar até
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