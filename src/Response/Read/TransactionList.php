<?php

namespace h4kuna\Fio\Response\Read;

use Countable;
use h4kuna\Fio\Response\Read\ATransaction;
use h4kuna\Fio\Response\Read\Info;
use Iterator;

/**
 * @author Milan Matějček
 */
final class TransactionList implements Iterator, Countable
{

    private $data;

    /** @var Info */
    private $info;

    public function __construct(Info $info)
    {
        $this->info = $info;
    }

    public function append(ATransaction $transaction)
    {
        $this->data[] = $transaction;
    }

    /** @return Info */
    public function getInfo()
    {
        return $this->info;
    }

    /** @return Transaction */
    public function current()
    {
        return current($this->data);
    }

    public function key()
    {
        return $this->current()->getName();
    }

    public function next()
    {
        next($this->data);
    }

    public function rewind()
    {
        reset($this->data);
    }

    public function valid()
    {
        return array_key_exists($this->key(), $this->data);
    }

    public function count()
    {
        return count($this->data);
    }

}
