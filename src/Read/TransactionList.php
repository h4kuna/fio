<?php declare(strict_types = 1);

namespace h4kuna\Fio\Read;

use Countable;
use Generator;
use h4kuna\Fio\Utils\Fio;
use IteratorAggregate;
use stdClass;
use function assert;
use function count;
use function is_array;
use function is_int;
use function is_object;
use function is_scalar;

/**
 * @implements IteratorAggregate<int, Transaction>
 */
final class TransactionList implements Countable, IteratorAggregate
{

	private stdClass $info;

	/**
	 * @var array<int|string, mixed>
	 */
	private array $transactions;

	private ?TransactionFactory $transactionFactory;


	public function __construct(
		stdClass $response,
		?TransactionFactory $transactionFactory = null,
	)
	{
		$this->info = self::extractInfo($response);
		$this->transactions = self::extractTransactions($response);

		foreach (['dateEnd', 'dateStart'] as $key) {
			if (isset($this->info->$key) && is_scalar($this->info->$key)) {
				$this->info->$key = Fio::toDate((string) $this->info->$key);
			}
		}

		if ($transactionFactory === null && $this->transactions !== []) {
			$transactionFactory = new TransactionFactory();
		}
		$this->transactionFactory = $transactionFactory;
	}

	public function getInfo(): stdClass
	{
		return $this->info;
	}

	public function getIterator(): Generator
	{
		foreach ($this->transactions as $k => $item) {
			if ($this->transactionFactory === null) {
				yield $k => $item;
				continue;
			}

			assert($item instanceof stdClass);
			yield $k => $this->transactionFactory->create($item);
		}
	}

	public function count(): int
	{
		return count($this->transactions);
	}

	/**
	 * @return array<string, mixed>
	 */
	public function __serialize(): array
	{
		$transactions = [];
		foreach ($this as $k => $transaction) {
			assert(is_int($k) && is_object($transaction));
			if (isset($transaction->original)) {
				$transaction->original = null;
			}
			$transactions[$k] = $transaction;
		}

		return [
			'info' => $this->info,
			'transactions' => $transactions,
		];
	}

	/**
	 * @param array<string, mixed> $data
	 */
	public function __unserialize(array $data): void
	{
		$info = $data['info'] ?? null;
		$transactions = $data['transactions'] ?? [];
		assert($info instanceof stdClass && is_array($transactions));

		$this->info = $info;
		$this->transactions = $transactions;
		$this->transactionFactory = null;
	}

	private static function extractInfo(stdClass $response): stdClass
	{
		$info = $response->info ?? null;

		return $info instanceof stdClass ? $info : new stdClass();
	}

	/**
	 * @return array<int|string, mixed>
	 */
	private static function extractTransactions(stdClass $response): array
	{
		$transactionList = $response->transactionList ?? null;
		if (!$transactionList instanceof stdClass) {
			return [];
		}

		$transactions = $transactionList->transaction ?? [];

		return is_array($transactions) ? $transactions : [];
	}

}
