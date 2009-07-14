<?php
/**
 * @author Кваст Александр Владимирович aka Alehandr
 */
class Users extends AModule {

	public $config = array();

	public function __construct(){
		$this->config = Config::Load('users', 'users');

		$this->SetName('Пользователи');

		$this->AddPage('optionsPage', 'Настройки');
		$this->AddEvent('optionsEdit', 'Редактирование настроек');
		
		$this->AddPage('usersPage', 'Пользователи');

		parent::__construct('users');
	}
	
	//Настройки

	public function ajax_optionsPage(){
		if (!$this->Access("optionsPage")) return array("err" => true);
		$res = array('content' => '');
		
		//Сообщения при входе
		
		$res['content'] .= OutH::H('Вход в систему:') . OutForm::Form('Сообщения:', 'users', 'editEnterMsgs')
		->add(OutInput::MLText('enter_error', 'Не верные данные:', null, $this->config['enter_error']))
		->add(OutInput::Submit(null, 'сохранить'));
		
		//Поля для входа

		$enterByTable = OutTable::Table('Вход по полям:', 'users|enter_by')
		->setTh('Поле:', 'Функция шифрования:', '');

		$arr = array();
		foreach ($this->config['enter_by'] as $i => $v){
			$arr[] = array($i, $v, OutLink::Ajax('users', 'deleteEnterBy', 'удалить', 'Удалить?', array('field' => $i)));
		}
		$enterByTable->setArray($arr);
		
		$addForm = OutForm::Form('Добавить (изменить) поле:', 'users', 'addEnterBy')
		->add(OutInput::Text('field', 'Поле:', 'должно присутствовать в таблице users базы данных', null, 'Введите имя поля!'))
		->add(OutInput::Text('function', 'Функция шифрования:', 'не обязательно'))
		->add(OutInput::Submit(null, 'добавить / изменить'));
		
		$res['content'] .= $enterByTable . $addForm;
		
		//Сообщения при регистрации
		
		$res['content'] .= OutH::H('Регистрация пользователей:') . OutForm::Form('Сообщения:', 'users', 'editRegisterMsgs')
		->add(OutInput::MLText('register_error', 'Не уникальные поля:', null, $this->config['register_error']))
		->add(OutInput::MLText('register_complite', 'Успешная регистрация:', null, $this->config['register_complite']))
		->add(OutInput::Submit(null, 'сохранить'));
		
		//Поля для регистрации
		
		$registerByTable = OutTable::Table('Регистраиця по полям:', 'users|registre_by')
		->setTh('Поле:', 'Функция шифрования:', '');
		
		$arr = array();
		foreach ($this->config['register_by'] as $i => $v){
			$arr[] = array($i, $v, OutLink::Ajax('users', 'deleteRegisterBy', 'удалить', 'Удалить?', array('field' => $i)));
		}
		$registerByTable->setArray($arr);
		
		$addForm = OutForm::Form('Добавть (изменить) поле:', 'users', 'addRegisterBy')
		->add(OutInput::Text('field', 'Поле:', 'должно присутствовать в таблице users базы данных', null, 'Введите имя поля!'))
		->add(OutInput::Text('function', 'Функция шифрования:', 'не обязательно'))
		->add(OutInput::Submit(null, 'добавить / изменить'));
		
		$res['content'] .= $registerByTable . $addForm;

		return $res;
	}
	
	public function ajax_editEnterMsgs(){
		if (!$this->Access('optionsPage', 'optionsEdit')) return array("err" => true);
		
		$this->config['enter_error'] = $_POST['enter_error'];
		Config::Save('users', $this->config, 'users');
		
		return array('res' => 'Сохранено');
	}
	
	public function ajax_deleteEnterBy(){
		if (!$this->Access('optionsPage', 'optionsEdit')) return array("err" => true);
		
		unset($this->config['enter_by'][$_POST['field']]);
		Config::Save('users', $this->config, 'users');
		
		return array('res' => 'Удалено', 'reload' => true);
	}
	
	public function ajax_addEnterBy(){
		if (!$this->Access('optionsPage', 'optionsEdit')) return array("err" => true);
		
		$this->config['enter_by'][$_POST['field']] = $_POST['function'];
		Config::Save('users', $this->config, 'users');
		
		return array('res' => 'Сохранено', 'reload' => true);
	}
	
	public function ajax_editRegisterMsgs(){
		if (!$this->Access('optionsPage', 'optionsEdit')) return array("err" => true);
		
		$this->config['register_error'] = $_POST['register_error'];
		$this->config['register_complite'] = $_POST['register_complite'];
		Config::Save('users', $this->config, 'users');
		
		return array('res' => 'Сохранено');
	}
	
	public function ajax_deleteRegisterBy(){
		if (!$this->Access('optionsPage', 'optionsEdit')) return array("err" => true);
		
		unset($this->config['register_by'][$_POST['field']]);
		Config::Save('users', $this->config, 'users');
		
		return array('res' => 'Удалено', 'reload' => true);
	}
	
	public function ajax_addRegisterBy(){
		if (!$this->Access('optionsPage', 'optionsEdit')) return array("err" => true);
		
		$this->config['register_by'][$_POST['field']] = $_POST['function'];
		Config::Save('users', $this->config, 'users');
		
		return array('res' => 'Сохранено', 'reload' => true);
	}
	
	//Пользователи
	
	public function ajax_usersPage(){
		if (!$this->Access('usersPage')) return array("err" => true);
		$res = array('content' => '');
		
		$th = array(); $arr = array();
		
		$result = DB::GetPDO()->query('SELECT * FROM ' . DB::GetPref() . 'users');
		
		if ($user = $result->fetch(PDO::FETCH_ASSOC)){
			$th = array_keys($user);
			$arr[] = array_map('htmlspecialchars', array_values($user));
			//for($i = 0 ; $i < count($arr[count($arr) - 1]) ; $i++) $arr[count($arr) - 1][$i] = htmlspecialchars($arr[count($arr) - 1][$i]);
		}
		while ($user = $result->fetch(PDO::FETCH_NUM)) $arr[] = array_map('htmlspecialchars', $user);
		
		$res['content'] .= OutTable::Table('Пользователи:', 'users|users')
		->setTh($th)->setArray($arr); 
		
		return $res;
	}

}