<?php
// config.php - Configuration centrale de CVMatch IA

// ============================================
// 1. DÉMARRAGE DE LA SESSION
// ============================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================
// 2. CHARGEMENT DU FICHIER .env
// ============================================
/**
 * Charge les variables depuis le fichier .env
 */
function load_env($path) {
    if (!file_exists($path)) {
        die("⚠️ Fichier .env introuvable. Copiez '.env.example' en '.env' et remplissez vos valeurs.");
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Ignorer les commentaires
        if (strpos(trim($line), '#') === 0) continue;
        // Ignorer les lignes sans "="
        if (strpos($line, '=') === false) continue;
        
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        
        if (!empty($key)) {
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

// Charger le .env à la racine du projet
load_env(__DIR__ . '/.env');

// ============================================
// 3. PARAMÈTRES DE CONNEXION À LA BASE DE DONNÉES
// ============================================
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'cvmatchia_db');

// Configuration du microservice IA
define('IA_HOST', $_ENV['IA_HOST'] ?? '127.0.0.1');
define('IA_PORT', $_ENV['IA_PORT'] ?? 5000);

try {
    // Connexion via PDO avec support de l'UTF-8 pour les accents
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", 
        DB_USER, 
        DB_PASS
    );
    
    // Activation des erreurs pour nous aider à débugger
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Mode de récupération par défaut : Tableaux associatifs
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// ============================================
// 4. FONCTIONS DE SÉCURITÉ ET UTILITAIRES
// ============================================

/**
 * Nettoie les données saisies par l'utilisateur (évite les failles XSS)
 */
/**
 * Nettoie les données saisies par l'utilisateur
 * ⚠️ On NE fait PAS htmlspecialchars ici (on le fera à l'affichage)
 */
function clean_input($data) {
    return trim($data);
}

/**
 * Vérifie si l'utilisateur est connecté au site
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Vérifie si la personne connectée est un CANDIDAT
 */
function is_candidate() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'candidate';
}

/**
 * Vérifie si la personne connectée est un RECRUTEUR
 */
function is_recruiter() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'recruiter';
}

/**
 * Valide le fichier uploadé (Format et Taille)
 */
function validate_uploaded_file($file) {
    $allowed_extensions = ['pdf'];
    $file_info = pathinfo($file['name']);
    $file_extension = strtolower($file_info['extension']);
    
    // Vérification de l'extension
    if (!in_array($file_extension, $allowed_extensions)) {
        return [
            'success' => false, 
            'error' => "Format non supporté. Veuillez utiliser un PDF."
        ];
    }
    
    // Vérification de la taille (Max 5 Mo)
    if ($file['size'] > 5000000) {
        return [
            'success' => false, 
            'error' => "Le fichier est trop lourd. La limite est de 5 Mo."
        ];
    }
    
    return ['success' => true];
}

// ============================================
// 5. FONCTIONS D'APPEL AU MICROSERVICE IA
// ============================================

/**
 * Fonction générique d'appel à l'IA
 */
function call_ia($endpoint, $payload, $timeout = 60) {
    $url = "http://" . IA_HOST . ":" . IA_PORT . "/" . $endpoint;
    $data = json_encode($payload);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code === 200) {
        return json_decode($response, true);
    }
    
    return [
        'status' => 'error', 
        'message' => "IA non disponible (HTTP $http_code)"
    ];
}

/**
 * Traite un CV (extraction de texte via pdfplumber + Groq)
 */
function call_ia_process_cv($cv_id, $file_path) {
    return call_ia('process_cv', [
        'cv_id' => $cv_id,
        'file_path' => $file_path
    ], 30);
}

/**
 * Compare TOUS les CV avec une offre (matching par offre)
 */
function call_ia_match_all($job_id) {
    return call_ia('match_all', ['job_id' => $job_id], 180);
}

/**
 * Recherche libre par mots-clés (matching par requête)
 */
function call_ia_search($query) {
    return call_ia('search', ['query' => $query], 180);
}

?>