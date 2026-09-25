<?php declare(strict_types = 1);

namespace h4kuna\Fio\Read;

use h4kuna\Fio\Exceptions\LowAuthorization;
use h4kuna\Fio\Exceptions\ServiceUnavailable;
use Psr\Http\Message\ResponseInterface;

interface Reader
{

	/** supported */
	public const JSON = 'json';

	/** not supported */
	public const XML = 'xml';
	public const OFX = 'ofx';
	public const HTML = 'html';
	public const STA = 'sta';
	public const GPC = 'gpc';
	public const CSV = 'csv';


	public function getExtension(): string;

	/**
	 * Prepare downloaded data before append.
	 *
	 * @throws LowAuthorization
	 * @throws ServiceUnavailable
	 */
	public function create(ResponseInterface $response): TransactionList;

}
