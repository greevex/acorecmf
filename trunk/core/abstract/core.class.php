<?php

class ACore {

	public static $data = array();
	public static $config = null;
	public static $url = array();
	public static $page = "index";
	public static $language;
	public static $modules = array();

	public static $main_folder = "";

	public static $generate_start_time;

	protected static function Init(){
		self::$generate_start_time = microtime();
		self::$config = Config::Load("core");
		self::$language = self::$config['default_language'];

		self::$data = self::$config['consts'];
		self::$data['root'] = "http://" . $_SERVER['SERVER_NAME'] . str_replace($_SERVER['DOCUMENT_ROOT'], "", ROOT);
		self::$data['lang_root'] = self::$data['root'];
		self::$data['rand'] = rand(0, 1000000000);
	}
	
	public static function Run(){
		self::Init();
		$processData = UrlManager::ProcessRequest();
		self::$url = &UrlManager::$url;
		
		if ($processData['ajax'] == true) self::AjaxOut();
		else self::Out();
	}
	
	public static function GetModule($mod){
		$mod = strtolower($mod);
		if (!isset(self::$modules[$mod])){
			if (!is_file(ROOT . "/modules/{$mod}/{$mod}.php")) throw new Exception("Не найден модуль `{$mod}`!");
			require(ROOT . "/modules/{$mod}/{$mod}.php");
			try {
				if (!class_exists($mod)) throw new Exception("Не найден модуль `{$mod}`!");
			} catch (Exception $ex){
				 throw new Exception("Не найден модуль `{$mod}`!");
			}
			self::$modules[$mod] = new $mod();
			
			Events::EvalEvent('core', 'AddModule', array('mod' => $mod));
		}
		return self::$modules[$mod];
	}

	public static function GetConst($name){
		if (!isset(self::$data[$name])) return "";
		return is_array(self::$data[$name]) ? self::$data[$name][self::$language] : self::$data[$name];
	}

	public static function GetModuleVar($module, $name){
		if (!isset(self::GetModule($module)->data[$name])) return "";
		return is_array(self::GetModule($module)->data[$name]) ? self::GetModule($module)->data[$name][self::$language] : self::GetModule($module)->data[$name];
	}

	private static function Out(){
		$result = str_replace("<[generate_time]>",
		(int)((microtime() - self::$generate_start_time) * 10000) / 10000,
		Tpl::Get(self::$page, true));
		Tpl::SaveCached();
		
		Events::EvalEvent('core', 'Out', array('result' => &$result));
		
		echo $result;
	}

	private static function AjaxOut(){
		$func = "ajax_" . self::$url[2];
		$mod = &self::GetModule(self::$url[1]);
		echo json_encode($mod->$func());
	}
	
	public static function encode($array, $pref = "\t"){
		if ($pref == "\t"){
			$res = "<?php\nreturn array(\n";
		} else {
			$res = "array(\n";
		}
		foreach ($array as $i => $v)
		$res .= $pref .  (!is_numeric($i) ? "'" . str_replace("'", "\\'", $i) . "' => " : "") .
		(is_array($v) ? self::encode($v, $pref . "\t") : (is_numeric($v) ? $v : (is_bool($v) ? ($v ? 'true' : 'false') : "'" . str_replace("'", "\\'", $v)) . "'")) . ",\n";
		if ($pref == "\t"){
			return $res . substr($pref, 0, -1) . ");\n?>";
		} else {
			return $res . substr($pref, 0, -1) . ")";
		}
	}
	
	public static function decode($file){
		return include($file);
	}
	
}