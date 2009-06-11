<?php
/**
 * @author Кваст Александр Владимирович
 */
abstract class AOutContainer extends AOut {
	protected $innerHTML = "";
	
	public function add()
	{
		for ($i = 0 ; $i < func_num_args() ; $i++)
			$this->innerHTML .= func_get_arg($i);
		return $this;
	}
	
	public function clear()
	{
		$this->innerHTML = "";
		return $this;
	}
}
?>