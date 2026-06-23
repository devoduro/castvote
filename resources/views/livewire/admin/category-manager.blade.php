<div>
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
        <div>
            <h3 style="font-size:17px;font-weight:800;color:#1a0030">Categories</h3>
            <p style="color:#9ca3af;font-size:13px;margin-top:2px">Manage voting categories for this event.</p>
        </div>
        <button wire:click="openCreate"
                style="display:inline-flex;align-items:center;gap:6px;background:linear-gradient(135deg,#2d0050,#3b0068);color:white;border:none;border-radius:10px;padding:10px 18px;font-size:13px;font-weight:700;cursor:pointer;box-shadow:0 4px 14px rgba(45,0,80,.25)">
            <svg style="width:14px;height:14px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Add Category
        </button>
    </div>

    @if($showForm)
    <div style="background:#fdf4ff;border:1.5px solid #e9d5ff;border-radius:14px;padding:20px;margin-bottom:20px">
        <h4 style="font-weight:700;color:#1a0030;font-size:14px;margin-bottom:14px">{{ $editingId ? 'Edit Category' : 'New Category' }}</h4>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px">
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px">Name</label>
                <input wire:model="name" type="text" placeholder="Artiste of the Year"
                       style="width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:9px 12px;font-size:13.5px;outline:none;color:#1a0030;background:white"
                       onfocus="this.style.borderColor='#e91e8c'" onblur="this.style.borderColor='#e5e7eb'">
                @error('name')<p style="color:#ef4444;font-size:12px;margin-top:3px">{{ $message }}</p>@enderror
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px">Code <span style="color:#9ca3af;font-weight:400">(voters type this)</span></label>
                <input wire:model="code" type="text" placeholder="01" maxlength="10"
                       style="width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:9px 12px;font-size:13.5px;font-family:monospace;outline:none;color:#1a0030;background:white"
                       onfocus="this.style.borderColor='#e91e8c'" onblur="this.style.borderColor='#e5e7eb'">
                @error('code')<p style="color:#ef4444;font-size:12px;margin-top:3px">{{ $message }}</p>@enderror
            </div>
        </div>
        <div style="display:flex;gap:8px">
            <button wire:click="save"
                    style="background:linear-gradient(135deg,#e91e8c,#c2185b);color:white;border:none;border-radius:10px;padding:9px 20px;font-size:13px;font-weight:700;cursor:pointer">
                Save
            </button>
            <button wire:click="$set('showForm', false)"
                    style="background:none;border:1.5px solid #e5e7eb;color:#6b7280;border-radius:10px;padding:9px 16px;font-size:13px;cursor:pointer">
                Cancel
            </button>
        </div>
    </div>
    @endif

    <div style="background:white;border:1px solid #e5e7eb;border-radius:16px;overflow:hidden">
        <table style="width:100%;border-collapse:collapse">
            <thead>
                <tr style="background:#fafafa;border-bottom:1px solid #f3f4f6">
                    @foreach(['Code','Category Name','Nominees','Actions'] as $h)
                    <th style="padding:11px 20px;text-align:{{ $h==='Nominees'||$h==='Actions'?'right':'left' }};font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $cat)
                <tr style="border-bottom:1px solid #f9fafb" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
                    <td style="padding:14px 20px">
                        <span style="background:#f3f0ff;color:#7c3aed;padding:3px 10px;border-radius:8px;font-family:monospace;font-size:13px;font-weight:800">{{ $cat->code }}</span>
                    </td>
                    <td style="padding:14px 20px;font-weight:600;color:#1a0030;font-size:14px">{{ $cat->name }}</td>
                    <td style="padding:14px 20px;text-align:right;color:#6b7280;font-size:13.5px">{{ $cat->nominees_count }}</td>
                    <td style="padding:14px 20px;text-align:right">
                        <div style="display:flex;gap:10px;align-items:center;justify-content:flex-end">
                            <a href="{{ route('admin.events.nominees', [$event, $cat]) }}"
                               style="font-size:12.5px;font-weight:600;color:#7c3aed;text-decoration:none">Nominees</a>
                            <span style="color:#e5e7eb">·</span>
                            <button wire:click="openEdit({{ $cat->id }})"
                                    style="font-size:12.5px;font-weight:600;color:#6b7280;background:none;border:none;cursor:pointer;padding:0">Edit</button>
                            <span style="color:#e5e7eb">·</span>
                            <button wire:click="delete({{ $cat->id }})"
                                    wire:confirm="Delete '{{ $cat->name }}'? This removes all nominees and votes too."
                                    style="font-size:12.5px;font-weight:600;color:#dc2626;background:none;border:none;cursor:pointer;padding:0">Delete</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="padding:56px;text-align:center">
                        <svg style="width:40px;height:40px;color:#e5e7eb;margin:0 auto 12px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <p style="color:#6b7280;font-weight:600;font-size:14px">No categories yet.</p>
                        <p style="color:#9ca3af;font-size:13px;margin-top:4px">Click "Add Category" to get started.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
