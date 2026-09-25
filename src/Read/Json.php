<?php declare(strict_types = 1);

namespace h4kuna\Fio\Read;

use h4kuna\Fio\Exceptions\LowAuthorization;
use h4kuna\Fio\Exceptions\ServiceUnavailable;
use h4kuna\Fio\Utils\Fio;
use Nette\Utils\Json as NetteJson;
use Nette\Utils\JsonException;
use Psr\Http\Message\ResponseInterface;
use stdClass;
use function assert;
use function sprintf;

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
			throw new LowAuthorization($content, $response->getStatusCode());
		}

		if ($content === '') {
			$content = '{}';
		}

		try {
			$json = NetteJson::decode($content);
		} catch (JsonException $e) {
			throw new ServiceUnavailable(sprintf('%s: %s', $e->getMessage(), $content), 0, $e);
		}
		assert($json instanceof stdClass && $json->accountStatement instanceof stdClass);

		return new TransactionList($json->accountStatement, $this->transactionFactory);
	}

}
