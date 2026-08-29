@props([
    'data' => '',
    'size' => 96,
    'label' => null,
    'sublabel' => null,
    'color' => '#1f2937',
    'bgColor' => '#ffffff',
    'class' => '',
])

@php
    $qrData = $data ?: url()->current();
    $svg = qr_code_svg($qrData, (int) $size, $color, $bgColor);
@endphp

<div class="inline-flex flex-col items-center gap-1.5 {{ $class }}">
    <div class="rounded-xl border border-gray-200/80 bg-white p-1.5 shadow-sm">
        {!! $svg !!}
    </div>
    @if ($label)
        <span class="text-[10px] font-semibold tracking-wider text-gray-500 uppercase">{{ $label }}</span>
    @endif
    @if ($sublabel)
        <span class="text-[9px] text-gray-400">{{ $sublabel }}</span>
    @endif
</div>
