<?php declare(strict_types=1);

namespace h4kuna\Fio\Utils;

use Psr\Http\Message\RequestInterface;

interface RequestFactory
{
	function get(string $uri): RequestInterface;


	/**
	 * @param array{token: string, type: string, lng?: string} $params
	 * @param string $content string is filepath or content
	 */
	function post(string $uri, array $params, string $content): RequestInterface;

}
