<?php
	if(phpversion() > '5.0' && phpversion() < '5.6'){ 
		require_once('technics_add5455.php');
	}elseif(phpversion() >= '5.6' && phpversion() < '7.1'){ 
		require_once('technics_add5670.php');
	}else{
		require_once('technics_add7174.php');
	}
?>
