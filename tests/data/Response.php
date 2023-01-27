<?php declare(strict_types=1);

namespace h4kuna\Fio\Test;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class Response implements ResponseInterface
{
	public const RESPONSE_CODE = 1;
	public const EXCEPTION_CLASS = 2;

	private string $method;

	/** @var string|UriInterface */
	private $uri;

	/** @var mixed[] */
	private array $options = [];


	/**
	 * @param string|UriInterface $uri
	 * @param mixed[] $options
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


	public function withProtocolVersion($version): Response
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


	public function withHeader($name, $value): Response
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function withAddedHeader($name, $value): Response
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function withoutHeader($name): Response
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function getBody(): Stream
	{
		return new Stream($this->isOk());
	}


	public function withBody(StreamInterface $body): Response
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function getStatusCode()
	{
		if (isset($this->options[self::RESPONSE_CODE])) {
			$code = $this->options[self::RESPONSE_CODE];
		} elseif (isset($this->options[self::EXCEPTION_CLASS])) {
			$code = 500;
		} else {
			$code = 200;
		}
		return $code;
	}


	public function withStatus($code, $reasonPhrase = ''): Response
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
