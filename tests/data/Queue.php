<?php declare(strict_types=1);

namespace h4kuna\Fio\Test;

use h4kuna\Fio;

class Queue implements Fio\Request\IQueue
{

	public function download(string $token, string $url): string
	{
		$file = '';
		switch (basename($url, 'json')) {
			case 'transactions.':
				preg_match('~((?:/[^/]+){3})$~U', $url, $find);
				$file = str_replace(['/', '-' . $token], ['-', ''], ltrim($find[1], '/'));
				break;
		}
		if ($file) {
			return file_get_contents(__DIR__ . '/../data/tests/' . $file);
		}
		return $file;
	}


	public function upload(string $url, string $token, array $post, string $filename): Fio\Response\Pay\IResponse
	{
		return new Fio\Response\Pay\XMLResponse((string) file_get_contents($filename));
	}

}
