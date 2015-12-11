<?php

namespace h4kuna\Fio\Request\Pay\Payment;

use h4kuna\Fio\Utils,
	Iterator,
	Nette\Utils\DateTime;

/**
 * @author Milan Matějček
 */
abstract class Property implements Iterator
{
	/** XML PROPERTY MUST BE PROTECTED ************************************** */

	/** @var int */
	protected $accountFrom = TRUE;

	/** @var string */
	protected $currency = 'CZK';

	/** @var float  */
	protected $amount = TRUE;

	/** @var int  */
	protected $accountTo = TRUE;

	/** @var int */
	protected $ks;

	/** @var int */
	protected $vs;

	/** @var int */
	protected $ss;

	/** @var DateTime */
	protected $date;

	/** @var string */
	protected $comment;

	/**
	 * Section in manual 7.2.3.1.
	 * @var int
	 */
	protected $paymentReason = FALSE;

	/** @var array */
	private static $iterator = [];

	/** @var string */
	private $key;

	/**
	 * @param string $account
	 * @param string $temp
	 */
	public function __construct($account)
	{
		$accountObject = Utils\String::createAccount($account);
		$this->accountFrom = $accountObject->getAccount();
		$this->setDate('now');
	}

	/** @return self */
	public function setAmount($val)
	{
		$this->amount = floatval($val);
		return $this;
	}

	/**
	 * @param string $accountTo
	 * @return self
	 */
	abstract public function setAccountTo($accountTo);

	/**
	 * Currency code ISO 4217.
	 *
	 * @param string $code case insensitive
	 * @return self
	 */
	public function setCurrency($code)
	{
		if (!preg_match('~[a-z]{3}~i', $code)) {
			throw new Utils\FioException('Currency code must match ISO 4217.');
		}
		$this->currency = strtoupper($code);
		return $this;
	}

	/**
	 * @param string $ks
	 * @return self
	 */
	public function setConstantSymbol($ks)
	{
		if (!$ks) {
			$ks = NULL;
		} elseif (!preg_match('~\d{1,4}~', $ks)) {
			throw new Utils\FioException('Constant symbol must contain 1-4 digits.');
		}
		$this->ks = $ks;
		return $this;
	}

	/**
	 * @param string $str
	 * @return self
	 */
	public function setMyComment($str)
	{
		$this->comment = Utils\String::substr($str, 255);
		return $this;
	}

	/**
	 * @param string|DateTime $str
	 * @return self
	 */
	public function setDate($str)
	{
		$this->date = DateTime::from($str)->format('Y-m-d');
		return $this;
	}

	/** @return self */
	public function setPaymentReason($code)
	{
		if (!$code) {
			$code = NULL;
		} elseif (!preg_match('~\d{3}~', $code)) {
			throw new Utils\FioException('Payment reason must contain 3 digits.');
		}
		$this->paymentReason = $code;
		return $this;
	}

	/**
	 *
	 * @param string $ss
	 * @return self
	 */
	public function setSpecificSymbol($ss)
	{
		if (!$ss) {
			$ss = NULL;
		} elseif (!preg_match('~\d{1,10}~', $ss)) {
			throw new Utils\FioException('Specific symbol must contain 1-10 digits.');
		}
		$this->ss = $ss;
		return $this;
	}

	/**
	 * @param string|int $vs
	 * @return self
	 */
	public function setVariableSymbol($vs)
	{
		if (!$vs) {
			$vs = NULL;
		} elseif (!preg_match('~\d{1,10}~', $vs)) {
			throw new Utils\FioException('Variable symbol must contain 1-10 digits.');
		}
		$this->vs = $vs;
		return $this;
	}

	/**
	 * Order is important.
	 * @return string[]
	 */
	abstract protected function getExpectedProperty();

	abstract public function getStartXmlElement();

	private function getProperties()
	{
		if ($this->key === NULL) {
			$this->key = get_called_class();
		}
		if (isset(self::$iterator[$this->key])) {
			return self::$iterator[$this->key];
		}

		return self::$iterator[$this->key] = $this->getExpectedProperty();
	}

	/**
	 * ITERATOR INTERFACE ******************************************************
	 * *************************************************************************
	 */
	public function current()
	{
		$property = $this->key();
		$method = 'get' . ucfirst($property);
		if (method_exists($this, $method)) {
			return $this->{$method}();
		}
		return $this->{$property};
	}

	public function key()
	{
		return current(self::$iterator[$this->key]);
	}

	public function next()
	{
		next(self::$iterator[$this->key]);
	}

	public function rewind()
	{
		$this->getProperties();
		reset(self::$iterator[$this->key]);
	}

	public function valid()
	{
		return array_key_exists(key(self::$iterator[$this->key]), self::$iterator[$this->key]);
	}

}
