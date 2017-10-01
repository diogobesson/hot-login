<?php
global $user_level,$anderson_makiyama;

$logado = is_user_logged_in();
if(!$logado){
	return;
}

get_currentuserinfo();

$options = get_option(self::CLASS_NAME . "_options");
$restricao = $options["restricoes"];

if($restricao ==3) return; //Não ativou as restrições

$menu_real = '';

if($user_level <10 && isset($anderson_makiyama)){
	
	$menu_lateral = '
	<div id="fl_menu">
		<div class="label">MENU</div>
			';			
	$menu_lateral .= $anderson_makiyama[100]->monta_menus('my-menu-lateral');
	$menu_lateral .= '</div>';
		
	echo $menu_lateral;
	echo '
	<script>
	//config
	$float_speed=1500; //milliseconds
	$float_easing="easeOutQuint";
	$menu_fade_speed=500; //milliseconds
	$closed_menu_opacity=0.75;
	 
	//cache vars
	$fl_menu=$("#fl_menu");
	$fl_menu_menu=$("#fl_menu .my-menu-lateral");
	$fl_menu_label=$("#fl_menu .label");
	 
	$(window).load(function() {
		menuPosition=$(\'#fl_menu\').position().top;
		FloatMenu();
		$fl_menu.hover(
			function(){ //mouse over
				$fl_menu_label.fadeTo($menu_fade_speed, 1);
				$fl_menu_menu.fadeIn($menu_fade_speed);
			},
			function(){ //mouse out
				$fl_menu_label.fadeTo($menu_fade_speed, $closed_menu_opacity);
				$fl_menu_menu.fadeOut($menu_fade_speed);
			}
		);
	});
	 
	$(window).scroll(function () {
		FloatMenu();
	});
	 
	function FloatMenu(){
		var scrollAmount=$(document).scrollTop();
		var newPosition=menuPosition+scrollAmount;
		if($(window).height()<$fl_menu.height()+$fl_menu_menu.height()){
			$fl_menu.css("top",menuPosition);
		} else {
			$fl_menu.stop().animate({top: newPosition}, $float_speed, $float_easing);
		}
	}
	</script>		
	';				
		
}
?>