<?php
class ATpl {

	public static $tpls;
	public static $cache = null;
	public static $cacheTable = array();
	public static $cacheUpdated = false;

	public static function Get($tpl, $cache = false){
		if ($cache){
			self::$cache = Core::$main_folder . "|" . $tpl . "|" . Core::$language;
			self::LoadCache();
		}

		//Если кэшируем
		if (self::$cache != null){
			if (!isset(self::$cacheTable[$tpl])){
				self::$cacheUpdated = true;
				$content = self::Load($tpl, $filename);
				self::$cacheTable[$tpl] = array(time(), $filename, $content);
				unset($content);
			}
			return self::VarEval(self::$cacheTable[$tpl][2]);
		}

		//Если не кэшируем
		if (!isset(self::$tpls[$tpl])) self::$tpls[$tpl] = self::Load($tpl);
		return self::Parse(self::$tpls[$tpl]);
	}

	private static function Load($tpl, &$filename = null){
		$content = "";
		$type = $tpl[0] == "_" ? "/tpls/" : "/pages/";
		if (is_file(ROOT . Core::$main_folder . $type . $tpl . "." . Core::$language . ".html")){
			$filename = ROOT . Core::$main_folder . $type . $tpl . "." . Core::$language . ".html";
			$content = file_get_contents(ROOT . Core::$main_folder . $type . $tpl . ".html");
		} else if (is_file(ROOT . Core::$main_folder . $type . $tpl . ".html")){
			$filename = ROOT . Core::$main_folder . $type . $tpl . ".html";
			$content = file_get_contents(ROOT . Core::$main_folder . $type . $tpl . ".html");
		}
		if (self::$cache != null)
		$content = self::toEval($content);
		return $content;
	}

	private static function VarEval($text){
		ob_start();
		eval($text);
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

	public static function Parse($text){
		return self::VarEval(self::toEval($text));
	}

	private static function toEval($text){
		$matches = array();
		while(true){
			if (preg_match("/<:(.+):>/uiU", $text, $matches)){
				$text = str_replace($matches[0], "<? Core::GetModule('{$matches[1]}'); ?>", $text);
				continue;
			}
			if (preg_match("/<\+(.+) (.+) (.*)\+>/uiU", $text, $matches)){
				$data = self::ParseData($matches[3]);
				$text = str_replace($matches[0], "<?=Core::GetModule('{$matches[1]}')->{$matches[2]}({$data})?>", $text);
				continue;
			}
			if (preg_match("/<#(.+)#>/uiU", $text, $matches)){
				$text = str_replace($matches[0], "<?=Tpl::Get('{$matches[1]}')?>", $text);
				continue;
			}
			if (preg_match("/<\*_post (.+)\*>/uiU", $text, $matches)){
				$text = str_replace($matches[0], "<?=\$_POST['{$matches[1]}']?>", $text);
				continue;
			}
			if (preg_match("/<\*_get (.+)\*>/uiU", $text, $matches)){
				$text = str_replace($matches[0], "<?=\$_GET['{$matches[1]}']?>", $text);
				continue;
			}
			if (preg_match("/<\*_url (.+)\*>/uiU", $text, $matches)){
				$matches[1] = intval($matches[1]);
				$text = str_replace($matches[0], "<?=Core::\$url[{$matches[1]}]?>", $text);
				continue;
			}
			if (preg_match("/<\*(.+) (.+)\*>/uiU", $text, $matches)){
				$text = str_replace($matches[0], "<?=Core::GetModuleVar('{$matches[1]}', '{$matches[2]}')?>", $text);
				continue;
			}
			if (preg_match("/<{(.+)}>/uiU", $text, $matches)){
				$text = str_replace($matches[0], "<?=Core::GetConst('{$matches[1]}')?>", $text);
				continue;
			}
			break;
		}
		while(preg_match("/(<\?(?(?<!\?>).)+)<\?=(.+)\?>(.(?(?<!\?>).)+\?>)/smuiU", $text, $matches)){
			$pos1 = strpos($matches[3], "'");
			$pos2 = strpos($matches[3], '"');
			if ($pos1 !== false && ($pos2 === false || $pos1 < $pos2)){
				$text = str_replace("{$matches[1]}<?={$matches[2]}?>{$matches[3]}", "{$matches[1]}' . {$matches[2]} . '{$matches[3]}", $text);
			} else {
				$text = str_replace("{$matches[1]}<?={$matches[2]}?>{$matches[3]}", "{$matches[1]}\" . {$matches[2]} . \"{$matches[3]}", $text);
			}
		}
		//echo $text . "\n";
		return '?>' . $text . '<?';
	}

	private static function ParseData($str){
		if ($str == "") return "array()";
		$dataArr = explode("&", $str);
		$data = 'array(';
		$i = 0;
		foreach($dataArr as $dataNow){
			$now = explode("=", $dataNow);
			$param = $now[0];
			unset($now[0]);
			if ($i != 0) $data .= ", ";
			$data .= "'" . str_replace("'", "\\\'", $param) . "' => '" . str_replace("'", "\'", implode("=", $now)) . "'";
			$i++;
		}
		return $data . ")";
	}

	private static function LoadCache(){
		if (is_file(ROOT . "/core/cache/" . md5(self::$cache))){
			self::$cacheTable = Core::decode(file_get_contents(ROOT . "/core/cache/" . md5(self::$cache)), true);
			$pages = array_keys(self::$cacheTable);
			for ($i = 0 ; $i < count($pages) ; $i++){
				$cache = &self::$cacheTable[$pages[$i]];
				if (!is_file($cache[1])){
					unset(self::$cacheTable[$pages[$i]]);
					continue;
				}
				if ($cache[0] < filemtime($cache[1])){
					unset(self::$cacheTable[$pages[$i]]);
				}
			}
		}
	}

	public static function SaveCached(){
		if (self::$cacheUpdated){
			file_put_contents(ROOT . "/core/cache/" . md5(self::$cache), Core::encode(self::$cacheTable));
		}
	}

}
?>