<?
class OutBlock extends AOutContainer {
	
	public function __construct($settings = null)
	{
		if ($settings != null) parent::__construct($settings);
	}
	
	public static function Block($text){
		return new OutBlock(array('text' => $text));
	}
	
	public static function Menu(){
		return new OutBlock(array('type' => 'menu'));
	}
	
	public function __toString(){
		if ($this->settings['type'] == "menu"){
			return "<div class=\"b-mmenu\">{$this->innerHTML}</div>";
		}
		return "<div class=\"b-mblock\"><h1>{$this->settings['text']}</h1>{$this->innerHTML}</div>";
	}
	
}
?>