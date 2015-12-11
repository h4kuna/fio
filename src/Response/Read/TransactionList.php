<?php

namespace h4kuna\Fio\Response\Read;

use Countable,
	h4kuna\Fio\Response\Read\ATransaction,
	Iterator;

/**
 * @author Milan Matějček
 */
final class TransactionList implements Iterator, Countable
{

	/** @var ATransaction[] */
	private $transactions = [];
	private $info;

	public function __construct($info)
	{
		$this->info = $info;
	}

	public function append(ATransaction $transaction)
	{
		$this->transactions[] = $transaction;
	}

	public function getInfo()
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
