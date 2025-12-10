import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.store('favorites', {
    ids: [],

    async load() {
        const res = await fetch('/api/favorites/ids', {
            credentials: 'include',
            headers: {
                Accept: 'application/json',
            },
        });

        this.ids = res.ok ? await res.json() : [];
    },

    has(id) {
        return this.ids.includes(id);
    },

    async toggle(listingId) {
        const csrf = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute('content');

        if (!csrf) return;

        const headers = {
            'Content-Type': 'application/json',
            'X-XSRF-TOKEN': csrf, 
            Accept: 'application/json',
        };

        if (this.has(listingId)) {
            await fetch(`/api/favorite/${listingId}`, {
                method: 'DELETE',
                credentials: 'include',
                headers,
            });
        } else {
            await fetch('/api/favorite', {
                method: 'POST',
                credentials: 'include',
                headers,
                body: JSON.stringify({ listing_id: listingId }),
            });
        }

        await this.load();
    },
});

document.addEventListener('alpine:init', () => {
    Alpine.store('favorites').load();
});

Alpine.start();
