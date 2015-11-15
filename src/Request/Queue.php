<?php

namespace h4kuna\Fio\Request;

use h4kuna\Fio\Response\Pay,
	Kdyby\Curl,
	Nette\Utils;

class Queue implements IQueue
{

	/** @var string */
	private $temp;

	/** @var string[] */
	private static $tokens = array();

	public function __construct($temp)
	{
		Utils\FileSystem::createDir($temp, 0755);
		$this->temp = $temp;
	}

	public function download($token, $url)
	{
		$this->availableAnotherRequest($token, 0);
		$request = new Curl\Request($url);
		return $request->get()->getResponse();
	}

	/** @return Pay\IResponse  */
	public function upload($url, array $post, $filename)
	{
		$this->availableAnotherRequest($post['token'], self::API_INTERVAL);
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
	final protected function availableAnotherRequest($token, $timeWait)
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
		$request->setPost($post, array(
			'file' => $filename
		));

		$curl = new Curl\CurlSender();
		$curl->setTimeout(30);
		$curl->options = array(
			'verbose' => 0,
			'ssl_verifypeer' => 0,
			'ssl_verifyhost' => 2,
				# 'httpheader' => 'Content-Type: multipart/form-data; charset=utf-8;'
				) + $curl->options;
		$request->setSender($curl);
		return $request;
	}

}
