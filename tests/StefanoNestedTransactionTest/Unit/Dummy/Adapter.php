<?php
namespace StefanoNestedTransactionTest\Unit\Dummy;

use StefanoNestedTransaction\Adapter\TransactionInterface;

class Adapter
    implements TransactionInterface
{
    public function begin() {

    }

    public function commit() {
        
    }

    public function rollback() {

    }

}
