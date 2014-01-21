<?php
namespace StefanoNestedTransaction;

interface TransactionManagerInterface
{
    /**
     * Begin transaction if nested transaction has not been opened yet
     * @return this
     */
    public function begin();

    /**
     * Commit transaction if nested transaction has not been opened yet
     * @return this
     */
    public function commit();

    /**
     * Roolback transaction
     * @return this
     */
    public function rollback();

    /**
     * Return true if transaction is opened
     * @return boolean
     */
    public function isInTransaction();
}
