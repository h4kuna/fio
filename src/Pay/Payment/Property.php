<?php declare(strict_types=1);

namespace h4kuna\Fio\Pay\Payment;

use h4kuna\Fio\Account;
use h4kuna\Fio\Exceptions\InvalidArgument;
use h4kuna\Fio\Utils\Fio;
use Iterator;
use Nette\Utils\Strings;

/**
 * @implements Iterator<string, mixed>
 */
abstract class Property implements Iterator
{
	protected Account\FioAccount $accountFrom;

	protected string $currency = 'CZK';

	protected float $amount = 0.0;

	protected string $accountTo = '';

	protected string $date;

	protected string $comment = '';

	/**
	 * Section in manual 7.2.3.1.
	 */
	protected int $paymentReason = 0;

	/** @var array<string, array<string, bool>> */
	private static array $iterator = [];


	public function __construct(Account\FioAccount $account)
	{
		$this->accountFrom = $account;
		$this->setDate('now');
	}


	public function setAmount(float $amount): static
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
	 */
	public function setCurrency(string $code): static
	{
		$match = Strings::match($code, '/[a-z]{3}/i');
		if ($match === null) {
			throw new InvalidArgument('Currency code must match ISO 4217.');
		}
		$this->currency = strtoupper($code);

		return $this;
	}


	public function setMyComment(string $comment): static
	{
		$this->comment = InvalidArgument::check($comment, 255);

		return $this;
	}


	public function setDate(int|string|\DateTimeInterface $date): static
	{
		$this->date = Fio::date($date);

		return $this;
	}


	public function setPaymentReason(int $code): static
	{
		InvalidArgument::checkRange($code, 999);
		$this->paymentReason = $code;

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
		$key = $this::class;
		if (isset(self::$iterator[$key])) {
			return self::$iterator[$key];
		}

		return self::$iterator[$key] = $this->getExpectedProperty();
	}


	/**
	 * ITERATOR INTERFACE ******************************************************
	 * *************************************************************************
	 */

	public function current(): mixed
	{
		$property = $this->key();
		$method = 'get' . ucfirst($property);
		if (method_exists($this, $method)) {
			return $this->$method();
		}

		return $this->$property;
	}


	public function key(): string
	{
		return (string) key(self::$iterator[$this::class]);
	}


	public function next(): void
	{
		next(self::$iterator[$this::class]);
	}


	public function rewind(): void
	{
		$this->getProperties();
		reset(self::$iterator[$this::class]);
	}


	public function valid(): bool
	{
		return array_key_exists($this->key(), self::$iterator[$this::class]);
	}

}
