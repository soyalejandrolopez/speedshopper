<div wire:key="chat-request-form" class="space-y-5">
    @if ($finished)
        <div class="flex items-start justify-end gap-2.5">
            <div class="max-w-[85%]">
                <div class="rounded-2xl rounded-tr-sm bg-gradient-to-r from-emerald-600 to-teal-600 px-4 py-3 text-sm leading-relaxed text-white shadow-md shadow-emerald-200/50">
                    {{ __('Perfect! We received your request. We will send you the quote soon.') }}
                </div>
                <p class="mt-1 pe-1 text-end text-[10px] text-gray-400">{{ __('now') }}</p>
            </div>
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-200 text-gray-500 ring-2 ring-white">
                <i class="fa-solid fa-circle-info text-lg"></i>
            </span>
        </div>
        <div class="flex justify-center">
            <button type="button" wire:click="resetChat" class="btn-ghost px-4 py-2 text-sm">
                <i class="fa-solid fa-arrows-rotate text-base"></i>
                {{ __('Start over') }}
            </button>
        </div>
    @elseif (! $started)
        <div class="flex items-start gap-2.5">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 text-white ring-2 ring-white shadow-sm">
                <i class="fa-solid fa-comments text-lg"></i>
            </span>
            <div class="max-w-[85%]">
                <div class="rounded-2xl rounded-tl-sm border border-gray-100 bg-white px-4 py-3 text-sm leading-relaxed text-gray-700 shadow-sm">
                    {{ __('I will ask you a few quick questions to prepare your quote. Ready?') }}
                </div>
                <p class="mt-1 ps-1 text-[10px] text-gray-400">{{ __('now') }}</p>
            </div>
        </div>
        <div class="flex justify-end">
            <button type="button" wire:click="start" class="btn-primary px-5 py-2.5">
                {{ __('Start') }}
                <i class="fa-solid fa-chevron-right text-base"></i>
            </button>
        </div>
    @else
        <div>
            <div class="mb-1.5 flex items-center justify-between text-[10px] font-bold uppercase tracking-wider text-gray-400">
                <span>{{ __('Step') }} {{ $this->step }} {{ __('of') }} {{ \App\Livewire\ChatRequestForm::TOTAL_STEPS }}</span>
                <span>{{ $this->progressPercent() }}%</span>
            </div>
            <div class="h-1.5 overflow-hidden rounded-full bg-gray-200">
                <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-500 transition-all duration-500" style="width: {{ $this->progressPercent() }}%"></div>
            </div>
        </div>

        @php
            $questions = $this->questions();
            $answers = $this->answers();
        @endphp

        @for ($i = 1; $i < $this->step; $i++)
            <div class="flex items-start gap-2.5">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 text-white ring-2 ring-white shadow-sm">
                    <i class="fa-solid fa-comments text-lg"></i>
                </span>
                <div class="max-w-[85%]">
                    <div class="rounded-2xl rounded-tl-sm border border-gray-100 bg-white px-4 py-3 text-sm leading-relaxed text-gray-700 shadow-sm">
                        {{ $questions[$i] }}
                    </div>
                    <p class="mt-1 ps-1 text-[10px] text-gray-400">{{ __('now') }}</p>
                </div>
            </div>

            <div class="flex items-start justify-end gap-2.5">
                <div class="max-w-[85%]">
                    <div class="rounded-2xl rounded-tr-sm bg-gradient-to-r from-emerald-600 to-teal-600 px-4 py-3 text-sm leading-relaxed text-white shadow-md shadow-emerald-200/50">
                        {{ $answers[$i] ?: __('Skipped') }}
                    </div>
                </div>
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-200 text-gray-500 ring-2 ring-white">
                    <i class="fa-solid fa-circle-info text-lg"></i>
            </span>
            </div>
        @endfor

        <div class="flex items-start gap-2.5">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 text-white ring-2 ring-white shadow-sm">
                <i class="fa-solid fa-comments text-lg"></i>
            </span>
            <div class="flex-1 rounded-2xl rounded-tl-sm border border-gray-100 bg-white px-4 py-3 text-sm leading-relaxed text-gray-700 shadow-sm">
                {{ $questions[$this->step] }}
            </div>
        </div>

        <form wire:submit="next" class="flex items-end gap-2">
            <div class="flex-1">
                @if ($this->isInputLong($this->step))
                    <textarea wire:key="answer-{{ $this->step }}" wire:model="currentAnswer" rows="2"
                              class="input" placeholder="{{ $this->placeholder($this->step) }}"></textarea>
                @else
                    <input wire:key="answer-{{ $this->step }}" wire:model="currentAnswer" type="{{ $this->inputType($this->step) }}"
                           class="input" placeholder="{{ $this->placeholder($this->step) }}" autofocus>
                @endif
                @error('currentAnswer') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            @if ($this->step > 1)
                <button type="button" wire:click="back" class="shrink-0 rounded-lg border border-gray-300 px-3 py-2.5 text-gray-500 transition-colors hover:bg-gray-50" title="{{ __('Back') }}">
                    <i class="fa-solid fa-arrow-left text-base"></i>
                </button>
            @endif
            <button type="submit" class="btn-primary shrink-0 !px-4 !py-2.5">
                <i class="fa-solid fa-paper-plane text-lg"></i>
            </button>
            @if (! $this->isRequiredStep($this->step))
                <button type="button" wire:click="skip" class="shrink-0 text-xs font-medium text-gray-400 transition-colors hover:text-gray-600">
                    {{ __('Skip') }}
                </button>
            @endif
        </form>
    @endif
</div>
