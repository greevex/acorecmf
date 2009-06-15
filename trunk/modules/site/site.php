<?php
$path = str_replace("site.php", "", __FILE__);
if (GLOBAL_MOD == 'site'){
include "{$path}core/core.class.php";
}
include "{$path}pages/index.php";
?>