<?php

namespace h4kuna\Fio\Request;

use h4kuna\Fio\Response;

/**
 * @author Milan Matějček
 */
interface IQueue
{
	/** @var int [s] */
	const WAIT_TIME = 30;
	const HEADER_CONFLICT = 409;

	/**
	 * @param string $url
	 * @return string raw data
	 */
	public function download($token, $url);

	/**
	 * @param string $url
	 * @param array $post
	 * @param string $filename
	 * @return Response\Pay\IResponse
	 */
	public function upload($url, array $post, $filename);
}
