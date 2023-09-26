<?php declare(strict_types=1);

namespace h4kuna\Fio\Exceptions;

use GuzzleHttp\Client;
use Psr\Http\Client\ClientInterface;

final class MissingDependency extends \RuntimeException
{

	public static function checkGuzzlehttp(): void
	{
		if (class_exists(Client::class) === false) {
			throw self::create(Client::class, 'guzzlehttp/guzzle');
		}

		if (is_a(Client::class, ClientInterface::class, true) === false) {
			throw new self("Supported only guzzlehttp/guzzle 7.0+.");
		}
	}


	private static function create(string $class, string $package): self
	{
		return new self("Missing class \"$class\", you can install by: composer require $package");
	}

}
