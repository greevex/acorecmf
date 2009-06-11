<?
/**
 * Функция автозагрузки классов
 *
 * @param string $classname Имя класса
 */
function __autoload($classname) {
	$modu = ROOT."/modules/";
	$core = ROOT."/core/";
	
	#Проверяем, асбтрактный ли класс
	if ($classname{0} == 'A' && (strtolower($classname[1])!=$classname[1])) {
		//убираем первую букву А, чтобы подключить файл
		$classname = substr($classname, 1, (strlen($classname)-1));
		//добавляем в путь core папку abstract, так как класс абстрактный
		$core = $core."abstract/";
	}
	
	#Переводим в нижний регистр
	$classname = strtolower($classname);
	
	#Подключаем. Сначала проверяем не переопределен ли как модуль.
	#Затем проверяем в движке. (абстракный или нет проверили выше)
	if (is_file("{$modu}{$classname}/{$classname}.php")) {
		require("{$modu}{$classname}/{$classname}.php");
	}
	elseif (is_file("{$core}{$classname}.class.php")) {
			require("{$core}{$classname}.class.php");
	} else {
		die("Невозможно найти модуль `{$classname}`!");
	}
}
?>