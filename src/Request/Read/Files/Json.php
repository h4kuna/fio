<?php

namespace h4kuna\Fio\Request\Read\Files;

use h4kuna\Fio\Request\Read\File;
use Nette\Utils;

/**
 * @author Milan Matejcek
 */
class Json extends File
{

    /**
     *
     * @return string
     */
    public function getExtension()
    {
        return self::JSON;
    }

    public function parse($data)
    {
        $json = Utils\Json::decode($data);
//        $json->accountStatement->info
        return Utils\ArrayHash::from($json);
    }

    /**
     *
     * @return string
     */
    public function getDateFormat()
    {
        return 'Y-m-dO';
    }

}
