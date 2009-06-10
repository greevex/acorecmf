<?
class Test {
	
	public $data = array();
	
	public function __construct(){
		echo "Construct TEST!<br>";
		$this->data['test'] = "Success!";
	}
	
}
Core::AddModule(new Test());
?>