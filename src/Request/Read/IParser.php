<?php

namespace h4kuna\Fio\Request\Read;

use h4kuna\Fio\Response\Read\IStatementFactory,
	h4kuna\Fio\Response\Read\TransactionList;

/**
 *
 * @author Milan Matějček
 */
interface IParser
{

	/** supported */
	const JSON = 'json';

	/** not supported */
	const XML = 'xml';
	const OFX = 'ofx';
	const HTML = 'html';
	const STA = 'sta';

	/** exists but not supported */
	const GPC = 'gpc';
	const CSV = 'csv';

	public function __construct(IStatementFactory $statement);

	/**
	 * File extension.
	 *
	 * @return string
	 */
	public function getExtension();

	/**
	 * Prepare downloaded data before append.
	 *
	 * @param string $data
	 * @return TransactionList
	 */
	public function parse($data);
}
