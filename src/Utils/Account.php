<?php

namespace h4kuna\Fio\Utils;

/**
 * @author Milan Matějček
 */
class Account
{

    /** @var int */
    private $account;

    /** @var int */
    private $bankCode;

    public function __construct($account, $bankCode = NULL)
    {
        if (!preg_match('~^(\d+)(?: ?/ ?(\d+))?$~', $account, $find)) {
            throw new FioException('Accoun and bank code does not equal.');
        }

        $this->account = $find[1];

        if (strlen($this->account) > 16) {
            throw new FioException('Account max length is 16 chars.');
        }

        if (isset($find[2])) {
            $this->bankCode = $find[2];
        } elseif ($bankCode) {
            $this->bankCode = $bankCode;
        }


        if ($this->bankCode !== NULL && $bankCode && $this->bankCode != $bankCode) {
            throw new FioException('You set bank code (' . $bankCode . ') bud this account (' . $account . ') has too bank code and these are not equals.');
        }
    }

    public function getAccount()
    {
        return $this->account;
    }

    public function getBankCode()
    {
        return $this->bankCode;
    }

    public function __toString()
    {
        return (string) $this->getAccount();
    }

}
