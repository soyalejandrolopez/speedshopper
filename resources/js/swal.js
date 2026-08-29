import Swal from 'sweetalert2';

/* ─────────────────────────────────────────
   Locale detection
───────────────────────────────────────── */
const locale = document.documentElement.lang?.startsWith('es') ? 'es' : 'en';

const messages = {
    en: {
        confirm_title:   'Are you sure?',
        confirm_delete:  'This record will be permanently deleted.',
        confirm_btn:     'Yes, delete it',
        cancel_btn:      'Cancel',
        deleted_title:   'Deleted!',
        deleted_text:    'The record has been deleted.',
        saved_title:     'Saved!',
        saved_text:      'The record has been saved successfully.',
        updated_title:   'Updated!',
        updated_text:    'The record has been updated successfully.',
        error_title:     'Oops!',
        error_text:      'Something went wrong. Please try again.',
        validation_title:'Check the form',
        validation_text: 'Please fix the highlighted errors before saving.',
        login_ok_title:  'Welcome!',
        login_ok_text:   'You have signed in successfully.',
        logout_title:    'Signed out',
        logout_text:     'You have been signed out successfully.',
        register_title:  'Account created!',
        register_text:   'Your account has been created. Welcome!',
    },
    es: {
        confirm_title:   '¿Estás seguro?',
        confirm_delete:  'Este registro será eliminado permanentemente.',
        confirm_btn:     'Sí, eliminar',
        cancel_btn:      'Cancelar',
        deleted_title:   '¡Eliminado!',
        deleted_text:    'El registro ha sido eliminado.',
        saved_title:     '¡Guardado!',
        saved_text:      'El registro se guardó correctamente.',
        updated_title:   '¡Actualizado!',
        updated_text:    'El registro se actualizó correctamente.',
        error_title:     '¡Ups!',
        error_text:      'Algo salió mal. Por favor intenta de nuevo.',
        validation_title:'Revisa el formulario',
        validation_text: 'Corrige los errores indicados antes de guardar.',
        login_ok_title:  '¡Bienvenido!',
        login_ok_text:   'Has iniciado sesión correctamente.',
        logout_title:    'Sesión cerrada',
        logout_text:     'Has cerrado sesión correctamente.',
        register_title:  '¡Cuenta creada!',
        register_text:   'Tu cuenta ha sido creada. ¡Bienvenido!',
    },
};

const t = messages[locale];

/* ─────────────────────────────────────────
   Base instances
───────────────────────────────────────── */
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3500,
    timerProgressBar: true,
    customClass: {
        popup: 'swal-toast-popup',
    },
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    },
});

const Confirm = Swal.mixin({
    customClass: {
        popup:          'swal-confirm-popup',
        confirmButton:  'swal-btn-danger',
        cancelButton:   'swal-btn-cancel',
    },
    buttonsStyling: false,
    reverseButtons: true,
    focusCancel: true,
});

/* ─────────────────────────────────────────
   Public helpers
───────────────────────────────────────── */

/** Show success toast */
export function toastSuccess(text = t.saved_text, title = t.saved_title) {
    Toast.fire({ icon: 'success', title, text });
}

/** Show error toast */
export function toastError(text = t.error_text, title = t.error_title) {
    Toast.fire({ icon: 'error', title, text });
}

/** Show info toast */
export function toastInfo(text, title = '') {
    Toast.fire({ icon: 'info', title, text });
}

/** Delete confirmation – returns true if user confirmed */
export async function confirmDelete() {
    const result = await Confirm.fire({
        icon: 'warning',
        title: t.confirm_title,
        text:  t.confirm_delete,
        showCancelButton:   true,
        confirmButtonText:  t.confirm_btn,
        cancelButtonText:   t.cancel_btn,
    });
    return result.isConfirmed;
}

/** Validation errors alert */
export function alertValidation(errors = []) {
    const html = errors.length
        ? `<ul class="text-left text-sm text-red-600 mt-1 space-y-1 list-disc list-inside">${errors.map(e => `<li>${e}</li>`).join('')}</ul>`
        : '';
    Swal.fire({
        icon:  'error',
        title: t.validation_title,
        html:  `<p class="text-sm text-gray-600">${t.validation_text}</p>${html}`,
        confirmButtonText: locale === 'es' ? 'Entendido' : 'Got it',
        customClass: { confirmButton: 'swal-btn-primary' },
        buttonsStyling: false,
    });
}

/** Auth toasts */
export function toastLogin()    { Toast.fire({ icon: 'success', title: t.login_ok_title,   text: t.login_ok_text }); }
export function toastLogout()   { Toast.fire({ icon: 'info',    title: t.logout_title,      text: t.logout_text }); }
export function toastRegister() { Toast.fire({ icon: 'success', title: t.register_title,    text: t.register_text }); }

/* ─────────────────────────────────────────
   Livewire event listeners
   Dispatched from PHP: $this->dispatch('swal.success')
───────────────────────────────────────── */
function bindLivewireEvents() {
    window.addEventListener('swal.success',    (e) => toastSuccess(e.detail?.text, e.detail?.title));
    window.addEventListener('swal.error',      (e) => toastError(e.detail?.text, e.detail?.title));
    window.addEventListener('swal.info',       (e) => toastInfo(e.detail?.text, e.detail?.title));
    window.addEventListener('swal.saved',      ()  => toastSuccess(t.saved_text, t.saved_title));
    window.addEventListener('swal.updated',    ()  => toastSuccess(t.updated_text, t.updated_title));
    window.addEventListener('swal.deleted',    ()  => toastSuccess(t.deleted_text, t.deleted_title));
    window.addEventListener('swal.validation', (e) => alertValidation(e.detail?.errors ?? []));
    window.addEventListener('swal.login',      ()  => toastLogin());
    window.addEventListener('swal.logout',     ()  => toastLogout());
    window.addEventListener('swal.register',   ()  => toastRegister());
}

/* ─────────────────────────────────────────
   Delete confirmation helper (called from blade via x-on:click)
   Usage in blade:
     x-on:click="swalConfirmDelete(() => $wire.delete({{ $id }}))"
───────────────────────────────────────── */
window.swalConfirmDelete = async function (callback) {
    const confirmed = await confirmDelete();
    if (confirmed && typeof callback === 'function') {
        callback();
    }
};

/* ─────────────────────────────────────────
   Boot
───────────────────────────────────────── */
document.addEventListener('livewire:init', () => {
    bindLivewireEvents();
});

// Also bind on SPA navigations
document.addEventListener('livewire:navigated', () => {
    // Handle regular session flash toasts
    const flashEl = document.getElementById('swal-flash');
    if (flashEl) {
        const { type, title, text } = flashEl.dataset;
        if (type === 'success') toastSuccess(text, title);
        else if (type === 'error') toastError(text, title);
        else if (type === 'info') toastInfo(text, title);
        flashEl.remove();
    }

    // Handle auth-specific events (login, register, logout)
    const authFlash = document.getElementById('swal-auth-flash');
    if (authFlash) {
        const event = authFlash.dataset.event;
        if (event) window.dispatchEvent(new CustomEvent(event));
        authFlash.remove();
    }
});

export { Swal, Toast, Confirm, t };
