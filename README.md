# Fio

[![Downloads this Month](https://img.shields.io/packagist/dm/h4kuna/fio.svg)](https://packagist.org/packages/h4kuna/fio)
[![Latest Stable Version](https://poser.pugx.org/h4kuna/fio/v/stable?format=flat)](https://packagist.org/packages/h4kuna/fio)
[![Coverage Status](https://coveralls.io/repos/github/h4kuna/fio/badge.svg?branch=master)](https://coveralls.io/github/h4kuna/fio?branch=master)
[![Total Downloads](https://poser.pugx.org/h4kuna/fio/downloads?format=flat)](https://packagist.org/packages/h4kuna/fio)
[![License](https://poser.pugx.org/h4kuna/fio/license?format=flat)](https://packagist.org/packages/h4kuna/fio)

Support [Fio API](http://www.fio.sk/docs/cz/API_Bankovnictvi.pdf). Read is provided via json file.

### Versions

Here is [changlog](changelog.md)

### Nette framework
Follow this [extension](//github.com/h4kuna/fio-nette).


### Installation to project by composer

```sh
$ composer require h4kuna/fio
```

## Not implemented
- 5.3.1.7: STA (MT940)
- 5.3.2: Transakce z POS terminálů nebo platební brány obchodníka
- 6.4.2: pain.008 (příkazy k inkasu)
- only json for read movements
- only xml for import

### How to use
Here is [example](tests/origin/FioTest.php) and run via cli. This script require account.ini in same directory, whose looks like.

```ini
[my-account]
account = 123456789
token = abcdefghijklmn

[wife-account]
account = 987654321
token = zyxuvtsrfd
```

FioFactory class help you create instances of classes FioPay and FioRead.

```php
use h4kuna\Fio;

$fioFactory = new Fio\FioFactory(parse_ini_file($ini, true));

$fioRead = $fioFactory->createFioRead('my-account');
$fioPay = $fioFactory->createFioPay('wife-account');

$fioRead2 = $fioFactory->createFioRead(); // first in list is default, [my-account]
```

You can use different config bud keep structure of php array
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

#### Read range between date.

```php
use h4kuna\Fio;
/* @var $fioRead Fio\FioRead */
/* @var $list Fio\Read\TransactionList */
$list = $fioRead->movements(/* $from, $to */); // default is last week

foreach ($list as $transaction) {
    /* @var $transaction Fio\Read\Transaction */
    var_dump($transaction->moveId);
    foreach ($transaction as $property => $value) {
        var_dump($property, $value);
    }
}

var_dump($list->getInfo());
```

#### You can download transaction by id of year.

```php
use h4kuna\Fio;
/* @var $fioRead Fio\FioRead */
/* @var $list Fio\Read\TransactionList */
$list = $fioRead->movementId(2, 2015); // second transaction of year 2015
```

#### Very useful method where download last transactions.
After download it automatic set new break point.

```php
use h4kuna\Fio;
/* @var $fioRead Fio\FioRead */
/* @var $list Fio\Read\TransactionList */
$list = $fioRead->lastDownload();
// same use like above
var_dump($list->getInfo()->idLastDownload);
```

#### Change your break point.
By date.
```php
$fioRead->setLastDate('1986-12-30');
$list = $fioRead->lastDownload();
var_dump($list->getInfo()->idLastDownload);
```

By movement ID.
```php
$fioRead->setLastId(123456789);
$list = $fioRead->lastDownload();
var_dump($list->getInfo()->idLastDownload); // 123456789
```

> Tip: You can define own TransactionFactory and create instance and add to Read\Json::__construct()

## Payment (writing)

Api has three response languages, default is set **cs**. For change:
```php
/* @var $fioPay h4kuna\Fio\FioPay */
$fioPay->setLanguage('en');
```

For send request is method send whose accept, file path to your xml or abo file or instance of class Property.
```php
$myFile = '/path/to/my/xml/or/abo/file.xml'; // file extension is important
$fioPay->send($myFile);
```

Object pay only to czech or slovak:

```php
/* @var $national Fio\Pay\Payment\National */
$national = $fioPay->createNational($amount, $accountTo);
$national->setVariableSymbol($vs);
/* set next payment property $national->set* */
$fioPay->send();
```

Euro zone payment:

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
$international = $fioPay->createInternational($amount, $accountTo, $bic, $name, $street, $city, $country, $info);
$international->setRemittanceInfo2('foo');
/* set next payment property $international->set* */
$fioPay->send();
```

Send more payments in one request:

```php
foreach($pamentsRows as $row) {
	/* @var $national Fio\Pay\Payment\National */
	$national = $fioPay->createNational($row->amount, $row->accountTo);
	$national->setVariableSymbol($row->vs);
}
$fioPay->send();
```
