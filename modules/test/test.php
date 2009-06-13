<?php
$path = str_replace("test.php", "", __FILE__);
if (defined("MANAGED")){
	
} else {
	include "{$path}test.class.php";
	if (GLOBAL_MOD == 'test'){
		include "{$path}core/core.class.php";
	}
}
?>