<?php
/**
 * Модуль загрузки и сохранения конфигурации модулей.
 * @author Кваст Александр Владимирович
 */
class AConfig {
	public static function Load($name, $module = null){
		$file = ROOT . ($module === null ? "/core/config/" : "/modules/{$module}/config/") . "{$name}.php";
		if (is_file($file)){
			return include($file);
		} else {
			return array();
		}
	}
	public static function Save($name, $config, $module = null){
		$file = ROOT . ($module === null ? "/core/config/" : "/modules/{$module}/config/") . "{$name}.php";
		if (is_file($file)) chmod($file, 0777);
		$h = fopen($file, "w");
		fwrite($h, Core::encode($config));
		fclose($h);
		chmod($file, 0755);
	}
}
?>