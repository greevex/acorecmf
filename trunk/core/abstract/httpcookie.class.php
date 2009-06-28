<?php
class AHttpCookie
{
	const TYPE_NOTDECRYPTABLE = 0;
	const TYPE_DECRYPTABLE = 1;
	
	private $type;
	private $value;
	private $bCrypted = null;
	
	public function __construct($type = self::TYPE_DECRYPTABLE)
	{
		if ($type == self::TYPE_NOTDECRYPTABLE)
			$this->type = self::TYPE_NOTDECRYPTABLE;
		else
			$this->type = self::TYPE_DECRYPTABLE;
	}
	
	public function GetType()
	{
		return $this->type;
	}
	
	public function SetValue($value)
	{
		$this->value = $value;
	}
	
	public function GetValue()
	{
		return $this->value;
	}
	
	public function Encrypt()
	{
		$this->UseEncrypt();
	}
	
	public function UseEncrypt()
	{
		if($this->bCrypted === true)
			echo 'ѕовторное кодирование куки';
		
		$this->bCrypted = true;
	}
	
	public function Decrypt($bDecrypt = true)
	{
		if ($this->type == self::TYPE_NOTDECRYPTABLE)
			return;
			
		if ($bDecrypt)
			$this->UseDecrypt();
	}
	
	public function UseDecrypt()
	{
		if ($this->bCrypted === false)
			echo 'ѕовторное декодирование куки';
			
		$this->bCrypted = false;
		// собстенно декодирование. алгоритм где-нить в конфиге, и согласно ему декодитс€.
	}
	
	public function IsCrypted()
	{
		return $this->bCrypted;
	}
}