-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307
-- Generation Time: May 03, 2025 at 01:55 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `message`, `image_path`, `created_at`) VALUES
(4, 'Kalooppia', 'kukukukukkukukukukukukukukukukukkukukukukukku', NULL, '2025-05-03 06:52:26'),
(5, 'yutygquhijsj', 'hfanhw3jq', NULL, '2025-05-03 06:52:36'),
(6, 'BFHANBVNZ MV AW', 'MACKKNQKVNESVCAZ', NULL, '2025-05-03 06:52:42'),
(7, 'hello', 'ojveodsxjkfeds', '080e6ecd44f52483.png', '2025-05-03 10:04:22');

-- --------------------------------------------------------

--
-- Table structure for table `commissions`
--

CREATE TABLE `commissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `category` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `commissions`
--

INSERT INTO `commissions` (`id`, `user_id`, `category`, `description`, `created_at`) VALUES
(1, 7, 'MEeeeeeeeeeh', 'Hello', '2025-05-03 11:22:49'),
(2, 7, 'Development', 'Hellooooo', '2025-05-03 11:24:02'),
(3, 7, 'Writing', 'guvsjvvkwjekvdsz', '2025-05-03 11:26:28');

-- --------------------------------------------------------

--
-- Table structure for table `freelancer_photos`
--

CREATE TABLE `freelancer_photos` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `freelancer_photos`
--

INSERT INTO `freelancer_photos` (`id`, `user_id`, `file_path`, `uploaded_at`) VALUES
(1, 4, 'uploadsfl/ph_6815e112bced97.62602516.png', '2025-05-03 09:25:38'),
(2, 4, 'uploadsfl/ph_6815e480f01024.85457589.png', '2025-05-03 09:40:16'),
(3, 4, 'uploadsfl/ph_6815e48882e5a3.03057369.png', '2025-05-03 09:40:24'),
(4, 4, 'uploadsfl/ph_6815e48f645f60.50998311.png', '2025-05-03 09:40:31'),
(5, 4, 'uploadsfl/ph_6815e618ce7f50.76119489.png', '2025-05-03 09:47:04'),
(6, 4, 'uploadsfl/ph_6815e64db87e97.20544955.png', '2025-05-03 09:47:57');

-- --------------------------------------------------------

--
-- Table structure for table `freelancer_portfolio`
--

CREATE TABLE `freelancer_portfolio` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `freelancer_profiles`
--

CREATE TABLE `freelancer_profiles` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `freelancer_profiles`
--

INSERT INTO `freelancer_profiles` (`id`, `user_id`, `profile_picture`, `bio`, `skills`, `location`, `created_at`) VALUES
(1, 1, 'uploads/1.png', 'I am Inevitable', 'Mag drama', 'iloilo', '2025-05-02 08:16:26'),
(2, 3, 'uploads/1.png', 'I like Hatdog rawr', 'Mag drama, Mag relapse', 'Japan', '2025-05-02 09:27:41'),
(3, 4, 'uploads/2.png', 'I am the greatest', 'Swimming', 'Iloilo', '2025-05-03 05:46:52'),
(4, 6, 'uploads/4.png', 'hysjdkjs', 'bjdfdvsnckafmvsx', 'sbejwdkffsbnj', '2025-05-03 06:46:31'),
(5, 8, 'uploads/RobloxScreenShot20240823_202842766.png', 'KReg', 'Roblox player', 'Japan', '2025-05-03 10:29:58');

-- --------------------------------------------------------

--
-- Table structure for table `public_thread`
--

CREATE TABLE `public_thread` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `posted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `id` int(10) UNSIGNED NOT NULL,
  `freelancer_id` int(10) UNSIGNED NOT NULL,
  `commissioner_id` int(10) UNSIGNED NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('freelancer','commissioner','admin') NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `role`, `password`, `created_at`) VALUES
(1, 'dedu', 'dedu@gmail.com', 'freelancer', '$2y$10$0lgh92kDoszx24kHI5aUweLXwLZFdjGlP8o2rXS6wkHt8wx2pMfR.', '2025-05-02 08:07:19'),
(2, 'Ngina mo mercs', 'ecashalk@gmail.com', 'commissioner', '$2y$10$Cq9FC4PTJAl393LwRgDcpe4vLYIDz9pungGyfIc4aqTSe/jZVp/ye', '2025-05-02 09:25:28'),
(3, 'Ambot', 'ambot@gmail.com', 'freelancer', '$2y$10$WWzw61OGyGcNbC/FCLTv/.nyR5TyIPJeBSG05PNztG3QPoWqo0X8G', '2025-05-02 09:26:32'),
(4, 'jj', 'jj@gmail.com', 'freelancer', '$2y$10$aTtlJqNFnY9kGwbqmEnceesGhXguPUpxefZz99..rFZjHKGm4jLOu', '2025-05-03 05:45:10'),
(5, 'admin', 'admin@gmail.com', 'admin', '$2y$10$v1lR4l7924KBOcU7W6DxPeV/MPgyB.uDuFmmWlhKIERM9YLYewzL2', '2025-05-03 05:57:16'),
(6, 'aa', 'aa@gmail.com', 'freelancer', '$2y$10$pd/39A2iZ6sZxYLE72jlyOVhZI6exX9GWxn5GtQ3jeQCn2Rh8Nx9y', '2025-05-03 06:45:53'),
(7, 'kk', 'kk@gmail', 'commissioner', '$2y$10$Aef9ZquGzBBd4RtrzHGdJueujwEthBSzng77VL2GOI8R.05RHcP4u', '2025-05-03 10:11:24'),
(8, 'EZ', 'EZGITGUD@gmail.com', 'freelancer', '$2y$10$TowNIQ1GEMGP5gbepdR7mOh5wfWvUQAONzeW.vmc2UYMb1RtS4ks2', '2025-05-03 10:28:25');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `commissions`
--
ALTER TABLE `commissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `freelancer_photos`
--
ALTER TABLE `freelancer_photos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `freelancer_portfolio`
--
ALTER TABLE `freelancer_portfolio`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- Indexes for table `freelancer_profiles`
--
ALTER TABLE `freelancer_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_id_profiles` (`user_id`);

--
-- Indexes for table `public_thread`
--
ALTER TABLE `public_thread`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `freelancer_id` (`freelancer_id`),
  ADD KEY `commissioner_id` (`commissioner_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `commissions`
--
ALTER TABLE `commissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `freelancer_photos`
--
ALTER TABLE `freelancer_photos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `freelancer_portfolio`
--
ALTER TABLE `freelancer_portfolio`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `freelancer_profiles`
--
ALTER TABLE `freelancer_profiles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `public_thread`
--
ALTER TABLE `public_thread`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `commissions`
--
ALTER TABLE `commissions`
  ADD CONSTRAINT `fk_commissions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `freelancer_portfolio`
--
ALTER TABLE `freelancer_portfolio`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `freelancer_profiles`
--
ALTER TABLE `freelancer_profiles`
  ADD CONSTRAINT `fk_user_id_profiles` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `public_thread`
--
ALTER TABLE `public_thread`
  ADD CONSTRAINT `public_thread_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`freelancer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`commissioner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
