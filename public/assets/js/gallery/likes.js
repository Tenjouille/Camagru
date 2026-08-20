import { modalLikeBtn, modalLikeIcon, modalLikeCount, state } from './state.js';

modalLikeBtn.addEventListener('click', async () => {
    if (!state.currentPostId) return;

    if (!IS_LOGGED_IN) {
        return;
    }

    const res = await fetch('/post/like', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-Token': CSRF_TOKEN
        },
        body: `post_id=${state.currentPostId}`
    });
    const data = await res.json();
    updateLike(data.hasLiked, data.likeCount);
});

export function updateLike(hasLiked, likeCount) {
    modalLikeIcon.className = hasLiked ? 'bi bi-heart-fill' : 'bi bi-heart';
    modalLikeCount.textContent = likeCount;
}
