<div class="wrap">
<div class="icon32"><img src='<?php echo plugins_url('/images/icon-32.png', dirname(__FILE__))?>' /></div>
        
<h2>Configuração do Produto:</h2>
    
  		<table width="100%"><tr>
        <td style="vertical-align:top">
 
 		<form action="" method="post">
        
        <div class="metabox-holder">         
		<div class="postbox" >
        
        	<h3>Atualizar o Produto</h3>
        
        	<div class="inside">
                
                            
            	<p>
                <label>Nome do Produto</label> <input type="text" value="<?php echo $produto["nome_produto"]?>" name="nome" />
            	</p>
                                
            	<p>
                <label>ID do produto</label> <input type="text" value="<?php echo $produto["id_produto"]?>" name="id_produto" class="regular-text"  />
                <input type="hidden" value="<?php echo $produto["id_produto"]?>" name="realid"/>
                </p>

             	<p>
                Descrição:  <textarea name="descricao" class="large-text code"><?php echo $produto["desc_produto"]?></textarea>
                </p>
                
                <p>
                <label>Página Inicial do Comprador </label> <input type="text" value="<?php echo $produto["pagina_inicial"]?>" name="pagina_inicial" class="regular-text"  />
                </p>
                
                <p>
                <input type="hidden" name="id" value="<?php echo $_REQUEST["id"]?>">
                <input type="submit" name="atualizar" value="Atualizar Produto" class="myButton" />  <a href="<?php echo $admin_url;?>_Produtos" class="button-secondary" >Voltar para Listagem de Produtos</a>
				</p>
                               
                			</div>
		</div>
        </div>
 		</form>
        
 		<form action="" method="post">
        
        <div class="metabox-holder">         
		<div class="postbox" >
        
        	<h3>Adicionar Nova Seção (Módulo)</h3>
        
        	<div class="inside">
                
                            
            	<p>
                <label>Nome da Seção </label> <input type="text" value="" name="nome_secao" />
            	</p>
                                
            	<p>
                 Descrição:  <textarea name="descricao_secao" class="large-text code"></textarea>
                </p>

            	<p>
                <label>Ordem</label> <input type="text" value="" name="ordem_secao"  /><small>Digite um Número para ser a Ordem dessa Seção</small>
            	</p>
                
               <p>
                 <input type="hidden" name="id" value="<?php echo $_REQUEST["id"]?>">
                <input type="submit" name="adicionar" value="Adicionar Nova Seção" class="myButton" />
				</p>
                               
                			</div>
		</div>
        </div>
        
 		</form>
                
 		<form action="" method="post">
 		<div class="metabox-holder">
 		<div class="postbox">
        
        	<h3>Seções já Inseridas</h3>
        
        	<div class="inside">
            	<p>
                <table id="listagem-tables" class="display" cellspacing="0" width="100%">
                <thead>
				<tr>
				<th>
				Nome da Seção
				</th> 
				<th>
				Descrição
				</th>          
				<th>
				Ordem
				</th>
                <th>
                </th>               
				</tr>  
                </thead>         
                <tbody>                         
                <?php
					
					foreach($secoes as $secao){
						

						echo "<tr>";
						echo "<td class='center'>";
						echo $secao["nome_secao"];
						echo "</td>";						
						echo "<td class='center'>";
						echo $secao["desc_secao"];
						echo "</td>";
						echo "<td class='center'>";
						echo $secao["ordem"];
						echo "</td>";
						echo "<td class='center'>";
						echo "<a href='".$admin_url."_Produtos&id=".$_REQUEST["id"]."&pid=". $secao["id_secao"] ."' class='button-secondary'>Editar</a><br>";

					echo "&nbsp;&nbsp;<a href='javascript:my_confirm(\"Quer mesmo Excluir essa Seção?\",\"". $admin_url ."_Produtos&id=".$_REQUEST["id"]."&piddel=". $secao["id_secao"] ."\")' class='button-secondary' onclick='javascript:my_confirm(\"Quer mesmo Excluir essa Seção?\",\"". $admin_url ."_Produtos&id=".$_REQUEST["id"]."&piddel=". $secao["id_secao"] ."' \");return false;'>Excluir</a>"; 
											
						echo "</td>";
						echo "</tr>";
					}
				?>
                </tbody>
                <tfoot> 
				<tr>
				<th>
				Nome da Seção
				</th> 
				<th>
				Descrição
				</th>          
				<th>
				Ordem
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