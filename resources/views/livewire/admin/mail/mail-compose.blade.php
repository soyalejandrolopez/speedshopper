<div>
    <x-slot name="header">{{ __('Send Email') }}</x-slot>

    @if ($status === 'sent')
        <div class="mb-4 flex items-center gap-2 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 ring-1 ring-inset ring-emerald-200">
            <i class="fa-solid fa-circle-check text-base"></i>
            {{ $statusMessage }}
        </div>
    @elseif ($status === 'error')
        <div class="mb-4 flex items-start gap-2 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700 ring-1 ring-inset ring-red-200">
            <i class="fa-solid fa-circle-exclamation mt-0.5 text-base shrink-0"></i>
            <span class="break-all">{{ $statusMessage }}</span>
        </div>
    @endif

    <form wire:submit="send" class="max-w-3xl space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white">
            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-3">
                <div class="flex items-center gap-2">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                        <i class="fa-solid fa-paper-plane text-base"></i>
                    </span>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">{{ __('New Message') }}</h3>
                        <p class="text-xs text-gray-500">{{ __('Send an email using the configured SMTP account.') }}</p>
                    </div>
                </div>
            </div>

            <div class="grid gap-5 p-5">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('Customer') }}</label>
                    <select wire:model.live="customer_id" class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">— {{ __('Type a recipient below') }} —</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }} · {{ $customer->email }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-400">{{ __('Selecting a customer fills the recipient automatically.') }}</p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700" for="recipient-{{ $this->getId() }}">{{ __('Recipient') }} *</label>
                    <input id="recipient-{{ $this->getId() }}" name="recipient" type="email" wire:model="recipient"
                           placeholder="cliente@example.com"
                           class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    @error('recipient') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700" for="subject-{{ $this->getId() }}">{{ __('Subject') }} *</label>
                    <input id="subject-{{ $this->getId() }}" name="subject" type="text" wire:model="subject"
                           placeholder="{{ __('Message subject') }}"
                           class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    @error('subject') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700" for="message-{{ $this->getId() }}">{{ __('Message') }} *</label>
                    <textarea id="message-{{ $this->getId() }}" name="message" wire:model="message" rows="8"
                              placeholder="{{ __('Write the email body here...') }}"
                              class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                    @error('message') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Sección de Adjuntos (Fotos, Videos, Documentos) --}}
                <div class="rounded-xl border border-gray-200 bg-gray-50/70 p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-semibold text-gray-900 flex items-center gap-1.5">
                            <i class="fa-solid fa-paperclip text-emerald-600 text-sm"></i>
                            <span>{{ __('Adjuntar Archivos (Fotos, Videos, Documentos)') }}</span>
                        </label>
                        <span class="text-xs text-gray-500">
                            {{ count($attachments) }} {{ __('archivo(s) seleccionado(s)') }}
                        </span>
                    </div>

                    {{-- Dropzone / File Input --}}
                    <div class="relative flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-white p-5 text-center transition-colors hover:border-emerald-400 hover:bg-emerald-50/30">
                        <input type="file" wire:model="attachments" multiple
                               accept="image/*,video/*,application/pdf,.doc,.docx,.xls,.xlsx,.zip"
                               class="absolute inset-0 h-full w-full cursor-pointer opacity-0" id="mail_attachments">
                        
                        <div class="flex flex-col items-center pointer-events-none">
                            <div class="flex items-center gap-2 text-emerald-600 mb-1">
                                <i class="fa-solid fa-image text-lg"></i>
                                <i class="fa-solid fa-video text-lg"></i>
                                <i class="fa-solid fa-file-lines text-lg"></i>
                            </div>
                            <p class="text-xs font-semibold text-gray-700">
                                <span class="text-emerald-700 underline">{{ __('Haz clic para seleccionar') }}</span> {{ __('o arrastra archivos aquí') }}
                            </p>
                            <p class="text-[11px] text-gray-400 mt-0.5">
                                {{ __('Soporta fotos (JPG, PNG), videos (MP4, MOV) y documentos (PDF, etc.)') }}
                            </p>
                        </div>
                    </div>

                    {{-- Uploading State --}}
                    <div wire:loading wire:target="attachments" class="w-full">
                        <div class="flex items-center gap-2 rounded-lg bg-emerald-50 px-3 py-2 text-xs font-medium text-emerald-700 border border-emerald-200">
                            <i class="fa-solid fa-circle-notch fa-spin text-sm"></i>
                            <span>{{ __('Cargando archivos adjuntos...') }}</span>
                        </div>
                    </div>

                    @error('attachments.*')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror

                    {{-- Lista de Archivos Seleccionados --}}
                    @if (count($attachments) > 0)
                        <div class="grid gap-2.5 sm:grid-cols-2 pt-1">
                            @foreach ($attachments as $index => $file)
                                @php
                                    $mime = is_object($file) && method_exists($file, 'getMimeType') ? $file->getMimeType() : '';
                                    $name = is_object($file) && method_exists($file, 'getClientOriginalName') ? $file->getClientOriginalName() : 'Archivo';
                                    $size = is_object($file) && method_exists($file, 'getSize') ? round($file->getSize() / 1024 / 1024, 2) : null;
                                    $isImage = str_starts_with($mime, 'image/');
                                    $isVideo = str_starts_with($mime, 'video/');
                                @endphp
                                <div wire:key="attachment-{{ $index }}" class="flex items-center justify-between gap-2.5 rounded-xl border border-gray-200 bg-white p-2.5 shadow-2xs">
                                    <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                        @if ($isImage)
                                            <div class="h-10 w-10 shrink-0 rounded-lg overflow-hidden border border-emerald-200 bg-emerald-50 flex items-center justify-center">
                                                @try
                                                    <img src="{{ $file->temporaryUrl() }}" alt="{{ $name }}" class="h-full w-full object-cover">
                                                @catch (\Throwable $e)
                                                    <i class="fa-solid fa-image text-emerald-600 text-sm"></i>
                                                @endtry
                                            </div>
                                        @elseif ($isVideo)
                                            <div class="h-10 w-10 shrink-0 rounded-lg border border-purple-200 bg-purple-50 text-purple-600 flex items-center justify-center">
                                                <i class="fa-solid fa-video text-base"></i>
                                            </div>
                                        @else
                                            <div class="h-10 w-10 shrink-0 rounded-lg border border-blue-200 bg-blue-50 text-blue-600 flex items-center justify-center">
                                                <i class="fa-solid fa-file-lines text-base"></i>
                                            </div>
                                        @endif

                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs font-semibold text-gray-800 truncate" title="{{ $name }}">
                                                {{ $name }}
                                            </p>
                                            <div class="flex items-center gap-2 text-[10px] text-gray-500 mt-0.5">
                                                @if ($isVideo)
                                                    <span class="font-bold text-purple-700 uppercase">{{ __('Video') }}</span>
                                                @elseif ($isImage)
                                                    <span class="font-bold text-emerald-700 uppercase">{{ __('Foto') }}</span>
                                                @else
                                                    <span class="font-bold text-blue-700 uppercase">{{ __('Documento') }}</span>
                                                @endif
                                                @if ($size !== null)
                                                    <span>&bull; {{ $size }} MB</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <button type="button" wire:click="removeAttachment({{ $index }})"
                                            class="h-7 w-7 rounded-lg text-gray-400 hover:bg-red-50 hover:text-red-600 flex items-center justify-center transition-colors shrink-0"
                                            title="{{ __('Quitar archivo') }}">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3">
            <p class="text-xs text-gray-400">
                <i class="fa-solid fa-circle-info text-sm me-1"></i>
                {{ __('The email is sent from the address configured in Mail / SMTP settings.') }}
            </p>
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-[0_8px_20px_rgba(16,185,129,0.3)] transition-all duration-300 hover:from-emerald-400 hover:to-emerald-500 hover:shadow-[0_12px_25px_rgba(16,185,129,0.4)] active:scale-[0.97]">
                <i class="fa-solid fa-paper-plane text-base"></i>
                {{ __('Send Email') }}
            </button>
        </div>
    </form>
</div>
