<?php

namespace h4kuna\Fio\Request;

use Kdyby\Curl\Request;

/**
 *
 * @author Milan Matějček
 */
interface IQueue
{

    /** @var int [s] */
    const API_INTERVAL = 30;

    /**
     * @param string $url
     * @return string
     */
    public function download($token, $url);

    /**
     * @param CUrl $curl
     * @return XMLResponse
     */
    public function upload($token, Request $curl);
}
