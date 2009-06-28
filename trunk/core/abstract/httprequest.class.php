<?php
class AHttpRequest
{
	private static $cookies = null;
	
	
	public static function GetCookie($name, $decrypt = true)
	{
		self::InitCookies();
		
		if (isset(self::$cookies[$name]))
			return self::ParseCookieValue(self::$cookies[$name], $decrypt);
	}
	
	public static function SetCookie($name, HttpCookie $cookie, $params = array(), $encrypt = true)
	{
		self::InitCookies();

		if ($encrypt)
			$cookie->Encrypt();
			
		setcookie($name, $cookie->GetType() . $cookie->GetValue(),
			isset($params['expires']) ? $params['expires'] : null,
			isset($params['path']) ? $params['path'] : null,
			isset($params['domain']) ? $params['domain'] : null,
			isset($params['secure']) ? $params['secure'] : null);
			
		// не уверен, что без следующей строчки хорошо. и с ней - тоже не уверен.
			self::$cookies[$name] = $cookie->GetType() . $cookie->GetValue();
	}
	
	public static function GetCookies()
	{
		self::InitCookies();

		return self::$cookies;
	}
	
	private static function InitCookies()
	{
		if (self::$cookies == null)
		{
			global $_COOKIE;
			self::$cookies = &$_COOKIE;
		}		
	}
	
	private static function ParseCookieValue($value, $decrypt = true)
	{
		if (strlen($value))
		{
			$cookie = new HttpCookie($value{0});
			$cookie->SetValue(substr($value, 1));
			$cookie->Decrypt($decrypt);
			return $cookie;
		}
		
		return null;
	}
}
?>