<?php
declare(strict_types=1);

namespace Franzose\DoctrineBulkInsert;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Identifier;

final class Query
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function execute(string $table, array $dataset, array $types = []): int
    {
        if (empty($dataset)) {
            return 0;
        }

        $sql = sql($this->connection->getDatabasePlatform(), new Identifier($table), $dataset);

        return $this->connection->executeUpdate($sql, parameters($dataset), types($types, count($dataset)));
    }

    public function transactional(string $table, array $dataset, array $types = []): int
    {
        return $this->connection->transactional(static function () use ($table, $dataset, $types): int {
            return $this->execute($table, $dataset, $types);
        });
    }
}
