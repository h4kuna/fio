<?php

namespace h4kuna\Fio\Response\Read;

/**
 * @author Milan Matějček
 */
interface IStatementFactory
{

    /** @return ATransaction */
    public function createTransaction();

    /** @return Info */
    public function createInfo();
}
