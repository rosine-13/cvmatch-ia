<?php
require_once '../config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = clean_input($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        if ($user['role'] === 'candidate') {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['role'] = $user['role'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Accès refusé : vous n'êtes pas un candidat.";
        }
    } else {
        $error = "Email ou mot de passe incorrect.";
    }
}

$page_title = "Connexion Candidat";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Candidat - CVMatch IA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem;
        }
        .login-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            padding: 3rem;
            width: 100%;
            max-width: 440px;
        }
        .login-logo {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #4f46e5, #4338ca);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 1.5rem;
            box-shadow: 0 10px 30px rgba(79, 70, 229, 0.3);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card fade-in-up">
            <div class="text-center mb-4">
                <div class="login-logo">🤖</div>
                <h2 class="fw-bold mb-1">Bienvenue</h2>
                <p class="text-muted">Connectez-vous à votre espace candidat</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">
                        <i class="fas fa-envelope text-muted"></i> Email
                    </label>
                    <input type="email" name="email" class="form-control form-control-lg" 
                           placeholder="votre@email.com" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">
                        <i class="fas fa-lock text-muted"></i> Mot de passe
                    </label>
                    <input type="password" name="password" class="form-control form-control-lg" 
                           placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                    <i class="fas fa-sign-in-alt"></i> Se connecter
                </button>
            </form>

            <div class="text-center">
                <p class="text-muted mb-2">
                    Pas encore de compte ? 
                    <a href="register.php" class="text-decoration-none fw-semibold">S'inscrire</a>
                </p>
                <a href="../index.php" class="text-muted small text-decoration-none">
                    <i class="fas fa-arrow-left"></i> Retour à l'accueil
                </a>
            </div>
        </div>
    </div>
</body>
</html>