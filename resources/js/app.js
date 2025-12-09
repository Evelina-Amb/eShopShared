import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.store('favorites', {
    ids: [],

    async load() {
        try {
            const res = await fetch('/api/favorites/ids', {
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                },
            });

            if (!res.ok) {
                this.ids = [];
                return;
            }

            this.ids = await res.json();
        } catch (e) {
            console.error('Failed to load favorites', e);
            this.ids = [];
        }
    },

    has(id) {
        return this.ids.includes(id);
    },

    async toggle(listingId) {
        const csrf = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute('content');

        if (!csrf) {
            console.error('CSRF token missing');
            return;
        }

        const options = {
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
        };

        if (this.has(listingId)) {
            await fetch(`/api/favorite/${listingId}`, {
                ...options,
                method: 'DELETE',
            });
        } else {
            await fetch('/api/favorite', {
                ...options,
                method: 'POST',
                headers: {
                    ...options.headers,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ listing_id: listingId }),
            });
        }

        await this.load();
    },
});

Alpine.start();

document.addEventListener('alpine:init', () => {
    Alpine.store('favorites').load();
});
