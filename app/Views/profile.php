<div class="profile-layout">

    <div class="auth-card mb-4">
        <h5 class="mb-3">Mon compte</h5>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <!-- Pseudo -->
        <form method="POST" action="/profile/pseudo" class="mb-3 pseudo-form">
            <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">
            <label class="form-label">Pseudo</label>
            <div class="input-group">
                <input type="text" name="pseudo" id="changePseudo" class="form-control"
                       value="<?= htmlspecialchars($currentUser['pseudo']) ?>" <?= Session::isAdmin() ? 'disabled' : 'required' ?>>
                <button type="submit" class="btn btn-dark <?= Session::isAdmin() ? 'd-none' : "" ?>">Mettre à jour</button>
            </div>
            <span class="field-status" aria-live="polite"></span>
        </form>

        <!-- Email (lecture seule) -->
        <form method="POST" action="/profile/email" class="mb-3 email-form">
            <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">
            <label class="form-label">Email</label>
            <div class="input-group">
                <input type="email" name="email" id="changeEmail" class="form-control" value="<?= htmlspecialchars($currentUser['email']) ?>">
                <button type="submit" class="btn btn-dark <?= Session::isAdmin() ? 'd-none' : "" ?>">Mettre à jour</button>
            </div>
            <span class="field-status" aria-live="polite"></span>
        </form>

        <!-- Mot de passe -->
        <form method="POST" action="/profile/password" class="mb-3 pwd-form">
            <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">
            <label class="form-label">Changer le mot de passe</label>
            <input type="password" name="current_password" class="form-control mb-2" placeholder="Mot de passe actuel" required>
            <input type="password" name="password" class="form-control mb-2" placeholder="Nouveau mot de passe" required>
            <input type="password" name="confirm" class="form-control mb-2" placeholder="Confirmer le nouveau mot de passe" required>
            <button type="submit" class="btn btn-dark">Changer le mot de passe</button>
            <span class="field-status" aria-live="polite"></span>
        </form>

        <!-- Notifications email -->
        <form method="POST" action="/profile/notifications" class="notif-form">
            <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">
            <div class="form-check form-switch d-flex align-items-center gap-2">
                <input type="checkbox" class="form-check-input" id="notifyOnComment" name="email_notification"
                       value="1" <?= !empty($currentUser['email_notification']) ? 'checked' : '' ?>>
                <label class="form-check-label mb-0" for="notifyOnComment">
                    Recevoir un email quand quelqu'un commente un de mes posts
                </label>
                <span class="field-status" aria-live="polite"></span>
            </div>
        </form>
    </div>

    <!-- Stickers personnels -->
    <div class="auth-card">
        <h5 class="mb-3">Mes stickers</h5>

        <div class="stickers-grid-wrapper mb-3">
            <div class="stickers-grid" id="myStickersGrid">
                <?php if (empty($stickers)): ?>
                    <p class="text-muted small">Aucun sticker personnel pour le moment.</p>
                <?php else: ?>
                    <?php foreach ($stickers as $sticker): ?>
                        <?php
                            // Même règle que ProfileController::deleteSticker() :
                            // propriétaire du sticker, ou admin (qui peut tout supprimer).
                            $canDelete = Session::isAdmin()
                                || ($sticker['user_id'] !== null && (int) $sticker['user_id'] === (int) $currentUser['id']);
                        ?>
                        <div class="sticker-thumb" data-id="<?= $sticker['id'] ?>">
                            <img src="<?= htmlspecialchars($sticker['path']) ?>" alt="Sticker <?= $sticker['id'] ?>">
                            <?php if ($canDelete): ?>
                                <button type="button" class="sticker-delete-overlay" aria-label="Supprimer ce sticker">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <form method="POST" action="/profile/sticker/add" enctype="multipart/form-data" class="sticker-form d-flex gap-2">
            <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">
            <input type="file" name="sticker" accept="image/*" class="form-control" required>
            <button type="submit" class="btn btn-outline-dark text-nowrap">
                <i class="bi bi-plus-lg"></i> Ajouter
            </button>
            <span class="field-status" aria-live="polite"></span>
        </form>
    </div>

</div>
