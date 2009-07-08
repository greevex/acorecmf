<?php
/**
 * @author Кваст Александр Владимирович aka Alehandr
 */
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
	
	public static function Arr($arr){
		return new OutBlock(array('type' => 'array', 'text' => self::ArrayToText($arr)));
	}
	
	private static function ArrayToText($arr, $pref = ''){
		$text = "";
		foreach($arr as $i => $v){
			$text .= $text == "" ? "" : "<br>";
			$text .= "{$pref}<b>[{$i}]</b>: " . (is_array($v) ? "<br>" . self::ArrayToText($v, $pref . "&nbsp;&nbsp;&nbsp;") : htmlspecialchars($v));
		}
		return $text;
	}
	
	public function __toString(){
		if ($this->settings['type'] == 'menu'){
			return "<div class=\"b-mmenu\">{$this->innerHTML}</div>";
		}
		if ($this->settings['type'] == 'array'){
			return "<div class=\"b-array\">{$this->settings['text']}</div>";
		}
		return "<div class=\"b-mblock\"><h1>{$this->settings['text']}</h1>{$this->innerHTML}</div>";
	}
	
}