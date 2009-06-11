<?
/**
 * Функция автозагрузки классов
 *
 * @param string $classname Имя класса
 */
function __autoload($classname) {
	$core = ROOT."/core/";
	
	//Для вывода в исходном виде
	$class = $classname;
	
	#Проверяем, асбтрактный ли класс
	if ($classname{0} == 'A' && (strtolower($classname{1})!=$classname{1})) {
		//убираем первую букву А, чтобы подключить файл
		$classname = strtolower(substr($classname, 1, (strlen($classname)-1)));
		//добавляем в путь core папку abstract, так как класс абстрактный
		if(is_file("{$core}abstract/{$classname}.class.php"))
			require("{$core}abstract/{$classname}.class.php");
		else exit("Невозможно найти абстракный модуль `{$class}`!");
	} else {
		#Переводим в нижний регистр
		$classname = strtolower($classname);
		#Подключаем. Сначала проверяем не переопределен ли как модуль.
		if (is_file("{$core}{$classname}.class.php"))
				require("{$core}{$classname}.class.php");
		else 	exit("Невозможно найти модуль `{$class}`!");
	}
}
?>