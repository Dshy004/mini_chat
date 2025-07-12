-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql-eslash.alwaysdata.net
-- Generation Time: Jan 07, 2025 at 10:10 PM
-- Server version: 10.11.9-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `minichat`
--

-- --------------------------------------------------------

--
-- Table structure for table `amis`
--

CREATE TABLE `amis` (
  `id` int(10) UNSIGNED NOT NULL,
  `idu_one` int(10) UNSIGNED NOT NULL,
  `idu_two` int(10) UNSIGNED NOT NULL,
  `valide` enum('oui','non') NOT NULL DEFAULT 'non'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `amis`
--

INSERT INTO `amis` (`id`, `idu_one`, `idu_two`, `valide`) VALUES
(1, 2, 3, 'oui'),
(2, 3, 4, 'oui'),
(3, 3, 5, 'non');

-- --------------------------------------------------------

--
-- Table structure for table `chat`
--

CREATE TABLE `chat` (
  `id` int(10) UNSIGNED NOT NULL,
  `idu` int(10) UNSIGNED NOT NULL,
  `id_receiver` int(10) UNSIGNED NOT NULL,
  `pseudo` varchar(255) NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `docs` varchar(255) DEFAULT NULL,
  `vue` enum('oui','non') NOT NULL DEFAULT 'non',
  `delete` enum('oui','non') NOT NULL DEFAULT 'non',
  `dateSent` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `historique`
--

CREATE TABLE `historique` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_users` int(10) UNSIGNED NOT NULL,
  `action` varchar(255) NOT NULL,
  `dateAction` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `historique`
--

INSERT INTO `historique` (`id`, `id_users`, `action`, `dateAction`) VALUES
(1, 3, 'Déconnexion', '2025-01-01 19:08:57'),
(2, 1, 'Connexion', '2025-01-06 21:48:10'),
(3, 1, 'Déconnexion', '2025-01-06 21:49:01');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `pseudo` varchar(6) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `type` enum('users','admin','inactif') NOT NULL DEFAULT 'users',
  `online` enum('non','oui') NOT NULL DEFAULT 'non',
  `discute` varchar(255) DEFAULT NULL,
  `code_valid` int(6) DEFAULT NULL,
  `dateCreate` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `pseudo`, `email`, `password`, `type`, `online`, `discute`, `code_valid`, `dateCreate`) VALUES
(1, 'slash', 'user', '$2y$10$ykRPA0D9K01UV5Jxzjl.tOlAP8KB6E7KWI4gjICr8T7JD.AdApRhS', 'admin', 'non', NULL, NULL, '2024-12-14 17:13:05'),
(2, 'zozo', 'zozo@gmail.com', '$2y$10$Ymb7M9j/Eq6bn2wBGrCATOzpieiQg52uvbJG0YGjMCohb4wDlb.MK', 'users', 'non', NULL, NULL, '2024-12-14 19:25:54'),
(3, 'serge', 'sergeharold1@gmail.com', '$2y$10$2wa7P7VgRQDuv/uSMeZhfuExM772oSj6HzWxaXesOlNU2BRz49g7i', 'users', 'non', NULL, NULL, '2024-12-14 19:25:54'),
(4, 'ismath', 'ismathmalehossou@gmail.com', '$2y$10$IwcOjQfCXWSzBc1kRh7cTu3hZM6zC0FvVWQUSxxQ8kXMrTlGapwBC', 'users', 'non', NULL, NULL, '2024-12-15 14:23:48'),
(5, 'Titila', 'Titiland07@gmail.com', '$2y$10$8EiJ6/KSjkxWpSxjb9Q85.eg/HQHx/MJIXr9e0MLF9Y462yxnmdQy', 'users', 'non', NULL, NULL, '2024-12-15 16:32:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `amis`
--
ALTER TABLE `amis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_one` (`idu_one`),
  ADD KEY `fk_two` (`idu_two`);

--
-- Indexes for table `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_sender` (`idu`),
  ADD KEY `fk_receiver` (`id_receiver`);

--
-- Indexes for table `historique`
--
ALTER TABLE `historique`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_users` (`id_users`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `amis`
--
ALTER TABLE `amis`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `chat`
--
ALTER TABLE `chat`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `historique`
--
ALTER TABLE `historique`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `amis`
--
ALTER TABLE `amis`
  ADD CONSTRAINT `fk_one` FOREIGN KEY (`idu_one`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_two` FOREIGN KEY (`idu_two`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `chat`
--
ALTER TABLE `chat`
  ADD CONSTRAINT `fk_receiver` FOREIGN KEY (`id_receiver`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sender` FOREIGN KEY (`idu`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `historique`
--
ALTER TABLE `historique`
  ADD CONSTRAINT `fk_users` FOREIGN KEY (`id_users`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
