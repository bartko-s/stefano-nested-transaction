<?php
namespace StefanoNestedTransaction;

use StefanoNestedTransaction\Adapter\TransactionInterface as TransactionAdapterInterface;
use StefanoNestedTransaction\TransactionManagerInterface;

class TransactionManager
    implements TransactionManagerInterface
{
    private $transactionAdapter;
    private $numberOfOpenedTransaction = 0;

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
        if(1 == $this->getNumberOfOpenedTransaction()) {
            $this->getTransactionAdapter()
                 ->commit();
        }

        if(0 < $this->getNumberOfOpenedTransaction()) {
            $this->decreaseNumberOfOpenedTransaction();
        }

        return $this;
    }

    public function rollback() {
        if(1 <= $this->getNumberOfOpenedTransaction()) {
            $this->getTransactionAdapter()
                 ->rollback();
        }

        $this->resetNumberOfOpenedTransaction();

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

    private function resetNumberOfOpenedTransaction() {
        $this->numberOfOpenedTransaction = 0;
    }

    private function increaseNumberOfOpenedTransaction() {
        $this->numberOfOpenedTransaction++;
    }

    private function decreaseNumberOfOpenedTransaction() {
        $this->numberOfOpenedTransaction--;
    }
}