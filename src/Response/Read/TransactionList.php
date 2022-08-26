<?php declare(strict_types=1);

namespace h4kuna\Fio\Response\Read;

/**
 * @implements \Iterator<int, Transaction>
 */
final class TransactionList implements \Iterator, \Countable
{
	/** @var array<TransactionAbstract> */
	private $transactions = [];

	/**
	 * @var \stdClass
	 */
	private $info;


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


	/**
	 * @return int
	 */
	#[\ReturnTypeWillChange]
	public function key()
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


	#[\ReturnTypeWillChange]
	public function valid()
	{
		$key = key($this->transactions);
		return $key !== null && isset($this->transactions[$key]);
	}


	#[\ReturnTypeWillChange]
	public function count()
	{
		return count($this->transactions);
	}

}
