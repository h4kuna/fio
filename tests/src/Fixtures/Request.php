<?php declare(strict_types=1);

namespace h4kuna\Fio\Tests\Fixtures;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class Request implements RequestInterface
{
	private Uri $uri;


	public function __construct(string $url)
	{
		$this->uri = new Uri($url);
	}


	public function getProtocolVersion(): string
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function withProtocolVersion($version): MessageInterface
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


	public function withHeader($name, $value): MessageInterface
	{
		return $this;
	}


	public function withAddedHeader($name, $value): MessageInterface
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function withoutHeader($name): MessageInterface
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


	public function withRequestTarget($requestTarget): RequestInterface
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function getMethod(): string
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function withMethod($method): RequestInterface
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function getUri(): UriInterface
	{
		return $this->uri;
	}


	public function withUri(UriInterface $uri, $preserveHost = false): RequestInterface
	{
		throw new \RuntimeException('Not implemented.');
	}

}
