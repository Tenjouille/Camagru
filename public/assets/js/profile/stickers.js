import { stickerForm, myStickersGrid } from './state.js';
import { showStatus } from './utils.js';
import { escapeHtml } from '../utils.js';

// --- Upload d'un sticker perso ---
const status = stickerForm.querySelector('.field-status');
stickerForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    stickerForm.classList.add('is-saving');
    try {
        const response = await fetch(stickerForm.action, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': CSRF_TOKEN },
            // pas de URLSearchParams s'il y a un fichier dans la requete
            body: new FormData(stickerForm)
        });

        if (!response.ok) {
            const data = await response.json().catch(() => null);
            showStatus(status, data?.errors?.[0] ?? "Erreur, veuillez contacter un admin");
            return;
        }

        const data = await response.json();

        const thumb = document.createElement('div');
        thumb.className = 'sticker-thumb entering';
        thumb.dataset.id = data.id;
        thumb.innerHTML = `
            <img src="${escapeHtml(data.path)}" alt="Sticker ${escapeHtml(String(data.id))}">
            <button type="button" class="sticker-delete-overlay" aria-label="Supprimer ce sticker">
                <i class="bi bi-trash3-fill"></i>
            </button>`;
        myStickersGrid.appendChild(thumb);

        showStatus(status, "Enregistré");
        stickerForm.reset();
    } catch {
        showStatus(status, "Erreur, veuillez contacter un admin");
    } finally {
        stickerForm.classList.remove('is-saving');
    }
});

// --- Suppression (clic pour armer, clic sur la poubelle pour confirmer) ---
myStickersGrid.addEventListener('click', async (event) => {
    const deleteBtn = event.target.closest('.sticker-delete-overlay');
    const thumb = event.target.closest('.sticker-thumb');

    if (deleteBtn) {
        const stickerId = deleteBtn.closest('.sticker-thumb').dataset.id;
        const response = await fetch('/profile/sticker/delete', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': CSRF_TOKEN },
            body: new URLSearchParams({ sticker_id: stickerId })
        });
        if (response.ok) {
            const el = deleteBtn.closest('.sticker-thumb');
            el.classList.add('leaving');
            el.addEventListener('animationend', () => el.remove(), { once: true });
        }
        return;
    }

    myStickersGrid.querySelectorAll('.sticker-thumb.confirm-delete').forEach((el) => {
        if (el !== thumb) el.classList.remove('confirm-delete');
    });
    if (thumb)
        thumb.classList.add('confirm-delete');
});

document.addEventListener('click', (event) => {
    if (!myStickersGrid.contains(event.target)) {
        myStickersGrid.querySelectorAll('.sticker-thumb.confirm-delete').forEach((el) => {
            el.classList.remove('confirm-delete');
        });
    }
});
