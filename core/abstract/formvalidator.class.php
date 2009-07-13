<?php
/**
 * @author Кваст Александр Владимирович aka Alehandr
 */
class AFormValidator {
	
	public static $init = false;
	public static $config = array();
	
	public static function Init(){
		if (self::$init) return;
		
		self::$config = Config::Load('formvalidator');
		self::$init = true;
	}
	
	/**
	 * Функция проверки данных, переданных через POST
	 * @param $config_name
	 * @return array
	 */
	public static function Validate($config_name, $assoc_result = false){
		self::Init();
		$config = &self::$config[$config_name];
		$err = false;
		$errors = array();
		
		foreach($config as $options){
			$func = "Validate_" . $options['validator'];
			if (!self::$func($_POST[$options['field']], $options['args'])){
				$err = true;
				if ($assoc_result){
					if (!isset($errors[$options['field']])) $errors[$options['field']] = array();
					$errors[$options['field']][] = $options['error'][Core::$language];
				} else {
					$errors[] = $options['error'][Core::$language];
				}
			}
		}
		
		if (!$err) return true;
		return $errors;
	}
	
	//Валидаторы:
	
	private static function Validate_notnull(&$value, &$args){
		if (strlen(trim($value)) == '') return false;
		return true;
	}
	
	private static function Validate_preg(&$value, &$args){
		if (!preg_match($args[0], $value)) return false;
		return true;
	}
	
	private static function Validate_minlen(&$value, &$args){
		if (strlen($value) < (int)$args[0]) return false;
		return true;
	}
	
	private static function Validate_maxlen(&$value, &$args){
		if (strlen($value) > (int)$args[0]) return false;
		return true;
	}
	
	private static function Validate_equals(&$value, &$args){
		if ($value != $_POST[$args[0]]) return false;
		return true;
	}
	
}