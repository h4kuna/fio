<?php

namespace h4kuna\Fio\Request\Read;

use h4kuna\Fio\Response\Read;

/**
 *
 * @author Milan Matějček
 */
interface IReaderFactory {

    /** supported */
    const JSON = 'json';

    /** not supported */
    const
        XML = 'xml',
        OFX = 'ofx',
        HTML = 'html',
        STA = 'sta',
        GPC = 'gpc',
        CSV = 'csv';

    public function __construct(Read\ITransactionListFactory $statement);

    /**
     * File extension.
     * @return string
     */
    public function getExtension();

    /**
     * Prepare downloaded data before append.
     *
     * @param string $data
     * @return Read\TransactionList
     */
    public function create($data);
}
