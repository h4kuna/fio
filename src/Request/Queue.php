<?php declare(strict_types=1);

namespace h4kuna\Fio\Request;

use GuzzleHttp;
use h4kuna\Fio\Exceptions;
use h4kuna\Fio\Response\Pay;
use Nette\Utils;
use Psr\Http\Message\ResponseInterface;

class Queue implements IQueue
{

	/** @var string[] */
	private static $tokens = [];

	/** @var int */
	private $limitLoop = 5;

	/** @var bool */
	private $sleep = true;

	/** @var array */
	private $downloadOptions = [];

	/** @var string */
	private $tempDir;


	public function __construct(string $tempDir)
	{
		$this->tempDir = $tempDir;
	}


	public function setLimitLoop(int $limitLoop): void
	{
		$this->limitLoop = $limitLoop;
	}


	public function setDownloadOptions(iterable $downloadOptions): void
	{
		foreach ($downloadOptions as $define => $value) {
			if (is_string($define) && defined($define)) {
				$define = constant($define);
			}
			$this->downloadOptions[$define] = $value;
		}
	}


	public function setSleep(bool $sleep): void
	{
		$this->sleep = $sleep;
	}


	/**
	 * @throws Exceptions\QueueLimit
	 * @throws Exceptions\ServiceUnavailable
	 */
	public function download(string $token, string $url): string
	{
		$response = $this->request($token, function (GuzzleHttp\ClientInterface $client) use ($url) {
			return $client->request('GET', $url, $this->downloadOptions);
		});
		$this->detectDownloadResponse($response);
		return (string) $response->getBody();
	}


	/**
	 * @throws Exceptions\QueueLimit
	 * @throws Exceptions\ServiceUnavailable
	 */
	public function upload(string $url, string $token, array $post, string $filename): Pay\IResponse
	{
		$newPost = [];
		foreach ($post as $name => $value) {
			$newPost[] = ['name' => $name, 'contents' => $value];
		}
		$newPost[] = ['name' => 'file', 'contents' => fopen($filename, 'r')];

		$response = $this->request($token, function (GuzzleHttp\ClientInterface $client) use ($url, $newPost) {
			return $client->request('POST', $url, [GuzzleHttp\RequestOptions::MULTIPART => $newPost]);
		});
		return $this->createXmlResponse($response);
	}


	/**
	 * @throws Exceptions\QueueLimit
	 * @throws Exceptions\ServiceUnavailable()
	 */
	private function request(string $token, callable $fallback): ResponseInterface
	{
		$client = $this->createClient();
		$tempFile = $this->loadFileName($token);
		$file = self::createFileResource($tempFile);
		$i = 0;
		do {
			$next = false;
			++$i;
			try {
				$response = $fallback($client);
				fclose($file);
				touch($tempFile);
				return $response;
			} catch (GuzzleHttp\Exception\ClientException $e) {
				if ($e->getCode() !== self::HEADER_CONFLICT || !$this->sleep) {
					fclose($file);
					throw $e;
				} elseif ($i >= $this->limitLoop) {
					fclose($file);
					throw new Exceptions\QueueLimit('You have limit up requests to server ' . $this->limitLoop);
				}
				self::sleep($tempFile);
				$next = true;
			} catch (GuzzleHttp\Exception\BadResponseException $e) {
				throw new Exceptions\ServiceUnavailable($e->getMessage(), $e->getCode(), $e);
			}
		} while ($next);
	}


	private static function createFileResource(string $filePath)
	{
		$file = fopen(self::safeProtocol($filePath), 'w');
		if ($file === false) {
			throw new Exceptions\InvalidState('Open file is failed ' . $filePath);
		}
		return $file;
	}


	/**
	 * @throws Exceptions\ServiceUnavailable()
	 */
	private function detectDownloadResponse(ResponseInterface $response): void
	{
		/* @var $contentTypeHeaders array */
		$contentTypeHeaders = $response->getHeader('Content-Type');
		$contentType = array_shift($contentTypeHeaders);
		if ($contentType === 'text/xml;charset=UTF-8') {
			$xmlResponse = $this->createXmlResponse($response);
			if ($xmlResponse->code() !== 0) {
				throw new Exceptions\ServiceUnavailable($xmlResponse->status(), $xmlResponse->code());
			}
		}
	}


	private static function sleep(string $filename): void
	{
		$criticalTime = time() - filemtime($filename);
		if ($criticalTime < static::WAIT_TIME) {
			sleep(static::WAIT_TIME - $criticalTime);
		}
	}


	private function loadFileName(string $token): string
	{
		$key = substr($token, 10, -10);
		if (!isset(self::$tokens[$key])) {
			self::$tokens[$key] = $this->tempDir . DIRECTORY_SEPARATOR . md5($key);
		}

		return self::$tokens[$key];
	}


	private static function safeProtocol(string $filename): string
	{
		return Utils\SafeStream::PROTOCOL . '://' . $filename;
	}


	protected function createXmlResponse(ResponseInterface $response): Pay\IResponse
	{
		return new Pay\XMLResponse($response->getBody()->getContents());
	}


	protected function createClient(): GuzzleHttp\ClientInterface
	{
		return new GuzzleHttp\Client(['headers' => ['X-Powered-By' => 'h4kuna/fio']]);
	}

}
