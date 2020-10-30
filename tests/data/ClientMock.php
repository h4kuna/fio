<?php declare(strict_types=1);

namespace h4kuna\Fio\Test;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

$classReflection = new \ReflectionClass(ClientInterface::class);
$x = $classReflection->getMethod('requestAsync')->getParameters()[0]->getType();
if ($x !== null && $x->getName() === 'string') {
	class ClientMock implements ClientInterface
	{

		public function send(RequestInterface $request, array $options = []): ResponseInterface
		{
			throw new \RuntimeException('Not implemented. ' . __METHOD__);
		}


		public function sendAsync(RequestInterface $request, array $options = []): PromiseInterface
		{
			throw new \RuntimeException('Not implemented. ' . __METHOD__);
		}


		public function request(string $method, $uri, array $options = []): ResponseInterface
		{
			$response = new Response($method, $uri, $options);
			if (isset($options[Response::EXCEPTION_CLASS])) {
				if (in_array($options[Response::EXCEPTION_CLASS], [ClientException::class, ServerException::class])) {
					throw new $options[Response::EXCEPTION_CLASS]($uri, new Request(), $response);
				} else {
					throw new \RuntimeException('Unknown class.');
				}
			}

			return $response;
		}


		public function requestAsync(string $method, $uri, array $options = []): PromiseInterface
		{
			throw new \RuntimeException('Not implemented. ' . __METHOD__);
		}


		public function getConfig(?string $option = null): ResponseInterface
		{
			throw new \RuntimeException('Not implemented. ' . __METHOD__);
		}

	}
} else {
	class ClientMock implements ClientInterface
	{

		public function send(RequestInterface $request, array $options = [])
		{
			throw new \RuntimeException('Not implemented. ' . __METHOD__);
		}


		public function sendAsync(RequestInterface $request, array $options = [])
		{
			throw new \RuntimeException('Not implemented. ' . __METHOD__);
		}


		public function request($method, $uri, array $options = [])
		{
			$response = new Response($method, $uri, $options);
			if (isset($options[Response::EXCEPTION_CLASS])) {
				if (in_array($options[Response::EXCEPTION_CLASS], [ClientException::class, ServerException::class])) {
					throw new $options[Response::EXCEPTION_CLASS]($uri, new Request(), $response);
				} else {
					throw new \RuntimeException('Unknown class.');
				}
			}

			return $response;
		}


		public function requestAsync($method, $uri, array $options = [])
		{
			throw new \RuntimeException('Not implemented. ' . __METHOD__);
		}


		public function getConfig($option = null)
		{
			throw new \RuntimeException('Not implemented. ' . __METHOD__);
		}

	}
}
