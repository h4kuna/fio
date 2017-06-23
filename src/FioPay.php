<?php

namespace h4kuna\Fio;

use h4kuna\Fio\Request\Pay;

class FioPay extends Fio
{

	/** @var string[] */
	private static $langs = ['en', 'cs', 'sk'];

	/** @var string */
	private $uploadExtension;

	/** @var string */
	private $language = 'cs';

	/** @var Pay\XMLResponse */
	private $response;

	/** @var Pay\XMLFile */
	private $xmlFile;

	public function __construct(Request\IQueue $queue, Account\FioAccount $account, Pay\XMLFile $xmlFile)
	{
		parent::__construct($queue, $account);
		$this->xmlFile = $xmlFile;
	}

	/** @return Pay\Payment\Euro */
	public function createEuro($amount, $accountTo, $name)
	{
		return (new Pay\Payment\Euro($this->account))
				->setName($name)
				->setAccountTo($accountTo)
				->setAmount($amount);
	}

	/** @return Pay\Payment\National */
	public function createNational($amount, $accountTo, $bankCode = NULL)
	{
		return (new Pay\Payment\National($this->account))
				->setAccountTo($accountTo, $bankCode)
				->setAmount($amount);
	}

	/** @return Pay\Payment\International */
	public function createInternational($amount, $accountTo, $bic, $name, $street, $city, $country, $info)
	{
		return (new Pay\Payment\International($this->account))
				->setBic($bic)
				->setName($name)
				->setCountry($country)
				->setAccountTo($accountTo)
				->setStreet($street)
				->setCity($city)
				->setRemittanceInfo1($info)
				->setAmount($amount);
	}

	/** @return Pay\IResponse */
	public function getUploadResponse()
	{
		return $this->response;
	}

	/**
	 * @param Pay\Payment\Property $property
	 * @return self
	 */
	public function addPayment(Pay\Payment\Property $property)
	{
		$this->xmlFile->setData($property);
		return $this;
	}

	/**
	 * @param string|Pay\Payment\Property $filename
	 * @return Response\Pay\IResponse
	 * @throws InvalidArgumentException
	 */
	public function send($filename = NULL)
	{
		if ($filename instanceof Pay\Payment\Property) {
			$this->xmlFile->setData($filename);
		}

		if ($this->xmlFile->isReady()) {
			$this->setUploadExtenstion('xml');
			$filename = $this->xmlFile->getPathname();
		} elseif (is_file($filename)) {
			$this->setUploadExtenstion(pathinfo($filename, PATHINFO_EXTENSION));
		} else {
			throw new InvalidArgumentException('Is supported only filepath or Property object.');
		}

		$token = $this->getAccount()->getToken();
		$post = [
			'type' => $this->uploadExtension,
			'token' => $token,
			'lng' => $this->language,
		];

		return $this->response = $this->queue->upload($this->getUrl(), $token, $post, $filename);
	}

	/**
	 * Response language.
	 * @param string $lang
	 * @return self
	 * @throws InvalidArgumentException
	 */
	public function setLanguage($lang)
	{
		$lang = strtolower($lang);
		if (!in_array($lang, self::$langs)) {
			throw new InvalidArgumentException($lang . ' avaible are ' . implode(', ', self::$langs));
		}
		$this->language = $lang;
		return $this;
	}

	/** @return string */
	private function getUrl()
	{
		return self::REST_URL . 'import/';
	}

	/**
	 * Set upload file extension.
	 * @param string $extension
	 * @return self
	 * @throws InvalidArgumentException
	 */
	private function setUploadExtenstion($extension)
	{
		$extension = strtolower($extension);
		static $extensions = ['xml', 'abo'];
		if (!in_array($extension, $extensions)) {
			throw new InvalidArgumentException('Unsupported file upload format: ' . $extension . ' avaible are ' . implode(', ', $extensions));
		}
		$this->uploadExtension = $extension;
		return $this;
	}

}
