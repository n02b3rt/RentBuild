@extends('layouts.app')

@section('content')
    <section class="max-w-[1140px] border-gray-50 mx-auto px-4 py-12">
        <h1 class="text-2xl font-bold">Panel administratora</h1>
        <p class="mt-2 text-gray-600">Witaj w panelu admina!</p>
        <a href="{{ route('admin.equipment.index') }}">Sprzęt (lista)</a>
        <a href="{{ route('admin.equipment.create') }}" class="btn btn-primary mb-3">Dodaj nowy sprzęt</a>

    </section>
@endsection
