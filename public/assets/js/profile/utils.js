// Affiche un statut temporaire à côté d'un champ (ex: "Enregistré").
export const showStatus = (statusEl, text) => {
    clearTimeout(statusEl._timer);
    statusEl.textContent = text;
    statusEl.classList.add('visible');
    statusEl._timer = setTimeout(() => statusEl.classList.remove('visible'), 1500);
};

// Soumission AJAX générique pour les formulaires profile (pseudo, email,
// mot de passe, notifications) : POST form-encodé, statut affiché via
// showStatus(), renvoie true/false selon le succès.
export async function submitFormWithAjax(form, statusEl) {
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
