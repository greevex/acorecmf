<?
class OutLink extends AOut {

	public function __construct($settings = null)
	{
		if ($settings != null) parent::__construct($settings);
	}
	
	public static function Module($module, $function, $text, $module_text = null,  $params = array()){
		if ($module_text == null) $module_text = $text;
		return new OutLink(array('type' => 'module', 'module' => $module, 'function' => $function, 'text' => $text, 'module_text' => $module_text, 'params' => $params));
	}
	
	public static function Ajax($module, $function, $text, $confirm = null, $params = array()){
		return new OutLink(array('type' => 'ajax', 'module' => $module, 'function' => $function, 'text' => $text, 'confirm' => $confirm, 'params' => $params));
	}
	
	public function __toString(){
		if ($this->settings['type'] == "module"){
			$res = "<a href=\"#\" onclick=\"new Module(this, '{$this->settings['module_text']}', '{$this->settings['module']}', '{$this->settings['function']}', {";
			$added = false;
			$spec = "";
			foreach ($this->settings['params'] as $name => $value) {
				$res .= "'{$name}':'{$value}', ";
				$spec .= "|{$name}={$value}";
				$added = true;
			}
			if ($added) $res = substr($res, 0, strlen($res) - 2);
			$res .= "}, '{$spec}'); return false;\">{$this->settings['text']}</a>";
			return $res;
		} elseif ($this->settings['type'] == "ajax") {
			$res = "<a href=\"#\" onclick=\"" .
			($this->settings['confirm'] != null ? "if (confirm('{$this->settings['confirm']}')) " : "") .
			"new Ajax(this, '{$this->settings['module']}', '{$this->settings['function']}', {";
			$added = false;
			foreach ($this->settings['params'] as $name => $value) {
				$res .= "'{$name}':'{$value}', ";
				$added = true;
			}
			if ($added) $res = substr($res, 0, strlen($res) - 2);
			$res .= "}); return false;\">{$this->settings['text']}</a>";
			return $res;
		}
		return "OutLink output error. Link type not found! ";
	}

}
?>