<?php
/**
 * @author Кваст Александр Владимирович aka Alehandr
 */
class News extends AModule {
	
	public $config;
	
	public function __construct(){
		$this->config = Config::Load('news', 'news');
		
		$this->SetName('Новости');
		
		$this->AddPage('configPage', 'Настройки');
		$this->AddEvent('editConfig', 'Изменение настроек');
		
		$this->AddPage('addNewsPage', 'Добавить новость');
		$this->AddEvent('addNews', 'Добавление новостей');
		
		$this->AddPage('newsPage', 'Все новости');
		$this->AddEvent('editNews', 'Редактирование новостей');
		$this->AddEvent('delNews', 'Удаление новостей');
		
		parent::__construct('news');
	}
	
	/**
	 * Страница настроек
	 * @return array
	 */
	public function ajax_configPage(){
		if (!$this->Access('configPage')) return array('err' => true);
		$res = array('content' => '');
		
		$res['content'] .= OutForm::Form('Значения на вывод по умолчанию:', 'news', 'editConfig')
		->add(OutInput::Text('default_count', 'Количество новостей на вывод:', '', $this->config['default_count']))
		->add(OutInput::Text('default_tpl', 'Шаблон:', '', $this->config['default_tpl']))
		->add(OutInput::Text('default_sep', 'Разделитель:', '', $this->config['default_sep']))
		->add(OutInput::Submit(null, 'сохранить'));
		
		return $res;
	}
	
	/**
	 * Редактирование настроек
	 * @return array
	 */
	public function ajax_editConfig(){
		if (!$this->Access('configPage', 'editConfig')) return array('err' => true);
		
		$this->config['default_count'] = (int)$_POST['default_count'];
		$this->config['default_tpl'] = $_POST['default_tpl'];
		$this->config['default_sep'] = $_POST['default_sep'];
		
		Config::Save('news', $this->config, 'news');
		
		return array('res' => 'Сохранено!');
	}
	
	public function ajax_addNewsPage(){
		if (!$this->Access('addNewsPage')) return array('err' => true);
		$res = array('content' => '');
		
		$res['content'] .= OutForm::Form('Добавление новости:', 'news', 'addNews')
		->add(OutInput::MLText('title', 'Заголовок:'))
		->add(OutTextarea::MLTextarea('text', 'Текст:'))
		->add(OutInput::Text('date', 'Дата публикации:', 'если пусто то сегодняшняя дата, иначе по формату ГГГГ-ММ-ДД'))
		->add(OutInput::Submit(null, 'добавить'));
		
		return $res;
	}
	
	public function ajax_addNews(){
		if (!$this->Access('addNewsPage', 'addNews')) return array('err' => true);
		
		$vars = array(json_encode($_POST['title']), json_encode($_POST['text']));
		if (trim($_POST['date']) != ''){
			$result = DB::GetPDO()->prepare('INSERT INTO ' . DB::GetPref() . 'news (title, text, date) VALUES (?, ?, ?)');
			$vars[] = trim($_POST['date']);
		} else {
			$result = DB::GetPDO()->prepare('INSERT INTO ' . DB::GetPref() . 'news (title, text, date) VALUES (?, ?, NOW())');
		}
		
		$result->execute($vars) or DIE('Ошибка!');
		
		return array('res' => 'Добавлено!');
	}
	
	/**
	 * Страница новостей
	 * @return array
	 */
	public function ajax_newsPage(){
		if (!$this->Access('newsPage')) return array('err' => true);
		$res = array('content' => '');
		
		$news = array();
		$result = DB::GetPDO()->query('SELECT * FROM ' . DB::GetPref() . 'news ORDER BY id DESC');
		while($n = $result->fetch(PDO::FETCH_ASSOC)){
			$news[] = array($n['id'], $n['date'], OutBlock::Arr(json_decode($n['title'])),
				OutLink::Module('news', 'editNewsPage', 'редактировать', 'Редактирование новости', array('id' => $n['id'])),
				OutLink::Ajax('news', 'delNews', 'удалить', 'Удалить?', array('id' => $n['id'])),
			);
		}
		
		$res['content'] .= OutTable::Table('Новости:', 'news|news')
		->setTh('Id:', 'Дата публикации:', 'Заголовок:', '', '')
		->setArray($news);
		
		return $res;
	}
	
}