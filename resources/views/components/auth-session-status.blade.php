@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'mb-4 flex items-center gap-2 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2.5 text-sm font-medium text-emerald-800']) }}>
        <i class="fa-solid fa-circle-check text-lg text-emerald-500"></i>
        {{ $status }}
    </div>
@endif
