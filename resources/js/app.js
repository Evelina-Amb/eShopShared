import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.store('favorites', {
    list: JSON.parse(localStorage.getItem('favorites') || '[]'),

    toggle(id) {
        if (this.list.includes(id)) {
            this.list = this.list.filter(x => x !== id);
        } else {
            this.list.push(id);
        }
        this.save();
    },

    save() {
        localStorage.setItem('favorites', JSON.stringify(this.list));
    }
});

Alpine.start();
