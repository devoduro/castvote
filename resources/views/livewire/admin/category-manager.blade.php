<div>
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-700">Categories</h3>
        <button wire:click="openCreate"
                class="bg-orange-600 hover:bg-orange-700 text-white text-sm px-3 py-1.5 rounded-lg font-medium">
            + Add Category
        </button>
    </div>

    @if($showForm)
    <div class="bg-orange-50 border border-orange-200 rounded-xl p-5 mb-4 space-y-3">
        <h4 class="font-medium text-gray-700">{{ $editingId ? 'Edit Category' : 'New Category' }}</h4>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="text-xs font-medium text-gray-600 mb-1 block">Name</label>
                <input wire:model="name" type="text" placeholder="Artiste of the Year"
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
        <div class="flex gap-2">
            <button wire:click="save" class="bg-orange-600 text-white text-sm px-4 py-1.5 rounded-lg font-medium hover:bg-orange-700">Save</button>
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
                    <th class="text-right px-4 py-3">Nominees</th>
                    <th class="text-right px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($categories as $cat)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono font-bold text-orange-600">{{ $cat->code }}</td>
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $cat->name }}</td>
                    <td class="px-4 py-3 text-right text-gray-500">{{ $cat->nominees_count }}</td>
                    <td class="px-4 py-3 text-right space-x-2">
                        <a href="{{ route('admin.events.nominees', [$event, $cat]) }}" class="text-blue-600 hover:underline text-xs">Nominees</a>
                        <button wire:click="openEdit({{ $cat->id }})" class="text-gray-500 hover:underline text-xs">Edit</button>
                        <button wire:click="delete({{ $cat->id }})"
                                wire:confirm="Delete '{{ $cat->name }}'? This removes all nominees and votes too."
                                class="text-red-500 hover:underline text-xs">Delete</button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">No categories yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
