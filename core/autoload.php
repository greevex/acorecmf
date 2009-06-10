<?
/**
 * Функция автозагрузки классов
 *
 * @param string $classname Имя класса
 */
function __autoload($classname) {
	$root = ROOT . "/core" . (Core::$main_folder == "/manager" ? "/manager/" : "/modules/");
	
	//Абстрактные классы
	if (preg_match("/^A[A-Z]/u", $classname)){
		$classname = strtolower(preg_replace("/^A/u", "", $classname));
		if (is_file("{$root}{$classname}.class.php")){
			require_once("{$root}{$classname}.class.php");
		} elseif (is_file("{$root}abstract/{$classname}.class.php")) {
			require_once ("{$root}abstract/{$classname}.class.php");
		}else {
			die("Невозможно найти модуль `{$classname}`!");
		}
		return;
	}

	$class = preg_replace("/([a-z])([A-Z])/u", "$1/$2", $classname);
	$class = strtolower($class);
	$classname = strtolower($classname);
	if (is_file(ROOT . "/core/{$class}.class.php")) {
		require_once (ROOT . "/core/{$class}.class.php");
	} elseif (is_file(ROOT . "/core/{$classname}.class.php")) {
		require_once (ROOT . "/core/{$classname}.class.php");
	} elseif (is_file("{$root}{$class}.class.php")) {
		require_once ("{$root}{$class}.class.php");
	} elseif (is_file("{$root}{$classname}.class.php")) {
		require_once ("{$root}{$classname}.class.php");
	} else {
		die("Невозможно найти модуль `{$classname}`!");
	}
}
?>