Fio
=====
Support [Fio API](http://www.fio.sk/docs/cz/API_Bankovnictvi.pdf). Default read via json file.

Installation to project
-----------------------
The best way to install h4kuna/fio is using Composer:
```sh
$ composer require h4kuna/fio:
```

Example NEON config
-------------------
```
extensions:
    fioExtension: h4kuna\Fio\DI\FioExtension

fioExtension:
    # mandatory
    account: 2600267402/2010
    token: 5asd64as5d46ad5a6

    # optional
    temp: %tempDir%/fio
    transactionClass: \h4kuna\Fio\Response\Read\Transaction # if you need change name of property
```

How to use READ
---------------

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

### You can download transaction by id of year.
```php
use h4kuna\Fio;
/* @var $fioRead Fio\FioRead */
/* @var $list Fio\Response\Read\TransactionList */
$list = $fioRead->movementId(2, 2015); // second transaction of year 2015
```

### Change your break point.
By date.
```php
$fioRead->setLastDate('1986-12-30');
```

By movement ID.
```php
$fioRead->setLastId(123456789);
```
