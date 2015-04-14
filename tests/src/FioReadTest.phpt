<?php

namespace h4kuna\Fio;

use h4kuna\Fio\Utils\Context,
    Tester,
    Tester\Assert;

$container = require_once __DIR__ . '/../bootstrap.php';

class FioReadTest extends Tester\TestCase
{

    /** @var FioRead */
    private $fioRead;
    private $container;
    private $token;

    public function __construct($container)
    {
        $this->container = $container;
        $this->token = $container->getByType('h4kuna\Fio\Utils\Context')->getToken();
    }

    protected function setUp()
    {
        $this->fioRead = $this->container->createService('fioExtension.fioRead');
    }

    public function testMovements()
    {
        $data = $this->fioRead->movements(1420070400, '2015-04-16');
        $moveId = 7139752765;
        foreach ($data as $transaction) {
            /* @var $transaction Response\Read\Transaction */
            Assert::equal($moveId, $transaction->moveId);
            foreach ($transaction as $property => $value) {
                if ($property === 'moveId') {
                    Assert::equal($moveId, $value);
                    break 2;
                }
            }
        }

        Assert::equal(Context::REST_URL . 'periods/' . $this->token . '/2015-01-01/2015-04-16/transactions.json', $this->fioRead->getRequestUrl());
        Assert::equal(self::getContent('2015-01-01-2015-04-16-transactions.srlz'), serialize($data));
    }

    public function testMovementsEmpty()
    {
        $data = $this->fioRead->movements('2011-01-01', '2011-01-02');
        Assert::equal(self::getContent('2011-01-01-2011-01-02-transactions.srlz'), serialize($data));
    }

    public function testMovementId()
    {
        $data = $this->fioRead->movementId(2, 2015);
        Assert::equal(self::getContent('2015-2-transactions.srlz'), serialize($data));
        Assert::equal(Context::REST_URL . 'by-id/' . $this->token . '/2015/2/transactions.json', $this->fioRead->getRequestUrl());
    }

    public function testLastDownload()
    {
        $data = $this->fioRead->lastDownload();
        Assert::equal(self::getContent('last-transactions.srlz'), serialize($data));
        Assert::equal(Context::REST_URL . 'last/' . $this->token . '/transactions.json', $this->fioRead->getRequestUrl());
    }

    public function testSetLastId()
    {
        $this->fioRead->setLastId(7155451447);
        Assert::equal(Context::REST_URL . 'set-last-id/' . $this->token . "/7155451447/", $this->fioRead->getRequestUrl());
    }

    public function testSetLastDate()
    {
        $dt = new \DateTime('-1 week');
        $this->fioRead->setLastDate('-1 week');
        Assert::equal(Context::REST_URL . 'set-last-date/' . $this->token . '/' . $dt->format('Y-m-d') . '/', $this->fioRead->getRequestUrl());
    }

    private static function getPathData($file)
    {
        return __DIR__ . '/../data/tests/' . $file;
    }

    private static function getContent($file)
    {
        return file_get_contents(self::getPathData($file));
    }

}

$test = new FioReadTest($container);
$test->run();
