<?php

namespace App\Livewire\Admin\Concerns;

/**
 * Guards write actions on admin Livewire components.
 *
 * Livewire actions are callable directly from the browser, so a read-only
 * `viewer` reaching a management screen could otherwise invoke save() or
 * delete() even though the UI hides the buttons. Every mutating action must
 * call this; reads stay open so viewers can still see the data.
 */
trait AuthorizesAdminWrites
{
    protected function authorizeWrite(): void
    {
        abort_unless(auth('admin')->user()?->isManager(), 403);
    }

    /** Whether the current admin may mutate — used to hide UI controls. */
    public function canWrite(): bool
    {
        return (bool) auth('admin')->user()?->isManager();
    }
}
