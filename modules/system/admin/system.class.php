<?
/**
 * Основной модуль панели менеджеров
 * 
 * @author  Кваст Александр Владимирович
 */
class System extends AModule {

	public $login = null;
	public $config = array();

	/**
	 * Конструктор
	 */
	public function __construct()
	{
		$this->config = Config::Load("system", "system");

		$this->SetName("Основное");

		$this->AddPage("userPage", "Личные настройки", "смена пароля и почты");
		$this->AddEvent("editUser", "Редактирование личных настроек");

		$this->AddPage("settingsPage", "Основные настройки", "языки и пр.");
		$this->AddEvent("editSettings", "Редактирование основных настроек");

		$this->AddPage("managersPage", "Менеджеры проекта");
		$this->AddEvent("editManagers", "Редактирование менеджеров");

		$this->AddPage("mTypesPage", "Привелегии менеджеров", "распределение ролей");
		$this->AddEvent("editMTypes", "Редактирование привелегий менеджеров");

		$this->AddPage("constsPage", "Константы", "часто используемые выражения");
		$this->AddEvent("editConsts", "Редактирование констант");

		$this->AddPage("pagesPage", "Страницы");
		$this->AddEvent("editPages", "Редактирование страниц");

		$this->AddPage("tplsPage", "Шаблоны", "часто используемые блоки");
		$this->AddEvent("editTpls", "Редактирование шаблонов");
		
		$session = HttpSession::GetSession();

		if (isset($session['manager_name']) && isset($this->config['managers'][$session['manager_name']])){
			$this->data['manager_name'] = $this->login = $session['manager_name'];
		} else {
			unset($session["manager_name"]);
			return;
		}

		parent::__construct('system');
	}

	function getMain($data){
		if (!isset($data['design'])){
			$data['design'] = "";
		} else {
			$data['design'] .= "/";
		}
		if (!session_is_registered("manager_name")){
			return TPL::Get($data['design'] . "_enter");
		} else {
			return TPL::Get($data['design'] . "_panel");
		}
	}

	/**
	 * Функция авторизации
	 *
	 * @return array
	 */
	function ajax_enter(){
		$res = array();
		if ($this->config['managers'][$_POST['login']]['pass'] != $_POST['pass']){
			$res['res'] = "err";
		} else {
			$res['res'] = "entered";
			$session = HttpSession::GetSession();
			$session['manager_name'] = $_POST['login'];
			$res['alert'] = $session['manager_name'];
		}
		return $res;
	}

	/**
	 * Функция выхода
	 *
	 * @return array
	 */
	function ajax_exit(){
		$res = array();
		$res['err'] = "exit";
		session_unregister("manager_name");
		return $res;
	}

	/**
	 * Функция вывода меню в панель
	 *
	 * @param array $data
	 * @return array
	 */
	public function getMenu($data)
	{
		$res = "<h1 class=\"menusection\">{$this->mod_name}</h1><ul class=\"menusection\">";
		foreach ($this->pages as $page){
			$res .= "<li>" . OutLink::Module('system', $page[0], "{$page[1]}" . ($page[2] != "" ? "<b>{$page[2]}</b>" : ""), '') . "</li>";
		}
		$res .= "</ul>";

		$dir = ROOT . "/modules/";
		$modules = scandir($dir);
		foreach ($modules as $i => $mod){
			if (!is_dir($dir . $mod) || $mod{0} == "." || $mod == "system") continue;
			try {
				$mod = &Core::GetModule(str_replace('.class.php', '', $mod));
			} catch (Exception $ex){
				continue;
			}
			if ($mod->mod_name !== null){
				$res .= "<h1 class=\"menusection\">{$mod->mod_name}</h1><ul class=\"menusection\">";
				foreach ($mod->pages as $page){
					$res .= "<li>" . OutLink::Module(strtolower(get_class($mod)), $page[0], "{$page[1]}" . ($page[2] != "" ? "<b>{$page[2]}</b>" : ""), '') . "</li>";
				}
				$res .= "</ul>";
			}
		}

		return $res;
	}

