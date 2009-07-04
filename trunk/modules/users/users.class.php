<?php

class Users {

	public $data = array();
	
	private $session;
	private $cookies;
	
	private $user_id = 0;
	
	private $config;

	public function __construct(){
		$this->config = Config::Load('users', 'users');
		
		$this->session = HttpSession::GetSession();
		$this->cookies = HttpRequest::GetCookies();
		
		Events::Reserve("core", "AddModule", $this, 'Authorize');
		Events::Reserve("core", "AddModule", $this, 'Actions');
	}
	
	/**
	 * Обработка форм
	 */
	public function Actions(){
		switch ($_POST['users_action']){
			case 'enter':
				if (!isset($this->session['user_id'])) $this->Enter();
				break;
			case 'exit':
				if (isset($this->session['user_id'])) $this->Quit();
				break;
			case 'register':
				if (!isset($this->session['user_id'])) $this->Register();
				break;
			default:
				break;
		}
	}
	
	/**
	 * Функция входа
	 */
	public function Enter(){
		$vars = array();
		foreach ($this->config['enter_by'] as $name => $func) {
			$vars[] = trim($_POST[$name]);
			if ($func !== null){
				$vars[count($vars) - 1] = $func($vars[count($vars) - 1]);
			}
		}
		
		$result = DB::GetPDO()->prepare("SELECT * FROM " . DB::GetPref() . "users WHERE " . implode(" = ? AND ", array_keys($this->config['enter_by'])) . " = ? LIMIT 1");
		$result->execute($vars);
		if ($result->rowCount() == 1){
			$user = $result->fetch(PDO::FETCH_ASSOC);
			$this->session['user_id'] = $this->user_id = $user['id'];
			foreach ($user as $i => $val){
				$this->data['user_' . $i] = htmlspecialchars($val);
			}
			reset($user);
			
			Events::EvalEvent("users", "EnterComplite");
		} else {
			Events::EvalEvent("users", "EnterFail");
		}
	}
	
	/**
	 * Функция выхода
	 */
	public function Quit(){
		unset($this->session['user_id']);
		$this->user_id = 0;
		
		Events::EvalEvent("users", "Quit");
	}
	
	/**
	 * Функция регистрации
	 */
	public function Register(){
		$vars = array();
		$query = "";
		foreach ($this->config['register_by'] as $name => $params) {
			$query .= ($query != "" ? ", " : "") . "?";
			if ($params[0] !== null && !preg_match($params[0], $_POST[$name])){
				$this->data['register_result'] = $params[1];
				
				Events::EvalEvent("users", "RegisterFail");
				return;
			}
			$vars[] = $_POST[$name];
			if ($params[2] !== null){
				$vars[count($vars) - 1] = $params[2]($vars[count($vars) - 1]);
			}
		}
		
		$result = DB::GetPDO()->prepare("INSERT INTO " . DB::GetPref() . "users (" . implode(", ", array_keys($this->config['register_by'])) . ") VALUES ({$query})");
		$result->execute($vars);
		
		if ($result->errorCode() != 0){
			$this->data['register_result'] = "Такой пользователь уже существует!";
			Events::EvalEvent("users", "RegisterFail");
		} else {
			$this->data['register_result'] = "Вы успешно зарегистрировались!";
			Events::EvalEvent("users", "RegisterComplite");
		}
	}

	/**
	 * Функция авторизации через сессии
	 */
	public function Authorize(){
		if (isset($this->session['user_id'])){
			$this->user_id = (int)$this->session['user_id'];
			
			$result = DB::GetPDO()->prepare("SELECT * FROM " . DB::GetPref() . "users WHERE id = ? LIMIT 1");
			$result->execute(array($this->user_id));
			$user = $result->fetch(PDO::FETCH_ASSOC);
			foreach ($user as $i => $val){
				$this->data['user_' . $i] = htmlspecialchars($val);
			}
			reset($user);
		}
	}
	
	/**
	 * Шаблоння функция проверки на авторизованность пользователя
	 *
	 * @param array $data
	 * @return string
	 */
	public function isUser($data){
		$data['true'] = isset($data['true']) ? $data['true'] : "";
		$data['false'] = isset($data['false']) ? $data['false'] : "";
		if ($this->user_id !== 0){
			return $data['true'];
		}
		return $data['false'];
	}

}