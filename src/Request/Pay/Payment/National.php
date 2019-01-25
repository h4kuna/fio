<?php declare(strict_types=1);

namespace h4kuna\Fio\Request\Pay\Payment;

use h4kuna\Fio\Exceptions;

class National extends Property
{

	use Symbols;

	public const PAYMENT_STANDARD = 431001;
	public const PAYMENT_PRIORITY = 431005;
	public const PAYMENT_COLLECTION = 431022;

	private const TYPES_PAYMENT = [self::PAYMENT_STANDARD, self::PAYMENT_PRIORITY, self::PAYMENT_COLLECTION];

	/** @var string */
	protected $bankCode = '';

	/** @var string */
	protected $messageForRecipient = '';

	/** @var int */
	protected $paymentType = 0;


	/** @return static */
	public function setPaymentType(int $type)
	{
		$this->paymentType = Exceptions\InvalidArgument::checkIsInList($type, self::TYPES_PAYMENT);;
		return $this;
	}


	/** @return static */
	public function setMessage(string $message)
	{
		$this->messageForRecipient = Exceptions\InvalidArgument::check($message, 140);
		return $this;
	}


	/** @return static */
	public function setAccountTo(string $accountTo)
	{
		$this->accountTo = $accountTo;
		return $this;
	}


	/** @return static */
	public function setBankCode(string $bankCode)
	{
		$this->bankCode = $bankCode;
		return $this;
	}


	public function getExpectedProperty(): array
	{
		return [
			// key => mandatory,
			'accountFrom' => true,
			'currency' => true,
			'amount' => true,
			'accountTo' => true,
			'bankCode' => true,
			'ks' => false,
			'vs' => false,
			'ss' => false,
			'date' => true,
			'messageForRecipient' => false,
			'comment' => false,
			'paymentReason' => false,
			'paymentType' => false,
		];
	}


	public function getStartXmlElement(): string
	{
		return 'DomesticTransaction';
	}

}
