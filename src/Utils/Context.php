<?php

namespace h4kuna\Fio\Utils;

use h4kuna\Fio\Request\IQueue;
use Nette\Object;

class Context extends Object
{

    /** @var string url Fio REST API */
    const REST_URL = 'https://www.fio.cz/ib_api/rest/';

    /** @var IQueue */
    private $queue;

    /** @var string */
    private $token;

    public function __construct($token, IQueue $queue)
    {
        $this->token = $token;
        $this->queue = $queue;
    }

    /** @return string */
    public function getToken()
    {
        return $this->token;
    }

    /** @return IQueue */
    public function getQueue()
    {
        return $this->queue;
    }

    /** @return string */
    public function getUrl()
    {
        return self::REST_URL;
    }

}
