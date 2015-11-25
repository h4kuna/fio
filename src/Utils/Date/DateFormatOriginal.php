<?php

namespace h4kuna\Fio\Utils\Date;

class DateFormatOriginal
{

    /** @var DateFormat */
    private $list;

    /** @return DateFormat */
    public function get($format)
    {
        if (isset($this->list[$format])) {
            return $this->list[$format];
        }
        return $this->list[$format] = new DateFormat($format);
    }

}
