<?php

require __DIR__ . '/bootstrap.php';

$json = \Nette\Utils\Json::decode(file_get_contents(__DIR__ . '/data/movements.json'));

var_dump(h4kuna\Fio\Response\Read\Transaction::getProperties());

exit;
foreach (get_object_vars($json) as $property) {
    var_dump($property);
}
//var_dump($json->accountStatement);
