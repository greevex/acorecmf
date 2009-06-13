<?php
class System {
	
	public $data = array();
	
	public function getLangs($data){
		if (!isset($data['separator'])) $data['separator'] = "";
		$res = "";
		foreach (Core::$config['languages'] as $pref => $lang) {
			if ($res != "") $res .= $data['separator'];
			if (!isset($data['tpl'])){
				$res .= "<a href=\"" . Core::$data['root'] . "/{$pref}/\">{$lang}</a>";
			} else {
				$this->data['pref'] = $pref;
				$this->data['lang'] = $lang;
				$res .= Tpl::Get($data['tpl']);
			}
		}
		return $res;
	}
	
}
?>