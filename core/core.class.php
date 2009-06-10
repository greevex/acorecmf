<?
class Core {

	public static $data = array();
	public static $config = null;
	public static $url = array();
	public static $page = "index";
	public static $language;
	public static $modules = array();

	public static $main_folder = "";

	public static $generate_start_time;

	public static function Init(){
		self::$generate_start_time = microtime();
		self::$config = Config::Load("core");
		self::$language = self::$config['default_language'];

		self::$data = Config::Load("consts");
		unset(self::$data['root']); unset(self::$data['rand']);
		self::$data['root'] = "http://" . $_SERVER['SERVER_NAME'] . str_replace($_SERVER['DOCUMENT_ROOT'], "", ROOT);
		self::$data['rand'] = rand(0, 1000000000);

		$url = str_replace(str_replace($_SERVER['DOCUMENT_ROOT'], "", ROOT), "", $_SERVER['REDIRECT_URL']);
		if (strlen($url) > 0){
			$url = substr($url, 1);
			if ($url[strlen($url)-1] == "/") $url = substr($url, 0, strlen($url)-1);
		}
		self::$url = explode("/", $url);

		foreach (self::$config['languages'] as $pref => $name){
			if (self::$url[0] == $pref){
				self::$language = $pref;
				self::$data['root'] .= "/$pref";
				unset(self::$url[0]);
				$url = implode("/", self::$url);
				self::$url = explode("/", $url);
				break;
			}
		}

		if (self::$url[0] == "manager"){
			self::$main_folder = "/manager";
			unset(self::$url[0]);
			self::$url = implode("/", self::$url);
			self::$url = explode("/", self::$url);
		} else if (self::$config['status']=='off' && !isset($_SESSION['manager_name'])){
			die("Сайт находится в режиме \"только для менеджеров\"!");
		}

		if (self::$url[0] == "ajax") return self::AjaxOut();

		if (self::$url[0] != ""){
			$url = self::$url;
			while (count($url) > 0){
				$page = implode("/", $url);
				if (is_file(ROOT . self::$main_folder . "/pages/" . $page . ".html") || is_file(ROOT . self::$main_folder . "/pages/" . $page . "." . self::$language . ".html")){
					self::$page = $page;
					return self::Out();
				}
				unset($url[count($url) - 1]);
			}
			die("Страница не найдена!");
		}
		return self::Out();
	}

	public static function AddModule($mod){
		self::$modules[strtolower(get_class($mod))] = &$mod;
	}

	public static function GetModule($mod){
		require_once(ROOT . "/core" . (Core::$main_folder == "/manager" ? "/manager/" : "/modules/") . "{$mod}.class.php");
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
}
?>