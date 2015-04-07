<?php

namespace h4kuna\Fio\Utils;


use Nette\Utils\DateTime;

/**
 * @author Milan Matějček
 */
final class String
{

    private function __construct()
    {
        
    }

    /**
     *
     * @param string $str
     * @param int $limit
     * @return string|NULL
     */
    public static function substr($str, $limit)
    {
        return $str ? mb_substr($str, 0, $limit) : NULL; // max length from API
    }

    public static function createAccount($account, $bankCode = NULL)
    {
        return new Account($account, $bankCode);
    }

    /**
     * @param 
     */
    public static function date($date)
    {
        return DateTime::from($date)->format('Y-m-d');
    }

}
