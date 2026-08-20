<div class="auth-card">
    <div class="text-center mb-4">
        <h2>Mot de passe oublié</h2>
        <p class="text-muted">Entrez votre adresse email pour recevoir un lien de réinitialisation.</p>
    </div>

    <?php if ($success ?? false): ?>
        <div class="alert alert-success">
            Si cette adresse existe, un lien vous a été envoyé
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="/forgot-password">
        <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control auth-input" required>
        </div>
        <button type="submit" class="btn btn-dark w-100 auth-btn">Envoyer le lien</button>
    </form>

    <div class="text-center mt-3">
        <small><a href="/login">Retour à la connexion</a></small>
    </div>
</div>