<?php
namespace StefanoNestedTransactionTest\Unit;

use StefanoNestedTransaction\TransactionManager;
use StefanoNestedTransactionTest\Unit\Dummy\Adapter;

class TransactionManagerTest
    extends \PHPUnit_Framework_TestCase
{
    protected function tearDown() {
        \Mockery::close();
    }

    public function testBeginTransaction() {
        $adapterMock = \Mockery::mock(new Adapter());
        $adapterMock->shouldReceive('begin')
                    ->once();

        $transactionManager = new TransactionManager($adapterMock);

        $transactionManager->begin();
    }

    public function testCommitTransaction() {
        $adapterMock = \Mockery::mock(new Adapter());
        $adapterMock->shouldReceive('commit')
                    ->once();

        $transactionManager = new TransactionManager($adapterMock);

        $transactionManager->begin()
                           ->commit();
    }

    public function testRollbackTransaction() {
        $adapterMock = \Mockery::mock(new Adapter());
        $adapterMock->shouldReceive('rollback')
                    ->once();

        $transactionManager = new TransactionManager($adapterMock);

        $transactionManager->begin()
                           ->rollback();
    }

    public function testIsInTransaction() {
        $transactionManager = new TransactionManager(new Adapter());

        $this->assertFalse($transactionManager->isInTransaction());
        $transactionManager->begin();
        $this->assertTrue($transactionManager->isInTransaction());
        $transactionManager->commit();
        $this->assertFalse($transactionManager->isInTransaction());
        $transactionManager->begin();
        $this->assertTrue($transactionManager->isInTransaction());
        $transactionManager->rollback();
        $this->assertFalse($transactionManager->isInTransaction());
    }

    public function testBeginNestedTransaction() {
        $adapterMock = \Mockery::mock(new Adapter());
        $adapterMock->shouldReceive('begin')
                    ->once();

        $transactionManager = new TransactionManager($adapterMock);

        $transactionManager->begin()
                           ->begin()
                           ->begin();
    }

    public function testCommitNestedTransaction() {
        $adapterMock = \Mockery::mock(new Adapter());
        $adapterMock->shouldReceive('commit')
                    ->once();

        $transactionManager = new TransactionManager($adapterMock);

        $transactionManager->begin()
                           ->begin()
                           ->commit()
                           ->begin()
                           ->commit()
                           ->commit();
    }

    public function testRollbackNestedTransaction() {
        $adapterMock = \Mockery::mock(new Adapter());
        $adapterMock->shouldReceive('rollback')
                    ->once();

        $transactionManager = new TransactionManager($adapterMock);

        $transactionManager->begin()
                           ->begin()
                           ->begin()
                           ->rollback()
                           ->rollback();
    }

    public function testIsInTransactionNestedTransaction() {
        $transactionManager = new TransactionManager(new Adapter());

        // begin commit
        $this->assertFalse($transactionManager->isInTransaction());
        $transactionManager->begin();
        $this->assertTrue($transactionManager->isInTransaction());
        $transactionManager->begin();
        $transactionManager->commit();
        $this->assertTrue($transactionManager->isInTransaction());
        $transactionManager->commit();
        $this->assertFalse($transactionManager->isInTransaction());

        //begin roolback
        $this->assertFalse($transactionManager->isInTransaction());
        $transactionManager->begin();
        $this->assertTrue($transactionManager->isInTransaction());
        $transactionManager->begin();
        $transactionManager->rollback();
        $this->assertFalse($transactionManager->isInTransaction());
    }
}