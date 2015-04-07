<?php

namespace h4kuna\Fio\Response\Read;

/**
 * @author Milan Matějče
 */
class StatementFactory implements IStatementFactory
{

    public function createInfo()
    {
        return new Info();
    }

    /** @return Transaction */
    public function createTransaction()
    {
        return new Transaction();
    }

}
