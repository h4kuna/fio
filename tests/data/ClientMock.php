<?php declare(strict_types=1);

namespace h4kuna\Fio\Test;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Message\RequestInterface;

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
