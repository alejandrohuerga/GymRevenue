<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeadRequest;
use App\Models\GymLead;
use App\Models\GymLeadAnalysis;
use App\Services\Analysis\CsvAnalysisEngine;
use App\Services\Csv\CsvParseException;
use App\Services\Csv\CsvParser;
use App\Services\OpportunityEstimator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LeadController extends Controller
{
    public function store(StoreLeadRequest $request): RedirectResponse
    {
        $validated = $request->safe()->except(['consent', 'website', 'csv']);

        $estimatedOpportunity = OpportunityEstimator::estimate(
            (int) ($validated['inactive_members'] ?? 0),
            (int) ($validated['monthly_cancellations'] ?? 0),
            (float) ($validated['average_fee'] ?? 0),
        );

        $csv = $this->storeCsv($request->file('csv'));

        $lead = GymLead::create([
            ...$validated,
            'estimated_opportunity' => $estimatedOpportunity,
            'consent_at' => now(),
            'csv_path' => $csv['path'] ?? null,
            'csv_original_name' => $csv['original_name'] ?? null,
            'csv_uploaded_at' => $csv['uploaded_at'] ?? null,
        ]);

        if ($csv !== null) {
            $this->computeAnalysis($lead);
        }

        return redirect()->route('thanks');
    }

    /**
     * Almacena el CSV en almacenamiento privado con nombre seguro.
     *
     * @return array{path: string, original_name: string, uploaded_at: Carbon}|null
     */
    private function storeCsv(?UploadedFile $file): ?array
    {
        if ($file === null || ! $file->isValid()) {
            return null;
        }

        $generatedName = Str::uuid()->toString().'.csv';

        $stored = Storage::disk('local')->putFileAs('csv', $file, $generatedName);

        if ($stored === false) {
            throw ValidationException::withMessages([
                'csv' => 'No se ha podido guardar el archivo. Inténtalo de nuevo.',
            ]);
        }

        return [
            'path' => $stored,
            'original_name' => $this->sanitizeOriginalName($file),
            'uploaded_at' => now(),
        ];
    }

    private function sanitizeOriginalName(UploadedFile $file): string
    {
        $name = basename((string) $file->getClientOriginalName());
        $name = preg_replace('/[\x00-\x1F\x7F]/u', '', $name) ?? '';
        $name = trim($name);

        return $name === '' ? 'socios.csv' : Str::limit($name, 255);
    }

    /**
     * Calcula el análisis del CSV y lo guarda asociado al lead.
     *
     * Si el análisis falla, el lead se mantiene guardado y el fallo se registra.
     */
    private function computeAnalysis(GymLead $lead): void
    {
        try {
            $content = Storage::disk('local')->get($lead->csv_path);

            if ($content === null) {
                return;
            }

            $parsed = (new CsvParser)->parse($content);
            $result = (new CsvAnalysisEngine)->analyze($parsed);

            $lead->analysis()->updateOrCreate([], GymLeadAnalysis::fromResult($result));
        } catch (CsvParseException|\Throwable $exception) {
            Log::warning('No se ha podido analizar el CSV del lead {lead}.', [
                'lead' => $lead->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
