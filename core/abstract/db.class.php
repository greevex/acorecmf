<?php
class ADB {
	
	public static $config = null;
	public static $pdo = null;
	public static $error = null;
	
	public static function GetConnector(){
		if (self::$config === null) self::$config = Config::Load('db');
		return self::$config['connector'];
	}
	
	public static function GetPref(){
		if (self::$config === null) self::$config = Config::Load('db');
		return self::$config['pref'];
	}
	
	/**
	 * Возвращает PDO объект
	 * Если нет соединения, подключается
	 * В случае ошибки возвращает false
	 *
	 * @return PDO
	 */
	public static function GetPDO(){
		if (self::$config === null) self::$config = Config::Load('db');
		$config = &self::$config;
		if (self::$pdo === null){
			switch ($config['connector']){
				case 'mysql':
					try {
						self::$pdo = new PDO("mysql:dbname={$config['db']};host={$config['host']}", $config['user'], $config['pass']);
						self::$pdo->query('SET CHARACTER SET utf8');
						self::$pdo->query('SET NAMES utf8');
					} catch (PDOException $ex){
						self::$error = $ex->getMessage();
						return false;
					}
					break;
				case 'pgsql':
					try {
						self::$pdo = new PDO("pgsql:host={$config['host']} port={$config['port']} dbname={$config['db']} user={$config['user']} password={$config['pass']}");
					} catch (PDOException $ex){
						self::$error = $ex->getMessage();
						return false;
					}
					break;
				default:
					self::$error = "Неизвестный коннектор!";
					return false;
			}
		}
		return self::$pdo;
	}
	
}
?>