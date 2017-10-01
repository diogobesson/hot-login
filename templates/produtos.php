<div class="wrap">
<div class="icon32"><img src='<?php echo plugins_url('/images/icon-32.png', dirname(__FILE__))?>' /></div>
        
<h2>Produtos:</h2>
    
  		<table width="100%"><tr>
        <td style="vertical-align:top">
 
 		<form action="" method="post">
        
        <div class="metabox-holder">         
		<div class="postbox" >
        
        	<h3>Adicionar Novo Produto</h3>
        
        	<div class="inside">
                
                            
            	<p>
                <label>Nome do Produto</label> <input type="text" value="" name="nome_produto" />
            	</p>
   
            	<p>
                <label>ID do Produto</label> <input type="text" value="" name="id_produto" class="regular-text"  />
            	</p>
                
             <p>
              Descrição:  <textarea name="descricao" class="large-text code"></textarea>
             </p> 
  
  
              	<p>
                <label>Página Inicial do Comprador </label> <input type="text" value="" name="pagina_inicial" class="regular-text"  />
            	</p>
                                                                   
                
                 <p>
                <input type="submit" name="submit" value="Adicionar" class="myButton" />
				</p>
                               
                			</div>
		</div>
        </div>
 		</form>
 		<form action="" method="post">
 		<div class="metabox-holder">
 		<div class="postbox">
        
        	<h3>Produtos já Criados (<span style="color:yellow">Ao excluir um Produto, todas as Seções dele serão excluídas</span>)</h3>
        
        	<div class="inside">
            	<p>
                <table id="listagem-tables" class="display" cellspacing="0" width="100%">
                <thead>
				<tr>
				<th>
				Nome do Produto</th> 
				<th>
				ID do Produto
				</th>
                                
				<th>
				Página Inicial
				</th>
				<th>
				Descrição
				</th>
                <th>
                Ações
                </th>               
				</tr>  
                </thead>         
                <tbody>                         
                <?php
					
					foreach($produtos as $produto){
						echo "<tr>";
						echo "<td class='center'>";
						echo $produto["nome_produto"];
						echo "</td>";						
						echo "<td class='center'>";
						echo $produto["id_produto"];
						echo "</td>";
						echo "<td class='center'>";
						echo $produto["pagina_inicial"];
						echo "</td>";
						echo "<td class='center'>";
						echo $produto["desc_produto"];
						echo "</td>";
						echo "<td class='center'>";
						echo "<a href='".$admin_url."_Produtos&id=".$produto["id_produto"]."' class='button-secondary'>Editar</a> ";
						
						echo "&nbsp;&nbsp;<a href='javascript:my_confirm(\"Atenção, todas as seções, preços alternativos, acessos programados, com ligação a esse produto também serão Excluídas. \nQuer Excluir Mesmo Assim?\",\"". $admin_url .  "_Produtos&iddel=" . $produto["id_produto"] ."\");' class='button-secondary' onclick='javascript:my_confirm(\"Atenção, todas as seções, preços alternativos, acessos programados, com ligação a esse produto também serão Excluídas. \nQuer Excluir Mesmo Assim?\",\"". $admin_url ."_Produtos&iddel=". $produto["id_produto"] ."\");return false;'>Excluir</a>"; 
						
						echo "</td>";
						echo "</tr>";
					}
				?>
                </tbody>
                <tfoot> 
				<tr>
				<th>
				Nome do Produto
				</th>
				<th>
				ID do Produto
				</th> 
				<th>
				Página Inicial</th>
				<th>
				Descrição
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