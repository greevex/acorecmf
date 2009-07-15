<?php
/**
 * @author Кваст Александр Владимирович aka Alehandr
 */
class Users {

	public $data = array();
	
	private $session;
	private $cookies;
	
	public $user_id = 0;
	public $config;

	public function __construct(){
		$this->config = Config::Load('users', 'users');
		
		$this->session = &HttpSession::GetSession();
		$this->cookies = &HttpRequest::GetCookies();
		
		Events::Reserve("core", "AddModule", $this, 'Authorize');
		Events::Reserve("core", "AddModule", $this, 'Actions');
	}
	
	/**
	 * Обработка форм
	 */
	public function Actions(){
		if (isset($_POST['users_action']))
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
			if ($func != ''){
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
			return true;
		}
		
		$this->data['enter_result'] = $this->config['enter_error'];
		Events::EvalEvent("users", "EnterFail");
		return false;
	}

	public function ajax_enter(){
		return array('result' => $this->Enter(), 'data' => $this->data);
	}
	
	/**
	 * Функция выхода
	 */
	public function Quit(){
		unset($this->session['user_id']);
		$this->user_id = 0;
		
		Events::EvalEvent("users", "Quit");
	}

	public function ajax_exit(){
		$this->Quit();
		return array();
	}
	
	/**
	 * Функция регистрации
	 */
	public function Register(){
		
		$validate = FormValidator::Validate('register', true);
		if ($validate !== true){
			$this->data['register_error'] = '';
			foreach ($validate as $field => $errors){
				$this->data['register_' . $field . "_error"] = implode('<br>', $errors);
				if ($this->data['register_error'] != '') $this->data['register_error'] .= "<br>";
				$this->data['register_error'] .= $this->data['register_' . $field . "_error"]; 
			}
			Events::EvalEvent("users", "RegisterFail");
			return false;
		}
		
		$vars = array();
		$query = "";
		foreach ($this->config['register_by'] as $name => $func) {
			$query .= ($query != "" ? ", " : "") . "?";
			$vars[] = $_POST[$name];
			if ($func != ''){
				$vars[count($vars) - 1] = $func($vars[count($vars) - 1]);
			}
		}
		
		$result = DB::GetPDO()->prepare("INSERT INTO " . DB::GetPref() . "users (" . implode(", ", array_keys($this->config['register_by'])) . ") VALUES ({$query})");
		$result->execute($vars);
		
		if ($result->errorCode() != 0){
			$this->data['register_result'] = $this->config['register_error'][Core::$language];
			Events::EvalEvent("users", "RegisterFail");
			return false;
		}
		
		$this->data['register_result'] = $this->config['register_complite'][Core::$language];
		Events::EvalEvent("users", "RegisterComplite");
		return true;
	}

	public function ajax_register(){
		return array('result' => $this->Register(), 'data' => $this->data);
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
	 * Шаблонная функция проверки на авторизованность пользователя
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