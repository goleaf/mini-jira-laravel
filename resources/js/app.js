import './bootstrap';
import { Tooltip } from 'bootstrap';

const handleTooltip = (el, action) => {
    if (el instanceof Element && el.getAttribute('data-bs-toggle') === 'tooltip') {
        action === 'create' ? new Tooltip(el) : Tooltip.getInstance(el)?.dispose();
    }
};

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new Tooltip(el));
});

new MutationObserver(mutations => {
    mutations.forEach(m => {
        if (m.type === 'childList') {
            m.addedNodes.forEach(n => handleTooltip(n, 'create'));
            m.removedNodes.forEach(n => handleTooltip(n, 'destroy'));
        }
    });
}).observe(document.body, { childList: true, subtree: true });