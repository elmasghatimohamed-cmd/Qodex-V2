-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 29, 2025 at 09:01 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quiz_platform`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `nom`, `description`, `created_by`, `created_at`, `updated_at`) VALUES
(2, 'HTML/CSS', 'Nostrud at perspicia', 1, '2025-12-25 20:04:24', '2025-12-25 20:04:24'),
(3, 'SQL', 'Fugiat qui molestia', 1, '2025-12-25 20:21:59', '2025-12-29 09:00:46'),
(4, 'HTML/CSS', 'Anim veritatis anim', 2, '2025-12-25 21:23:02', '2025-12-25 21:23:13'),
(5, 'Javascript', 'Dolorem laboris accu', 3, '2025-12-26 23:41:58', '2025-12-29 09:00:41'),
(6, 'Java', 'Sed est illum paria', 1, '2025-12-28 21:19:22', '2025-12-29 09:00:35');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int NOT NULL,
  `quiz_id` int NOT NULL,
  `question` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `option1` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `option2` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `option3` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `option4` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `correct_option` tinyint NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `quiz_id`, `question`, `option1`, `option2`, `option3`, `option4`, `correct_option`, `created_at`) VALUES
(3, 2, 'Reiciendis repellend', 'Dignissimos ex est e', 'Commodo occaecat ear', 'Consequatur Atque v', 'Dolorem officia fugi', 1, '2025-12-25 20:22:30'),
(4, 2, 'Officia unde fugit', 'Officiis ex veniam', 'Ut necessitatibus ip', 'Dicta voluptatum sae', 'Dolor reiciendis ut', 3, '2025-12-25 20:22:30'),
(5, 2, 'Odio optio non et l', 'Vitae voluptate ut m', 'Nesciunt ducimus v', 'Sed molestiae atque', 'Voluptatem Sunt quo', 1, '2025-12-25 20:22:30'),
(6, 2, 'Numquam sed et exped', 'Atque esse labore a', 'Est consequatur Iur', 'Nulla quam modi quia', 'Excepteur qui est c', 1, '2025-12-25 20:22:30'),
(7, 2, 'Minus dolore cum eni', 'Sed autem non volupt', 'Excepturi qui volupt', 'Impedit explicabo', 'Similique id impedi', 4, '2025-12-25 20:22:30'),
(8, 2, 'Deserunt et in volup', 'Necessitatibus qui n', 'Id blanditiis et sim', 'Sapiente dolore quas', 'Nostrum est et sunt', 4, '2025-12-25 20:22:30'),
(9, 2, 'Eu itaque pariatur', 'Culpa cillum ratione', 'Ullamco tempora dolo', 'Odit reprehenderit e', 'Voluptate laborum as', 4, '2025-12-25 20:22:30'),
(10, 2, 'Omnis quia odit quo', 'Modi ea sit quas dig', 'Sit quo assumenda ex', 'Aliquam impedit sun', 'Voluptates dolore re', 3, '2025-12-25 20:22:30'),
(11, 2, 'At consequat Dolore', 'Dolores sit quaerat', 'Lorem eveniet ipsam', 'Inventore occaecat n', 'Quisquam fugit cons', 4, '2025-12-25 20:22:30'),
(13, 3, 'Non non eiusmod veni', 'Sunt nostrud laborum', 'Rerum voluptatem Mo', 'Autem facilis accusa', 'Molestiae quia ex au', 1, '2025-12-25 21:23:37'),
(14, 3, 'Eaque rerum numquam', 'Voluptas laboriosam', 'Sit reiciendis nemo', 'Laboris necessitatib', 'Fugiat ut facilis a', 2, '2025-12-25 21:23:37'),
(15, 2, 'Laudantium molestia', 'Ullamco quisquam lau', 'Libero elit sapient', 'Quia iure aut repreh', 'Voluptatem suscipit', 1, '2025-12-25 22:55:13'),
(16, 4, 'Quaerat cumque ex qu', 'Aut quis perferendis', 'Adipisicing nesciunt', 'Rerum repudiandae hi', 'Qui rem accusamus es', 3, '2025-12-26 23:42:36'),
(17, 4, 'Dolorum officia vero', 'Mollit pariatur Qua', 'Illo consequatur An', 'Culpa et ipsam minim', 'Expedita rem nulla i', 1, '2025-12-26 23:42:36'),
(18, 2, 'Reprehenderit minim', 'Rerum rerum Nam cons', 'Autem temporibus min', 'Iste beatae consequa', 'Culpa incidunt fuga', 3, '2025-12-28 21:36:04');

-- --------------------------------------------------------

--
-- Table structure for table `quiz`
--

CREATE TABLE `quiz` (
  `id` int NOT NULL,
  `titre` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `categorie_id` int NOT NULL,
  `enseignant_id` int NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quiz`
