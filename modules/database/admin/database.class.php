<?
class DataBase extends AModule {
	
	public $config;
	
	public function __construct(){
		$this->config = Config::Load('db');
		$this->SetName('Базы данных');
		
		$this->AddPage('configPage', 'Конфигурация', 'настройки подключения');
		$this->AddEvent('editConfig', 'Изменение конфигурации');
		
		$this->AddPage('tablesPage', 'Таблицы');
		
		parent::__construct('db');
	}
	
	public function ajax_configPage(){
		if (!$this->Access('configPage')) return array('err' => true);
		$res = array('content' => '');
		
		$connectors = OutSelect::Select('connector', 'Коннектор:');
		$drivers = PDO::getAvailableDrivers();
		foreach ($drivers as $connector){
			$connectors->add($connector, $connector, $this->config['connector'] == $connector);
		}
		
		$res['content'] .=
		OutForm::Form('Настройки:', 'database', 'editConfig')
		->add($connectors)
		->add(OutInput::Text('host', 'Хост:', null, $this->config['host']))
		->add(OutInput::Text('port', 'Порт:', 'не для всех коннекторов', $this->config['port']))
		->add(OutInput::Text('user', 'Логин:', null, $this->config['user']))
		->add(OutInput::Text('pass', 'Пароль:', null, $this->config['pass']))
		->add(OutInput::Text('db', 'База данных:', null, $this->config['db']))
		->add(OutInput::Text('pref', 'Префикс к таблицам:', null, $this->config['pref']))
		->add(OutInput::Submit('editConfig', 'сохранить'));
		
		$res['content'] .= OutH::H('Статус:');
		$db = DB::GetPDO();
		if ($db === false){
			$res['content'] .= DB::$error;
		} else {
			$res['content'] .= 'Соединение установлено!';
		}
		
		return $res;
	}
	
	public function ajax_editConfig(){
		if (!$this->Access('configPage', 'editConfig')) return array('err' => true);
		
		if (isset($_POST['editConfig'])){
			$params = array('connector', 'host', 'port', 'user', 'pass', 'db', 'pref');
			foreach ($params as $param) $this->config[$param] = $_POST[$param];
			Config::Save('db', $this->config);
			return array('reload' => true, 'res' => 'Конфигурация сохранена!');
		}
		
		return array('res' => 'Неизвестное действие!');
	}
	
	public function ajax_tablesPage(){
		if (!$this->Access('tablesPage')) return array('err' => true);
		$res = array('content' => '');
		
		if (DB::GetPDO() === false){
			$res['content'] .= OutH::H("Ошибка!") . DB::$error;
			return $res;
		}
		
		if (DB::GetConnector() == 'pgsql'){
			$result = DB::GetPDO()->query("SELECT tablename FROM pg_tables WHERE tablename NOT LIKE 'pg%' AND schemaname != 'information_schema'");
		} else if (DB::GetConnector() == 'mysql'){
			$result = DB::GetPDO()->query("SHOW TABLES");
		} else {
			$res['content'] .= "Не поддерживаемый коннектор!";
			return $res;
		}
		
		$tables = array();
		while ($row = $result->fetch(PDO::FETCH_NUM)){
			$table_name = $row[0];
			$rows = DB::GetPDO()->query("SELECT COUNT(*) FROM {$table_name}", PDO::FETCH_NUM);
			$rows = $rows->fetch(); $rows = $rows[0];
			$tables[] = array($table_name, $rows,
			'' . OutLink::Module('database', 'tableRowsPage', 'содержимое', 'Содержимое таблицы', array('table' => $table_name)));
		}
		$res['content'] .=
		OutTable::Table('Таблицы:', 'database|tables')
		->setTh('Имя таблицы:', 'Количество строк:', '')
		->setArray($tables);
		
		return $res;
	}
	
	public function ajax_tableRowsPage(){
		if (!$this->Access('tablesPage')) return array('err' => true);
		$res = array('content' => '');

		$result = DB::GetPDO()->query("SELECT * FROM {$_POST['table']}");
		
		$rows = array();
		$th = array('Таблица пуста!');
		while($row = $result->fetch(PDO::FETCH_ASSOC)){
			$th = array_keys($row);
			for ($i = 0 ; $i < count($row) ; $i++) $row[$th[$i]] = htmlspecialchars($row[$th[$i]]);
			$rows[] = array_values($row);
		}
		
		$res['content'] .=
		OutTable::Table("Содержимое таблицы {$_POST['table']}:", 'tables|table_rows')->setTh($th)->setArray($rows);
	
		return $res;
	}
	
}
Core::AddModule(new DataBase());
?>