<?php declare(strict_types=1);

namespace h4kuna\Fio\Utils;

use h4kuna\Fio\Contracts\RequestBlockingServiceContract;
use h4kuna\Fio\Exceptions;
use h4kuna\Fio\Pay\Response;
use h4kuna\Fio\Pay\XMLResponse;
use Nette\Utils\Strings;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Queue
{
	private const HEADER_CONFLICT = 409;
	private int $limitLoop = 3;


	public function __construct(
		private ClientInterface $client,
		private FioRequestFactory $requestFactory,
		private RequestBlockingServiceContract $requestBlockingService
	) {
	}


	/**
	 * @param positive-int $limitLoop
	 */
	public function setLimitLoop(int $limitLoop): void
	{
		$this->limitLoop = $limitLoop;
	}


	/**
	 * @throws Exceptions\ServiceUnavailable
	 */
	public function download(string $token, string $url): ResponseInterface
	{
		$response = $this->request($token, $this->requestFactory->get($url));
		$this->detectDownloadResponse($response);

		return $response;
	}


	/**
	 * @throws Exceptions\ServiceUnavailable
	 */
	private function detectDownloadResponse(ResponseInterface $response): void
	{
		$contentTypeHeaders = $response->getHeader('Content-Type');
		$contentType = array_shift($contentTypeHeaders);
		if ($contentType !== null && str_contains($contentType, 'text/xml')) {
			$xmlResponse = $this->createXmlResponse($response);

			throw new Exceptions\ServiceUnavailable($xmlResponse->status(), $xmlResponse->code());
		}
	}


	/**
	 * @param array{token: string, type: string, lng?: string} $params
	 * @param string $content string is filepath or content
	 */
	public function import(array $params, string $content): Response
	{
		$response = $this->request(
			$params['token'],
			$this->requestFactory->post(Fio::REST_URL . 'import/', $params, $content),
		);

		return $this->createXmlResponse($response);
	}


	private function request(string $token, RequestInterface $request): ResponseInterface
	{
		$request = $request->withHeader('X-Powered-By', 'h4kuna/fio');
		$response = null;
		try {
			for ($i = 0; $i < $this->limitLoop && $response === null; ++$i) {
				$response = $this->requestBlockingService->synchronize($token, function () use ($request) {
					$response = $this->client->sendRequest($request);

					if ($response->getStatusCode() === self::HEADER_CONFLICT) {
						return null;
					}

					return $response;
				});
			}
		} catch (ClientExceptionInterface $e) {
			$message = str_replace($token, Strings::truncate($token, 10), $e->getMessage());
			throw new Exceptions\ServiceUnavailable($message, $e->getCode()); // in url is token, don't need keep previous exception
		}

		if ($response === null) {
			throw new Exceptions\QueueLimit(sprintf('You have limit up requests to server "%s". Too many requests in short time interval.', $this->limitLoop));
		}

		return $response;
	}


	/**
	 * @throws Exceptions\ServiceUnavailable
	 */
	private function createXmlResponse(ResponseInterface $response): XMLResponse
	{
		return new XMLResponse(Fio::getContents($response));
	}

}
