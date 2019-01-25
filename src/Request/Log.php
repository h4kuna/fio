<?php declare(strict_types=1);

namespace h4kuna\Fio\Request;

use h4kuna\Fio\Exceptions\InvalidState;

class Log
{

	private $filename = '';


	public function setFilename(string $filename): void
	{
		$this->filename = $filename;
	}


	public function getFilename(): string
	{
		return $this->filename;
	}


	public function getContent(): string
	{
		$content = @file_get_contents($this->filename);
		if ($content === false) {
			throw new InvalidState(sprintf('Filname "%s" can not read.', $this->filename));
		}
		return $content;
	}

}
