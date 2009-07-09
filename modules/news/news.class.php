<?php
/**
 * @author Кваст Александр Владимирович aka Alehandr
 */
class News {
	
	public $data = array();
	public $config = array();
	
	public function __construct(){
		$this->config = Config::Load('news', 'news');
	}
	
	public function getNews($data){
		$res = '';
		
		$data['count'] = isset($data['count']) ? (int)$data['count'] : (int)$this->config['default_count'];
		$data['tpl'] = isset($data['tpl']) ? $data['tpl'] : $this->config['default_tpl'];
		$data['sep'] = isset($data['sep']) ? $data['sep'] : $this->config['default_sep'];
		
		$result = DB::GetPDO()->query('SELECT * FROM ' . DB::GetPref() . 'news ORDER BY date DESC LIMIT ' . $data['count']);
		while($this->data = $result->fetch(PDO::FETCH_ASSOC)){
			$this->data['title'] = json_decode($this->data['title'], true);
			$this->data['text'] = json_decode($this->data['text'], true);
			if ($res != '') $res .= $data['sep'];
			$res .= Tpl::Get($data['tpl']);
		}
		
		return $res;
	}
	
}