	/**
	 * Страница личных настроек
	 *
	 * @return array
	 */
	public function ajax_userPage()
	{
		if (!$this->Access('userPage')) return array('err' => 'true');
		$res = array();

		$res['content'] = "" .
		OutForm::Form('Смена пароля', 'system', 'userChange')
		->add(OutInput::Text('old_pass', 'Старый пароль:', '', null, 'Введите старый пароль!'))
		->add(OutInput::Text('new_pass', 'Новый пароль:', 'для вашей же безопастности не используйте слишком короткие и простые пароли', null, 'Введите новый пароль!'))
		->add(OutInput::Text('new_pass2', 'Новый пароль:', 'повторите еще раз', null, 'Введите новый пароль повторно!'))
		->add(OutInput::Submit('changePass', 'изменить')) .
		OutForm::Form('Смена почтового адреса', 'system', 'userChange')
		->add(OutInput::Text('mail', 'Новый почтовый адрес:', 'учтите, что данный почтовый адрес будет использоваться для восстановления пароля', null, 'Введите новый почтовый адрес!'))
		->add(OutInput::Submit('changeMail', 'изменить')) .
		OutForm::Form('Блокнот', 'system', 'userChange')
		->add(OutTextarea::Textarea('text', 'Личные заметки:', null, $this->config['managers'][$this->login]['notepad']))
		->add(OutInput::Submit('changeNotepad', 'сохранить'));

		return $res;
	}

	/**
	 * Изменение личный настроек
	 *
	 * @return array
	 */
	public function ajax_userChange(){
		if (!$this->Access('userPage', 'editUser')) return array('err' => 'true');
		if (isset($_POST['changePass'])){
			if ($_POST['old_pass'] != $this->config['managers'][$this->login]['pass']){
				return array('res' => 'Не верный старый пароль!');
			}
			if ($_POST['new_pass'] != $_POST['new_pass2']){
				return array('res' => 'Пароли не совпадают!');
			}
			$this->config['managers'][$this->login]['pass'] = $_POST['new_pass'];
			Config::Save('system', $this->config, 'system');
			return array('res' => 'Пароль изменен!');
		}
		if (isset($_POST['changeMail'])){
			$this->config['managers'][$this->login]['mail'] = $_POST['mail'];
			Config::Save('system', $this->config, 'system');
			return array('res' => 'Почтовый адрес изменен!');
		}
		if (isset($_POST['changeNotepad'])){
			$this->config['managers'][$this->login]['notepad'] = $_POST['text'];
			Config::Save('system', $this->config, 'system');
			return array('res' => 'Заметки сохранены!');
		}
		return array('res' => 'Неизвестное действие!');
	}

	/**
	 * Страница основных настроек
	 *
	 * @return array
	 */
	public function ajax_settingsPage(){
		if (!$this->Access('settingsPage')) return array('err' => 'true');
		$res = array();

		$res['content'] = "" .
		OutForm::Form('Статус сайта:', 'system', 'editSettings')
		->add(OutSelect::Select('status', 'Статус сайта:')
		->add('on', 'Рабочее состояние!', Core::$config['status'] == 'on')->add('off', 'Только для менеджеров!', Core::$config['status'] == 'off'))
		->add(OutInput::Submit('changeStatus', 'сохранить', 'Действительно изменить статус сайта?'));

		$languages = OutSelect::Select("language", "Язык по умолчанию:", "данный язык будет использоваться по умолчанию, если не выбран другой");
		foreach (Core::$config['languages'] as $pref => $language){
			$languages->add($pref, $language, $pref == Core::$config['default_language']);
		}

		$res['content'] .=
		OutForm::Form('Язык по умолчанию:', 'system', 'editSettings')
		->add($languages)
		->add(OutInput::Submit('changeDefaultLanguage', 'сохранить')) .
		OutForm::Form('Добавить язык:', 'system', 'editSettings')
		->add(OutInput::Text('pref', 'Префикс:', '2 символа', null, 'Введите префикс!'))
		->add(OutInput::Text('language', 'Имя языка:', null, null, 'Введите название языка!'))
		->add(OutInput::Submit('addLanguage', 'добавить')) .
		OutH::H("Языки:");

		foreach (Core::$config['languages'] as $pref => $language){
			$res['content'] .= OutForm::Form($language . ":", 'system', 'editSettings')
			->add(OutInput::Hidden('old_pref', $pref))
			->add(OutInput::Text('pref', "Префикс:", "2 символа", $pref, "Введите префикс языка!"))
			->add(OutInput::Text('language', "Имя языка:", null, $language, "Введите название языка!"))
			->add(OutInput::Submit('editLanguage', 'сохранить'))
			->add(OutInput::Submit('deleteLanguage', 'удалить', 'Удалить?'));
		}

		return $res;
	}

