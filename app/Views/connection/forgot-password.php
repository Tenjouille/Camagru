<div class="auth-card">
    <div class="text-center mb-4">
        <h2>Forgotten Password</h2>
        <p class="text-muted">Enter your email address to get a reinitialization link.</p>
    </div>

    <?php if ($success ?? false): ?>
        <div class="alert alert-success">
            If this email exists, a link will be sent
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
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control auth-input" required>
        </div>
        <button type="submit" class="btn btn-dark w-100 auth-btn">Send the link</button>
    </form>

    <div class="text-center mt-3">
        <small><a href="/login">Back to login</a></small>
    </div>
</div>