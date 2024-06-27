<?php declare(strict_types=1);

namespace h4kuna\Fio;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use h4kuna\Dir\Dir;
use h4kuna\Dir\TempDir;
use h4kuna\Fio;
use h4kuna\Fio\Contracts\RequestBlockingServiceContract;
use Psr\Http\Client\ClientInterface;

class FioFactory
{
	protected Account\AccountCollection $accountCollection;

	protected Utils\Queue $queue;


	/**
	 * @param array<array{token: string, account: string}> $accounts
	 */
	public function __construct(
		array $accounts,
		string|Dir $temp = 'fio',
		?ClientInterface $client = null,
		?Utils\FioRequestFactory $fioRequestFactory = null,
	) {

		$this->accountCollection = $this->createAccountCollection($accounts);
		$this->queue = $this->createQueue(
			$this->createRequestBlockingService(is_string($temp) ? new TempDir($temp) : $temp),
			$client ?? self::createClientInterface(),
			$fioRequestFactory ?? self::createRequestFactory()
		);
	}


	public function createFioRead(string $name = ''): Fio\FioRead
	{
		return new Fio\FioRead($this->queue, $this->accountCollection->account($name), $this->createReader());
	}


	public function createFioPay(string $name = ''): Fio\FioPay
	{
		return new Fio\FioPay($this->queue, $this->accountCollection->account($name), $this->createXmlFile());
	}


	protected function createQueue(RequestBlockingServiceContract $requestBlockingService, ClientInterface $client, Utils\FioRequestFactory $fioRequestFactory): Utils\Queue
	{
		return new Utils\Queue($client, $fioRequestFactory, $requestBlockingService);
	}

	protected function createRequestBlockingService(Dir $tempDir): RequestBlockingServiceContract
	{
		return new Fio\Utils\FileRequestBlockingService($tempDir->create());
	}


	/**
	 * @param array<array{token: string, account: string}> $accounts
	 */
	protected function createAccountCollection(array $accounts): Account\AccountCollection
	{
		return Account\AccountCollectionFactory::create($accounts);
	}


	protected function createReader(): Fio\Read\Json
	{
		return new Fio\Read\Json();
	}


	/**
	 * PAY *********************************************************************
	 * *************************************************************************
	 */
	protected function createXmlFile(): Pay\XMLFile
	{
		return new Pay\XMLFile();
	}


	private static function createClientInterface(): ClientInterface
	{
		Fio\Exceptions\MissingDependency::checkGuzzlehttp();

		return new Client();
	}


	private static function createRequestFactory(): Utils\FioRequestFactory
	{
		Fio\Exceptions\MissingDependency::checkGuzzlehttp();

		$httpFactory = new HttpFactory();
		return new Utils\FioRequestFactory($httpFactory, $httpFactory);
	}

}