	/**
	 * Редактирование основных настроек
	 *
	 * @return array
	 */
	public function ajax_editSettings(){
		if (!$this->Access('settingsPage', 'editSettings')) return array('err' => 'true');

		if (isset($_POST['changeStatus'])){
			Core::$config['status'] = $_POST['status'];
			Config::Save('core', Core::$config);
			return array('res' => "Статус изменен!");
		}

		if (isset($_POST['changeDefaultLanguage'])){
			Core::$config['default_language'] = $_POST['language'];
			Config::Save('core', Core::$config);
			return array('res' => "Язык по умолчанию изменен!");
		}

		if (isset($_POST['addLanguage'])){
			if (isset(Core::$config['languages'][$_POST['pref']])){
				return array('res' => "Данный префикс уже используется!");
			}
			Core::$config['languages'][$_POST['pref']] = $_POST['language'];
			Config::Save('core', Core::$config);
			return array('reload' => true, 'res' => "Язык добавлен!");
		}

		if (isset($_POST['editLanguage'])){
			$reload = false;
			if ($_POST['old_pref'] != $_POST['pref']){
				unset(Core::$config['languages'][$_POST['old_pref']]);
			}
			Core::$config['languages'][$_POST['pref']] = $_POST['language'];
			Config::Save('core', Core::$config);
			if ($_POST['old_pref'] != $_POST['pref']) return array('reload' => true, 'res' => "Язык изменен!");
			return array('res' => "Язык изменен!");
		}

		if (isset($_POST['deleteLanguage'])){
			unset(Core::$config['languages'][$_POST['old_pref']]);
			Config::Save('core', Core::$config);
			return array('reload' => true, 'res' => "Язык удален!");
		}

	}

	/**
	 * Страница менеджеров
	 *
	 * @return array
	 */
	public function ajax_managersPage(){
		if (!$this->Access('managersPage')) return array('err' => 'true');
		$res = array('content' => '');

		$types = OutSelect::Select('m_type', 'Тип менеджера:');
		foreach($this->config['m_types'] as $id => $type) $types->add($id, $type['name']);

		$res['content'] .=
		OutForm::Form('Новый менеджер:', 'system', 'editManagers')
		->add(OutInput::Text('name', 'Имя менеджера:', null, null, 'Введите имя менеджера!'))
		->add($types)
		->add(OutInput::Text('pass', 'Пароль:', null, null, 'Введите пароль для менеджера!'))
		->add(OutInput::Text('mail', 'Электронная почта:'))
		->add(OutInput::Submit('addManager', 'добавить'));

		$res['content'] .= OutH::H("Менеджеры:");
		foreach ($this->config['managers'] as $name => $sett){
			$res['content'] .= OutForm::Form($name . ":", 'system', 'editManagers')
			->add(OutInput::Hidden('old_name', $name))
			->add(OutInput::Text('name', 'Имя менеджера:', null, $name, 'Введите имя менеджера!'))
			->add(str_replace("value=\"{$sett['m_type']}\"", "value=\"{$sett['m_type']}\" selected", $types))
			->add(OutInput::Text('mail', 'Электронная почта:', null, $sett['mail']))
			->add(OutInput::Text('pass', 'Новый пароль:'))
			->add(OutInput::Submit('editManager', 'сохранить'))
			->add(OutInput::Submit('deleteManager', 'удалить', 'Удалить менеджера?'));
		}

		return $res;
	}

