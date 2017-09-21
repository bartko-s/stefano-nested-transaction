<?php
namespace StefanoNestedTransactionTest\Unit;

use StefanoNestedTransaction\Exception\LogicException;
use StefanoNestedTransaction\TransactionManager;
use StefanoNestedTransactionTest\TestCase;
use StefanoNestedTransactionTest\Unit\Dummy\Adapter;

class TransactionManagerTest
    extends TestCase
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

    public function testThrowExceptionIfYouTryCommitTransactionAndTransactionIsNotActive() {
        $transactionManager = new TransactionManager(new Adapter());

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('No active transaction');

        $transactionManager->commit();
    }

    public function testRollbackTransaction() {
        $adapterMock = \Mockery::mock(new Adapter());
        $adapterMock->shouldReceive('rollback')
                    ->once();

        $transactionManager = new TransactionManager($adapterMock);

        $transactionManager->begin()
                           ->rollback();
    }

    public function testThrowExceptionIfYouTryRollbackTransactionAndTransactionIsNotActive() {
        $transactionManager = new TransactionManager(new Adapter());

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('No active transaction');

        $transactionManager->rollback();
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

    public function testCommitNestedTransactionOnlyLastCommitIsRealCommit() {
        $adapterMock = \Mockery::mock(new Adapter());
        $adapterMock->shouldReceive('commit')
                    ->never();

        $transactionManager = new TransactionManager($adapterMock);

        //3 * begin but only 2 * commit so real commit is never called
        $transactionManager->begin()
                           ->begin()
                           ->commit()
                           ->begin()
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
                           ->rollback()
                           ->rollback();
    }

    public function testRollbackNestedTransactionOnlyLastRollbackIsRealRollback() {
        $adapterMock = \Mockery::mock(new Adapter());
        $adapterMock->shouldReceive('rollback')
                    ->never();

        $transactionManager = new TransactionManager($adapterMock);

        //3 * begin but only 2 * rollback so real rollback is never called
        $transactionManager->begin()
                           ->begin()
                           ->begin()
                           ->rollback()
                           ->rollback();
    }

    public function testThrowExceptionIfYouTryCommitTransactionWhichHasBeenMarkedAsRollback() {
        $transactionManager = new TransactionManager(new Adapter());

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('This transaction has been marked as rollback only');

        $transactionManager->begin()
                           ->begin()
                           ->rollback()
                           ->commit();
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

        //begin rollback
        $this->assertFalse($transactionManager->isInTransaction());
        $transactionManager->begin();
        $this->assertTrue($transactionManager->isInTransaction());
        $transactionManager->begin();
        $transactionManager->rollback();
        $transactionManager->rollback();
        $this->assertFalse($transactionManager->isInTransaction());
    }
}