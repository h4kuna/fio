<?php

namespace h4kuna\Fio\Response\Read;

use h4kuna\Fio\Response\Read\TransactionAbstract;

/**
 * @author Milan Matějček
 */
final class TransactionList implements \Iterator, \Countable
{

	/** @var TransactionAbstract[] */
	private $transactions = [];

	private $info;

	public function __construct($info)
	{
		$this->info = $info;
	}

	public function append(TransactionAbstract $transaction)
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
