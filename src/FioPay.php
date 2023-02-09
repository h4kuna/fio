<?php declare(strict_types=1);

namespace h4kuna\Fio;

use h4kuna\Fio\Account\Bank;
use h4kuna\Fio\Exceptions\InvalidArgument;
use h4kuna\Fio\Pay;
use h4kuna\Fio\Utils;

class FioPay
{
	private const LANGUAGES = ['en', 'cs', 'sk'];

	private const XML = 'xml';
	private const EXTENSIONS = [self::XML, 'abo'];

	private string $language = 'cs';

	/**
	 * @var array<Pay\Payment\Property>
	 */
	private array $payments = [];


	public function __construct(
		private Utils\Queue $queue,
		private Account\FioAccount $account,
		private Pay\XMLFile $xmlFile,
	)
	{
	}


	public function createEuro(float $amount, string $accountTo, string $name, string $bic = ''): Pay\Payment\Euro
	{
		$account = Bank::createInternational($accountTo);

		$euro = (new Pay\Payment\Euro($this->account))
			->setName($name)
			->setAccountTo($account->getAccount())
			->setAmount($amount);
		if ($bic !== '') {
			$euro->setBic($bic);
		}

		$this->payments[] = $euro;

		return $euro;
	}


	public function createNational(float $amount, string $accountTo, string $bankCode = ''): Pay\Payment\National
	{
		$account = Bank::createNational($accountTo);
		if ($bankCode === '') {
			$bankCode = $account->getBankCode();
		}

		$payment = (new Pay\Payment\National($this->account))
			->setAccountTo($account->getAccount())
			->setBankCode($bankCode)
			->setAmount($amount);
		$this->addPayment($payment);

		return $payment;
	}


	public function createInternational(
		float $amount,
		string $accountTo,
		string $name,
		string $street,
		string $city,
		string $country,
		string $info,
		string $bic,
	): Pay\Payment\International
	{
		$account = Bank::createInternational($accountTo);

		$payment = (new Pay\Payment\International($this->account))
			->setBic($bic)
			->setName($name)
			->setCountry($country)
			->setAccountTo($account->getAccount())
			->setStreet($street)
			->setCity($city)
			->setRemittanceInfo1($info)
			->setAmount($amount);
		$this->addPayment($payment);

		return $payment;
	}


	public function getXml(): string
	{
		foreach ($this->payments as $property) {
			$this->xmlFile->setData($property);
		}
		$this->payments = [];

		return $this->xmlFile->getXml();
	}


	public function addPayment(Pay\Payment\Property $property): static
	{
		$this->payments[] = $property;

		return $this;
	}


	/**
	 * @param ?string $filename string is filepath or xml content
	 */
	public function send(?string $filename = null): Pay\Response
	{
		if ($filename === null && $this->payments !== []) {
			$content = $this->getXml();
			$extension = self::XML;
		} elseif ($filename !== null && $filename !== '') {
			$content = $filename;
			$extension = str_starts_with($filename, '<') ? self::XML : InvalidArgument::checkIsInList(strtolower(pathinfo($filename, PATHINFO_EXTENSION)), self::EXTENSIONS);
		} else {
			throw new InvalidArgument('Is supported only filepath or file content. Or missing payments.');
		}

		$token = $this->getAccount()->getToken();
		$post = [
			'type' => $extension,
			'token' => $token,
			'lng' => $this->language,
		];

		return $this->queue->import($post, $content);
	}


	/**
	 * Response language.
	 */
	public function setLanguage(string $lang): static
	{
		$this->language = InvalidArgument::checkIsInList(strtolower($lang), self::LANGUAGES);

		return $this;
	}


	public function getAccount(): Account\FioAccount
	{
		return $this->account;
	}

}
