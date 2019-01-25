# Fio

### v 2.0
- support only php 7.1+
- exceptions move and rename from `h4kuna\Fio\<name>Exception` to `h4kuna\Fio\Exceptions\<name>`
- add type hint for methods and parameters
- add to files declare(strict_types=1)
- change interface API `h4kuna\Fio\Response\Pay\IResponse` remove prefix `get` 
- add `Log` for request pay xml 