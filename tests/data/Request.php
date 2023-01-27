<?php declare(strict_types=1);

namespace h4kuna\Fio\Test;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class Request implements RequestInterface
{

	public function getProtocolVersion(): string
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function withProtocolVersion($version): Request
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function getHeaders(): array
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function hasHeader($name): bool
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function getHeader($name): array
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function getHeaderLine($name): string
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function withHeader($name, $value): Request
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function withAddedHeader($name, $value): Request
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function withoutHeader($name): Request
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function getBody(): StreamInterface
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function withBody(StreamInterface $body): Request
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function getRequestTarget(): string
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function withRequestTarget($requestTarget): Request
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function getMethod(): string
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function withMethod($method): Request
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function getUri(): UriInterface
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function withUri(UriInterface $uri, $preserveHost = false): Request
	{
		throw new \RuntimeException('Not implemented.');
	}

}
