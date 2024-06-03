<?php declare(strict_types=1);

namespace h4kuna\Fio\Test;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class Request implements RequestInterface
{

	public function getProtocolVersion(): string
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function withProtocolVersion(string $version): MessageInterface
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function getHeaders(): array
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function hasHeader(string $name): bool
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function getHeader(string $name): array
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function getHeaderLine(string $name): string
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function withHeader(string $name, $value): MessageInterface
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function withAddedHeader(string $name, $value): MessageInterface
	{
		throw new \RuntimeException('Not implemented.');
	}

	public function withoutHeader(string $name): MessageInterface
	{
		throw new \RuntimeException('Not implemented.');
	}

	public function getBody(): StreamInterface
	{
		throw new \RuntimeException('Not implemented.');
	}

	public function withBody(StreamInterface $body): MessageInterface
	{
		throw new \RuntimeException('Not implemented.');
	}

	public function getRequestTarget(): string
	{
		throw new \RuntimeException('Not implemented.');
	}

	public function withRequestTarget(string $requestTarget): RequestInterface
	{
		throw new \RuntimeException('Not implemented.');
	}

	public function getMethod(): string
	{
		throw new \RuntimeException('Not implemented.');
	}

	public function withMethod(string $method): RequestInterface
	{
		throw new \RuntimeException('Not implemented.');
	}

	public function getUri(): UriInterface
	{
		throw new \RuntimeException('Not implemented.');
	}

	public function withUri(UriInterface $uri, bool $preserveHost = false): RequestInterface
	{
		throw new \RuntimeException('Not implemented.');
	}


}
