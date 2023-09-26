<?php declare(strict_types=1);

namespace h4kuna\Fio\Utils;

use h4kuna\Dir\TempDir;
use h4kuna\Fio\Exceptions;
use h4kuna\Fio\FioRead;
use h4kuna\Fio\Pay\Response;
use h4kuna\Fio\Pay\XMLResponse;
use Nette\SafeStream;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Queue
{
	/** @var int [s] */
	protected const WAIT_TIME = 30;
	private const HEADER_CONFLICT = 409;

	/** @var array<string, string> */
	private static array $tokens = [];

	private int $limitLoop = 3;


	public function __construct(
		private TempDir $tempDir,
		private ClientInterface $client,
		private FioRequestFactory $requestFactory,
	)
	{
	}


	public function setLimitLoop(int $limitLoop): void
	{
		$this->limitLoop = $limitLoop;
	}


	/**
	 * @throws Exceptions\ServiceUnavailable
	 */
	public function download(string $token, string $url): ResponseInterface
	{
		$response = $this->request($token, $this->requestFactory->get($url));
		$this->detectDownloadResponse($response);

		return $response;
	}


	private function detectDownloadResponse(ResponseInterface $response): void
	{
		/* @var $contentTypeHeaders array */
		$contentTypeHeaders = $response->getHeader('Content-Type');
		$contentType = array_shift($contentTypeHeaders);
		if ($contentType === 'text/xml;charset=UTF-8') {
			$xmlResponse = $this->createXmlResponse($response);

			throw new Exceptions\ServiceUnavailable($xmlResponse->status(), $xmlResponse->code());
		}
	}


	/**
	 * @param array{token: string, type: string, lng?: string} $params
	 * @param string $content string is filepath or content
	 */
	public function import(array $params, string $content): Response
	{
		$response = $this->request(
			$params['token'],
			$this->requestFactory->post(Fio::REST_URL . 'import/', $params, $content),
		);

		return $this->createXmlResponse($response);
	}


	private function request(string $token, RequestInterface $request): ResponseInterface
	{
		$tempFile = $this->loadFileName($token);
		$i = 0;

		request:
		$file = self::createFileResource($tempFile);
		++$i;
		try {
			$response = $this->client->sendRequest($request->withHeader('X-Powered-By', 'h4kuna/fio'));

			if ($response->getStatusCode() === self::HEADER_CONFLICT) {
				if ($i >= $this->limitLoop) {
					throw new Exceptions\QueueLimit(sprintf('You have limit up requests to server "%s". Too many requests in short time interval.', $i));
				}
				self::sleep($tempFile);
				goto request;
			}

			touch($tempFile);

			return $response;
		} catch (ClientExceptionInterface $e) {
			throw new Exceptions\ServiceUnavailable($e->getMessage(), $e->getCode(), $e);
		} finally {
			fclose($file);
		}
	}


	/**
	 * @return resource
	 */
	private static function createFileResource(string $filePath)
	{
		$file = fopen(self::safeProtocol($filePath), 'w');
		if ($file === false) {
			throw new Exceptions\InvalidState('Open file is failed ' . $filePath);
		}

		return $file;
	}


	private static function sleep(string $filename): void
	{
		$criticalTime = time() - intval(filemtime($filename));
		if ($criticalTime < static::WAIT_TIME) {
			sleep(static::WAIT_TIME - $criticalTime);
		}
	}


	private function loadFileName(string $token): string
	{
		$key = substr($token, 10, -10);
		if (!isset(self::$tokens[$key])) {
			self::$tokens[$key] = $this->tempDir->filename(md5($key));
		}

		return self::$tokens[$key];
	}


	private static function safeProtocol(string $filename): string
	{
		return SafeStream\Wrapper::Protocol . "://$filename";
	}


	private function createXmlResponse(ResponseInterface $response): XMLResponse
	{
		return new XMLResponse($response->getBody()->getContents());
	}

}
