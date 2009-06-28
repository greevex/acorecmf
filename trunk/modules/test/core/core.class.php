<?php
class Core extends ACore {
	public static function Run(){
		
		$cookie = new HttpCookie(HttpCookie::TYPE_DECRYPTABLE);
		$cookie->SetValue('My first cookie');
		HttpRequest::SetCookie('test2', $cookie);
		
		echo '<pre>';
		echo 'First character is a cookie type' . "\n";
		print_r(HttpRequest::GetCookies());
		echo '</pre>';
		
		echo "Переопределение работает!";
	}
}
?>