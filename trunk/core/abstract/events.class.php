<?php

class AEvents {
	
	public static $reserved = array();
	
	public static function Reserve($class, $event, &$object, $function, $repeat = false){
		$class = strtolower($class);
		
		self::$reserved[$class][$event][] = array(
			'repeat' => $repeat,
			'object' => &$object,
			'function' => $function,
		);
	}
	
	public static function EvalEvent($class, $event, $args = array()){
		$class = strtolower($class);
		
		if (isset(self::$reserved[$class][$event]))
		foreach (self::$reserved[$class][$event] as $i => $e) {
			$e['object']->$e['function']($class, $event, &$args);
			if (!$event['repeat']){
				unset(self::$reserved[$class][$event][$i]);
			}
		}
	}
	
}