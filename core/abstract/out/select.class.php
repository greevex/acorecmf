<?
/**
 * @author Кваст Александр Владимирович
 */
class OutSelect extends AOutContainer {
	
	public function __construct($settings = null)
	{
		if ($settings != null) parent::__construct($settings);
	}
	
	public static function Select($name, $text, $info= null){
		return new OutSelect(array('name' => $name, 'text' => $text, 'info' => $info));
	}
	
	public function add($value, $text, $selected = false){
		$this->innerHTML .= "<option value=\"{$value}\"" .
		($selected == true ? " selected" : "") . ">{$text}</option>";
		return $this;
	}
	
	public function __toString()
	{
		return "<div><ins><select name=\"{$this->settings['name']}\">{$this->innerHTML}</select>{$this->settings['text']}" .
		($this->settings['info'] != null ? "<b>{$this->settings['info']}</b>" : "") .
		"</ins></div>";
	}
	
}
?>