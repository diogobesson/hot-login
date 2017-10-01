<?php
global $anderson_makiyama, $wpdb, $wp_roles;

global $user_level;

get_currentuserinfo();

if ($user_level < 10) { //Limita acesso para somente administradores

	return;

}		

$table_precos_alternativos = $wpdb->prefix . self::CLASS_NAME . "_precos_alternativos";
$table_name_produtos = $wpdb->prefix . self::CLASS_NAME . "_produtos";

$admin_url = get_admin_url();
$admin_url.= 'admin.php?page=' . self::CLASS_NAME . "_Precos_Alternativos";


if(isset($_POST["sub_acesso"])){
	

	if($_POST["produto_master"] == $_POST["produto_slave"]){
		
		echo '<div id="message" class="error">';
		echo '<p><strong>'. 'Você selecionou o Mesmo Produto! Ação Cancelada!' . '</strong></p>';
		echo '</div>';	
				
	}else{
		
		$sanitiza_preco = trim($_POST["preco"]);
		$sanitiza_preco = str_replace(array("R","r",'$','%','#','&'),'',$sanitiza_preco);
		
		if(empty($sanitiza_preco)){
			
			echo '<div id="message" class="error">';
			echo '<p><strong>'. 'Você não Informou um Preço! Ação Cancelada!' . '</strong></p>';
			echo '</div>';
					
		}else{
			
			$sanitiza_preco = strpos($sanitiza_preco,",")===false?$sanitiza_preco.",00":$sanitiza_preco;
		
			//Verifica se já não existe o Preço
			$total_preco = $wpdb->get_row( $wpdb->prepare( 
				"
					SELECT count(*) as total FROM $table_precos_alternativos
					where id_produto_principal = %d 
					AND id_produto_alternativo = %d
					AND vl_alternativo = %s
				", 
				$_POST["produto_master"],
				$_POST["produto_slave"],
				$sanitiza_preco
			), ARRAY_A );
			
			if(	$total_preco["total"] > 0){
				
				echo '<div id="message" class="error">';
				echo '<p><strong>'. 'Já existe esse Preço Alternativo! Ação Cancelada!' . '</strong></p>';
				echo '</div>';	
								
			}else{
				
				$wpdb->query( $wpdb->prepare( 
					"
						INSERT INTO $table_precos_alternativos
						(id_produto_principal, id_produto_alternativo, vl_alternativo )
						VALUES ( %d, %d, %s )
					", 
					$_POST['produto_master'],
					$_POST['produto_slave'],
					$sanitiza_preco
				) );	
				
				echo '<div id="message" class="updated">';
				echo '<p><strong>'. 'Preço Alternativo Cadastrado com Sucesso!' . '</strong></p>';
				echo '</div>';	
						
			}
		
		}
	
	}
	
}elseif(isset($_GET["principal"]) && isset($_GET["alternativo"])){

	$wpdb->query( $wpdb->prepare( 
		"
			DELETE from $table_precos_alternativos
			where id_produto_principal = %d and id_produto_alternativo = %d
		", 
		$_GET['principal'],
		$_GET['alternativo'] 
	) );
		
	echo '<div id="message" class="updated">';
	echo '<p><strong>'. 'Preço Alternativo Excluído com Sucesso!' . '</strong></p>';
	echo '</div>';	
	
}


//Pega Produtos
$produtos = $wpdb->get_results( 
"
	SELECT * FROM $table_name_produtos
	order by nome_produto ASC
",ARRAY_A
);
	

//Pega Preços Alternativos
$precos_alternativos = $wpdb->get_results( 
"
	SELECT (select nome_produto from $table_name_produtos where id_produto=id_produto_principal) as principal, id_produto_principal, (select nome_produto from $table_name_produtos where id_produto=id_produto_alternativo) as alternativo, id_produto_alternativo, vl_alternativo FROM $table_precos_alternativos
",ARRAY_A
);

?>
<div class="wrap">
<div class="icon32"><img src='<?php echo plugins_url('/images/icon-32.png', dirname(__FILE__))?>' /></div>
        
<h2>Preços Alternativos:</h2>
    
  		<table width="100%"><tr>
        <td style="vertical-align:top">
        

  		<form action="" method="post">
        
        <div class="metabox-holder">         
		<div class="postbox" >
        
        	<h3>Configuração de Preços Alternativos</h3>
        
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
              <br />
              Pelo preço 
              R$
              <input type="text" name="preco" />, em vez dele<br />
              Terá na verdade Acesso ao Produto
<select name="produto_slave">
  <?php 
				

				
				foreach ( $produtos as $produto ): 
				?>
  <option value="<?php echo $produto["id_produto"]; ?>" ><?php echo $produto["nome_produto"]; ?></option>
  <?php endforeach; ?>
</select>
              </p>
               
              <p>
                <input type="submit" name="sub_acesso" value="Cadastrar Preço Alternativo" class="myButton" />
				</p>
                               
   			  </div>
		</div>
        </div>
        
 		</form>
<form action="" method="post">
 		<div class="metabox-holder">
 		<div class="postbox">
        
        	<h3>Preços Alternativos já Cadastrados</h3>
        
        	<div class="inside">
            	<p>
                <table id="listagem-tables" class="display" cellspacing="0" width="100%">
                <thead>
				<tr>
				<th>
				Quem Comprar Esse Produto
				</th>          
				<th>
				Por Esse Preço</th>
                <th>
				Receberá Esse Produto</th>
                <th>
                </th>               
				</tr>  
                </thead>         
                <tbody>                         
				<?php
				
					
				foreach($precos_alternativos as $preco){
					echo "<tr>";
					echo "<td>";
					echo $preco["principal"];
					echo "</td>";						
					echo "<td>";
					echo $preco["vl_alternativo"];
					echo "</td>";
					echo "<td>";
					echo $preco["alternativo"];
					echo "</td>";					
					echo "<td>";
					
					echo "&nbsp;&nbsp;<a href='javascript:my_confirm(\"Atenção, Se você Excluir esse registro, Quem Já Comprou com Esse preço Alternativo e Quem Vir a Comprar Terá acesso ao Produto Principal. \nQuer Excluir Mesmo Assim?\",\"". $admin_url ."&principal=".$preco["id_produto_principal"]."&alternativo=".$preco["id_produto_alternativo"]."\")' class='button-secondary' onclick='javascript:my_confirm(\"Atenção, todas as programações do Locutor/DJ serão Excluídas. \nQuer Excluir Mesmo Assim?\",\"". $admin_url ."&principal=".$preco["id_produto_principal"]."&alternativo=".$preco["id_produto_alternativo"]."' \");return false;'>Excluir</a>"; 
						
						
						
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
				Por Esse Preço</th>
               <th>
				Receberá Esse Produto</th> 
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