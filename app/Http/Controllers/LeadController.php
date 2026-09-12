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

        // Señal one-shot para analytics: lead_created solo si el guardado fue real.
        session()->flash('lead_created', true);

        if ($csv !== null) {
            return $this->handleCsvFlow($lead);
        }

        return redirect()->route('thanks');
    }

    /**
     * Redirige tras guardar un lead con CSV.
     *
     * - Con registros válidos: redirección directa al informe público.
     * - Sin registros válidos o con error de análisis: mensaje amigable y
     *   vuelta al formulario conservando el lead y los datos introducidos.
     */
    private function handleCsvFlow(GymLead $lead): RedirectResponse
    {
        try {
            $analysis = $this->computeAnalysis($lead);

            if ($analysis === null || $analysis->members_valid < 1) {
                Log::info('El CSV del lead {lead} no contiene registros válidos para el informe.', [
                    'lead' => $lead->id,
                ]);

                return $this->analysisUnavailable(
                    'No hemos podido generar tu informe: ninguna fila del CSV contiene datos válidos. Revisa el archivo e inténtalo de nuevo.',
                );
            }

            return redirect()->route('analysis.public.show', [
                'token' => $analysis->ensurePublicToken(),
            ]);
        } catch (CsvParseException|\Throwable $exception) {
            Log::error('No se ha podido analizar el CSV del lead {lead}.', [
                'lead' => $lead->id,
                'error' => $exception->getMessage(),
            ]);

            return $this->analysisUnavailable(
                'No hemos podido generar tu informe con este CSV. Revisa el archivo e inténtalo de nuevo.',
            );
        }
    }

    private function analysisUnavailable(string $message): RedirectResponse
    {
        return redirect()->back()
            ->withInput()
            ->withErrors([
                'csv' => $message,
            ]);
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
     * Devuelve el análisis persistido o null cuando no se dispone de contenido.
     * Los errores de procesamiento se propagan para que el flujo público
     * decida cómo responder; el lead queda guardado en cualquier caso.
     */
    private function computeAnalysis(GymLead $lead): ?GymLeadAnalysis
    {
        $content = Storage::disk('local')->get($lead->csv_path);

        if ($content === null) {
            return null;
        }

        $parsed = (new CsvParser)->parse($content);
        $result = app(CsvAnalysisEngine::class)->analyze($parsed);

        return $lead->analysis()->updateOrCreate([], GymLeadAnalysis::fromResult($result));
    }
}
