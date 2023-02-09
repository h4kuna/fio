<?php declare(strict_types=1);

namespace h4kuna\Fio\Utils;

use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\RequestInterface;

class GuzzleRequestFactory implements RequestFactory
{
	public function __construct(private HttpFactory $requestFactory)
	{
	}


	public function get(string $uri): RequestInterface
	{
		return $this->requestFactory->createRequest('GET', $uri);
	}


	public function post(string $uri, array $params, string $content): RequestInterface
	{
		$request = $this->requestFactory->createRequest('POST', $uri);

		if (is_file($content)) {
			$filename = basename($content);
			$contents = Utils::tryFopen($content, 'r');
		} else {
			$filename = 'h4kuna.memory.xml';

			$stream = Utils::tryFopen('php://memory', 'r+');
			fwrite($stream, $content);
			fseek($stream, 0);
			$contents = new Stream($stream);
		}

		$newPost = [
			[
				'name' => 'file',
				'filename' => $filename, // require!
				'contents' => $contents,
			],
		];
		foreach ($params as $name => $value) {
			$newPost[] = ['name' => $name, 'contents' => $value];
		}

		return $request->withBody(new MultipartStream($newPost));
	}

}
