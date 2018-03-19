<?php
	$tela_horizontal = array('');
	


	$menu = array(
		array('label'=>'Hello','icon'=>'fa-dashboard','link'=>'hello'),
		
		array('label'=>'More options','icon'=>'fa-wrench','id'=>'idOptions','linkdrop'=>'link_config','dropdown'=>array(
			
			array('label'=>'Option 1','icon'=>'fa-tasks','link'=>'hello'),
			array('label'=>'Option 2','icon'=>'fa-tasks','link'=>'hello')
		)),
		
	);
	
	$config_menu = array(
		'menu' => $menu,
		'tela_horizontal' => $tela_horizontal
	);
?>