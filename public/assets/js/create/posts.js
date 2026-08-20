// Suppression d'un post depuis la sidebar "Mes posts" (clic pour armer,
// clic sur la poubelle pour confirmer). Vivait avant dans le main.js
// global alors que #myCreationSidebar n'existe que sur cette page.
import { myCreationSidebar } from './state.js';

myCreationSidebar.addEventListener('click', async (event) => {
    const deleteBtn = event.target.closest('.post-delete-overlay');
    const selectedPost = event.target.closest('.sidebar-post');

    if (deleteBtn) {
        const postId = deleteBtn.closest('.sidebar-post').dataset.id;
        const response = await fetch('/post/delete', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': CSRF_TOKEN },
            body: new URLSearchParams({ post_id: postId })
        });
        if (response.ok) {
            const el = deleteBtn.closest('.sidebar-post');
            el.classList.add('leaving');
            el.addEventListener('animationend', () => el.remove(), { once: true });
        }
        return;
    }

    myCreationSidebar.querySelectorAll('.sidebar-post.confirm-delete').forEach((el) => {
        if (el !== selectedPost)
            el.classList.remove('confirm-delete');
    });
    if (selectedPost)
        selectedPost.classList.add('confirm-delete');
});

document.addEventListener('click', (event) => {
    if (!myCreationSidebar.contains(event.target)) {
        myCreationSidebar.querySelectorAll('.sidebar-post.confirm-delete').forEach((el) => {
            el.classList.remove('confirm-delete');
        });
    }
});
