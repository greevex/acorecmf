<?php
/**
 * @author Кваст Александр Владимирович
 */
abstract class AModule {
	public $mod_name = null;
	public $data = array();
	public $access = array();
	public $pages = array();
	public $events = array();
	
	public function __construct($mod)
	{
		$this->access = Access::Create(&$this, $mod);
	}
	
	public function SetName($name)
	{
		$this->mod_name = $name;
	}
	
	public function AddPage($name, $text, $info = "")
	{
		$this->pages[] = array($name, $text, $info);
	}
	
	public function AddEvent($name, $text)
	{
		$this->events[] = array($name, $text);
	}
	
	public function Access(){
		for ($i = 0 ; $i < func_num_args() ; $i++)
		{
			if (!$this->access[func_get_arg($i)])
			{
				return false;
			}
		}
		return true;
	}
	
}
?>