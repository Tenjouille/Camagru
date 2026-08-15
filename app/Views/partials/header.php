<?php $user = Session::get('user'); ?>

<nav class="navbar navbar-expand-md sticky-top camagru-header">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/">
            <img src="/assets/images/camagru_icon.png" alt="Logo" class="logo">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto gap-2 align-items-md-center">
                <li class="nav-item">
                    <a class="nav-link" href="<?= $user ? '/' : '/login' ?>"><i class="bi bi-house"></i> Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/gallery"><i class="bi bi-grid"></i> Gallery</a>
                </li>
                
                <?php if ($user): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/profile">
                            <i class="bi bi-person"></i> <span id="headerPseudo"><?= htmlspecialchars($user['pseudo']) ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-danger btn-sm px-3" href="/logout">Deconnexion</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="btn btn-dark btn-sm px-3" href="/login">Connexion</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>