<?php

namespace h4kuna\Fio\Test;

use h4kuna\Fio\Response\Read\Info;
use h4kuna\Fio\Response\Read\IStatementFactory;

/**
 *
 * @author Milan Matějček
 */
class StatementFactory implements IStatementFactory
{

    public function createInfo()
    {
        return new Info();
    }

    public function createTransaction()
    {
        return new \h4kuna\Fio\Response\Read\Transaction;
    }

}
