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
		//добавляем в путь core папку abstract, так как класс абстрактный и подключаем.
		if(is_file("{$core}abstract/{$classname}.class.php")) {
			require("{$core}abstract/{$classname}.class.php");
		} else {
			throw new Exception("Невозможно найти абстракный модуль `{$class}`!");
		}
	} else {
		//Для выводных классов OutName
		if (substr($classname, 0, 3) == "Out" && strlen($classname) > 3) {
			$core .= "abstract/out/";
			$classname = substr($classname, 3);
		}
		#Переводим в нижний регистр
		$classname = strtolower($classname);
		#Подключаем.
		if (is_file("{$core}{$classname}.class.php")) {
			require("{$core}{$classname}.class.php");
		} else {
			throw new Exception("Невозможно найти модуль `{$class}`!");
		}
	}
}
?>