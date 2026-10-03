<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CVMatch IA - Recrutement intelligent par Intelligence Artificielle</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: #f8fafc;
            overflow-x: hidden;
        }

        /* ============================
           NAVBAR
        ============================ */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.05);
            padding: 1rem 0;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            color: #4f46e5 !important;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-brand .logo-icon {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        /* ============================
           HERO SECTION
        ============================ */
        .hero {
            position: relative;
            padding: 120px 0 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(255,255,255,0.1), transparent 70%);
            border-radius: 50%;
        }

        .hero::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255,255,255,0.08), transparent 70%);
            border-radius: 50%;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 0.5rem 1.2rem;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 1.5rem;
            backdrop-filter: blur(10px);
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 1.5rem;
        }

        .hero h1 .highlight {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero p.lead {
            font-size: 1.2rem;
            opacity: 0.95;
            max-width: 600px;
            margin: 0 auto 2rem;
        }

        .hero-stats {
            display: flex;
            justify-content: center;
            gap: 3rem;
            margin-top: 3rem;
            flex-wrap: wrap;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            display: block;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.85;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* ============================
           SECTION FONCTIONNALITÉS
        ============================ */
        .section-title {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 1rem;
        }

        .section-title p {
            color: #64748b;
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }

        /* ============================
           CARTES DE RÔLE
        ============================ */
        .role-card {
            background: white;
            border-radius: 24px;
            padding: 2.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            border: 2px solid transparent;
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .role-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
        }

        .role-card.candidate::before {
            background: linear-gradient(90deg, #4f46e5, #7c3aed);
        }

        .role-card.recruiter::before {
            background: linear-gradient(90deg, #10b981, #059669);
        }

        .role-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.12);
        }

        .role-card.candidate:hover {
            border-color: #4f46e5;
        }

        .role-card.recruiter:hover {
            border-color: #10b981;
        }

        .role-icon {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 1.5rem;
            color: white;
        }

        .role-card.candidate .role-icon {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            box-shadow: 0 8px 24px rgba(79, 70, 229, 0.3);
        }

        .role-card.recruiter .role-icon {
            background: linear-gradient(135deg, #10b981, #059669);
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.3);
        }

        .role-card h3 {
            font-size: 1.6rem;
            font-weight: 800;
            margin-bottom: 1rem;
            color: #1e293b;
        }

        .role-card ul {
            list-style: none;
            padding: 0;
            margin-bottom: 2rem;
        }

        .role-card ul li {
            padding: 0.5rem 0;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .role-card ul li i {
            font-size: 0.9rem;
        }

        .role-card.candidate ul li i {
            color: #4f46e5;
        }

        .role-card.recruiter ul li i {
            color: #10b981;
        }

        .btn-role {
            border-radius: 12px;
            padding: 0.9rem 1.5rem;
            font-weight: 600;
            border: none;
            width: 100%;
            transition: all 0.2s;
        }

        .btn-candidate {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
        }

        .btn-candidate:hover {
            background: linear-gradient(135deg, #4338ca, #6d28d9);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.4);
            color: white;
        }

        .btn-recruiter {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .btn-recruiter:hover {
            background: linear-gradient(135deg, #059669, #047857);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
            color: white;
        }

        /* ============================
           SECTION IA
        ============================ */
        .ia-section {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            color: white;
            padding: 80px 0;
            border-radius: 30px;
            margin: 80px 0;
        }

        .ia-step {
            text-align: center;
            padding: 1rem;
        }

        .ia-step-number {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            color: #1e293b;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.3rem;
            margin: 0 auto 1rem;
        }

        /* ============================
           FOOTER
        ============================ */
        footer {
            background: #1e293b;
            color: #94a3b8;
            padding: 40px 0 20px;
            margin-top: 80px;
        }

        footer a {
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.2s;
        }

        footer a:hover {
            color: white;
        }

        /* ============================
           ANIMATIONS
        ============================ */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.6s ease forwards;
        }

        /* ============================
           RESPONSIVE
        ============================ */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.2rem;
            }
            .hero-stats {
                gap: 1.5rem;
            }
            .stat-number {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>

<!-- ============================
     NAVBAR
============================ -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <span class="logo-icon">🤖</span>
            CVMatch IA
        </a>
        <div class="d-flex gap-2">
            <a href="candidate/login.php" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-sign-in-alt"></i> Connexion
            </a>
            <a href="candidate/register.php" class="btn btn-primary btn-sm">
                <i class="fas fa-user-plus"></i> S'inscrire
            </a>
        </div>
    </div>
</nav>

<!-- ============================
     HERO
============================ -->
<section class="hero" style="margin-top: 76px;">
    <div class="container hero-content">
        <div class="text-center">
            <div class="hero-badge fade-in-up">
                <i class="fas fa-sparkles"></i>
                Propulsé par l'Intelligence Artificielle
            </div>
            <h1 class="fade-in-up">
                Trouvez le <span class="highlight">match parfait</span><br>
                entre CV et offres d'emploi
            </h1>
            <p class="lead fade-in-up">
                CVMatch IA analyse automatiquement les CV et les offres pour vous proposer 
                les meilleurs candidats en quelques secondes.
            </p>
            <div class="d-flex justify-content-center gap-3 flex-wrap fade-in-up">
                <a href="candidate/register.php" class="btn btn-light btn-lg px-4">
                    <i class="fas fa-user-graduate"></i> Je suis candidat
                </a>
                <a href="recruiter/register.php" class="btn btn-outline-light btn-lg px-4">
                    <i class="fas fa-building"></i> Je suis recruteur
                </a>
            </div>

            <!-- Statistiques -->
            <div class="hero-stats fade-in-up">
                <div class="stat-item">
                    <span class="stat-number">🤖</span>
                    <span class="stat-label">IA Analyse</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">⚡</span>
                    <span class="stat-label">Rapide</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">🎯</span>
                    <span class="stat-label">Précis</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">100%</span>
                    <span class="stat-label">Automatisé</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================
     FONCTIONNALITÉS
============================ -->
<section class="container py-5" style="margin-top: 60px;">
    <div class="section-title">
        <h2>Comment ça marche ?</h2>
        <p>Un processus simple et intelligent en 4 étapes</p>
    </div>

    <div class="row g-4">
        <div class="col-md-3">
            <div class="text-center">
                <div class="feature-icon mx-auto" style="background: #e0e7ff; color: #4f46e5;">
                    <i class="fas fa-file-upload"></i>
                </div>
                <h5 class="fw-bold">1. Déposez votre CV</h5>
                <p class="text-muted small">Uploadez votre CV au format PDF en quelques secondes.</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="text-center">
                <div class="feature-icon mx-auto" style="background: #fef3c7; color: #d97706;">
                    <i class="fas fa-robot"></i>
                </div>
                <h5 class="fw-bold">2. L'IA analyse</h5>
                <p class="text-muted small">Notre IA extrait et comprend le contenu de votre CV.</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="text-center">
                <div class="feature-icon mx-auto" style="background: #d1fae5; color: #059669;">
                    <i class="fas fa-bullseye"></i>
                </div>
                <h5 class="fw-bold">3. Matching intelligent</h5>
                <p class="text-muted small">Les CV sont comparés aux offres d'emploi.</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="text-center">
                <div class="feature-icon mx-auto" style="background: #fce7f3; color: #db2777;">
                    <i class="fas fa-trophy"></i>
                </div>
                <h5 class="fw-bold">4. Classement</h5>
                <p class="text-muted small">Les meilleurs profils sont classés par pertinence.</p>
            </div>
        </div>
    </div>
</section>

<!-- ============================
     CHOIX DU RÔLE
============================ -->
<section class="container py-5">
    <div class="section-title">
        <h2>Choisissez votre espace</h2>
        <p>Que vous soyez candidat ou recruteur, CVMatch IA est fait pour vous.</p>
    </div>

    <div class="row g-4 justify-content-center">
        <!-- CANDIDAT -->
        <div class="col-md-5">
            <div class="role-card candidate">
                <div class="role-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <h3>Je suis Candidat</h3>
                <ul>
                    <li><i class="fas fa-check-circle"></i> Déposez vos CV en PDF</li>
                    <li><i class="fas fa-check-circle"></i> Suivez vos candidatures</li>
                    <li><i class="fas fa-check-circle"></i> Analyse IA de vos compétences</li>
                    <li><i class="fas fa-check-circle"></i> Gérez plusieurs versions de CV</li>
                </ul>
                <a href="candidate/register.php" class="btn btn-role btn-candidate">
                    <i class="fas fa-user-plus"></i> Créer un compte candidat
                </a>
            </div>
        </div>

        <!-- RECRUTEUR -->
        <div class="col-md-5">
            <div class="role-card recruiter">
                <div class="role-icon">
                    <i class="fas fa-building"></i>
                </div>
                <h3>Je suis Recruteur</h3>
                <ul>
                    <li><i class="fas fa-check-circle"></i> Publiez vos offres d'emploi</li>
                    <li><i class="fas fa-check-circle"></i> Consultez les CV analysés par l'IA</li>
                    <li><i class="fas fa-check-circle"></i> Candidats classés par pertinence</li>
                    <li><i class="fas fa-check-circle"></i> Gain de temps considérable</li>
                </ul>
                <a href="recruiter/register_recruiter.php" class="btn btn-role btn-recruiter">
                    <i class="fas fa-building"></i> Créer un compte recruteur
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ============================
     SECTION IA
============================ -->
<section class="container">
    <div class="ia-section">
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-3">
                <i class="fas fa-brain text-warning"></i> 
                Le pouvoir de l'IA
            </h2>
            <p class="lead">Notre intelligence artificielle analyse sémantiquement chaque CV pour trouver les meilleurs profils.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="ia-step">
                    <div class="ia-step-number">1</div>
                    <h5 class="fw-bold">Extraction de texte</h5>
                    <p class="small opacity-75">Le contenu du PDF est automatiquement extrait.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="ia-step">
                    <div class="ia-step-number">2</div>
                    <h5 class="fw-bold">Analyse sémantique</h5>
                    <p class="small opacity-75">Le modèle spaCy comprend le sens du texte.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="ia-step">
                    <div class="ia-step-number">3</div>
                    <h5 class="fw-bold">Score de matching</h5>
                    <p class="small opacity-75">Un score de similarité est calculé pour chaque CV.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================
     FOOTER
============================ -->
<footer>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h5 class="text-white fw-bold mb-3">
                    <i class="fas fa-robot"></i> CVMatch IA
                </h5>
                <p class="small">
                    Plateforme intelligente de mise en relation entre candidats et recruteurs, 
                    propulsée par l'intelligence artificielle.
                </p>
            </div>
            <div class="col-md-3">
                <h6 class="text-white fw-bold mb-3">Navigation</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="candidate/register.php">Inscription Candidat</a></li>
                    <li class="mb-2"><a href="recruiter/register.php">Inscription Recruteur</a></li>
                    <li class="mb-2"><a href="candidate/login.php">Connexion Candidat</a></li>
                    <li class="mb-2"><a href="recruiter/login.php">Connexion Recruteur</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h6 class="text-white fw-bold mb-3">Technologies</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><i class="fab fa-php"></i> PHP & MySQL</li>
                    <li class="mb-2"><i class="fab fa-python"></i> Python (Flask)</li>
                    <li class="mb-2"><i class="fas fa-brain"></i> spaCy (NLP)</li>
                    <li class="mb-2"><i class="fas fa-file-pdf"></i> pdfplumber</li>
                </ul>
            </div>
        </div>
        <hr class="border-secondary my-4">
        <div class="text-center small">
            © 2026 CVMatch IA - Projet de démonstration | Développé par Kossia Rosine AMOSSI
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>