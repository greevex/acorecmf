<?php
/**
 * Модуль загрузки и сохранения конфигурации модулей.
 * @author Кваст Александр Владимирович aka Alehandr
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
		file_put_contents($file, Core::encode($config));
	}

}