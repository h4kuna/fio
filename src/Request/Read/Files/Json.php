<?php

namespace h4kuna\Fio\Request\Read\Files;

use h4kuna\Fio\Request,
	h4kuna\Fio\Response\Read,
	Nette;

/**
 * @author Milan Matějček
 */
class Json implements Request\Read\IReader
{

	/** @var Read\ITransactionListFactory */
	private $transactionListFactory;

	public function __construct(Read\ITransactionListFactory $transactionListFactory)
	{
		$this->transactionListFactory = $transactionListFactory;
	}

	/** @return string */
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

		if (self::isJsonBug()) {
			// all float values are transform to string
			// bug for php7.1 https://bugs.php.net/bug.php?id=72567
			$data = preg_replace('~: ?(-?\d+\.\d+),~', ':"$1",', $data);
		}

		$dateFormat = 'Y-m-dO';
		$json = Nette\Utils\Json::decode($data);
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
			$row = $this->transactionListFactory->createTransaction($transactionData, $dateFormat);
			$transactionList->append($row);
			$row->clearTemporaryValues();
		}
		return $transactionList;
	}

	/**
	 * @internal
	 * @return bool
	 */
	public static function isJsonBug()
	{
		return PHP_VERSION_ID >= 70100;
	}

}
