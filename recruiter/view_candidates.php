<?php
require_once '../config.php';

if (!is_logged_in() || !is_recruiter()) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['job_id']) || empty($_GET['job_id'])) {
    header("Location: dashboard.php");
    exit;
}

$job_id = intval($_GET['job_id']);

$stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = ? AND recruiter_id = ?");
$stmt->execute([$job_id, $_SESSION['user_id']]);
$job = $stmt->fetch();

if (!$job) {
    header("Location: dashboard.php");
    exit;
}

$ia_result = call_ia_match_all($job_id);
$candidates = [];

if ($ia_result && $ia_result['status'] === 'success') {
    foreach ($ia_result['results'] as $res) {
        $stmt = $pdo->prepare("
            SELECT cvs.id AS cv_id, cvs.file_path, 
                   cp.full_name, cp.city, cp.skills
            FROM cvs 
            LEFT JOIN candidate_profiles cp ON cvs.user_id = cp.user_id
            WHERE cvs.id = ?
        ");
        $stmt->execute([$res['cv_id']]);
        $cv_data = $stmt->fetch();

        if ($cv_data) {
            $candidates[] = [
                'cv_id' => $res['cv_id'],
                'score' => $res['score'],
                'full_name' => $cv_data['full_name'] ?? 'Candidat',
                'city' => $cv_data['city'] ?? 'Ville inconnue',
                'skills' => $cv_data['skills'] ?? '',
                'file_path' => $cv_data['file_path']
            ];
        }
    }
}
$page_title = "Résultats IA";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats IA - CVMatch IA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #10b981, #059669);">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">
            <span class="logo-icon">🤖</span>
            CVMatch IA
        </a>
        <a href="dashboard.php" class="btn btn-outline-light btn-sm">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
</nav>

<div class="container py-5">

    <!-- Carte de l'offre -->
    <div class="card mb-4 fade-in-up">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="fw-bold mb-2">
                        <i class="fas fa-briefcase text-success"></i> 
                        <?= htmlspecialchars($job['title']) ?>
                    </h2>
                    <p class="text-muted mb-2">
                        <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($job['location']) ?>
                        <span class="mx-2">|</span>
                        <i class="fas fa-file-contract"></i> <?= htmlspecialchars($job['contract_type']) ?>
                    </p>
                    <p class="mb-0">
                        <strong><i class="fas fa-code"></i> Compétences requises :</strong><br>
                        <?php foreach (explode(',', $job['required_skills']) as $skill): ?>
                            <span class="badge bg-primary bg-opacity-10 text-primary me-1 mt-1">
                                <?= htmlspecialchars(trim($skill)) ?>
                            </span>
                        <?php endforeach; ?>
                    </p>
                </div>
                <div class="col-md-4 text-center">
                    <div class="display-4 fw-bold text-success"><?= count($candidates) ?></div>
                    <div class="text-muted">Candidat(s) analysé(s)</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Titre résultats -->
    <h4 class="mb-3 fw-bold">
        <i class="fas fa-trophy text-warning"></i> 
        Classement par pertinence (IA)
    </h4>

    <?php if (empty($candidates)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Aucun CV n'a été analysé pour le moment.
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($candidates as $index => $c): ?>
                <?php
                    if ($c['score'] >= 70) {
                        $color = 'success'; $icon = 'fa-star'; $label = 'Excellent match';
                    } elseif ($c['score'] >= 40) {
                        $color = 'warning'; $icon = 'fa-thumbs-up'; $label = 'Bon match';
                    } else {
                        $color = 'danger'; $icon = 'fa-thumbs-down'; $label = 'Match faible';
                    }
                    $rank = $index + 1;
                ?>
                <div class="col-md-6 fade-in-up">
                    <div class="card h-100 border-0">
                        <div class="card-body">
                            <!-- En-tête avec rang -->
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-<?= $color ?> bg-opacity-10 text-<?= $color ?> rounded-circle d-flex align-items-center justify-content-center fw-bold" 
                                         style="width: 50px; height: 50px; font-size: 1.3rem;">
                                        #<?= $rank ?>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-0">
                                            <?= htmlspecialchars($c['full_name']) ?>
                                        </h5>
                                        <p class="text-muted small mb-0">
                                            <i class="fas fa-map-marker-alt"></i> 
                                            <?= htmlspecialchars($c['city']) ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Score -->
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-semibold">
                                    <i class="fas <?= $icon ?> text-<?= $color ?>"></i> 
                                    <?= $label ?>
                                </span>
                                <span class="h4 fw-bold text-<?= $color ?> mb-0">
                                    <?= $c['score'] ?>%
                                </span>
                            </div>

                            <div class="progress mb-3" style="height: 10px;">
                                <div class="progress-bar bg-<?= $color ?>" 
                                     style="width: <?= $c['score'] ?>%"></div>
                            </div>

                            <!-- Compétences -->
                            <?php if (!empty($c['skills'])): ?>
                                <div class="mb-3">
                                    <small class="text-muted fw-semibold">Compétences déclarées :</small><br>
                                    <?php foreach (explode(',', $c['skills']) as $skill): ?>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary me-1 mt-1">
                                            <?= htmlspecialchars(trim($skill)) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <a href="../<?= htmlspecialchars($c['file_path']) ?>" 
                               target="_blank" 
                               class="btn btn-outline-primary w-100">
                                <i class="fas fa-file-pdf"></i> Consulter le CV
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>