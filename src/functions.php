<?php
declare(strict_types=1);

namespace Franzose\DoctrineBulkInsert;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\Identifier;

function sql(AbstractPlatform $platform, Identifier $table, array $dataset): string
{
    $columns = quote_columns($platform, extract_columns($dataset));

    $sql = sprintf(
        'INSERT INTO %s %s VALUES %s;',
        $table->getQuotedName($platform),
        stringify_columns($columns),
        generate_placeholders(count($columns), count($dataset))
    );

    return $sql;
}

function extract_columns(array $dataset): array
{
    if (empty($dataset)) {
        return [];
    }

    $first = reset($dataset);

    return array_keys($first);
}

function quote_columns(AbstractPlatform $platform, array $columns): array
{
    $mapper = static fn (string $column) => (new Identifier($column))->getQuotedName($platform);

    return array_map($mapper, $columns);
}

function stringify_columns(array $columns): string
{
    return empty($columns) ? '' : sprintf('(%s)', implode(', ', $columns));
}

function generate_placeholders(int $columnsLength, int $datasetLength): string
{
    // (?, ?, ?, ?)
    $placeholders = sprintf('(%s)', implode(', ', array_fill(0, $columnsLength, '?')));

    // (?, ?), (?, ?)
    return implode(', ', array_fill(0, $datasetLength, $placeholders));
}

function parameters(array $dataset): array
{
    $reducer = static fn (array $flattenedValues, array $dataset) => array_merge($flattenedValues, array_values($dataset));

    return array_reduce($dataset, $reducer, []);
}

function types(array $types, int $datasetLength): array
{
    if (empty($types)) {
        return [];
    }

    $types = array_values($types);

    $positionalTypes = [];

    for ($idx = 1; $idx <= $datasetLength; $idx++) {
        $positionalTypes = array_merge($positionalTypes, $types);
    }

    return $positionalTypes;
}
