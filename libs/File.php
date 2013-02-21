<?php

namespace h4kuna\fio\libs;

use \Nette\Object;

require_once 'IFile.php';

abstract class File extends Object implements IFile, \Iterator {

    /** @var array */
    protected static $dataKeys = array('moveId', 'moveDate', 'amount', 'currency',
        'toAccount', 'toAccountName', 'bankCode', 'bankName', 'constantSymbol',
        'variableSymbol', 'specificSymbol', 'userNote', 'message', 'type',
        'performed', 'specification', 'comment', 'bic', 'instructionId');

    /** @var array */
    protected static $headerKeys = array('accountId', 'bankId', 'currency',
        'iban', 'bic', 'openingBalance', 'closingBalance', 'dateStart',
        'dateEnd', 'idFrom', 'idTo');

    /** @var array */
    private $header = array();
    private $data = array();

    /** @var array */
    private $headerKeyMap = array();

    /** @var array */
    private $dataKeyMap = array();

    public function __construct($headerKeyMap = array(), $dataKeyMap = array()) {
        if ($headerKeyMap) {
            if (key($headerKeyMap) === 0) {
                $this->headerKeyMap = array_combine(self::$headerKeys, $headerKeyMap);
            } else {
                $this->headerKeyMap = $headerKeyMap;
            }
        }

        if ($dataKeyMap) {
            if (key($dataKeyMap) === 0) {
                $this->dataKeyMap = array_combine(self::$dataKeys, $dataKeyMap);
            } else {
                $this->dataKeyMap = $dataKeyMap;
            }
        }
    }

    protected function setHeader(array $data) {
        if ($this->headerKeyMap) {
            $map = $this->headerKeyMap;
        } else {
            $map = array_combine(self::$headerKeys, self::$headerKeys);
        }

        foreach ($data as $k => $v) {
            if (isset($map[$k])) {
                $this->header[$map[$k]] = $this->prepareHeader($k, $v);
            }
        }

        return $this;
    }

    protected function prepareHeader($key, $value) {
        switch ($key) {
            case 'openingBalance':
            case 'closingBalance':
                $value = new \h4kuna\Float($value);
                break;
            case 'dateStart':
            case 'dateEnd':
                $value = $this->createDateTime($value);
                break;
        }
        return $value;
    }

    protected function prepareData(array & $data) {
        $data['moveDate'] = $this->createDateTime($data['moveDate']);
        $data['amount'] = new \h4kuna\Float($data['amount']);
        $data['comment'] = trim($data['comment']);
        $data['userNote'] = trim($data['userNote']);
    }

    /**
     * @return array
     */
    public function getHeader() {
        return $this->header;
    }

    /**
     * @return array
     */
    public function getData() {
        return $this->data;
    }

    final protected function createDateTime($value, $midnight = TRUE) {
        $dt = date_create_from_format($this->getDateFormat(), $value);
        if ($midnight) {
            $dt->setTime(0, 0, 0);
        }
        return $dt;
    }

    final protected function append(array $data) {
        $this->prepareData($data);
        $this->data[] = $data;
        return $this;
    }

//----------------- implements iterator ----------------------------------------
    public function current() {
        return current($this->data);
    }

    public function next() {
        return next($this->data);
    }

    public function key() {
        return key($this->data);
    }

    public function valid() {
        return array_key_exists($this->key(), $this->data);
    }

    public function rewind() {
        reset($this->data);
        return $this;
    }

// Pohyby na účtu za určené období
// https://www.fio.cz/ib_api/rest/periods/aGEMQB9Idh35fh1g51h3ekkQwyGlQ/2012-08-25/2012-08-31/transactions.xml
// Oficiální výpisy pohybů z účtu
// https://www.fio.cz/ib_api/rest/by-id/aGEMtmwcsg5EbfIjqIhunibjhuvfdtsersxexdtgMR9Idh6u3/2012/1/transactions.xml
// Pohyby na účtu od posledního stažení
// https://www.fio.cz/ib_api/rest/last/aGEMtmwcsWAjPzhg3bPH3j7Iu15g56d66AdEbfIjqIgMR9Idh6u3/transactions.xml
// Na ID posledního úspěšně staženého pohybu
// https://www.fio.cz/ib_api/rest/set-last-id/Pu5CMBu5nYBtWAk4gsj0FaUlY7JIjUnYBthKaquSWf1eUl/1147608196/
// Na datum posledního neúspěšně staženého pohybu
// https://www.fio.cz/ib_api/rest/set-last-date/Pu5CMBu5nYBthKaqM0FaUlY7JIjUnY0FaUlY7JIjU1eUl/2012-07-27/
    public function __toString() {
        return $this->getExtension();
    }

}
