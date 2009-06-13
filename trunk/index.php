<?php
/**
 * ACore Content Manager Framework
 * @copyright Copyright (C) 2009, AlArta Studio <www.alarta.ru>
 * @author Кваст Александр Владимирович <axel.90@inbox.ru>
 */
define("ROOT", str_replace("/index.php", "", $_SERVER['SCRIPT_FILENAME']));
include("./core/system/extensions.php");
include("./core/system/autoload.php");
include("./core/system/global_mod.php");
setGlobalMod();
Core::Run();
?>