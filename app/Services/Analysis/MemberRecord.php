<?php

namespace App\Services\Analysis;

use Carbon\CarbonInterface;

final readonly class MemberRecord
{
    /**
     * Fila de socio normalizada para el análisis.
     *
     * @param  string[]  $errors  Claves de errores de calidad.
     */
    public function __construct(
        public int $index,
        public string $memberId,
        public string $name,
        public ?string $email,
        public string $status,
        public ?CarbonInterface $joinDate,
        public ?CarbonInterface $lastVisit,
        public ?float $monthlyFee,
        public ?CarbonInterface $cancelDate,
        public array $errors = [],
        public bool $duplicate = false,
    ) {}
}
