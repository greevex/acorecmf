<?
/**
 * @author Кваст Александр Владимирович
 */
class OutForm extends AOutContainer {
	
	protected $settings = array(
	"text" => "",
	"module" => "",
	"function" => "",
	"files" => false
	);
	
	public function __construct($settings = null)
	{
		if ($settings != null) parent::__construct($settings);
	}
	
	public static function Form($text, $module, $function, $files = false){
		return new OutForm(array('text' => $text, 'module' => $module, 'function' => $function, 'files' => $files));
	}
	
	public function __toString()
	{
		return "<div class=\"b-block\"><h1>{$this->settings['text']}</h1><form" .
		" module=\"{$this->settings['module']}\" func=\"{$this->settings['function']}\"" .
		($this->settings['files'] ? " enctype=\"multipart/form-data\"" : "") .
		">{$this->innerHTML}</form></div>";
		//($this->settings['files'] ? " onsubmit=\"new FileSubmit(this); return false;\"" : " onsubmit=\"new Submit(this); return false;\"") . 
	}
	
}
?>