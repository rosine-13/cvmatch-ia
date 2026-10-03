<?php
require_once '../config.php';

if (!is_logged_in() || !is_recruiter()) {
    header("Location: login.php");
    exit;
}

// Récupérer les offres du recruteur connecté
$stmt = $pdo->prepare("
    SELECT id, title, location, contract_type, required_skills, created_at 
    FROM jobs 
    WHERE recruiter_id = ? 
    ORDER BY created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$my_jobs = $stmt->fetchAll();

// Compter les CV
$stmt = $pdo->query("SELECT COUNT(*) as total FROM cvs");
$total_cvs = $stmt->fetch()['total'];

// Compter les CV analysés
$stmt = $pdo->query("SELECT COUNT(*) as total FROM cvs WHERE status = 'Analysé'");
$total_analyzed = $stmt->fetch()['total'];

// Compter les candidats
$stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'candidate'");
$total_candidates = $stmt->fetch()['total'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Recruteur - CVMatch IA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<!-- NAVBAR RECRUTEUR -->
<nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #10b981, #059669);">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">
            <span class="logo-icon">🤖</span> CVMatch IA
        </a>
        <div class="d-flex gap-2">
            <a href="post_job.php" class="btn btn-light btn-sm">
                <i class="fas fa-plus"></i> Publier une offre
            </a>
            <a href="../logout.php" class="btn btn-outline-light btn-sm">
                <i class="fas fa-sign-out-alt"></i> Déconnexion
            </a>
        </div>
    </div>
</nav>

<div class="container py-5">

    <!-- BIENVENUE -->
    <div class="mb-4">
        <h2 class="fw-bold">
            <i class="fas fa-building text-success"></i> 
            Bonjour, <?= htmlspecialchars($_SESSION['nom']) ?>
        </h2>
        <p class="text-muted">Bienvenue dans votre espace recruteur CVMatch IA.</p>
    </div>

    <!-- STATISTIQUES -->
    <div class="row g-3 mb-5">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <div class="display-5 fw-bold text-primary"><?= $total_candidates ?></div>
                    <div class="text-muted">
                        <i class="fas fa-users"></i> Candidats inscrits
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <div class="display-5 fw-bold text-warning"><?= $total_cvs ?></div>
                    <div class="text-muted">
                        <i class="fas fa-file-alt"></i> CV déposés
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <div class="display-5 fw-bold text-success"><?= $total_analyzed ?></div>
                    <div class="text-muted">
                        <i class="fas fa-robot"></i> CV analysés par l'IA
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- 🔍 SECTION RECHERCHE IA (MISE EN AVANT)      -->
    <!-- ============================================ -->
    <div class="card shadow-lg mb-5 border-0" style="background: linear-gradient(135deg, #10b981, #059669);">
        <div class="card-body p-5 text-white">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="fw-bold mb-2">
                        <i class="fas fa-search"></i> Recherche intelligente de candidats
                    </h3>
                    <p class="mb-0 opacity-90">
                        Tapez des mots-clés (compétences, poste, ville...) et l'IA trouvera 
                        les candidats les plus pertinents parmi les <?= $total_analyzed ?> CV analysés.
                    </p>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <i class="fas fa-brain" style="font-size: 4rem; opacity: 0.4;"></i>
                    </div>
                </div>
            </div>

            <!-- Formulaire de recherche -->
            <form action="search_ia.php" method="GET" class="mt-4">
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-white border-0">
                        <i class="fas fa-search text-success"></i>
                    </span>
                    <input type="text" 
                           name="query" 
                           class="form-control border-0" 
                           placeholder="Ex: Développeur PHP, Marketing, Abidjan..."
                           autofocus>
                    <button type="submit" class="btn btn-light fw-bold px-4">
                        <i class="fas fa-robot"></i> Rechercher avec l'IA
                    </button>
                </div>
                <div class="mt-2 small opacity-75">
                    <i class="fas fa-lightbulb"></i> 
                    Astuce : utilisez des compétences précises (PHP, MySQL, Python) pour de meilleurs résultats.
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- SECTION : MES OFFRES D'EMPLOI                -->
    <!-- ============================================ -->
    <h3 class="mb-3">
        <i class="fas fa-briefcase text-success"></i> 
        Mes offres d'emploi (<?= count($my_jobs) ?>)
    </h3>

    <?php if (empty($my_jobs)): ?>
        <div class="alert alert-info mb-5">
            <i class="fas fa-info-circle"></i> 
            Vous n'avez publié aucune offre pour le moment. 
            <a href="post_job.php" class="alert-link">Publiez-en une maintenant !</a>
        </div>
    <?php else: ?>
        <div class="row mb-5">
            <?php foreach ($my_jobs as $job): ?>
                <div class="col-md-6 mb-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-briefcase text-success"></i>
                                <?= htmlspecialchars($job['title']) ?>
                            </h5>
                            <p class="text-muted small mb-2">
                                <i class="fas fa-map-marker-alt"></i> 
                                <?= htmlspecialchars($job['location'] ?? 'Non précisé') ?>
                                | <i class="fas fa-file-contract"></i> 
                                <?= htmlspecialchars($job['contract_type'] ?? 'N/A') ?>
                            </p>
                            <p class="small mb-3">
                                <strong>Compétences :</strong> 
                                <?= htmlspecialchars($job['required_skills']) ?>
                            </p>
                            <a href="view_candidates.php?job_id=<?= $job['id'] ?>" 
                               class="btn btn-success w-100">
                                <i class="fas fa-robot"></i> 
                                Voir les candidats (IA)
                            </a>
                        </div>
                        <div class="card-footer text-muted small">
                            Publiée le <?= date('d/m/Y', strtotime($job['created_at'])) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

</body>
</html>