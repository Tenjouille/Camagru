<div class="auth-card">
    <div class="text-center mb-4">
        <h2>Nouveau mot de passe</h2>
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

    <form method="POST" action="/reset-password?token=<?= htmlspecialchars($token) ?>">
        <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">
        <div class="mb-3">
            <label class="form-label">Nouveau mot de passe</label>
            <input type="password" name="password" class="form-control auth-input" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Confirmation du mot de passe</label>
            <input type="password" name="confirm" class="form-control auth-input" required>
        </div>
        <button type="submit" class="btn btn-dark w-100 auth-btn">Réinitialiser</button>
    </form>
</div>