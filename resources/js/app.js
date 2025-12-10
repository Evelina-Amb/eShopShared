import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.store('favorites', {
    ids: [],

    async load() {
        try {
            const res = await fetch('/api/favorites/ids', {
                credentials: 'include',
                headers: { Accept: 'application/json' },
            });

            this.ids = res.ok ? await res.json() : [];
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
            console.error('Missing CSRF token');
            return;
        }

        try {
            if (this.has(listingId)) {
                // REMOVE favorite
                await fetch(`/api/favorite/${listingId}`, {
                    method: 'DELETE',
                    credentials: 'include',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                    },
                });
            } else {
                // ADD favorite
                await fetch('/api/favorite', {
                    method: 'POST',
                    credentials: 'include',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ listing_id: listingId }),
                });
            }
        } catch (e) {
            console.error('Favorite toggle failed', e);
        }

        // Refresh IDs after change
        await this.load();
    },
});

document.addEventListener('alpine:init', () => {
    Alpine.store('favorites').load();
});

Alpine.start();
