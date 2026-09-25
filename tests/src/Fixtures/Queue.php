<?php declare(strict_types = 1);

namespace h4kuna\Fio\Tests\Fixtures;

use GuzzleHttp\Psr7\Response as Psr7Response;
use h4kuna\Fio\Pay\Response;
use h4kuna\Fio\Pay\XMLResponse;
use h4kuna\Fio\Utils\Queue as FioQueue;
use Psr\Http\Message\ResponseInterface;
use function assert;
use function basename;
use function file_get_contents;
use function h4kuna\Fio\Tests\loadResult;
use function is_file;
use function is_string;
use function ltrim;
use function preg_match;
use function str_replace;

class Queue extends FioQueue
{

	public function __construct() // @phpstan-ignore-line
	{
	}

	public function download(
		string $token,
		string $url,
	): ResponseInterface
	{
		$file = '';
		switch (basename($url, 'json')) {
			case 'transactions.':
				if (preg_match('~((?:/[^/]+){3})$~U', $url, $find) === 1) {
					$file = str_replace(['/', '-' . $token], ['-', ''], ltrim($find[1], '/'));
				}
				break;
		}
		if ($file !== '') {
			$file = loadResult('raw://' . $file);
			assert(is_string($file));
		}

		return new Psr7Response(body: $file, reason: $url);
	}

	public function import(
		array $params,
		string $content,
	): Response
	{
		if (is_file($content)) {
			$content = (string) file_get_contents($content);
		}

		return new XMLResponse($content);
	}

}
