<?php
if(isset($_POST["alterar"])){
	
	if(empty($_POST['nome']) || empty($_POST['ordem']) || empty($_POST['descricao'])){

	echo '<div id="message" class="error">';
	echo '<p><strong>'. 'Há campos não preenchidos!<br>Preencha todos os campos e Tente novamente!' . '</strong></p>';
	echo '</div>';					
	
	}else{

		
		$wpdb->query( $wpdb->prepare( 
			"
				update $table_name
				set nome_secao = %s, desc_secao = %s, ordem = %d
				where id_secao = %d
			", 
			$_POST['nome'], 
			$_POST['descricao'], 
			$_POST['ordem'], 
			$_POST['pid'] 
		) );		


		echo '<div id="message" class="updated">';
		echo '<p><strong>'. 'Seção Atualizada com sucesso!' . '</strong></p>';
		echo '</div>';	
					
	}

}elseif(isset($_POST["add_page_post"])){ //Adiciona paginas e posts
	
	
	if(empty($_POST['postagem']) && empty($_POST['pagina'])){

		echo '<div id="message" class="error">';
		echo '<p><strong>'. 'Selecione ao menos um Post ou uma Página! Tente novamente!' . '</strong></p>';
		echo '</div>';				
		
	}else{
		
		$in = '';
		if(!empty($_POST["postagem"])) $in = $_POST["postagem"];
		
		$in .= empty($in)?$_POST["pagina"]:(empty($_POST["pagina"])?"":"," . $_POST["pagina"]);
		
		//Verifica se a página já foi adicionada
		$p_restrita = $wpdb->get_row( $wpdb->prepare( 
			"
				SELECT count(*) as total FROM $table_name_paginas_restritas
				where id_secao = %d and id_pagina in(".$in.")
			", 
			$_POST["pid"] 
		), ARRAY_A );

		if($p_restrita["total"] >0){
		
			echo '<div id="message" class="updated">';
			echo '<p><strong>'. 'Post e/ou Página Já está na Seção! Ação Cancelada!' . '</strong></p>';
			echo '</div>';
			
		}else{
		

			if(empty($_POST["post_ordem"])) $_POST["post_ordem"] = 1;
			if(empty($_POST["pagina_ordem"])) $_POST["pagina_ordem"] = 1;
			
			
			if(!empty($_POST["postagem"])){
				
				$wpdb->query( $wpdb->prepare( 
				"
					INSERT INTO $table_name_paginas_restritas
					(id_pagina, id_secao, ordem, liberacao )
					VALUES ( %d, %d, %d, %d )
				", 
				$_POST['postagem'],
				$_POST['pid'], 
				$_POST['post_ordem'],
				$_POST['post_data_lib']
				) );	
									
			}
			
			
			if(!empty($_POST["pagina"])){
				 
				
				$wpdb->query( $wpdb->prepare( 
				"
					INSERT INTO $table_name_paginas_restritas
					(id_pagina, id_secao, ordem, liberacao )
					VALUES ( %d, %d, %d, %d)
				", 
				$_POST['pagina'],
				$_POST['pid'], 
				$_POST['pagina_ordem'],
				$_POST['pagina_data_lib']
				) );	
				
				//$wpdb->show_errors();	
				//$wpdb->print_error();
									
			}

			echo '<div id="message" class="updated">';
			echo '<p><strong>'. 'Post e/ou Página cadastrados com sucesso!' . '</strong></p>';
			echo '</div>';	
		
		}
					
	}					
	
	
}elseif(isset($_GET["ppdel"])){
	
	$wpdb->query( $wpdb->prepare( 
		"
			DELETE FROM $table_name_paginas_restritas
			WHERE id_pagina= %d
		", 
		$_GET["ppdel"] 
	) );
	
	echo '<div id="message" class="updated">';
	echo '<p><strong>'. 'Post ou Página removida da Seção com Sucesso!' . '</strong></p>';
	echo '</div>';				
	
}

//Pega Páginas da Seção
$paginas = $wpdb->get_results( $wpdb->prepare( 
	"
		SELECT * FROM $table_name_paginas_restritas
		where id_secao = %d
		ORDER BY ordem
	", 
	$_REQUEST["pid"] 
), ARRAY_A );


//Pega dados da Seção
$produto = $wpdb->get_row( $wpdb->prepare( 
	"
		SELECT * FROM $table_name
		where id_secao = %d
	", 
	$_REQUEST["pid"] 
), ARRAY_A );
				
					
?>
<div class="wrap">
<div class="icon32"><img src='<?php echo plugins_url('/images/icon-32.png', dirname(__FILE__))?>' /></div>
        
