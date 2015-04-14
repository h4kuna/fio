<?php

namespace h4kuna\Fio\Request\Read\Files;

use h4kuna\Fio\Request\Read\IParser,
    h4kuna\Fio\Response\Read\IStatementFactory,
    Nette\Object,
    Nette\Utils;

/**
 * @author Milan Matejcek
 */
class Json extends Object implements IParser
{

    /** @var IStatementFactory */
    private $statementFactory;

    public function __construct(IStatementFactory $statementFactory)
    {
        $this->statementFactory = $statementFactory;
    }

    /**
     *
     * @return string
     */
    public function getExtension()
    {
        return self::JSON;
    }

    /**
     *
     * @param type $data
     * @return \h4kuna\Fio\Response\Read\TransactionList
     */
    public function parse($data)
    {
        if (!$data) {
            $data = '{}';
        }

        $dateFormat = 'Y-m-dO';
        $json = Utils\Json::decode($data);
        if (isset($json->accountStatement->info)) {
            $info = $this->statementFactory->createInfo($json->accountStatement->info, $dateFormat);
        } else {
            $info = new \stdClass();
        }
        $transactionList = $this->statementFactory->createTransactionList($info);
        if (!isset($json->accountStatement->transactionList)) {
            return $transactionList;
        }
        foreach ($json->accountStatement->transactionList->transaction as $transactionData) {
            $transactionList->append($this->statementFactory->createTransaction($transactionData, $dateFormat));
        }
        return $transactionList;
    }

}
