<?php

namespace h4kuna\Fio\Response\Pay;

use Kdyby\Curl\Request;

/**
 * @author Milan Matějček
 */
class BadResponse implements IResponse
{

	/** @var Request */
	private $curl;

	public function __construct(Request $curl)
	{
		$this->curl = $curl;
	}

	public function isOk()
	{
		return FALSE;
	}

	public function getError()
	{
		return $this->curl;
	}

	public function getErrorCode()
	{
		return $this->curl->getCode();
	}

}
