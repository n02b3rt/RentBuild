@extends('layouts.admin')

@section('admin-content')
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    <h1 class="text-3xl font-bold mb-8 text-[#f56600]">Lista zamówień</h1>

    <div class="mb-6">
        <a href="{{ route('admin.rentals.create') }}"
           class="inline-block bg-[#f56600] hover:bg-[#f98800] text-white font-semibold py-2 px-6 rounded">
            Dodaj nowe zamówienie
        </a>
    </div>

    <form method="GET" action="{{ route('admin.rentals.list.index') }}" class="mb-6 flex items-center gap-4">
        <label for="status" class="font-medium text-gray-700">Filtruj po statusie:</label>
        @php
            $status = request('status', 'oczekujace');
        @endphp

        <select name="status" id="status" class="border p-2 pl-8 pr-8 rounded">
            <option value="wszystkie" {{ $status === 'wszystkie' ? 'selected' : '' }}>Wszystkie</option>
            <option value="oczekujace" {{ $status === 'oczekujace' ? 'selected' : '' }}>Oczekujące</option>
            <option value="nadchodzace" {{ $status === 'nadchodzace' ? 'selected' : '' }}>Nadchodzące</option>
            <option value="aktualne" {{ $status === 'aktualne' ? 'selected' : '' }}>Aktualne</option>
            <option value="zrealizowane" {{ $status === 'zrealizowane' ? 'selected' : '' }}>Zrealizowane</option>
            <option value="anulowane" {{ $status === 'anulowane' ? 'selected' : '' }}>Anulowane</option>
            <option value="odrzucone" {{ $status === 'odrzucone' ? 'selected' : '' }}>Odrzucone</option>
        </select>


        <button type="submit" class="bg-[#f56600] hover:bg-[#f98800] text-white py-2 px-4 rounded font-semibold">
            Filtruj
        </button>
    </form>

    @if($rentals->isEmpty())
        <p class="text-gray-600 italic">Brak zamówień.</p>
    @else

        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-300 rounded-lg divide-y divide-gray-200">
                <thead class="bg-[#f56600] text-white">
                <tr>
                    <th class="px-4 py-3 text-center">ID</th>
                    <th class="px-4 py-3 text-center">Sprzęt</th>
                    <th class="px-4 py-3 text-center">Użytkownik</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Data rozpoczęcia</th>
                    <th class="px-4 py-3 text-center">Akcje</th>
                </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                @foreach($rentals as $rental)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 border text-center">{{ $rental->id }}</td>
                        <td class="px-4 py-3 border text-center">
                            <a href="{{ route('equipment.show', $rental->equipment->id) }}"
                               class="hover-preview text-blue-600 underline"
                               data-preview-url="{{ route('equipment.showPreview', $rental->equipment->id) }}">
                                {{ $rental->equipment->name ?? '-' }}
                            </a>
                        </td>

                        <td class="px-4 py-3 border text-center">
                            <a href="{{ route('admin.users.edit', $rental->user->id) }}"
                               class="text-blue-600 underline">
                                {{ $rental->user->first_name }} {{ $rental->user->last_name }}
                            </a>
                        </td>

                        <td class="px-4 py-3 border capitalize text-center">
                            {{ str_replace('_', ' ', $rental->status) }}
                        </td>

                        <td class="px-4 py-3 border text-center">
                            {{ optional($rental->start_date)->format('Y-m-d H:i') }}
                        </td>

                        <td class="px-4 py-3 border text-center">
                            @if($rental->status === 'oczekujace')
                                <div class="inline-flex gap-2">
                                    <a href="{{ route('admin.rentals.show', $rental) }}"
                                       class="bg-[#f56600] hover:bg-[#f98800] text-white py-1.5 px-4 rounded font-semibold text-sm shadow">
                                        Szczegóły
                                    </a>
                                    <button type="button"
                                            onclick="openDecisionModal({{ $rental->id }})"
                                            class="bg-green-600 hover:bg-green-700 text-white py-1.5 px-4 rounded font-semibold text-sm shadow">
                                        Decyzja
                                    </button>
                                </div>
                            @elseif($rental->status === 'nadchodzace')
                                <div class="inline-flex gap-2">
                                    <a href="{{ route('admin.rentals.show', $rental) }}"
                                       class="bg-[#f56600] hover:bg-[#f98800] text-white py-1.5 px-4 rounded font-semibold text-sm shadow">
                                        Szczegóły
                                    </a>
                                    <form action="{{ route('admin.rentals.cancel', $rental) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="bg-red-600 hover:bg-red-700 text-white py-1.5 px-4 rounded font-semibold text-sm shadow"
                                                onclick="return confirm('Anulować wypożyczenie #{{ $rental->id }}?')">
                                            Anuluj
                                        </button>
                                    </form>
                                </div>
                            @else
                                <a href="{{ route('admin.rentals.show', $rental) }}"
                                   class="block w-full text-center bg-[#f56600] hover:bg-[#f98800] text-white py-1.5 px-4 rounded font-semibold text-sm shadow">
                                    Szczegóły
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <!-- Hover Preview Box -->
            <div id="hover-preview-box"
                 class="absolute w-[400px] h-[300px] border border-gray-300 shadow-xl bg-white hidden z-50">
                <iframe src="" class="w-full h-full"></iframe>
            </div>

            <!-- Modal Decyzji -->
            <div id="decision-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
                <div class="bg-white p-6 rounded-lg shadow-xl max-w-md w-full relative">
                    <button onclick="closeDecisionModal()"
                            class="absolute top-3 right-3 text-gray-400 hover:text-gray-700 text-2xl font-bold leading-none transition-colors duration-200">
                        &times;
                    </button>
                    <h2 class="text-2xl font-bold mb-5 text-gray-900">Decyzja dotycząca wypożyczenia <span id="rental-id-label" class="text-orange-600"></span></h2>
                    <p class="text-gray-700 mb-6">
                        Wybierz, czy chcesz zatwierdzić lub odrzucić to zamówienie.
                    </p>

                    <div class="flex justify-end gap-4">
                        <!-- Formularz zatwierdzenia -->
                        <form id="approve-form" method="POST" action="">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="bg-green-600 hover:bg-green-700 text-white py-2 px-6 rounded-md font-semibold text-sm shadow transition-colors duration-200">
                                Zatwierdź
                            </button>
                        </form>

                        <!-- Przycisk pokazujący textarea do odrzucenia -->
                        <button type="button"
                                onclick="showRejectionTextarea()"
                                class="bg-red-600 hover:bg-red-700 text-white py-2 px-6 rounded-md font-semibold text-sm shadow transition-colors duration-200">
                            Odrzuć
                        </button>
                    </div>

                    <!-- Formularz odrzucenia z uwagą -->
                    <form id="reject-form" method="POST" action="" class="mt-6 space-y-4 hidden">
                        @csrf
                        @method('PATCH')

                        <label for="rejection_note" class="block text-sm font-medium text-gray-700">
                            Powód odrzucenia (opcjonalny)
                        </label>
                        <textarea name="rejection_note" id="rejection_note"
                                  class="border border-gray-300 p-3 rounded-md w-full text-sm text-gray-800 placeholder-gray-400 resize-y focus:outline-none focus:ring-2 focus:ring-orange-500 transition"
                                  rows="4"
                                  placeholder="Dodaj powód odrzucenia..."></textarea>

                        <div class="flex justify-end gap-3">
                            <button type="button"
                                    onclick="hideRejectionTextarea()"
                                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-5 rounded-md font-semibold text-sm transition-colors duration-200">
                                Anuluj
                            </button>
                            <button type="submit"
                                    class="bg-red-600 hover:bg-red-700 text-white py-2 px-6 rounded-md font-semibold text-sm shadow transition-colors duration-200">
                                Potwierdź odrzucenie
                            </button>
                        </div>
                    </form>
                </div>

                    <!-- Formularz odrzucenia z uwagą -->
                    <form id="reject-form"
                          method="POST"
                          action="{{ route('admin.rentals.reject', $rental->id) }}"
                          class="mt-4 hidden">
                        @csrf
                        @method('PATCH')

                        <label for="rejection_note" class="block text-sm font-medium text-gray-700 mb-1">
                            Powód odrzucenia (opcjonalny)
                        </label>
                        <textarea name="rejection_note" id="rejection_note"
                                  class="border p-2 rounded w-full text-sm text-gray-800 mb-3"
                                  placeholder="Dodaj powód odrzucenia..."></textarea>

                        <button type="submit"
                                class="bg-red-600 hover:bg-red-700 text-white py-1 px-4 rounded font-semibold text-sm">
                            Potwierdź odrzucenie
                        </button>
                    </form>

                </div>

            <!-- Script do hover preview i modala -->
            <script>
                const previewBox = document.getElementById('hover-preview-box');
                const iframe = previewBox.querySelector('iframe');

                document.querySelectorAll('.hover-preview').forEach(link => {
                    link.addEventListener('mouseenter', () => {
                        const url = link.dataset.previewUrl;
                        iframe.src = url;
                        previewBox.classList.remove('hidden');
                    });

                    link.addEventListener('mousemove', (e) => {
                        previewBox.style.top = (e.pageY + 15) + 'px';
                        previewBox.style.left = (e.pageX + 15) + 'px';
                    });

                    link.addEventListener('mouseleave', () => {
                        previewBox.classList.add('hidden');
                        iframe.src = '';
                    });
                });

                function openDecisionModal(rentalId) {
                    const modal = document.getElementById('decision-modal');
                    const approveForm = document.getElementById('approve-form');
                    const rejectForm = document.getElementById('reject-form');
                    const label = document.getElementById('rental-id-label');

                    label.textContent = `#${rentalId}`;
                    approveForm.action = `/admin/rentals/${rentalId}/approve`;
                    rejectForm.action = `/admin/rentals/${rentalId}/reject`;

                    modal.classList.remove('hidden');
                }

                function closeDecisionModal() {
                    document.getElementById('decision-modal').classList.add('hidden');
                }

                function showRejectionTextarea() {
                    document.getElementById('reject-form').classList.remove('hidden');
                    document.getElementById('approve-form').style.display = 'none';
                    event.target.style.display = 'none';
                }

                function hideRejectionTextarea() {
                    document.getElementById('reject-form').classList.add('hidden');
                    document.getElementById('approve-form').style.display = '';
                    const rejectButton = document.querySelector('button[onclick="showRejectionTextarea()"]');
                    if (rejectButton) rejectButton.style.display = '';
                }
            </script>
        </div>

        <div class="mt-4">
            {{ $rentals->links() }}
        </div>
    @endif
@endsection
