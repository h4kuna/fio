<?php declare(strict_types=1);

namespace h4kuna\Fio\Response\Read;

/**
 * @implements \Iterator<int, Transaction>
 */
final class TransactionList implements \Iterator, \Countable
{
	/** @var array<TransactionAbstract> */
	private array $transactions = [];

	private \stdClass $info;


	public function __construct(\stdClass $info)
	{
		$this->info = $info;
	}


	public function append(TransactionAbstract $transaction): void
	{
		$this->transactions[] = $transaction;
	}


	public function getInfo(): \stdClass
	{
		return $this->info;
	}


	/** @return TransactionAbstract */
	#[\ReturnTypeWillChange]
	public function current()
	{
		$current = current($this->transactions);
		assert($current !== false);
		return $current;
	}


	public function key(): int
	{
		return (int) key($this->transactions);
	}


	#[\ReturnTypeWillChange]
	public function next()
	{
		next($this->transactions);
	}


	#[\ReturnTypeWillChange]
	public function rewind()
	{
		reset($this->transactions);
	}


	public function valid(): bool
	{
		$key = key($this->transactions);
		return $key !== null && isset($this->transactions[$key]);
	}


	public function count(): int
	{
		return count($this->transactions);
	}

}
