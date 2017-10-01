<div class="wrap">
<div class="icon32"><img src='<?php echo plugins_url('/images/icon-32.png', dirname(__FILE__))?>' /></div>
        
<h2>Relatório de Logins via Hotmart</h2>
    
  		<table width="100%"><tr>
        <td style="vertical-align:top">

        <div class="metabox-holder">         
		<div class="postbox" >
        	<h3>Últimos 1000 Logins</h3>
        
        	<div class="inside">
            
                <p>
                
                <table id="listagem-tables" class="display" cellspacing="0" width="100%">
                <thead>
				<tr>
				<th>
				Nome</th> 
				<th>
				Data e Horário
				</th>
                                
				<th>
				Status
				</th>              
				</tr>  
                </thead>         
                <tbody>                         
                <?php
					
					foreach($last_100_logins as $logins){
						echo "<tr>";
						echo "<td>";
						echo $logins[0];
						echo "</td>";						
						echo "<td>";
						echo $logins[1];
						echo "</td>";
						echo "<td>";
						echo $logins[2];
						echo "</td>";
						echo "</tr>";
					}
				?>
                </tbody>
                <tfoot> 
				<tr>
				<th>
				Nome
				</th>
				<th>
				Data e Horário
				</th> 
				<th>
				Status
                </th>               
				</tr>  
                </tfoot>                 
                </table>
                
                </p>

			</div>
		</div>
        </div>
 
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