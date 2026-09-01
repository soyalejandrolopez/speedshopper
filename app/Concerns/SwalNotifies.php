<?php

namespace App\Concerns;

/**
 * Adds SweetAlert2 notification helpers to any Livewire component.
 *
 * Usage:
 *   use App\Concerns\SwalNotifies;
 *   …
 *   $this->swalSuccess();
 *   $this->swalError('Something went wrong');
 *   $this->swalSaved();
 *   $this->swalDeleted();
 */
trait SwalNotifies
{
    /** Fire a custom modal alert. */
    public function swalFire(string $title = '', string $text = '', string $icon = 'success'): void
    {
        $this->dispatch('swal.fire', title: $title, text: $text, icon: $icon);
    }

    /** Fire a success toast. */
    public function swalSuccess(string $text = '', string $title = ''): void
    {
        $this->dispatch('swal.success', text: $text, title: $title);
    }

    /** Fire an error toast. */
    public function swalError(string $text = '', string $title = ''): void
    {
        $this->dispatch('swal.error', text: $text, title: $title);
    }

    /** Fire a warning toast. */
    public function swalWarning(string $text = '', string $title = ''): void
    {
        $this->dispatch('swal.warning', text: $text, title: $title);
    }

    /** Fire an info toast. */
    public function swalInfo(string $text = '', string $title = ''): void
    {
        $this->dispatch('swal.info', text: $text, title: $title);
    }

    /**
     * Fire the "saved" success toast (uses JS locale messages.saved_title/text).
     * Pass a custom $text to override.
     */
    public function swalSaved(string $text = ''): void
    {
        $this->dispatch('swal.saved', text: $text);
    }

    /**
     * Fire the "updated" success toast.
     */
    public function swalUpdated(string $text = ''): void
    {
        $this->dispatch('swal.updated', text: $text);
    }

    /**
     * Fire the "deleted" success toast.
     */
    public function swalDeleted(): void
    {
        $this->dispatch('swal.deleted');
    }

    /**
     * Fire a validation-error alert with a list of errors.
     *
     * @param  string[]  $errors
     */
    public function swalValidation(array $errors = []): void
    {
        $this->dispatch('swal.validation', errors: $errors);
    }
}
