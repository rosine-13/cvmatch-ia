-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 03 oct. 2026 à 18:00
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `cvmatchia_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `candidate_profiles`
--

CREATE TABLE `candidate_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `city` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `experience_years` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `candidate_profiles`
--

INSERT INTO `candidate_profiles` (`id`, `user_id`, `full_name`, `city`, `phone`, `bio`, `skills`, `experience_years`, `created_at`) VALUES
(1, 1, 'Amossi Rosine', 'Abidjan', NULL, NULL, NULL, 0, '2026-10-01 14:19:26'),
(2, 3, 'Amossi Armande', 'Korogho', NULL, NULL, NULL, 0, '2026-10-02 15:27:53');

-- --------------------------------------------------------

--
-- Structure de la table `cvs`
--

CREATE TABLE `cvs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `extracted_text` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'En attente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `cvs`
--

INSERT INTO `cvs` (`id`, `user_id`, `file_path`, `extracted_text`, `status`, `created_at`) VALUES
(2, 1, 'uploads/1790942400_AMOSSI_KOSSIA_ROSINE_CV_DATA_ANALYST.pdf', 'AMOSSI KOSSIA ROSINE\nData Analyst junior – Python, SQL et Power BI\nTéléphone : +225 01 00 48 31 41 | E-mail : rosineamossi@gmail.com | Localisation : Abidjan, Côte d\'Ivoire\nGitHub : github.com/rosine-13 | LinkedIn : linkedin.com/in/rosine-amossi | Portfolio : https://rosine-13.github.io/mon_portfolio/\nProfil Professionnel\nData Analyst junior, titulaire d’un BTS en Informatique (option Développeur d’Applications) et actuellement apprenante\nDéveloppeuse Data et Intelligence Artificielle chez Simplon Côte d’Ivoire. Je développe mes compétences en analyse, nettoyage et\nvisualisation de données avec Python, SQL, Pandas et Power BI. Mon parcours en développement d\'applications me permet\négalement de comprendre la conception logicielle et l’automatisation des processus. Rigoureuse et curieuse, je souhaite contribuer\nà des projets fondés sur l’exploitation et la valorisation des données.\nProjets Techniques Réalisés\n1. IvoireExplorerIA – Assistant touristique virtuel intelligent\n● Problème traité : Orientations et recommandations personnalisées de lieux touristiques en Côte d\'Ivoire.\n● Mon rôle : Conception globale de l\'architecture, intégration du modèle de langage (LLM) et traitement des flux de données.\n● Fonctionnalités : Traitement du langage naturel, recherche d\'informations en temps réel via API et archivage structuré des\nrequêtes utilisateurs.\n● Technologies : Python (FastAPI), LLM (DeepSeek), Google Sheets API, SerpAPI, Telegram Bot API.\n● Liens de démonstration: Voir la vidéo demo\n2. AlcoVision – Tableau de bord mondial de la consommation d\'alcool\n● Problème traité : Les décideurs de santé publique manquent d\'un outil visuel pour analyser les tendances mondiales de\nconsommation d\'alcool et identifier les zones à risque afin d\'orienter les politiques de prévention.\n● Mon rôle : Collecte et import des données OMS, nettoyage et transformation via Power Query, modélisation des données,\ncréation des KPI en DAX et conception du tableau de bord interactif.\n● Fonctionnalités : KPI dynamiques (consommation moyenne, évolution annuelle), courbe de tendance 2000–2022, Top 10 pays\nconsommateurs, carte mondiale interactive et classement par région géographique.\n● Technologies : Power BI Desktop, Power Query (M), DAX, CSV (OMS — 188 pays, 23 ans).\n● Liens de démonstration: Voir la demo\nExpérience Professionnelle\nStagiaire développeuse d’applications — Access Informatique 07 Juillet au 06 Octobre 2025\n● Participation au développement d’une application de gestion de microfinance.\n● Contribution à la conception des interfaces utilisateurs et à la gestion de la base de données (clients, comptes, agents et\ntransactions).\n● Technologies : WebDev, HFSQL.\n● Liens de démonstration: Voir la démo\nFormations\nApprenante Développeuse Data et Intelligence Artificielle — Simplon Côte d’Ivoire Février 2026 – En cours\nBTS Informatique (Option Développeur d’Applications) — Institut Supérieur Jean-Paul II (Groupe LOKO)\nDiplôme obtenu en 2024\nBaccalauréat Général (Série D) — Lycée Municipal de Zikisso Diplôme obtenu en 2022\nCompétences Techniques\nAnalyse de données : Python (Pandas, NumPy), SQL, Power BI, Excel, nettoyage et visualisation\nDéveloppement Web & Mobile : HTML5, CSS3, JavaScript, PHP, Flutter\nDéveloppement logiciel : WebDev, HFSQL\nBases de données : MySQL, HFSQL, SQLite\nAutomatisation & IA : Notions d\'intégration d\'API REST, LLM et workflows n8n\nOutils & Environnements : Git, GitHub, VS Code, XAMPP, Microsoft Office\nCertifications\n● Python 101 for Data Science — IBM SkillsBuild (2025)\nLien de vérification: https://drive.google.com/file/d/1ZuyrMEAKZByQhBU8GHfTbxB717KdFy3j/view?usp=sharing\n● Data Literacy — Coursera / Sorbonne Université / Alliance 4EU+\nLien de vérification: https://coursera.org/verify/D8U8Z2JGET2C\n● Data Science Math Skills — Coursera / Duke University (2025)\nLien de vérification: https://coursera.org/verify/7UT4O3OCJP2Y\nLangues & Divers\n● Français : Courant (langue maternelle)\n● Anglais : Intermédiaire (lecture et compréhension de documentations techniques)\n● Références : Disponibles sur demande.\n', 'Analysé', '2026-10-02 12:00:00'),
(3, 3, 'uploads/1790954930_CV AMOSSI ABENAN ARMANDE T.pdf', 'amossiabenanarmandetheodora@gmail.com\n', 'Analysé', '2026-10-02 15:28:50'),
(4, 3, 'uploads/1790961692_CV_Astronaute_Neurochirurgien.pdf', 'CURRICULUM VITAE\nDouble expertise unique : Exploration Interplanétaire & Neurochirurgie\nEXPÉRIENCE SPATIALE (8 ANS)\nAstronaute de Mission – Exploration Lunaire\nAstronaute &\nMissions de surface\nNeurochirurgien\n• Marche effective sur la surface lunaire pour la collecte de données\ngéologiques.\nINFOS PERSONNELLES • Déploiement d\'instruments de mesure sismique de haute précision.\nÂge : 54 ans Navigateur de Mission – Survol Martien\nMission orbitale Mars Flyby\nNé le : 12 Mai\nLieu : Londres, UK • Pilotage technique pour une trajectoire orbitale autour de Mars sans\ncontact de surface.\nFamille : 3 enfants\n• Observation atmosphérique et cartographie laser de la zone Valles\nMarineris.\nLANGUES\nEXPÉRIENCE MÉDICALE\nBaoulé (Natif)\nNeurochirurgien Senior (Spécialiste du Cerveau)\nAnglais (Bilingue)\nArabe (Avancé)\n• Expertise en micro-neurochirurgie et interventions cérébrales complexes.\nChinois (Professionnel) • Pionnier dans l\'étude des impacts de la radiation spatiale sur les tissus\ncérébraux.\n• Capacité à opérer en conditions environnementales dégradées.\nCOMPÉTENCES CLÉS\nÉDUCATION\n• Navigation Spatiale\n• Chirurgie du Cerveau\nDoctorat en Médecine (Neurochirurgie)\n• Survie en Microgravité Faculté de Londres\n• Gestion de Crise\nMaster en Astrophysique & Entraînement Astronaute\nAcadémie Spatiale\nVALEURS\nEngagement total envers la science, la précision chirurgicale et l\'exploration\ndes nouvelles frontières de l\'humanité.\n', 'Analysé', '2026-10-02 17:21:32');

-- --------------------------------------------------------

--
-- Structure de la table `jobs`
--

CREATE TABLE `jobs` (
  `id` int(11) NOT NULL,
  `recruiter_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `required_skills` text NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `contract_type` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `jobs`
