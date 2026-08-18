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
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
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
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
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

function renderComments(comments) {
    if (!comments.length) {
        modalComments.innerHTML = '<p class="text-muted small">No comments yet.</p>';
        return;
    }

    modalComments.innerHTML = comments.map(c => `
        <div class="post-comment">
            <span class="comment-author">${c.pseudo}</span>
            <span class="comment-content">${c.content}</span>
        </div>
    `).join('');

    // Scroll vers le bas pour voir le dernier commentaire
    modalComments.scrollTop = modalComments.scrollHeight;
}

// document.querySelectorAll('.gallery-item').forEach(item => {
//     item.addEventListener('click', async (e) => {
//         e.preventDefault();
//         console.log('dataset:', item.dataset);
//         console.log('postId:', item.dataset.postId);
//         // ...
//     });
// });

// async function loadPost(postId) {
//     const res  = await fetch(`/post?id=${postId}`);
//     const text = await res.text();
//     console.log('status:', res.status);
//     console.log('response:', text);
// }