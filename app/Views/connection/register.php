<div class="auth-card">

    <div class="text-center mb-4">
        <img src="/assets/images/camagru_name_logo.png" alt="Camagru" class="auth-logo">
        <p class="text-muted mb-0">Créer un compte</p>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="/register">
        <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">

        <div class="mb-3">
            <label class="form-label">Pseudo</label>
            <input type="text" name="pseudo" class="form-control auth-input"
                   value="<?= htmlspecialchars(Request::post('pseudo', '')) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control auth-input"
                   value="<?= htmlspecialchars(Request::post('email', '')) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Mot de passe</label>
            <input type="password" name="password" class="form-control auth-input" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Validation du mot de passe</label>
            <input type="password" name="confirm" class="form-control auth-input" required>
        </div>

        <button type="submit" class="btn btn-dark w-100 auth-btn">
            S'inscrire
        </button>

    </form>

    <div class="text-center mt-3">
        <small>
            Déjà inscrit ?
            <a href="/login">Se connecter</a>
        </small>
    </div>

</div>