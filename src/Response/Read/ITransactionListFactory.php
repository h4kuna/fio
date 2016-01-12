<?php

namespace h4kuna\Fio\Response\Read;

use h4kuna\Fio\Request,
	h4kuna\Fio\Utils\Date;

/**
 * @author Milan Matějček
 */
interface ITransactionListFactory
{

	/** @return ATransaction */
	public function createTransaction($data, $dateFormat);

	/** @return \stdClass */
	public function createInfo($data, $dateFormat);

	/** @return TransactionList */
	public function createTransactionList($info);

	/** @return Request\Read\IReader */
	public function createReader();
}