	public function ajax_editManagers(){
		if (!$this->Access('managersPage', 'editManagers')) return array('err' => 'true');

		if (isset($_POST['addManager'])){
			if (isset($this->config['managers'][$_POST['name']])){
				return array('res' => "Менеджер с таким именем уже существует!");
			}
			$this->config['managers'][$_POST['name']] = array('pass' => $_POST['pass'], 'mail' => $_POST['mail'], 'm_type' => (int)$_POST['m_type']);
			Config::Save('system', $this->config, 'system');
			return array('reload' => true, 'res' => "Менеджер добавлен!");
		}

		if (isset($_POST['editManager'])){
			if (!isset($this->config['managers'][$_POST['old_name']])){
				return array('reload' => true, 'res' => "Менеджер не найден!");
			}
			$this->config['managers'][$_POST['name']] = array(
			'pass' => $this->config['managers'][$_POST['old_name']]['pass'],
			'mail' => $_POST['mail'],
			'm_type' => (int)$_POST['m_type']);
			if ($_POST['old_name'] != $_POST['name']) unset($this->config['managers'][$_POST['old_name']]);
			if ($_POST['pass'] != ""){
				$this->config['managers'][$_POST['name']]['pass'] = $_POST['pass'];
			}
			Config::Save('system', $this->config, 'system');
			if ($_POST['old_name'] != $_POST['name']){
				return array('reload' => true, 'res' => "Сохранено!");
			}
			return array('res' => "Сохранено!");
		}

		if (isset($_POST['deleteManager'])){
			if (!isset($this->config['managers'][$_POST['old_name']])){
				return array('reload' => true, 'res' => "Менеджер не найден!");
			}
			unset($this->config['managers'][$_POST['old_name']]);
			Config::Save('system', $this->config, 'system');
			return array('reload' => true, 'res' => "Менеджер удален!");
		}

		return array('res' => "Неизвестное действие!");
	}

	/**
	 * Страница типов привелегий
	 *
	 * @return array
	 */
	public function ajax_mTypesPage(){
		if (!$this->Access('managersPage')) return array('err' => 'true');
		$res = array('content' => '');

		$files = scandir(ROOT . "/modules/");
		$modules = array("system" => &$this);
		foreach ($files as $file){
			if (!is_dir(ROOT . "/modules/" . $file) || $file{0} == "." || $file == "system") continue;
			try {
				if (Core::GetModule($file)->mod_name !== null){
					$modules[$file] = &Core::GetModule($file);
				}
			} catch (Exception $e){ }
		}

		$checkboxes = "";
		foreach ($modules as $name => $mod){
			$block = OutBlock::Block($mod->mod_name . " (страницы):");
			foreach ($mod->pages as $page){
				$block->add(OutCheck::Box($name . '|' . $page[0], $page[1]));
			}
			$checkboxes .= $block;
			$block = OutBlock::Block($mod->mod_name . " (действия):");
			foreach ($mod->events as $event){
				$block->add(OutCheck::Box($name . '|' . $event[0], $event[1]));
			}
			$checkboxes .= $block;
		}

		$res['content'] .=
		OutForm::Form($this->config['m_types'][0]['name'], 'system', 'editMTypes')
		->add(OutInput::Hidden('id', 0))
		->add(OutInput::Text('name', 'Название:', 'данному типу доступны все привелегии', $this->config['m_types'][0]['name'], 'Введите наименование типа!'))
		->add(OutInput::Submit('editMType', 'сохранить'));

		foreach ($this->config['m_types'] as $id => $type){
			if ($id == 0) continue;

			$type_checkboxes = $checkboxes;
			foreach($type['mods'] as $check)
			$type_checkboxes = str_replace("name=\"{$check}\"", "name=\"{$check}\" checked", $type_checkboxes);

			$res['content'] .=
			OutForm::Form($type['name'], 'system', 'editMTypes')
			->add(OutInput::Hidden('id', $id))
			->add(OutInput::Text('name', 'Название:', null, $type['name'], 'Введите наименование типа!'))
			->add(OutH::H('Привелегии:'))
			->add($type_checkboxes)
			->add(OutInput::Submit('editMType', 'сохранить'))
			->add(OutInput::Submit('deleteMType', 'удалить', 'Удалить?'));
		}

		$res['content'] .=
		OutForm::Form('Новый тип привелегий менеджеров:', 'system', 'editMTypes')
		->add(OutInput::Text('name', 'Название:', null, null, 'Введите наименование типа!'))
		->add(OutH::H('Привелегии:'))
		->add($checkboxes)
		->add(OutInput::Submit('addMType', 'добавить'));

		return $res;
	}

