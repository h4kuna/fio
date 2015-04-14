<?php

namespace h4kuna\Fio\Test;

use h4kuna\Fio\Request\IQueue,
    h4kuna\Fio\Response\Pay\BadResponse,
    Kdyby\Curl\Request;

/**
 * @author Milan Matějček
 */
class Queue implements IQueue
{

    public function download($token, $url)
    {
        $file = '';
        switch (basename($url, 'json')) {
            case 'transactions.':
                preg_match('~((?:/[^/]+){3})$~U', $url, $find);
                $file = str_replace(array('/', '-' . $token), array('-', ''), ltrim($find[1], '/'));
                break;
        }
        if ($file) {
            return file_get_contents(__DIR__ . '/tests/' . $file);
        }
        return $file;
    }

    public function upload($token, Request $curl)
    {
        return new BadResponse($curl);
    }

}
