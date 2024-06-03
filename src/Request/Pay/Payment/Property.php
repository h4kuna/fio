<?php declare(strict_types=1);

namespace h4kuna\Fio\Request\Pay\Payment;

use h4kuna\Fio\Account;
use h4kuna\Fio\Exceptions\InvalidArgument;
use Iterator;
use Nette\Utils\DateTime;

/**
 * @implements Iterator<string, mixed>
 */
abstract class Property implements Iterator
{
	/** @var Account\FioAccount */
	protected $accountFrom;

	/** @var string */
	protected $currency = 'CZK';

	/** @var float */
	protected $amount = 0.0;

	/** @var string */
	protected $accountTo = '';

	/** @var string */
	protected $date;

	/** @var string */
	protected $comment;

	/**
	 * Section in manual 7.2.3.1.
	 * @var int
	 */
	protected $paymentReason = 0;

	/** @var array<string, array<string, bool>> */
	private static $iterator = [];

	/** @var string */
	private $key;


	public function __construct(Account\FioAccount $account)
	{
		$this->accountFrom = $account;
		$this->setDate('now');
	}


	/**
	 * @return static
	 */
	public function setAmount(float $amount)
	{
		$this->amount = $amount;
		if ($amount <= 0) {
			throw new InvalidArgument('Amount must by positive number.');
		}
		return $this;
	}


	/**
	 * @return static
	 */
	abstract public function setAccountTo(string $accountTo);


	/**
	 * Currency code ISO 4217.
	 * @return static
	 */
	public function setCurrency(string $code)
	{
		if (!preg_match('~[a-z]{3}~i', $code)) {
			throw new InvalidArgument('Currency code must match ISO 4217.');
		}
		$this->currency = strtoupper($code);
		return $this;
	}


	/**
	 * @return static
	 */
	public function setMyComment(string $comment)
	{
		$this->comment = InvalidArgument::check($comment, 255);
		return $this;
	}


	/**
	 * @param int|string|\DateTimeInterface $date
	 * @return static
	 */
	public function setDate($date)
	{
		$this->date = DateTime::from($date)->format('Y-m-d');
		return $this;
	}


	/**
	 * @return static
	 */
	public function setPaymentReason(int $code)
	{
		$this->paymentReason = InvalidArgument::checkRange($code, 999);
		return $this;
	}


	/**
	 * Order is important.
	 * @return array<string, bool>
	 */
	abstract public function getExpectedProperty(): array;


	abstract public function getStartXmlElement(): string;


	/**
	 * @return array<string, bool>
	 */
	private function getProperties(): array
	{
		if ($this->key === null) {
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

	/**
	 * @return mixed
	 */
	#[\ReturnTypeWillChange]
	public function current()
	{
		$property = $this->key();
		$method = 'get' . ucfirst($property);
		if (method_exists($this, $method)) {
			return $this->{$method}();
		}
		return $this->{$property};
	}


	/**
	 * @return string
	 */
	#[\ReturnTypeWillChange]
	public function key()
	{
		return (string) key(self::$iterator[$this->key]);
	}


	#[\ReturnTypeWillChange]
	public function next()
	{
		next(self::$iterator[$this->key]);
	}


	#[\ReturnTypeWillChange]
	public function rewind()
	{
		$this->getProperties();
		reset(self::$iterator[$this->key]);
	}


	#[\ReturnTypeWillChange]
	public function valid()
	{
		return array_key_exists($this->key(), self::$iterator[$this->key]);
	}

}