	/**
	 * Редактирование типов привелегий
	 *
	 * @return arra
	 */
	public function ajax_editMTypes(){
		if (!$this->Access('managersPage', 'editMTypes')) return array('err' => 'true');

		if (isset($_POST['addMType'])){
			foreach($this->config['m_types'] as $m_type){
				if ($m_type == $_POST['name']){
					return array('res' => "Тип привелегий с таким названием уже существует!");
				}
			}
			$mods = array();
			foreach ($_POST as $name => $value) {
				if (strstr($name, "|")) $mods[] = $name;
			}
			$this->config['m_types'][] = array('name' => $_POST['name'], 'mods' => $mods);
			Config::Save('system', $this->config, 'system');

			return array('reload' => 'true', 'res' => "Новый тип привелегий добавлен!");
		}

		if (isset($_POST['editMType'])){
			if (!isset($this->config['m_types'][$_POST['id']])){
				return array('reload' => 'true', 'res' => "Тип привелегий не найден!");
			}
			$reload = false;
			if ($_POST['id'] != 0){
				$mods = array();
				foreach ($_POST as $name => $value) {
					if (strstr($name, "|")) $mods[] = $name;
				}
				if ($this->config['m_types'][$_POST['id']]['name'] != $_POST['name']) $reload = true;
				$this->config['m_types'][$_POST['id']] = array('name' => $_POST['name'], 'mods' => $mods);
			} else {
				$reload = true;
				$this->config['m_types'][0] = array('name' => $_POST['name']);
			}

			Config::Save('system', $this->config, 'system');

			if ($reload){
				return array('reload' => 'true', 'res' => "Тип привелегий изменен!");
			} else {
				return array('res' => "Тип привелегий изменен!");
			}
		}

		if (isset($_POST['deleteMType'])){
			if (!isset($this->config['m_types'][$_POST['id']])){
				return array('reload' => 'true', 'res' => "Тип привелегий не найден!");
			}
			foreach ($this->config['managers'] as $manager) {
				if ($manager['m_type'] == $_POST['id'])
				return array('res' => "Невозможно удалить данный тип привелегий, так как он используется!");
			}
			unset($this->config['m_types'][$_POST['id']]);
			$this->config['m_types'] = array_values($this->config['m_types']);
			foreach ($this->config['managers'] as $name => $manager){
				if ((int)$manager['m_type'] > $_POST['id']) $this->config['managers'][$name]['m_type'] = (int)$manager['m_type'] - 1;
			}
			Config::Save('system', $this->config, 'system');
			return array('reload' => 'true', 'res' => "Тип привелегий удален!");
		}

		return array('res' => "Неизвестное действие!");
	}

	/**
	 * Страница констант
	 *
	 * @return array
	 */
	public function ajax_constsPage(){
		if (!$this->Access('constsPage')) return array('err' => 'true');
		$res = array('content' => '');

		$consts = array();
		foreach (Core::$data as $name => $value){
			if ($name == 'root' || $name == 'lang_root' || $name == 'rand'){
				$consts[] = array($name, 'предопределенная константа', '', '');
				continue;
			}
			if (is_array($value)){
				$consts[] = array($name,'мультиязычная константа',
				'' . OutLink::Module('system', 'editConstPage', 'редактировать', 'Редактирование константы', array('name' => $name)),
				'' . OutLink::Ajax('system', 'editConsts', 'удалить', 'Удалить?', array('deleteConst' => true, 'name' => $name)));
			} else {
				$consts[] = array($name, 'простая константа',
				'' . OutLink::Module('system', 'editConstPage', 'редактировать', 'Редактирование константы', array('name' => $name)),
				'' . OutLink::Ajax('system', 'editConsts', 'удалить', 'Удалить?', array('deleteConst' => true, 'name' => $name)));
			}
		}

		$res['content'] .=
		OutBlock::Menu()
		->add(OutLink::Module('system', 'addConstPage', 'Добавить константу', 'Добавление константы'))
		->add(OutLink::Module('system', 'addMLConstPage', 'Добавить мультиязычную константу', 'Добавление мультиязычной константы'))
		. OutTable::Table('Константы:', 'system|consts')
		->setTh('Имя:', 'Тип:', '', '')->setArray($consts);

		return $res;
	}

	/**
	 * Страница добавления простых констант
	 *
	 * @return array
	 */
	public function ajax_addConstPage(){
		if (!$this->Access('constsPage')) return array('err' => 'true');
		$res = array('content' => '');

		$res['content'] .=
		OutForm::Form('Добавление мультиязычной константы', 'system', 'editConsts')
		->add(OutInput::Text('name', 'Имя константы:', null, null, 'Введите имя константы!'))
		->add(OutInput::Text('text', 'Содержимое:'))
		->add(OutInput::Submit('addConst', 'добавить'));

		return $res;
	}

