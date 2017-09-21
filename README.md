Stefano Nested Transaction
===================

| Test Status | Code Coverage | Dependencies |
| :---: | :---: | :---: |
| <a href="https://travis-ci.org/bartko-s/stefano-nested-transaction"><img src="https://secure.travis-ci.org/bartko-s/stefano-nested-transaction.png?branch=master" /></a> | <a href='https://coveralls.io/r/bartko-s/stefano-nested-transaction?branch=master'><img src='https://coveralls.io/repos/bartko-s/stefano-nested-transaction/badge.png?branch=master' alt='Coverage Status' /></a> | <a href='https://www.versioneye.com/user/projects/52de6ec2ec1375d5f70002d1'><img src='https://www.versioneye.com/user/projects/52de6ec2ec1375d5f70002d1/badge.png' alt="Dependency Status" /></a> |

Instalation using Composer
--------------------------
1. Run command  ``` composer require stefano/stefano-nested-transaction```

Features
------------

- manages nested transaction

Usage
------

- Configuration

```
//$transactionAdapter implements \StefanoNestedTransaction\Adapter\TransactionInterface
$transactionAdapter = new YourTransactionAdapter();

$transactionManager = new \StefanoNestedTransaction\TransactionManager($transactionAdapter);
```

- Example: normal flow

```
$transactionManager->begin(); //REAL start transaction
try {
    // ...

    //nested transaction block, that might be in some other code
    $transactionManager->begin(); //increase internal transaction counter
    try {
        // ...

        $transactionManager->commit(); //decrease internal transaction counter
    } catch(\Exception $e) {
        $transactionManager->rollback(); //skipped
        throw $e->getPrevious();
    }

    // ...
    $transactionManager->commit(); //REAL commit transaction;
} catch(\Exception $e) {
    $transactionManager->rollback(); //skipped
    throw $e->getPrevious();
}
```

- Example: throw exception

```
$transactionManager->begin(); //REAL start transaction
try {
    // ...

    //nested transaction block, that might be in some other code
    $transactionManager->begin(); //increase internal transaction counter
    try {
        // ...

        throw new \Exception();

        $transactionManager->commit(); //skipped
    } catch(\Exception $e) {
        $transactionManager->rollback(); //marked as rollback only
        throw $e->getPrevious();
    }

    // ...
    $transactionManager->commit(); //skipped
} catch(\Exception $e) {
    $transactionManager->rollback(); //REAL rollback
    throw $e->getPrevious();
}
```

- Example: throw exception

```
$transactionManager->begin(); //REAL start transaction
try {
    // ...

    //nested transaction block, that might be in some other code
    $transactionManager->begin(); //increase internal transaction counter
    try {
        // ...

        throw new \Exception();

        $transactionManager->commit(); //do nothing
    } catch(\Exception $e) {
        $transactionManager->rollback(); //marked as rollback only
    }

    // ...
    $transactionManager->commit(); //this throw exception because transaction is marked as rollback only
} catch(\Exception $e) {
    $transactionManager->rollback(); //REAL rollback
    throw $e->getPrevious();
}
```