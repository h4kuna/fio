# Fio

### v 3.0
- PHP 8.0+ let's use version 3.0+
- PSR-7, PRS-17, PSR-18
- FioRead and FioPay keep API
- remove log, use the logging provided by the library for request / response
- xml file does not save to file system, only to memory, if you need content:
```php
/** @var h4kuna\Fio\FioPay $fioPay  */    
$fioPay->createEuro();
$fioPay->createEuro();

$xml = $fioPay->getXml();

$fioPay->send($xml);
```
- created payments are automatic added to xml
```php
/** @var h4kuna\Fio\FioPay $fioPay  */  
// old behavior
$fioPay->addPayment($fioPay->createEuro());
$fioPay->addPayment($fioPay->createEuro());
$fioPay->send();

// new only
$fioPay->createEuro();
$fioPay->createEuro();
$fioPay->send();
```
- variable, constant and specific symbols are type string, integer is deprecated 

### v 2.0
- support only php 7.1+
- add type hint for methods and parameters
- add to files declare(strict_types=1)
- change interface API `h4kuna\Fio\Response\Pay\IResponse` remove prefix `get` 
- add `Log` for request pay xml 

### v 1.0
- PHP 5.5 - 7.0 let's use stable [version 1.3.5](https://github.com/h4kuna/fio/releases/tag/v1.3.5).
- PHP 5.3 - 5.4 let's use stable [version 1.2.1](https://github.com/h4kuna/fio/releases/tag/v1.2.1).
