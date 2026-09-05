<?php

namespace App\Services\Csv;

class CsvParser
{
    private const DELIMITERS = [',', ';'];

    /**
     * Detecta el delimitador más probable comparando su presencia en la primera línea.
     */
    public function detectDelimiter(string $content): string
    {
        $newlinePosition = strpos($content, "\n");
        $firstLine = $newlinePosition === false ? $content : substr($content, 0, $newlinePosition);
        $firstLine = rtrim($firstLine, "\r");

        $count = [',' => 0, ';' => 0];

        foreach (self::DELIMITERS as $delimiter) {
            $count[$delimiter] = substr_count($firstLine, $delimiter);
        }

        return $count[';'] > $count[','] ? ';' : ',';
    }

    /**
     * Normaliza la codificación a UTF-8 y elimina el BOM si existe.
     */
    public function normalizeEncoding(string $content): string
    {
        if (str_starts_with($content, "\xEF\xBB\xBF")) {
            $content = substr($content, 3);
        }

        if (! function_exists('mb_detect_encoding')) {
            return $content;
        }

        $detected = mb_detect_encoding($content, ['UTF-8', 'Windows-1252', 'ISO-8859-1'], true);

        if ($detected !== null && $detected !== 'UTF-8') {
            $converted = mb_convert_encoding($content, 'UTF-8', $detected);

            if ($converted !== false) {
                return $converted;
            }
        }

        return $content;
    }

    /**
     * Interpreta el contenido como CSV y devuelve cabecera y filas.
     *
     * @throws CsvParseException
     */
    public function parse(string $content): ParsedCsv
    {
        $content = $this->normalizeEncoding($content);

        if (trim($content) === '') {
            throw new CsvParseException('El CSV está vacío.');
        }

        $delimiter = $this->detectDelimiter($content);

        $handle = fopen('php://temp', 'r+');
        fwrite($handle, $content);
        rewind($handle);

        $rows = [];

        while (($line = fgetcsv($handle, 0, $delimiter)) !== false) {
            $line = array_map(
                static fn ($field) => trim((string) ($field ?? '')),
                $line,
            );

            if ($line === ['']) {
                continue;
            }

            $rows[] = $line;
        }

        fclose($handle);

        if ($rows === []) {
            throw new CsvParseException('No se ha podido leer ninguna línea del CSV.');
        }

        $header = array_map('trim', $rows[0]);

        if (count($header) === 1 && trim($header[0]) === '') {
            throw new CsvParseException('No se ha podido identificar la cabecera del CSV.');
        }

        return new ParsedCsv(
            delimiter: $delimiter,
            header: $header,
            rows: array_slice($rows, 1),
        );
    }
}
