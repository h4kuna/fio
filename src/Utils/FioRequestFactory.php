<?php declare(strict_types=1);

namespace h4kuna\Fio\Utils;

use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

class FioRequestFactory
{
	public function __construct(
		private RequestFactoryInterface $requestFactory,
		private StreamFactoryInterface $streamFactory
	)
	{
	}


	public function get(string $uri): RequestInterface
	{
		return $this->requestFactory->createRequest('GET', $uri);
	}


	/**
	 * @param array{token: string, type: string, lng?: string} $params
	 * @param string $content string is filepath or content
	 */
	public function post(string $uri, array $params, string $content): RequestInterface
	{
		$request = $this->requestFactory->createRequest('POST', $uri);

		if (is_file($content)) {
			$filename = basename($content);
			$stream = $this->streamFactory->createStreamFromFile($content);
		} else {
			$filename = 'h4kuna.memory.xml';
			$stream = $this->streamFactory->createStreamFromResource(
				$this->createTempFile($content)
			);
		}

		$multipart = $this->createMultiPart($filename, $stream, $params);

		return $request->withHeader('Content-Type', 'multipart/form-data; boundary=' . $multipart->getBoundary())
			->withBody($multipart);
	}


	/**
	 * @param array{token: string, type: string, lng?: string} $params
	 */
	private function createMultiPart(string $filename, StreamInterface $file, array $params): MultipartStream
	{
		$newPost = [
			[
				'name' => 'file',
				'filename' => $filename, // require!
				'contents' => $file,
			],
		];
		foreach ($params as $name => $value) {
			$newPost[] = ['name' => $name, 'contents' => $value];
		}

		return new MultipartStream($newPost);
	}


	/**
	 * @return resource
	 */
	protected function createTempFile(string $content)
	{
		$resource = Utils::tryFopen('php://memory', 'r+');
		fwrite($resource, $content);
		fseek($resource, 0);

		return $resource;
	}

}
