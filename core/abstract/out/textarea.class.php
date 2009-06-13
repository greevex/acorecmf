<?
/**
 * @author Кваст Александр Владимирович
 */
class OutTextarea extends AOut {

	public function __construct($settings = null)
	{
		if ($settings != null) parent::__construct($settings);
	}

	public static function Textarea($name, $text, $info, $value = '', $notnull = null)
	{
		return new OutTextarea(array('text' => $text, 'info' => $info, 'name' => $name, 'value' => $value, 'notnull' => $notnull));
	}

	public function __toString()
	{
		return "<div><ins>{$this->settings['text']}<textarea name=\"{$this->settings['name']}\"" .
		($this->settings['notnull'] != null ? " notnull=\"{$this->settings['notnull']}\"" : '') .
		">". htmlspecialchars($this->settings['value']) . "</textarea>" .
		($this->settings['info'] != null ? "<b>{$this->settings['info']}</b>" : '') . "</ins></div>";
	}
	
}
?>