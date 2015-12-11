<?php

namespace h4kuna\Fio\Request\Read\Files;

use h4kuna\Fio\Request,
	h4kuna\Fio\Response\Read,
	Nette,
	Nette\Utils;

/**
 * @author Milan Matějček
 */
class Json extends Nette\Object implements Request\Read\IReader
{

	/** @var Read\ITransactionListFactory */
	private $transactionListFactory;

	/** @var Utils\Date\DateFormatOriginal */
	private $dateFormatOriginal;

	public function __construct(Read\ITransactionListFactory $transactionListFactory, Utils\Date\DateFormatOriginal $dateFormatOriginal)
	{
		$this->transactionListFactory = $transactionListFactory;
		$this->dateFormatOriginal = $dateFormatOriginal;
	}

	/**
	 *
	 * @return string
	 */
	public function getExtension()
	{
		return self::JSON;
	}

	/**
	 * @param string $data
	 * @return Read\TransactionList
	 */
	public function create($data)
	{
		if (!$data) {
			$data = '{}';
		}

		$dateFormat = $this->dateFormatOriginal->get('Y-m-dO');
		$json = Utils\Json::decode($data);
		if (isset($json->accountStatement->info)) {
			$info = $this->transactionListFactory->createInfo($json->accountStatement->info, $dateFormat);
		} else {
			$info = new \stdClass();
		}
		$transactionList = $this->transactionListFactory->createTransactionList($info);
		if (!isset($json->accountStatement->transactionList)) {
			return $transactionList;
		}
		foreach ($json->accountStatement->transactionList->transaction as $transactionData) {
			$transactionList->append($this->transactionListFactory->createTransaction($transactionData, $dateFormat));
		}
		return $transactionList;
	}

}
