<div class="auth-card">

    <div class="text-center mb-4">
        <img src="/assets/images/camagru_name_logo.png" alt="Camagru" class="auth-logo">
        <p class="text-muted mb-0">Sign in to continue</p>
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

    <form method="POST" action="/login">

        <div class="mb-3">
            <label class="form-label">Pseudo</label>
            <input type="text" name="pseudo" class="form-control auth-input"
                    value="<?= htmlspecialchars(Request::post('pseudo', '')) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control auth-input" required>
        </div>

        <button type="submit" class="btn btn-dark w-100 auth-btn">
            Login
        </button>

    </form>

    <div class="text-center mt-3">
        <small>
            No account ?
            <a href="/register">Create one</a>
        </small>
    </div>
    <div class="text-center mt-3">
        <small>
            <a href="/forgot-password">Forgotten password ?</a>
        </small>
    </div>
</div>