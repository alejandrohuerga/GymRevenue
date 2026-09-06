<?php

namespace App\Rules;

use App\Services\Csv\CsvParseException;
use App\Services\Csv\CsvParser;
use App\Services\Csv\MemberCsvValidator;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class ValidMemberCsv implements ValidationRule
{
    /**
     * Valida la estructura del CSV con el formato estándar de GymRevenue.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile) {
            $fail('El archivo subido no es válido.');

            return;
        }

        $content = $value->get();

        try {
            $parsed = (new CsvParser)->parse((string) $content);
        } catch (CsvParseException $exception) {
            $fail($exception->getMessage());

            return;
        }

        $error = (new MemberCsvValidator)->validate($parsed);

        if ($error !== null) {
            $fail($error);
        }
    }
}
