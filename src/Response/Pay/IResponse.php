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

    /** @return string */
    public function getError();

    /** @return string */
    public function getErrorCode();
}
