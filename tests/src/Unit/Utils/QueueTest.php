<?php declare(strict_types=1);

namespace h4kuna\Fio\Tests\Unit\Utils;

use GuzzleHttp;
use h4kuna;
use h4kuna\Dir\TempDir;
use h4kuna\Fio\Tests\Fixtures\ClientMock;
use h4kuna\Fio\Tests\Fixtures\RequestFactoryMock;
use h4kuna\Fio\Tests\Fixtures\TestCase;
use h4kuna\Fio\Utils\FileRequestBlockingService;
use h4kuna\Fio\Utils\Queue;
use Tester\Assert;
use function h4kuna\Fio\Tests\loadResult;

require __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
class QueueTest extends TestCase
{
	private const TOKEN = 'test_test_test_test_test_test_test';


	/**
	 * @throws \h4kuna\Fio\Exceptions\ServiceUnavailable
	 */
	public function testDownloadThrowServiceUnavailable(): void
	{
		$queue = self::createQueue();
		$queue->download(self::TOKEN, 'http://www.example.com/?file=server-exception.xml&status=500');
	}


	/**
	 * @throws h4kuna\Fio\Exceptions\ServiceUnavailable
	 */
	public function testDownloadThrowClientException(): void
	{
		$queue = self::createQueue();
		$queue->download(self::TOKEN, 'http://www.example.com/?exception=' . GuzzleHttp\Exception\TransferException::class);
	}


	public function testDownloadOk(): void
	{
		$queue = self::createQueue();
		$json = $queue->download(self::TOKEN, 'http://www.example.com/');
		Assert::same(loadResult('raw://2015-2-transactions.json'), $json->getBody()->getContents());
	}


	public function testDownloadThrowQueueNoLimit(): void
	{
		$queue = self::createQueue();
		$queue->setLimitLoop(1);
		Assert::exception(fn () => $queue->download(self::TOKEN, 'http://www.example.com/?status=409'), h4kuna\Fio\Exceptions\QueueLimit::class, 'You have limit up requests to server "1". Too many requests in short time interval.');
	}


	public function testDownloadThrowQueueLimit(): void
	{
		$queue = self::createQueue();
		$queue->setLimitLoop(2);
		Assert::exception(fn () => $queue->download(self::TOKEN, 'http://www.example.com/?status=409'), h4kuna\Fio\Exceptions\QueueLimit::class, 'You have limit up requests to server "2". Too many requests in short time interval.');
	}


	public function testUpload(): void
	{
		$queue = self::createQueue();
		$xml = $queue->import([
			'type' => 'xml',
			'lng' => 'cs',
			'token' => self::TOKEN,
		], __DIR__ . '/../../../data/payment/xml-pay-response-ok.xml');

		Assert::true($xml->isOk());
	}


	private static function createQueue(): Queue
	{
		$tempDir = new TempDir(__DIR__ . '/../../../temp');

		$q = new Queue(new ClientMock(), new RequestFactoryMock(), new FileRequestBlockingService($tempDir, 2));
		$q->setLimitLoop(1);

		return $q;
	}

}

(new QueueTest())->run();
