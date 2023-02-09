<?php declare(strict_types=1);

namespace h4kuna\Fio\Exceptions;

use GuzzleHttp\Client;

final class MissingDependency extends \RuntimeException
{

	public static function checkGuzzlehttp(): void
	{
		if (class_exists(Client::class) === false) {
			throw self::create(Client::class, 'guzzlehttp/guzzle');
		}
	}


	private static function create(string $class, string $package): self
	{
		return new self("Missing class \"$class\", you can install by: composer require $package");
	}

}
