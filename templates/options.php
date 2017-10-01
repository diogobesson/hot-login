<div class="wrap my-wrap">
<div class="icon32"><img src='<?php echo plugins_url('/images/icon-32.png', dirname(__FILE__))?>' /></div>
        
<h2>Configurações <?php echo self::PLUGIN_NAME?>:</h2>
    
  		<table width="100%"><tr>
        <td style="vertical-align:top">
                
 		<form action="" method="post">
        
        <div class="metabox-holder">         
		<div class="postbox" >
        
        	<h3>Configurações do HotConnect</h3>
        
        	<div class="inside">
                
                            
            	<p>
            	  <label>ID da Aplicação</label> <input type="text" value="<?php echo $options["app_id"];?>" name="app_id" />
              </p>
               <p>
            	  <label>Código Secreto da Aplicação</label> <input type="text" value="<?php echo $options["app_secret"];?>" name="app_secret" class="text"  />
              </p>                 
            	
             
             <p>
                <input type="submit" name="sub_hotconnect" value="Salvar Configurações" class="myButton" />
			 </p>
                                 
            </div>
		</div>
        </div>
        
 		</form>
 

 		<form action="" method="post">
        
        <div class="metabox-holder">         
		<div class="postbox" >
        
        	<h3>Configurações de Restrições de Conteúdo</h3>
        
        	<div class="inside">
                
                            
               <p>
            	  Ativar/Desativar Restrições <select name="restricoes">
                  	
                  	<option value="1" <?php selected(1,$options['restricoes'])?>>Ativar: Irei Criar as Seções Restritas a Membros</option>
                    <option value="3" <?php selected(3,$options['restricoes'])?>>Desativar: Já uso outro Plugin que Controla restrições</option>
                  </select>
              </p>
              
              <p>
              Usuários Cadastrados pelo Hotlogin serão: 
              <select name="tipo_usuario">
				<?php 
				$recomendado = '';
				foreach ( $wp_roles->roles as $key=>$value ): 
				
					if($key == "administrator") continue;
					if($key == 'subscriber') $recomendado = ' (Recomendado)';
					
				?>
                <option value="<?php echo $key; ?>" <?php selected($options['tipo_usuario'],$key)?>><?php echo $value['name']; ?><?php echo $recomendado;?></option>
                <?php 
				$recomendado = '';
				endforeach; ?>
                </select>
              
              </p>
             
            	 <p>
                <input type="submit" name="sub_restricoes" value="Salvar Restrições" class="myButton" />
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





<table class="author">

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