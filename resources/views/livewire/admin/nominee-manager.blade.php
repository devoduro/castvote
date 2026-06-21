<div>
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-700">{{ $category->name }} — Nominees</h3>
            <p class="text-xs text-gray-400">Category code: <span class="font-mono font-bold text-orange-600">{{ $category->code }}</span></p>
        </div>
        <div class="flex gap-2">
            <button wire:click="$set('showImport', true)"
                    class="border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm px-3 py-1.5 rounded-lg font-medium">
                CSV Import
            </button>
            <button wire:click="openCreate"
                    class="bg-orange-600 hover:bg-orange-700 text-white text-sm px-3 py-1.5 rounded-lg font-medium">
                + Add Nominee
            </button>
        </div>
    </div>

    {{-- CSV Import panel --}}
    @if($showImport)
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 mb-4 space-y-3">
        <h4 class="font-medium text-gray-700">Import Nominees via CSV</h4>
        <p class="text-xs text-gray-500">CSV format: <code class="bg-white px-1 rounded border">name, code, bio (optional)</code> — first row is header.</p>
        <input wire:model="csvFile" type="file" accept=".csv,.txt"
               class="block w-full text-sm text-gray-600 file:mr-3 file:py-1 file:px-3 file:rounded file:border file:border-gray-300 file:text-sm file:bg-white">
        @error('csvFile') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror

        @if(!empty($importErrors))
        <ul class="text-xs text-red-600 space-y-0.5">
            @foreach($importErrors as $err) <li>{{ $err }}</li> @endforeach
        </ul>
        @endif

        <div class="flex gap-2">
            <button wire:click="importCsv" wire:loading.attr="disabled"
                    class="bg-blue-600 text-white text-sm px-4 py-1.5 rounded-lg font-medium hover:bg-blue-700 disabled:opacity-50">
                <span wire:loading.remove>Import</span><span wire:loading>Importing…</span>
            </button>
            <button wire:click="$set('showImport', false)" class="text-gray-500 text-sm px-3 py-1.5 hover:text-gray-700">Cancel</button>
        </div>
    </div>
    @endif

    {{-- Nominee form --}}
    @if($showForm)
    <div class="bg-orange-50 border border-orange-200 rounded-xl p-5 mb-4 space-y-3">
        <h4 class="font-medium text-gray-700">{{ $editingId ? 'Edit Nominee' : 'New Nominee' }}</h4>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="text-xs font-medium text-gray-600 mb-1 block">Name / Stage Name</label>
                <input wire:model="name" type="text" placeholder="Sarkodie"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 outline-none">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-xs font-medium text-gray-600 mb-1 block">Code (voters type this)</label>
                <input wire:model="code" type="text" placeholder="01" maxlength="10"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-orange-500 outline-none">
                @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
        <div>
            <label class="text-xs font-medium text-gray-600 mb-1 block">Bio (optional)</label>
            <textarea wire:model="bio" rows="2" placeholder="Short bio…"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 outline-none"></textarea>
        </div>
        <div>
            <label class="text-xs font-medium text-gray-600 mb-1 block">Photo (optional)</label>
            <input wire:model="photo" type="file" accept="image/*"
                   class="block w-full text-sm text-gray-600 file:mr-3 file:py-1 file:px-3 file:rounded file:border file:border-gray-300 file:text-sm file:bg-white">
            @error('photo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="flex gap-2">
            <button wire:click="save" wire:loading.attr="disabled"
                    class="bg-orange-600 text-white text-sm px-4 py-1.5 rounded-lg font-medium hover:bg-orange-700 disabled:opacity-50">
                <span wire:loading.remove>Save</span><span wire:loading>Saving…</span>
            </button>
            <button wire:click="$set('showForm', false)" class="text-gray-500 text-sm px-3 py-1.5 hover:text-gray-700">Cancel</button>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-400 uppercase text-xs">
                <tr>
                    <th class="text-left px-4 py-3 w-16">Code</th>
                    <th class="text-left px-4 py-3">Name</th>
                    <th class="text-left px-4 py-3">Bio</th>
                    <th class="text-right px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($nominees as $nom)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono font-bold text-orange-600">{{ $nom->code }}</td>
                    <td class="px-4 py-3 font-medium text-gray-800">
                        @if($nom->photo_path)
                        <img src="{{ asset('storage/'.$nom->photo_path) }}" class="w-7 h-7 rounded-full object-cover inline mr-2">
                        @endif
                        {{ $nom->name }}
                    </td>
                    <td class="px-4 py-3 text-gray-400 text-xs truncate max-w-xs">{{ Str::limit($nom->bio, 80) }}</td>
                    <td class="px-4 py-3 text-right space-x-2">
                        <button wire:click="openEdit({{ $nom->id }})" class="text-gray-500 hover:underline text-xs">Edit</button>
                        <button wire:click="delete({{ $nom->id }})"
                                wire:confirm="Delete {{ $nom->name }}? This also removes their votes."
                                class="text-red-500 hover:underline text-xs">Delete</button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">No nominees yet. Add one or import via CSV.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
