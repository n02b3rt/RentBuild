@extends('layouts.admin')

@section('admin-content')
    <h1 class="text-3xl font-semibold mb-6">Dodaj nowego użytkownika</h1>

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-100 text-red-700 rounded">
            <strong class="block mb-2">Wystąpiły błędy:</strong>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.users.store') }}" method="POST" class="max-w-3xl">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-4">
            <div>
                <label for="first_name" class="block mb-1 font-medium">Imię:</label>
                <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="last_name" class="block mb-1 font-medium">Nazwisko:</label>
                <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="phone" class="block mb-1 font-medium">Telefon:</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="email" class="block mb-1 font-medium">Email:</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="password" class="block mb-1 font-medium">Hasło:</label>
                <input type="password" id="password" name="password" required
                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="password_confirmation" class="block mb-1 font-medium">Potwierdź hasło:</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <div class="mb-4">
            <label for="address" class="block mb-1 font-medium">Adres:</label>
            <textarea id="address" name="address" rows="3"
                      class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('address') }}</textarea>
        </div>

        <div class="mb-4">
            <label for="shipping_address" class="block mb-1 font-medium">Adres wysyłki:</label>
            <textarea id="shipping_address" name="shipping_address" rows="3"
                      class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('shipping_address') }}</textarea>
        </div>

        <div class="mb-6">
            <label for="role" class="block mb-1 font-medium">Rola:</label>
            <select id="role" name="role"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="administrator" {{ old('role') === 'administrator' ? 'selected' : '' }}>Administrator</option>
                <option value="klient" {{ old('role', 'klient') === 'klient' ? 'selected' : '' }}>Klient</option>
            </select>
        </div>

        <div class="flex items-center space-x-4">
            <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Zapisz</button>
            <a href="{{ route('admin.users.index') }}"
               class="text-gray-600 hover:text-gray-800">Anuluj</a>
        </div>
    </form>
@endsection
