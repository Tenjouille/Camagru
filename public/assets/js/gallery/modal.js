import { postModal, modalImage, modalAuthor, modalDescription, modalComments, state } from './state.js';
import { updateLike } from './likes.js';
import { renderComments } from './comments.js';

// --- Ouvre la modale ---
document.querySelectorAll('.gallery-item').forEach(item => {
    item.addEventListener('click', async (e) => {
        e.preventDefault();

        state.currentPostId = item.dataset.postId;
        await loadPost(state.currentPostId);
        postModal.classList.add('open');
    });
});

// --- Ferme la modale ---
document.getElementById('postModalClose').addEventListener('click', closeModal);
postModal.addEventListener('click', (e) => {
    if (e.target === postModal) closeModal();
});

function closeModal() {
    postModal.classList.remove('open');
    state.currentPostId = null;
    modalImage.src = '';
    modalComments.innerHTML = '';

    // Nettoie le ?post=ID de l'URL pour ne pas rouvrir la modale au refresh
    if (new URLSearchParams(window.location.search).has('post')) {
        const url = new URL(window.location);
        url.searchParams.delete('post');
        history.replaceState(null, '', url);
    }
}

// --- Charge les données du post ---
async function loadPost(postId) {
    const res = await fetch(`/post?id=${postId}`);
    const data = await res.json();

    modalImage.src               = data.post.path;
    modalAuthor.textContent      = data.post.pseudo;
    modalDescription.textContent = data.post.description;

    updateLike(data.hasLiked, data.likeCount);
    renderComments(data.comments);
}

// --- Deep link : /gallery?post=ID ouvre directement la modale du post
// (ex: lien reçu par email de notification) ---
const deepLinkPostId = new URLSearchParams(window.location.search).get('post');
if (deepLinkPostId) {
    state.currentPostId = deepLinkPostId;
    loadPost(deepLinkPostId).then(() => postModal.classList.add('open'));
}
