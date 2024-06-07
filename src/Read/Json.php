<?php declare(strict_types=1);

namespace h4kuna\Fio\Read;

use h4kuna\Fio\Exceptions;
use h4kuna\Fio\Utils\Fio;
use Nette\Utils;
use Psr\Http\Message\ResponseInterface;

/* readonly */ class Json implements Reader
{

	public function __construct(private ?TransactionFactory $transactionFactory = null)
	{
	}


	public function getExtension(): string
	{
		return self::JSON;
	}


	public function create(ResponseInterface $response): TransactionList
	{
		$content = Fio::getContents($response);

		if ($response->getStatusCode() === 422) {
			throw new Exceptions\LowAuthorization($content, $response->getStatusCode());
		}

		if ($content === '') {
			$content = '{}';
		}

		try {
			$json = Utils\Json::decode($content);
		} catch (Utils\JsonException $e) {
			throw new Exceptions\ServiceUnavailable(sprintf('%s: %s', $e->getMessage(), $content), 0, $e);
		}
		assert($json instanceof \stdClass);

		return new TransactionList($json->accountStatement, $this->transactionFactory);
	}

}
