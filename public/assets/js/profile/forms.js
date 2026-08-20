import { pseudoForm, emailForm, passwordForm, notifForm, headerPseudo } from './state.js';
import { submitFormWithAjax } from './utils.js';

pseudoForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    const textbox = pseudoForm.querySelector('#changePseudo');
    const status = pseudoForm.querySelector('.field-status');
    const previousPseudo = textbox.value;
    const response = await submitFormWithAjax(pseudoForm, status);
    if (response === true)
        headerPseudo.textContent = textbox.value;
    else
        textbox.value = previousPseudo;
});

emailForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    const status = emailForm.querySelector('.field-status');
    await submitFormWithAjax(emailForm, status);
});

passwordForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    const status = passwordForm.querySelector('.field-status');
    await submitFormWithAjax(passwordForm, status);
    passwordForm.reset();
});

const notifCheckbox = notifForm.querySelector('#notifyOnComment');
const notifStatus = notifForm.querySelector('.field-status');

notifCheckbox.addEventListener('change', async () => {
    const previousChecked = !notifCheckbox.checked;
    notifCheckbox.checked =
        await submitFormWithAjax(notifForm, notifStatus)
            ? notifCheckbox.checked : previousChecked;
});
