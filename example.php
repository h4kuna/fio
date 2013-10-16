<?php

include __DIR__ . "/vendor/autoload.php";

$configurator = new Nette\Config\Configurator;
$tmp = __DIR__ . '/tests/tmp';
$configurator->enableDebugger($tmp);
$configurator->setTempDirectory($tmp);
$container = $configurator->createContainer();


$fio = new h4kuna\Fio(require __DIR__ . '/tests/tmp/secureToken.php');

$fioXml = new h4kuna\Fio\XMLFio('2600267402');
$fioXml->addPayment(0.3, '2000242017/2010');
$fioXml->addPayment(0.4, '2000242017/2010');
file_put_contents($tmp . '/generated.xml', (string) $fioXml);
//$response = $fio->uploadXmlFio($fioXml);

$response = new \h4kuna\Fio\XMLResponse(file_get_contents($tmp . '/../response/respons.xml'));

dump($response);

exit;




foreach ($fio->movements() as $data) {
    dump($data); // save to db
}