<?
/**
 * @author Кваст Александр Владимирович
 */
class OutCheck extends AOutContainer {
	
	public function __construct($settings = null)
	{
		if ($settings != null) parent::__construct($settings);
	}
	
	public static function Box($name, $text, $checked = false, $info = null){
		return new OutCheck(array('name' => $name, 'text' => $text, 'checked' => $checked, 'info' => $info));
	}
	
	public function __toString()
	{
		return "<label class=\"checkbox\"><input type=\"checkbox\" " .
		"name=\"{$this->settings['name']}\">{$this->settings['text']}" .
		($this->settings['info'] != null ? "<b>{$this->settings['info']}</b>" : "") .
		"</label>";
	}
	
}
?>