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
        <div class="space-y-4">
            @forelse ($users as $user)
                <div class="bg-white border border-gray-200 rounded-lg p-4 flex flex-col md:flex-row justify-between items-start space-y-4 md:space-y-0 md:space-x-6 hover:shadow-lg transition-all">
                    <div class="flex-1">
                        <div class="text-lg font-semibold">{{ $user->first_name }} {{ $user->last_name }}</div>
                        <div class="text-sm text-gray-600 mt-2">Email: <span class="font-medium">{{ $user->email }}</span></div>
                        <div class="text-sm text-gray-600 mt-1">Telefon: <span class="font-medium">{{ $user->phone ?? 'Brak' }}</span></div>
                        <div class="text-sm text-gray-600 mt-1">Rola: <span class="font-medium">{{ $user->role }}</span></div>
                    </div>
                    <div class="flex flex-col md:flex-row space-x-4 space-y-2 md:space-y-0 justify-start md:justify-end">
                        <a href="{{ route('admin.users.edit', $user) }}"
                           class="text-blue-600 hover:text-blue-800 text-sm">Edytuj</a>

                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline"
                              onsubmit="return confirm('Na pewno usunąć?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="text-red-600 hover:text-red-800 bg-transparent border-none cursor-pointer text-sm">
                                Usuń
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-500">Brak użytkowników</div>
            @endforelse
        </div>
    </div>

    <div class="mt-6">
        {{ $users->links() }}
    </div>
@endsection
