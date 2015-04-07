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


    public function __construct($container)
    {
        $this->container = $container;
    }

    protected function setUp()
    {
        $this->fioRead = $this->container->createService('fioExtension.fioRead');
    }

    public function testMovements()
    {

        $data = $this->fioRead->movements(1371081600, new \DateTime('2013-12-31'));

        dd($data);
        Assert::equals(Context::REST_URL . 'periods/' . $this->token . '/2013-06-13/2013-12-31/transactions.json', $this->object->getRequestUrl());

        Assert::equals('h4kuna\Fio\Response\Read\TransactionList', get_class($data));
    }

    public function testMovementId()
    {
        $year = 2015;
        $moveId = 321654;
        $this->object->movementId($moveId, $year);
        Assert::equals(Context::REST_URL . 'by-id/' . $this->token . "/{$year}/{$moveId}/transactions.json", $this->object->getRequestUrl());
    }

    public function testLastDownload()
    {
        $this->object->lastDownload();
        Assert::equals(Context::REST_URL . 'last/' . $this->token . '/transactions.json', $this->object->getRequestUrl());
    }

    public function testSetLastId()
    {
        $moveId = 321654;
        $this->object->setLastId($moveId);
        Assert::equals(Context::REST_URL . 'set-last-id/' . $this->token . "/{$moveId}/", $this->object->getRequestUrl());
    }

    public function testSetLastDate()
    {
        $this->object->setLastDate(1371081600);
        Assert::equals(Context::REST_URL . 'set-last-date/' . $this->token . '/2013-06-13/', $this->object->getRequestUrl());

        $this->object->setLastDate('2013-12-31');
        Assert::equals(Context::REST_URL . 'set-last-date/' . $this->token . '/2013-12-31/', $this->object->getRequestUrl());

        $this->object->setLastDate(new \DateTime('2013-12-31'));
        Assert::equals(Context::REST_URL . 'set-last-date/' . $this->token . '/2013-12-31/', $this->object->getRequestUrl());
    }

}

$test = new FioReadTest($container);
$test->run();
