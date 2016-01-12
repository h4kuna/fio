<?php

namespace h4kuna\Fio\Response\Read;

use DateTime;

/**
 * Columns 11, 13, 15, 19, 20, 21, 23 and 24 does not exists
 * @todo advancedInformation and bic are not tested
 * @author Milan Matějček
 *
 * @property-read DateTime $moveDate [0]
 * @property-read float $volume [1]
 * @property-read string $toAccount [2]
 * @property-read string $bankCode [3]
 * @property-read string $constantSymbol [4]
 * @property-read string $variableSymbol [5]
 * @property-read string $specificSymbol [6]
 * @property-read string|NULL $note [7]
 * @property-read string $type [8]
 * @property-read string $whoDone [9]
 * @property-read string $nameAccountTo [10]
 * @property-read string $bankName [12]
 * @property-read string $currency [14]
 * @property-read string|NULL $messageTo [16]
 * @property-read int $instructionId [17]
 * @property-read string $advancedInformation [*18]
 * @property-read int $moveId [22]
 * @property-read string|NULL $comment [25]
 * @property-read string $bic [*26]
 *
 */
final class Transaction extends TransactionAbstract
{

}
