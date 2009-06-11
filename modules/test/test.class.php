<?
class Test {
	
	public $data = array();
	
	public function __construct(){
		$this->data['test'] = "Success!";
	}
	
}
Core::AddModule(new Test());
?>