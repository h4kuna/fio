<?php

namespace h4kuna\Fio;

abstract class FioException extends \Exception {}

class AccountException extends FioException {}

class TransactionExtendException extends FioException {}

class TransactionPropertyException extends FioException {}

class QueueLimitException extends FioException {}

class FileUplodException extends FioException {}

class InvalidArgumentException extends FioException {}
