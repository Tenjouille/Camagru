<div class="create-layout">

    <!-- Main section : webcam + stickers + controls -->
    <section class="create-main">

        <!-- Canvas de prévisualisation -->
        <div class="canvas-wrapper">
            <div class="video-box" id="videoBox">
                <canvas id="previewCanvas" width="640" height="480"></canvas>

                <div id="fallbackSection" class="fallback-section" style="display:none;">
                    <label for="photoInput" class="btn btn-outline-dark">
                        <i class="bi bi-image"></i> Choose a photo
                    </label>
                    <input type="file" id="photoInput" accept="image/*" style="display:none;">
                </div>
            </div>
        </div>

        <!-- Contrôles sticker -->
        <div class="sticker-controls <?= empty($stickers) ? 'd-none' : '' ?>">
            <div class="control-group">
                <label><i class="bi bi-arrow-clockwise"></i> Rotation</label>
                <input type="range" id="stickerRotation" min="0" max="360" value="0" class="form-range">
                <span id="rotationValue">0°</span>
            </div>
            <div class="control-group">
                <label><i class="bi bi-arrows-fullscreen"></i> Size</label>
                <input type="range" id="stickerSize" min="50" max="500" value="150" class="form-range">
                <span id="sizeValue">150px</span>
            </div>
        </div>

        <!-- Sélection stickers — scrollable si trop -->
        <div class="stickers-list">
            <h6>Choose a sticker</h6>
            <?php if (empty($stickers)): ?>
                <p class="text-muted small">No stickers available yet.</p>
            <?php else: ?>
                <div class="stickers-grid-wrapper">  <!-- AJOUT wrapper scrollable -->
                    <div class="stickers-grid">
                        <?php foreach ($stickers as $sticker): ?>
                            <div class="sticker-thumb"
                                 data-id="<?= $sticker['id'] ?>"
                                 data-path="<?= htmlspecialchars($sticker['path']) ?>">
                                <img src="<?= htmlspecialchars($sticker['path']) ?>"
                                     alt="Sticker <?= $sticker['id'] ?>">
                            </div>
                        <?php endforeach; ?>
                        <button 
                            type="button" 
                            id="deleteStickerBtn" 
                            class="btn btn-lg text-danger"
                            title="Delete sticker">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Formulaire de soumission -->
        <form id="createForm" method="POST" action="/create">
            <input type="hidden" name="image_data" id="imageData">
            <input type="hidden" name="stickers" id="stickersData">

            <!-- AJOUT wrapper pour isoler description + bouton -->
            <div class="create-form-bottom">
                <div class="mb-3">
                    <textarea name="description" class="form-control" placeholder="Add a description..." rows="2"></textarea>
                </div>

                <button type="button" id="captureBtn" class="btn btn-dark w-100" disabled>
                    <i class="bi bi-camera"></i> Capture & Post
                </button>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger mt-3">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>

        </form>

    </section>

    <!-- Side section : posts de l'utilisateur -->
    <aside class="create-sidebar">
        <h6>Your posts</h6>
        <div class="sidebar-posts">
            <?php if (empty($userPosts)): ?>
                <p class="text-muted small">No posts yet.</p>
            <?php else: ?>
                <?php foreach ($userPosts as $post): ?>
                    <div class="sidebar-post">
                        <img src="<?= htmlspecialchars($post['path']) ?>"
                             alt="Your post">
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </aside>

</div>

<script type="module" src="/assets/js/create/main.js"></script>