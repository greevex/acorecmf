<?php
/**
 * ACore Content Manager Framework
 * @copyright Copyright (C) 2009, AlArta Studio <www.alarta.ru>
 * @author Кваст Александр Владимирович <axel.90@inbox.ru>
 */
session_start();
define("ROOT", str_replace("/index.php", "", $_SERVER['SCRIPT_FILENAME']));
include_once("./core/system/extensions.php");
include_once("./core/system/autoload.php");
Core::Run();
?>