--

INSERT INTO `jobs` (`id`, `recruiter_id`, `title`, `description`, `required_skills`, `location`, `contract_type`, `created_at`) VALUES
(1, 2, 'Développeur PHP Senior', 'Nous recherchons un développeur PHP expérimenté pour rejoindre notre équipe.', 'PHP, MySQL, JavaScript, Bootstrap, Python', 'Abidjan', 'CDI', '2026-10-02 12:10:33');

-- --------------------------------------------------------

--
-- Structure de la table `recruiter_profiles`
--

CREATE TABLE `recruiter_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `company_name` varchar(100) NOT NULL,
  `company_sector` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `recruiter_profiles`
--

INSERT INTO `recruiter_profiles` (`id`, `user_id`, `company_name`, `company_sector`, `phone`, `bio`, `website`, `created_at`) VALUES
(1, 2, 'TechCorp', 'Informatique', '+225 01 02 03 04 05', NULL, NULL, '2026-10-01 16:47:57');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('candidate','recruiter') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `nom`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Amossi', 'rosineamossi@gmail.com', '$2y$10$ghi4uH4Qu5wma4btZSRZQuCMy6SqzfbAa0kt7gxnTl/g9SZTRWoWy', 'candidate', '2026-10-01 14:19:26'),
(2, 'TechCorp', 'contact@techcorp.com', '$2y$10$YBpC3ilVGjthyeFTGevjd.YrNKb7tkmoBfKUsp5M9WPaY0QIHj8ny', 'recruiter', '2026-10-01 16:47:57'),
(3, 'Amossi', 'armandeamossi@gmail.com', '$2y$10$rjlLDbdZkmdvUomhrMNONup6gP/rpl6KDCsRgxMi/bY41C.lDIfwS', 'candidate', '2026-10-02 15:27:53');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `candidate_profiles`
--
ALTER TABLE `candidate_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Index pour la table `cvs`
--
ALTER TABLE `cvs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recruiter_id` (`recruiter_id`);

--
-- Index pour la table `recruiter_profiles`
--
ALTER TABLE `recruiter_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `candidate_profiles`
--
ALTER TABLE `candidate_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `cvs`
--
ALTER TABLE `cvs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `recruiter_profiles`
--
ALTER TABLE `recruiter_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `candidate_profiles`
--
ALTER TABLE `candidate_profiles`
  ADD CONSTRAINT `candidate_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `cvs`
--
ALTER TABLE `cvs`
  ADD CONSTRAINT `cvs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `jobs`
--
ALTER TABLE `jobs`
  ADD CONSTRAINT `jobs_ibfk_1` FOREIGN KEY (`recruiter_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `recruiter_profiles`
--
ALTER TABLE `recruiter_profiles`
  ADD CONSTRAINT `recruiter_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