	/**
	 * Страница добавления мультиязычных констант
	 *
	 * @return array
	 */
	public function ajax_addMLConstPage(){
		if (!$this->Access('constsPage')) return array('err' => 'true');
		$res = array('content' => '');

		$res['content'] .=
		OutForm::Form('Добавление мультиязычной константы', 'system', 'editConsts')
		->add(OutInput::Text('name', 'Имя константы:', null, null, 'Введите имя константы!'))
		->add(OutInput::MLText('text', 'Содержимое:'))
		->add(OutInput::Submit('addConst', 'добавить'));

		return $res;
	}

	/**
	 * Страница редактирования константы
	 *
	 * @return array
	 */
	public function ajax_editConstPage(){
		if (!$this->Access('constsPage', 'editConsts')) return array('err' => 'true');

		if (!isset(Core::$data[$_POST['name']])){
			return array('content' => 'Константа не найдена!');
		}

		$res = array('content' => '');

		$res['content'] .=
		OutForm::Form('Редактирование константы:', 'system', 'editConsts')
		->add(OutInput::Hidden('old_name', $_POST['name']))
		->add(OutInput::Text('name', 'Имя константы:', null, $_POST['name'], 'Введите имя константы!'))
		->add(is_array(Core::$data[$_POST['name']]) ? OutInput::MLText('text', 'Содержимое:', null, Core::$data[$_POST['name']]) : OutInput::Text('text', 'Содержимое:', null, Core::$data[$_POST['name']]))
		->add(OutInput::Submit('editConst', 'сохранить'));

		return $res;
	}

	/**
	 * Редактирование, удаление и добавление констант
	 *
	 * @return array
	 */
	public function ajax_editConsts(){
		if (!$this->Access('constsPage', 'editConsts')) return array('err' => 'true');

		if (isset($_POST['addConst'])){
			if (isset(Core::$data[$_POST['name']])){
				return array('res' => 'Константа с таким именем уже существует!');
			}
			Core::$config['consts'][$_POST['name']] = $_POST['text'];
			Config::Save('core', Core::$config);
			return array('res' => 'Константа добавлена!');
		}

		if (isset($_POST['editConst'])){
			if (!isset(Core::$data[$_POST['old_name']])){
				return array('reload' => true, 'res' => 'Константа не найдена!');
			}
			if ($_POST['old_name'] != $_POST['name']) unset(Core::$config['consts'][$_POST['old_name']]);
			Core::$config['consts'][$_POST['name']] = $_POST['text'];
			Config::Save('core', Core::$config);
			return array('res' => 'Сохранено!');
		}

		if (isset($_POST['deleteConst'])){
			if (!isset(Core::$data[$_POST['name']])){
				return array('reload' => true, 'res' => 'Константа не найдена!');
			}
			unset(Core::$config['consts'][$_POST['name']]);
			Config::Save('core', Core::$config);
			return array('reload' => true, 'res' => 'Константа удалена!');
		}

		return array('res' => "Неизвестное действие!");
	}

	public function ajax_pagesPage(){
		if (!$this->Access('pagesPage')) return array('err' => 'true');
		$res = array('content' => '');

		$pages = array();
		$this->getPagesIn('', $pages);

		$res['content'] .=
		OutTable::Table('Страницы:', 'system|pages')
		->setTh('Страница:', '', '')->setArray($pages);

		return $res;
	}

	public function getPagesIn($folder, &$array){
		$files = scandir(ROOT . "/pages" . $folder);
		foreach ($files as $file){
			if ($file{0} == '.') continue;
			if (is_dir(ROOT . "/pages" . $folder . "/" . $file)){
				$this->getPagesIn($folder . "/" . $file, $array);
			} else if (strstr($file, ".html") !== false) {
				$array[] = array("TPL: " . $folder . "/" . substr($file, 0, - 5) . "/", 'редактировать', 'удалить');
			} else if (strstr($file, ".php") !== false) {
				$array[] = array("PHP: " . $folder . "/" . substr($file, 0, - 4) . "/", 'редактировать', 'удалить');
			}
		}
	}

}
?>