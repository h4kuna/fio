<?php declare(strict_types=1);

namespace h4kuna\Fio\Tests\Fixtures;

use Psr\Http\Message\StreamInterface;
use function h4kuna\Fio\Tests\loadResult;

class Stream implements StreamInterface
{

	public function __construct(private bool $isOk)
	{
	}


	public function __toString()
	{
		return $this->getContents();
	}


	public function close()
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function detach()
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function getSize(): ?int
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function tell(): int
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function eof(): bool
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function isSeekable(): bool
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function seek($offset, $whence = SEEK_SET): void
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function rewind(): void
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function isWritable(): bool
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function write($string): int
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function isReadable(): bool
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function read($length): string
	{
		throw new \RuntimeException('Not implemented.');
	}


	public function getContents(): string
	{
		$content = loadResult(sprintf('payment/response%s.xml', $this->isOk ? '' : '-error'));
		assert(is_string($content));

		return $content;
	}


	public function getMetadata($key = null)
	{
		throw new \RuntimeException('Not implemented.');
	}

}
