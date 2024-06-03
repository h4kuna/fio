<?php declare(strict_types=1);

namespace h4kuna\Fio\Test;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class Response implements ResponseInterface
{
	public const RESPONSE_CODE = 1;
	public const EXCEPTION_CLASS = 2;

	/** @var string */
	private $method;

	/** @var string|UriInterface */
	private $uri;

	/** @var array */
	private $options = [];


	/**
	 * @param string|UriInterface $uri
	 */
	public function __construct(string $method, $uri, array $options)
	{
		$this->method = $method;
		$this->uri = $uri;
		$this->options = $options;
	}


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
		if ('Content-Type' === $name) {
			if ($this->isOk()) {
				return ['application/json;charset=UTF-8'];
			}
			return ['text/xml;charset=UTF-8'];
		}
		return [];
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
		return new Stream($this->isOk());
	}


	public function withBody(StreamInterface $body): MessageInterface
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function getStatusCode(): int
	{
		if (isset($this->options[self::RESPONSE_CODE])) {
			$code = $this->options[self::RESPONSE_CODE];
		} elseif (isset($this->options[self::EXCEPTION_CLASS])) {
			$code = 500;
		} else {
			$code = 200;
		}
		return (int) $code;
	}


	public function withStatus(int $code, string $reasonPhrase = ''): ResponseInterface
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function getReasonPhrase(): string
	{
		throw new \RuntimeException('Not implemented.');
	}


	private function isOk(): bool
	{
		return $this->getStatusCode() === 200;
	}

}
