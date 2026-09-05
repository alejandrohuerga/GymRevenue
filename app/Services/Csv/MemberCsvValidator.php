<?php

namespace App\Services\Csv;

class MemberCsvValidator
{
    public const REQUIRED_COLUMNS = [
        'member_id',
        'name',
        'status',
        'join_date',
        'last_visit',
    ];

    public const OPTIONAL_COLUMNS = [
        'email',
        'monthly_fee',
        'cancel_date',
    ];

    /**
     * Devuelve null si la estructura cumple el formato estándar o el mensaje de error.
     */
    public function validate(ParsedCsv $csv): ?string
    {
        $available = array_map('strtolower', $csv->header);

        $missing = array_values(array_diff(self::REQUIRED_COLUMNS, $available));

        if ($missing !== []) {
            return 'El CSV no contiene las columnas obligatorias: '.implode(', ', $missing).'.';
        }

        if (count($available) !== count(array_unique($available))) {
            return 'La cabecera del CSV contiene columnas duplicadas.';
        }

        if ($csv->rows === []) {
            return 'El CSV no contiene filas de datos.';
        }

        $expectedColumns = count($csv->header);

        foreach ($csv->rows as $index => $row) {
            $hasContent = array_filter($row, static fn ($value) => trim((string) $value) !== '');

            if ($hasContent === []) {
                continue;
            }

            if (count($row) !== $expectedColumns) {
                return 'La fila '.($index + 2).' no tiene el mismo número de columnas que la cabecera.';
            }
        }

        return null;
    }
}
