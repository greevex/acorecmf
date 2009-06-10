<?
class Core {

	public static $data = array();
	public static $config = null;
//	public static $url = array();
	public static $page = "index";
	public static $language;
	public static $modules = array();

	public static $main_folder = "";

	public static $generate_start_time;

	protected static function Init(){
		self::$generate_start_time = microtime();
		self::$config = Config::Load("core");
		self::$language = self::$config['default_language'];

		self::$data = Config::Load("consts");
		self::$data['root'] = "http://" . $_SERVER['SERVER_NAME'] . str_replace($_SERVER['DOCUMENT_ROOT'], "", ROOT);
		self::$data['rand'] = rand(0, 1000000000);
	}
	
	public static function Run(){
		self::Init();
		$processData = UrlManager::ProcessRequest();
		
		if ($processData['ajax'] == true) self::AjaxOut();
		else self::Out();
	}

	public static function AddModule($mod){
		self::$modules[strtolower(get_class($mod))] = &$mod;
	}

	public static function GetModule($mod){
		if (!isset(self::$modules[strtolower($mod)])) require(ROOT . "/modules/"."{$mod}.php");
		return self::$modules[strtolower($mod)];
	}

	public static function GetConst($name){
		return is_array(self::$data[$name]) ? self::$data[$name][self::$language] : self::$data[$name];
	}

	public static function GetModuleVar($module, $name){
		return is_array(self::GetModule($module)->data[$name]) ? self::GetModule($module)->data[$name][self::$language] : self::GetModule($module)->data[$name];
	}

	private static function Out(){
		echo preg_replace("/<\[generate_time\]>/",
		(int)((microtime() - self::$generate_start_time) * 10000) / 10000,
		Tpl::Get(self::$page, true));
		Tpl::SaveCached();
		return;
	}

	private static function AjaxOut(){
		$func = "ajax_" . self::$url[2];
		$mod = &self::GetModule(self::$url[1]);
		echo json_encode($mod->$func());
		return;
	}
	
	public static function encode($array, $pref = "\t"){
		$res = "array(\n";
		foreach ($array as $i => $v)
		$res .= $pref .  (!is_numeric($i) ? "'" . str_replace("'", "\\'", $i) . "' => " : "") .
		(is_array($v) ? self::encode($v, $pref . "\t") : (is_numeric($v) ? $v : (is_bool($v) ? ($v ? 'true' : 'false') : "'" . str_replace("'", "\\'", $v)) . "'")) . ",\n";
		return $res . substr($pref, 0, -1) . ")";
	}
	public static function decode($string){
		eval("\$array = {$string};");
		return $array;
	}
}
?>