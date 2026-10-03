<?php
require_once '../config.php';

if (!is_logged_in() || !is_candidate()) {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->prepare("SELECT id, file_path, status, created_at FROM cvs WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$my_cvs = $stmt->fetchAll();

$display_name = $_SESSION['nom'] ?? 'Candidat';
$page_title = "Mon Espace";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Espace Candidat - CVMatch IA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #4f46e5, #4338ca);">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">
            <span class="logo-icon">🤖</span>
            CVMatch IA
        </a>
        <div class="d-flex align-items-center gap-3">
            <span class="text-white d-none d-md-inline">
                <i class="fas fa-user-circle"></i> <?= htmlspecialchars($display_name) ?>
            </span>
            <a href="../logout.php" class="btn btn-light btn-sm">
                <i class="fas fa-sign-out-alt"></i> Déconnexion
            </a>
        </div>
    </div>
</nav>

<div class="container py-5">

    <!-- MESSAGES -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i>
            <?php 
                if ($_GET['success'] == 'deleted') echo "Le CV a été supprimé avec succès.";
                if ($_GET['success'] == 'replaced') echo "Le CV a été remplacé et analysé par l'IA.";
                if ($_GET['success'] == 'replaced_ia_pending') echo "CV remplacé, mais l'IA n'a pas pu l'analyser.";
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- PROFIL -->
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body py-4">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($display_name) ?>&background=4f46e5&color=fff&size=128" 
                         class="rounded-circle mb-3" width="100" height="100" alt="Avatar">
                    <h4 class="fw-bold mb-1"><?= htmlspecialchars($display_name) ?></h4>
                    <p class="text-muted small mb-3">
                        <i class="fas fa-user-tag"></i> Candidat
                    </p>
                    <a href="upload_cv.php" class="btn btn-primary w-100">
                        <i class="fas fa-cloud-upload-alt"></i> Déposer un CV
                    </a>
                </div>
            </div>
        </div>

        <!-- CV -->
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="fw-bold mb-0">
                    <i class="fas fa-file-alt text-primary"></i> 
                    Mes CV (<?= count($my_cvs) ?>)
                </h3>
            </div>

            <?php if (empty($my_cvs)): ?>
                <div class="card text-center py-5">
                    <div class="card-body">
                        <i class="fas fa-folder-open text-muted" style="font-size: 4rem;"></i>
                        <h5 class="mt-3">Aucun CV pour le moment</h5>
                        <p class="text-muted">Commencez par déposer votre premier CV.</p>
                        <a href="upload_cv.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Déposer mon premier CV
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($my_cvs as $cv): ?>
                    <div class="card mb-3 fade-in-up">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 50px; height: 50px;">
                                            <i class="fas fa-file-pdf fa-lg"></i>
                                        </div>
                                        <div>
                                            <a href="../<?= htmlspecialchars($cv['file_path']) ?>" 
                                               target="_blank" 
                                               class="text-decoration-none fw-semibold">
                                                <?= basename($cv['file_path']) ?>
                                            </a>
                                            <p class="text-muted small mb-0">
                                                <i class="fas fa-clock"></i> 
                                                <?= date('d/m/Y à H:i', strtotime($cv['created_at'])) ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center">
                                    <?php if ($cv['status'] == 'Analysé'): ?>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check"></i> Analysé par l'IA
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-spinner"></i> En attente
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-3 text-end">
                                    <a href="upload_cv.php?replace=<?= $cv['id'] ?>" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-sync-alt"></i>
                                    </a>
                                    <a href="delete_cv.php?id=<?= $cv['id'] ?>" 
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Supprimer ce CV ?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>