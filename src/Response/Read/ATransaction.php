<?php

namespace h4kuna\Fio\Response\Read;

use h4kuna\Fio\Utils\FioException;
use ReflectionClass;

/**
 * @author Milan Matějček
 */
abstract class ATransaction
{

    /** @var string[] */
    private static $property;

    /** @var array */
    private $properties = array();

    public function __construct()
    {
        $this->loadProperties();
    }

    public function __get($name)
    {
        if (isset($this->properties[$name])) {
            return $this->properties[$name];
        }
        return NULL;
    }

    public function setProperty($id, $value)
    {
        $key = isset(self::$property[$id]) ? self::$property[$id] : $id;
        $this->properties[$key] = $value;
    }

    private function loadProperties()
    {
        if (self::$property !== NULL) {
            return;
        }
        $reflection = new ReflectionClass($this);
        if (!preg_match_all('/@property-read (?P<type>\w+) \$(?P<name>\w+).*\[(?P<id>\d+)\]/', $reflection->getDocComment(), $find)) {
            throw new FioException('Property not found you have bad syntax.');
        }

        self::$property = array();
        foreach ($find['name'] as $key => $property) {
            self::$property[$find['id'][$key]] = $property;
        }
    }

}
