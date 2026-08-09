<div class="auth-card text-center">
    <?php if ($success): ?>
        <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
        <h2 class="mt-3">Verified account</h2>
        <p class="text-muted">You may now connect.</p>
        <a href="/login" class="btn btn-dark mt-2">Sign in</a>
    <?php else: ?>
        <i class="bi bi-x-circle text-danger" style="font-size: 3rem;"></i>
        <h2 class="mt-3">Invalid link</h2>
        <p class="text-muted">This link is invalid or obsolete.</p>
        <a href="/register" class="btn btn-dark mt-2">Sign up</a>
    <?php endif; ?>
</div>