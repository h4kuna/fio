<?php declare(strict_types=1);

namespace h4kuna\Fio\Request\Read;

use h4kuna\Fio\Response;

interface IReader
{

	/** supported */
	const JSON = 'json';

	/** not supported */
	const XML = 'xml';
	const OFX = 'ofx';
	const HTML = 'html';
	const STA = 'sta';
	const GPC = 'gpc';
	const CSV = 'csv';

	function __construct(Response\Read\ITransactionListFactory $statement);

	function getExtension(): string;

	/**
	 * Prepare downloaded data before append.
	 */
	function create(string $data): Response\Read\TransactionList;

}
