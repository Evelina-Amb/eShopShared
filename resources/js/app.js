import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.store('favorites', {
    ids: [],

    async load() {
        try {
            const res = await fetch('/api/favorites/ids', {
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

        if (this.has(listingId)) {
            // REMOVE favorite
            await fetch(`/api/favorite/${listingId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                },
            });
        } else {
            // ADD favorite
            await fetch('/api/favorite', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    listing_id: listingId,
                }),
            });
        }

        await this.load();
    },
});

Alpine.start();

// Load favorites once Alpine is ready
document.addEventListener('alpine:init', () => {
    Alpine.store('favorites').load();
});
