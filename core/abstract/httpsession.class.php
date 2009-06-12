<?php
/**
 * AHttpSession class.
 * @package core
 * @author Naumov Vasiliy aka glock18, SilentWalk. mailto: wnaumov@gmail.com
 * 
 * Gives session object oriented implementation.
 *
 */
class AHttpSession implements IteratorAggregate, ArrayAccess
{
	/**
	* @var boolean Whether the session is already started
	*/
	protected $sessionOpened = false;

	/**
	 * @var array Stores session array when the session is started, and null otherwise.
	 */
	protected $session = null;

	/**
	 * Starts a session
	 */
	protected function StartSession()
	{
		session_start();
		$this->sessionOpened = true;
		global $_SESSION;
		$this->session = &$_SESSION;
	}
	
	private static $object = null;
	public static function GetSession(){
		if (self::$object === null){
			self::$object = new HttpSession();
		}
		return self::$object;
	}

	/**
	 * An abstract method of IteratorAggregate interface which allows to use this object in foreach loops.
	 *
	 * @return ArrayIterator Object for foreach loop.
	 */
	public function getIterator()
	{
		if (!$this->sessionOpened)
		$this->StartSession();

		return new ArrayIterator($this->session);
	}

	/**
    	 * ArrayAccess abstract methods. 
    	 * This one checks whether an offset is set.
    	 *
    	 * @param string $key Session variable key.
    	 * @return boolean
    	 */
	public function offsetExists($key)
	{
		if (!$this->sessionOpened)
		$this->StartSession();

		return isset($this->session[$key]);
	}

	/**
	 * Returns element defined with the passed key.
	 *
	 * @param string $key Session variable key.
	 * @return mixed
	 */
	public function offsetGet($key)
	{
		if (!$this->offsetExists($key))
		throw new Exception(__METHOD__);

		return $this->session[$key];
	}

	/**
	 * @param string $key Session variable key.
	 * @param mixed $value Value to set.
	 */
	public function offsetSet($key, $value)
	{
		if (!$this->sessionOpened)
		$this->StartSession();

		$this->session[$key] = $value;
	}

	/**
	 * This method is been called with unset() on session variable, such as unset($session['var']);
	 *
	 * @param string $key Session variable key.
	 */
	public function offsetUnset($key)
	{
		if (!$this->sessionOpened)
		$this->StartSession();

		unset($this->session[$key]);
	}
}
?>