<?php declare(strict_types=1);

namespace h4kuna\Fio\Utils;

use Closure;
use h4kuna\Dir\Dir;
use h4kuna\Fio\Contracts\RequestBlockingServiceContract;
use h4kuna\Fio\Exceptions\InvalidState;
use Nette\SafeStream\Wrapper;
use Psr\Http\Message\ResponseInterface;

final class FileRequestBlockingService implements RequestBlockingServiceContract
{
	/** @var array<string, string> */
	private static array $tokens = [];

	/**
	 * @param int $waitTime - seconds
	 */
	public function __construct(
		private Dir $tempDir,
		private int $waitTime = 31,
	) {
	}


	public function synchronize(string $token, Closure $callback): ?ResponseInterface
	{
		$tempFile = $this->loadFileName($token);
		$file = self::createFileResource($tempFile);
		$sleep = $this->waitTime - (time() - ((int) fgets($file, 30)));
		if ($sleep > 0) {
			sleep($sleep);
		}

		try {
			$response = $callback();
		} finally {
			fseek($file, 0);
			ftruncate($file, 0);
			fputs($file, (string) time());
			fclose($file);
		}

		return $response;
	}

	/**
	 * @return resource
	 */
	private static function createFileResource(string $filePath)
	{
		if (is_file($filePath) === false) {
			touch($filePath);
		}
		$file = fopen(self::safeProtocol($filePath), 'r+');
		if ($file === false) {
			throw new InvalidState('Open file is failed ' . $filePath);
		}

		return $file;
	}


	private function loadFileName(string $token): string
	{
		$key = substr($token, 10, -10);
		if (!isset(self::$tokens[$key])) {
			self::$tokens[$key] = $this->tempDir->filename(md5($key));
		}

		return self::$tokens[$key];
	}


	private static function safeProtocol(string $filename): string
	{
		return Wrapper::Protocol . "://$filename";
	}
}
