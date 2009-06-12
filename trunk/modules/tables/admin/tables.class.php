<?
class Tables extends AModule {
	
	public function __construct(){
		//parent::__construct('tables');
	}
	
	public function ajax_out(){
		$name = $_POST['table_name'];
		if ($result = $this->cacheLoad($name)){
			$keys = array();
			
			$res = "<tr>";
			foreach ($result['th'] as $val){
				$res .= "<th>{$val}</th>";
			}
			$res .= "</tr>";
			
			$start = $_POST['page'] * $_POST['count'];
			$end = $start + $_POST['count'];
			if ($end > count($result['table'])) $end = count($result['table']);
			for ($i = $start ; $i < $end ; $i++){
				$res .= "<tr>";
				foreach ($result['table'][$i] as $val){
					$res .= "<td>{$val}</td>";
				}
				$res .= "</tr>";
			}
			
			return array('res' => $res);
		}
		return array('err' => $_POST['table_name']);
	}
	
	//Функции для работы с кэш файлами

	public function cacheSave($name, $array){
		$content = Core::encode($array);
		$file = ROOT . "/core/cache/tables/" . md5($_SESSION['manager_name'] . $name) . ".php";
		file_put_contents($file, $content);
		unset($content);
	}

	public function cacheLoad($name){
		$file = ROOT . "/core/cache/tables/" . md5($_SESSION['manager_name'] . $name) . ".php";
		if (!file_exists($file)) return false;
		if (!$data = Core::decode($file)) return false;
		return $data;
	}
	
}
?>