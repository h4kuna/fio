<?php

namespace h4kuna\Fio\Test;

use h4kuna\Fio;

/**
 * @author Milan Matějček
 */
class Queue implements Fio\Request\IQueue
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
            return Fio\FioReadTest::getContent($file);
        }
        return $file;
    }

    public function upload($url, array $post, $filename)
    {

        dd($url);
    }

}
