<?php
namespace StefanoNestedTransaction;

use StefanoNestedTransaction\Adapter\TransactionInterface as TransactionAdapterInterface;
use StefanoNestedTransaction\TransactionManagerInterface;
use StefanoNestedTransaction\Exception\LogicException;

class TransactionManager
    implements TransactionManagerInterface
{
    private $transactionAdapter;
    private $numberOfOpenedTransaction = 0;
    private $isMarkedAsRollbackOnly = false;

    public function __construct(TransactionAdapterInterface $transactionAdapter) {
        $this->transactionAdapter = $transactionAdapter;
    }

    public function begin() {
        if(0 == $this->getNumberOfOpenedTransaction()) {
            $this->getTransactionAdapter()
                 ->begin();
        }

        $this->increaseNumberOfOpenedTransaction();
        
        return $this;
    }

    public function commit() {
        if($this->isMarkedAsRollbackOnly()) {
            throw new LogicException('This transaction has been marked as rollback only');
        }

        if(0 == $this->getNumberOfOpenedTransaction()) {
            throw new LogicException('No active transaction');
        }

        if(1 == $this->getNumberOfOpenedTransaction()) {
            $this->getTransactionAdapter()
                 ->commit();
        }

        $this->decreaseNumberOfOpenedTransaction();
        
        return $this;
    }

    public function rollback() {
        if(0 == $this->getNumberOfOpenedTransaction()) {
            throw new LogicException('No active transaction');
        }

        if(1 == $this->getNumberOfOpenedTransaction()) {
            $this->getTransactionAdapter()
                 ->rollback();
            $this->markAsRollbackOnly(false);
        }  else {
            $this->markAsRollbackOnly(true);
        }

        $this->decreaseNumberOfOpenedTransaction();

        return $this;
    }

    public function isInTransaction() {
        if(0 < $this->getNumberOfOpenedTransaction()) {
            return  true;
        } else {
            return false;
        }
    }

    /**
     * @return TransactionAdapterInterface
     */
    private function getTransactionAdapter() {
        return $this->transactionAdapter;
    }

    /**
    * Get number of opened transaction
    * @return int
    */
    private function getNumberOfOpenedTransaction() {
        return $this->numberOfOpenedTransaction;
    }

    private function increaseNumberOfOpenedTransaction() {
        $this->numberOfOpenedTransaction++;
    }

    private function decreaseNumberOfOpenedTransaction() {
        $this->numberOfOpenedTransaction--;
    }

    /**
     * @return bool
     */
    private function isMarkedAsRollBackOnly() {
        return $this->isMarkedAsRollbackOnly;
    }

    /**
     * @param bool $bool
     */
    private function markAsRollbackOnly($bool) {
        $this->isMarkedAsRollbackOnly = (bool) $bool;
    }
}