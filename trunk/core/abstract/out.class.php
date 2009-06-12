<?php
/**
 * @author Кваст Александр Владимирович
 */
abstract class AOut {
	protected $settings = array();
	
	function __construct($settings = array())
	{
		foreach ($settings as $name => $value){
			$this->settings[$name] = $value;
		}
	}
	
	public function __set($name, $value)
	{
		$this->settings[$name] = $value;
		return $this->settings[$name];
	}
	
	public function __get($name)
	{
		return $this->settings[$name];
	}

	abstract public function __toString();
}
?>