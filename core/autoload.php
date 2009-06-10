<?
/**
 * Функция автозагрузки классов
 *
 * @param string $classname Имя класса
 */
function __autoload($classname) {
	$root = ROOT . "/modules/";
	$core = ROOT . "/core/manager/";
	
	//Абстрактные классы
	if (preg_match("/^A[A-Z]/u", $classname)){
		$classname = strtolower(preg_replace("/^A/u", "", $classname));
		if (is_file("{$root}{$classname}.php")){
			require_once("{$root}{$classname}.php");
		} elseif (is_file("{$core}abstract/{$classname}.class.php")) {
			require_once ("{$core}abstract/{$classname}.class.php");
		}else {
			die("Невозможно найти модуль `{$classname}`!");
		}
		return;
	}

	$class = preg_replace("/([a-z])([A-Z])/u", "$1/$2", $classname);
	$class = strtolower($class);
	$classname = strtolower($classname);
	//if (is_file("{$root}{$class}/{$class}.php")) {
	//	require_once ("{$root}{$class}/{$class}.php");
	if (is_file(ROOT . "/core/{$class}.class.php")){
		require (ROOT . "/core/{$class}.class.php");
	} else if (is_file(ROOT . "/core/{$classname}.class.php")){
		require (ROOT . "/core/{$classname}.class.php");
	} elseif (is_file("{$root}{$classname}/{$classname}.php")) {
		require ("{$root}{$classname}/{$classname}.php");
	} elseif (is_file("{$core}abstract/{$class}.class.php")) {
		require ("{$core}abstract/{$class}.class.php");
	} elseif (is_file("{$core}abstract/{$classname}.class.php")) {
		require ("{$core}abstract/{$classname}.class.php");
	} else {
		die("Невозможно найти модуль `{$classname}`!");
	}
}
?>