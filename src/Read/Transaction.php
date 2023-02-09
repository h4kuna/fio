<?php declare(strict_types=1);

namespace h4kuna\Fio\Read;

class Transaction
{

	#[Column(id: 0)]
	public \DateTimeImmutable $moveDate;

	/** @deprecated use amount */
	#[Column(id: 1)]
	public float $volume;

	#[Column(id: 1)]
	public float $amount;

	#[Column(id: 2)]
	public string $toAccount;

	#[Column(id: 3)]
	public string $bankCode;

	#[Column(id: 4)]
	public string $constantSymbol;

	#[Column(id: 5)]
	public string $variableSymbol;

	#[Column(id: 6)]
	public string $specificSymbol;

	#[Column(id: 7)]
	public ?string $note;

	#[Column(id: 8)]
	public string $type;

	#[Column(id: 9)]
	public string $whoDone;

	#[Column(id: 10)]
	public string $nameAccountTo;

	#[Column(id: 12)]
	public string $bankName;

	#[Column(id: 14)]
	public string $currency;

	#[Column(id: 16)]
	public ?string $messageTo;

	#[Column(id: 17)]
	public int $instructionId;

	#[Column(id: 18)]
	public string $advancedInformation;

	#[Column(id: 22)]
	public int $moveId;

	#[Column(id: 25)]
	public ?string $comment;

	#[Column(id: 26)]
	public string $bic;

	#[Column(id: 27)]
	public string $payerReference;

}
