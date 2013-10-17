<?php

include __DIR__ . "/vendor/autoload.php";

$configurator = new Nette\Config\Configurator;
$tmp = __DIR__ . '/tests/tmp';
$configurator->enableDebugger($tmp);
$configurator->setTempDirectory($tmp);
$container = $configurator->createContainer();

// init Fio, param is token
$fio = new h4kuna\Fio(require __DIR__ . '/tests/tmp/secureToken.php');

/**
 * READ MOVEMENTS **************************************************************
 * *****************************************************************************
 */
// from last download
foreach ($fio->lastDownload() as $data) {
    dump($data); // save to db
}

# $fio->getRequestUrl(); // request url
// date range - default is one month ago
# foreach ($fio->movements() as $data) {
#     dump($data); // save to db
# }
// from move id
# foreach ($fio->movementId('int moveId like 3540372617') as $data) {
#    dump($data); // save to db
# }

/**
 * BREAKPOINTS *****************************************************************
 */
# $fio->setLastDate('2013-01-01'); // read movements from this date
# $fio->setLastId('123456'); // read movement from this id

/**
 * SEND PAYMENT ****************************************************************
 * *****************************************************************************
 */
/**
 * PREPARE XML *****************************************************************
 */
# $fio->setLanguage('en'); // change request language default is czech

$fioXml = new h4kuna\Fio\XMLFio('2600267402'); // your FIO account, accept 2600267402/2010
$fioXml
        ->setVariableSymbol('789456123') // optional, look at to all setters
        ->setConstantSymbol('0308') // optional
        ->addPayment(300, '2000242017/2010'); // mandatory
// ->addPayment(300, '2000242017', '2010'); // is same as line above
// file_put_contents($tmp . '/generated.xml', (string) $fioXml); // request xml

/**
 * SEND REQUEST ****************************************************************
 */
$response = $fio->uploadXmlFio($fioXml); // response is h4kuna\Fio\XMLResponse
# $fio->getUploadResponse(); // is same object as in variable $response
// $fio->uploadFile($path) // file path
// $fio->uploadFileContent('<?xml ...'); // file content


if ($response->isOk()) {
    dump('good');
} else {
    dump($response->getXml(), $response->getStatus(), $response->getError());
}


// test ************************************************************************


dump($response);

/**
 * @todo Curl update
 * @todo README
 */

