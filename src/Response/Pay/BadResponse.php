<?php

namespace h4kuna\Fio\Response\Pay;

use Kdyby\Curl\CurlException;

/**
 * @author Milan Matějček
 */
class BadResponse implements IResponse
{

    /** @var CurlException */
    private $curl;

    public function __construct(CurlException $curl)
    {
        $this->curl = $curl;
    }

    public function isOk()
    {
        return FALSE;
    }

    public function getError()
    {
        return $this->curl->getMessage();
    }

    public function getErrorCode()
    {
        return $this->curl->getCode();
    }

}
