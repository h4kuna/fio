<?php declare(strict_types=1);

namespace h4kuna\Fio\Request\Read\Files;

use h4kuna\Fio\Exceptions;
use h4kuna\Fio\Request;
use h4kuna\Fio\Response\Read;
use Nette\Utils;

class Json implements Request\Read\IReader
{
	private Read\ITransactionListFactory $transactionListFactory;


	public function __construct(Read\ITransactionListFactory $transactionListFactory)
	{
		$this->transactionListFactory = $transactionListFactory;
	}


	public function getExtension(): string
	{
		return self::JSON;
	}


	/**
	 * @throws Exceptions\ServiceUnavailable
	 */
	public function create(string $data): Read\TransactionList
	{
		if ($data === '') {
			$data = '{}';
		}

		if (self::isJsonBug()) {
			// all float values are transform to string
			// bug for php7.1 https://bugs.php.net/bug.php?id=72567
			$data = (string) preg_replace('~: ?(-?\d+\.\d+),~', ':"$1",', $data);
		}

		$dateFormat = 'Y-m-dO';
		try {
			$json = Utils\Json::decode($data);
		} catch (Utils\JsonException $e) {
			throw new Exceptions\ServiceUnavailable($e->getMessage(), 0, $e);
		}

		\assert($json instanceof \stdClass);

		if (isset($json->accountStatement?->info)) {
			$info = $this->transactionListFactory->createInfo($json->accountStatement->info, $dateFormat);
		} else {
			$info = new \stdClass();
		}
		$transactionList = $this->transactionListFactory->createTransactionList($info);
		if (!isset($json->accountStatement?->transactionList)) {
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
	 */
	public static function isJsonBug(): bool
	{
		return PHP_VERSION_ID >= 70100 && PHP_VERSION_ID < 70200;
	}

}
