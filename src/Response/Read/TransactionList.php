<?php declare(strict_types=1);

namespace h4kuna\Fio\Response\Read;

final class TransactionList implements \Iterator, \Countable
{

	/** @var TransactionAbstract[] */
	private $transactions = [];

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


	/** @return Transaction */
	public function current()
	{
		return current($this->transactions);
	}


	public function key()
	{
		return key($this->transactions);
	}


	public function next()
	{
		next($this->transactions);
	}


	public function rewind()
	{
		reset($this->transactions);
	}


	public function valid()
	{
		return array_key_exists($this->key(), $this->transactions);
	}


	public function count()
	{
		return count($this->transactions);
	}

}
