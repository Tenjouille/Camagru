const postModal             = document.getElementById('postModal');
const modalImage            = document.getElementById('modalPostImage');
const modalAuthor           = document.getElementById('modalPostAuthor');
const modalDescription      = document.getElementById('modalPostDescription');
const modalComments         = document.getElementById('modalComments');
const modalLikeBtn          = document.getElementById('modalLikeBtn');
const modalLikeIcon         = document.getElementById('modalLikeIcon');
const modalLikeCount        = document.getElementById('modalLikeCount');
const modalCommentInput     = document.getElementById('modalCommentInput');
const modalCommentSubmit    = document.getElementById('modalCommentSubmit');

let currentPostId = null;

// --- Ouvre la modale ---
document.querySelectorAll('.gallery-item').forEach(item => {
    item.addEventListener('click', async (e) => {
        e.preventDefault();

        currentPostId = item.dataset.postId;
        await loadPost(currentPostId);
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
    currentPostId = null;
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
    const res  = await fetch(`/post?id=${postId}`);
    const data = await res.json();

    modalImage.src                  = data.post.path;
    modalAuthor.textContent         = data.post.pseudo;
    modalDescription.textContent    = data.post.description;

    updateLike(data.hasLiked, data.likeCount);
    renderComments(data.comments);
}

// --- Like ---
modalLikeBtn.addEventListener('click', async () => {
    if (!currentPostId) return;

    // Guest → ouvre la modale d'auth
    if (!IS_LOGGED_IN) {
        openAuthModal();
        return;
    }

    const res  = await fetch('/post/like', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-Token': CSRF_TOKEN
        },
        body: `post_id=${currentPostId}`
    });
    const data = await res.json();
    updateLike(data.hasLiked, data.likeCount);
});

function updateLike(hasLiked, likeCount) {
    modalLikeIcon.className = hasLiked ? 'bi bi-heart-fill' : 'bi bi-heart';
    modalLikeCount.textContent = likeCount;
}

// --- Commentaires ---
modalCommentSubmit.addEventListener('click', async () => {
    const content = modalCommentInput.value.trim();
    if (!content || !currentPostId) return;

    if (!IS_LOGGED_IN) {
        openAuthModal();
        return;
    }

    const res  = await fetch('/post/comment', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-Token': CSRF_TOKEN
        },
        body: `post_id=${currentPostId}&content=${encodeURIComponent(content)}`
    });
    const data = await res.json();

    modalCommentInput.value = '';
    renderComments(data.comments);
});

// Envoie aussi avec Entrée
modalCommentInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') modalCommentSubmit.click();
});

// Echappe le texte avant de l'insérer en HTML (les commentaires/pseudos
// viennent des utilisateurs — sans ça, <script> dans un commentaire
// s'exécuterait chez tout le monde qui ouvre le post).
function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str ?? '';
    return div.innerHTML;
}

function renderComments(comments) {
    if (!comments.length) {
        modalComments.innerHTML = '<p class="text-muted small">No comments yet.</p>';
        return;
    }

    modalComments.innerHTML = comments.map(c => `
        <div class="post-comment">
            <span class="comment-author">${escapeHtml(c.pseudo)}</span>
            <span class="comment-content">${escapeHtml(c.content)}</span>
        </div>
    `).join('');

    // Scroll vers le bas pour voir le dernier commentaire
    modalComments.scrollTop = modalComments.scrollHeight;
}

// --- Deep link : /gallery?post=ID ouvre directement la modale du post
// (ex: lien reçu par email de notification) ---
const deepLinkPostId = new URLSearchParams(window.location.search).get('post');
if (deepLinkPostId) {
    currentPostId = deepLinkPostId;
    loadPost(deepLinkPostId).then(() => postModal.classList.add('open'));
}