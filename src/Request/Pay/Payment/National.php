<?php

namespace h4kuna\Fio\Request\Pay\Payment;

use h4kuna\Fio\Utils;

/**
 *
 * @author Milan Matějček
 */
class National extends Property
{

	const
			PAYMENT_STANDARD = 431001,
			PAYMENT_FAST = 431004,
			PAYMENT_PRIORITY = 431005,
			PAYMENT_COLLECTION = 431022;

	/** @var int */
	protected $bankCode = TRUE;

	/** @var string */
	protected $messageForRecipient;

	/** @var int */
	protected $paymentType = self::PAYMENT_STANDARD;

	/**
	 * @param int|string $type
	 * @return self
	 * @throws Utils\FioException
	 */
	public function setPaymentType($type)
	{
		static $types = array(self::PAYMENT_STANDARD, self::PAYMENT_FAST, self::PAYMENT_PRIORITY, self::PAYMENT_COLLECTION);
		if (!in_array($type, $types)) {
			throw new Utils\FioException('Unsupported payment type: ' . $type);
		}
		$this->paymentType = $type;
		return $this;
	}

	/**
	 * @param string $str
	 * @return self
	 */
	public function setMessage($str)
	{
		$this->messageForRecipient = Utils\String::substr($str, 140);
		return $this;
	}

	public function setAccountTo($accountTo, $bankCode = NULL)
	{
		$accountObject = Utils\String::createAccount($accountTo, $bankCode);
		$this->accountTo = $accountObject->getAccount();
		$this->bankCode = $accountObject->getBankCode();
		return $this;
	}

	protected function getExpectedProperty()
	{
		return array('accountFrom', 'currency', 'amount', 'accountTo', 'bankCode',
			'ks', 'vs', 'ss', 'date', 'messageForRecipient', 'comment',
			'paymentReason', 'paymentType');
	}

	public function getStartXmlElement()
	{
		return 'DomesticTransaction';
	}

}
