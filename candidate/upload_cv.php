<?php
require_once '../config.php';

// 1. Sécurité
if (!is_logged_in() || !is_candidate()) {
    header("Location: login.php");
    exit;
}

$message = '';
$replace_id = isset($_GET['replace']) ? intval($_GET['replace']) : null;
$is_replacing = false;
$old_cv = null;

// 2. Si on est en mode "remplacement", on récupère l'ancien CV
if ($replace_id) {
    $stmt = $pdo->prepare("SELECT * FROM cvs WHERE id = ? AND user_id = ?");
    $stmt->execute([$replace_id, $_SESSION['user_id']]);
    $old_cv = $stmt->fetch();

    if ($old_cv) {
        $is_replacing = true;
    } else {
        header("Location: dashboard.php?error=not_found");
        exit;
    }
}

// 3. Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['cv_file'])) {
    $file = $_FILES['cv_file'];
    $validation = validate_uploaded_file($file);

    if ($validation['success']) {

        if (!is_dir('../uploads')) {
            mkdir('../uploads', 0777, true);
        }

        $filename = time() . '_' . basename($file['name']);
        $target_path = '../uploads/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            $db_path = 'uploads/' . $filename;

            try {
                if ($is_replacing) {
                    // --- MODE REMPLACEMENT ---
                    // 1. Supprimer l'ancien fichier physique
                    $old_file = '../' . $old_cv['file_path'];
                    if (file_exists($old_file)) {
                        unlink($old_file);
                    }

                    // 2. Mettre à jour la base de données (même ID)
                    $stmt = $pdo->prepare("UPDATE cvs SET file_path = ?, status = 'En attente', created_at = NOW() WHERE id = ? AND user_id = ?");
                    $stmt->execute([$db_path, $replace_id, $_SESSION['user_id']]);

                    // ✅ APPEL DE L'IA PYTHON (pour ré-analyser le nouveau fichier)
                    $ia_result = call_ia_process_cv($replace_id, $db_path);

                    // Redirection avec message adapté
                    if ($ia_result['status'] === 'success') {
                        header("Location: dashboard.php?success=replaced");
                    } else {
                        header("Location: dashboard.php?success=replaced_ia_pending");
                    }
                    exit;

                } else {
                    // --- MODE AJOUT NORMAL ---
                    $stmt = $pdo->prepare("INSERT INTO cvs (user_id, file_path, status) VALUES (?, ?, 'En attente')");
                    $stmt->execute([$_SESSION['user_id'], $db_path]);
                    $new_cv_id = $pdo->lastInsertId(); // On récupère l'ID du nouveau CV

                    // ✅ APPEL DE L'IA PYTHON
                    $ia_result = call_ia_process_cv($new_cv_id, $db_path);

                    if ($ia_result['status'] === 'success') {
                        $message = "<div class='alert alert-success'>
                                        <strong>✅ Succès !</strong> Votre CV a été envoyé et analysé par l'IA.
                                    </div>";
                    } else {
                        $message = "<div class='alert alert-warning'>
                                        <strong>⚠️ CV envoyé.</strong> L'analyse IA est en cours. 
                                        <br><small>Détail : " . htmlspecialchars($ia_result['message'] ?? 'Erreur inconnue') . "</small>
                                    </div>";
                    }
                }
            } catch (Exception $e) {
                $message = "<div class='alert alert-danger'>
                                <strong>❌ Erreur :</strong> " . $e->getMessage() . "
                            </div>";
            }
        } else {
            $message = "<div class='alert alert-danger'>Impossible de déplacer le fichier.</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>" . $validation['error'] . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= $is_replacing ? 'Remplacer mon CV' : 'Déposer mon CV' ?> - CVMatch IA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-primary mb-4">
    <div class="container">
        <span class="navbar-brand">
            <i class="fas fa-file-upload"></i> 
            CVMatch IA | <?= $is_replacing ? 'Remplacement de CV' : 'Dépôt de CV' ?>
        </span>
        <a href="dashboard.php" class="btn btn-outline-light btn-sm">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
</nav>

<div class="container py-3">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm p-4">

                <h4 class="mb-1">
                    <i class="fas fa-cloud-upload-alt text-primary"></i> 
                    <?= $is_replacing ? 'Remplacer mon CV' : 'Déposer mon CV' ?>
                </h4>
                
                <?php if ($is_replacing): ?>
                    <div class="alert alert-info">
                        <strong>ℹ️ Info :</strong> Vous êtes en train de remplacer le fichier suivant :<br>
                        <code><?= basename($old_cv['file_path']) ?></code>
                    </div>
                <?php endif; ?>

                <p class="text-muted mb-4">Format accepté : PDF uniquement (max 5 Mo)</p>

                <?= $message ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <?= $is_replacing ? 'Choisir le nouveau fichier' : 'Choisir un fichier' ?>
                        </label>
                        <input type="file" 
                               name="cv_file" 
                               class="form-control" 
                               accept=".pdf"
                               required>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> 
                            <?= $is_replacing ? 'Remplacer le CV' : 'Envoyer mon CV' ?>
                        </button>
                        <a href="dashboard.php" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

</body>
</html>