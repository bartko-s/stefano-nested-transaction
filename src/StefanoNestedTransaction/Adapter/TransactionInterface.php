<?php
namespace StefanoNestedTransaction\Adapter;

interface TransactionInterface
{
    /**
     * Begin transaction
     * @return void
     */
    public function begin();

    /**
     * Commit transaction
     * @return void
     */
    public function commit();

    /**
     * Roolback transaction
     * @return void
     */
    public function rollback();
}
