<?php declare(strict_types=1);

namespace h4kuna\Fio\Tests\Fixtures;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class Response implements ResponseInterface
{
	public const RESPONSE_CODE = 1;
	public const EXCEPTION_CLASS = 2;


	/**
	 * @param array<mixed> $options
	 */
	public function __construct(private string $method, private string|UriInterface $uri, private array $options)
	{
		assert($this->method !== '');
		assert($this->uri !== '');
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
		if ('Content-Type' === $name) {
			if ($this->isOk()) {
				return ['application/json;charset=UTF-8'];
			}

			return ['text/xml;charset=UTF-8'];
		}

		return [];
	}


	public function getHeaderLine($name): string
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function withHeader($name, $value): MessageInterface
	{
		throw new \RuntimeException('Not implemented.');
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
		return new Stream($this->isOk());
	}


	public function withBody(StreamInterface $body): MessageInterface
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function getStatusCode(): int
	{
		if (isset($this->options[self::RESPONSE_CODE])) {
			$code = intval($this->options[self::RESPONSE_CODE]);
		} elseif (isset($this->options[self::EXCEPTION_CLASS])) {
			$code = 500;
		} else {
			$code = 200;
		}

		return $code;
	}


	public function withStatus($code, $reasonPhrase = ''): ResponseInterface
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
