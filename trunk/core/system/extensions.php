<?php
set_error_handler('error_to_extension', E_NOTICE & E_WARNING);
function error_to_extension($errno, $errmsg, $file, $line){
	throw new Exception("{$errmsg} [{$errno}]", $errno);
}
?>