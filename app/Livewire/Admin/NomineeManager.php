<?php

namespace App\Livewire\Admin;

use App\Livewire\Admin\Concerns\AuthorizesAdminWrites;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Event;
use App\Models\Nominee;
use Livewire\Component;
use Livewire\WithFileUploads;

class NomineeManager extends Component
{
    use AuthorizesAdminWrites;

    use WithFileUploads;

    public Event    $event;
    public Category $category;

    public bool   $showForm    = false;
    public ?int   $editingId   = null;
    public string $name        = '';
    public string $code        = '';
    public string $bio         = '';
    public int    $display_order = 0;
    public        $photo       = null;  // uploaded file

    // CSV import
    public        $csvFile     = null;
    public bool   $showImport  = false;
    public array  $importErrors = [];

    public function mount(Event $event, Category $category): void
    {
        $this->event    = $event;
        $this->category = $category;
    }

    public function openCreate(): void
    {
        $this->authorizeWrite();

        $this->reset('name', 'code', 'bio', 'display_order', 'editingId', 'photo');
        $this->display_order = Nominee::where('category_id', $this->category->id)->max('display_order') + 1;
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->authorizeWrite();

        $nom = Nominee::where('category_id', $this->category->id)->findOrFail($id);
        $this->editingId     = $id;
        $this->name          = $nom->name;
        $this->code          = $nom->code;
        $this->bio           = $nom->bio ?? '';
        $this->display_order = $nom->display_order;
        $this->showForm      = true;
    }

    public function save(): void
    {
        $this->authorizeWrite();

        $this->validate([
            'name'          => 'required|string|max:150',
            'code'          => 'required|string|max:10',
            'bio'           => 'nullable|string|max:1000',
            'display_order' => 'required|integer|min:0',
            'photo'         => 'nullable|image|max:2048',
        ]);

        $photoPath = null;
        if ($this->photo) {
            $photoPath = $this->photo->store('nominees', 'public');
        }

        if ($this->editingId) {
            $nom = Nominee::where('category_id', $this->category->id)->findOrFail($this->editingId);
            $exists = Nominee::where('category_id', $this->category->id)
                ->where('code', $this->code)->where('id', '!=', $this->editingId)->exists();
            if ($exists) { $this->addError('code', 'Code already used in this category.'); return; }

            $data = ['name' => $this->name, 'code' => $this->code, 'bio' => $this->bio, 'display_order' => $this->display_order];
            if ($photoPath) $data['photo_path'] = $photoPath;
            $nom->update($data);
            AuditLog::record('nominee.updated', $nom);
        } else {
            $exists = Nominee::where('category_id', $this->category->id)->where('code', $this->code)->exists();
            if ($exists) { $this->addError('code', 'Code already used in this category.'); return; }

            $nom = Nominee::create([
                'category_id'   => $this->category->id,
                'name'          => $this->name,
                'code'          => $this->code,
                'bio'           => $this->bio,
                'photo_path'    => $photoPath,
                'display_order' => $this->display_order,
            ]);
            AuditLog::record('nominee.created', $nom);
        }

        $this->showForm = false;
        $this->reset('name', 'code', 'bio', 'display_order', 'editingId', 'photo');
    }

    public function delete(int $id): void
    {
        $this->authorizeWrite();

        $nom = Nominee::where('category_id', $this->category->id)->findOrFail($id);
        AuditLog::record('nominee.deleted', $nom, ['name' => $nom->name]);
        $nom->delete();
    }

    public function importCsv(): void
    {
        $this->validate(['csvFile' => 'required|file|mimes:csv,txt|max:2048']);

        $this->importErrors = [];
        $path   = $this->csvFile->getRealPath();
        $handle = fopen($path, 'r');
        $header = fgetcsv($handle); // skip header row
        $row    = 1;
        $imported = 0;

        while (($line = fgetcsv($handle)) !== false) {
            $row++;
            if (count($line) < 2) {
                $this->importErrors[] = "Row {$row}: needs at least 2 columns (name, code).";
                continue;
            }

            [$name, $code, $bio] = array_pad($line, 3, '');
            $name = trim($name);
            $code = trim($code);
            $bio  = trim($bio);

            if (!$name || !$code) {
                $this->importErrors[] = "Row {$row}: name and code are required.";
                continue;
            }

            $exists = Nominee::where('category_id', $this->category->id)->where('code', $code)->exists();
            if ($exists) {
                $this->importErrors[] = "Row {$row}: code '{$code}' already exists — skipped.";
                continue;
            }

            Nominee::create([
                'category_id'   => $this->category->id,
                'name'          => $name,
                'code'          => $code,
                'bio'           => $bio ?: null,
                'display_order' => Nominee::where('category_id', $this->category->id)->max('display_order') + 1,
            ]);
            $imported++;
        }

        fclose($handle);
        AuditLog::record('nominees.csv_imported', $this->category, ['count' => $imported]);
        $this->showImport = false;
        $this->csvFile    = null;

        $this->dispatch('notify', type: 'success', message: "Imported {$imported} nominees. " . count($this->importErrors) . " row(s) skipped.");
    }

    public function render()
    {
        $nominees = Nominee::where('category_id', $this->category->id)
            ->orderBy('display_order')
            ->get();

        return view('livewire.admin.nominee-manager', compact('nominees'));
    }
}
