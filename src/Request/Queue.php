<?php

namespace h4kuna\Fio\Request;

use Kdyby\Curl\Request;
use Kdyby;
use h4kuna\Fio\Utils\FioException;
use h4kuna\Fio\Request\Pay\BadResponse;
use h4kuna\Fio\Response\Pay\IResponse;
use h4kuna\Fio\Response\Pay\XMLResponse;
use Nette\Utils\FileSystem;
use Nette\Utils\SafeStream;

class Queue implements IQueue
{

    /** @var SafeStream */
    private $stream;

    /** @var string */
    private $temp;

    /** @var string[] */
    private static $tokens = array();

    public function __construct($temp)
    {
        FileSystem::createDir($temp, 0755);
        $this->temp = $temp;
        $this->stream = new SafeStream;
    }

    public function download($token, $url)
    {
        $this->availableAnotherRequest($token);
        $request = new Kdyby\Curl\Request($url);
        return $request->get()->getResponse();
    }

    /** @return IResponse  */
    public function upload($token, Request $curl)
    {
        $this->availableAnotherRequest($token);
        try {
            $xml = $curl->send();
        } catch (Kdyby\Curl\CurlException $e) {
            return new \h4kuna\Fio\Response\Pay\BadResponse($e);
        }

        return new XMLResponse($xml);
    }

    /**
     * Interval between requests is 30s per token.
     * @param string $token
     */
    final protected function availableAnotherRequest($token)
    {
        $tempFile = $this->loadFileName($token);
        $time = (int) file_get_contents($tempFile);
        $diff = ($time + self::API_INTERVAL) - time();
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
        return SafeStream::PROTOCOL . '://' . self::$tokens[$key];
    }

}
