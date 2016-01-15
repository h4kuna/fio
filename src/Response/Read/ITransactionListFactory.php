<?php

namespace h4kuna\Fio\Response\Read;

/**
 * @author Milan Matějček
 */
interface ITransactionListFactory
{

	/** @return TransactionAbstract */
	public function createTransaction($data, $dateFormat);

	/** @return \stdClass */
	public function createInfo($data, $dateFormat);

	/** @return TransactionList */
	public function createTransactionList($info);
}
