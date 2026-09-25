<?php declare(strict_types = 1);

namespace h4kuna\Fio\Tests\E2E;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use h4kuna\Dir\Dir;
use h4kuna\Fio\Exceptions\InvalidState;
use h4kuna\Fio\FioFactory;
use h4kuna\Fio\Read\Transaction;
use h4kuna\Fio\Utils\FioRequestFactory;
use stdClass;
use Symfony\Component\HttpClient\Psr18Client;
use Tester\Assert;
use Tester\Environment;
use function is_file;
use function parse_ini_file;

require __DIR__ . '/../bootstrap.php';

$ini = __DIR__ . '/account.ini';
if (!is_file($ini)) {
	Environment::skip('Missing config file.');
}

/**
 * @var false|array<array{token: string, account: string}> $accounts
 */
$accounts = parse_ini_file($ini, true);

if ($accounts === false) {
	throw new InvalidState('You have bad format for ini file. Let\'s see account.example.ini.');
}
$dir = new Dir(__DIR__ . '/../../temp');

// "strictphp/http-clients": "^0.1.3", // php 8.2+
// class Filesystem implements FileFactoryContract
// {
//	public function __construct(
//		private Dir $dir
//	) {
//	}
//
//	public function create(FileInfoEntity $file, string $suffix = ''): FileContract
//	{
//		$dir = $this->dir->dir($file->path);
//		\Nette\Utils\FileSystem::createDir($dir->getDir());
//
//		return new File($dir->filename($file->name . $suffix));
//	}
//
// }
// $configManager = new ConfigManager();
// $configManager->addDefault(new StoreConfig(true));
// $filesystemFactory = new Filesystem($dir);
// $makePathAction = new MakePathAction();
// $streamAction = new StreamAction();
// $saveResponse = new SaveResponse($filesystemFactory, $makePathAction, new FindExtensionFromHeadersAction(), $streamAction, serialized: false);
// $saveForPhpstormRequest = new SaveForPhpstormRequest($filesystemFactory, $makePathAction, $saveResponse, $streamAction);
// $client = new StoreResponseClient($saveForPhpstormRequest, $configManager, $psrClient);

$psrFactory = new HttpFactory();
$fioRequest = new FioRequestFactory($psrFactory, $psrFactory);
$psrClient = new Psr18Client(responseFactory: $psrFactory, streamFactory: $psrFactory); // symfony
$psrClient = new Client(); // guzzle

$client = $psrClient;

$fioFactory = new FioFactory($accounts, $dir->dir('fio'), client: $client, fioRequestFactory: $fioRequest);
$fioRead = $fioFactory->createFioRead();
Assert::same($accounts['my-fio-account']['account'], $fioRead->getAccount()->getAccount());

$movements = $fioRead->movements('-14 days');

Assert::type(stdClass::class, $movements->getInfo());
Assert::same($accounts['my-fio-account']['account'], $movements->getInfo()->accountId);

foreach ($movements as $transaction) {
	Assert::type(Transaction::class, $transaction);
}

// blocking is per token
$fioPay = $fioFactory->createFioPay();

$fioPay->createNational(100, '2600267402/2010');
$response = $fioPay->send();
Assert::true($response->isOk());
Assert::same(1, $response->code());
Assert::same([108 => 'Číslo účtu příjemce je identické s číslem účtu plátce.'], $response->errorMessages());
