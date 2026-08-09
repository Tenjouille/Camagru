<div class="auth-card">
    <div class="text-center mb-4">
        <h2>New Password</h2>
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
        <div class="mb-3">
            <label class="form-label">New Password</label>
            <input type="password" name="password" class="form-control auth-input" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password Confirmation</label>
            <input type="password" name="confirm" class="form-control auth-input" required>
        </div>
        <button type="submit" class="btn btn-dark w-100 auth-btn">Reinitialize</button>
    </form>
</div>