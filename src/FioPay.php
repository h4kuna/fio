<?php declare(strict_types = 1);

namespace h4kuna\Fio;

use h4kuna\Fio\Account\Bank;
use h4kuna\Fio\Account\FioAccount;
use h4kuna\Fio\Exceptions\InvalidArgument;
use h4kuna\Fio\Pay\Payment\Euro;
use h4kuna\Fio\Pay\Payment\International;
use h4kuna\Fio\Pay\Payment\National;
use h4kuna\Fio\Pay\Payment\Property;
use h4kuna\Fio\Pay\Response;
use h4kuna\Fio\Pay\XMLFile;
use h4kuna\Fio\Utils\Queue;
use function pathinfo;
use function str_starts_with;
use function strtolower;
use const PATHINFO_EXTENSION;

class FioPay
{

	private const LANGUAGES = ['en', 'cs', 'sk'];

	private const XML = 'xml';
	private const EXTENSIONS = [self::XML, 'abo'];

	private string $language = 'cs';

	/**
	 * @var array<Property>
	 */
	private array $payments = [];


	public function __construct(
		private Queue $queue,
		private FioAccount $account,
		private XMLFile $xmlFile,
	)
	{
	}

	public function createEuro(
		float $amount,
		string $accountTo,
		string $name,
		string $bic = '',
	): Euro
	{
		$account = Bank::createInternational($accountTo);

		$euro = (new Euro($this->account))
			->setName($name)
			->setAccountTo($account->getAccount())
			->setAmount($amount);
		if ($bic !== '') {
			$euro->setBic($bic);
		}

		$this->payments[] = $euro;

		return $euro;
	}

	public function createNational(
		float $amount,
		string $accountTo,
		string $bankCode = '',
	): National
	{
		$account = Bank::createNational($accountTo);
		if ($bankCode === '') {
			$bankCode = $account->getBankCode();
		}

		$payment = (new National($this->account))
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
	): International
	{
		$account = Bank::createInternational($accountTo);

		$payment = (new International($this->account))
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

	public function addPayment(Property $property): static
	{
		$this->payments[] = $property;

		return $this;
	}

	/**
	 * @param ?string $filename string is filepath or xml content
	 */
	public function send(?string $filename = null): Response
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

	public function getAccount(): FioAccount
	{
		return $this->account;
	}

}
