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

        if (!response.ok) {
            const data = await response.json().catch(() => null);
            showStatus(statusEl, data?.errors?.[0] ?? errMsg);
            return false;
        }
        showStatus(statusEl, successMsg);
        return true;
    } catch {
        showStatus(statusEl, errMsg);
        return false;
    } finally {
        form.classList.remove('is-saving');
    }
}

const pseudoForm = document.querySelector('.pseudo-form');
const passwordForm = document.querySelector('.pwd-form');
const notifForm = document.querySelector('.notif-form');

if (pseudoForm) {
    const textbox = pseudoForm.querySelector('#changePseudo');
    const status = pseudoForm.querySelector('.field-status');
    const headerPseudo = document.querySelector('#headerPseudo');
    pseudoForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const previousPseudo = textbox.value;
        const response = await submitFormWithAjax(pseudoForm, status, "Enregistré", "Erreur, réessayez")
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
        await submitFormWithAjax(passwordForm, status, "Enregistré", "Erreur, réessayez");
        passwordForm.reset();
    });
}

if (notifForm) {
    const checkbox = notifForm.querySelector('#notifyOnComment');
    const status = notifForm.querySelector('.field-status');

    checkbox.addEventListener('change', async () => {
        const previousChecked = !checkbox.checked;
        checkbox.checked = 
            await submitFormWithAjax(notifForm, status, "Enregistré", "Erreur, réessayez")
                ? checkbox.checked : previousChecked; 
    });
}