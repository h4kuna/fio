<?php

namespace h4kuna\Fio\Files;

use h4kuna\Fio\File;

/**
 * @author Milan Matejcek
 */
class Gpc extends File {

    const REPORT = '074';
    const ITEM = '075';

    public function getExtension() {
        return self::GPC;
    }

    public function parse($data) {
        foreach (new \h4kuna\TextIterator(iconv('windows-1250', 'UTF-8', $data)) as $line) {
            $type = mb_substr($line, 0, 3);
            if ($type == self::REPORT) {
                $this->setHeader($this->report($line));
            } else {
                $this->append($this->item($line));
            }
        }
        return $this;
    }

    public function getDateFormat() {
        return 'dmy';
    }

    private function item($data) {
        $values = array(
            $this->ltrim(mb_substr($data, 3, 16)),
            $this->ltrim(rtrim(mb_substr($data, 19, 16))),
            $this->ltrim(mb_substr($data, 35, 13)),
            $this->floatVal(mb_substr($data, 48, 12)),
            mb_substr($data, 60, 1),
            $this->ltrim(mb_substr($data, 61, 10)),
            mb_substr($data, 73, 4), //zkontrolovat
            mb_substr($data, 77, 4),
            $this->ltrim(mb_substr($data, 81, 10)),
            mb_substr($data, 91, 6),
            rtrim(mb_substr($data, 97, 20)),
            //'zero' => mb_substr($data, 117, 1),
            mb_substr($data, 118, 4),
            mb_substr($data, 122, 6),
        );
        return array_combine($this->getDataKeys(), $values);
    }

    private function report($data) {
        $values = array(
            $this->ltrim(mb_substr($data, 3, 16)),
            rtrim(mb_substr($data, 19, 20)),
            mb_substr($data, 39, 6),
            $this->floatVal(mb_substr($data, 45, 14), mb_substr($data, 59, 1)),
            $this->floatVal(mb_substr($data, 60, 14), mb_substr($data, 74, 1)),
            $this->floatVal(mb_substr($data, 75, 14), mb_substr($data, 89, 1)),
            $this->floatVal(mb_substr($data, 90, 14), mb_substr($data, 104, 1)),
            intval(mb_substr($data, 105, 3)),
            mb_substr($data, 108, 6),
            trim(substr($data, 116, 12))
        );
        return array_combine($this->getHeaderKeys(), $values);
    }

    private function floatVal($num, $mark = '+') {
        $v = intval($num);
        if ($mark == '-') {
            $v *= -1;
        }
        return $v / 100;
    }

    private function ltrim($s) {
        return ltrim($s, '0');
    }

    protected function getDataKeys() {
        return array('fromAccount', 'toAccount', 'moveId', 'amount', 'code',
            'variableSymbol', 'bankCode', 'constantSymbol', 'specificSymbol',
            'moveDate', 'toAccountName', 'currencyCode', 'dueDate'
        );
    }

    protected function getHeaderKeys() {
        return array('accountId', 'accountName', 'dateStart', 'openingBalance',
            'closingBalance', 'debitValue', 'creditValue', 'sequenceNumber',
            'dateEnd', 'note');
    }

}
