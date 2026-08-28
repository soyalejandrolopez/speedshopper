<?php

namespace App\Livewire\Admin\Inquiries;

use App\Models\ContactInquiry;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Mensajes de Contacto')]
class InquiriesIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = '';

    public ?int $selectedInquiryId = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function openInquiry(int $id): void
    {
        $this->selectedInquiryId = $id;
        $inquiry = ContactInquiry::find($id);
        if ($inquiry && $inquiry->isUnread()) {
            $inquiry->markAsRead();
        }
    }

    public function closeInquiry(): void
    {
        $this->selectedInquiryId = null;
    }

    public function markAsRead(int $id): void
    {
        $inquiry = ContactInquiry::find($id);
        if ($inquiry) {
            $inquiry->markAsRead();
            session()->flash('toast', [
                'type' => 'success',
                'message' => __('Mensaje marcado como leído.'),
            ]);
        }
    }

    public function markAsContacted(int $id): void
    {
        $inquiry = ContactInquiry::find($id);
        if ($inquiry) {
            $inquiry->markAsContacted();
            session()->flash('toast', [
                'type' => 'success',
                'message' => __('Mensaje marcado como atendido.'),
            ]);
        }
    }

    public function delete(int $id): void
    {
        $inquiry = ContactInquiry::find($id);
        if ($inquiry) {
            $inquiry->delete();
            if ($this->selectedInquiryId === $id) {
                $this->selectedInquiryId = null;
            }
            session()->flash('toast', [
                'type' => 'success',
                'message' => __('Mensaje eliminado correctamente.'),
            ]);
        }
    }

    public function render(): View
    {
        $inquiries = ContactInquiry::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('phone', 'like', "%{$this->search}%")
                        ->orWhere('message', 'like', "%{$this->search}%");
                });
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->latest()
            ->paginate(15);

        $selectedInquiry = $this->selectedInquiryId
            ? ContactInquiry::find($this->selectedInquiryId)
            : null;

        return view('livewire.admin.inquiries.inquiries-index', [
            'inquiries' => $inquiries,
            'selectedInquiry' => $selectedInquiry,
            'unreadCount' => ContactInquiry::unread()->count(),
            'totalCount' => ContactInquiry::count(),
        ]);
    }
}
