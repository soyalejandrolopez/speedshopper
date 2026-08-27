@props(['status'])

@php
    $color = match ($status->color()) {
        'gray' => 'bg-gray-100 text-gray-700 ring-gray-200',
        'blue' => 'bg-blue-50 text-blue-700 ring-blue-200',
        'amber' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'indigo' => 'bg-indigo-50 text-indigo-700 ring-indigo-200',
        'purple' => 'bg-purple-50 text-purple-700 ring-purple-200',
        'green' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'red' => 'bg-red-50 text-red-700 ring-red-200',
        'cyan' => 'bg-cyan-50 text-cyan-700 ring-cyan-200',
        'sky' => 'bg-sky-50 text-sky-700 ring-sky-200',
        'teal' => 'bg-teal-50 text-teal-700 ring-teal-200',
        default => 'bg-gray-100 text-gray-700 ring-gray-200',
    };
@endphp

<span class="badge-dot inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $color }}">
    <span class="h-1.5 w-1.5 rounded-full bg-current {{ in_array($status->color(), ['amber', 'purple', 'cyan', 'indigo']) ? 'animate-pulse-dot' : '' }}"></span>
    {{ $status->label() }}
</span>
