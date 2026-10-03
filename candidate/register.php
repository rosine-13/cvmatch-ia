<?php 
require_once '../config.php'; 

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Récupération et nettoyage des données
    $nom       = clean_input($_POST['nom']);
    $prenom    = clean_input($_POST['prenom']);
    $email     = clean_input($_POST['email']);
    $ville     = clean_input($_POST['ville']);
    $password  = $_POST['password'];

    // 2. Vérifications de base
    if (empty($nom) || empty($prenom) || empty($email) || empty($password)) {
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
                // 4. DEBUT DE LA TRANSACTION (On remplit deux tables)
                $pdo->beginTransaction();

                // --- Insertion dans 'users' ---
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $ins_user = $pdo->prepare("INSERT INTO users (nom, email, password, role) VALUES (?, ?, ?, 'candidate')");
                $ins_user->execute([$nom, $email, $hashed_password]);
                
                $user_id = $pdo->lastInsertId(); // On récupère l'ID du nouvel utilisateur

                // --- Insertion dans 'candidate_profiles' ---
                // On combine le Nom et le Prénom pour le champ 'full_name'
                $full_name = $nom . ' ' . $prenom; 
                
                // On insère uniquement les infos de base. Le reste (phone, bio, skills...) sera NULL.
                $ins_profile = $pdo->prepare("INSERT INTO candidate_profiles (user_id, full_name, city) VALUES (?, ?, ?)");
                $ins_profile->execute([$user_id, $full_name, $ville]);

                $pdo->commit(); // On valide les deux insertions
                $success = "Inscription réussie ! <a href='login.php'>Connectez-vous ici</a>";
            }
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack(); // En cas d'erreur, on annule tout
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
    <title>Inscription Candidat - CVMatch IA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4>Créer un compte Candidat</h4>
                </div>
                <div class="card-body">
                    <?php if($error): ?> <div class="alert alert-danger"><?= $error ?></div> <?php endif; ?>
                    <?php if($success): ?> <div class="alert alert-success"><?= $success ?></div> <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label>Nom</label>
                            <input type="text" name="nom" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Prénom</label>
                            <input type="text" name="prenom" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Ville</label>
                            <input type="text" name="ville" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Mot de passe</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">S'inscrire</button>
                    </form>
                    <p class="text-center mt-3">Déjà un compte ? <a href="login.php">Se connecter</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>