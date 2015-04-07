<?php

namespace h4kuna\Fio\Request\Read;

use DateTime;
use Nette\Object;

abstract class File extends Object implements IFile
{

    /**
     * Same format for all files
     *
     * @param string|int $key
     * @param mixed $value
     * @return mixed
     */
    protected function prepareHeader($key, $value)
    {
        switch ($key) {
            case 'openingBalance':
            case 'closingBalance':
                $value = floatval($value);
                break;
            case 'dateStart':
            case 'dateEnd':
                $value = $this->createDateTime($value);
                break;
        }
        return $value;
    }

    /**
     * Same format for all files
     *
     * @param string|int $key
     * @param mixed $value
     * @return mixed
     */
    protected function prepareData($key, $value)
    {
        switch ($key) {
            case 'amount':
                $value = floatval($value);
                break;
            case 'comment':
            case 'userNote':
                $value = trim($value);
                break;
            case 'moveDate':
            case 'dueDate':
                $value = $this->createDateTime($value);
                break;
        }
        return $value;
    }

    /**
     * Convert string to DateTime
     *
     * @param string $value
     * @param bool $midnight
     * @return DateTime
     */
    final protected function createDateTime($value, $midnight = TRUE)
    {
        $dt = date_create_from_format($this->getDateFormat(), $value);
        if ($midnight) {
            $dt->setTime(0, 0, 0);
        }
        return $dt;
    }

    protected function dataKeys()
    {
        return array('moveId', 'moveDate', 'amount', 'currency',
            'toAccount', 'toAccountName', 'bankCode', 'bankName', 'constantSymbol',
            'variableSymbol', 'specificSymbol', 'userNote', 'message', 'type',
            'performed', 'specification', 'comment', 'bic', 'instructionId',
        );
    }

    protected function headerKeys()
    {
        return array('accountId', 'bankId', 'currency',
            'iban', 'bic', 'openingBalance', 'closingBalance', 'dateStart',
            'dateEnd', 'idFrom', 'idTo');
    }

    /**
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getExtension();
    }

}
