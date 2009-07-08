<?php
/**
 * @author Кваст Александр Владимирович aka Alehandr
 */
class OutH extends AOut {
	
	public function __construct($settings = null)
	{
		if ($settings != null) parent::__construct($settings);
	}
	
	/**
	 * @param $text
	 * @return OutH
	 */
	public static function H($text){
		return new OutH(array('text' => $text));
	}
	
	public function __toString(){
		return "<div class=\"b-h\">{$this->settings['text']}</div>";
	}
	
}