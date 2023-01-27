<?php declare(strict_types=1);

namespace h4kuna\Fio\Request\Pay\Payment;

class Euro extends Foreign
{
	use Symbols;

	/** @return array<string, bool> */
	public function getExpectedProperty(): array
	{
		return [
			'accountFrom' => true,
			'currency' => true,
			'amount' => true,
			'accountTo' => true,
			'ks' => false,
			'vs' => false,
			'ss' => false,
			'bic' => false,
			'date' => true,
			'comment' => false,
			'benefName' => true,
			'benefStreet' => false,
			'benefCity' => false,
			'benefCountry' => false,
			'remittanceInfo1' => false,
			'remittanceInfo2' => false,
			'remittanceInfo3' => false,
			'paymentReason' => false,
			'paymentType' => false,
		];
	}


	public function getStartXmlElement(): string
	{
		return 'T2Transaction';
	}

}
