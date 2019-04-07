<?php
declare(strict_types=1);

namespace Franzose\DoctrineBulkInsert\Tests;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\PostgreSqlPlatform;
use Franzose\DoctrineBulkInsert\Query;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

final class QueryTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testExecute(): void
    {
        $connection = Mockery::mock(Connection::class);
        $connection->shouldReceive('getDatabasePlatform')
            ->once()
            ->andReturn(new PostgreSqlPlatform());

        $connection->shouldReceive('executeUpdate')
            ->once()
            ->with('INSERT INTO foo (foo, bar) VALUES (?, ?), (?, ?);', [111, 222, 333, 444], [])
            ->andReturn(2);

        $rows = (new Query($connection))->execute('foo', [
            ['foo' => 111, 'bar' => 222],
            ['foo' => 333, 'bar' => 444],
        ]);

        static::assertEquals(2, $rows);
    }

    public function testExecuteWithEmptyDataset(): void
    {
        $connection = Mockery::mock(Connection::class);
        $connection->shouldNotReceive('getDatabasePlatform', 'executeUpdate');

        $rows = (new Query($connection))->execute('foo', []);

        static::assertEquals(0, $rows);
    }
}