<h2>Configuração <?php echo self::PLUGIN_NAME?>:</h2>
    
  		<table width="100%"><tr>
        <td  style="vertical-align:top">
 
 		<form action="" method="post">
        
        <div class="metabox-holder">         
		<div class="postbox" >
        
        	<h3>Atualizar Seção</h3>
        
        	<div class="inside">
                
                            
            	<p>
                <label>Nome da Seção</label><input type="text" value="<?php echo $produto["nome_secao"]?>" name="nome" />
            	</p>      

             	<p>
                Descrição:  <textarea name="descricao" class="large-text code"><?php echo $produto["desc_secao"]?></textarea>
                </p>
                
                <p>
                <label>Ordem</label> <input type="text" value="<?php echo $produto["ordem"]?>" name="ordem" class="regular-text"  />
                </p>
                
                <p>
                <input type="hidden" name="id" value="<?php echo $_REQUEST["id"]?>">
                <input type="hidden" name="pid" value="<?php echo $_REQUEST["pid"]?>">
                <input type="submit" name="alterar" value="Atualizar Seção" class="myButton" />  <a href="<?php echo $admin_url;?>_Produtos&id=<?php echo $produto["id_produto"]?>" class="button-secondary" >Voltar para Listagem de Seções do Produto</a>
				</p>
                               
                			</div>
		</div>
        </div>
 		</form>
        
 		<form action="" method="post">
        
        <div class="metabox-holder">         
		<div class="postbox" >
        
        	<h3>Adicionar Posts e Páginas à Seção</h3>
        
        	<div class="inside">
                
                            
            	<p>
                <label>Adicione um Post</label> 

				<select name="postagem" style="max-width:300px;">
                <option value=""></option>
				<?php
                $args = array( 'posts_per_page' => -1, 'order'=> 'ASC', 'orderby' => 'title' );
                $postslist = get_posts( $args );
                foreach ( $postslist as $my_post ) :
                  setup_postdata( $my_post );
				  
				  echo "<option value='". $my_post->ID ."'>" . $my_post->post_title . "</option>";
				endforeach;
                ?>                
                </select> Ordem: <input type="number" name="post_ordem" />
                
               Liberação: <input type="number" name="post_data_lib" class="small-text" value="0" > dias <small>(deixe 0 para liberação imediata)</small>
                
            	</p>
                                
            	<p>
                 Adicione uma Página
                 
				<select name="pagina" style="max-width:300px;">
                <option value=""></option>
				<?php
                $args = array( 'posts_per_page' => -1, 'order'=> 'ASC', 'orderby' => 'title' );
                $postslist = get_pages( $args );
                foreach ( $postslist as $my_post ) :
                  setup_postdata( $my_post );
				  
				  echo "<option value='". $my_post->ID ."'>" . $my_post->post_title . "</option>";
				endforeach;
                ?>                
                </select> Ordem: <input type="number" name="pagina_ordem" />  
                
               Liberação: <input type="number" name="pagina_data_lib" class="small-text" value="0" > dias <small>(deixe 0 para liberação imediata)</small>
                              
                </p>

                
               <p>
                <input type="hidden" name="id" value="<?php echo $_REQUEST["id"]?>">
                <input type="hidden" name="pid" value="<?php echo $_REQUEST["pid"]?>">
                <input type="submit" name="add_page_post" value="Adicionar Post ou Página" class="myButton" />
				</p>
                               
                </div>
		</div>
        </div>
        
 		</form>
                
 		<form action="" method="post">
 		<div class="metabox-holder">
 		<div class="postbox">
        
        	<h3>Páginas e Posts já Adicionados</h3>
        
        	<div class="inside">
            	<p>
                <table id="listagem-tables" class="display" cellspacing="0" width="100%">
                <thead>
				<tr>
				<th>
				Nome da Página/Post
				</th>          
				<th>
				Ordem
				</th>
                <th>
				Liberação
				</th>
                <th>
                </th>               
				</tr>  
                </thead>         
                <tbody>                         
				<?php
				
					foreach($paginas as $pagina){
						
						$pagina_title = get_the_title( $pagina["id_pagina"] );
						
						$quando_liberar = $pagina["liberacao"]==0?"Imediata":"Após ".$pagina["liberacao"] . " dias";
						
						echo "<tr>";
						echo "<td class='center'>";
						echo $pagina_title;
						echo "</td>";						
						echo "<td class='center'>";
						echo $pagina["ordem"];
						echo "</td>";
						echo "<td class='center'>";
						echo $quando_liberar;
						echo "</td>";
						echo "<td class='center'>";
						echo "<a href='".$admin_url."_Produtos&ppdel=".$pagina["id_pagina"]."&id=".$_REQUEST["id"]."&pid=". $_REQUEST["pid"] ."' class='button-secondary'>Remover da Seção</a>";	
						echo "</td>";
						echo "</tr>";
						
					}
				?>
                </tbody>
                <tfoot> 
				<tr>
				<th>
				Nome da Página/Post
				</th>           
				<th>
				Ordem
				</th>
                <th>
				Liberação
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