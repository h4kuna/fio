<?php declare(strict_types=1);

namespace h4kuna\Fio;

use h4kuna\Fio\Account\Bank;
use h4kuna\Fio\Exceptions\InvalidArgument;
use h4kuna\Fio\Request\Log;
use h4kuna\Fio\Request\Pay;
use h4kuna\Fio\Response\Pay\IResponse;

class FioPay extends Fio
{

	private const LANGS = ['en', 'cs', 'sk'];
	private const EXTENSIONS = ['xml', 'abo'];

	/** @var string */
	private $uploadExtension;

	/** @var string */
	private $language = 'cs';

	/** @var IResponse */
	private $response;

	/** @var Pay\XMLFile */
	private $xmlFile;

	/** @var Log */
	private $log;


	public function __construct(Request\IQueue $queue, Account\FioAccount $account, Pay\XMLFile $xmlFile)
	{
		parent::__construct($queue, $account);
		$this->xmlFile = $xmlFile;
	}


	public function enableLog(): Log
	{
		return $this->log = new Log();
	}


	public function createEuro(float $amount, string $accountTo, string $name, string $bic = ''): Pay\Payment\Euro
	{
		$account = Bank::createInternational($accountTo);
		if ($bic === '') {
			$bic = $account->getBankCode();
		}

		return (new Pay\Payment\Euro($this->account))
			->setName($name)
			->setAccountTo($account->getAccount())
			->setAmount($amount)
			->setBic($bic);
	}


	public function createNational(float $amount, string $accountTo, string $bankCode = ''): Pay\Payment\National
	{
		$account = Bank::createNational($accountTo);
		if ($bankCode === '') {
			$bankCode = $account->getBankCode();
		}
		return (new Pay\Payment\National($this->account))
			->setAccountTo($account->getAccount())
			->setBankCode($bankCode)
			->setAmount($amount);
	}


	public function createInternational(float $amount, string $accountTo, string $name, string $street, string $city, string $country, string $info, string $bic = ''): Pay\Payment\International
	{
		$account = Bank::createInternational($accountTo);
		if ($bic === '') {
			$bic = $account->getBankCode();
		}

		return (new Pay\Payment\International($this->account))
			->setBic($bic)
			->setName($name)
			->setCountry($country)
			->setAccountTo($account->getAccount())
			->setStreet($street)
			->setCity($city)
			->setRemittanceInfo1($info)
			->setAmount($amount);
	}


	public function getUploadResponse(): IResponse
	{
		return $this->response;
	}


	public function getXmlFile(): Pay\XMLFile
    {
        return $this->xmlFile;
    }


	/**
	 * @return static
	 */
	public function addPayment(Pay\Payment\Property $property)
	{
		$this->xmlFile->setData($property);
		return $this;
	}


	/**
	 * @param string|Pay\Payment\Property $filename
	 */
	public function send($filename = null): IResponse
	{
		if ($filename instanceof Pay\Payment\Property) {
			$this->xmlFile->setData($filename);
		}

		if ($this->xmlFile->isReady()) {
			$this->setUploadExtension('xml');
			$filename = $this->xmlFile->getPathname($this->log !== null);
			if ($this->log !== null) {
				$this->log->setFilename($filename);
			}
		} elseif (is_string($filename) && is_file($filename)) {
			$this->setUploadExtension(pathinfo($filename, PATHINFO_EXTENSION));
		} else {
			throw new InvalidArgument('Is supported only filepath or Property object.');
		}

		$token = $this->getAccount()->getToken();
		$post = [
			'type' => $this->uploadExtension,
			'token' => $token,
			'lng' => $this->language,
		];

		return $this->response = $this->queue->upload(self::getUrl(), $token, $post, $filename);
	}


	/**
	 * Response language.
	 * @return static
	 */
	public function setLanguage(string $lang)
	{
		$this->language = InvalidArgument::checkIsInList(strtolower($lang), self::LANGS);
		return $this;
	}


	private static function getUrl(): string
	{
		return self::REST_URL . 'import/';
	}


	private function setUploadExtension(string $extension): void
	{
		$this->uploadExtension = InvalidArgument::checkIsInList(strtolower($extension), self::EXTENSIONS);
	}

}
