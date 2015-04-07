<?php

namespace h4kuna\Fio\Response\Read;

use DateTime;


/**
 * @author Milan Matějček
 * 
 * @property-read DateTime $moveDate [0]
 * @property-read float $volume [1]
 * @property-read string $toAccount [2]
 * @property-read type $name [3]
 * @property-read type $name [4]
 * @property-read type $name [5]
 * @property-read type $name [6]
 * @property-read type $name [7]
 * @property-read type $name [8]
 * @property-read type $name [9]
 * @property-read type $name [10]
 * @property-read type $name [11]
 * @property-read type $name [12]
 * @property-read type $name [13]
 * @property-read string $currency Description [14]
 * @property-read type $name [15]
 * @property-read type $name [16]
 * @property-read type $name [17]
 * @property-read type $name [18]
 * @property-read type $name [19]
 * @property-read int $moveId [20]
 * @property-read type $name [21]
 * @property-read type $name [22]
 * 
 */
final class Transaction extends ATransaction
{

    /** @var int @id 20 */
    public $moveId;

    /** @var DateTime @id 21 */
    public $moveDate;
    public $amount;

    /** @var string @id 14 */
    public $currency;
    public $toAccount;
    public $toAccountName;
    public $bankCode;
    public $bankName;
    public $constantSymbol;
    public $variableSymbol;
    public $specificSymbol;
    public $userNote;
    public $message;
    public $type;
    public $performed;
    public $specification;
    public $comment;
    public $bic;
    public $instructionId;

    public function __toString()
    {
        return (string) $this->moveId;
    }

}
