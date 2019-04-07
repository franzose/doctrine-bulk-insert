<?php
declare(strict_types=1);

namespace Franzose\DoctrineBulkInsert\Tests;

use Doctrine\DBAL\Platforms\PostgreSqlPlatform;
use Doctrine\DBAL\Schema\Identifier;
use function Franzose\DoctrineBulkInsert\extract_columns;
use function Franzose\DoctrineBulkInsert\parameters;
use function Franzose\DoctrineBulkInsert\generate_placeholders;
use function Franzose\DoctrineBulkInsert\sql;
use function Franzose\DoctrineBulkInsert\stringify_columns;
use function Franzose\DoctrineBulkInsert\types;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

final class FunctionsTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public const DATASET = [
        [
            'foo' => 111,
            'bar' => 222,
            'qux' => 333
        ],
        [
            'foo' => 444,
            'bar' => 555,
            'qux' => 777
        ],
    ];

    public function testExtractColumns(): void
    {
        static::assertEquals(['foo', 'bar', 'qux'], extract_columns(static::DATASET));
        static::assertEquals([], extract_columns([]));
    }

    public function testStringifyColumns(): void
    {
        static::assertEquals('(foo, bar, qux)', stringify_columns(['foo', 'bar', 'qux']));
        static::assertEquals('', stringify_columns([]));
    }

    public function testPlaceholders(): void
    {
        static::assertEquals(
            '(?, ?, ?, ?, ?), (?, ?, ?, ?, ?)',
            generate_placeholders(5, 2)
        );
    }

    public function testParameters(): void
    {
        static::assertEquals([111, 222, 333, 444, 555, 777], parameters(static::DATASET));
        static::assertEquals([], parameters([]));
    }

    public function testTypes(): void
    {
        $types = ['string', 'text', 'json'];

        $expected = [
            'string', 'text', 'json',
            'string', 'text', 'json'
        ];

        static::assertEquals($expected, types($types, 2));
    }

    public function testSql(): void
    {
        $sql = sql(new PostgreSqlPlatform(), new Identifier('foo'), static::DATASET);
        $expected = 'INSERT INTO foo (foo, bar, qux) VALUES (?, ?, ?), (?, ?, ?);';

        static::assertEquals($expected, $sql);
    }
}
