<?php

namespace h4kuna\Fio\Response\Pay;

/**
 *
 * @author Milan Matějček
 */
interface IResponse
{

    /** @return bool */
    public function isOk();

    /** @return mixed */
    public function getError();

    /** @return int */
    public function getErrorCode();
}
