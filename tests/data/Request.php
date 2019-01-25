<?php declare(strict_types=1);

namespace h4kuna\Fio\Test;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class Request implements RequestInterface
{

	public function getProtocolVersion()
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function withProtocolVersion($version)
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function getHeaders()
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function hasHeader($name)
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function getHeader($name)
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function getHeaderLine($name)
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function withHeader($name, $value)
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function withAddedHeader($name, $value)
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function withoutHeader($name)
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function getBody()
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function withBody(StreamInterface $body)
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function getRequestTarget()
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function withRequestTarget($requestTarget)
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function getMethod()
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function withMethod($method)
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function getUri()
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function withUri(UriInterface $uri, $preserveHost = false)
	{
		throw new \RuntimeException('Not implemented.');
	}

}