--

INSERT INTO `quiz` (`id`, `titre`, `description`, `categorie_id`, `enseignant_id`, `is_active`, `created_at`, `updated_at`) VALUES
(2, 'Porro ab autem molli', 'Sunt ut earum odit q', 3, 1, 1, '2025-12-25 20:22:30', '2025-12-29 08:31:06'),
(3, 'Non laboris ut eos s', 'Vel non rerum placea', 4, 2, 1, '2025-12-25 21:23:37', '2025-12-25 21:23:37'),
(4, 'Vitae velit id est e', 'Suscipit aperiam rer', 5, 3, 1, '2025-12-26 23:42:36', '2025-12-26 23:42:36');

-- --------------------------------------------------------

--
-- Table structure for table `results`
--

CREATE TABLE `results` (
  `id` int NOT NULL,
  `quiz_id` int NOT NULL,
  `etudiant_id` int NOT NULL,
  `score` int NOT NULL,
  `total_questions` int NOT NULL,
  `completed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `results`
--

INSERT INTO `results` (`id`, `quiz_id`, `etudiant_id`, `score`, `total_questions`, `completed_at`, `created_at`) VALUES
(1, 3, 1, 23, 3, '2025-12-25 21:56:15', '2025-12-25 21:57:18');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('enseignant','etudiant') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nom`, `email`, `password_hash`, `role`, `created_at`, `deleted_at`) VALUES
(1, 'Sint cumque ea corpo', 'wukabe@mailinator.com', '$2y$10$.H3xaFVs/SCpXCl3PS8B..24BFwC6hfDEM46J.W.msWtNpgLt8Kn2', 'enseignant', '2025-12-25 20:03:15', NULL),
(2, 'Assumenda laboriosam', 'cijevog@mailinator.com', '$2y$10$Jy4hDlKEc2yK8vST1Sidw.rMvlp66WiLwMNIrcN4oZbilMqI1J3DC', 'etudiant', '2025-12-25 21:22:26', NULL),
(3, 'Dolores sit aute fug', 'tucevekabi@mailinator.com', '$2y$10$aBl3TEHjxAPhpBOL6ALu9O/aEaHg9s/ffUpCDygIi74OoJk91p6x6', 'enseignant', '2025-12-26 23:41:02', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_created_by` (`created_by`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_quiz` (`quiz_id`);

--
-- Indexes for table `quiz`
--
ALTER TABLE `quiz`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_categorie` (`categorie_id`),
  ADD KEY `idx_enseignant` (`enseignant_id`);

--
-- Indexes for table `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_quiz` (`quiz_id`),
  ADD KEY `idx_etudiant` (`etudiant_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quiz`
--
ALTER TABLE `quiz`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `results`
--
ALTER TABLE `results`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quiz` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz`
--
ALTER TABLE `quiz`
  ADD CONSTRAINT `quiz_ibfk_1` FOREIGN KEY (`categorie_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quiz_ibfk_2` FOREIGN KEY (`enseignant_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `results`
--
ALTER TABLE `results`
  ADD CONSTRAINT `results_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quiz` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `results_ibfk_2` FOREIGN KEY (`etudiant_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
