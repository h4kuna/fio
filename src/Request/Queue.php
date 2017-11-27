<?php

namespace h4kuna\Fio\Request;

use GuzzleHttp,
	h4kuna\Fio,
	h4kuna\Fio\Response\Pay,
	Nette\Utils;

class Queue implements IQueue
{

	/** @var string[] */
	private static $tokens = [];

	/** @var int */
	private $limitLoop = 5;

	/** @var bool */
	private $sleep = TRUE;

	/** @var array */
	private $downloadOptions = [];

	/** @var string */
	private $tempDir;

	public function __construct($tempDir)
	{
		$this->tempDir = $tempDir;
	}

	public function setLimitLoop($limitLoop)
	{
		$this->limitLoop = $limitLoop;
	}

	/**
	 * @param array|\Iterator $downloadOptions
	 */
	public function setDownloadOptions($downloadOptions)
	{
		foreach ($downloadOptions as $define => $value) {
			if (is_string($define) && defined($define)) {
				$define = constant($define);
			}
			$this->downloadOptions[$define] = $value;
		}
	}

	public function setSleep($sleep)
	{
		$this->sleep = (bool) $sleep;
	}

	public function download($token, $url)
	{
		return $this->request($token, function(GuzzleHttp\Client $client) use ($url) {
				return $client->request('GET', $url, $this->downloadOptions);
			});
	}

	/** @return Pay\IResponse  */
	public function upload($url, $token, array $post, $filename)
	{
		$newPost = [];
		foreach ($post as $name => $value) {
			$newPost[] = ['name' => $name, 'contents' => $value];
		}
		$newPost[] = ['name' => 'file', 'contents' => fopen($filename, 'r')];

		/* @var $response GuzzleHttp\Psr7\Stream */
		$response = $this->request($token, function(GuzzleHttp\Client $client) use ($url, $newPost) {
			return $client->request('POST', $url, [GuzzleHttp\RequestOptions::MULTIPART => $newPost]);
		});
		return new Pay\XMLResponse($response->getContents());
	}

	private function request($token, $fallback)
	{
		$request = new GuzzleHttp\Client(['headers' => ['X-Powered-By' => 'h4kuna/fio']]);
		$tempFile = $this->loadFileName($token);
		$file = fopen(self::safeProtocol($tempFile), 'w');
		$i = 0;
		do {
			$next = FALSE;
			++$i;
			try {
				$response = $fallback($request);
			} catch (GuzzleHttp\Exception\ClientException $e) {
				if ($e->getCode() !== self::HEADER_CONFLICT || !$this->sleep) {
					fclose($file);
					throw $e;
				} elseif ($i >= $this->limitLoop) {
					fclose($file);
					throw new Fio\QueueLimitException('You have limit up requests to server ' . $this->limitLoop);
				}
				self::sleep($tempFile);
				$next = TRUE;
			}
		} while ($next);
		fclose($file);
		touch($tempFile);
		return $response->getBody();
	}

	private static function sleep($filename)
	{
		$criticalTime = time() - filemtime($filename);
		if ($criticalTime < self::WAIT_TIME) {
			sleep(self::WAIT_TIME - $criticalTime);
		}
	}

	/**
	 * @param string $token
	 * @return string
	 */
	private function loadFileName($token)
	{
		$key = substr($token, 10, -10);
		if (!isset(self::$tokens[$key])) {
			self::$tokens[$key] = $this->tempDir . DIRECTORY_SEPARATOR . md5($key);
		}

		return self::$tokens[$key];
	}

	private static function safeProtocol($filename)
	{
		return Utils\SafeStream::PROTOCOL . '://' . $filename;
	}

}
