<?php declare(strict_types=1);

namespace h4kuna\Fio\Tests\Fixtures;

use h4kuna\Fio\Utils\FioRequestFactory;
use Psr\Http\Message\RequestInterface;

final class RequestFactoryMock extends FioRequestFactory
{
	public function __construct() // @phpstan-ignore-line
	{
	}


	public function get(string $uri): RequestInterface
	{
		return new Request($uri);
	}


	public function post(string $uri, array $params, string $content): RequestInterface
	{
		return new Request($uri . '?file=' . $content);
	}

}
