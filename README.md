Fio
=====
[![Build Status](https://travis-ci.org/h4kuna/fio.svg?branch=master)](https://travis-ci.org/h4kuna/fio)

Support [Fio API](http://www.fio.sk/docs/cz/API_Bankovnictvi.pdf). Default read via json file.

Installation to project
-----------------------
The best way to install h4kuna/fio is using Composer:
```sh
$ composer require h4kuna/fio
```

Example NEON config
-------------------
```
extensions:
    fioExtension: h4kuna\Fio\DI\FioExtension

fioExtension:
    # mandatory
	accounts:
		alias: # name for select account
			account: 2600267402/2010
			token: 5asd64as5d46ad5a6

    # optional
    temp: %tempDir%/fio
    transactionClass: \h4kuna\Fio\Response\Read\Transaction # if you need change name of property
```

How to use
---------------
Reading
=======
### Read range between date.

```php
use h4kuna\Fio;
/* @var $fioRead Fio\FioRead */
/* @var $list Fio\Response\Read\TransactionList */
$list = $fioRead->movements(/* $from, $to */); // default is last week

foreach ($list as $transaction) {
    /* @var $transaction Fio\Response\Read\Transaction */
    dump($transaction->moveId);
    foreach ($transaction as $property => $value) {
        dump($property, $value);
    }
}

dump($list->getInfo());
```

### You can download transaction by id of year.
```php
use h4kuna\Fio;
/* @var $fioRead Fio\FioRead */
/* @var $list Fio\Response\Read\TransactionList */
$list = $fioRead->movementId(2, 2015); // second transaction of year 2015
```

### Very useful method where download last transactions.
After download it automaticaly set new break point.
```php
use h4kuna\Fio;
/* @var $fioRead Fio\FioRead */
/* @var $list Fio\Response\Read\TransactionList */
$list = $fioRead->lastDownload();
// same use like above
dump($list->getInfo()->idLastDownload);
```

### Change your break point.
By date.
```php
$fioRead->setLastDate('1986-12-30');
$list = $fioRead->lastDownload();
dump($list->getInfo()->idLastDownload);
```

By movement ID.
```php
$fioRead->setLastId(123456789);
$list = $fioRead->lastDownload();
dump($list->getInfo()->idLastDownload); // 123456789
```

### Custom Transaction class
By default is h4kuna\Fio\Response\Read\Transaction if you want other names for properties. You can set by Neon.
```sh
fioExtension:
    transactionClass: \MyTransaction
```

Define annotation and you don't forget id in brackets.
```php
<?php

use h4kuna\Fio\Response\Read\TransactionAbstract

/**
 * @property-read float $amount [1]
 * @property-read string $to_account [2]
 * @property-read string $bank_code [3]
 */
class MyTransaction extends TransactionAbstract
{
	/** custom method */
	public function setBank_code($value)
	{
		return str_pad($value, 4, '0', STR_PAD_LEFT);
	}
}
```


Payment (writing)
=============
Api has three response languages, default is set **cs**. For change:
```php
/* @var $fioPay h4kuna\Fio\FioPay */
$fioPay->setLanguage('en');
```

For send request is method send whose accept, file path to your xml or abo file or instance of class Property.
```php
$myFile = '/path/to/my/xml/or/abo/file'
$fioPay->send($myFile);
```

Object pay only to czech or slovak:
```php
/* @var $national h4kuna\Fio\Request\Pay\Payment\National */
$national = $fioPay->createNational($amount, $accountTo);
$national->setVariableSymbol($vs);
/* set next payment property $national->set* */
$fioPay->send($national);
```

Euro zone payment:
```php
/* @var $euro h4kuna\Fio\Request\Pay\Payment\Euro */
$euro = $fioPay->createEuro($amount, $accountTo, $bic, $name, $country);
$euro->setVariableSymbol($vs);
/* set next payment property $euro->set* */
$fioPay->send($euro);
```

International payment:
```php
/* @var $international h4kuna\Fio\Request\Pay\Payment\International */
$international = $fioPay->createInternational($amount, $accountTo, $bic, $name, $street, $city, $country, $info);
$international->setRemittanceInfo2('foo');
/* set next payment property $international->set* */
$fioPay->send($international);
```

Send more payments in one request:
```php
foreach($pamentsRows as $row) {
	/* @var $national h4kuna\Fio\Request\Pay\Payment\National */
	$national = $fioPay->createNational($row->amount, $row->accountTo);
	$national->setVariableSymbol($row->vs);
	$fioPay->addPayment($national);
}
$fioPay->send();
```
