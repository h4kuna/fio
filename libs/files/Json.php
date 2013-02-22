<?php

namespace h4kuna\fio\libs\files;

use h4kuna\fio\libs\File;

/**
 * Description of Csv
 *
 * @author h4kuna
 */
class Json extends File {

    public function getExtension() {
        return self::JSON;
    }

    public function parse($data) {
        $json = json_decode($data);
        $this->setHeader((array) $json->accountStatement->info);
        static $mapper = array(22, 0, 1, 14, 2, 10, 3, 12, 4, 5, 6, 7, 16, 8, 9, 18, 25, 26, 17);
        $combine = array_combine($mapper, self::$dataKeys);

        foreach ($json->accountStatement->transactionList->transaction as $row) {
            $out = array();
            foreach ($row as $column) {
                if ($column) {
                    $out[$combine[$column->id]] = $column->value;
                }
            }
            $this->append($out);
        }
        return $this;
    }

    public function getDateFormat() {
        return 'Y-m-dO';
    }

    public function getDataKeys() {
        return array('moveId', 'moveDate', 'amount', 'currency',
            'toAccount', 'toAccountName', 'bankCode', 'bankName', 'constantSymbol',
            'variableSymbol', 'specificSymbol', 'userNote', 'message', 'type',
            'performed', 'specification', 'comment', 'bic', 'instructionId',
        );
    }

    public function getHeaderKeys() {
        return array('accountId', 'bankId', 'currency',
            'iban', 'bic', 'openingBalance', 'closingBalance', 'dateStart',
            'dateEnd', 'idFrom', 'idTo', 'yearList', 'idList', 'idLastDownload');
    }

}