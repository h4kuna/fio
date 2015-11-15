<?php

namespace h4kuna\Fio\Response\Read;

/**
 * @author Milan Matějček
 */
interface IStatementFactory
{

	/** @return ATransaction */
	public function createTransaction($data, $dateFormat);

	/** @return \stdClass */
	public function createInfo($data, $dateFormat);

	/** @return TransactionList */
	public function createTransactionList($info);

	/** @return \h4kuna\Fio\Request\Read\IFile */
	public function createParser();
}
