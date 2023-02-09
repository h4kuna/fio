<?php declare(strict_types=1);

namespace h4kuna\Fio;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use h4kuna\Dir\TempDir;
use h4kuna\Fio;
use h4kuna\Fio\Account;
use Psr\Http\Client\ClientInterface;

class FioFactory
{
	protected Account\AccountCollection $accountCollection;

	protected Utils\Queue $queue;

	protected TempDir $tempDir;


	/**
	 * @param array<array{token: string, account: string}> $accounts
	 */
	public function __construct(
		array $accounts,
		string $temp = 'fio',
	)
	{
		$this->tempDir = new TempDir($temp);
		$this->accountCollection = $this->createAccountCollection($accounts);
		$this->queue = $this->createQueue();
	}


	public function createFioRead(string $name = ''): Fio\FioRead
	{
		return new Fio\FioRead($this->queue, $this->accountCollection->account($name), $this->createReader());
	}


	public function createFioPay(string $name = ''): Fio\FioPay
	{
		return new Fio\FioPay($this->queue, $this->accountCollection->account($name), $this->createXmlFile());
	}


	protected function createQueue(): Utils\Queue
	{
		return new Utils\Queue($this->tempDir, $this->createClientInterface(), $this->createRequestFactory());
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


	private function createClientInterface(): ClientInterface
	{
		Fio\Exceptions\MissingDependency::checkGuzzlehttp();

		return new Client();
	}


	private function createRequestFactory(): Utils\GuzzleRequestFactory
	{
		Fio\Exceptions\MissingDependency::checkGuzzlehttp();

		return new Utils\GuzzleRequestFactory(new HttpFactory());
	}

}
