import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

/**
 * Favorites store
 * - loads favorite listing IDs from DB
 * - toggles favorite state
 */
Alpine.store('favorites', {
    ids: [],

    async load() {
        try {
            const res = await fetch('/api/favorites/ids', {
                credentials: 'include',
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
            console.error('Favorites load failed', e);
            this.ids = [];
        }
    },

    has(listingId) {
        return this.ids.includes(listingId);
    },

    async toggle(listingId) {
        const csrf = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute('content');

        if (!csrf) return;

        const options = {
            credentials: 'include',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
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

/**
 * Init Alpine
 */
document.addEventListener('alpine:init', () => {
    Alpine.store('favorites').load();
});

Alpine.start();
