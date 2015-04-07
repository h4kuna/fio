<?php

namespace h4kuna\Fio\Test;

use Kdyby\Curl\Request;
use h4kuna\Fio\Request\IQueue;
use h4kuna\Fio\Request\Read\IFile;

/**
 * @author Milan Matějček
 */
class Queue implements IQueue
{

    public function download($token, $url)
    {
        $path = __DIR__ . '/' . basename($url);
        if (substr($url, -3) === IFile::GPC) {
            return 'todo download';
        } elseif (strstr($url, 'set-last-id')) {
            return 'todo download';
        } elseif (strstr($url, 'set-last-date')) {
            return 'todo download';
        }

        return file_get_contents($path);
    }

    public function upload($token, Request $curl)
    {
        return new \h4kuna\Fio\Response\Pay\BadResponse($curl);
    }

}
