<div class="container py-4">

    <h2 class="mb-4">Gallery</h2>

    <?php if (empty($posts)): ?>
        <p class="text-muted text-center">No posts yet.</p>
    <?php else: ?>

        <!-- Grille 3 colonnes -->
        <div class="row g-3">
            <?php foreach ($posts as $post): ?>
                <div class="col-6 col-md-4">
                    <a href="#" class="gallery-item d-block position-relative" data-post-id="<?= $post['id'] ?>">
                        <img
                            src="<?= htmlspecialchars($post['path']) ?>"
                            alt="Post by <?= htmlspecialchars($post['pseudo']) ?>"
                            class="img-fluid w-100"
                        >
                        <div class="gallery-sticker">
                            <span><i class="bi bi-person"></i> <?= htmlspecialchars($post['pseudo']) ?></span>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->

        <?php if ($totalPages > 1): ?>
            <nav class="mt-4 d-flex justify-content-center">
                <ul class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="/gallery?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
    <!-- Modale post -->
    <div class="modal-overlay" id="postModal">
        <div class="modal-box post-modal-box">

            <!-- Bouton fermer -->
            <button class="modal-close" id="postModalClose">
                <i class="bi bi-x-lg"></i>
            </button>

            <!-- Contenu chargé dynamiquement -->
            <div class="post-modal-image">
                <img id="modalPostImage" src="" alt="">
            </div>

            <div class="post-modal-side">

                <!-- Header : auteur -->
                <div class="post-modal-author">
                    <i class="bi bi-person-circle"></i>
                    <span id="modalPostAuthor"></span>
                </div>
                <div class="post-modal-description">
                    <span id="modalPostDescription"></span>
                </div>
                <!-- Commentaires -->
                <div class="post-modal-comments" id="modalComments">
                    <!-- rempli par JS -->
                </div>

                <!-- Like + commentaire -->
                <div class="post-modal-actions">
                    <button class="btn-like" id="modalLikeBtn">
                        <i class="bi bi-heart" id="modalLikeIcon"></i>
                        <span id="modalLikeCount">0</span>
                    </button>
                </div>

                <!-- Formulaire commentaire -->
                <div class="post-modal-comment-form">
                    <input type="text" id="modalCommentInput"
                        placeholder="Add a comment..."
                        class="form-control form-control-sm">
                    <button class="btn btn-dark btn-sm" id="modalCommentSubmit">
                        Post
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>
<script>
    const IS_LOGGED_IN = <?= Session::has('user') ? 'true' : 'false' ?>;
</script>
<script src="/assets/js/gallery.js"></script>