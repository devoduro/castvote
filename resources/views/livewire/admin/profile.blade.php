<div>
    <div style="margin-bottom:28px">
        <h1 style="font-size:22px;font-weight:800;color:#1a0030;margin-bottom:4px">Profile &amp; Settings</h1>
        <p style="color:#9ca3af;font-size:13.5px">Manage your account and organization details.</p>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px">

        {{-- Account Details --}}
        <div style="background:white;border:1px solid #e5e7eb;border-radius:16px;padding:24px">
            <h2 style="font-size:15px;font-weight:800;color:#1a0030;margin-bottom:4px">Account Details</h2>
            <p style="color:#9ca3af;font-size:13px;margin-bottom:20px">Your personal account information.</p>

            @if(session('profile_success'))
            <div style="background:#f0fdf4;border:1.5px solid #bbf7d0;color:#166534;border-radius:10px;padding:12px 16px;font-size:13px;margin-bottom:16px">
                ✓ {{ session('profile_success') }}
            </div>
            @endif

            <div style="display:flex;flex-direction:column;gap:14px">
                <div>
                    <label style="display:block;font-size:12.5px;font-weight:600;color:#374151;margin-bottom:5px">Full Name</label>
                    <input wire:model="name" type="text"
                           style="width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:10px 14px;font-size:13.5px;outline:none;color:#1a0030;background:#f9fafb"
                           onfocus="this.style.borderColor='#e91e8c'" onblur="this.style.borderColor='#e5e7eb'">
                    @error('name')<p style="color:#ef4444;font-size:12px;margin-top:3px">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label style="display:block;font-size:12.5px;font-weight:600;color:#374151;margin-bottom:5px">Email Address</label>
                    <input wire:model="email" type="email"
                           style="width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:10px 14px;font-size:13.5px;outline:none;color:#1a0030;background:#f9fafb"
                           onfocus="this.style.borderColor='#e91e8c'" onblur="this.style.borderColor='#e5e7eb'">
                    @error('email')<p style="color:#ef4444;font-size:12px;margin-top:3px">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label style="display:block;font-size:12.5px;font-weight:600;color:#374151;margin-bottom:5px">Phone Number</label>
                    <input wire:model="phone" type="tel"
                           style="width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:10px 14px;font-size:13.5px;outline:none;color:#1a0030;background:#f9fafb"
                           onfocus="this.style.borderColor='#e91e8c'" onblur="this.style.borderColor='#e5e7eb'">
                </div>
                <button wire:click="saveAccount" wire:loading.attr="disabled"
                        style="background:linear-gradient(135deg,#2d0050,#3b0068);color:white;border:none;border-radius:10px;padding:11px;font-size:14px;font-weight:700;cursor:pointer">
                    Save Account Details
                </button>
            </div>
        </div>

        {{-- Organization Details --}}
        <div style="background:white;border:1px solid #e5e7eb;border-radius:16px;padding:24px">
            <h2 style="font-size:15px;font-weight:800;color:#1a0030;margin-bottom:4px">Organization</h2>
            <p style="color:#9ca3af;font-size:13px;margin-bottom:20px">Your organization's public information.</p>

            @if(session('org_success'))
            <div style="background:#f0fdf4;border:1.5px solid #bbf7d0;color:#166534;border-radius:10px;padding:12px 16px;font-size:13px;margin-bottom:16px">
                ✓ {{ session('org_success') }}
            </div>
            @endif

            <div style="display:flex;flex-direction:column;gap:14px">
                <div>
                    <label style="display:block;font-size:12.5px;font-weight:600;color:#374151;margin-bottom:5px">Organization Name</label>
                    <input wire:model="orgName" type="text"
                           style="width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:10px 14px;font-size:13.5px;outline:none;color:#1a0030;background:#f9fafb"
                           onfocus="this.style.borderColor='#e91e8c'" onblur="this.style.borderColor='#e5e7eb'">
                    @error('orgName')<p style="color:#ef4444;font-size:12px;margin-top:3px">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label style="display:block;font-size:12.5px;font-weight:600;color:#374151;margin-bottom:5px">Contact Email</label>
                    <input wire:model="orgEmail" type="email"
                           style="width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:10px 14px;font-size:13.5px;outline:none;color:#1a0030;background:#f9fafb"
                           onfocus="this.style.borderColor='#e91e8c'" onblur="this.style.borderColor='#e5e7eb'">
                </div>
                <div>
                    <label style="display:block;font-size:12.5px;font-weight:600;color:#374151;margin-bottom:5px">Website</label>
                    <input wire:model="orgWebsite" type="url" placeholder="https://"
                           style="width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:10px 14px;font-size:13.5px;outline:none;color:#1a0030;background:#f9fafb"
                           onfocus="this.style.borderColor='#e91e8c'" onblur="this.style.borderColor='#e5e7eb'">
                </div>
                <div>
                    <label style="display:block;font-size:12.5px;font-weight:600;color:#374151;margin-bottom:5px">Phone</label>
                    <input wire:model="orgPhone" type="tel"
                           style="width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:10px 14px;font-size:13.5px;outline:none;color:#1a0030;background:#f9fafb"
                           onfocus="this.style.borderColor='#e91e8c'" onblur="this.style.borderColor='#e5e7eb'">
                </div>
                <button wire:click="saveOrganization" wire:loading.attr="disabled"
                        style="background:linear-gradient(135deg,#2d0050,#3b0068);color:white;border:none;border-radius:10px;padding:11px;font-size:14px;font-weight:700;cursor:pointer">
                    Save Organization
                </button>
            </div>
        </div>

        {{-- Change Password --}}
        <div style="background:white;border:1px solid #e5e7eb;border-radius:16px;padding:24px">
            <h2 style="font-size:15px;font-weight:800;color:#1a0030;margin-bottom:4px">Change Password</h2>
            <p style="color:#9ca3af;font-size:13px;margin-bottom:20px">Use a strong password of at least 8 characters.</p>

            @if(session('password_success'))
            <div style="background:#f0fdf4;border:1.5px solid #bbf7d0;color:#166534;border-radius:10px;padding:12px 16px;font-size:13px;margin-bottom:16px">
                ✓ {{ session('password_success') }}
            </div>
            @endif

            <div style="display:flex;flex-direction:column;gap:14px">
                <div>
                    <label style="display:block;font-size:12.5px;font-weight:600;color:#374151;margin-bottom:5px">Current Password</label>
                    <input wire:model="currentPassword" type="password"
                           style="width:100%;border:1.5px solid {{ $errors->has('currentPassword') ? '#ef4444' : '#e5e7eb' }};border-radius:10px;padding:10px 14px;font-size:13.5px;outline:none;color:#1a0030;background:#f9fafb">
                    @error('currentPassword')<p style="color:#ef4444;font-size:12px;margin-top:3px">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label style="display:block;font-size:12.5px;font-weight:600;color:#374151;margin-bottom:5px">New Password</label>
                    <input wire:model="newPassword" type="password"
                           style="width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:10px 14px;font-size:13.5px;outline:none;color:#1a0030;background:#f9fafb">
                    @error('newPassword')<p style="color:#ef4444;font-size:12px;margin-top:3px">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label style="display:block;font-size:12.5px;font-weight:600;color:#374151;margin-bottom:5px">Confirm New Password</label>
                    <input wire:model="confirmPassword" type="password"
                           style="width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:10px 14px;font-size:13.5px;outline:none;color:#1a0030;background:#f9fafb">
                </div>
                <button wire:click="changePassword" wire:loading.attr="disabled"
                        style="background:#fee2e2;color:#dc2626;border:none;border-radius:10px;padding:11px;font-size:14px;font-weight:700;cursor:pointer">
                    Update Password
                </button>
            </div>
        </div>

        {{-- Account Info card --}}
        <div style="background:white;border:1px solid #e5e7eb;border-radius:16px;padding:24px">
            <h2 style="font-size:15px;font-weight:800;color:#1a0030;margin-bottom:16px">Account Status</h2>
            @php $admin = auth('admin')->user(); @endphp

            <div style="display:flex;align-items:center;gap:14px;margin-bottom:20px;padding:16px;background:#f9fafb;border-radius:12px">
                <div style="width:52px;height:52px;border-radius:14px;background:linear-gradient(135deg,#e91e8c,#7c3aed);display:flex;align-items:center;justify-content:center;color:white;font-size:18px;font-weight:800;flex-shrink:0">
                    {{ strtoupper(substr($admin->name,0,2)) }}
                </div>
                <div>
                    <p style="font-weight:700;color:#1a0030;font-size:15px">{{ $admin->name }}</p>
                    <p style="color:#9ca3af;font-size:13px">{{ $admin->email }}</p>
                </div>
            </div>

            <div style="display:flex;flex-direction:column;gap:12px">
                @foreach([
                    ['Role',            ucfirst($admin->role)],
                    ['Account Status',  ucfirst($admin->account_status)],
                    ['Organization',    $admin->organization?->name ?? '—'],
                    ['Member Since',    $admin->created_at->format('d M Y')],
                ] as [$label, $val])
                <div style="display:flex;justify-content:space-between;align-items:center;padding-bottom:12px;border-bottom:1px solid #f3f4f6">
                    <p style="font-size:13px;color:#6b7280">{{ $label }}</p>
                    <p style="font-size:13.5px;font-weight:600;color:#1a0030">{{ $val }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
