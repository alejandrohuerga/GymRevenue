@extends('layouts.admin')

@section('title', 'Informe - '.$lead->gym_name.' - GymRevenue')

@section('content')
    <div class="max-w-3xl mx-auto px-6 py-10">
        <a href="{{ route('admin.leads.show', $lead) }}" class="text-sm text-zinc-400 hover:text-zinc-100 transition-colors">← Volver al lead</a>

        <x-report-content
            :report="$report"
            context="admin"
            :lead="$lead"
            :public-url="$lead->analysis?->public_token !== null ? route('analysis.public.show', $lead->analysis->public_token) : null"
        />
    </div>
@endsection