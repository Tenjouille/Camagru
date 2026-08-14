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

async function submitFormWithAjax(form, statusEl, successMsg, errMsg) {
    form.classList.add('is-saving');
    try {
        const response = await fetch(form.action, {
            method: "POST",
            headers: { 'X-Requested-With': "XMLHttpRequest" },
            body: new URLSearchParams(new FormData(form))
        });

        if (!response.ok)
            throw new Error('Request Failed');
        showStatus(statusEl, successMsg);
        return true;
    } catch {
        showStatus(statusEl, errMsg);
        return false;
    } finally {
        form.classList.remove('is-saving');
    }
}

// Toggle notifications email sans recharger la page, pour une animation fluide
const notifForm = document.querySelector('.notif-form');

if (notifForm) {
    const checkbox = notifForm.querySelector('#notifyOnComment');
    const status = notifForm.querySelector('.notif-status');

    checkbox.addEventListener('change', async () => {
        const previousChecked = !checkbox.checked;
        checkbox.checked = 
            await submitFormWithAjax(notifForm, status, "Enregistré", "Erreur, réessayez")
                ? !previousChecked : previousChecked; 
    });
}