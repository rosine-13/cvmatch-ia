<nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #4f46e5, #4338ca);">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">
            <span class="logo-icon">🤖</span>
            CVMatch IA
        </a>
        <div class="d-flex align-items-center gap-3">
            <span class="text-white d-none d-md-inline">
                <i class="fas fa-user-circle"></i> <?= htmlspecialchars($user_name) ?>
            </span>
            <a href="../logout.php" class="btn btn-light btn-sm">
                <i class="fas fa-sign-out-alt"></i> Déconnexion
            </a>
        </div>
    </div>
</nav>