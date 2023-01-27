<?php declare(strict_types=1);

namespace h4kuna\Fio\Test;

use Psr\Http\Message\StreamInterface;
use Salamium\Testinium\File;

class Stream implements StreamInterface
{
	private bool $isOk;


	public function __construct(bool $failed)
	{
		$this->isOk = $failed;
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


	public function getContents(): bool|string
	{
		return File::load(sprintf('payment/response%s.xml', $this->isOk ? '' : '-error'));
	}


	public function getMetadata($key = null)
	{
		throw new \RuntimeException('Not implemented.');
	}

}
