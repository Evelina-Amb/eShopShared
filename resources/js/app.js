import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.store('favorites', {
    ids: [],

    async load() {
        const res = await fetch('/api/favorites/ids', {
            credentials: 'include',
            headers: { Accept: 'application/json' },
        });

        this.ids = res.ok ? await res.json() : [];
    },

    has(id) {
        return this.ids.includes(id);
    },

    async toggle(listingId) {
        const csrf = document
            .querySelector('meta[name="csrf-token"]')
            .content;

        const res = await fetch('/api/favorite', {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ listing_id: listingId }),
        });

        if (!res.ok) {
            console.error('Favorite toggle failed');
            return;
        }

        await this.load();
    },
});

document.addEventListener('alpine:init', () => {
    Alpine.store('favorites').load();
});

Alpine.start();
