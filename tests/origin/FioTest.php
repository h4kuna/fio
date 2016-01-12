<?php

require __DIR__ . '/../bootstrap.php';

$ini = __DIR__ . '/account.ini';
if (!is_file($ini)) {
	throw new \h4kuna\Fio\InvalidArgumentException('File not found: ' . $ini);
}

$fioFactory = new \h4kuna\Fio\Utils\FioFactory(parse_ini_file($ini, TRUE));
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
$fioPay->setActive('pay');
$national = $fioPay->createNational(100, '2000242017/2010');

$response = $fioPay->send($national);
dd($response->isOk(), $response->getError(), $response->getErrorCode());

