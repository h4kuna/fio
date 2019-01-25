<?php declare(strict_types=1);

namespace h4kuna\Fio\Test;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class Response implements ResponseInterface
{

	public const RESPONSE_CODE = 1;
	public const EXCEPTION_CLASS = 2;

	private $method;

	private $uri;

	private $options;


	public function __construct($method, $uri, $options)
	{
		$this->method = $method;
		$this->uri = $uri;
		$this->options = $options;
	}


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
		if ('Content-Type' === $name) {
			if ($this->isOk()) {
				return ['application/json;charset=UTF-8'];
			}
			return ['text/xml;charset=UTF-8'];
		}
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
		return new Stream($this->isOk());
	}


	public function withBody(StreamInterface $body)
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


	public function withStatus($code, $reasonPhrase = '')
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function getReasonPhrase()
	{
		throw new \RuntimeException('Not implemented.');
	}


	private function isOk(): bool
	{
		return $this->getStatusCode() === 200;
	}

}
