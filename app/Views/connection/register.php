<div class="auth-card">

    <div class="text-center mb-4">
        <img src="/assets/images/camagru_name_logo.png" alt="Camagru" class="auth-logo">
        <p class="text-muted mb-0">Create an account</p>
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

        <div class="mb-3">
            <label class="form-label">Pseudo</label>
            <input type="text" name="pseudo" class="form-control auth-input"
                   value="<?= htmlspecialchars($_POST['pseudo'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control auth-input"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control auth-input" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Confirm password</label>
            <input type="password" name="confirm" class="form-control auth-input" required>
        </div>

        <button type="submit" class="btn btn-dark w-100 auth-btn">
            Register
        </button>

    </form>

    <div class="text-center mt-3">
        <small>
            Already have an account ?
            <a href="/login">Sign in</a>
        </small>
    </div>

</div>