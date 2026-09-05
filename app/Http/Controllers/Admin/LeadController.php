<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateLeadNotesRequest;
use App\Http\Requests\Admin\UpdateLeadStatusRequest;
use App\Models\GymLead;
use App\Models\GymLeadAnalysis;
use App\Services\Analysis\CommercialReport;
use App\Services\Analysis\CsvAnalysisEngine;
use App\Services\Csv\CsvParseException;
use App\Services\Csv\CsvParser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class LeadController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        if ($status !== null && ! in_array($status, GymLead::STATUSES, true)) {
            $status = null;
        }

        return view('admin.leads.index', [
            'leads' => GymLead::query()
                ->with('analysis')
                ->when($status, fn ($query) => $query->where('status', $status))
                ->latest()
                ->paginate(20)
                ->withQueryString(),
            'currentStatus' => $status,
            'statuses' => GymLead::STATUS_LABELS,
        ]);
    }

    public function show(GymLead $lead): View
    {
        return view('admin.leads.show', [
            'lead' => $lead,
            'statuses' => GymLead::STATUS_LABELS,
            'csv' => $this->csvInfo($lead),
            'analysis' => $lead->analysis,
        ]);
    }

    /**
     * Informe comercial orientado al propietario del gimnasio.
     *
     * Usa únicamente los resultados ya calculados por el motor y persistidos
     * en gym_lead_analyses. No reanaliza ni recalcula nada.
     */
    public function report(GymLead $lead): View
    {
        abort_unless($lead->analysis, 404);

        $lead->analysis->ensurePublicToken();

        $report = CommercialReport::fromAnalysis($lead->analysis);

        return view('admin.leads.report', [
            'lead' => $lead,
            'report' => $report,
        ]);
    }

    public function analyze(GymLead $lead): RedirectResponse
    {
        abort_unless($lead->csv_path, 404);

        $disk = Storage::disk('local');

        abort_unless($disk->exists($lead->csv_path), 404);

        try {
            $content = $disk->get($lead->csv_path);

            if ($content === null) {
                return back()->with('status', 'No se ha podido leer el CSV almacenado.');
            }

            $parsed = (new CsvParser)->parse($content);
            $result = (new CsvAnalysisEngine)->analyze($parsed);

            $lead->analysis()->updateOrCreate([], GymLeadAnalysis::fromResult($result));
        } catch (CsvParseException|Throwable $exception) {
            Log::error('No se ha podido analizar el CSV del lead {lead}.', [
                'lead' => $lead->id,
                'error' => $exception->getMessage(),
            ]);

            return back()->with('status', 'No se ha podido analizar el CSV: '.$exception->getMessage());
        }

        return back()->with('status', 'Análisis del CSV actualizado.');
    }

    public function downloadCsv(GymLead $lead): Response
    {
        abort_unless($lead->csv_path, 404);

        $disk = Storage::disk('local');

        abort_unless($disk->exists($lead->csv_path), 404);

        $filename = $lead->csv_original_name ?: 'socios.csv';

        return $disk->download($lead->csv_path, $filename);
    }

    public function updateStatus(GymLead $lead, UpdateLeadStatusRequest $request): RedirectResponse
    {
        $lead->update($request->validated());

        return back()->with('status', 'Estado del lead actualizado.');
    }

    public function updateNotes(GymLead $lead, UpdateLeadNotesRequest $request): RedirectResponse
    {
        $lead->update([
            'notes' => filled($request->validated('notes')) ? $request->validated('notes') : null,
        ]);

        return back()->with('status', 'Notas guardadas.');
    }

    /**
     * Datos del CSV para la vista de detalle.
     *
     * @return array{original_name: ?string, uploaded_at: ?Carbon, size: ?int, exists: bool}|null
     */
    private function csvInfo(GymLead $lead): ?array
    {
        if (! $lead->csv_path) {
            return null;
        }

        $disk = Storage::disk('local');
        $exists = $disk->exists($lead->csv_path);

        return [
            'original_name' => $lead->csv_original_name,
            'uploaded_at' => $lead->csv_uploaded_at,
            'size' => $exists ? $disk->size($lead->csv_path) : null,
            'exists' => $exists,
        ];
    }
}
