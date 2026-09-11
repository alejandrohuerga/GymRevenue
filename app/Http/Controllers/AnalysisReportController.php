<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnalysisContactRequest;
use App\Models\GymLeadAnalysis;
use App\Services\Analysis\CommercialReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Informe comercial público.
 *
 * Expone únicamente los resultados agregados persistidos del análisis a través
 * de un token criptográfico. No requiere autenticación. NO expone el CSV,
 * los datos individuales de socios ni la información de contacto del lead.
 *
 * El token es el único secreto que autoriza el acceso: por eso la ruta es
 * pública, no se indexa y la respuesta se sirve sin almacenamiento en caché
 * pública. 
 */
class AnalysisReportController extends Controller
{
    /**
     * Muestra el informe comercial a partir del token secreto.
     */
    public function show(Request $request, string $token): Response
    {
        $analysis = $this->findByToken($token);

        abort_unless($analysis, 404);

        $report = CommercialReport::fromAnalysis($analysis);

        return response()
            ->view('analysis.public', [
                'report' => $report,
                'token' => $token,
            ])
            ->header('Cache-Control', 'no-store, private, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('X-Robots-Tag', 'noindex, nofollow');
    }

    /**
     * Registra el interés comercial procedente del informe público.
     *
     * No crea cuentas ni CRM: sólo anota sobre el lead vinculado al análisis
     * que el propietario manifestó interés. Así se conserva el contexto del
     * informe sin exponer el token como información comercial.
     */
    public function contact(string $token, AnalysisContactRequest $request): RedirectResponse
    {
        $analysis = $this->findByToken($token);

        abort_unless($analysis, 404);

        $lead = $analysis->gymLead;

        if ($lead !== null) {
            if ($lead->status === 'new') {
                $lead->update(['status' => 'interested']);
            }

            $details = trim($request->validated('name').' <'.$request->validated('email').'>');
            $message = trim((string) $request->validated('message'));

            $note = '['.now()->format('d/m/Y H:i').'] Contacto desde informe público: '.$details.($message !== '' ? ' — '.$message : '');

            $lead->update([
                'notes' => trim(($lead->notes !== null && $lead->notes !== '' ? $lead->notes."\n" : '').$note),
            ]);
        }

        return redirect()->route('analysis.public.show', $token)
            ->with('contact_sent', 'Gracias por tu interés. Te contactaremos muy pronto.');
    }

    /**
     * Busca el análisis por su token público. Devuelve null cuando no existe
     * para que la ruta responda 404 sin revelar información adicional.
     */
    private function findByToken(string $token): ?GymLeadAnalysis
    {
        return GymLeadAnalysis::query()
            ->where('public_token', $token)
            ->first();
    }
}
