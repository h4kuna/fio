Fio
=====
Support [Fio API](http://www.fio.sk/docs/cz/API_Bankovnictvi.pdf). Default read via json file.

Installation to project
-----------------------
The best way to install h4kuna/fio is using Composer:
```sh
$ composer require h4kuna/fio:1.1.4
```

Example NEON config
-------------------
<pre>
extensions:
    fioExtension: h4kuna\Fio\DI\FioExtension

fioExtension:
    account: 2600267402/2010
    token: 5asd64as5d46ad5a6
</pre>

Run simple example
------------------
```sh
$ cd to/your/web/document/root
$ git clone git@github.com:h4kuna/fio.git
$ cd fio
$ chmod 777 tests/temp
$ composer install
$ echo '<?php return "YOUR SECURE TOKEN"' > tests/tmp/secureToken.php
```

Look at to example.php and read it, after you can open in browser and you can see how it is work.
