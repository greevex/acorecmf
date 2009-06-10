<?
/**
 * ACore Content Manager Framework
 * @copyright Copyright (C) 2009, AlArta Studio <www.alarta.ru>
 * @author Кваст Александр Владимирович <axel.90@inbox.ru>
 */
session_start();
define("ROOT", str_replace("/index.php", "", $_SERVER['SCRIPT_FILENAME']));
include_once("./core/core.class.php");
include_once("./core/autoload.php");
Core::Run();
?>