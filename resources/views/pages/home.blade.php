@extends('layouts.app')

@section('title', 'GymRevenue — Tu gimnasio está perdiendo ingresos. Te decimos dónde.')
@section('meta_description', 'Descubre qué socios están en riesgo, qué clientes están inactivos y qué oportunidades de recuperación estás dejando pasar. Analiza tu gimnasio gratis.')

@section('content')
    <x-hero />
    <x-calculator />
    <x-lead-form />
    <x-social-proof />
    <x-problem />
    <x-solution />
    <x-how-it-works />
    <x-dashboard-preview />
    <x-benefits />
    <x-differentiation />
    <x-target-customer />
    <x-faq />
    <x-final-cta />
@endsection