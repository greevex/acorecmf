<?php
/**
 * @author Кваст Александр Владимирович aka Alehandr
 */
class AAccess {
	/**
	 * @param $mod
	 * @param $mod_name
	 * @return array
	 */
	public static function Create($mod, $mod_name) {
		$session = HttpSession::GetSession();
		$config = Config::Load("system", "system");
		if (isset($session['manager_name']) && isset ($config['managers'][$session['manager_name']])) {
			$login = $session['manager_name'];
		} else {
			unset($session['manager_name']);
			return array ();
		}
		$access = array ();
		foreach ($mod->pages as $page) {
			$name = $page[0];
			if ((int) $config['managers'][$login]['m_type'] == 0) {
				$access[$name] = true;
			} else {
				if (in_array("{$mod_name}|{$name}", $config['m_types'][$config['managers'][$login]['m_type']]['mods'])) {
					$access[$name] = true;
				} else {
					$access[$name] = false;
				}
			}
		}
		foreach ($mod->events as $event) {
			$name = $event[0];
			if ((int) $config['managers'][$login]['m_type'] == 0) {
				$access[$name] = preg_match("/Not/u", $name) ? false : true;
			} else {
				if (in_array("{$mod_name}|{$name}", $config['m_types'][$config['managers'][$login]['m_type']]['mods'])) {
					$access[$name] = true;
				} else {
					$access[$name] = false;
				}
			}
		}
		return $access;
	}
}