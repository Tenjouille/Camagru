const   toggler = document.querySelector('.navbar-toggler');
const   navMenu = document.querySelector('#mainNav');

toggler.addEventListener('click', () => {
    navMenu.classList.toggle('show');
});

const showStatus = (statusEl, text) => {
    clearTimeout(statusEl._timer);
    statusEl.textContent = text;
    statusEl.classList.add('visible');
    statusEl._timer = setTimeout(() => statusEl.classList.remove('visible'), 1500);
};

async function submitFormWithAjax(form, statusEl) {
    form.classList.add('is-saving');
    try {
        const response = await fetch(form.action, {
            method: "POST",
            headers: { 'X-Requested-With': "XMLHttpRequest" },
            body: new URLSearchParams(new FormData(form))
        });

        if (!response.ok) {
            const data = await response.json().catch(() => null);
            showStatus(statusEl, data?.errors?.[0] ?? "Erreur, veuillez contacter un admin");
            return false;
        }
        showStatus(statusEl, "Enregisté");
        return true;
    } catch {
        showStatus(statusEl, "Erreur, veuillez contacter un admin");
        return false;
    } finally {
        form.classList.remove('is-saving');
    }
}

const pseudoForm = document.querySelector('.pseudo-form');
const passwordForm = document.querySelector('.pwd-form');
const notifForm = document.querySelector('.notif-form');
const stickerForm = document.querySelector('.sticker-form');

if (pseudoForm) {
    const textbox = pseudoForm.querySelector('#changePseudo');
    const status = pseudoForm.querySelector('.field-status');
    const headerPseudo = document.querySelector('#headerPseudo');
    pseudoForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const previousPseudo = textbox.value;
        const response = await submitFormWithAjax(pseudoForm, status);
        if (response === true)
            headerPseudo.textContent = textbox.value;
        else
            textbox.value = previousPseudo;
    });
}

if (passwordForm) {
    const status = passwordForm.querySelector('.field-status');
    passwordForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        await submitFormWithAjax(passwordForm, status);
        passwordForm.reset();
    });
}

if (notifForm) {
    const checkbox = notifForm.querySelector('#notifyOnComment');
    const status = notifForm.querySelector('.field-status');

    checkbox.addEventListener('change', async () => {
        const previousChecked = !checkbox.checked;
        checkbox.checked = 
            await submitFormWithAjax(notifForm, status)
                ? checkbox.checked : previousChecked; 
    });
}

if (stickerForm) {
    const status = stickerForm.querySelector('.field-status');
    stickerForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        stickerForm.classList.add('is-saving');
        try {
            const response = await fetch(stickerForm.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(stickerForm), // pas de URLSearchParams : il y a un fichier dedans
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
                <img src="${data.path}" alt="Sticker ${data.id}">
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
}

const myStickersGrid = document.querySelector('#myStickersGrid');
const myCreationsGrid = document.querySelector('#myCreationSidebar');

if (myStickersGrid) {
    myStickersGrid.addEventListener('click', async (event) => {
        const deleteBtn = event.target.closest('.sticker-delete-overlay');
        const thumb = event.target.closest('.sticker-thumb');

        if (deleteBtn) {
            const stickerId = deleteBtn.closest('.sticker-thumb').dataset.id;
            const response = await fetch('/profile/sticker/delete', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
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

}

if(myCreationsGrid) {
    myCreationsGrid.addEventListener('click', async (event) => {
        const deleteBtn = event.target.closest('.post-delete-overlay');
        const selectedPost = event.target.closest('.sidebar-post');
        
        if (deleteBtn) {
            const postId = deleteBtn.closest('.sidebar-post').dataset.id;
            const response = await fetch('/post/delete', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: new URLSearchParams({ post_id: postId })
            });
            if (response.ok) {
                const el = deleteBtn.closest('.sidebar-post');
                el.classList.add('leaving');
                el.addEventListener('animationend', () => el.remove(), { once: true});
            }
            return;
        }

        myCreationsGrid.querySelectorAll('.sidebar-post.confirm-delete').forEach((el) => {
            if (el !== selectedPost)
                el.classList.remove('confirm-delete');
        });
        if (selectedPost)
            selectedPost.classList.add('confirm-delete');
    });

    document.addEventListener('click', (event) => {
        if (!myCreationsGrid.contains(event.target)) {
            myCreationsGrid.querySelectorAll('.sidebar-post.confirm-delete').forEach((el) => {
                el.classList.remove('confirm-delete');
            });
        }
    });
}