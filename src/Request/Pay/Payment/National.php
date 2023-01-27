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

	protected string $bankCode = '';

	protected string $messageForRecipient = '';

	/** @var int */
	protected $paymentType = 0;


	public function setPaymentType(int $type): static
	{
		$this->paymentType = Exceptions\InvalidArgument::checkIsInList($type, self::TYPES_PAYMENT);;
		return $this;
	}


	public function setMessage(string $message): static
	{
		$this->messageForRecipient = Exceptions\InvalidArgument::check($message, 140);
		return $this;
	}


	public function setAccountTo(string $accountTo): static
	{
		$this->accountTo = $accountTo;
		return $this;
	}


	public function setBankCode(string $bankCode): static
	{
		$this->bankCode = $bankCode;
		return $this;
	}


	/** @return array<string, bool> */
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
