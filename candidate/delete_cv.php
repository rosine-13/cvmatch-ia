<?php
require_once '../config.php';

// 1. Sécurité : Vérifier que l'utilisateur est connecté et est un candidat
if (!is_logged_in() || !is_candidate()) {
    header("Location: login.php");
    exit;
}

// 2. Vérifier qu'un ID de CV est passé dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: dashboard.php?error=missing_id");
    exit;
}

$cv_id = intval($_GET['id']); // Sécurisation : on force en entier
$user_id = $_SESSION['user_id'];

try {
    // 3. Vérifier que ce CV appartient bien à l'utilisateur connecté
    $stmt = $pdo->prepare("SELECT file_path FROM cvs WHERE id = ? AND user_id = ?");
    $stmt->execute([$cv_id, $user_id]);
    $cv = $stmt->fetch();

    if (!$cv) {
        // Le CV n'existe pas OU n'appartient pas à cet utilisateur
        header("Location: dashboard.php?error=not_found");
        exit;
    }

    // 4. Supprimer le fichier physique du serveur
    $file_path = '../' . $cv['file_path'];
    if (file_exists($file_path)) {
        unlink($file_path); // Supprime le fichier
    }

    // 5. Supprimer l'entrée dans la base de données
    $delete = $pdo->prepare("DELETE FROM cvs WHERE id = ? AND user_id = ?");
    $delete->execute([$cv_id, $user_id]);

    // 6. Rediriger vers le dashboard avec un message de succès
    header("Location: dashboard.php?success=deleted");
    exit;

} catch (Exception $e) {
    header("Location: dashboard.php?error=db_error");
    exit;
}
?>