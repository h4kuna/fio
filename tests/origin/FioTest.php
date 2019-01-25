<?php declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

$ini = __DIR__ . '/account.ini';
if (!is_file($ini)) {
	throw new \h4kuna\Fio\Exceptions\InvalidArgument('File not found: ' . $ini);
}

$accounts = parse_ini_file($ini, true);
if ($accounts === false) {
	throw new \h4kuna\Fio\Exceptions\InvalidState('You have bad format for ini file. Let\'s see account.example.ini.');
}

$fioFactory = new \h4kuna\Fio\Utils\FioFactory($accounts);
$fioRead = $fioFactory->createFioRead();

foreach ($fioRead->movements('-1 month') as $transaction) {
	/* @var $transaction \h4kuna\Fio\Response\Read\Transaction */
	var_dump($transaction->moveId);
}

// sleep
foreach ($fioRead->movements('-1 month') as $transaction) {
	/* @var $transaction \h4kuna\Fio\Response\Read\Transaction */
	\Tester\Assert::true(is_int($transaction->moveId));
}

// blocation is per token
$fioPay = $fioFactory->createFioPay();
$log = $fioPay->enableLog();
//$national = $fioPay->createNational(100, '2600267402/2010');
//$response = $fioPay->send($national);

$euro = $fioPay->createEuro(100, 'EE957700771001355096/LHVBEE22XXX', 'Coinbase UK, Ltd.');
$response = $fioPay->send($euro);

dump($log->getFilename());
echo($log->getContent());
dumpe($response->isOk(), $response->status(), $response->code(), $response->errorMessages());
