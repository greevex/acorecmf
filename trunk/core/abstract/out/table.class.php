<?php
/**
 * @author Кваст Александр Владимирович aka Alehandr
 */
class OutTable extends AOutContainer {

	public $array = array('th' => array(), 'table' => array());

	public function __construct($settings = null)
	{
		if ($settings != null) parent::__construct($settings);
	}

	/**
	 * @param $text
	 * @param $table_name
	 * @return OutTable
	 */
	public static function Table($text, $table_name)
	{
		return new OutTable(array('text' => $text, 'table_name' => $table_name));
	}

	/**
	 * @return OutTable
	 */
	public function setTh(){
		if (func_num_args() == 1 && is_array(func_get_arg(0))){
			$this->array['th'] = func_get_arg(0);
			return $this;
		}
		for ($i = 0 ; $i < func_num_args() ; $i++)
		{
			$this->array['th'][] = func_get_arg($i);
		}
		return $this;
	}

	/**
	 * @param $array
	 * @return OutTable
	 */
	public function setArray($array)
	{
		$this->array['table'] = $array;
		return $this;
	}

	public function __toString()
	{
		Core::GetModule('tables')->cacheSave($this->settings['table_name'], $this->array);
		return "<div class=\"b-block\"><h1>{$this->settings['text']}</h1><table><tbody></tbody></table>" .
		"<form table=\"true\" count=\"" . count($this->array['table']) . "\">" . OutInput::Hidden('table_name', $this->settings['table_name']) .
		"<div style=\"width: 30%; float:right;\"><ins><select name=\"page\"></select>" .
		"<b>Страница:</b></ins></div><div style=\"width: 30%; float:left;\"><ins><select name=\"count\">" .
		"<option value=\"10\">10</option><option value=\"25\">25</option><option value=\"50\">50</option><option value=\"100\">100</option><option value=\"250\">250</option>" .
		"</select><b>На странице:</b></ins></div><div style=\"width: 30%; float:left;\"><ins><select name=\"sort\">" .
		"<option value=\"id\">id</option></select><b>Сортировка по:</b></ins></div>" .
		"<div class=\"button\"><input type=\"submit\" value=\"просмотр\"></div></form></div>";
	}

}