<?php
global $anderson_makiyama, $wpdb, $wp_roles;

global $user_level;

get_currentuserinfo();

if ($user_level < 10) { //Limita acesso para somente administradores

	return;

}

if(isset($_POST["sub_logo"])){
	
	$logo = isset($_POST["logo_"])?trim($_POST["logo_"]):'';
	$fav = isset($_POST["fav"])?trim($_POST["fav"]):'';
	
	update_option('hot_theme_logo_url', $logo);
	update_option('hot_theme_fav_url', $fav);
	
	echo '<div id="message" class="updated">';
	echo '<p><strong>'. 'Alterações Salvas com Sucesso!' . '</strong></p>';
	echo '</div>';		

}

$fav = get_option('hot_theme_fav_url');
$fav = trim($fav);


$logo = get_option('hot_theme_logo_url');
$logo = trim($logo);

?>
<div class="wrap my-wrap">
<div class="icon32"><img src='<?php echo plugins_url('/images/icon-32.png', dirname(__FILE__))?>' /></div>
        
<h2>Opções do Tema da Área Restrita:</h2>
    
  		<table width="100%"><tr>
        <td style="vertical-align:top">

  		<form action="" method="post">
        
        <div class="metabox-holder">         
		<div class="postbox" >
        
        	<h3>Logo e Favicon</h3>
        
        	<div class="inside">
                                
            	<p style="color:blue;font-weight:bold;">
            	  <label>Logo</label> 
            	  <input type="text" value="<?php echo $logo?>" class="text" name="logo_" id="logo_" /> <input type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Upload Image">
              </p>

            	<p style="color:blue;font-weight:bold;">
            	  <label>Favicon</label> 
            	  <input type="text" value="<?php echo $fav?>" class="text" name="fav" id="fav" /> <input type="button" name="upload-btn-fav" id="upload-btn-fav" class="button-secondary" value="Upload Image">
              </p>
                           
               <p>
                <input type="submit" name="sub_logo" value="Salvar Alterações" class="myButton" />
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
<script type="text/javascript">
jQuery(document).ready(function($){
    $('#upload-btn').click(function(e) {
        e.preventDefault();
        var image = wp.media({ 
            title: 'Upload Image',
            // mutiple: true if you want to upload multiple files at once
            multiple: false
        }).open()
        .on('select', function(e){
            // This will return the selected image from the Media Uploader, the result is an object
            var uploaded_image = image.state().get('selection').first();
            // We convert uploaded_image to a JSON object to make accessing it easier
            // Output to the console uploaded_image
            console.log(uploaded_image);
            var image_url = uploaded_image.toJSON().url;
            // Let's assign the url value to the input field
            $('#logo_').val(image_url);
        });
    });
	
    $('#upload-btn-fav').click(function(e) {
        e.preventDefault();
        var image = wp.media({ 
            title: 'Upload Image',
            // mutiple: true if you want to upload multiple files at once
            multiple: false
        }).open()
        .on('select', function(e){
            // This will return the selected image from the Media Uploader, the result is an object
            var uploaded_image = image.state().get('selection').first();
            // We convert uploaded_image to a JSON object to make accessing it easier
            // Output to the console uploaded_image
            console.log(uploaded_image);
            var image_url = uploaded_image.toJSON().url;
            // Let's assign the url value to the input field
            $('#fav').val(image_url);
        });
    });
		
});
</script>