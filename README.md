# Doctrine/DBAL Bulk Insert
The library is based on the [gist](https://gist.github.com/gskema/a182aaf7cc04001aebba9c1aad86b40b) and provides bulk insert functionality to the [Doctrine/DBAL](https://github.com/doctrine/dbal).

## Usage

```php
<?php

use Doctrine\DBAL\Connection;
use Franzose\DoctrineBulkInsert\Query;

// Prepare database connection
$connection = new Connection(...);

// Execute query and get affected rows back
$rows = (new Query($connection))->execute('FooTable', [
    ['foo' => 111, 'bar' => 222],
    ['foo' => 333, 'bar' => 444],
]);
```
