<?php declare(strict_types = 1);

namespace h4kuna\Fio;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use h4kuna\Dir\Dir;
use h4kuna\Dir\TempDir;
use h4kuna\Fio\Account\AccountCollection;
use h4kuna\Fio\Account\AccountCollectionFactory;
use h4kuna\Fio\Contracts\RequestBlockingServiceContract;
use h4kuna\Fio\Exceptions\MissingDependency;
use h4kuna\Fio\Pay\XMLFile;
use h4kuna\Fio\Read\Json;
use h4kuna\Fio\Utils\FileRequestBlockingService;
use h4kuna\Fio\Utils\FioRequestFactory;
use h4kuna\Fio\Utils\Queue;
use Psr\Http\Client\ClientInterface;
use function is_string;

class FioFactory
{

	protected AccountCollection $accountCollection;

	protected Queue $queue;


	/**
	 * @param array<array{token: string, account: string}> $accounts
	 */
	public function __construct(
		array $accounts,
		string|Dir $temp = 'fio',
		?ClientInterface $client = null,
		?FioRequestFactory $fioRequestFactory = null,
	)
	{
		$this->accountCollection = $this->createAccountCollection($accounts);
		$this->queue = $this->createQueue(
			$this->createRequestBlockingService(is_string($temp) ? new TempDir($temp) : $temp),
			$client ?? self::createClientInterface(),
			$fioRequestFactory ?? self::createRequestFactory(),
		);
	}

	public function createFioRead(string $name = ''): FioRead
	{
		return new FioRead($this->queue, $this->accountCollection->account($name), $this->createReader());
	}

	public function createFioPay(string $name = ''): FioPay
	{
		return new FioPay($this->queue, $this->accountCollection->account($name), $this->createXmlFile());
	}

	protected function createQueue(
		RequestBlockingServiceContract $requestBlockingService,
		ClientInterface $client,
		FioRequestFactory $fioRequestFactory,
	): Queue
	{
		return new Queue($client, $fioRequestFactory, $requestBlockingService);
	}

	protected function createRequestBlockingService(Dir $tempDir): RequestBlockingServiceContract
	{
		return new FileRequestBlockingService($tempDir->create());
	}

	/**
	 * @param array<array{token: string, account: string}> $accounts
	 */
	protected function createAccountCollection(array $accounts): AccountCollection
	{
		return AccountCollectionFactory::create($accounts);
	}

	protected function createReader(): Json
	{
		return new Json();
	}

	/**
	 * PAY *********************************************************************
	 * *************************************************************************
	 */
	protected function createXmlFile(): XMLFile
	{
		return new XMLFile();
	}

	private static function createClientInterface(): ClientInterface
	{
		MissingDependency::checkGuzzlehttp();

		return new Client();
	}

	private static function createRequestFactory(): FioRequestFactory
	{
		MissingDependency::checkGuzzlehttp();

		$httpFactory = new HttpFactory();
		return new FioRequestFactory($httpFactory, $httpFactory);
	}

}
