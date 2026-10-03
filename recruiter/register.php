<?php 
require_once '../config.php'; 

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Récupération des données
    $company_name = clean_input($_POST['company_name']);
    $email        = clean_input($_POST['email']);
    $sector       = clean_input($_POST['sector']);
    $phone        = clean_input($_POST['phone']);
    $password     = $_POST['password'];

    // 2. Vérifications
    if (empty($company_name) || empty($email) || empty($password)) {
        $error = "Veuillez remplir tous les champs obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format d'email invalide.";
    } else {
        try {
            // 3. Vérifier si l'email existe déjà
            $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $check->execute([$email]);
            
            if ($check->fetch()) {
                $error = "Cet email est déjà utilisé.";
            } else {
                // 4. TRANSACTION
                $pdo->beginTransaction();

                // --- Insertion dans 'users' avec role = 'recruiter' ---
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $ins_user = $pdo->prepare("INSERT INTO users (nom, email, password, role) VALUES (?, ?, ?, 'recruiter')");
                $ins_user->execute([$company_name, $email, $hashed_password]);
                
                $user_id = $pdo->lastInsertId();

                // --- Insertion dans 'recruiter_profiles' ---
                $ins_profile = $pdo->prepare("
                    INSERT INTO recruiter_profiles (user_id, company_name, company_sector, phone) 
                    VALUES (?, ?, ?, ?)
                ");
                $ins_profile->execute([$user_id, $company_name, $sector, $phone]);

                $pdo->commit();
                $success = "Compte recruteur créé avec succès ! <a href='login.php'>Connectez-vous ici</a>";
            }
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = "Erreur lors de l'inscription : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription Recruteur - CVMatch IA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-dark">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-header bg-success text-white text-center py-3">
                    <h4><i class="fas fa-building"></i> Créer un compte Recruteur</h4>
                </div>
                <div class="card-body p-4">
                    
                    <?php if($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    <?php if($success): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Nom de l'entreprise <span class="text-danger">*</span></label>
                            <input type="text" name="company_name" class="form-control" placeholder="Ex: Google, TechCorp..." required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Secteur d'activité</label>
                            <input type="text" name="sector" class="form-control" placeholder="Ex: Informatique, Marketing, Santé...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email professionnel <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="contact@entreprise.com" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Téléphone</label>
                            <input type="text" name="phone" class="form-control" placeholder="+225 01 02 03 04 05">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mot de passe <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100 py-2">
                            <i class="fas fa-user-plus"></i> S'inscrire
                        </button>
                    </form>
                    
                </div>
                <div class="card-footer text-center">
                    <p class="mb-1">Déjà un compte ? <a href="login.php">Se connecter</a></p>
                    <a href="../index.php" class="text-muted small">Retour au site</a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>