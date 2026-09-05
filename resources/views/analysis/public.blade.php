@extends('layouts.app')

@section('title', 'Análisis de tu gimnasio - GymRevenue')

@push('head')
    <meta name="robots" content="noindex, nofollow, noarchive">
    <meta name="googlebot" content="noindex, nofollow, noarchive">
@endpush

@section('content')
    <div class="max-w-3xl mx-auto px-6 py-10">
        <x-report-content
            :report="$report"
            context="public"
            :contact-url="route('analysis.public.contact', $token)"
        />
    </div>
@endsection