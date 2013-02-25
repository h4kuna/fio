Fio
=====
Support [Fio API](http://www.fio.sk/docs/cz/API_Bankovnictvi.pdf). Default read via csv file.

For dependency look at to composer.json
- h4kuna/curl
- h4kuna/iterators
- h4kuna/data-type

```php
$fio = new Fio($token);

foreach($fio->movements() as $data) {
    var_dump($data);// save to db
}
```
Look at to public method of class **\h4kuna\fio\Fio**.