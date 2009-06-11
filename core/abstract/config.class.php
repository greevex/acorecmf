<?php
/**
 * Модуль загрузки и сохранения конфигурации модулей.
 * @author Кваст Александр Владимирович
 */
class AConfig {
	public static function Load($name, $module = null){
		if ($module !== null){
			if (is_file(ROOT . "/modules/{$module}/config/{$name}.php")){
				return include(ROOT . "/modules/{$module}/config/{$name}.php");
			} else {
				return array();
			}
		}
		if (is_file(ROOT . "/core/config/{$name}.php")){
			return include(ROOT . "/core/config/{$name}.php");
		} else {
			return array();
		}
	}
	public static function Save($name, $config){
		if (is_file(ROOT . "/core/config/{$name}")) chmod(ROOT . "/core/config/" . $name, 0777);
		$file = fopen(ROOT . "/core/config/" . $name, "w");
		fwrite($file, Core::encode($config));
		fclose($file);
		chmod(ROOT . "/core/config/" . $name, 0755);
	}
}
?>