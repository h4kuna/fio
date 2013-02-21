<?php

namespace h4kuna\fio\libs\files;

use h4kuna\fio\libs\File;

/**
 * Description of Csv
 *
 * @author h4kuna
 */
class Gpc extends File {

    const REPORT = '074';
    const ITEM = '075';

    public function getExtension() {
        return self::GPC;
    }

    public function parse($data) {
        foreach (new \h4kuna\TextIterator($data) as $line) {
            pd($this->parseLine($line));

            pd($line);
        }
        exit;
    }

    public function getDateFormat() {
        return 'dmy';
    }

    private function parseLine($data) {
        $type = mb_substr($data, 0, 3);
        if ($type == self::REPORT) {
            pd($this->report($data));
            return $this->setHeader($this->report($data));
        }
        return $this->item($data);
    }

    private function report($data) {
        return array(
            'accountNumber' => $this->ltrim(mb_substr($data, 3, 16)),
            'accountName' => rtrim(mb_substr($data, 19, 20)),
            'oldBalanceDate' => $this->createDateTime(mb_substr($data, 39, 6)),
            'oldBalanceValue' => $this->floatVal(mb_substr($data, 45, 14), mb_substr($data, 59, 1)),
            'newBalanceValue' => $this->floatVal(mb_substr($data, 60, 14), mb_substr($data, 74, 1)),
            'debitValue' => $this->floatVal(mb_substr($data, 75, 14), mb_substr($data, 89, 1)),
            'creditValue' => $this->floatVal(mb_substr($data, 90, 14), mb_substr($data, 104, 1)),
            'sequenceNumber' => intval(mb_substr($data, 105, 3)),
            'date' => $this->createDateTime(mb_substr($data, 108, 6)),
            'note' => substr($data, 116, 12)
        );
    }

    private function item($data) {
        return array(
            'accountNumber' => $this->ltrim(mb_substr($data, 3, 16)),
            'offsetAccount' => $this->ltrim(rtrim(mb_substr($data, 19, 16))),
            'recordNumber' => $this->ltrim(mb_substr($data, 35, 13)),
            'value' => $this->floatVal(mb_substr($data, 48, 12)),
            'code' => mb_substr($data, 60, 1),
            'variableSymbol' => intval((mb_substr($data, 61, 10))),
            'bankCode' => mb_substr($data, 73, 4), //zkontrolovat
            'constantSymbol' => $this->ltrim(mb_substr($data, 77, 4)),
            'specificSymbol' => intval(mb_substr($data, 81, 10)),
            'valut' => mb_substr($data, 91, 6),
            'clientName' => rtrim(mb_substr($data, 97, 20)),
            //'zero' => mb_substr($data, 117, 1),
            'currencyCode' => mb_substr($data, 118, 4),
            'dueDate' => $this->createDateTime(mb_substr($data, 122, 6)),
        );
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

}
