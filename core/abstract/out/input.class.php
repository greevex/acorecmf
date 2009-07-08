<?
/**
 * @author Кваст Александр Владимирович
 */
class OutInput extends AOut {
	
	protected $settings = array (
	"text"=>null,
	"type"=>"text",
	"name"=>null,
	"value"=>null,
	"notnull"=>null
	);

	public function __construct($settings = null)
	{
		if ($settings != null) parent::__construct($settings);
	}

	/**
	 * @param $name
	 * @param $value
	 * @return OutInput
	 */
	public static function Hidden($name, $value)
	{
		return new OutInput(array('type' => 'hidden', 'name' => $name, 'value' => $value));
	}

	/**
	 * @param $name
	 * @param $text
	 * @param $info
	 * @param $value
	 * @param $notnull
	 * @return OutInput
	 */
	public static function Text($name, $text, $info = null, $value = null, $notnull = null)
	{
		return new OutInput(array('type' => 'text', 'text' => $text, 'info' => $info, 'name' => $name, 'value' => $value, 'notnull' => $notnull));
	}
	
	public static function MLText($name, $text, $info = null, $value = null, $notnull = null)
	{
		$res = "";
		foreach (Core::$config['languages'] as $pref => $language)
		$res .= new OutInput(array('type' => 'text', 'text' => "[{$language}] ".$text, 'info' => $info, 'name' => $name . "[{$pref}]", 'value' => (is_array($value) ? $value[$pref] : ''), 'notnull' => $notnull));
		return $res;
	}
	
	/**
	 * @param $name
	 * @param $text
	 * @param $info
	 * @param $notnull
	 * @return OutInput
	 */
	public static function File($name, $text, $info = null, $notnull = null)
	{
		return new OutInput(array('type' => 'file', 'text' => $text, 'info' => $info, 'name' => $name, 'notnull' => $notnull));
	}

	/**
	 * @param $name
	 * @param $value
	 * @param $confirm
	 * @return OutInput
	 */
	public static function Submit($name, $value, $confirm = null)
	{
		return new OutInput(array('type' => 'submit', 'name' => $name, 'value' => $value, 'confirm' => $confirm));
	}

	public function __toString()
	{
		if ($this->settings['type'] == "submit")
		{
			return "<div class=\"button\"><input type=\"submit\" name=\"{$this->settings['name']}\"".
				($this->settings['value'] !== null?" value=\"{$this->settings['value']}\"":"") .
				($this->settings['confirm'] !== null?" confirm=\"{$this->settings['confirm']}\"":"") . "></div>";
		}
		if ($this->settings['type'] == "hidden"){
			return "<input type=\"hidden\" name=\"{$this->settings['name']}\" value=\"{$this->settings['value']}\">";
		}
		return "<div><ins>" .
		"<input type=\"{$this->settings['type']}\" name=\"{$this->settings['name']}\"" .
		($this->settings['value'] !== null?" value=\"{$this->settings['value']}\"":"") .
		($this->settings['notnull'] !== null?" notnull=\"{$this->settings['notnull']}\"":"") . 
		">{$this->settings['text']}" . ($this->settings['info'] != '' ? "<b>{$this->settings['info']}</b>" : '') . "</ins></div>";
	}
	
}