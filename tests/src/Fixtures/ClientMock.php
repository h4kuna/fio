<?php declare(strict_types=1);

namespace h4kuna\Fio\Tests\Fixtures;

use GuzzleHttp\Psr7;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use function h4kuna\Fio\Tests\loadResult;

class ClientMock implements ClientInterface
{
	public function sendRequest(RequestInterface $request): ResponseInterface
	{
		$uri = $request->getUri();
		parse_str($uri->getQuery(), $output);
		/** @var array{file?: string, exception?: string, status?: string} $output */
		$file = $output['file'] ?? '2015-2-transactions.json';
		$status = intval($output['status'] ?? 200);
		$exception = $output['exception'] ?? '';

		$headers = [];
		$content = '';

		if ($exception !== '') {
			assert(is_subclass_of($exception, \Throwable::class));
			throw new $exception();
		}

		if ($file !== '') {
			$content = loadResult("raw://$file");
			assert(is_string($content));
			$extension = pathinfo($file, PATHINFO_EXTENSION);

			$headers['Content-Type'] = match ($extension) {
				'xml' => 'text/xml;charset=UTF-8',
				'json' => 'application/json',
				default => throw new \Exception('header not defined.'),
			};
		}

		return new Psr7\Response($status, $headers, $content);
	}

}
