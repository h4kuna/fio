# Fio

[![Downloads this Month](https://img.shields.io/packagist/dm/h4kuna/fio.svg)](https://packagist.org/packages/h4kuna/fio)
[![Latest Stable Version](https://poser.pugx.org/h4kuna/fio/v/stable?format=flat)](https://packagist.org/packages/h4kuna/fio)
[![Coverage Status](https://coveralls.io/repos/github/h4kuna/fio/badge.svg?branch=main)](https://coveralls.io/github/h4kuna/fio?branch=main)
[![Total Downloads](https://poser.pugx.org/h4kuna/fio/downloads?format=flat)](https://packagist.org/packages/h4kuna/fio)
[![License](https://poser.pugx.org/h4kuna/fio/license?format=flat)](https://packagist.org/packages/h4kuna/fio)

Part of the [h4kuna PHP libraries](https://github.com/h4kuna/library), see the overview of all packages.

Supports the [Fio API](https://www.fio.cz/docs/cz/API_Bankovnictvi.pdf). Movements are read in the JSON format.

### Versions

Here is the [changelog](changelog.md).

### Nette framework
Follow this [extension](https://github.com/h4kuna/fio-nette).

### Installation to project by composer

Requires PHP 8.2 or newer.

```sh
composer require h4kuna/fio

# optional, default HTTP client used by FioFactory
composer require guzzlehttp/guzzle
```

Without Guzzle, pass your own PSR-18 client and `Utils\FioRequestFactory` to the `FioFactory` constructor.

## Not implemented
- 5.3.1.7: STA (MT940)
- 5.3.2: Transakce z POS terminálů nebo platební brány obchodníka
- 6.4.2: pain.008 (příkazy k inkasu)
- only JSON for reading movements
- only XML or ABO file for import

### How to use
Here is an [example](tests/src/E2E/FioTest.php), you can run it from the CLI. The script requires `account.ini` in the same directory (see [account.ini.example](tests/src/E2E/account.ini.example)), which looks like this:

```ini
[my-account]
account = 123456789
token = abcdefghijklmn

[wife-account]
account = 987654321
token = zyxuvtsrfd
```

The class `FioFactory` helps you create instances of the classes `FioPay` and `FioRead`. The second parameter of the constructor is the temp directory used to block concurrent requests with the same token (Fio allows one request per 30 seconds), the default is `h4kuna/fio` in the system temp directory.

```php
use h4kuna\Fio;

$fioFactory = new Fio\FioFactory(parse_ini_file($ini, true));

$fioRead = $fioFactory->createFioRead('my-account');
$fioPay = $fioFactory->createFioPay('wife-account');

$fioRead2 = $fioFactory->createFioRead(); // first in list is default, [my-account]
```

You can use a different config, but keep the structure of this PHP array:
```php
[
	'my-alias' => [
		'account' => '123456789',
		'token' => 'abcdefg'
	],
	'next-alias' => [
		'account' => '987654321',
		'token' => 'tuvwxyz'
	]
]
```

## Reading

> Keep in mind that the variable, constant and specific symbols are strings and can have leading zeros.

#### Read movements in a date range

```php
use h4kuna\Fio;
/* @var $fioRead Fio\FioRead */
/* @var $list Fio\Read\TransactionList */
$list = $fioRead->movements(/* $from, $to */); // default is the last week

foreach ($list as $transaction) {
    /* @var $transaction Fio\Read\Transaction */
    var_dump($transaction->moveId);
    foreach ($transaction as $property => $value) {
        var_dump($property, $value);
    }
}

var_dump($list->getInfo());
```

#### Download transactions by the statement id and year

```php
use h4kuna\Fio;
/* @var $fioRead Fio\FioRead */
/* @var $list Fio\Read\TransactionList */
$list = $fioRead->movementId(2, 2015); // second statement of the year 2015
```

#### Download new transactions since the last download
After the download, the break point is moved automatically.

```php
use h4kuna\Fio;
/* @var $fioRead Fio\FioRead */
/* @var $list Fio\Read\TransactionList */
$list = $fioRead->lastDownload();
// same usage as above
var_dump($list->getInfo()->idLastDownload);
```

#### Change your break point
By date:
```php
$fioRead->setLastDate('1986-12-30');
$list = $fioRead->lastDownload();
var_dump($list->getInfo()->idLastDownload);
```

By movement ID:
```php
$fioRead->setLastId(123456789);
$list = $fioRead->lastDownload();
var_dump($list->getInfo()->idLastDownload); // 123456789
```

> Tip: You can use your own transaction class. Extend `Read\TransactionFactory` and override `createTransaction()`, pass the factory to `Read\Json::__construct()` and return this reader from `createReader()` in your own subclass of `FioFactory`.

## Payment (writing)

The API has three response languages (`cs`, `en`, `sk`), the default is **cs**. To change it:
```php
use h4kuna\Fio;
/* @var $fioPay Fio\FioPay */
$fioPay->setLanguage('en');
```

The method `send()` sends the request. It accepts a path to your XML or ABO file, or XML content. Without a parameter it sends all payments created by `create*()` methods.
```php
$myFile = '/path/to/my/xml/or/abo/file.xml'; // file extension is important
$response = $fioPay->send($myFile);
var_dump($response->isOk(), $response->errorMessages());
```

Payment to a Czech or Slovak account:

```php
/* @var $national Fio\Pay\Payment\National */
$national = $fioPay->createNational($amount, $accountTo);
$national->setVariableSymbol($vs);
/* set next payment property $national->set* */
$fioPay->send();
```

Euro zone (SEPA) payment:

```php
/* @var $euro Fio\Pay\Payment\Euro */
$euro = $fioPay->createEuro($amount, $accountTo, $name);
$euro->setVariableSymbol($vs);
/* set next payment property $euro->set* */
$fioPay->send();
```

International payment:

```php
/* @var $international Fio\Pay\Payment\International */
$international = $fioPay->createInternational($amount, $accountTo, $name, $street, $city, $country, $info, $bic);
$international->setRemittanceInfo2('foo');
/* set next payment property $international->set* */
$fioPay->send();
```

Send more payments in one request:

```php
foreach ($paymentRows as $row) {
	/* @var $national Fio\Pay\Payment\National */
	$national = $fioPay->createNational($row->amount, $row->accountTo);
	$national->setVariableSymbol($row->vs);
}
$fioPay->send();
```
