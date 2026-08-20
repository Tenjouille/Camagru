import { modalComments, modalCommentInput, modalCommentSubmit, state } from './state.js';
import { escapeHtml } from '../utils.js';

modalCommentSubmit.addEventListener('click', async () => {
    const content = modalCommentInput.value.trim();
    if (!content || !state.currentPostId) return;

    if (!IS_LOGGED_IN) {
        return;
    }

    const res = await fetch('/post/comment', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-Token': CSRF_TOKEN
        },
        body: `post_id=${state.currentPostId}&content=${encodeURIComponent(content)}`
    });
    const data = await res.json();

    modalCommentInput.value = '';
    renderComments(data.comments);
});

// Envoie aussi avec Entrée
modalCommentInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') modalCommentSubmit.click();
});

export function renderComments(comments) {
    if (!comments.length) {
        modalComments.innerHTML = '<p class="text-muted small">Aucun commentaire pour le moment.</p>';
        return;
    }

    modalComments.innerHTML = comments.map(c => `
        <div class="post-comment">
            <span class="comment-author">${escapeHtml(c.pseudo)}</span>
            <span class="comment-content">${escapeHtml(c.content)}</span>
        </div>
    `).join('');

    modalComments.scrollTop = modalComments.scrollHeight;
}
