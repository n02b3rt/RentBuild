@props(['field', 'label'])

@php
    $currentSort = request('sort');
    $currentDirection = request('direction', 'asc');
    $isActive = $currentSort === $field;
    $nextDirection = ($isActive && $currentDirection === 'asc') ? 'desc' : 'asc';
@endphp

<a href="{{ route(Route::currentRouteName(), array_merge(request()->all(), ['sort' => $field, 'direction' => $nextDirection])) }}"
    {{ $attributes->merge(['class' => 'hover:underline flex items-center space-x-1']) }}>
    <span>{{ $label }}</span>
    @if($isActive)
        @if($currentDirection === 'asc')
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
            </svg>
        @else
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
        @endif
    @endif
</a>
