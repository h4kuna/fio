<?php

namespace h4kuna\Fio\Request\Read;

use h4kuna\Fio\Response;

/**
 * @author Milan Matějček
 */
interface IReader
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

	public function __construct(Response\Read\ITransactionListFactory $statement);

	/**
	 * File extension.
	 * @return string
	 */
	public function getExtension();

	/**
	 * Prepare downloaded data before append.
	 * @param string $data
	 * @return Response\Read\TransactionList
	 */
	public function create($data);
}
