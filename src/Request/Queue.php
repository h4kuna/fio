<?php

namespace h4kuna\Fio\Request;

use GuzzleHttp,
	h4kuna\Fio\Response\Pay,
	Nette\Utils;

class Queue implements IQueue
{

	/** @var string[] */
	private static $tokens = [];

	public function download($token, $url)
	{
		do {
			$request = new GuzzleHttp\Client();
			$response = $request->request('GET', $url);
		} while ($this->availableAnotherRequest($token, $response));
		return $response->getBody();
	}

	/** @return Pay\IResponse  */
	public function upload($url, array $post, $filename)
	{
		$this->availableAnotherRequest($post['token'], $response);
		try {
			$xml = $this->createCurl($url, $post, $filename)->send();
		} catch (Curl\CurlException $e) {
			return new Pay\BadResponse($e);
		}

		return new Pay\XMLResponse($xml->getResponse());
	}

	/**
	 * Interval between requests is 30s per token.
	 * @param string $token
	 * @param int $timeWait
	 */
	final protected function availableAnotherRequest($token, $response)
	{
		$tempFile = $this->loadFileName($token);
		$time = (int) file_get_contents($tempFile);
		$diff = ($time + $timeWait) - time();
		if ($time && $diff > 0) {
			sleep($diff);
		}
		file_put_contents($tempFile, time());
	}

	/**
	 *
	 * @param string $token
	 * @return string
	 */
	private function loadFileName($token)
	{
		$key = substr($token, 10, -10);
		if (!isset(self::$tokens[$key])) {
			self::$tokens[$key] = $this->temp . DIRECTORY_SEPARATOR . md5($key);
		}
		if (!is_file(self::$tokens[$key])) {
			touch(self::$tokens[$key]);
		}
		return Utils\SafeStream::PROTOCOL . '://' . self::$tokens[$key];
	}

	/** @return CUrl */
	private function createCurl($url, array $post, $filename)
	{
		$request = new Curl\Request($url);
		$request->setPost($post, [
			'file' => $filename
		]);

		$curl = new Curl\CurlSender();
		$curl->setTimeout(30);
		$curl->options = [
			'verbose' => 0,
			'ssl_verifypeer' => 0,
			'ssl_verifyhost' => 2,
			# 'httpheader' => 'Content-Type: multipart/form-data; charset=utf-8;'
			] + $curl->options;
		$request->setSender($curl);
		return $request;
	}

}
