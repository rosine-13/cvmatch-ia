<?php
require_once '../config.php';

if (!is_logged_in() || !is_recruiter()) {
    header("Location: login.php");
    exit;
}

$query = isset($_GET['query']) ? clean_input($_GET['query']) : '';
$candidates = [];

if (!empty($query)) {
    $ia_result = call_ia_search($query);

    if ($ia_result && $ia_result['status'] === 'success') {
        foreach ($ia_result['results'] as $res) {
            $stmt = $pdo->prepare("
                SELECT cvs.id AS cv_id, cvs.file_path, 
                       cp.full_name, cp.city
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
                    'justification' => $res['justification'] ?? '',
                    'points_forts' => $res['points_forts'] ?? [],
                    'points_faibles' => $res['points_faibles'] ?? [],
                    'full_name' => $cv_data['full_name'] ?? 'Candidat',
                    'city' => $cv_data['city'] ?? '',
                    'file_path' => $cv_data['file_path']
                ];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche de candidats - CVMatch IA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #10b981, #059669);">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">
            <span class="logo-icon">🤖</span> CVMatch IA
        </a>
        <a href="dashboard.php" class="btn btn-outline-light btn-sm">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
</nav>

<div class="container py-5">

    <!-- Titre -->
    <div class="text-center mb-4">
        <h2 class="fw-bold">
            <i class="fas fa-search text-success"></i> Rechercher des candidats
        </h2>
        <p class="text-muted">Décrivez le profil recherché.</p>
    </div>

    <!-- Formulaire -->
    <div class="card shadow-sm mb-4">
        <div class="card-body p-4">
            <form method="GET" class="d-flex gap-2">
                <input type="text" name="query" class="form-control form-control-lg" 
                       value="<?= htmlspecialchars($query, ENT_QUOTES, 'UTF-8') ?>"
                       placeholder="Ex: Développeur PHP, Data Analyst, Marketing..." autofocus>
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-search"></i> Rechercher
                </button>
            </form>
        </div>
    </div>

    <!-- Résultats -->
    <?php if (!empty($query)): ?>
        <p class="text-muted small mb-3">
            <strong><?= count($candidates) ?></strong> candidat(s) correspondant(s)
        </p>

        <?php if (empty($candidates)): ?>
            <div class="alert alert-light border">
                <i class="fas fa-info-circle text-muted"></i> 
                Aucun candidat ne correspond à votre recherche.
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($candidates as $c): ?>
                    <?php
                        // Couleur sobre selon le score
                        if ($c['score'] >= 80) { $color = 'success'; }
                        elseif ($c['score'] >= 60) { $color = 'primary'; }
                        else { $color = 'secondary'; }
                    ?>
                    <div class="col-md-6">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body p-4">

                                <!-- Nom + Ville -->
                                <h5 class="fw-bold mb-1"><?= htmlspecialchars($c['full_name']) ?></h5>
                                <?php if (!empty($c['city'])): ?>
                                    <p class="text-muted small mb-3">
                                        <i class="fas fa-map-marker-alt"></i> 
                                        <?= htmlspecialchars($c['city']) ?>
                                    </p>
                                <?php endif; ?>

                                <!-- Score -->
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar bg-<?= $color ?>" 
                                             style="width: <?= $c['score'] ?>%"></div>
                                    </div>
                                    <span class="fw-bold text-<?= $color ?>">
                                        <?= $c['score'] ?>%
                                    </span>
                                </div>

                                <!-- Justification courte -->
                                <?php if (!empty($c['justification'])): ?>
                                    <p class="text-muted small mb-3">
                                        <?= htmlspecialchars($c['justification']) ?>
                                    </p>
                                <?php endif; ?>

                                <!-- Points forts -->
                                <?php if (!empty($c['points_forts'])): ?>
                                    <div class="mb-2">
                                        <?php 
                                            $points = is_array($c['points_forts']) 
                                                ? $c['points_forts'] 
                                                : [$c['points_forts']];
                                            foreach (array_slice($points, 0, 2) as $pt): 
                                        ?>
                                            <div class="small text-success">
                                                <i class="fas fa-check"></i> 
                                                <?= htmlspecialchars(trim($pt)) ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Points faibles -->
                                <?php if (!empty($c['points_faibles'])): ?>
                                    <div class="mb-3">
                                        <?php 
                                            $points = is_array($c['points_faibles']) 
                                                ? $c['points_faibles'] 
                                                : [$c['points_faibles']];
                                            foreach (array_slice($points, 0, 1) as $pt): 
                                        ?>
                                            <div class="small text-muted">
                                                <i class="fas fa-info-circle"></i> 
                                                <?= htmlspecialchars(trim($pt)) ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Bouton CV -->
                                <a href="../<?= htmlspecialchars($c['file_path']) ?>" 
                                   target="_blank" 
                                   class="btn btn-outline-primary btn-sm w-100 mt-2">
                                    <i class="fas fa-file-pdf"></i> Consulter le CV
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

</body>
</html>