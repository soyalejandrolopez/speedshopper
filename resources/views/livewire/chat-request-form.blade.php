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
                <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </span>
        </div>
        <div class="flex justify-center">
            <button type="button" wire:click="resetChat" class="btn-ghost px-4 py-2 text-sm">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                {{ __('Start over') }}
            </button>
        </div>
    @elseif (! $started)
        <div class="flex items-start gap-2.5">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 text-white ring-2 ring-white shadow-sm">
                <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
                </svg>
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
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                </svg>
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
                    <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
                    </svg>
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
                    <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </span>
            </div>
        @endfor

        <div class="flex items-start gap-2.5">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 text-white ring-2 ring-white shadow-sm">
                <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
                </svg>
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
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                </button>
            @endif
            <button type="submit" class="btn-primary shrink-0 !px-4 !py-2.5">
                <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                </svg>
            </button>
            @if (! $this->isRequiredStep($this->step))
                <button type="button" wire:click="skip" class="shrink-0 text-xs font-medium text-gray-400 transition-colors hover:text-gray-600">
                    {{ __('Skip') }}
                </button>
            @endif
        </form>
    @endif
</div>
