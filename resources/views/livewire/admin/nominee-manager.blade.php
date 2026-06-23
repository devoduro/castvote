<div>
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:20px">
        <div>
            <h3 style="font-size:17px;font-weight:800;color:#1a0030">{{ $category->name }} — Nominees</h3>
            <p style="color:#9ca3af;font-size:13px;margin-top:2px">
                Category code: <span style="font-family:monospace;font-weight:700;color:#7c3aed">{{ $category->code }}</span>
            </p>
        </div>
        <div style="display:flex;gap:8px">
            <button wire:click="$set('showImport', true)"
                    style="display:inline-flex;align-items:center;gap:6px;border:1.5px solid #e5e7eb;color:#6b7280;border-radius:10px;padding:9px 16px;font-size:13px;font-weight:600;cursor:pointer;background:white">
                CSV Import
            </button>
            <button wire:click="openCreate"
                    style="display:inline-flex;align-items:center;gap:6px;background:linear-gradient(135deg,#2d0050,#3b0068);color:white;border:none;border-radius:10px;padding:10px 18px;font-size:13px;font-weight:700;cursor:pointer;box-shadow:0 4px 14px rgba(45,0,80,.25)">
                <svg style="width:14px;height:14px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Add Nominee
            </button>
        </div>
    </div>

    {{-- CSV Import panel --}}
    @if($showImport)
    <div style="background:#eff6ff;border:1.5px solid #bfdbfe;border-radius:14px;padding:20px;margin-bottom:20px">
        <h4 style="font-weight:700;color:#1e40af;font-size:14px;margin-bottom:6px">Import Nominees via CSV</h4>
        <p style="font-size:12px;color:#6b7280;margin-bottom:12px">CSV format: <code style="background:white;border:1px solid #e5e7eb;padding:2px 6px;border-radius:4px;font-family:monospace">name, code, bio (optional)</code> — first row is header.</p>
        <input wire:model="csvFile" type="file" accept=".csv,.txt"
               style="display:block;margin-bottom:10px;font-size:13px;color:#374151">
        @error('csvFile')<p style="color:#ef4444;font-size:12px;margin-bottom:8px">{{ $message }}</p>@enderror
        @if(!empty($importErrors))
        <ul style="color:#dc2626;font-size:12px;margin-bottom:10px;padding-left:16px">
            @foreach($importErrors as $err)<li>{{ $err }}</li>@endforeach
        </ul>
        @endif
        <div style="display:flex;gap:8px">
            <button wire:click="importCsv" wire:loading.attr="disabled"
                    style="background:#1d4ed8;color:white;border:none;border-radius:10px;padding:9px 20px;font-size:13px;font-weight:700;cursor:pointer">
                <span wire:loading.remove>Import</span><span wire:loading>Importing…</span>
            </button>
            <button wire:click="$set('showImport', false)"
                    style="background:none;border:1.5px solid #e5e7eb;color:#6b7280;border-radius:10px;padding:9px 16px;font-size:13px;cursor:pointer">
                Cancel
            </button>
        </div>
    </div>
    @endif

    {{-- Nominee form --}}
    @if($showForm)
    <div style="background:#fdf4ff;border:1.5px solid #e9d5ff;border-radius:14px;padding:20px;margin-bottom:20px">
        <h4 style="font-weight:700;color:#1a0030;font-size:14px;margin-bottom:14px">{{ $editingId ? 'Edit Nominee' : 'New Nominee' }}</h4>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px">
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px">Name / Stage Name</label>
                <input wire:model="name" type="text" placeholder="Sarkodie"
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
        <div style="margin-bottom:12px">
            <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px">Bio <span style="color:#9ca3af;font-weight:400">(optional)</span></label>
            <textarea wire:model="bio" rows="2" placeholder="Short bio…"
                      style="width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:9px 12px;font-size:13.5px;outline:none;color:#1a0030;background:white;resize:vertical"
                      onfocus="this.style.borderColor='#e91e8c'" onblur="this.style.borderColor='#e5e7eb'"></textarea>
        </div>
        <div style="margin-bottom:14px">
            <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px">Photo <span style="color:#9ca3af;font-weight:400">(optional)</span></label>
            <input wire:model="photo" type="file" accept="image/*" style="font-size:13px;color:#374151">
            @error('photo')<p style="color:#ef4444;font-size:12px;margin-top:3px">{{ $message }}</p>@enderror
        </div>
        <div style="display:flex;gap:8px">
            <button wire:click="save" wire:loading.attr="disabled"
                    style="background:linear-gradient(135deg,#e91e8c,#c2185b);color:white;border:none;border-radius:10px;padding:9px 20px;font-size:13px;font-weight:700;cursor:pointer">
                <span wire:loading.remove>Save</span><span wire:loading>Saving…</span>
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
                    @foreach(['Code','Nominee','Bio','Actions'] as $h)
                    <th style="padding:11px 20px;text-align:{{ $h==='Actions'?'right':'left' }};font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($nominees as $nom)
                <tr style="border-bottom:1px solid #f9fafb" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
                    <td style="padding:14px 20px">
                        <span style="background:#f3f0ff;color:#7c3aed;padding:3px 10px;border-radius:8px;font-family:monospace;font-size:13px;font-weight:800">{{ $nom->code }}</span>
                    </td>
                    <td style="padding:14px 20px">
                        <div style="display:flex;align-items:center;gap:10px">
                            @if($nom->photo_path)
                            <img src="{{ asset('storage/'.$nom->photo_path) }}" style="width:34px;height:34px;border-radius:8px;object-fit:cover;flex-shrink:0">
                            @else
                            <div style="width:34px;height:34px;border-radius:8px;background:linear-gradient(135deg,#e91e8c20,#7c3aed20);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:12px;font-weight:700;color:#7c3aed">
                                {{ strtoupper(substr($nom->name,0,2)) }}
                            </div>
                            @endif
                            <span style="font-weight:600;color:#1a0030;font-size:14px">{{ $nom->name }}</span>
                        </div>
                    </td>
                    <td style="padding:14px 20px;color:#9ca3af;font-size:12.5px;max-width:240px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                        {{ Str::limit($nom->bio, 80) }}
                    </td>
                    <td style="padding:14px 20px;text-align:right">
                        <div style="display:flex;gap:10px;align-items:center;justify-content:flex-end">
                            <button wire:click="openEdit({{ $nom->id }})"
                                    style="font-size:12.5px;font-weight:600;color:#6b7280;background:none;border:none;cursor:pointer;padding:0">Edit</button>
                            <span style="color:#e5e7eb">·</span>
                            <button wire:click="delete({{ $nom->id }})"
                                    wire:confirm="Delete {{ $nom->name }}? This also removes their votes."
                                    style="font-size:12.5px;font-weight:600;color:#dc2626;background:none;border:none;cursor:pointer;padding:0">Delete</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="padding:56px;text-align:center">
                        <svg style="width:40px;height:40px;color:#e5e7eb;margin:0 auto 12px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <p style="color:#6b7280;font-weight:600;font-size:14px">No nominees yet.</p>
                        <p style="color:#9ca3af;font-size:13px;margin-top:4px">Add one or import via CSV.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
