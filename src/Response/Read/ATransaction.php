<?php

namespace h4kuna\Fio\Response\Read;

use h4kuna\Fio\Utils;

/**
 * @author Milan Matějček
 */
abstract class ATransaction implements \Iterator
{

    /** @var array */
    private $properties = array();

    public function __get($name)
    {
        if (array_key_exists($name, $this->properties)) {
            return $this->properties[$name];
        }
        throw new Utils\FioException('Property does not exists. ' . $name);
    }

    public function bindProperty($name, $type, $value, $dateFormat)
    {
        if ($value) {
            $value = $this->checkValue($value, $type, $dateFormat);
        }
        $this->properties[$name] = $value;
    }

    public function current()
    {
        return current($this->properties);
    }

    public function key()
    {
        return key($this->properties);
    }

    public function next()
    {
        next($this->properties);
    }

    public function rewind()
    {
        reset($this->properties);
    }

    public function valid()
    {
        return array_key_exists($this->key(), $this->properties);
    }

    protected function checkValue($value, $type, $dateFormat)
    {
        switch ($type) {
            case 'int':
                return intval($value);
            case 'datetime':
                return Utils\String::createFromFormat($value, $dateFormat);
            case 'float':
                return floatval($value);
            case 'string':
                return trim($value);
            case 'string|null':
                return trim($value) ? : NULL;
        }
        return $value;
    }

}
