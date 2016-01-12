<?php

require __DIR__ . '/../bootstrap.php';

$ini = __DIR__ . '/account.ini';
if (!is_file($ini)) {
	throw new \h4kuna\Fio\FioException('File not found: ' . $ini);
}

$fioFactory = new \h4kuna\Fio\Utils\FioFactory;
$fioRead = $fioFactory->createFioRead(parse_ini_file($ini, TRUE));

foreach ($fioRead->movements('-1 month') as $transaction) {
	/* @var $transaction \h4kuna\Fio\Response\Read\Transaction */
	var_dump($transaction->moveId);
}

// sleep 30s
foreach ($fioRead->movements('-1 month') as $transaction) {
	/* @var $transaction \h4kuna\Fio\Response\Read\Transaction */
	\Tester\Assert::true(is_int($transaction->moveId));
}

