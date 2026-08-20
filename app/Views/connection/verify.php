<div class="auth-card text-center">
    <?php if ($success): ?>
        <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
        <h2 class="mt-3">Compte verifié</h2>
        <p class="text-muted">Vous pouvez maintenant vous connecter.</p>
        <a href="/login" class="btn btn-dark mt-2">Se connecter</a>
    <?php else: ?>
        <i class="bi bi-x-circle text-danger" style="font-size: 3rem;"></i>
        <h2 class="mt-3">Lien invalide</h2>
        <p class="text-muted">Ce lien est invalide ou obsolete.</p>
        <a href="/register" class="btn btn-dark mt-2">S'inscrire</a>
    <?php endif; ?>
</div>