<?php declare(strict_types=1);

namespace h4kuna\Fio\Tests\Fixtures;

use h4kuna\Fio;
use GuzzleHttp\Psr7;
use Psr\Http\Message\ResponseInterface;

class Queue extends Fio\Utils\Queue
{

	public function __construct() // @phpstan-ignore-line
	{
	}


	public function download(string $token, string $url): ResponseInterface
	{
		$file = '';
		switch (basename($url, 'json')) {
			case 'transactions.':
				preg_match('~((?:/[^/]+){3})$~U', $url, $find);
				$file = str_replace(['/', '-' . $token], ['-', ''], ltrim($find[1], '/'));
				break;
		}
		if ($file !== '') {
			$file = Fio\Tests\loadResult('raw://' . $file);
			assert(is_string($file));
		}

		return new Psr7\Response(body: $file, reason: $url);
	}


	public function import(array $params, string $content): Fio\Pay\Response
	{
		if (is_file($content)) {
			$content = (string) file_get_contents($content);
		}

		return new Fio\Pay\XMLResponse($content);
	}

}
