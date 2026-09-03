{{--
    Toast host. Renders Laravel flash messages and listens for a `cv-toast`
    browser event so Livewire components can push notifications with
    $this->dispatch('cv-toast', type: 'success', message: '…').
--}}
<div x-data="cvToasts()" x-init="init()"
     class="fixed z-[80] inset-x-3 top-3 sm:inset-x-auto sm:right-5 sm:top-5 sm:w-[368px] flex flex-col gap-2.5"
     role="status" aria-live="polite">
    <template x-for="t in items" :key="t.id">
        <div x-show="t.show" x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2 sm:translate-x-3 sm:translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-end="opacity-0 translate-y-1"
             class="card flex items-start gap-3 p-3.5 pr-2.5 shadow-lift">
            <span class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0"
                  :style="t.type === 'success' ? 'background:#e7f8ef;color:#0c7f47'
                        : t.type === 'error'   ? 'background:#fee2e2;color:#b91c1c'
                        : 'background:#ffe4ef;color:#c11062'">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path :d="t.type === 'success' ? 'M4.5 12.75l6 6 9-13.5'
                            : t.type === 'error'   ? 'M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z'
                            : 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'"/>
                </svg>
            </span>
            <p class="text-[13.5px] font-semibold text-ink-900 leading-snug flex-1 pt-1.5" x-text="t.message"></p>
            <button type="button" @click="dismiss(t.id)"
                    class="w-8 h-8 rounded-lg text-ink-400 hover:bg-ink-50 hover:text-ink-700 flex items-center justify-center shrink-0 transition"
                    aria-label="Dismiss notification">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2.4" stroke-linecap="round"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </template>
</div>

<script>
    function cvToasts() {
        return {
            items: [],
            seq: 0,
            init() {
                @if(session('success')) this.push('success', @json(session('success'))); @endif
                @if(session('error'))   this.push('error',   @json(session('error')));   @endif
                @if(session('status'))  this.push('info',    @json(session('status')));  @endif
                window.addEventListener('cv-toast', e =>
                    this.push(e.detail?.type || 'info', e.detail?.message || ''));
            },
            push(type, message) {
                if (!message) return;
                const id = ++this.seq;
                this.items.push({ id, type, message, show: true });
                setTimeout(() => this.dismiss(id), type === 'error' ? 8000 : 5000);
            },
            dismiss(id) {
                const t = this.items.find(i => i.id === id);
                if (t) t.show = false;
                setTimeout(() => { this.items = this.items.filter(i => i.id !== id); }, 200);
            },
        };
    }
</script>
