<?php

namespace h4kuna\Fio\Request;

/**
 *
 * @author Milan Matějček
 */
interface IQueue
{

	/**
	 * @param string $url
	 * @return string
	 */
	public function download($token, $url);

	/**
	 * @param string $url
	 * @param array $post
	 * @param string $filename
	 * @return \h4kuna\Fio\Response\Pay\IResponse
	 */
	public function upload($url, array $post, $filename);
}
