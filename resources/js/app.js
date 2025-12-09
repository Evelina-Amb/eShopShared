import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.store('favorites', {
    ids: [],

    async load() {
        const res = await fetch('/api/favorites/ids', {
            headers: { 'Accept': 'application/json' }
        });

        if (!res.ok) return;

        this.ids = await res.json();
    },

    has(id) {
        return this.ids.includes(id);
    },

    async toggle(listingId) {
        if (this.has(listingId)) {
            await fetch(`/api/favorite/${listingId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
            });
        } else {
            await fetch('/api/favorite', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({ listing_id: listingId })
            });
        }

        await this.load();
    }
});

Alpine.start();
