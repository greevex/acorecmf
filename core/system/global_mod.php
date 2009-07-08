<?php
function setGlobalMod(){

	$url = explode("/", substr(str_replace(str_replace($_SERVER['DOCUMENT_ROOT'], '', ROOT), '', $_SERVER['REDIRECT_URL']), 1));
	if (count($url) == 0) return;
	if ($url[0] == "manager") return;

	$globs = ACore::decode(ROOT . "/core/config/global.php");

	while (count($url) > 0){
		$now = implode("/", $url);
		if (isset($globs[$now])){
			if ($globs[$now] == ""){
				define("GLOBAL_STR", $now);
				return;
			}
			define("GLOBAL_STR", $now);
			define("GLOBAL_MOD", $globs[$now]);
			include(ROOT . "/modules/{$globs[$now]}/{$globs[$now]}.php");
			return;
		}
		unset($url[count($url) - 1]);
	}
	$now = "";
	if (isset($globs[$now])){
		if ($globs[$now] == ""){
			define("GLOBAL_STR", $now);
			return;
		}
		define("GLOBAL_MOD", $globs[$now]);
		include(ROOT . "/modules/{$globs[$now]}/{$globs[$now]}.php");
	}
}