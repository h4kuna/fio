<?php declare(strict_types=1);

namespace h4kuna\Fio\Read;

use h4kuna\Fio\Exceptions;
use Psr\Http\Message\ResponseInterface;

interface Reader
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


	function getExtension(): string;


	/**
	 * Prepare downloaded data before append.
	 * @throws Exceptions\ServiceUnavailable
	 * @throws Exceptions\LowAuthorization
	 */
	function create(ResponseInterface $response): TransactionList;

}
