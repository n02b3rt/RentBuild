<div class="max-w-4xl mx-auto py-12 px-4">
    <h2 class="text-3xl font-bold text-center mb-8">Często zadawane pytania i odpowiedzi</h2>

    <div class="divide-y divide-gray-200">
        @foreach($faqs as $faq)
            <div x-data="{ open: false }" class="py-4">
                <button @click="open = !open" class="w-full text-left flex justify-between items-center">
                    <span class="text-lg font-semibold">{{ $faq['question'] }}</span>
                    <span class="text-2xl font-light leading-none" x-text="open ? '–' : '+'"></span>
                </button>
                <div x-show="open" x-transition class="mt-2 text-gray-600 text-sm" x-cloak>
                    {!! nl2br(e($faq['answer'])) !!}
                </div>
            </div>
        @endforeach
    </div>
</div>

