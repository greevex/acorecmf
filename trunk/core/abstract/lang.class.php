<?php
/**
 * @author Кваст Александр Владимирович aka Alehandr
 */
class ALang {
	public static function Load($module_name){
		if (is_file(ROOT . Core::$main_folder . "/langs/" . $module_name . ".ini")){
			$consts = parse_ini_file(ROOT . Core::$main_folder . "/langs/" . $module_name . ".ini", true);
			if (isset($consts[Core::$language]))
				return $consts[Core::$language]; 
		}
		return array();
	}
}
?>