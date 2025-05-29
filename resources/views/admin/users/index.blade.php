@extends('layouts.admin')

@section('admin-content')
    <div class="flex justify-between">
        <h1 class="text-3xl font-semibold mb-6">Lista użytkowników</h1>
        <a href="{{ route('admin.users.create') }}"
           class="inline-block mb-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
            Dodaj nowego użytkownika
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200">
            <thead class="bg-gray-100">
            <tr>
                <th class="text-left px-2 sm:px-4 py-2 border-b border-gray-300 text-xs sm:text-sm">ID</th>
                <th class="text-left px-2 sm:px-4 py-2 border-b border-gray-300 text-xs sm:text-sm">Imię</th>
                <th class="text-left px-2 sm:px-4 py-2 border-b border-gray-300 text-xs sm:text-sm">Nazwisko</th>
                <th class="text-left px-2 sm:px-4 py-2 border-b border-gray-300 text-xs sm:text-sm hidden sm:table-cell">Email</th>
                <th class="text-left px-2 sm:px-4 py-2 border-b border-gray-300 text-xs sm:text-sm hidden md:table-cell">Telefon</th>
                <th class="text-left px-2 sm:px-4 py-2 border-b border-gray-300 text-xs sm:text-sm hidden lg:table-cell">Rola</th>
                <th class="text-left px-2 sm:px-4 py-2 border-b border-gray-300 text-xs sm:text-sm">Akcje</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($users as $user)
                <tr class="hover:bg-gray-50 text-xs sm:text-sm">
                    <td class="px-2 sm:px-4 py-2 border-b border-gray-200">{{ $user->id }}</td>
                    <td class="px-2 sm:px-4 py-2 border-b border-gray-200">{{ $user->first_name }}</td>
                    <td class="px-2 sm:px-4 py-2 border-b border-gray-200">{{ $user->last_name }}</td>
                    <td class="px-2 sm:px-4 py-2 border-b border-gray-200 hidden sm:table-cell truncate max-w-xs" title="{{ $user->email }}">{{ $user->email }}</td>
                    <td class="px-2 sm:px-4 py-2 border-b border-gray-200 hidden md:table-cell">{{ $user->phone }}</td>
                    <td class="px-2 sm:px-4 py-2 border-b border-gray-200 hidden lg:table-cell">{{ $user->role }}</td>
                    <td class="px-2 sm:px-4 py-2 border-b border-gray-200 space-x-2 whitespace-nowrap">
                        <a href="{{ route('admin.users.edit', $user) }}"
                           class="text-blue-600 hover:text-blue-800">Edytuj</a>

                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline"
                              onsubmit="return confirm('Na pewno usunąć?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="text-red-600 hover:text-red-800 bg-transparent border-none cursor-pointer">
                                Usuń
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center px-4 py-6">Brak użytkowników</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
@endsection
