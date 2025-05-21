-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307
-- Generation Time: May 20, 2025 at 11:24 PM
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
(10, 'Hello Admin Announcement', 'This is an announcement', '97dd108f87cc3635.jpg', '2025-05-18 11:24:42'),
(12, 'hE;;;', 'OFAJKVDJKN', NULL, '2025-05-20 03:22:49');

-- --------------------------------------------------------

--
-- Table structure for table `commissions`
--

CREATE TABLE `commissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `taken_by` int(11) DEFAULT NULL,
  `category` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` enum('Pending','In Progress','Under Review','Awaiting Final','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
  `completed_file` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `payment_amount` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `watermark` tinyint(1) DEFAULT 0,
  `subcategory` varchar(100) DEFAULT NULL,
  `temp_file` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `commissions`
--

INSERT INTO `commissions` (`id`, `user_id`, `taken_by`, `category`, `description`, `status`, `completed_file`, `created_at`, `payment_amount`, `payment_method`, `watermark`, `subcategory`, `temp_file`) VALUES
(1, 7, NULL, 'MEeeeeeeeeeh', 'Hello', 'Pending', NULL, '2025-05-03 11:22:49', NULL, NULL, 0, NULL, NULL),
(2, 7, NULL, 'Development', 'Hellooooo', 'Pending', NULL, '2025-05-03 11:24:02', NULL, NULL, 0, NULL, NULL),
(3, 7, NULL, 'Writing', 'guvsjvvkwjekvdsz', 'Pending', NULL, '2025-05-03 11:26:28', NULL, NULL, 0, NULL, NULL),
(4, 7, 22, 'huwuhfkjkFJFL', 'IQ3URI0VNDKAJXFM', 'In Progress', NULL, '2025-05-05 06:55:47', NULL, NULL, 0, NULL, NULL),
(5, 12, 6, 'Merjggefkfd', 'fguhijopdifghjfsenkwdfdjnc', '', '1747536838_db.sql', '2025-05-05 08:32:04', NULL, NULL, 0, NULL, NULL),
(6, 15, 6, 'Essay', 'Can you give me an essay for our computer programming', '', '1747536423_IMG_1522.JPG', '2025-05-06 16:45:56', NULL, NULL, 0, NULL, NULL),
(7, 15, 4, 'Artwork', 'Can I have an artwork like mona lisa please', 'In Progress', NULL, '2025-05-08 16:25:33', NULL, NULL, 0, NULL, NULL),
(8, 12, 6, 'Artwork', 'Make m an artwork', 'Completed', '1747537358_IMG_1522.JPG', '2025-05-18 04:01:35', NULL, NULL, 0, NULL, NULL),
(9, 15, 32, 'Artwork', 'Give me an artwork', 'In Progress', NULL, '2025-05-18 04:42:39', NULL, NULL, 0, NULL, NULL),
(10, 25, 27, 'Artwork', 'ff needs help in art', 'Under Review', NULL, '2025-05-18 05:00:08', NULL, NULL, 0, NULL, '1747775058_1747537358_IMG_1522 (3).jpg'),
(11, 26, 27, 'Artwork', 'Hi I would like to have a painting like the mona lisa', 'Completed', '1747567773_1747537358_IMG_1522 (2).jpg', '2025-05-18 12:26:21', NULL, NULL, 0, NULL, NULL),
(12, 28, 27, 'Design', 'I like someone who can create wild and free design for our company, we\'ll pay 1 billion dollary doos for the job... huhu hu', 'Completed', '1747577844_1747537358_IMG_1522.jpg', '2025-05-18 15:16:14', NULL, NULL, 0, NULL, NULL),
(13, 26, 27, 'Design', 'please design this db', 'Completed', '1747687840_1747537358_IMG_1522 (1).jpg', '2025-05-18 19:34:51', NULL, NULL, 0, NULL, NULL),
(14, 26, 27, 'Development', 'develop this code diluuc', 'Completed', '1747594431_IMG_1642.jpg', '2025-05-18 19:52:35', NULL, NULL, 0, NULL, NULL),
(15, 26, 32, 'Development', 'Please debug this', 'Completed', '1747769947_1747537358_IMG_1522 (3).jpg', '2025-05-20 06:32:35', 2.00, 'Other', 1, NULL, NULL),
(16, 26, NULL, 'Design', 'udxctfyghijhgchjfxhcgjkghfhjkl', 'Pending', NULL, '2025-05-20 20:38:19', 600.00, 'PayPal', 1, NULL, NULL),
(17, 26, 27, 'Artwork', 'Art', 'Awaiting Final', NULL, '2025-05-20 22:04:55', 1000.00, 'PayPal', 1, NULL, '1747775135_1747577844_1747537358_IMG_1522.jpg'),
(18, 26, 27, 'TDCreation', 'secret', 'In Progress', NULL, '2025-05-20 22:06:50', 1000.00, 'PayPal', 1, NULL, NULL);

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
(6, 4, 'uploadsfl/ph_6815e64db87e97.20544955.png', '2025-05-03 09:47:57'),
(7, 4, 'uploadsfl/ph_681851ff454a72.79488579.png', '2025-05-05 05:51:59'),
(8, 4, 'uploadsfl/ph_68185200a0f467.27933648.png', '2025-05-05 05:52:00'),
(9, 4, 'uploadsfl/ph_68185205e14eb1.79088025.png', '2025-05-05 05:52:05'),
(10, 4, 'uploadsfl/ph_681852066077d9.42114902.png', '2025-05-05 05:52:06'),
(11, 4, 'uploadsfl/ph_68185206b28a05.81616795.png', '2025-05-05 05:52:06'),
(12, 4, 'uploadsfl/ph_681852075baa08.19912324.png', '2025-05-05 05:52:07'),
(13, 4, 'uploadsfl/ph_68185207cbb4a2.89161015.png', '2025-05-05 05:52:08'),
(14, 4, 'uploadsfl/ph_6818520857e014.82540830.png', '2025-05-05 05:52:08'),
(15, 4, 'uploadsfl/ph_68185280f0b1c4.37772235.png', '2025-05-05 05:54:08'),
(16, 11, 'uploadsfl/ph_681868e97b9930.45860045.png', '2025-05-05 07:29:45'),
(17, 4, 'uploadsfl/ph_68186cae892cc6.44199736.png', '2025-05-05 07:45:50'),
(18, 19, 'uploadsfl/ph_681a3c7c787442.86810474.png', '2025-05-06 16:44:44'),
(19, 19, 'uploadsfl/ph_681a3c82266b57.57802453.png', '2025-05-06 16:44:50'),
(20, 19, 'uploadsfl/ph_681a3efe1a3d46.67303239.jpg', '2025-05-06 16:55:26'),
(21, 6, 'uploadsfl/ph_681cd64f58d4f3.49319635.jpg', '2025-05-08 16:05:35'),
(22, 24, 'uploadsfl/ph_682956d4685925.82790447.jpg', '2025-05-18 03:41:08'),
(23, 27, 'uploadsfl/ph_6829c462ab64d9.92278886.jpg', '2025-05-18 11:28:34'),
(24, 27, 'uploadsfl/ph_6829ddc61d5fe4.52261803.jpg', '2025-05-18 13:16:54'),
(25, 27, 'uploadsfl/ph_6829de0a519688.60523387.jpg', '2025-05-18 13:18:02'),
(26, 27, 'uploadsfl/ph_6829dfbdd6ad42.99075325.jpg', '2025-05-18 13:25:17'),
(27, 27, 'uploadsfl/ph_6829e053d0f088.18467347.jpg', '2025-05-18 13:27:47'),
(28, 27, 'uploadsfl/ph_6829e09fb93309.28808444.jpg', '2025-05-18 13:29:03'),
(29, 27, 'uploadsfl/ph_6829e151a931a1.95564842.jpg', '2025-05-18 13:32:01'),
(30, 32, 'uploadsfl/ph_682cd939742f07.69303523.jpg', '2025-05-20 19:34:17');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `fname` varchar(100) NOT NULL,
  `lname` varchar(100) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_number` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `freelancer_profiles`
--

INSERT INTO `freelancer_profiles` (`id`, `user_id`, `profile_picture`, `bio`, `skills`, `location`, `created_at`, `fname`, `lname`, `payment_method`, `payment_number`) VALUES
(1, 1, 'uploads/1.png', 'I am Inevitable', 'Mag drama', 'iloilo', '2025-05-02 08:16:26', '', '', NULL, NULL),
(2, 3, 'uploads/1.png', 'I like Hatdog rawr', 'Mag drama, Mag relapse', 'Japan', '2025-05-02 09:27:41', '', '', NULL, NULL),
(3, 4, 'uploads/2.png', 'I am the greatest', 'Swimming', 'Iloilo', '2025-05-03 05:46:52', '', '', NULL, NULL),
(4, 6, 'uploads/4.png', 'hysjdkjs', 'bjdfdvsnckafmvsx', 'sbejwdkffsbnj', '2025-05-03 06:46:31', '', '', NULL, NULL),
(5, 8, 'uploads/RobloxScreenShot20240823_202842766.png', 'KReg', 'Roblox player', 'Japan', '2025-05-03 10:29:58', '', '', NULL, NULL),
(6, 9, 'uploads/RobloxScreenShot20240823_202845183.png', 'j hndgejwfkswBKG JDLFSw[zKNB VML;]', 'HBDGFSIOP[oaksdplswqkf nb', 'hufWIQQIFHBUVEJq', '2025-05-05 06:02:05', '', '', NULL, NULL),
(7, 11, 'uploads/RobloxScreenShot20240823_204345302.png', 'hijeqwkqdjfnsdkwqldfksdnb', 'ghjkhgcvhjkiopij', 'Iloilo', '2025-05-05 07:28:01', '', '', NULL, NULL),
(8, 14, 'uploads/16.png', 'nonchalant', 'mag lu2', 'sta.rita', '2025-05-05 07:59:17', '', '', NULL, NULL),
(9, 16, 'uploads/4.png', 'qhifh dfhfsi jsjsd odu ', 'jejsdwkwujwjww', 'hdhbwugwywiid', '2025-05-05 08:10:10', '', '', NULL, NULL),
(11, 19, 'uploads/1.png', '', '', '', '2025-05-06 16:44:32', 'test', '', NULL, NULL),
(12, 21, NULL, '', '', '', '2025-05-08 14:40:39', '', '', NULL, NULL),
(14, 24, 'uploads/1747537358_IMG_1522 (2).jpg', '', '', '', '2025-05-18 03:40:33', 'qq', '', NULL, NULL),
(15, 27, 'uploads/IMG_1522.JPG', 'heloo', 'drawing', 'Iloilo', '2025-05-18 11:27:54', 'fltest', 'meh', 'Cash', '09102899182'),
(16, 29, 'uploads/IMG_1653.jpg', 'gyefwdjQKDFSVBWJDIJF', 'Swimming', 'iLOILO', '2025-05-18 19:00:31', 'Kim', 'kim', NULL, NULL),
(17, 30, 'uploads/IMG_1654.jpg', 'jj\r\n', 'jj', 'jj', '2025-05-19 22:01:31', 'jj', 'jj', NULL, NULL),
(18, 31, 'uploads/IMG_1654.jpg', 'cc', 'cc', 'cc', '2025-05-20 03:51:45', 'cc', 'cc', 'GCash', '09102899182'),
(19, 32, 'uploads/1747577844_1747537358_IMG_1522.jpg', 'Gracer', 'I am Gracer', 'Gracerland', '2025-05-20 19:33:07', 'Gracer', 'Gracer', 'Cash', 'gracer');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `is_read`, `created_at`) VALUES
(1, 15, 'Your commission #6 has been released by aa.', 1, '2025-05-08 16:19:43'),
(2, 12, 'Your commission #5 has been released by aa.', 1, '2025-05-08 16:19:48'),
(3, 7, 'Your commission #4 has been released by aa.', 0, '2025-05-08 16:19:53'),
(4, 15, 'Your commission #6 has been taken by aa.', 1, '2025-05-08 16:20:13'),
(5, 7, 'Your commission #4 has been taken by aa.', 0, '2025-05-08 16:20:15'),
(6, 15, 'Your commission #6 has been released by aa.', 1, '2025-05-08 16:20:21'),
(7, 7, 'Your commission #4 has been released by aa.', 0, '2025-05-08 16:21:48'),
(8, 15, 'Your commission #7 has been taken by aa.', 1, '2025-05-08 16:26:00'),
(9, 15, 'Your commission #6 has been taken by aa.', 1, '2025-05-08 16:26:02'),
(10, 15, 'Your commission #7 has been released by aa.', 1, '2025-05-08 17:36:23'),
(11, 15, 'Your commission #7 has been taken by jj.', 1, '2025-05-14 21:47:07'),
(12, 7, 'Your commission #4 has been taken by gago.', 0, '2025-05-14 21:58:16'),
(13, 12, 'Your commission #5 has been taken by aa.', 1, '2025-05-17 04:01:57'),
(14, 12, 'Your commission #8 has been taken by aa.', 1, '2025-05-18 04:02:08'),
(15, 12, '✅ Your commission has been marked as completed!', 1, '2025-05-18 04:02:38'),
(16, 26, 'Your commission #11 has been taken by fltest.', 1, '2025-05-18 12:29:15'),
(17, 26, '✅ Your commission has been marked as completed!', 1, '2025-05-18 12:29:33'),
(18, 28, 'Your commission #12 has been taken by fltest.', 1, '2025-05-18 15:17:03'),
(19, 28, '✅ Your commission has been marked as completed!', 1, '2025-05-18 15:17:24'),
(20, 26, 'Your commission #13 has been taken by fltest.', 1, '2025-05-18 19:36:03'),
(21, 25, 'Your commission #10 has been taken by fltest.', 0, '2025-05-18 19:51:02'),
(22, 25, 'Your commission #10 has been released by fltest.', 0, '2025-05-18 19:51:07'),
(23, 25, 'Your commission #10 has been taken by fltest.', 0, '2025-05-18 19:51:17'),
(24, 26, 'Your commission #14 has been taken by fltest.', 1, '2025-05-18 19:53:21'),
(25, 26, '✅ Your commission has been marked as completed!', 1, '2025-05-18 19:53:51'),
(26, 26, '✅ Your commission has been marked as completed!', 1, '2025-05-19 21:50:41'),
(27, 26, 'Your commission #15 has been taken by xx.', 1, '2025-05-20 20:33:53'),
(28, 15, 'Your commission #9 has been taken by xx.', 0, '2025-05-20 20:33:55'),
(29, 26, '✅ Your commission has been marked as completed!', 1, '2025-05-20 20:39:07'),
(30, 26, 'Your commission #17 has been taken by fltest.', 1, '2025-05-20 22:05:21'),
(31, 26, 'Your commission #18 has been taken by fltest.', 1, '2025-05-20 22:07:18');

-- --------------------------------------------------------

--
-- Table structure for table `public_threads`
--

CREATE TABLE `public_threads` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `parent_id` int(10) UNSIGNED DEFAULT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `freelancer_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `public_threads`
--

INSERT INTO `public_threads` (`id`, `user_id`, `parent_id`, `content`, `created_at`, `freelancer_id`) VALUES
(1, 6, NULL, 'helooo', '2025-05-17 04:38:45', 0),
(2, 6, 1, 'hi', '2025-05-17 04:38:50', 0),
(3, 6, 1, 'yeah', '2025-05-17 04:38:55', 0),
(4, 6, 0, 'hello', '2025-05-17 19:09:17', 6),
(5, 6, 4, 'hi', '2025-05-17 19:09:22', 6),
(6, 15, 0, 'heloo', '2025-05-17 19:26:18', 6),
(7, 15, 4, 'hi', '2025-05-17 19:26:27', 6),
(8, 6, 6, 'hiii pp', '2025-05-17 19:27:50', 6),
(9, 24, 0, 'hello', '2025-05-18 04:41:34', 24),
(10, 24, 9, 'hi', '2025-05-18 04:41:41', 24),
(11, 25, 0, 'ff meh', '2025-05-18 05:00:41', 6),
(12, 25, 11, 'hiiii', '2025-05-18 05:00:50', 6),
(13, 15, 0, 'hiii', '2025-05-18 06:20:21', 6),
(14, 27, 0, 'Hello I am ievitable', '2025-05-18 12:28:15', 27),
(15, 27, 14, 'woah', '2025-05-18 12:28:20', 27),
(16, 26, 0, 'law ay', '2025-05-18 12:31:11', 27),
(17, 27, 16, 'hiiii', '2025-05-18 14:05:56', 27),
(18, 26, 16, 'helo', '2025-05-18 14:14:52', 27),
(19, 27, 16, 'ka', '2025-05-18 14:16:30', 27),
(20, 15, 0, 'halo', '2025-05-18 14:19:21', 27),
(21, 15, 16, 'kamo guro', '2025-05-18 14:19:30', 27),
(22, 27, 20, 'hi', '2025-05-18 14:27:35', 27),
(23, 27, 20, 'boom', '2025-05-18 14:32:16', 27),
(24, 27, 20, 'weeeeeeh', '2025-05-18 14:32:30', 27),
(25, 27, 20, 'hiiiiiiiiiiii', '2025-05-18 14:33:38', 27),
(26, 26, 0, 'Hi gwaps', '2025-05-18 14:47:17', 6),
(27, 6, 26, 'helo', '2025-05-18 14:48:20', 6),
(28, 28, 20, 'h1 puh, kamuzta kayoh diyan aq pu si Israel John Mirkado', '2025-05-18 15:20:39', 27),
(29, 31, 0, 'hiiii', '2025-05-20 05:28:10', 31),
(30, 26, 0, 'hi', '2025-05-20 05:30:07', 31),
(31, 26, 29, 'helo', '2025-05-20 05:30:11', 31),
(32, 27, 0, 'omg hi crush', '2025-05-20 20:26:16', 27),
(33, 26, 32, 'yuck', '2025-05-20 20:28:54', 27),
(34, 32, 0, 'Helloooooo', '2025-05-20 20:34:40', 32),
(35, 26, 34, 'hello', '2025-05-20 20:37:19', 32);

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

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`id`, `freelancer_id`, `commissioner_id`, `rating`, `comment`, `created_at`) VALUES
(4, 19, 23, 2, 'weak', '2025-05-16 19:46:12'),
(6, 19, 15, 5, 'damn', '2025-05-16 19:46:52'),
(7, 6, 15, 4, 'meh', '2025-05-17 19:14:09'),
(9, 27, 26, 1, 'laway', '2025-05-18 12:30:46'),
(10, 27, 15, 3, 'meh lawlaw', '2025-05-18 14:19:09'),
(12, 6, 26, 5, '5 kay gwaapo', '2025-05-18 14:47:05'),
(13, 27, 28, 3, 'it\'s ok i guess... n4ku pu', '2025-05-18 15:19:29'),
(14, 32, 26, 2, 'Lawlaw ka', '2025-05-20 20:36:46');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','resolved') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `user_id`, `subject`, `message`, `image_path`, `created_at`, `status`) VALUES
(3, 27, 'Law ay Ka', 'law ay ni sa', 'uploads/reports/report_682b8109075081.46663633.jpg', '2025-05-19 19:05:45', 'resolved');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','inactive') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `role`, `password`, `created_at`, `status`) VALUES
(1, 'dedu', 'dedu@gmail.com', 'freelancer', '$2y$10$0lgh92kDoszx24kHI5aUweLXwLZFdjGlP8o2rXS6wkHt8wx2pMfR.', '2025-05-02 08:07:19', 'active'),
(3, 'Ambot', 'ambot@gmail.com', 'freelancer', '$2y$10$WWzw61OGyGcNbC/FCLTv/.nyR5TyIPJeBSG05PNztG3QPoWqo0X8G', '2025-05-02 09:26:32', 'active'),
(4, 'jj', 'jj@gmail.com', 'freelancer', '$2y$10$aTtlJqNFnY9kGwbqmEnceesGhXguPUpxefZz99..rFZjHKGm4jLOu', '2025-05-03 05:45:10', 'active'),
(5, 'admin', 'admin@gmail.com', 'admin', '$2y$10$v1lR4l7924KBOcU7W6DxPeV/MPgyB.uDuFmmWlhKIERM9YLYewzL2', '2025-05-03 05:57:16', 'active'),
(6, 'aa', 'aa@gmail.com', 'freelancer', '$2y$10$pd/39A2iZ6sZxYLE72jlyOVhZI6exX9GWxn5GtQ3jeQCn2Rh8Nx9y', '2025-05-03 06:45:53', 'active'),
(7, 'kk', 'kk@gmail', 'commissioner', '$2y$10$Aef9ZquGzBBd4RtrzHGdJueujwEthBSzng77VL2GOI8R.05RHcP4u', '2025-05-03 10:11:24', 'active'),
(8, 'EZ', 'EZGITGUD@gmail.com', 'freelancer', '$2y$10$TowNIQ1GEMGP5gbepdR7mOh5wfWvUQAONzeW.vmc2UYMb1RtS4ks2', '2025-05-03 10:28:25', 'active'),
(9, 'bb', 'bb@gmail.com', 'freelancer', '$2y$10$sSo/rEXG5sUXxPP9HQqGhO6WacoGvM3Tw5F.tHxVwW/N37Zik38lW', '2025-05-05 06:01:09', 'active'),
(10, 'ewREGHTMHRWQ3EFNG FTREWH', 'johnisraelmercado51@wvsu.edu.ph', 'freelancer', '$2y$10$CtW4XXkRQ/CyopEzacKA4Oj79WYi54CUVFn6XPwtIynNDdCjhwJBG', '2025-05-05 06:09:13', 'active'),
(11, 'gg', 'gg@wvsu.edu.ph', 'freelancer', '$2y$10$yzImAMz9rWH0pEkV9qmfeO0/F/mTVMmYaeU6nTTGASHKb26cDm7u2', '2025-05-05 07:27:19', 'active'),
(12, 'mm', 'mm@wvsu.edu.ph', 'commissioner', '$2y$10$K4mIfIsxXncfWAz7lrNzIOuLTnvWhXRpM2mtPrpjZM1Mf4O.RVr2O', '2025-05-05 07:31:35', 'active'),
(13, 'jose', 'josesariego@wvsu.edu.ph', 'freelancer', '$2y$10$dUZdAClux7/i2.VZVlmuR.PAt9/p6.kLwPtV6WhqwEscvOllU4LHS', '2025-05-05 07:56:32', 'active'),
(14, 'emmm', 'em@wvsu.edu.ph', 'freelancer', '$2y$10$RbGSbuULsR02qGiREe./lO6BN.iZj4V1UAoseRYy3O9UuBCX8icOS', '2025-05-05 07:57:44', 'active'),
(15, 'pp', 'pp@wvsu.edu.ph', 'commissioner', '$2y$10$C5tRzg94Q.tjWI4tGNueXOK8ZA/khryMGF/8Ch/naluM1v/BvsmPq', '2025-05-05 08:00:43', 'active'),
(16, 'Luisa', 'sjkag@wvsu.edu.ph', 'freelancer', '$2y$10$73X8f4ueHMnDSREnB.eDVOuXMl4YtOm2EzkCNlt/q5NbXt.10Fld6', '2025-05-05 08:09:18', 'active'),
(18, 'rr', 'rr@wvsu.edu.ph', 'freelancer', '$2y$10$9Ob1hP8hjnbHsxE6KqcrCuT2mpaUnfRCRRmjgmWrbVV5nP3GLde8K', '2025-05-05 12:34:56', 'active'),
(19, 'test', 'test@wvsu.edu.ph', 'freelancer', '$2y$10$GbKPvti.SQNmhl2PqcW3aeY2a4p7vja3BIxEXfEn6Q3ai8/smVAWK', '2025-05-06 16:23:41', 'active'),
(20, 'israel', 'israel@wvsu.edu.ph', 'freelancer', '$2y$10$o6vAkkbR13Su5NtMoFylBu0fFUzc6oTVEfdRp84hcIrSr6.NrBhRi', '2025-05-06 17:15:27', 'active'),
(21, 'vv', 'vv@wvsu.edu.ph', 'freelancer', '$2y$10$3yo1ArDFoawJ0yLEDCSnJ.bGSrUl5R/2czJizVvCaL.z8wcXNsjyG', '2025-05-06 17:46:17', 'active'),
(23, 'zz', 'zz@wvsu.edu.ph', 'commissioner', '$2y$10$kkqtgNokXRzY8T9./zN7KuO35b1haa57KgDzjKgrW/F5D/BrJmFKK', '2025-05-16 18:28:00', 'active'),
(24, 'qq', 'qq@wvsu.edu.ph', 'freelancer', '$2y$10$.DVcE3SNVLtE98FY4zsGcu5tpW/5kYmvgVz5tfM8/L0gbnx9nUQHi', '2025-05-18 03:39:59', 'active'),
(25, 'ff', 'ff@wvsu.edu.ph', 'commissioner', '$2y$10$kkAIVSQKY5E9po8Yf7WEVus2jxvE47LHehFwxAhNWzzmx36VN6Z0K', '2025-05-18 03:59:48', 'active'),
(26, 'cmtest', 'cmtest@wvsu.edu.ph', 'commissioner', '$2y$10$K.aS7K1KiSmUrlVO9rz./OtYPA7whQbtcAQIYDkfKc8/YRdsroRoS', '2025-05-18 11:25:44', 'active'),
(27, 'fltest', 'fltest@wvsu.edu.ph', 'freelancer', '$2y$10$SMU76H2mU.PYJmjjg1NRquUaCXbQhG.8FAH6RtvHzCIvs1ebr5fQ.', '2025-05-18 11:27:17', 'active'),
(28, 'Jim jim', 'omaygad@wvsu.edu.ph', 'commissioner', '$2y$10$RKi6O/s1gtKVHWD8.Hz7au4rp/RaQ.urpVQmRADaa6isr00sh3Wwe', '2025-05-18 14:14:44', 'active'),
(29, 'll', 'll@wvsu.edu.ph', 'freelancer', '$2y$10$hTaP8ZfwV0ExaMzt.3qDQ.4ngIje.wBSdBnSBVqhPci1Kos8TEWJu', '2025-05-18 18:58:46', 'active'),
(30, 'nn', 'nn@wvsu.edu.ph', 'freelancer', '$2y$10$q/nlHp..frdKQNwSFwnGbuLSQTS/eM1jMVIumrCEQZZaAxx1r5aj2', '2025-05-19 21:50:23', 'active'),
(31, 'cc', 'cc@wvsu.edu.ph', 'freelancer', '$2y$10$JOrrJ5DQiJtTQPGqklKHdu7rOG6rz2gpt2oygnQb6I4GM2tyU.8Fm', '2025-05-20 03:39:31', 'active'),
(32, 'xx', 'xx@wvsu.edu.ph', 'freelancer', '$2y$10$kHOi17mLZ7TDTahu1h1ocOasstY8uX4CZliMfw7Zp7WYOVtrbdNKO', '2025-05-20 19:31:15', 'active');

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
-- Indexes for table `freelancer_profiles`
--
ALTER TABLE `freelancer_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_id_profiles` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `public_threads`
--
ALTER TABLE `public_threads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_rating` (`freelancer_id`,`commissioner_id`),
  ADD KEY `freelancer_id` (`freelancer_id`),
  ADD KEY `commissioner_id` (`commissioner_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `commissions`
--
ALTER TABLE `commissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `freelancer_photos`
--
ALTER TABLE `freelancer_photos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `freelancer_profiles`
--
ALTER TABLE `freelancer_profiles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `public_threads`
--
ALTER TABLE `public_threads`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `commissions`
--
ALTER TABLE `commissions`
  ADD CONSTRAINT `fk_commissions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `freelancer_profiles`
--
ALTER TABLE `freelancer_profiles`
  ADD CONSTRAINT `fk_user_id_profiles` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`freelancer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`commissioner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
