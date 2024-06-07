<?php declare(strict_types=1);

namespace h4kuna\Fio\Read;

use h4kuna\Fio\Utils\Fio;
use stdClass;

/**
 * @implements \IteratorAggregate<int, Transaction>
 */
final class TransactionList implements \Countable, \IteratorAggregate
{

	public function __construct(private stdClass $response, private ?TransactionFactory $transactionFactory = null)
	{
		$this->prepareDefault();
		if (isset($this->response->info->dateEnd)) {
			$this->response->info->dateEnd = Fio::toDate(strval($this->response->info->dateEnd));
		}

		if (isset($this->response->info->dateStart)) {
			$this->response->info->dateStart = Fio::toDate(strval($this->response->info->dateStart));
		}
		if ($this->transactionFactory === null && $this->response->transactionList->transaction !== []) {
			$this->transactionFactory = new TransactionFactory();
		}
	}


	public function getInfo(): stdClass
	{
		return $this->response->info;
	}


	public function getIterator(): \Generator
	{
		if ($this->transactionFactory === null) {
			$factory = new class {
				/**
				 * @template T of object
				 * @param T $item
				 * @return T
				 */
				public function create($item)
				{
					return $item;
				}

			};
		} else {
			$factory = $this->transactionFactory;
		}

		foreach ($this->response->transactionList->transaction as $k => $item) {
			yield $k => $factory->create($item);
		}
	}


	public function count(): int
	{
		return count($this->response->transactionList->transaction);
	}


	/**
	 * @return array<string, mixed>
	 */
	public function __serialize(): array
	{
		$transactions = [];
		foreach ($this as $k => $transaction) {
			assert(is_object($transaction));
			if (isset($transaction->original)) {
				$transaction->original = null;
			}
			$transactions[$k] = $transaction;
		}

		return [
			'info' => $this->response->info,
			'transactions' => $transactions,
		];
	}


	/**
	 * @param array<string, mixed> $data
	 */
	public function __unserialize(array $data): void
	{
		$this->response = new \stdClass();
		$this->response->info = $data['info'];
		$this->prepareDefault();
		$this->response->transactionList->transaction = $data['transactions'];
		$this->transactionFactory = null;
	}


	private function prepareDefault(): void
	{
		if (!isset($this->response->info)) {
			$this->response->info = new stdClass();
		}
		if (!isset($this->response->transactionList)) {
			$this->response->transactionList = new stdClass();
		}
		if (!isset($this->response->transactionList->transaction)) {
			$this->response->transactionList->transaction = [];
		}
	}

}
