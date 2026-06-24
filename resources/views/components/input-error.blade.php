@props(['messages'])

@if ($messages)
    <p {{ $attributes->merge(['class' => 'text-[10px] font-bold text-red-500 mt-1.5 transition-all']) }}>
        @if (is_array($messages))
            {{ implode(' ', $messages) }}
        @else
            {{ $messages }}
        @endif
    </p>
@endif
