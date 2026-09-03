<div class="rounded-3xl border border-emerald-100/80 bg-white p-6 shadow-xl shadow-emerald-100/30 sm:p-8">
    @if ($sent)
        <div class="animate-fade-up flex flex-col items-center py-8 text-center">
            <span class="relative flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100">
                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-200 opacity-60"></span>
                <i class="fa-solid fa-check text-3xl relative text-emerald-600"></i>
            </span>
            <h3 class="mt-5 text-xl font-bold text-gray-900">{{ __('¡Mensaje recibido con éxito!') }}</h3>
            <p class="mt-2 max-w-md text-sm text-gray-600">
                {{ __('Gracias por contactarnos. Uno de nuestros personal shoppers te responderá a la brevedad.') }}
            </p>

            <div class="mt-6 flex flex-wrap justify-center gap-3">
                @if ($whatsappUrl)
                    <a href="{{ $whatsappUrl }}" target="_blank" class="btn-primary !bg-emerald-600 hover:!bg-emerald-700">
                        <i class="fa-brands fa-whatsapp text-lg"></i>
                        {{ __('Abrir chat en WhatsApp ahora') }}
                    </a>
                @endif
                <button type="button" wire:click="resetForm" class="btn-ghost">
                    <i class="fa-solid fa-rotate-left text-sm"></i>
                    {{ __('Enviar otro mensaje') }}
                </button>
            </div>
        </div>
    @else
        <form wire:submit="submit" class="space-y-4">
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label flex items-center gap-1.5" for="contact-name">
                        <i class="fa-solid fa-user text-emerald-600 text-xs"></i>
                        <span>{{ __('Nombre completo') }} *</span>
                    </label>
                    <input id="contact-name" name="name" type="text" autocomplete="name" maxlength="255" wire:model="form.name" class="input" placeholder="Tu nombre">
                    @error('form.name') <p class="helper-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="label flex items-center gap-1.5" for="contact-email">
                        <i class="fa-solid fa-envelope text-emerald-600 text-xs"></i>
                        <span>{{ __('Correo electrónico') }} *</span>
                    </label>
                    <input id="contact-email" name="email" type="email" autocomplete="email" maxlength="255" wire:model="form.email" class="input" placeholder="tu@email.com">
                    @error('form.email') <p class="helper-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label flex items-center gap-1.5" for="contact-whatsapp">
                        <i class="fa-solid fa-phone text-emerald-600 text-xs"></i>
                        <span>{{ __('Teléfono o WhatsApp') }}</span>
                    </label>
                    <input id="contact-whatsapp" name="whatsapp" type="tel" autocomplete="tel" maxlength="20" wire:model="form.whatsapp" class="input" placeholder="+58 412 000 0000">
                    @error('form.whatsapp') <p class="helper-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="label flex items-center gap-1.5" for="contact-country">
                        <i class="fa-solid fa-location-dot text-emerald-600 text-xs"></i>
                        <span>{{ __('País de destino') }}</span>
                    </label>
                    <select id="contact-country" name="country" autocomplete="country-name" wire:model="form.country" class="input">
                        <option value="">{{ __('Selecciona un país') }}</option>
                        @foreach ($this->countries() as $code => $name)
                            <option value="{{ $code }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('form.country') <p class="helper-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="label flex items-center gap-1.5" for="contact-subject">
                    <i class="fa-solid fa-tag text-emerald-600 text-xs"></i>
                    <span>{{ __('Motivo de contacto') }} *</span>
                </label>
                <select id="contact-subject" name="subject" autocomplete="off" wire:model="form.subject" class="input">
                    @foreach ($this->subjects() as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('form.subject') <p class="helper-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label flex items-center gap-1.5" for="contact-message">
                    <i class="fa-solid fa-comment-dots text-emerald-600 text-xs"></i>
                    <span>{{ __('Mensaje o detalles de tu consulta') }} *</span>
                </label>
                <textarea id="contact-message" name="message" autocomplete="off" rows="4" wire:model="form.message" class="input"
                          placeholder="{{ __('¿En qué podemos ayudarte? Cuéntanos qué tiendas te interesan, productos que deseas comprar o dudas sobre nuestros servicios.') }}"></textarea>
                @error('form.message') <p class="helper-error">{{ $message }}</p> @enderror
            </div>

            <div class="pt-2">
                <button type="submit" class="btn-primary w-full py-3 text-base shadow-lg shadow-emerald-500/20">
                    <i class="fa-solid fa-paper-plane text-lg"></i>
                    {{ __('Enviar mensaje') }}
                </button>
            </div>
        </form>
    @endif
</div>
