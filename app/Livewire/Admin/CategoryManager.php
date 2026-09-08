<?php

namespace App\Livewire\Admin;

use App\Livewire\Admin\Concerns\AuthorizesAdminWrites;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Event;
use Livewire\Component;

class CategoryManager extends Component
{
    use AuthorizesAdminWrites;

    public Event $event;

    public bool   $showForm   = false;
    public ?int   $editingId  = null;
    public string $name       = '';
    public string $code       = '';
    public int    $display_order = 0;

    public function mount(Event $event): void
    {
        $this->event = $event;
    }

    public function openCreate(): void
    {
        $this->authorizeWrite();

        $this->reset('name', 'code', 'display_order', 'editingId');
        $this->display_order = $this->event->categories()->max('display_order') + 1;
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->authorizeWrite();

        $cat = Category::where('event_id', $this->event->id)->findOrFail($id);
        $this->editingId      = $id;
        $this->name           = $cat->name;
        $this->code           = $cat->code;
        $this->display_order  = $cat->display_order;
        $this->showForm       = true;
    }

    public function save(): void
    {
        $this->authorizeWrite();

        $this->validate([
            'name'          => 'required|string|max:150',
            'code'          => 'required|string|max:10',
            'display_order' => 'required|integer|min:0',
        ]);

        if ($this->editingId) {
            $cat = Category::where('event_id', $this->event->id)->findOrFail($this->editingId);

            // Ensure code unique per event (excluding self)
            $exists = Category::where('event_id', $this->event->id)
                ->where('code', $this->code)
                ->where('id', '!=', $this->editingId)
                ->exists();

            if ($exists) { $this->addError('code', 'This code is already used in this event.'); return; }

            $cat->update(['name' => $this->name, 'code' => $this->code, 'display_order' => $this->display_order]);
            AuditLog::record('category.updated', $cat);
        } else {
            $exists = Category::where('event_id', $this->event->id)->where('code', $this->code)->exists();
            if ($exists) { $this->addError('code', 'This code is already used in this event.'); return; }

            $cat = Category::create([
                'event_id'      => $this->event->id,
                'name'          => $this->name,
                'code'          => $this->code,
                'display_order' => $this->display_order,
            ]);
            AuditLog::record('category.created', $cat);
        }

        $this->showForm = false;
        $this->reset('name', 'code', 'display_order', 'editingId');
    }

    public function delete(int $id): void
    {
        $this->authorizeWrite();

        $cat = Category::where('event_id', $this->event->id)->findOrFail($id);
        AuditLog::record('category.deleted', $cat, ['name' => $cat->name]);
        $cat->delete();
    }

    public function render()
    {
        $categories = Category::where('event_id', $this->event->id)
            ->withCount('nominees')
            ->orderBy('display_order')
            ->get();

        return view('livewire.admin.category-manager', compact('categories'));
    }
}
