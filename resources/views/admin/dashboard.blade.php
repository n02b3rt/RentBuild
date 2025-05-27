@extends('layouts.admin')

@section('admin-content')
    <section class="">
        <h1 class="text-2xl font-bold">Panel administratora</h1>
        <p class="mt-2 text-gray-600">Witaj w panelu admina!</p>
        <a href="{{ route('admin.equipment.index') }}">Sprzęt (lista)</a>
        <a href="{{ route('admin.equipment.create') }}" class="btn btn-primary mb-3">Dodaj nowy sprzęt</a>

    </section>
@endsection
