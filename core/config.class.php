<?
/**
 * Модуль загрузки и сохранения конфигурации модулей.
 * @author Кваст Александр Владимирович
 */
class Config {
	public static function Load($name){
		if (is_file(ROOT . "/core/config/{$name}")){
			return JSON_decode(file_get_contents(ROOT . "/core/config/{$name}"), true);
		} else {
			return array();
		}
	}
	public static function Save($name, $config){
		if (is_file(ROOT . "/core/config/{$name}")) chmod(ROOT . "/core/config/" . $name, 0777);
		$file = fopen(ROOT . "/core/config/" . $name, "w");
		fwrite($file, json_encode($config));
		fclose($file);
		chmod(ROOT . "/core/config/" . $name, 0755);
	}
}
?>