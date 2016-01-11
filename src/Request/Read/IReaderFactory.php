<?php

namespace h4kuna\Fio\Request\Read;

use h4kuna\Fio\Response\Read,
	h4kuna\Fio\Utils;

/**
 *
 * @author Milan Matějček
 */
interface IReaderFactory
{

	/** supported */
	const JSON = 'json';

	/** not supported */
	const
		XML = 'xml',
		OFX = 'ofx',
		HTML = 'html',
		STA = 'sta',
		GPC = 'gpc',
		CSV = 'csv';

	public function __construct(Read\ITransactionListFactory $transactionListFactory);

	/**
	 * File extension.
	 * @return string
	 */
	public function getExtension();

	/**
	 * Prepare downloaded data before append.
	 *
	 * @param string $data
	 * @return Read\TransactionList
	 */
	public function create($data);
}
