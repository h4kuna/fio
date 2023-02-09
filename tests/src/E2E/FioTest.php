<?php declare(strict_types=1);

namespace h4kuna\Fio\Tests\E2E;

use h4kuna\Fio;
use Tester;

require __DIR__ . '/../bootstrap.php';

$ini = __DIR__ . '/account.ini';
if (!is_file($ini)) {
	Tester\Environment::skip('Missing config file.');
}

$accounts = parse_ini_file($ini, true);

if ($accounts === false) {
	throw new Fio\Exceptions\InvalidState('You have bad format for ini file. Let\'s see account.example.ini.');
}

$fioFactory = new Fio\FioFactory($accounts);
$fioRead = $fioFactory->createFioRead();
Tester\Assert::same($accounts['my-fio-account']['account'], $fioRead->getAccount()->getAccount());

$movements = $fioRead->movements('2023-01-01', '2023-01-31');
Tester\Assert::same(5, count($movements));

Tester\Assert::type(\stdClass::class, $movements->getInfo());
Tester\Assert::same($accounts['my-fio-account']['account'], $movements->getInfo()->accountId);

foreach ($movements as $transaction) {
	Tester\Assert::type(Fio\Read\Transaction::class, $transaction);
}

// blocking is per token
$fioPay = $fioFactory->createFioPay();

$fioPay->createNational(100, '2600267402/2010');
$response = $fioPay->send();
Tester\Assert::false($response->isOk());
Tester\Assert::same(1, $response->code());
Tester\Assert::same([108 => 'Číslo účtu příjemce je identické s číslem účtu plátce.'], $response->errorMessages());
