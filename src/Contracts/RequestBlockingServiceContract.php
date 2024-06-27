<?php declare(strict_types=1);

namespace h4kuna\Fio\Contracts;

use Closure;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\ResponseInterface;

interface RequestBlockingServiceContract
{
	/**
	 * @param Closure(): ?ResponseInterface $callback
	 *
	 * @throws ClientExceptionInterface
	 */
	function synchronize(string $token, Closure $callback): ?ResponseInterface;
}
