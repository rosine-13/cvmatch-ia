<?php
require_once '../config.php';

if (!is_logged_in() || !is_recruiter()) {
    header("Location: login.php");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title    = clean_input($_POST['title']);
    $desc     = clean_input($_POST['description']);
    $skills   = clean_input($_POST['required_skills']);
    $location = clean_input($_POST['location']);
    $contract = clean_input($_POST['contract_type']);

    if (empty($title) || empty($desc) || empty($skills)) {
        $message = "<div class='alert alert-danger'>Titre, description et compétences sont obligatoires.</div>";
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO jobs (recruiter_id, title, description, required_skills, location, contract_type)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$_SESSION['user_id'], $title, $desc, $skills, $location, $contract]);
            
            $message = "<div class='alert alert-success'>✅ Offre publiée avec succès !</div>";
        } catch (Exception $e) {
            $message = "<div class='alert alert-danger'>❌ Erreur : " . $e->getMessage() . "</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Publier une offre - CVMatch IA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-success mb-4">
    <div class="container">
        <span class="navbar-brand">CVMatch IA | Publier une offre</span>
        <a href="dashboard.php" class="btn btn-outline-light btn-sm">← Retour</a>
    </div>
</nav>

<div class="container py-3">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm p-4">
                <h3 class="mb-4">📢 Publier une nouvelle offre</h3>
                <?= $message ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Titre du poste *</label>
                        <input type="text" name="title" class="form-control" placeholder="Ex: Développeur PHP Senior" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description *</label>
                        <textarea name="description" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Compétences requises *</label>
                        <input type="text" name="required_skills" class="form-control" 
                               placeholder="Ex: PHP, MySQL, JavaScript, Bootstrap" required>
                        <div class="form-text">Séparez les compétences par des virgules.</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lieu</label>
                            <input type="text" name="location" class="form-control" placeholder="Ex: Abidjan">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type de contrat</label>
                            <select name="contract_type" class="form-select">
                                <option value="CDI">CDI</option>
                                <option value="CDD">CDD</option>
                                <option value="Stage">Stage</option>
                                <option value="Freelance">Freelance</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Publier l'offre</button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>