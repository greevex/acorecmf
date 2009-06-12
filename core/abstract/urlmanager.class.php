<?php
class AUrlManager
{
	public static $url = array();

	public static function ProcessRequest()
	{
		self::ParseUri();
		
		if (count(self::$url) == 0) return array('ajax' => false);
		
		if (!self::ParseLanguage()) return array('ajax' => false);
		if (!self::ParseManagerPart()) return array('ajax' => false);
		if (self::IsAjaxRequest())
		return array('ajax' => true);

		self::ParsePage();
		return array('ajax' => false);
	}

	protected static function ParseUri()
	{
		if (!isset($_SERVER['REDIRECT_URL'])) return;
		
		$url = str_replace(str_replace($_SERVER['DOCUMENT_ROOT'], '', ROOT), '', $_SERVER['REDIRECT_URL']);
		if (strlen($url) > 1)
		$url = trim($url, '/');

		self::$url = explode('/', $url);
	}

	protected static function ParseLanguage()
	{
		if (!isset(self::$url[0])) return false;

		if (isset(Core::$config['languages'][self::$url[0]]))
		{
			Core::$language = self::$url[0];
			Core::$data['lang_root'] .= '/' . self::$url[0];
			array_splice(self::$url, 0, 1);
		}
		return true;
	}

	protected static function ParseManagerPart()
	{
		if (!isset(self::$url[0])) return false;

		if (self::$url[0] == "manager")
		{
			Core::$main_folder = "/manager";
			define("MANAGED", true);
			array_splice(self::$url, 0, 1);
		}
		else if (Core::$config['status']=='off' && !isset($_SESSION['manager_name']))
		{
			die("Сайт находится в режиме \"только для менеджеров\"!");
		}
		return true;
	}

	protected static function IsAjaxRequest()
	{
		if (!isset(self::$url[0])) return false;

		return (self::$url[0] == 'ajax');
	}

	protected static function ParsePage()
	{
		if (empty(self::$url[0])) return;

		while (count(self::$url) > 0)
		{
			$page = implode('/', self::$url);
			if (is_file(ROOT . Core::$main_folder . "/pages/" . $page . ".php")
				|| is_file(ROOT . Core::$main_folder . "/pages/" . $page . "." . Core::$language . ".php")
				|| is_file(ROOT . Core::$main_folder . "/pages/" . $page . ".html")
				|| is_file(ROOT . Core::$main_folder . "/pages/" . $page . "." . Core::$language . ".html"))
			{
				Core::$page = $page;
				return;
			}
			unset(self::$url[count(self::$url) - 1]);
		}
		die("Страница не найдена!");
	}
}
?>