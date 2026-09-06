<?php

namespace App\Services\Csv;

class ParsedCsv
{
    /**
     * @param  string  $delimiter  Delimitador detectado.
     * @param  string[]  $header  Cabecera del CSV.
     * @param  array<int, array<int, string|null>>  $rows  Filas de datos.
     */
    public function __construct(
        public readonly string $delimiter,
        public readonly array $header,
        public readonly array $rows,
    ) {
        //
    }
}
