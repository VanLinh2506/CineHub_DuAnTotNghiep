-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th6 17, 2026 lúc 04:53 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `cinehub`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admin_logs`
--

CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `module` varchar(50) NOT NULL,
  `target_type` varchar(50) DEFAULT NULL,
  `target_id` int(11) DEFAULT NULL,
  `old_data` text DEFAULT NULL,
  `new_data` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `admin_logs`
--

INSERT INTO `admin_logs` (`id`, `user_id`, `action`, `module`, `target_type`, `target_id`, `old_data`, `new_data`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 2, 'create', 'movies', 'movie', 1, NULL, NULL, '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2025-11-12 07:41:09'),
(2, 2, 'update', 'users', 'user', 1, NULL, NULL, '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2025-11-12 07:41:09'),
(3, 3, 'delete', 'comments', 'comment', 1, NULL, NULL, '192.168.1.101', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)', '2025-11-12 07:41:09'),
(4, 2, 'publish', 'movies', 'movie', 2, NULL, NULL, '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2025-11-12 07:41:09'),
(5, 3, 'update', 'theaters', 'theater', 1, NULL, NULL, '192.168.1.101', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)', '2025-11-12 07:41:09'),
(6, 2, 'view', 'analytics', NULL, NULL, NULL, NULL, '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2025-11-12 07:41:09'),
(7, 3, 'Cập nhật điểm người dùng', 'User', 'user', 9, '{\"points\":0}', '{\"points\":100000,\"action\":\"add\",\"points_changed\":100000}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-11-19 01:08:28'),
(8, 3, 'Cập nhật điểm người dùng', 'User', 'user', 9, '{\"points\":100000}', '{\"points\":300000,\"action\":\"add\",\"points_changed\":200000}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-11-19 01:08:44'),
(9, 3, 'Cập nhật điểm người dùng', 'User', 'user', NULL, '{\"points\":0}', '{\"points\":5000,\"action\":\"add\",\"points_changed\":5000}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 02:23:48'),
(10, 3, 'Cập nhật điểm người dùng', 'User', 'user', NULL, '{\"points\":5000}', '{\"points\":505000,\"action\":\"add\",\"points_changed\":500000}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 02:25:18'),
(11, 3, 'Cập nhật vai trò người dùng', 'User', 'user', 9, '{\"role\":\"user\"}', '{\"role\":\"moderator\",\"theater_id\":\"3\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-30 16:13:48'),
(12, 2, 'Cập nhật vai trò người dùng', 'User', 'user', NULL, '{\"role\":\"user\"}', '{\"role\":\"moderator\",\"theater_id\":\"2\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-03 15:26:40'),
(13, 2, 'Thêm phim', 'Movie', 'movie', 38, NULL, '{\"title\":\"hheheh\",\"status\":\"Chi\\u1ebfu r\\u1ea1p\",\"status_admin\":\"published\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-04 02:26:18'),
(14, 2, 'Cập nhật phim', 'Movie', 'movie', 18, '{\"title\":\"Hai Ph\\u01b0\\u1ee3ng\",\"status\":\"Chi\\u1ebfu r\\u1ea1p\",\"status_admin\":\"published\"}', '{\"title\":\"Hai Ph\\u01b0\\u1ee3ng\",\"status\":\"Chi\\u1ebfu r\\u1ea1p\",\"status_admin\":\"published\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-04 02:59:28'),
(15, 3, 'Cập nhật điểm người dùng', 'User', 'user', 16, '{\"points\":0}', '{\"points\":400000,\"action\":\"add\",\"points_changed\":400000}', '171.255.56.31', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2025-12-04 18:30:11'),
(16, 9, 'Tạo nhân viên đứng quầy', 'Counter Staff', 'user', 19, NULL, '{\"name\":\"Le Van Phat\",\"email\":\"plv@gmail.com\"}', '116.97.107.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-06 15:08:04'),
(17, 19, 'Xác nhận vé đã lấy', 'Counter Staff', 'tickets', 816, NULL, '{\"tickets_count\":2,\"updated_count\":2}', '116.97.107.37', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2025-12-06 15:24:22'),
(18, 19, 'Xác nhận vé đã lấy', 'Counter Staff', 'tickets', 817, NULL, '{\"tickets_count\":5,\"updated_count\":5}', '116.97.107.37', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2025-12-06 16:24:40'),
(19, 2, 'Tạo nhân viên đứng quầy', 'Counter Staff', 'user', 20, NULL, '{\"name\":\"Tuan Anh\",\"email\":\"awhtuan@gmail.com\"}', '14.170.222.119', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-06 17:27:56'),
(20, 3, 'Cập nhật vai trò người dùng', 'User', 'user', 23, '{\"role\":\"user\"}', '{\"role\":\"moderator\",\"theater_id\":\"7\"}', '117.2.114.182', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-08 18:54:47'),
(21, 3, 'Cập nhật vai trò người dùng', 'User', 'user', 22, '{\"role\":\"user\"}', '{\"role\":\"moderator\",\"theater_id\":\"6\"}', '117.2.114.182', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-08 18:54:55'),
(22, 19, 'Xác nhận vé đã lấy', 'Counter Staff', 'tickets', 825, NULL, '{\"tickets_count\":2,\"updated_count\":2}', '117.2.114.199', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2025-12-09 03:37:29'),
(23, 3, 'Đã khóa tài khoản người dùng', 'User', 'user', 25, '{\"is_active\":1}', '{\"is_active\":0}', '42.118.61.65', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-10 02:30:30'),
(24, 3, 'Đã mở khóa tài khoản người dùng', 'User', 'user', 25, '{\"is_active\":0}', '{\"is_active\":1}', '42.118.61.65', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-10 02:30:40'),
(25, 3, 'Xóa phim', 'Movie', 'movie', 39, '{\"title\":\"Spider-Man: No Way Home\",\"status\":\"Chi\\u1ebfu r\\u1ea1p\",\"status_admin\":\"published\"}', NULL, '171.255.57.66', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 07:47:20'),
(26, 3, 'Xóa phim', 'Movie', 'movie', 40, '{\"title\":\"Dune\",\"status\":\"Chi\\u1ebfu r\\u1ea1p\",\"status_admin\":\"published\"}', NULL, '171.255.57.66', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 07:47:23'),
(27, 3, 'Xóa phim', 'Movie', 'movie', 41, '{\"title\":\"The Matrix Resurrections\",\"status\":\"Chi\\u1ebfu r\\u1ea1p\",\"status_admin\":\"published\"}', NULL, '171.255.57.66', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 07:47:26'),
(28, 3, 'Xóa phim', 'Movie', 'movie', 42, '{\"title\":\"Fast & Furious 9\",\"status\":\"Chi\\u1ebfu r\\u1ea1p\",\"status_admin\":\"published\"}', NULL, '171.255.57.66', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 07:47:27'),
(29, 3, 'Xóa phim', 'Movie', 'movie', 43, '{\"title\":\"Black Widow\",\"status\":\"Chi\\u1ebfu r\\u1ea1p\",\"status_admin\":\"published\"}', NULL, '171.255.57.66', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 07:47:30'),
(30, 3, 'Xóa phim', 'Movie', 'movie', 44, '{\"title\":\"Shang-Chi\",\"status\":\"Chi\\u1ebfu r\\u1ea1p\",\"status_admin\":\"published\"}', NULL, '171.255.57.66', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 07:47:32'),
(31, 3, 'Xóa phim', 'Movie', 'movie', 45, '{\"title\":\"No Time to Die\",\"status\":\"Chi\\u1ebfu r\\u1ea1p\",\"status_admin\":\"published\"}', NULL, '171.255.57.66', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 07:47:34'),
(32, 3, 'Xóa phim', 'Movie', 'movie', 46, '{\"title\":\"Eternals\",\"status\":\"Chi\\u1ebfu r\\u1ea1p\",\"status_admin\":\"published\"}', NULL, '171.255.57.66', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 07:47:36'),
(33, 3, 'Xóa phim', 'Movie', 'movie', 47, '{\"title\":\"Top Gun: Maverick\",\"status\":\"Chi\\u1ebfu r\\u1ea1p\",\"status_admin\":\"published\"}', NULL, '171.255.57.66', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 07:47:38'),
(34, 3, 'Xóa phim', 'Movie', 'movie', 48, '{\"title\":\"The Batman\",\"status\":\"Chi\\u1ebfu r\\u1ea1p\",\"status_admin\":\"published\"}', NULL, '171.255.57.66', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 07:47:40'),
(35, 3, 'Xóa phim', 'Movie', 'movie', 49, '{\"title\":\"Doctor Strange 2\",\"status\":\"Chi\\u1ebfu r\\u1ea1p\",\"status_admin\":\"published\"}', NULL, '171.255.57.66', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 07:47:43'),
(36, 3, 'Xóa phim', 'Movie', 'movie', 50, '{\"title\":\"Jurassic World: Dominion\",\"status\":\"Chi\\u1ebfu r\\u1ea1p\",\"status_admin\":\"published\"}', NULL, '171.255.57.66', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-11 07:47:45'),
(37, 22, 'Tạo nhân viên đứng quầy', 'Counter Staff', 'user', 30, NULL, '{\"name\":\"Linh\",\"email\":\"nguyenvanlinh250606@gmail.com\"}', '42.118.61.65', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-12 02:06:48'),
(38, 3, 'Cập nhật phim', 'Movie', 'movie', 12, '{\"title\":\"House of Cards\",\"status\":\"Chi\\u1ebfu online\",\"status_admin\":\"published\"}', '{\"title\":\"House of Cards\",\"status\":\"Chi\\u1ebfu online\",\"status_admin\":\"published\"}', '118.70.43.248', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-13 12:23:20'),
(39, 3, 'Xóa tập phim', 'Episode', 'episode', 6, NULL, '{\"movie_id\":\"12\"}', '118.70.43.248', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-13 12:24:15'),
(40, 3, 'Xóa tập phim', 'Episode', 'episode', 7, NULL, '{\"movie_id\":\"12\"}', '118.70.43.248', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-13 12:24:18'),
(41, 3, 'Xóa tập phim', 'Episode', 'episode', 8, NULL, '{\"movie_id\":\"12\"}', '118.70.43.248', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-13 12:24:22'),
(42, 3, 'Xóa tập phim', 'Episode', 'episode', 9, NULL, '{\"movie_id\":\"12\"}', '118.70.43.248', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-13 12:24:25'),
(43, 3, 'Xóa tập phim', 'Episode', 'episode', 10, NULL, '{\"movie_id\":\"12\"}', '118.70.43.248', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-13 12:24:28'),
(44, 3, 'Xóa tập phim', 'Episode', 'episode', 11, NULL, '{\"movie_id\":\"12\"}', '118.70.43.248', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-13 12:24:31'),
(45, 3, 'Cập nhật phim', 'Movie', 'movie', 28, '{\"title\":\"H\\u01b0\\u01a1ng V\\u1ecb T\\u00ecnh Th\\u00e2n (Ph\\u1ea7n 1)\",\"status\":\"Chi\\u1ebfu online\",\"status_admin\":\"published\"}', '{\"title\":\"H\\u01b0\\u01a1ng V\\u1ecb T\\u00ecnh Th\\u00e2n (Ph\\u1ea7n 1)\",\"status\":\"Chi\\u1ebfu online\",\"status_admin\":\"published\"}', '118.70.43.248', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-13 13:43:19'),
(46, 3, 'Cập nhật phim', 'Movie', 'movie', 38, '{\"title\":\"hheheh\",\"status\":\"Chi\\u1ebfu r\\u1ea1p\",\"status_admin\":\"published\"}', '{\"title\":\"hheheh\",\"status\":\"Chi\\u1ebfu r\\u1ea1p\",\"status_admin\":\"published\"}', '103.156.42.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 14:09:10'),
(47, 3, 'Thêm phim', 'Movie', 'movie', 51, NULL, '{\"title\":\"Pha\\u0300m Nh\\u00e2n Tu Ti\\u00ean\",\"status\":\"Chi\\u1ebfu online\",\"status_admin\":\"draft\"}', '171.255.57.178', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-15 01:40:15'),
(48, 3, 'Cập nhật phim', 'Movie', 'movie', 51, '{\"title\":\"Pha\\u0300m Nh\\u00e2n Tu Ti\\u00ean\",\"status\":\"Chi\\u1ebfu online\",\"status_admin\":\"draft\"}', '{\"title\":\"Pha\\u0300m Nh\\u00e2n Tu Ti\\u00ean\",\"status\":\"Chi\\u1ebfu online\",\"status_admin\":\"draft\"}', '171.255.57.178', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-15 01:40:44'),
(49, 3, 'Cập nhật phim', 'Movie', 'movie', 9, '{\"title\":\"Breaking Bad\",\"status\":\"Chi\\u1ebfu online\",\"status_admin\":\"published\"}', '{\"title\":\"Breaking Bad\",\"status\":\"Chi\\u1ebfu online\",\"status_admin\":\"published\"}', '42.118.61.65', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-15 02:43:24'),
(50, 3, 'Cập nhật phim', 'Movie', 'movie', 9, '{\"title\":\"Breaking Bad\",\"status\":\"Chi\\u1ebfu online\",\"status_admin\":\"published\"}', '{\"title\":\"Breaking Bad\",\"status\":\"Chi\\u1ebfu online\",\"status_admin\":\"published\"}', '42.118.61.65', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-15 02:43:45'),
(51, 3, 'Thêm phim', 'Movie', 'movie', 52, NULL, '{\"title\":\"m\\u01b0a \\u0111o\\u0309\",\"status\":\"Chi\\u1ebfu r\\u1ea1p\",\"status_admin\":\"draft\"}', '42.118.61.65', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-15 03:22:07'),
(52, 3, 'Cập nhật phim', 'Movie', 'movie', 52, '{\"title\":\"m\\u01b0a \\u0111o\\u0309\",\"status\":\"Chi\\u1ebfu r\\u1ea1p\",\"status_admin\":\"draft\"}', '{\"title\":\"m\\u01b0a \\u0111o\\u0309\",\"status\":\"Chi\\u1ebfu r\\u1ea1p\",\"status_admin\":\"draft\"}', '42.118.61.65', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-15 03:24:21'),
(53, 3, 'Cập nhật phim', 'Movie', 'movie', 52, '{\"title\":\"m\\u01b0a \\u0111o\\u0309\",\"status\":\"Chi\\u1ebfu r\\u1ea1p\",\"status_admin\":\"draft\"}', '{\"title\":\"m\\u01b0a \\u0111o\\u0309\",\"status\":\"Chi\\u1ebfu r\\u1ea1p\",\"status_admin\":\"draft\"}', '58.186.230.31', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 13:08:29'),
(54, 3, 'Thêm phim', 'Movie', 'movie', 53, NULL, '{\"title\":\"Ti\\u00ean ngh\\u1ecbch\",\"status\":\"Chi\\u1ebfu online\",\"status_admin\":\"published\"}', '27.67.131.143', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.2 Mobile/15E148 Safari/604.1', '2026-02-09 03:06:41'),
(55, 3, 'Cập nhật phim', 'Movie', 'movie', 53, '{\"title\":\"Ti\\u00ean ngh\\u1ecbch\",\"status\":\"Chi\\u1ebfu online\",\"status_admin\":\"published\"}', '{\"title\":\"Ti\\u00ean ngh\\u1ecbch\",\"status\":\"Chi\\u1ebfu online\",\"status_admin\":\"published\"}', '27.67.131.143', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.2 Mobile/15E148 Safari/604.1', '2026-02-09 03:07:26'),
(56, 3, 'Thêm bình luận', 'Review', 'review', 14, NULL, '{\"movie_id\":\"53\",\"rating\":\"5\"}', '27.67.131.143', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.2 Mobile/15E148 Safari/604.1', '2026-02-09 03:08:02');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `booking_food_items`
--

CREATE TABLE `booking_food_items` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) DEFAULT NULL COMMENT 'Liên kết với ticket (để tương thích ngược)',
  `booking_pending_id` int(11) DEFAULT NULL COMMENT 'Liên kết với booking_pending (ưu tiên)',
  `food_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `booking_food_items`
--

INSERT INTO `booking_food_items` (`id`, `ticket_id`, `booking_pending_id`, `food_item_id`, `quantity`, `price`, `created_at`) VALUES
(10, NULL, 819, 1, 1, 85000.00, '2025-12-08 04:05:25'),
(11, NULL, 820, 1, 1, 85000.00, '2025-12-08 19:03:06'),
(12, NULL, 821, 1, 1, 85000.00, '2025-12-08 19:04:25'),
(13, NULL, 822, 1, 1, 85000.00, '2025-12-08 19:17:15'),
(14, NULL, 823, 1, 1, 85000.00, '2025-12-09 00:32:15'),
(15, NULL, 825, 1, 1, 85000.00, '2025-12-09 03:36:07'),
(16, NULL, 825, 2, 1, 120000.00, '2025-12-09 03:36:07'),
(17, NULL, 825, 3, 1, 150000.00, '2025-12-09 03:36:07'),
(18, NULL, 825, 35, 1, 95000.00, '2025-12-09 03:36:07'),
(19, NULL, 825, 18, 1, 200000.00, '2025-12-09 03:36:07'),
(20, NULL, 825, 34, 1, 280000.00, '2025-12-09 03:36:07'),
(21, NULL, 825, 10, 1, 250000.00, '2025-12-09 03:36:07'),
(22, NULL, 825, 25, 1, 130000.00, '2025-12-09 03:36:07'),
(23, NULL, 825, 19, 1, 90000.00, '2025-12-09 03:36:07'),
(24, NULL, 825, 9, 1, 140000.00, '2025-12-09 03:36:07'),
(25, NULL, 825, 27, 1, 70000.00, '2025-12-09 03:36:07'),
(26, NULL, 825, 26, 1, 220000.00, '2025-12-09 03:36:07'),
(27, NULL, 825, 33, 1, 170000.00, '2025-12-09 03:36:07'),
(28, NULL, 825, 11, 1, 110000.00, '2025-12-09 03:36:07'),
(29, NULL, 825, 17, 1, 160000.00, '2025-12-09 03:36:07'),
(30, NULL, 825, 22, 1, 70000.00, '2025-12-09 03:36:07'),
(31, NULL, 825, 30, 1, 60000.00, '2025-12-09 03:36:07'),
(32, NULL, 825, 38, 1, 120000.00, '2025-12-09 03:36:07'),
(33, NULL, 825, 29, 1, 35000.00, '2025-12-09 03:36:07'),
(34, NULL, 825, 20, 1, 60000.00, '2025-12-09 03:36:07'),
(35, NULL, 825, 12, 1, 65000.00, '2025-12-09 03:36:07'),
(36, NULL, 825, 4, 1, 55000.00, '2025-12-09 03:36:07'),
(37, NULL, 825, 28, 1, 60000.00, '2025-12-09 03:36:07'),
(38, NULL, 825, 13, 1, 65000.00, '2025-12-09 03:36:07'),
(39, NULL, 825, 36, 1, 70000.00, '2025-12-09 03:36:07'),
(40, NULL, 825, 5, 1, 40000.00, '2025-12-09 03:36:07'),
(41, NULL, 825, 14, 1, 75000.00, '2025-12-09 03:36:07'),
(42, NULL, 825, 37, 1, 85000.00, '2025-12-09 03:36:07'),
(43, NULL, 825, 21, 1, 50000.00, '2025-12-09 03:36:07'),
(44, NULL, 825, 8, 1, 45000.00, '2025-12-09 03:36:07'),
(45, NULL, 825, 16, 1, 40000.00, '2025-12-09 03:36:07'),
(46, NULL, 825, 39, 1, 40000.00, '2025-12-09 03:36:07'),
(47, NULL, 825, 32, 1, 50000.00, '2025-12-09 03:36:07'),
(48, NULL, 825, 15, 1, 45000.00, '2025-12-09 03:36:07'),
(49, NULL, 825, 24, 1, 20000.00, '2025-12-09 03:36:07'),
(50, NULL, 825, 6, 1, 35000.00, '2025-12-09 03:36:07'),
(51, NULL, 825, 7, 1, 25000.00, '2025-12-09 03:36:07'),
(52, NULL, 825, 40, 1, 40000.00, '2025-12-09 03:36:07'),
(53, NULL, 825, 31, 1, 40000.00, '2025-12-09 03:36:07'),
(54, NULL, 825, 23, 1, 55000.00, '2025-12-09 03:36:07'),
(55, NULL, 826, 1, 1, 85000.00, '2025-12-09 04:57:37'),
(56, NULL, 832, 2, 1, 120000.00, '2025-12-09 11:48:06'),
(57, NULL, 832, 9, 1, 140000.00, '2025-12-09 11:48:06'),
(58, NULL, 832, 27, 1, 70000.00, '2025-12-09 11:48:06'),
(59, NULL, 832, 26, 1, 220000.00, '2025-12-09 11:48:06'),
(60, NULL, 832, 33, 1, 170000.00, '2025-12-09 11:48:06'),
(61, NULL, 832, 11, 2, 110000.00, '2025-12-09 11:48:06'),
(62, NULL, 832, 17, 2, 160000.00, '2025-12-09 11:48:06'),
(63, NULL, 832, 22, 6, 70000.00, '2025-12-09 11:48:06'),
(64, NULL, 832, 30, 1, 60000.00, '2025-12-09 11:48:06'),
(65, NULL, 832, 38, 1, 120000.00, '2025-12-09 11:48:06'),
(66, NULL, 832, 29, 1, 35000.00, '2025-12-09 11:48:06'),
(67, NULL, 832, 20, 1, 60000.00, '2025-12-09 11:48:06'),
(68, NULL, 832, 12, 1, 65000.00, '2025-12-09 11:48:06'),
(69, NULL, 832, 4, 2, 55000.00, '2025-12-09 11:48:06'),
(70, NULL, 832, 28, 1, 60000.00, '2025-12-09 11:48:06'),
(71, NULL, 833, 2, 1, 120000.00, '2025-12-09 11:54:01'),
(72, NULL, 833, 3, 1, 150000.00, '2025-12-09 11:54:01'),
(73, NULL, 833, 35, 1, 95000.00, '2025-12-09 11:54:01'),
(74, NULL, 833, 18, 1, 200000.00, '2025-12-09 11:54:01'),
(75, NULL, 833, 34, 1, 280000.00, '2025-12-09 11:54:01'),
(76, NULL, 833, 10, 1, 250000.00, '2025-12-09 11:54:01'),
(77, NULL, 833, 25, 1, 130000.00, '2025-12-09 11:54:01'),
(78, NULL, 833, 19, 1, 90000.00, '2025-12-09 11:54:01'),
(79, NULL, 844, 1, 1, 85000.00, '2025-12-09 15:29:37'),
(80, NULL, 856, 1, 1, 85000.00, '2025-12-10 07:49:42'),
(81, NULL, 859, 1, 1, 85000.00, '2025-12-10 08:42:34'),
(82, NULL, 860, 1, 1, 85000.00, '2025-12-10 08:55:51'),
(83, NULL, 860, 2, 1, 120000.00, '2025-12-10 08:55:51'),
(84, NULL, 860, 3, 1, 150000.00, '2025-12-10 08:55:51'),
(85, NULL, 865, 1, 1, 85000.00, '2025-12-12 02:05:25'),
(86, NULL, 867, 1, 1, 85000.00, '2025-12-15 03:06:58'),
(87, NULL, 870, 1, 1, 85000.00, '2025-12-29 14:37:36');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `booking_pending`
--

CREATE TABLE `booking_pending` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `showtime_id` int(11) NOT NULL,
  `seats` text NOT NULL,
  `food_items` text DEFAULT NULL,
  `customer_email` varchar(255) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `vnp_txn_ref` varchar(100) DEFAULT NULL,
  `booking_code` varchar(100) DEFAULT NULL,
  `status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NULL DEFAULT NULL,
  `qr_code` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `booking_pending`
--

INSERT INTO `booking_pending` (`id`, `user_id`, `showtime_id`, `seats`, `food_items`, `customer_email`, `total_amount`, `vnp_txn_ref`, `booking_code`, `status`, `created_at`, `expires_at`, `qr_code`) VALUES
(244, 2, 1000, '[\"B1\",\"A2\",\"I3\",\"E4\",\"D5\",\"I6\",\"J7\",\"A8\",\"A9\",\"B10\",\"C11\",\"I12\",\"A13\",\"G14\"]', NULL, 'user2@test.com', 1680000.00, 'BOOKING_2_1000_1765032206_12157', 'BOOKING_2_1000_1765032206_12157', 'completed', '2025-12-06 14:43:26', NULL, NULL),
(258, 1, 1001, '[\"G1\",\"H2\",\"E3\",\"D4\",\"E5\"]', NULL, 'user1@test.com', 600000.00, 'BOOKING_1_1001_1765032206_35398', 'BOOKING_1_1001_1765032206_35398', 'completed', '2025-12-06 14:43:26', NULL, NULL),
(529, 9, 1002, '[\"A1\",\"A2\"]', NULL, 'user9@test.com', 240000.00, 'BOOKING_9_1002_1765032206_91704', 'BOOKING_9_1002_1765032206_91704', 'completed', '2025-12-06 14:43:26', NULL, NULL),
(543, 10, 1003, '[\"I1\",\"B2\",\"F3\",\"E4\",\"A5\",\"D6\"]', NULL, 'user10@test.com', 720000.00, 'BOOKING_10_1003_1765032206_96935', 'BOOKING_10_1003_1765032206_96935', 'completed', '2025-12-06 14:43:26', NULL, NULL),
(807, 3, 1004, '[\"C1\",\"D2\",\"D3\",\"J4\",\"I5\",\"I6\",\"B7\",\"E8\",\"B9\"]', NULL, 'user3@test.com', 1080000.00, 'BOOKING_3_1004_1765032207_59543', 'BOOKING_3_1004_1765032207_59543', 'completed', '2025-12-06 14:43:27', NULL, NULL),
(819, 10, 1005, '[\"H3\",\"H4\"]', '{\"1\":1}', 'tuanawh@gmail.com', 325000.00, 'BOOKING_10_1005_1765166697_5479', NULL, 'completed', '2025-12-08 04:04:57', '2025-12-07 21:14:57', 'BOOKING_69364e8506784_819_1765166725'),
(820, 9, 41713, '[\"I5\",\"I6\",\"I7\"]', '{\"1\":1}', 'nguyenvanlinh25062006@gmail.com', 685000.00, 'BOOKING_9_41713_1765220517_1584', NULL, 'completed', '2025-12-08 19:01:57', '2025-12-08 12:11:57', 'BOOKING_693720eaa022e_820_1765220586'),
(821, 9, 1008, '[\"F3\",\"F4\"]', '{\"1\":1}', 'nguyenvanlinh25062006@gmail.com', 425000.00, 'BOOKING_9_1008_1765220643_4089', NULL, 'completed', '2025-12-08 19:04:03', '2025-12-08 12:14:03', 'BOOKING_6937213938528_821_1765220665'),
(822, 9, 1009, '[\"F1\",\"F2\"]', '{\"1\":1}', 'nguyenvanlinh25062006@gmail.com', 425000.00, 'BOOKING_9_1009_1765221334_2542', NULL, 'completed', '2025-12-08 19:15:34', '2025-12-08 12:25:34', 'BOOKING_6937243b11636_822_1765221435'),
(823, 9, 1010, '[\"G3\",\"G4\",\"G5\"]', '{\"1\":1}', 'nguyenvanlinh25062006@gmail.com', 415000.00, 'BOOKING_9_1010_1765240201_7726', NULL, 'completed', '2025-12-09 00:30:01', '2025-12-08 17:40:01', 'BOOKING_69376e0f5b859_823_1765240335'),
(825, 25, 41726, '[\"L7\",\"L8\"]', '{\"1\":1,\"2\":1,\"3\":1,\"35\":1,\"18\":1,\"34\":1,\"10\":1,\"25\":1,\"19\":1,\"9\":1,\"27\":1,\"26\":1,\"33\":1,\"11\":1,\"17\":1,\"22\":1,\"30\":1,\"38\":1,\"29\":1,\"20\":1,\"12\":1,\"4\":1,\"28\":1,\"13\":1,\"36\":1,\"5\":1,\"14\":1,\"37\":1,\"21\":1,\"8\":1,\"16\":1,\"39\":1,\"32\":1,\"15\":1,\"24\":1,\"6\":1,\"7\":1,\"40\":1,\"31\":1,\"23\":1}', 'vanquan2006k@gmail.com', 3855000.00, 'BOOKING_25_41726_1765251311_1707', NULL, 'completed', '2025-12-09 03:35:11', '2025-12-08 20:45:11', 'BOOKING_69379927d6a67_825_1765251367'),
(826, 3, 41727, '[\"H3\",\"H4\",\"H5\"]', '{\"1\":1}', 'admin2@cinehub.com', 355000.00, 'BOOKING_3_41727_1765256213_5120', NULL, 'completed', '2025-12-09 04:56:53', '2025-12-08 22:06:53', 'BOOKING_6937ac4151ea1_826_1765256257'),
(829, 27, 1001, '[\"B5\",\"B6\"]', '{\"1\":1,\"2\":1}', 'thutrang18680@gmail.com', 445000.00, 'BOOKING_27_1001_1765280342_1401', NULL, 'cancelled', '2025-12-09 11:39:02', '2025-12-09 04:49:02', NULL),
(830, 27, 1001, '[\"C5\",\"C6\"]', NULL, 'thutrang12@gmail.com', 240000.00, 'BOOKING_27_1001_1765280522_5483', NULL, 'cancelled', '2025-12-09 11:42:02', '2025-12-09 04:52:02', NULL),
(831, 27, 1001, '[\"H7\",\"L3\",\"L4\"]', '{\"2\":1}', 'thutrang12@gmail.com', 540000.00, 'BOOKING_27_1001_1765280650_3041', NULL, 'cancelled', '2025-12-09 11:44:10', '2025-12-09 04:54:10', NULL),
(832, 27, 1005, '[\"C7\",\"C8\"]', '{\"2\":1,\"9\":1,\"27\":1,\"26\":1,\"33\":1,\"11\":2,\"17\":2,\"22\":6,\"30\":1,\"38\":1,\"29\":1,\"20\":1,\"12\":1,\"4\":2,\"28\":1}', 'thutrang18680@gmail.com', 2450000.00, 'BOOKING_27_1005_1765280839_1609', NULL, 'completed', '2025-12-09 11:47:19', '2025-12-09 04:57:19', 'BOOKING_69380c7630879_832_1765280886'),
(833, 27, 1005, '[\"A1\",\"A2\",\"A3\",\"A4\",\"A5\",\"A6\",\"A7\",\"A8\"]', '{\"2\":1,\"3\":1,\"35\":1,\"18\":1,\"34\":1,\"10\":1,\"25\":1,\"19\":1}', 'thutrang12@gmail.com', 2355000.00, 'BOOKING_27_1005_1765281199_8697', NULL, 'completed', '2025-12-09 11:53:19', '2025-12-09 05:03:19', 'BOOKING_69380dd96c6db_833_1765281241'),
(844, 9, 41731, '[\"I4\",\"I5\"]', '{\"1\":1}', 'nguyenvanlinh25062006@gmail.com', 485000.00, 'BOOKING_9_41731_1765294150_5419', NULL, 'completed', '2025-12-09 15:29:10', '2025-12-09 08:39:10', 'BOOKING_69384061879be_844_1765294177'),
(846, 25, 41731, '[\"I2\",\"I3\"]', NULL, 'vanquan2006k@gmail.com', 400000.00, 'BOOKING_25_41731_1765294219_7440', NULL, 'completed', '2025-12-09 15:30:19', '2025-12-09 08:40:19', 'BOOKING_693840c61a4d6_846_1765294278'),
(853, 3, 41729, '[\"I7\",\"I8\"]', NULL, 'admin2@cinehub.com', 420000.00, 'BOOKING_3_41729_1765351775_3344', NULL, 'cancelled', '2025-12-10 07:29:35', '2025-12-10 00:39:35', NULL),
(854, 3, 41729, '[\"L8\",\"L9\"]', NULL, 'admin2@cinehub.com', 280000.00, 'BOOKING_3_41729_1765351840_8044', NULL, 'completed', '2025-12-10 07:30:40', '2025-12-10 00:40:40', 'BOOKING_693921b87dd32_854_1765351864'),
(856, 9, 41740, '[\"I1\",\"I2\",\"I3\",\"I4\"]', '{\"1\":1}', 'nguyenvanlinh25062006@gmail.com', 805000.00, 'BOOKING_9_41740_1765352942_9119', NULL, 'completed', '2025-12-10 07:49:02', '2025-12-10 00:59:02', 'BOOKING_69392616a2516_856_1765352982'),
(859, 9, 41746, '[\"H1\",\"H2\",\"H3\",\"H4\",\"H5\"]', '{\"1\":1}', 'nguyenvanlinh25062006@gmail.com', 1085000.00, 'BOOKING_9_41746_1765356127_7181', NULL, 'completed', '2025-12-10 08:42:07', '2025-12-10 01:52:07', 'BOOKING_6939327a47860_859_1765356154'),
(860, 9, 1000, '[\"H4\",\"H5\",\"H6\"]', '{\"1\":1,\"2\":1,\"3\":1}', 'nguyenvanlinh25062006@gmail.com', 895000.00, 'BOOKING_9_1000_1765356919_2547', NULL, 'completed', '2025-12-10 08:55:19', '2025-12-10 02:05:19', 'BOOKING_69393597ea3b9_860_1765356951'),
(865, 10, 41758, '[\"J6\",\"J7\"]', '{\"1\":1}', 'tuanawh@gmail.com', 485000.00, 'BOOKING_10_41758_1765505045_4719', NULL, 'completed', '2025-12-12 02:04:05', '2025-12-11 19:14:05', 'BOOKING_693b7865d6729_865_1765505125'),
(867, 22, 41801, '[\"E1\",\"E2\"]', '{\"1\":1}', 'lotte@gmail.com', 345000.00, 'BOOKING_22_41801_1765767970_8466', NULL, 'completed', '2025-12-15 03:06:10', '2025-12-14 20:16:10', 'BOOKING_693f7b5244924_867_1765768018'),
(869, 10, 41824, '[\"G5\",\"G6\"]', NULL, 'tuanawh@gmail.com', 240000.00, 'BOOKING_10_41824_1766859821_2990', NULL, 'completed', '2025-12-27 18:23:41', '2025-12-27 11:33:41', 'BOOKING_69502449c58ec_869_1766859849'),
(870, 9, 41825, '[\"I3\",\"I4\"]', '{\"1\":1}', 'nguyenvanlinh25062006@gmail.com', 537000.00, 'BOOKING_9_41825_1767019012_9638', NULL, 'completed', '2025-12-29 14:36:52', '2025-12-29 07:46:52', 'BOOKING_69529230a4505_870_1767019056'),
(872, 32, 41826, '[\"I8\",\"I9\"]', NULL, 'ledinhtrung35@gmail.com', 218000.00, 'BOOKING_32_41826_1769520482_3177', NULL, 'cancelled', '2026-01-27 13:28:02', '2026-01-27 06:38:02', NULL),
(873, 32, 41826, '[\"I8\",\"I9\"]', NULL, 'ledinhtrung35@gmail.com', 218000.00, 'BOOKING_32_41826_1769520531_3424', NULL, 'cancelled', '2026-01-27 13:28:51', '2026-01-27 06:38:51', NULL),
(874, 33, 41827, '[\"L7\",\"L8\"]', '{\"1\":1}', 'ledinhtrungkm35@gmail.com', 249000.00, 'BOOKING_33_41827_1769522294_3030', NULL, 'cancelled', '2026-01-27 13:58:14', '2026-01-27 07:08:14', NULL),
(875, 33, 41828, '[\"H8\",\"H9\"]', '{\"1\":1}', 'ledinhtrungkm35@gmail.com', 318600.00, 'BOOKING_33_41828_1769534756_3145', NULL, 'cancelled', '2026-01-27 17:25:56', '2026-01-27 10:35:56', NULL),
(878, 33, 41829, '[\"K8\",\"K9\"]', NULL, 'ledinhtrungkm35@gmail.com', 233600.00, 'BOOKING_33_41829_1770088306_4445', NULL, 'pending', '2026-02-03 03:11:46', '2026-02-02 20:21:46', NULL),
(879, 9, 41830, '[\"E5\",\"F5\",\"F6\",\"F7\",\"F8\",\"F9\",\"N5\",\"N6\"]', '{\"1\":1}', 'nguyenvanlinh25062006@gmail.com', 1033800.00, 'BOOKING_9_41830_1770189171_5411', NULL, 'cancelled', '2026-02-04 07:12:51', '2026-02-04 00:22:51', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `booking_session_tracking`
--

CREATE TABLE `booking_session_tracking` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `showtime_id` int(11) NOT NULL,
  `screen_id` int(11) NOT NULL,
  `session_start` datetime NOT NULL,
  `session_end` datetime DEFAULT NULL,
  `total_duration_seconds` int(11) DEFAULT 0,
  `violation_count` int(11) DEFAULT 0,
  `is_banned` tinyint(1) DEFAULT 0,
  `ban_until` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `booking_session_tracking`
--

INSERT INTO `booking_session_tracking` (`id`, `user_id`, `showtime_id`, `screen_id`, `session_start`, `session_end`, `total_duration_seconds`, `violation_count`, `is_banned`, `ban_until`, `created_at`) VALUES
(87, 10, 1005, 17, '2025-12-08 11:04:49', NULL, 0, 0, 0, NULL, '2025-12-08 04:04:49'),
(88, 9, 41713, 31, '2025-12-09 02:01:50', '2025-12-09 22:15:31', 47621, 1, 0, NULL, '2025-12-08 19:01:50'),
(89, 9, 1008, 3, '2025-12-09 02:03:56', '2025-12-09 22:24:27', 48031, 1, 0, NULL, '2025-12-08 19:03:56'),
(90, 9, 1009, 3, '2025-12-09 02:15:27', '2025-12-10 15:54:33', 110346, 2, 0, NULL, '2025-12-08 19:15:27'),
(91, 9, 1010, 3, '2025-12-09 07:29:45', NULL, 0, 0, 0, NULL, '2025-12-09 00:29:45'),
(92, 9, 41716, 31, '2025-12-09 07:46:07', '2025-12-09 22:15:42', 26975, 2, 0, NULL, '2025-12-09 00:46:07'),
(93, 24, 1008, 3, '2025-12-09 09:33:28', NULL, 0, 0, 0, NULL, '2025-12-09 02:33:28'),
(94, 24, 1009, 3, '2025-12-09 09:33:36', NULL, 0, 0, 0, NULL, '2025-12-09 02:33:36'),
(95, 24, 1010, 3, '2025-12-09 09:34:03', NULL, 0, 0, 0, NULL, '2025-12-09 02:34:03'),
(96, 25, 41726, 27, '2025-12-09 10:34:01', NULL, 0, 0, 0, NULL, '2025-12-09 03:34:01'),
(97, 26, 41725, 27, '2025-12-09 10:38:06', NULL, 0, 0, 0, NULL, '2025-12-09 03:38:06'),
(98, 25, 41725, 27, '2025-12-09 10:38:31', NULL, 0, 0, 0, NULL, '2025-12-09 03:38:31'),
(99, 9, 41725, 27, '2025-12-09 11:55:08', NULL, 0, 0, 0, NULL, '2025-12-09 04:55:08'),
(100, 9, 41727, 12, '2025-12-09 11:55:10', NULL, 0, 0, 0, NULL, '2025-12-09 04:55:10'),
(101, 3, 41727, 12, '2025-12-09 11:56:02', NULL, 0, 0, 0, NULL, '2025-12-09 04:56:02'),
(102, 12, 41727, 12, '2025-12-09 11:56:46', NULL, 0, 0, 0, NULL, '2025-12-09 04:56:46'),
(103, 3, 41726, 27, '2025-12-09 11:58:24', NULL, 0, 0, 0, NULL, '2025-12-09 04:58:24'),
(104, 27, 1004, 2, '2025-12-09 18:34:50', NULL, 0, 0, 0, NULL, '2025-12-09 11:34:50'),
(105, 27, 1001, 1, '2025-12-09 18:37:23', NULL, 0, 0, 0, NULL, '2025-12-09 11:37:23'),
(106, 27, 1005, 2, '2025-12-09 18:46:27', NULL, 0, 0, 0, NULL, '2025-12-09 11:46:27'),
(107, 9, 41728, 12, '2025-12-09 22:17:00', NULL, 0, 0, 0, NULL, '2025-12-09 15:17:00'),
(108, 25, 41728, 12, '2025-12-09 22:17:57', NULL, 0, 0, 0, NULL, '2025-12-09 15:17:57'),
(109, 9, 1073, 1, '2025-12-09 22:24:58', NULL, 0, 0, 0, NULL, '2025-12-09 15:24:58'),
(110, 9, 1074, 1, '2025-12-09 22:25:09', NULL, 0, 0, 0, NULL, '2025-12-09 15:25:09'),
(111, 25, 41729, 5, '2025-12-09 22:26:46', NULL, 0, 0, 0, NULL, '2025-12-09 15:26:46'),
(112, 9, 41729, 5, '2025-12-09 22:26:48', '2025-12-10 14:40:38', 33230, 1, 0, NULL, '2025-12-09 15:26:48'),
(113, 9, 41730, 12, '2025-12-09 22:27:59', NULL, 0, 0, 0, NULL, '2025-12-09 15:27:59'),
(114, 25, 41730, 12, '2025-12-09 22:28:07', NULL, 0, 0, 0, NULL, '2025-12-09 15:28:07'),
(115, 9, 41718, 29, '2025-12-09 22:28:44', NULL, 0, 0, 0, NULL, '2025-12-09 15:28:44'),
(116, 25, 41731, 6, '2025-12-09 22:28:45', NULL, 0, 0, 0, NULL, '2025-12-09 15:28:45'),
(117, 9, 41717, 29, '2025-12-09 22:28:46', NULL, 0, 0, 0, NULL, '2025-12-09 15:28:46'),
(118, 9, 41731, 6, '2025-12-09 22:28:49', '2025-12-10 07:46:01', 8232, 1, 0, NULL, '2025-12-09 15:28:49'),
(119, 9, 41723, 5, '2025-12-09 22:32:12', NULL, 0, 0, 0, NULL, '2025-12-09 15:32:12'),
(120, 9, 41732, 11, '2025-12-09 22:32:15', NULL, 0, 0, 0, NULL, '2025-12-09 15:32:15'),
(121, 9, 41714, 32, '2025-12-09 22:47:19', '2025-12-10 15:25:17', 34678, 1, 0, NULL, '2025-12-09 15:47:19'),
(122, 9, 41715, 31, '2025-12-09 22:47:23', NULL, 0, 0, 0, NULL, '2025-12-09 15:47:23'),
(123, 9, 41733, 5, '2025-12-09 22:50:32', NULL, 0, 0, 0, NULL, '2025-12-09 15:50:32'),
(124, 3, 1004, 2, '2025-12-10 07:40:27', NULL, 0, 0, 0, NULL, '2025-12-10 00:40:27'),
(125, 3, 1001, 1, '2025-12-10 07:40:29', NULL, 0, 0, 0, NULL, '2025-12-10 00:40:29'),
(126, 3, 1005, 2, '2025-12-10 07:40:31', NULL, 0, 0, 0, NULL, '2025-12-10 00:40:31'),
(127, 3, 1002, 1, '2025-12-10 07:40:32', NULL, 0, 0, 0, NULL, '2025-12-10 00:40:32'),
(128, 3, 1003, 1, '2025-12-10 07:40:33', NULL, 0, 0, 0, NULL, '2025-12-10 00:40:33'),
(129, 3, 1007, 2, '2025-12-10 07:40:34', NULL, 0, 0, 0, NULL, '2025-12-10 00:40:34'),
(130, 3, 1000, 1, '2025-12-10 07:40:36', NULL, 0, 0, 0, NULL, '2025-12-10 00:40:36'),
(131, 3, 1008, 3, '2025-12-10 07:41:01', NULL, 0, 0, 0, NULL, '2025-12-10 00:41:01'),
(132, 3, 1009, 3, '2025-12-10 07:41:02', NULL, 0, 0, 0, NULL, '2025-12-10 00:41:02'),
(133, 3, 1010, 3, '2025-12-10 07:41:03', NULL, 0, 0, 0, NULL, '2025-12-10 00:41:03'),
(134, 3, 1011, 3, '2025-12-10 07:41:04', NULL, 0, 0, 0, NULL, '2025-12-10 00:41:04'),
(135, 3, 1012, 5, '2025-12-10 07:41:16', NULL, 0, 0, 0, NULL, '2025-12-10 00:41:16'),
(136, 3, 1013, 5, '2025-12-10 07:41:17', NULL, 0, 0, 0, NULL, '2025-12-10 00:41:17'),
(137, 3, 1014, 5, '2025-12-10 07:41:19', NULL, 0, 0, 0, NULL, '2025-12-10 00:41:19'),
(138, 3, 1015, 5, '2025-12-10 07:41:20', NULL, 0, 0, 0, NULL, '2025-12-10 00:41:20'),
(139, 3, 41717, 29, '2025-12-10 07:41:36', NULL, 0, 0, 0, NULL, '2025-12-10 00:41:36'),
(140, 3, 41718, 29, '2025-12-10 07:41:53', NULL, 0, 0, 0, NULL, '2025-12-10 00:41:53'),
(141, 3, 41731, 6, '2025-12-10 07:41:59', NULL, 0, 0, 0, NULL, '2025-12-10 00:41:59'),
(142, 3, 41713, 31, '2025-12-10 07:42:10', NULL, 0, 0, 0, NULL, '2025-12-10 00:42:10'),
(143, 3, 41733, 5, '2025-12-10 07:42:28', NULL, 0, 0, 0, NULL, '2025-12-10 00:42:28'),
(144, 3, 41729, 5, '2025-12-10 07:42:33', NULL, 0, 0, 0, NULL, '2025-12-10 00:42:33'),
(145, 3, 1063, 3, '2025-12-10 07:42:51', NULL, 0, 0, 0, NULL, '2025-12-10 00:42:51'),
(146, 3, 1064, 3, '2025-12-10 07:42:53', NULL, 0, 0, 0, NULL, '2025-12-10 00:42:53'),
(147, 3, 1065, 3, '2025-12-10 07:42:54', NULL, 0, 0, 0, NULL, '2025-12-10 00:42:54'),
(148, 3, 1066, 3, '2025-12-10 07:42:55', NULL, 0, 0, 0, NULL, '2025-12-10 00:42:55'),
(149, 3, 1067, 3, '2025-12-10 07:42:56', NULL, 0, 0, 0, NULL, '2025-12-10 00:42:56'),
(150, 3, 1058, 1, '2025-12-10 07:43:11', NULL, 0, 0, 0, NULL, '2025-12-10 00:43:11'),
(151, 3, 1059, 1, '2025-12-10 07:43:13', NULL, 0, 0, 0, NULL, '2025-12-10 00:43:13'),
(152, 3, 1060, 1, '2025-12-10 07:43:15', NULL, 0, 0, 0, NULL, '2025-12-10 00:43:15'),
(153, 3, 1061, 1, '2025-12-10 07:43:16', NULL, 0, 0, 0, NULL, '2025-12-10 00:43:16'),
(154, 3, 1062, 1, '2025-12-10 07:43:18', NULL, 0, 0, 0, NULL, '2025-12-10 00:43:18'),
(155, 3, 1068, 5, '2025-12-10 07:43:26', NULL, 0, 0, 0, NULL, '2025-12-10 00:43:26'),
(156, 3, 1069, 5, '2025-12-10 07:43:28', NULL, 0, 0, 0, NULL, '2025-12-10 00:43:28'),
(157, 3, 1070, 5, '2025-12-10 07:43:29', NULL, 0, 0, 0, NULL, '2025-12-10 00:43:29'),
(158, 3, 1071, 5, '2025-12-10 07:43:30', NULL, 0, 0, 0, NULL, '2025-12-10 00:43:30'),
(159, 3, 1072, 5, '2025-12-10 07:43:31', NULL, 0, 0, 0, NULL, '2025-12-10 00:43:31'),
(160, 3, 41719, 29, '2025-12-10 07:44:39', NULL, 0, 0, 0, NULL, '2025-12-10 00:44:39'),
(161, 3, 41734, 27, '2025-12-10 07:44:42', '2025-12-14 21:22:50', 369488, 1, 0, NULL, '2025-12-10 00:44:42'),
(162, 9, 41735, 28, '2025-12-10 07:45:46', NULL, 0, 0, 0, NULL, '2025-12-10 00:45:46'),
(163, 9, 41736, 28, '2025-12-10 08:03:30', NULL, 0, 0, 0, NULL, '2025-12-10 01:03:30'),
(164, 9, 41737, 12, '2025-12-10 08:10:20', NULL, 0, 0, 0, NULL, '2025-12-10 01:10:20'),
(165, 9, 41739, 12, '2025-12-10 08:29:35', NULL, 0, 0, 0, NULL, '2025-12-10 01:29:35'),
(166, 9, 41731, 6, '2025-12-10 14:25:35', NULL, 0, 0, 0, NULL, '2025-12-10 07:25:35'),
(167, 9, 41740, 30, '2025-12-10 14:44:34', NULL, 0, 0, 0, NULL, '2025-12-10 07:44:34'),
(168, 9, 41713, 31, '2025-12-10 15:09:27', NULL, 0, 0, 0, NULL, '2025-12-10 08:09:27'),
(169, 9, 41714, 32, '2025-12-10 15:25:23', NULL, 0, 0, 0, NULL, '2025-12-10 08:25:23'),
(170, 10, 1008, 3, '2025-12-10 15:38:00', NULL, 0, 0, 0, NULL, '2025-12-10 08:38:00'),
(171, 9, 41746, 33, '2025-12-10 15:41:47', NULL, 0, 0, 0, NULL, '2025-12-10 08:41:47'),
(172, 9, 1000, 1, '2025-12-10 15:54:50', NULL, 0, 0, 0, NULL, '2025-12-10 08:54:50'),
(173, 22, 1104, 3, '2025-12-11 15:31:48', NULL, 0, 0, 0, NULL, '2025-12-11 08:31:48'),
(174, 22, 41728, 12, '2025-12-11 15:47:08', NULL, 0, 0, 0, NULL, '2025-12-11 08:47:08'),
(175, 9, 1092, 3, '2025-12-11 21:38:32', NULL, 0, 0, 0, NULL, '2025-12-11 14:38:32'),
(176, 9, 41756, 30, '2025-12-11 21:42:42', NULL, 0, 0, 0, NULL, '2025-12-11 14:42:42'),
(177, 10, 41758, 31, '2025-12-12 09:03:26', NULL, 0, 0, 0, NULL, '2025-12-12 02:03:26'),
(178, 9, 1104, 3, '2025-12-13 18:00:19', NULL, 0, 0, 0, NULL, '2025-12-13 11:00:19'),
(179, 9, 1105, 3, '2025-12-13 18:01:02', NULL, 0, 0, 0, NULL, '2025-12-13 11:01:02'),
(180, 9, 1106, 3, '2025-12-13 18:01:03', NULL, 0, 0, 0, NULL, '2025-12-13 11:01:03'),
(181, 9, 1107, 3, '2025-12-13 18:01:05', NULL, 0, 0, 0, NULL, '2025-12-13 11:01:05'),
(182, 9, 1117, 3, '2025-12-13 18:36:43', NULL, 0, 0, 0, NULL, '2025-12-13 11:36:43'),
(183, 9, 1118, 3, '2025-12-13 18:36:56', NULL, 0, 0, 0, NULL, '2025-12-13 11:36:56'),
(184, 9, 41761, 41, '2025-12-13 19:04:25', NULL, 0, 0, 0, NULL, '2025-12-13 12:04:25'),
(185, 3, 1104, 3, '2025-12-13 19:20:54', '2025-12-14 21:20:39', 68385, 1, 0, NULL, '2025-12-13 12:20:54'),
(186, 3, 1136, 3, '2025-12-14 21:20:51', NULL, 0, 0, 0, NULL, '2025-12-14 14:20:51'),
(187, 3, 41798, 5, '2025-12-14 21:22:58', NULL, 0, 0, 0, NULL, '2025-12-14 14:22:58'),
(188, 31, 41798, 5, '2025-12-14 21:45:28', NULL, 0, 0, 0, NULL, '2025-12-14 14:45:28'),
(189, 9, 41799, 6, '2025-12-14 21:54:33', NULL, 0, 0, 0, NULL, '2025-12-14 14:54:33'),
(190, 22, 41801, 30, '2025-12-15 10:03:15', NULL, 0, 0, 0, NULL, '2025-12-15 03:03:15'),
(191, 20, 41801, 30, '2025-12-15 10:03:33', NULL, 0, 0, 0, NULL, '2025-12-15 03:03:33'),
(192, 9, 41801, 30, '2025-12-15 10:12:04', NULL, 0, 0, 0, NULL, '2025-12-15 03:12:04'),
(193, 10, 41824, 3, '2025-12-28 01:23:32', NULL, 0, 0, 0, NULL, '2025-12-27 18:23:32'),
(194, 9, 41825, 5, '2025-12-29 21:36:22', NULL, 0, 0, 0, NULL, '2025-12-29 14:36:22'),
(195, 32, 41826, 5, '2026-01-27 20:17:41', NULL, 0, 0, 0, NULL, '2026-01-27 13:17:41'),
(196, 33, 41827, 5, '2026-01-27 20:47:36', NULL, 0, 0, 0, NULL, '2026-01-27 13:47:36'),
(197, 33, 41828, 5, '2026-01-28 00:25:26', NULL, 0, 0, 0, NULL, '2026-01-27 17:25:26'),
(198, 9, 41828, 5, '2026-01-29 10:11:27', NULL, 0, 0, 0, NULL, '2026-01-29 03:11:27'),
(199, 33, 41829, 5, '2026-02-03 09:08:36', NULL, 0, 0, 0, NULL, '2026-02-03 02:08:36'),
(200, 9, 41830, 5, '2026-02-04 14:10:00', NULL, 0, 0, 0, NULL, '2026-02-04 07:10:00'),
(202, 9, 41893, 3, '2026-06-09 15:40:44', NULL, 0, 0, 0, NULL, '2026-06-09 08:40:44'),
(203, 3, 41893, 3, '2026-06-09 15:56:14', NULL, 0, 0, 0, NULL, '2026-06-09 08:56:14');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `parent_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `parent_id`) VALUES
(1, 'Hành động', NULL),
(2, 'Tình cảm', NULL),
(3, 'Hài', NULL),
(4, 'Kinh dị', NULL),
(5, 'Hoạt hình', NULL),
(6, 'Khoa học viễn tưởng', NULL),
(7, 'Phiêu lưu', NULL),
(8, 'Tài liệu', NULL),
(9, 'Chiến tranh', NULL),
(10, 'Thể thao', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `content` text NOT NULL,
  `status` enum('pending','approved','rejected','spam') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `likes` int(11) DEFAULT 0,
  `dislikes` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `comments`
--

INSERT INTO `comments` (`id`, `user_id`, `movie_id`, `parent_id`, `content`, `status`, `created_at`, `updated_at`, `likes`, `dislikes`) VALUES
(1, 1, 1, NULL, 'Phim này thật sự đáng xem!', 'approved', '2025-11-12 07:41:09', '2025-11-12 07:41:09', 0, 0),
(2, 2, 2, NULL, 'Cảm động quá, tôi đã khóc.', 'approved', '2025-11-12 07:41:09', '2025-11-12 07:41:09', 0, 0),
(3, 3, 3, NULL, 'Hài quá, cười không ngừng.', 'approved', '2025-11-12 07:41:09', '2025-11-12 07:41:09', 0, 0),
(4, 4, 4, NULL, 'Sợ quá, không dám xem một mình.', 'approved', '2025-11-12 07:41:09', '2025-11-12 07:41:09', 0, 0),
(5, 5, 5, NULL, 'Phim hay cho trẻ em.', 'approved', '2025-11-12 07:41:09', '2025-11-12 07:41:09', 0, 0),
(6, 1, 6, NULL, 'Khoa học viễn tưởng đỉnh cao!', 'approved', '2025-11-12 07:41:09', '2025-11-12 07:41:09', 0, 0),
(7, 2, 7, NULL, 'Cuộc phiêu lưu thú vị.', 'approved', '2025-11-12 07:41:09', '2025-11-12 07:41:09', 0, 0),
(8, 9, 35, NULL, 'ok', 'approved', '2025-12-04 20:12:42', '2025-12-04 20:13:07', 1, 0),
(9, 21, 33, NULL, 'Nguyễn Văn Linh sợ vl', 'approved', '2025-12-09 01:34:05', '2025-12-09 01:34:05', 0, 0),
(10, 9, 33, 9, 'ok', 'approved', '2025-12-09 01:56:22', '2025-12-09 01:57:09', 1, 0),
(11, 9, 33, NULL, 'má nó mắc cười ghê đó hahaa', 'approved', '2025-12-09 01:59:21', '2025-12-09 01:59:21', 0, 0),
(12, 22, 33, 11, 'ok', 'approved', '2025-12-09 01:59:39', '2025-12-09 01:59:39', 0, 0),
(13, 26, 8, NULL, 'Trả tiền đây mb: 0961182130\r\nCHUNG VAN DAT', 'approved', '2025-12-09 03:43:54', '2025-12-09 03:46:20', 1, 0),
(14, 9, 8, 13, 'cut me may di', 'approved', '2025-12-09 03:44:16', '2025-12-09 03:44:16', 0, 0),
(15, 29, 28, NULL, 'Wao', 'approved', '2025-12-11 08:56:34', '2025-12-11 08:56:34', 0, 0),
(16, 3, 53, NULL, 'Hay quá', 'approved', '2026-02-09 03:08:09', '2026-02-09 03:08:09', 0, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `comment_likes`
--

CREATE TABLE `comment_likes` (
  `id` int(11) NOT NULL,
  `comment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('like','dislike') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `comment_likes`
--

INSERT INTO `comment_likes` (`id`, `comment_id`, `user_id`, `type`, `created_at`) VALUES
(2, 8, 9, 'like', '2025-12-04 20:13:07'),
(3, 10, 3, 'like', '2025-12-09 01:57:09'),
(5, 13, 9, 'like', '2025-12-09 03:46:20');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('percentage','fixed') NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `min_amount` decimal(10,2) DEFAULT 0.00,
  `max_discount` decimal(10,2) DEFAULT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `used_count` int(11) DEFAULT 0,
  `valid_from` datetime NOT NULL,
  `valid_to` datetime NOT NULL,
  `status` enum('active','inactive','expired') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `name`, `type`, `value`, `min_amount`, `max_discount`, `usage_limit`, `used_count`, `valid_from`, `valid_to`, `status`, `created_at`) VALUES
(1, 'WELCOME10', 'Giảm 10% cho khách hàng mới', 'percentage', 10.00, 50000.00, 50000.00, 100, 0, '2025-11-01 00:00:00', '2025-12-31 23:59:59', 'active', '2025-11-12 07:41:09'),
(2, 'SAVE50K', 'Giảm 50.000đ', 'fixed', 50000.00, 200000.00, NULL, 200, 0, '2025-11-01 00:00:00', '2025-12-31 23:59:59', 'active', '2025-11-12 07:41:09'),
(3, 'VIP20', 'Giảm 20% cho thành viên VIP', 'percentage', 20.00, 100000.00, 100000.00, 50, 0, '2025-11-01 00:00:00', '2025-12-31 23:59:59', 'active', '2025-11-12 07:41:09'),
(4, 'FLASH30', 'Giảm 30% trong ngày', 'percentage', 30.00, 150000.00, 150000.00, 30, 0, '2025-11-15 00:00:00', '2025-11-15 23:59:59', 'active', '2025-11-12 07:41:09'),
(5, 'NEWUSER', 'Giảm 25.000đ cho người dùng mới', 'fixed', 25000.00, 100000.00, NULL, 500, 0, '2025-11-01 00:00:00', '2026-01-31 23:59:59', 'active', '2025-11-12 07:41:09');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `episodes`
--

CREATE TABLE `episodes` (
  `id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `episode_number` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `episodes`
--

INSERT INTO `episodes` (`id`, `movie_id`, `episode_number`, `title`, `video_url`, `thumbnail`, `duration`, `description`, `created_at`, `updated_at`) VALUES
(1, 8, 1, 'Tập 1', 'data/phim/phimbo/gameofthrones', NULL, NULL, NULL, '2025-11-19 02:21:55', '2025-11-19 02:21:55'),
(2, 8, 2, 'Tập 2', 'data/phim/phimbo/gameofthrones', NULL, NULL, NULL, '2025-11-19 02:21:55', '2025-11-19 02:21:55'),
(3, 8, 3, 'Tập 3', 'data/phim/phimbo/gameofthrones', NULL, NULL, NULL, '2025-11-19 02:21:55', '2025-11-19 02:21:55'),
(4, 8, 4, 'Tập 4', 'data/phim/phimbo/gameofthrones', NULL, NULL, NULL, '2025-11-19 02:21:55', '2025-11-19 02:21:55'),
(5, 29, 1, 'tập 1', 'data/phim/phimbo/venhadicon', NULL, NULL, NULL, '2025-11-25 03:19:46', '2025-11-24 03:28:37'),
(12, 28, 1, '', NULL, NULL, NULL, NULL, '2025-12-13 13:43:19', '2025-12-13 13:43:19'),
(13, 51, 1, '', 'data/phim/phimbo/phamnhantutien/tap_1.mp4', NULL, NULL, NULL, '2025-12-15 01:40:15', '2026-06-16 01:15:33'),
(14, 51, 2, '', 'data/phim/phimbo/movie_51_episode_2_1765762844.mp4', NULL, NULL, NULL, '2025-12-15 01:40:15', '2025-12-15 01:40:44'),
(15, 9, 1, '', 'data/phim/phimbo/movie_9_episode_1_1765766625.mp4', NULL, NULL, NULL, '2025-12-15 02:43:24', '2025-12-15 02:43:45'),
(16, 9, 2, '', 'data/phim/phimbo/movie_9_episode_2_1765766625.mp4', NULL, NULL, NULL, '2025-12-15 02:43:24', '2025-12-15 02:43:46'),
(17, 9, 3, '', 'data/phim/phimbo/movie_9_episode_3_1765766626.mp4', NULL, NULL, NULL, '2025-12-15 02:43:24', '2025-12-15 02:43:46'),
(18, 9, 4, '', 'data/phim/phimbo/movie_9_episode_4_1765766626.mp4', NULL, NULL, NULL, '2025-12-15 02:43:24', '2025-12-15 02:43:46'),
(19, 53, 1, '', 'data/phim/phimbo/movie_53_episode_1_1770606446.mp4', NULL, NULL, NULL, '2026-02-09 03:06:41', '2026-02-09 03:07:26'),
(20, 53, 2, '', NULL, NULL, NULL, NULL, '2026-02-09 03:07:26', '2026-02-09 03:07:26'),
(21, 53, 3, '', NULL, NULL, NULL, NULL, '2026-02-09 03:07:26', '2026-02-09 03:07:26'),
(22, 53, 4, '', NULL, NULL, NULL, NULL, '2026-02-09 03:07:26', '2026-02-09 03:07:26'),
(23, 53, 5, '', NULL, NULL, NULL, NULL, '2026-02-09 03:07:26', '2026-02-09 03:07:26'),
(24, 53, 6, '', NULL, NULL, NULL, NULL, '2026-02-09 03:07:26', '2026-02-09 03:07:26');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `food_items`
--

CREATE TABLE `food_items` (
  `id` int(11) NOT NULL,
  `theater_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `type` enum('combo','snack','drink') DEFAULT 'combo',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `food_items`
--

INSERT INTO `food_items` (`id`, `theater_id`, `name`, `description`, `price`, `image`, `type`, `is_active`, `created_at`) VALUES
(1, 1, 'Combo 1 - Bỏng + Nước', '1 bỏng ngô lớn + 1 nước ngọt lớn', 85000.00, NULL, 'combo', 1, '2025-11-28 01:03:19'),
(2, 1, 'Combo 2 - Bỏng + Nước + Snack', '1 bỏng ngô lớn + 1 nước ngọt lớn + 1 snack', 120000.00, NULL, 'combo', 1, '2025-11-28 01:03:19'),
(3, 1, 'Combo 3 - Đôi', '2 bỏng ngô lớn + 2 nước ngọt lớn', 150000.00, NULL, 'combo', 1, '2025-11-28 01:03:19'),
(4, 1, 'Bỏng ngô lớn', 'Bỏng ngô size lớn', 55000.00, NULL, 'snack', 1, '2025-11-28 01:03:19'),
(5, 1, 'Bỏng ngô vừa', 'Bỏng ngô size vừa', 40000.00, NULL, 'snack', 1, '2025-11-28 01:03:19'),
(6, 1, 'Nước ngọt lớn', 'Nước ngọt size lớn', 35000.00, NULL, 'drink', 1, '2025-11-28 01:03:19'),
(7, 1, 'Nước ngọt vừa', 'Nước ngọt size vừa', 25000.00, NULL, 'drink', 1, '2025-11-28 01:03:19'),
(8, 1, 'Snack mix', 'Hỗn hợp snack', 45000.00, NULL, 'snack', 1, '2025-11-28 01:03:19'),
(9, 2, 'Combo Premium', '1 bỏng ngô lớn + 1 nước ngọt lớn + 1 hotdog', 140000.00, NULL, 'combo', 1, '2025-11-28 01:03:19'),
(10, 2, 'Combo Family', '2 bỏng ngô lớn + 2 nước ngọt lớn + 2 snack', 250000.00, NULL, 'combo', 1, '2025-11-28 01:03:19'),
(11, 2, 'Combo Sweet', '1 bỏng ngô lớn + 1 nước ngọt lớn + 1 bánh ngọt', 110000.00, NULL, 'combo', 1, '2025-11-28 01:03:19'),
(12, 2, 'Bỏng ngô caramel', 'Bỏng ngô vị caramel size lớn', 65000.00, NULL, 'snack', 1, '2025-11-28 01:03:19'),
(13, 2, 'Bỏng ngô phô mai', 'Bỏng ngô vị phô mai size lớn', 65000.00, NULL, 'snack', 1, '2025-11-28 01:03:19'),
(14, 2, 'Hotdog', 'Hotdog thịt bò', 75000.00, NULL, 'snack', 1, '2025-11-28 01:03:19'),
(15, 2, 'Nước ép trái cây', 'Nước ép trái cây tươi', 45000.00, NULL, 'drink', 1, '2025-11-28 01:03:19'),
(16, 2, 'Cà phê đá', 'Cà phê đá phin', 40000.00, NULL, 'drink', 1, '2025-11-28 01:03:19'),
(17, 3, 'Combo VIP', '1 bỏng ngô lớn + 1 nước ngọt lớn + 1 snack + 1 bánh ngọt', 160000.00, NULL, 'combo', 1, '2025-11-28 01:03:19'),
(18, 3, 'Combo Couple', '2 bỏng ngô lớn + 2 nước ngọt lớn + 1 snack lớn', 200000.00, NULL, 'combo', 1, '2025-11-28 01:03:19'),
(19, 3, 'Combo Kids', '1 bỏng ngô vừa + 1 nước ngọt vừa + 1 kẹo', 90000.00, NULL, 'combo', 1, '2025-11-28 01:03:19'),
(20, 3, 'Bỏng ngô bơ', 'Bỏng ngô vị bơ size lớn', 60000.00, NULL, 'snack', 1, '2025-11-28 01:03:19'),
(21, 3, 'Khoai tây chiên', 'Khoai tây chiên giòn', 50000.00, NULL, 'snack', 1, '2025-11-28 01:03:19'),
(22, 3, 'Bánh mì sandwich', 'Bánh mì sandwich thịt nguội', 70000.00, NULL, 'snack', 1, '2025-11-28 01:03:19'),
(23, 3, 'Trà sữa', 'Trà sữa thái xanh', 55000.00, NULL, 'drink', 1, '2025-11-28 01:03:19'),
(24, 3, 'Nước lọc', 'Nước lọc tinh khiết', 20000.00, NULL, 'drink', 1, '2025-11-28 01:03:19'),
(25, 4, 'Combo Galaxy', '1 bỏng ngô lớn + 1 nước ngọt lớn + 1 snack + 1 bánh quy', 130000.00, NULL, 'combo', 1, '2025-11-28 01:03:19'),
(26, 4, 'Combo Star', '2 bỏng ngô lớn + 2 nước ngọt lớn + 2 snack', 220000.00, NULL, 'combo', 1, '2025-11-28 01:03:19'),
(27, 4, 'Combo Solo', '1 bỏng ngô vừa + 1 nước ngọt vừa', 70000.00, NULL, 'combo', 1, '2025-11-28 01:03:19'),
(28, 4, 'Bỏng ngô mật ong', 'Bỏng ngô vị mật ong size lớn', 60000.00, NULL, 'snack', 1, '2025-11-28 01:03:19'),
(29, 4, 'Bánh quy giòn', 'Bánh quy giòn thơm ngon', 35000.00, NULL, 'snack', 1, '2025-11-28 01:03:19'),
(30, 4, 'Bánh ngọt', 'Bánh ngọt kem tươi', 60000.00, NULL, 'snack', 1, '2025-11-28 01:03:19'),
(31, 4, 'Soda chanh', 'Soda chanh mát lạnh', 40000.00, NULL, 'drink', 1, '2025-11-28 01:03:19'),
(32, 4, 'Nước cam ép', 'Nước cam ép tươi', 50000.00, NULL, 'drink', 1, '2025-11-28 01:03:19'),
(33, 5, 'Combo Star', '1 bỏng ngô lớn + 1 nước ngọt lớn + 1 hotdog + 1 snack', 170000.00, NULL, 'combo', 1, '2025-11-28 01:03:19'),
(34, 5, 'Combo Deluxe', '2 bỏng ngô lớn + 2 nước ngọt lớn + 2 hotdog', 280000.00, NULL, 'combo', 1, '2025-11-28 01:03:19'),
(35, 5, 'Combo Classic', '1 bỏng ngô lớn + 1 nước ngọt lớn', 95000.00, NULL, 'combo', 1, '2025-11-28 01:03:19'),
(36, 5, 'Bỏng ngô socola', 'Bỏng ngô vị socola size lớn', 70000.00, NULL, 'snack', 1, '2025-11-28 01:03:19'),
(37, 5, 'Hotdog phô mai', 'Hotdog phô mai thơm ngon', 85000.00, NULL, 'snack', 1, '2025-11-28 01:03:19'),
(38, 5, 'Bánh pizza mini', 'Bánh pizza mini 4 miếng', 120000.00, NULL, 'snack', 1, '2025-11-28 01:03:19'),
(39, 5, 'Coca Cola', 'Coca Cola size lớn', 40000.00, NULL, 'drink', 1, '2025-11-28 01:03:19'),
(40, 5, 'Pepsi', 'Pepsi size lớn', 40000.00, NULL, 'drink', 1, '2025-11-28 01:03:19');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ip_blocks`
--

CREATE TABLE `ip_blocks` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `expires_at` datetime DEFAULT NULL COMMENT 'NULL = chặn vĩnh viễn',
  `reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ip_room_tracking`
--

CREATE TABLE `ip_room_tracking` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `screen_id` int(11) NOT NULL,
  `showtime_id` int(11) NOT NULL,
  `first_enter_time` datetime NOT NULL COMMENT 'Thời gian lần đầu vào phòng',
  `last_enter_time` datetime NOT NULL COMMENT 'Thời gian lần cuối vào phòng',
  `total_duration_seconds` int(11) DEFAULT 0 COMMENT 'Tổng thời gian đã ở trong phòng (giây)',
  `is_banned` tinyint(1) DEFAULT 0 COMMENT 'Có bị cấm không',
  `ban_until` datetime DEFAULT NULL COMMENT 'Cấm đến khi nào (thời gian phim chiếu)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `ip_room_tracking`
--

INSERT INTO `ip_room_tracking` (`id`, `ip_address`, `screen_id`, `showtime_id`, `first_enter_time`, `last_enter_time`, `total_duration_seconds`, `is_banned`, `ban_until`, `created_at`, `updated_at`) VALUES
(17, '42.118.61.65', 0, 498, '2025-12-04 04:50:46', '2025-12-04 04:50:46', 0, 0, NULL, '2025-12-04 03:50:46', '2025-12-04 03:50:46'),
(18, '42.118.61.65', 0, 368, '2025-12-04 05:01:48', '2025-12-04 05:07:21', 333, 0, NULL, '2025-12-04 04:01:48', '2025-12-04 04:07:21'),
(20, '42.118.61.65', 4, 327, '2025-12-04 05:11:42', '2025-12-04 05:12:00', 18, 0, NULL, '2025-12-04 04:11:42', '2025-12-04 04:12:00'),
(22, '183.81.104.232', 4, 335, '2025-12-04 14:34:05', '2025-12-04 13:45:13', -2932, 0, NULL, '2025-12-04 13:34:05', '2025-12-04 13:45:13'),
(23, '171.255.56.31', 4, 335, '2025-12-04 13:41:58', '2025-12-04 13:51:20', 562, 0, NULL, '2025-12-04 13:41:58', '2025-12-04 13:51:20'),
(24, '183.81.104.232', 4, 341, '2025-12-04 13:53:30', '2025-12-04 13:57:34', 244, 0, NULL, '2025-12-04 13:53:30', '2025-12-04 13:57:34'),
(25, '171.255.56.31', 4, 341, '2025-12-04 13:53:33', '2025-12-04 13:54:58', 85, 0, NULL, '2025-12-04 13:53:33', '2025-12-04 13:54:58'),
(26, '171.255.56.31', 6, 336, '2025-12-04 16:13:13', '2025-12-04 16:28:09', 1197, 1, '2025-12-07 16:00:00', '2025-12-04 16:13:13', '2025-12-04 16:33:10'),
(27, '103.156.42.71', 2, 333, '2025-12-04 16:17:13', '2025-12-04 16:17:13', 0, 0, NULL, '2025-12-04 16:17:13', '2025-12-04 16:17:13'),
(28, '171.255.56.31', 2, 333, '2025-12-04 16:27:13', '2025-12-04 16:39:54', 1454, 1, '2025-12-07 17:00:00', '2025-12-04 16:27:13', '2025-12-04 16:51:27'),
(29, '171.255.56.31', 15, 565, '2025-12-04 16:33:29', '2025-12-04 16:33:29', 0, 0, NULL, '2025-12-04 16:33:29', '2025-12-04 16:33:29'),
(30, '171.255.56.31', 26, 622, '2025-12-04 16:33:48', '2025-12-04 16:33:48', 0, 0, NULL, '2025-12-04 16:33:48', '2025-12-04 16:33:48'),
(31, '171.255.56.31', 21, 448, '2025-12-04 16:34:03', '2025-12-04 16:34:03', 0, 0, NULL, '2025-12-04 16:34:03', '2025-12-04 16:34:03'),
(32, '171.255.56.31', 0, 630, '2025-12-04 16:34:21', '2025-12-04 16:42:20', 1098, 1, '2025-12-07 04:00:00', '2025-12-04 16:34:21', '2025-12-04 16:52:39'),
(33, '171.255.56.31', 27, 674, '2025-12-04 16:42:43', '2025-12-04 16:42:43', 0, 0, NULL, '2025-12-04 16:42:43', '2025-12-04 16:42:43'),
(34, '171.255.56.31', 0, 654, '2025-12-04 16:42:49', '2025-12-04 16:42:49', 0, 0, NULL, '2025-12-04 16:42:49', '2025-12-04 16:42:49'),
(35, '171.255.56.31', 28, 669, '2025-12-04 16:42:52', '2025-12-04 16:42:52', 0, 0, NULL, '2025-12-04 16:42:52', '2025-12-04 16:42:52'),
(36, '171.255.56.31', 28, 675, '2025-12-04 16:42:56', '2025-12-04 16:42:56', 0, 0, NULL, '2025-12-04 16:42:56', '2025-12-04 16:42:56'),
(37, '171.255.56.31', 6, 683, '2025-12-04 16:42:59', '2025-12-04 16:43:06', 7, 0, NULL, '2025-12-04 16:42:59', '2025-12-04 16:43:06'),
(38, '171.255.56.31', 29, 688, '2025-12-04 16:43:03', '2025-12-04 16:53:28', 1190, 1, '2025-12-07 21:00:00', '2025-12-04 16:43:03', '2025-12-04 17:02:53'),
(39, '171.255.56.31', 6, 641, '2025-12-04 16:43:11', '2025-12-04 16:53:38', 627, 0, NULL, '2025-12-04 16:43:11', '2025-12-04 16:53:38'),
(40, '171.255.56.31', 0, 624, '2025-12-04 16:43:18', '2025-12-04 16:43:18', 0, 0, NULL, '2025-12-04 16:43:18', '2025-12-04 16:43:18'),
(41, '171.255.56.31', 27, 626, '2025-12-04 16:43:24', '2025-12-04 16:43:24', 0, 0, NULL, '2025-12-04 16:43:24', '2025-12-04 16:43:24'),
(42, '171.255.56.31', 6, 671, '2025-12-04 16:43:32', '2025-12-04 16:43:32', 0, 0, NULL, '2025-12-04 16:43:32', '2025-12-04 16:43:32'),
(43, '171.255.56.31', 7, 77, '2025-12-04 16:51:37', '2025-12-04 16:51:37', 0, 0, NULL, '2025-12-04 16:51:37', '2025-12-04 16:51:37'),
(44, '171.255.56.31', 27, 632, '2025-12-04 16:51:48', '2025-12-04 17:02:39', 651, 0, NULL, '2025-12-04 16:51:48', '2025-12-04 17:02:39'),
(45, '171.255.56.31', 28, 639, '2025-12-04 16:52:51', '2025-12-04 17:04:05', 674, 0, NULL, '2025-12-04 16:52:51', '2025-12-04 17:04:05'),
(46, '171.255.56.31', 28, 687, '2025-12-04 16:53:10', '2025-12-04 16:53:10', 0, 0, NULL, '2025-12-04 16:53:10', '2025-12-04 16:53:10'),
(47, '171.255.56.31', 6, 677, '2025-12-04 16:53:15', '2025-12-04 16:53:15', 0, 0, NULL, '2025-12-04 16:53:15', '2025-12-04 16:53:15'),
(48, '171.255.56.31', 27, 680, '2025-12-04 16:53:33', '2025-12-04 16:53:33', 0, 0, NULL, '2025-12-04 16:53:33', '2025-12-04 16:53:33'),
(49, '171.255.56.31', 28, 663, '2025-12-04 16:53:44', '2025-12-04 16:53:44', 0, 0, NULL, '2025-12-04 16:53:44', '2025-12-04 16:53:44'),
(50, '171.255.56.31', 0, 649, '2025-12-04 16:53:49', '2025-12-04 16:53:49', 3375, 1, '2025-12-07 14:30:00', '2025-12-04 16:53:49', '2025-12-04 17:50:04'),
(51, '171.255.56.31', 0, 637, '2025-12-04 17:03:04', '2025-12-04 17:04:49', 941, 1, '2025-12-07 10:00:00', '2025-12-04 17:03:04', '2025-12-04 17:18:45'),
(52, '171.255.56.31', 0, 625, '2025-12-04 17:20:54', '2025-12-04 17:33:37', 763, 0, NULL, '2025-12-04 17:20:54', '2025-12-04 17:33:37'),
(53, '171.255.56.31', 1, 331, '2025-12-04 17:21:37', '2025-12-04 17:29:47', 1054, 1, '2025-12-07 14:00:00', '2025-12-04 17:21:37', '2025-12-04 17:39:11'),
(54, '171.255.56.31', 29, 640, '2025-12-04 17:43:08', '2025-12-04 17:43:08', 0, 0, NULL, '2025-12-04 17:43:08', '2025-12-04 17:43:08'),
(55, '171.255.56.31', 29, 676, '2025-12-04 17:43:34', '2025-12-04 17:43:34', 0, 0, NULL, '2025-12-04 17:43:34', '2025-12-04 17:43:34'),
(56, '171.255.56.31', 0, 685, '2025-12-04 17:43:39', '2025-12-04 17:56:48', 1238, 1, '2025-12-07 21:00:00', '2025-12-04 17:43:39', '2025-12-04 18:04:17'),
(57, '171.255.56.31', 29, 652, '2025-12-04 17:50:13', '2025-12-04 17:50:13', 0, 0, NULL, '2025-12-04 17:50:13', '2025-12-04 17:50:13'),
(58, '103.156.42.51', 29, 652, '2025-12-04 17:52:56', '2025-12-04 17:52:56', 0, 0, NULL, '2025-12-04 17:52:56', '2025-12-04 17:52:56'),
(59, '103.156.42.51', 29, 688, '2025-12-04 17:53:17', '2025-12-04 17:53:38', 21, 0, NULL, '2025-12-04 17:53:17', '2025-12-04 17:53:38'),
(60, '103.156.42.51', 27, 686, '2025-12-04 17:53:19', '2025-12-04 17:53:19', 0, 0, NULL, '2025-12-04 17:53:19', '2025-12-04 17:53:19'),
(61, '103.156.42.51', 0, 684, '2025-12-04 17:53:21', '2025-12-04 17:53:27', 6, 0, NULL, '2025-12-04 17:53:21', '2025-12-04 17:53:27'),
(62, '103.156.42.51', 6, 683, '2025-12-04 17:53:25', '2025-12-04 17:53:30', 5, 0, NULL, '2025-12-04 17:53:25', '2025-12-04 17:53:30'),
(63, '103.156.42.51', 0, 685, '2025-12-04 17:53:34', '2025-12-04 17:53:34', 0, 0, NULL, '2025-12-04 17:53:34', '2025-12-04 17:53:34'),
(64, '103.156.42.51', 29, 682, '2025-12-04 17:53:40', '2025-12-04 17:53:50', NULL, 0, NULL, '2025-12-04 17:53:40', '2025-12-04 17:53:50'),
(65, '103.156.42.51', 6, 677, '2025-12-04 17:53:43', '2025-12-04 17:53:54', NULL, 0, NULL, '2025-12-04 17:53:43', '2025-12-04 17:53:54'),
(66, '103.156.42.51', 27, 680, '2025-12-04 17:53:45', '2025-12-04 17:53:45', 0, 0, NULL, '2025-12-04 17:53:45', '2025-12-04 17:53:45'),
(67, '103.156.42.51', 0, 678, '2025-12-04 17:53:48', '2025-12-04 17:53:48', 0, 0, NULL, '2025-12-04 17:53:48', '2025-12-04 17:53:48'),
(68, '103.156.42.51', 29, 676, '2025-12-04 17:54:00', '2025-12-04 17:54:00', 0, 0, NULL, '2025-12-04 17:54:00', '2025-12-04 17:54:00'),
(69, '103.156.42.51', 28, 675, '2025-12-04 17:54:01', '2025-12-04 17:54:01', 0, 0, NULL, '2025-12-04 17:54:01', '2025-12-04 17:54:01'),
(70, '103.156.42.51', 0, 673, '2025-12-04 17:54:04', '2025-12-04 17:54:24', 20, 0, NULL, '2025-12-04 17:54:04', '2025-12-04 17:54:24'),
(71, '103.156.42.51', 6, 671, '2025-12-04 17:54:05', '2025-12-04 17:54:05', 0, 0, NULL, '2025-12-04 17:54:05', '2025-12-04 17:54:05'),
(72, '103.156.42.51', 27, 674, '2025-12-04 17:54:22', '2025-12-04 18:05:16', 654, 0, NULL, '2025-12-04 17:54:22', '2025-12-04 18:05:16'),
(73, '171.255.56.31', 28, 651, '2025-12-04 18:04:32', '2025-12-04 18:04:32', 0, 0, NULL, '2025-12-04 18:04:32', '2025-12-04 18:04:32'),
(74, '171.255.56.31', 6, 1003, '2025-12-04 18:05:33', '2025-12-04 18:05:33', 0, 0, NULL, '2025-12-04 18:05:33', '2025-12-04 18:05:33'),
(75, '171.255.56.31', 29, 646, '2025-12-04 18:05:59', '2025-12-04 18:05:59', 0, 0, NULL, '2025-12-04 18:05:59', '2025-12-04 18:05:59'),
(76, '103.156.42.30', 2, 333, '2025-12-04 18:23:42', '2025-12-04 18:24:56', 74, 0, NULL, '2025-12-04 18:23:42', '2025-12-04 18:24:56'),
(77, '103.156.42.30', 3, 0, '2025-12-04 18:31:22', '2025-12-04 18:35:41', 259, 0, NULL, '2025-12-04 18:31:22', '2025-12-04 18:35:41'),
(78, '103.156.42.30', 6, 336, '2025-12-04 18:37:54', '2025-12-04 18:39:25', 91, 0, NULL, '2025-12-04 18:37:54', '2025-12-04 18:39:25'),
(79, '116.97.107.37', 5, 3941, '2025-12-06 15:00:16', '2025-12-06 15:00:31', 15, 0, NULL, '2025-12-06 15:00:16', '2025-12-06 15:00:31'),
(80, '116.97.107.37', 29, 22381, '2025-12-06 15:00:41', '2025-12-06 15:00:51', NULL, 0, NULL, '2025-12-06 15:00:41', '2025-12-06 15:00:51'),
(81, '116.97.107.37', 5, 3961, '2025-12-06 15:22:21', '2025-12-06 15:23:30', NULL, 0, NULL, '2025-12-06 15:22:21', '2025-12-06 15:23:30'),
(82, '116.97.107.37', 5, 3985, '2025-12-06 16:21:57', '2025-12-06 16:22:46', 49, 0, NULL, '2025-12-06 16:21:57', '2025-12-06 16:22:46'),
(83, '14.170.222.119', 3, 2349, '2025-12-06 17:20:01', '2025-12-06 17:20:33', 32, 0, NULL, '2025-12-06 17:20:01', '2025-12-06 17:20:33'),
(84, '42.118.61.65', 17, 33532, '2025-12-08 04:04:49', '2025-12-08 04:05:25', 36, 0, NULL, '2025-12-08 04:04:49', '2025-12-08 04:05:25'),
(85, '117.2.114.182', 31, 41713, '2025-12-08 19:01:50', '2025-12-08 19:03:06', 76, 0, NULL, '2025-12-08 19:01:50', '2025-12-08 19:03:06'),
(86, '117.2.114.182', 3, 1008, '2025-12-08 19:03:56', '2025-12-08 19:04:25', 29, 0, NULL, '2025-12-08 19:03:56', '2025-12-08 19:04:25'),
(87, '117.2.114.182', 3, 1009, '2025-12-08 19:15:27', '2025-12-08 19:17:15', 108, 0, NULL, '2025-12-08 19:15:27', '2025-12-08 19:17:15'),
(88, '42.118.61.65', 3, 1010, '2025-12-09 00:29:45', '2025-12-10 08:54:38', 0, 0, NULL, '2025-12-09 00:29:45', '2025-12-10 08:54:38'),
(89, '117.2.114.199', 3, 1010, '2025-12-09 00:32:15', '2025-12-09 00:32:15', 0, 0, NULL, '2025-12-09 00:32:15', '2025-12-09 00:32:15'),
(90, '117.2.114.199', 31, 41716, '2025-12-09 00:46:07', '2025-12-09 00:47:24', 77, 0, NULL, '2025-12-09 00:46:07', '2025-12-09 00:47:24'),
(91, '203.113.133.232', 3, 1008, '2025-12-09 02:33:28', '2025-12-09 02:33:51', 23, 0, NULL, '2025-12-09 02:33:28', '2025-12-09 02:33:51'),
(92, '203.113.133.232', 3, 1009, '2025-12-09 02:33:36', '2025-12-09 02:34:01', 25, 0, NULL, '2025-12-09 02:33:36', '2025-12-09 02:34:01'),
(93, '203.113.133.232', 3, 1010, '2025-12-09 02:34:03', '2025-12-09 02:44:17', 1274, 1, '2025-12-10 18:00:00', '2025-12-09 02:34:03', '2025-12-09 02:55:17'),
(94, '42.118.61.65', 27, 41726, '2025-12-09 03:34:01', '2025-12-09 03:38:30', 269, 0, NULL, '2025-12-09 03:34:01', '2025-12-09 03:38:30'),
(95, '42.118.61.65', 27, 41725, '2025-12-09 03:38:06', '2025-12-09 04:55:08', 25, 0, NULL, '2025-12-09 03:38:06', '2025-12-09 04:55:08'),
(96, '42.118.61.65', 12, 41727, '2025-12-09 04:55:10', '2025-12-09 04:56:46', 96, 0, NULL, '2025-12-09 04:55:10', '2025-12-09 04:56:46'),
(97, '59.153.231.212', 12, 41727, '2025-12-09 04:56:02', '2025-12-09 05:01:02', 300, 0, NULL, '2025-12-09 04:56:02', '2025-12-09 05:01:02'),
(98, '59.153.231.212', 27, 41726, '2025-12-09 04:58:24', '2025-12-09 04:58:24', 0, 0, NULL, '2025-12-09 04:58:24', '2025-12-09 04:58:24'),
(99, '1.55.108.99', 2, 1004, '2025-12-09 11:34:50', '2025-12-09 11:37:04', 134, 0, NULL, '2025-12-09 11:34:50', '2025-12-09 11:37:04'),
(100, '1.55.108.99', 1, 1001, '2025-12-09 11:37:23', '2025-12-09 11:50:39', 902, 1, '2025-12-10 13:00:00', '2025-12-09 11:37:23', '2025-12-09 11:52:25'),
(101, '1.55.108.99', 2, 1005, '2025-12-09 11:46:27', '2025-12-09 11:54:01', 454, 0, NULL, '2025-12-09 11:46:27', '2025-12-09 11:54:01'),
(102, '103.156.42.11', 31, 41713, '2025-12-09 15:15:31', '2025-12-09 15:15:31', 0, 0, NULL, '2025-12-09 15:15:31', '2025-12-09 15:15:31'),
(103, '103.156.42.11', 31, 41716, '2025-12-09 15:15:42', '2025-12-09 15:15:42', 1905, 1, '2025-12-10 20:00:00', '2025-12-09 15:15:42', '2025-12-09 15:47:27'),
(104, '103.156.42.11', 12, 41728, '2025-12-09 15:17:00', '2025-12-09 15:25:54', 534, 0, NULL, '2025-12-09 15:17:00', '2025-12-09 15:25:54'),
(105, '116.97.107.168', 12, 41728, '2025-12-09 15:17:57', '2025-12-09 15:22:36', 279, 0, NULL, '2025-12-09 15:17:57', '2025-12-09 15:22:36'),
(106, '59.153.224.177', 12, 41728, '2025-12-09 15:18:00', '2025-12-09 15:18:00', 0, 0, NULL, '2025-12-09 15:18:00', '2025-12-09 15:18:00'),
(107, '103.156.42.11', 3, 1008, '2025-12-09 15:24:27', '2025-12-09 15:24:27', 0, 0, NULL, '2025-12-09 15:24:27', '2025-12-09 15:24:27'),
(108, '103.156.42.11', 1, 1073, '2025-12-09 15:24:58', '2025-12-09 15:25:05', 7, 0, NULL, '2025-12-09 15:24:58', '2025-12-09 15:25:05'),
(109, '103.156.42.11', 1, 1074, '2025-12-09 15:25:09', '2025-12-09 15:25:16', 7, 0, NULL, '2025-12-09 15:25:09', '2025-12-09 15:25:16'),
(110, '116.97.107.168', 5, 41729, '2025-12-09 15:26:46', '2025-12-09 15:26:56', 10, 0, NULL, '2025-12-09 15:26:46', '2025-12-09 15:26:56'),
(111, '103.156.42.11', 5, 41729, '2025-12-09 15:26:48', '2025-12-09 15:26:54', 1269, 1, '2025-12-12 08:00:00', '2025-12-09 15:26:48', '2025-12-09 15:47:57'),
(112, '103.156.42.11', 12, 41730, '2025-12-09 15:27:59', '2025-12-09 15:34:35', 396, 0, NULL, '2025-12-09 15:27:59', '2025-12-09 15:34:35'),
(113, '116.97.107.168', 12, 41730, '2025-12-09 15:28:07', '2025-12-09 15:28:27', 20, 0, NULL, '2025-12-09 15:28:07', '2025-12-09 15:28:27'),
(114, '103.156.42.11', 29, 41718, '2025-12-09 15:28:44', '2025-12-09 15:28:44', 0, 0, NULL, '2025-12-09 15:28:44', '2025-12-09 15:28:44'),
(115, '116.97.107.168', 6, 41731, '2025-12-09 15:28:45', '2025-12-09 15:31:18', 153, 0, NULL, '2025-12-09 15:28:45', '2025-12-09 15:31:18'),
(116, '103.156.42.11', 29, 41717, '2025-12-09 15:28:46', '2025-12-09 15:28:46', 0, 0, NULL, '2025-12-09 15:28:46', '2025-12-09 15:28:46'),
(117, '103.156.42.11', 6, 41731, '2025-12-09 15:28:49', '2025-12-09 15:33:50', 301, 0, NULL, '2025-12-09 15:28:49', '2025-12-09 15:33:50'),
(118, '103.156.42.11', 5, 41723, '2025-12-09 15:32:12', '2025-12-09 15:34:53', 161, 0, NULL, '2025-12-09 15:32:12', '2025-12-09 15:34:53'),
(119, '103.156.42.11', 11, 41732, '2025-12-09 15:32:15', '2025-12-09 15:34:30', 135, 0, NULL, '2025-12-09 15:32:15', '2025-12-09 15:34:30'),
(120, '103.156.42.11', 32, 41714, '2025-12-09 15:47:19', '2025-12-09 15:47:19', 0, 0, NULL, '2025-12-09 15:47:19', '2025-12-09 15:47:19'),
(121, '103.156.42.11', 31, 41715, '2025-12-09 15:47:23', '2025-12-09 15:47:23', 0, 0, NULL, '2025-12-09 15:47:23', '2025-12-09 15:47:23'),
(122, '103.156.42.11', 5, 41733, '2025-12-09 15:50:32', '2025-12-09 15:50:32', 0, 0, NULL, '2025-12-09 15:50:32', '2025-12-09 15:50:32'),
(123, '42.118.61.65', 2, 1004, '2025-12-10 00:40:27', '2025-12-10 00:40:46', 19, 0, NULL, '2025-12-10 00:40:27', '2025-12-10 00:40:46'),
(124, '42.118.61.65', 1, 1001, '2025-12-10 00:40:29', '2025-12-10 00:40:29', 0, 0, NULL, '2025-12-10 00:40:29', '2025-12-10 00:40:29'),
(125, '42.118.61.65', 2, 1005, '2025-12-10 00:40:31', '2025-12-10 00:40:31', 0, 0, NULL, '2025-12-10 00:40:31', '2025-12-10 00:40:31'),
(126, '42.118.61.65', 1, 1002, '2025-12-10 00:40:32', '2025-12-10 00:40:32', 0, 0, NULL, '2025-12-10 00:40:32', '2025-12-10 00:40:32'),
(127, '42.118.61.65', 1, 1003, '2025-12-10 00:40:33', '2025-12-10 00:40:33', 0, 0, NULL, '2025-12-10 00:40:33', '2025-12-10 00:40:33'),
(128, '42.118.61.65', 2, 1007, '2025-12-10 00:40:34', '2025-12-10 00:40:34', 0, 0, NULL, '2025-12-10 00:40:34', '2025-12-10 00:40:34'),
(129, '42.118.61.65', 1, 1000, '2025-12-10 00:40:36', '2025-12-10 08:55:52', 66, 0, NULL, '2025-12-10 00:40:36', '2025-12-10 08:55:52'),
(130, '42.118.61.65', 3, 1008, '2025-12-10 00:41:01', '2025-12-10 08:38:56', 987, 1, '2025-12-10 10:00:00', '2025-12-10 00:41:01', '2025-12-10 08:54:27'),
(131, '42.118.61.65', 3, 1009, '2025-12-10 00:41:02', '2025-12-10 08:54:33', 0, 0, NULL, '2025-12-10 00:41:02', '2025-12-10 08:54:33'),
(132, '42.118.61.65', 3, 1011, '2025-12-10 00:41:04', '2025-12-10 08:54:44', 0, 0, NULL, '2025-12-10 00:41:04', '2025-12-10 08:54:44'),
(133, '42.118.61.65', 5, 1012, '2025-12-10 00:41:16', '2025-12-10 00:41:16', 0, 0, NULL, '2025-12-10 00:41:16', '2025-12-10 00:41:16'),
(134, '42.118.61.65', 5, 1013, '2025-12-10 00:41:17', '2025-12-10 00:41:17', 0, 0, NULL, '2025-12-10 00:41:17', '2025-12-10 00:41:17'),
(135, '42.118.61.65', 5, 1014, '2025-12-10 00:41:19', '2025-12-10 00:41:28', 9, 0, NULL, '2025-12-10 00:41:19', '2025-12-10 00:41:28'),
(136, '42.118.61.65', 5, 1015, '2025-12-10 00:41:20', '2025-12-10 00:41:20', 0, 0, NULL, '2025-12-10 00:41:20', '2025-12-10 00:41:20'),
(137, '42.118.61.65', 29, 41717, '2025-12-10 00:41:36', '2025-12-10 00:41:56', 20, 0, NULL, '2025-12-10 00:41:36', '2025-12-10 00:41:56'),
(138, '42.118.61.65', 29, 41718, '2025-12-10 00:41:53', '2025-12-10 00:41:53', 0, 0, NULL, '2025-12-10 00:41:53', '2025-12-10 00:41:53'),
(139, '42.118.61.65', 6, 41731, '2025-12-10 00:41:59', '2025-12-10 07:35:55', 862, 0, NULL, '2025-12-10 00:41:59', '2025-12-10 07:35:55'),
(140, '42.118.61.65', 31, 41713, '2025-12-10 00:42:10', '2025-12-10 08:19:43', 943, 1, '2025-12-10 14:00:00', '2025-12-10 00:42:10', '2025-12-10 08:25:10'),
(141, '42.118.61.65', 5, 41733, '2025-12-10 00:42:28', '2025-12-10 00:42:28', 0, 0, NULL, '2025-12-10 00:42:28', '2025-12-10 00:42:28'),
(142, '42.118.61.65', 5, 41729, '2025-12-10 00:42:33', '2025-12-10 07:40:38', 710, 0, NULL, '2025-12-10 00:42:33', '2025-12-10 07:40:38'),
(143, '42.118.61.65', 3, 1063, '2025-12-10 00:42:51', '2025-12-10 00:42:51', 0, 0, NULL, '2025-12-10 00:42:51', '2025-12-10 00:42:51'),
(144, '42.118.61.65', 3, 1064, '2025-12-10 00:42:53', '2025-12-10 00:42:53', 0, 0, NULL, '2025-12-10 00:42:53', '2025-12-10 00:42:53'),
(145, '42.118.61.65', 3, 1065, '2025-12-10 00:42:54', '2025-12-10 00:42:54', 0, 0, NULL, '2025-12-10 00:42:54', '2025-12-10 00:42:54'),
(146, '42.118.61.65', 3, 1066, '2025-12-10 00:42:55', '2025-12-10 00:42:55', 0, 0, NULL, '2025-12-10 00:42:55', '2025-12-10 00:42:55'),
(147, '42.118.61.65', 3, 1067, '2025-12-10 00:42:56', '2025-12-10 00:42:56', 0, 0, NULL, '2025-12-10 00:42:56', '2025-12-10 00:42:56'),
(148, '42.118.61.65', 1, 1058, '2025-12-10 00:43:11', '2025-12-10 00:43:11', 0, 0, NULL, '2025-12-10 00:43:11', '2025-12-10 00:43:11'),
(149, '42.118.61.65', 1, 1059, '2025-12-10 00:43:13', '2025-12-10 00:43:14', 1, 0, NULL, '2025-12-10 00:43:13', '2025-12-10 00:43:14'),
(150, '42.118.61.65', 1, 1060, '2025-12-10 00:43:15', '2025-12-10 00:43:15', 0, 0, NULL, '2025-12-10 00:43:15', '2025-12-10 00:43:15'),
(151, '42.118.61.65', 1, 1061, '2025-12-10 00:43:16', '2025-12-10 00:43:16', 0, 0, NULL, '2025-12-10 00:43:16', '2025-12-10 00:43:16'),
(152, '42.118.61.65', 1, 1062, '2025-12-10 00:43:18', '2025-12-10 00:43:18', 0, 0, NULL, '2025-12-10 00:43:18', '2025-12-10 00:43:18'),
(153, '42.118.61.65', 5, 1068, '2025-12-10 00:43:26', '2025-12-10 00:43:26', 0, 0, NULL, '2025-12-10 00:43:26', '2025-12-10 00:43:26'),
(154, '42.118.61.65', 5, 1069, '2025-12-10 00:43:28', '2025-12-10 00:43:28', 0, 0, NULL, '2025-12-10 00:43:28', '2025-12-10 00:43:28'),
(155, '42.118.61.65', 5, 1070, '2025-12-10 00:43:29', '2025-12-10 00:43:29', 0, 0, NULL, '2025-12-10 00:43:29', '2025-12-10 00:43:29'),
(156, '42.118.61.65', 5, 1071, '2025-12-10 00:43:30', '2025-12-10 00:43:30', 0, 0, NULL, '2025-12-10 00:43:30', '2025-12-10 00:43:30'),
(157, '42.118.61.65', 5, 1072, '2025-12-10 00:43:31', '2025-12-10 00:43:31', 0, 0, NULL, '2025-12-10 00:43:31', '2025-12-10 00:43:31'),
(158, '42.118.61.65', 29, 41719, '2025-12-10 00:44:39', '2025-12-10 00:44:39', 0, 0, NULL, '2025-12-10 00:44:39', '2025-12-10 00:44:39'),
(159, '42.118.61.65', 27, 41734, '2025-12-10 00:44:42', '2025-12-10 00:47:55', 2658, 1, '2025-12-15 08:00:00', '2025-12-10 00:44:42', '2025-12-10 01:29:00'),
(160, '42.118.61.65', 28, 41735, '2025-12-10 00:45:46', '2025-12-10 00:45:55', 9, 0, NULL, '2025-12-10 00:45:46', '2025-12-10 00:45:55'),
(161, '42.118.61.65', 28, 41736, '2025-12-10 01:03:30', '2025-12-10 01:09:37', 367, 0, NULL, '2025-12-10 01:03:30', '2025-12-10 01:09:37'),
(162, '42.118.61.65', 12, 41737, '2025-12-10 01:10:20', '2025-12-10 01:10:29', 931, 1, '2025-12-15 08:00:00', '2025-12-10 01:10:20', '2025-12-10 01:25:51'),
(163, '42.118.61.65', 12, 41739, '2025-12-10 01:29:35', '2025-12-10 01:44:14', 879, 0, NULL, '2025-12-10 01:29:35', '2025-12-10 01:44:14'),
(164, '42.118.61.65', 30, 41740, '2025-12-10 07:44:34', '2025-12-10 07:49:42', 308, 0, NULL, '2025-12-10 07:44:34', '2025-12-10 07:49:42'),
(165, '42.118.61.65', 32, 41714, '2025-12-10 08:25:17', '2025-12-10 08:36:51', 976, 1, '2025-12-10 15:00:00', '2025-12-10 08:25:17', '2025-12-10 08:41:33'),
(166, '42.118.61.65', 33, 41746, '2025-12-10 08:41:47', '2025-12-10 08:42:34', 47, 0, NULL, '2025-12-10 08:41:47', '2025-12-10 08:42:34'),
(167, '171.255.57.66', 3, 1104, '2025-12-11 08:31:48', '2025-12-11 08:33:15', 87, 0, NULL, '2025-12-11 08:31:48', '2025-12-11 08:33:15'),
(168, '171.255.57.66', 12, 41728, '2025-12-11 08:47:08', '2025-12-11 08:47:08', 0, 0, NULL, '2025-12-11 08:47:08', '2025-12-11 08:47:08'),
(169, '59.153.224.177', 3, 1092, '2025-12-11 14:38:32', '2025-12-11 14:38:56', 24, 0, NULL, '2025-12-11 14:38:32', '2025-12-11 14:38:56'),
(170, '59.153.224.177', 30, 41756, '2025-12-11 14:42:42', '2025-12-11 14:43:44', 62, 0, NULL, '2025-12-11 14:42:42', '2025-12-11 14:43:44'),
(171, '42.118.61.65', 31, 41758, '2025-12-12 02:03:26', '2025-12-12 02:05:26', 120, 0, NULL, '2025-12-12 02:03:26', '2025-12-12 02:05:26'),
(172, '42.118.61.65', 3, 1092, '2025-12-12 05:00:58', '2025-12-12 05:00:58', 0, 0, NULL, '2025-12-12 05:00:58', '2025-12-12 05:00:58'),
(173, '118.70.43.248', 3, 1104, '2025-12-13 11:00:19', '2025-12-13 12:20:54', 649, 0, NULL, '2025-12-13 11:00:19', '2025-12-13 12:20:54'),
(174, '118.70.43.248', 3, 1105, '2025-12-13 11:01:02', '2025-12-13 11:11:12', 2101, 1, '2025-12-17 12:30:00', '2025-12-13 11:01:02', '2025-12-13 11:36:03'),
(175, '118.70.43.248', 3, 1106, '2025-12-13 11:01:03', '2025-12-13 11:11:13', 610, 0, NULL, '2025-12-13 11:01:03', '2025-12-13 11:11:13'),
(176, '118.70.43.248', 3, 1107, '2025-12-13 11:01:05', '2025-12-13 11:11:14', 2104, 1, '2025-12-17 19:30:00', '2025-12-13 11:01:05', '2025-12-13 11:36:09'),
(177, '118.70.43.248', 3, 1117, '2025-12-13 11:36:43', '2025-12-13 11:36:43', 0, 0, NULL, '2025-12-13 11:36:43', '2025-12-13 11:36:43'),
(178, '118.70.43.248', 3, 1118, '2025-12-13 11:36:56', '2025-12-13 11:36:56', 0, 0, NULL, '2025-12-13 11:36:56', '2025-12-13 11:36:56'),
(179, '118.70.43.248', 41, 41761, '2025-12-13 12:04:25', '2025-12-13 12:04:25', 0, 0, NULL, '2025-12-13 12:04:25', '2025-12-13 12:04:25'),
(180, '103.156.42.16', 3, 1104, '2025-12-14 14:20:39', '2025-12-14 14:20:39', 0, 0, NULL, '2025-12-14 14:20:39', '2025-12-14 14:20:39'),
(181, '103.156.42.16', 3, 1136, '2025-12-14 14:20:51', '2025-12-14 14:20:51', 0, 0, NULL, '2025-12-14 14:20:51', '2025-12-14 14:20:51'),
(182, '103.156.42.16', 27, 41734, '2025-12-14 14:22:50', '2025-12-14 14:22:50', 0, 0, NULL, '2025-12-14 14:22:50', '2025-12-14 14:22:50'),
(183, '103.156.42.16', 5, 41798, '2025-12-14 14:22:58', '2025-12-14 14:22:58', 1405, 1, '2025-12-15 08:00:00', '2025-12-14 14:22:58', '2025-12-14 14:46:23'),
(184, '123.18.81.83', 5, 41798, '2025-12-14 14:45:28', '2025-12-14 15:00:00', 872, 0, NULL, '2025-12-14 14:45:28', '2025-12-14 15:00:00'),
(185, '103.156.42.16', 6, 41799, '2025-12-14 14:54:33', '2025-12-14 14:54:33', 0, 0, NULL, '2025-12-14 14:54:33', '2025-12-14 14:54:33'),
(186, '42.118.61.65', 30, 41801, '2025-12-15 03:03:15', '2025-12-15 03:17:12', 1292, 1, '2025-12-15 11:00:00', '2025-12-15 03:03:15', '2025-12-15 03:24:47'),
(187, '14.180.20.93', 3, 41824, '2025-12-27 18:23:32', '2025-12-27 18:24:10', 38, 0, NULL, '2025-12-27 18:23:32', '2025-12-27 18:24:10'),
(188, '116.106.98.133', 5, 41825, '2025-12-29 14:36:22', '2025-12-29 14:37:36', 74, 0, NULL, '2025-12-29 14:36:22', '2025-12-29 14:37:36'),
(189, '58.186.230.31', 5, 41826, '2026-01-27 13:17:41', '2026-01-27 13:28:22', 969, 1, '2026-01-28 12:00:00', '2026-01-27 13:17:41', '2026-01-27 13:33:50'),
(190, '58.186.230.31', 5, 41827, '2026-01-27 13:47:36', '2026-01-27 14:00:40', 2203, 1, '2026-01-28 10:00:00', '2026-01-27 13:47:36', '2026-01-27 14:24:19'),
(191, '58.186.230.31', 5, 41828, '2026-01-27 17:25:26', '2026-01-27 17:26:10', 44, 0, NULL, '2026-01-27 17:25:26', '2026-01-27 17:26:10'),
(192, '113.184.55.92', 5, 41828, '2026-01-29 03:11:27', '2026-01-29 03:11:27', 0, 0, NULL, '2026-01-29 03:11:27', '2026-01-29 03:11:27'),
(193, '42.119.72.51', 5, 41829, '2026-02-03 02:08:36', '2026-02-03 03:11:26', 29, 0, NULL, '2026-02-03 02:08:36', '2026-02-03 03:11:26'),
(194, '27.78.7.126', 5, 41830, '2026-02-04 07:10:00', '2026-02-04 07:13:09', 189, 0, NULL, '2026-02-04 07:10:00', '2026-02-04 07:13:09');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ip_spam_logs`
--

CREATE TABLE `ip_spam_logs` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `action_type` varchar(50) NOT NULL DEFAULT 'general',
  `is_spam` tinyint(1) DEFAULT 0,
  `details` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `ip_spam_logs`
--

INSERT INTO `ip_spam_logs` (`id`, `ip_address`, `action_type`, `is_spam`, `details`, `user_id`, `created_at`) VALUES
(1, '::1', 'booking', 0, 'Đặt vé thành công: 2 vé', 9, '2025-11-28 01:07:57'),
(2, '::1', 'booking', 0, 'Đặt vé thành công: 4 vé', 9, '2025-11-28 01:09:16'),
(3, '::1', 'booking', 0, 'Đặt vé thành công: 1 vé', 9, '2025-11-28 01:10:11'),
(4, '::1', 'booking', 0, 'Đặt vé thành công: 2 vé', 9, '2025-11-28 01:10:35'),
(5, '::1', 'booking', 0, 'Đặt vé thành công: 2 vé', 9, '2025-11-28 01:11:56'),
(6, '::1', 'booking', 0, 'Đặt vé thành công: 2 vé', 9, '2025-11-28 01:15:31'),
(7, '::1', 'booking', 0, 'Đặt vé thành công: 2 vé', 9, '2025-11-28 01:16:16'),
(8, '::1', 'booking', 0, 'Đặt vé thành công: 2 vé', 9, '2025-11-28 01:16:52'),
(9, '::1', 'booking', 0, 'Đặt vé thành công: 4 vé', 9, '2025-11-28 01:18:55'),
(10, '::1', 'booking', 0, 'Đặt vé thành công: 2 vé', 9, '2025-11-28 01:20:31'),
(11, '::1', 'booking', 0, 'Đặt vé thành công: 2 vé', 9, '2025-11-28 01:22:32'),
(12, '::1', 'booking', 0, 'Đặt vé thành công: 2 vé', 9, '2025-11-28 01:23:11'),
(13, '::1', 'booking', 0, 'Đặt vé thành công: 2 vé', 9, '2025-11-28 01:23:45'),
(14, '::1', 'booking', 0, 'Đặt vé thành công: 2 vé', 9, '2025-11-28 01:26:33'),
(15, '::1', 'booking', 0, 'Đặt vé thành công: 1 vé', 9, '2025-11-28 01:28:40'),
(16, '::1', 'login', 1, 'Đăng nhập thất bại: admin2@cinehub.com', NULL, '2025-11-28 01:31:44'),
(17, '183.81.104.232', 'login', 1, 'Đăng nhập thất bại: tuanawh@gmail.com', NULL, '2025-12-04 13:52:58'),
(18, '171.255.56.31', 'login', 1, 'Đăng nhập thất bại: admin2@gmail.com', NULL, '2025-12-04 18:29:26'),
(19, '171.255.56.31', 'review', 0, 'Review movie_id: 8, rating: 1', 9, '2025-12-04 19:53:53'),
(20, '171.255.56.31', 'comment', 0, 'Comment movie_id: 35', 9, '2025-12-04 20:12:42'),
(21, '171.255.56.31', 'review', 1, 'Comment quá ngắn', 9, '2025-12-04 20:12:53'),
(22, '14.170.222.119', 'login', 1, 'Đăng nhập thất bại: awhtuan@gmail.com', NULL, '2025-12-06 17:28:48'),
(23, '42.118.61.65', 'login', 1, 'Đăng nhập thất bại: hson97805@gmail.com', NULL, '2025-12-08 04:08:41'),
(24, '42.118.61.65', 'review', 1, 'Comment quá ngắn', 21, '2025-12-09 01:33:22'),
(25, '42.118.61.65', 'comment', 0, 'Comment movie_id: 33', 21, '2025-12-09 01:34:05'),
(26, '42.118.61.65', 'review', 1, 'Comment quá ngắn', 21, '2025-12-09 01:35:25'),
(27, '42.118.61.65', 'login', 1, 'Đăng nhập thất bại: vanlinh2506@gmail.com', NULL, '2025-12-09 01:37:30'),
(28, '42.118.61.65', 'register', 1, 'Email đã tồn tại: hson97805@gmail.com', NULL, '2025-12-09 01:37:42'),
(29, '117.2.114.199', 'comment', 0, 'Comment movie_id: 33', 9, '2025-12-09 01:56:22'),
(30, '117.2.114.199', 'comment', 0, 'Comment movie_id: 33', 9, '2025-12-09 01:59:21'),
(31, '117.2.114.199', 'comment', 0, 'Comment movie_id: 33', 22, '2025-12-09 01:59:39'),
(32, '203.113.133.232', 'login', 1, 'Đăng nhập thất bại: thutrang@gmail.com', NULL, '2025-12-09 02:29:33'),
(33, '203.113.133.232', 'login', 1, 'Đăng nhập thất bại: thutrang@gmail.com', NULL, '2025-12-09 02:29:34'),
(34, '203.113.133.232', 'login', 1, 'Đăng nhập thất bại: thutrang@gmail.com', NULL, '2025-12-09 02:29:35'),
(35, '203.113.133.232', 'login', 1, 'Đăng nhập thất bại: thutrang@gmail.com', NULL, '2025-12-09 02:29:35'),
(36, '203.113.133.232', 'login', 1, 'Đăng nhập thất bại: thutrang@gmail.com', NULL, '2025-12-09 02:29:36'),
(37, '42.118.61.65', 'review', 1, 'Comment quá ngắn', 26, '2025-12-09 03:43:08'),
(38, '42.118.61.65', 'comment', 0, 'Comment movie_id: 8', 26, '2025-12-09 03:43:54'),
(39, '117.2.114.199', 'comment', 0, 'Comment movie_id: 8', 9, '2025-12-09 03:44:16'),
(40, '1.55.108.99', 'register', 1, 'Email đã tồn tại: thutrang1@gmail.com', NULL, '2025-12-09 11:33:40'),
(41, '1.55.108.99', 'login', 1, 'Đăng nhập thất bại: trang12@gmail.com', NULL, '2025-12-09 12:35:03'),
(42, '1.55.108.99', 'login', 1, 'Đăng nhập thất bại: trang12@gmail.com', NULL, '2025-12-09 12:35:07'),
(43, '113.186.191.113', 'comment', 0, 'Comment movie_id: 28', 29, '2025-12-11 08:56:34'),
(44, '42.118.61.65', 'login', 1, 'Đăng nhập thất bại: lotte@gmail.com', NULL, '2025-12-12 02:02:01'),
(45, '42.118.61.65', 'login', 1, 'Đăng nhập thất bại: plv2006@gmail.com', NULL, '2025-12-12 05:05:11'),
(46, '42.118.61.65', 'login', 1, 'Đăng nhập thất bại: plv2006@gmail.com', NULL, '2025-12-12 05:05:17'),
(47, '123.18.81.83', 'login', 1, 'Đăng nhập thất bại: sccuong5222@gmail.com', NULL, '2025-12-14 14:43:21'),
(48, '123.18.81.83', 'login', 1, 'Đăng nhập thất bại: admin2@cinhub.com', NULL, '2025-12-14 15:05:02'),
(49, '42.118.61.65', 'login', 1, 'Đăng nhập thất bại: tuanawh@gmail.com', NULL, '2025-12-15 02:01:02'),
(50, '171.255.57.178', 'login', 1, 'Đăng nhập thất bại: plv@gmail.com', NULL, '2025-12-15 02:17:09'),
(51, '171.255.57.213', 'login', 1, 'Đăng nhập thất bại: nguyenvanlinh250606@gmail.com', NULL, '2025-12-15 02:37:16'),
(52, '222.255.243.95', 'login', 1, 'Đăng nhập thất bại: admin@example.com', NULL, '2025-12-19 05:37:06'),
(53, '222.255.243.95', 'login', 1, 'Đăng nhập thất bại: admin@example.com', NULL, '2025-12-19 05:37:09'),
(54, '222.255.243.95', 'login', 1, 'Đăng nhập thất bại: admin123@example.com', NULL, '2025-12-19 05:37:13'),
(55, '222.255.243.95', 'login', 1, 'Đăng nhập thất bại: admin123@example.com', NULL, '2025-12-19 05:37:28'),
(56, '222.255.243.95', 'login', 1, 'Đăng nhập thất bại: admin123@example.com', NULL, '2025-12-19 05:37:33'),
(57, '27.76.251.237', 'login', 1, 'Đăng nhập thất bại: admin123@example.com', NULL, '2026-01-17 14:17:42'),
(58, '27.67.131.143', 'review', 1, 'Comment quá ngắn', 3, '2026-02-09 03:08:02'),
(59, '27.67.131.143', 'comment', 0, 'Comment movie_id: 53', 3, '2026-02-09 03:08:09');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2026_06_09_064627_add_updated_at_to_watch_history_table', 1),
(2, '2026_06_16_023006_add_phone_to_users_table', 2),
(3, '2026_06_16_024417_modify_role_column_in_users_table', 3);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `moderator_permission_requests`
--

CREATE TABLE `moderator_permission_requests` (
  `id` int(11) NOT NULL,
  `theater_id` int(11) NOT NULL,
  `moderator_id` int(11) DEFAULT NULL,
  `requested_by` int(11) NOT NULL,
  `target_user_id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `old_data` text DEFAULT NULL,
  `new_data` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `responded_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `moderator_permission_requests`
--

INSERT INTO `moderator_permission_requests` (`id`, `theater_id`, `moderator_id`, `requested_by`, `target_user_id`, `action`, `old_data`, `new_data`, `status`, `created_at`, `responded_at`) VALUES
(1, 3, 9, 3, 0, 'update_role', '{\"role\":\"user\",\"theater_id\":null}', '{\"role\":\"moderator\",\"theater_id\":\"3\"}', 'rejected', '2025-11-30 17:57:49', '2025-12-07 14:37:29'),
(2, 3, 9, 3, 16, 'update_role', '{\"role\":\"user\",\"theater_id\":null}', '{\"role\":\"moderator\",\"theater_id\":\"3\"}', 'rejected', '2025-12-04 18:40:04', '2025-12-04 18:41:27');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `movies`
--

CREATE TABLE `movies` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `level` enum('Free','Silver','Gold','Premium') DEFAULT 'Free',
  `duration` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `director` varchar(100) DEFAULT NULL,
  `actors` text DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `trailer_url` varchar(255) DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `status` enum('Sắp chiếu','Chiếu rạp','Chiếu online') DEFAULT 'Sắp chiếu',
  `rating` float DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status_admin` enum('draft','scheduled','published','archived') DEFAULT 'draft',
  `publish_date` datetime DEFAULT NULL,
  `geo_restriction` text DEFAULT NULL,
  `drm_enabled` tinyint(1) DEFAULT 0,
  `banner` varchar(255) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `language` varchar(50) DEFAULT NULL,
  `age_rating` varchar(10) DEFAULT NULL,
  `type` enum('phimle','phimbo') DEFAULT 'phimle',
  `max_tickets` int(11) DEFAULT NULL COMMENT 'Số lượng vé tối đa cho phim (NULL = không giới hạn)',
  `normal_price` int(11) DEFAULT 90000 COMMENT 'Giá ghế thường',
  `vip_price` int(11) DEFAULT 120000 COMMENT 'Giá ghế VIP',
  `couple_price` int(11) DEFAULT 180000 COMMENT 'Giá ghế đôi'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `movies`
--

INSERT INTO `movies` (`id`, `title`, `category_id`, `level`, `duration`, `description`, `director`, `actors`, `video_url`, `trailer_url`, `thumbnail`, `status`, `rating`, `created_at`, `status_admin`, `publish_date`, `geo_restriction`, `drm_enabled`, `banner`, `country`, `language`, `age_rating`, `type`, `max_tickets`, `normal_price`, `vip_price`, `couple_price`) VALUES
(1, 'Avengers: Endgame', 1, 'Premium', 181, 'Phim siêu anh hùng Marvel, kết thúc của Infinity Saga', 'Anthony Russo, Joe Russo', 'Robert Downey Jr., Chris Evans, Mark Ruffalo', 'data/phim/phimle/Avengers_Endgame.mp4', 'https://example.com/avengers-trailer.mp4', 'data/img/avengers_end_game_img.jpg', 'Chiếu rạp', 9.2, '2025-11-12 07:41:09', 'published', NULL, NULL, 0, 'data/img/avengers_end_game.jpg', 'Mỹ', 'Tiếng Anh', 'PG-13', 'phimle', NULL, 90000, 120000, 180000),
(2, 'Titanic', 2, 'Gold', 194, 'Câu chuyện tình yêu trên con tàu định mệnh', 'James Cameron', 'Leonardo DiCaprio, Kate Winslet', 'data/phim/phimle/titanic.mp4\r\n', 'https://example.com/titanic-trailer.mp4', 'data/img/titanic.jpg', 'Chiếu rạp', 8.8, '2025-11-12 07:41:09', 'published', NULL, NULL, 0, NULL, 'Mỹ', 'Tiếng Anh', 'PG-13', 'phimle', NULL, 90000, 120000, 180000),
(3, 'The Hangover', 3, 'Silver', 100, 'Phim hài về chuyến đi Las Vegas đầy biến cố', 'Todd Phillips', 'Bradley Cooper, Ed Helms, Zach Galifianakis', 'https://example.com/hangover.mp4', 'https://example.com/hangover-trailer.mp4', 'data/img/the_hangover_img.jpg', 'Chiếu rạp', 7.7, '2025-11-12 07:41:09', 'published', NULL, NULL, 0, 'data/img/the_hangover.jpg', 'Mỹ', 'Tiếng Anh', 'R', 'phimle', NULL, 90000, 120000, 180000),
(4, 'The Conjuring', 4, 'Gold', 112, 'Phim kinh dị về các nhà điều tra siêu nhiên', 'James Wan', 'Patrick Wilson, Vera Farmiga', 'https://example.com/conjuring.mp4', 'https://example.com/conjuring-trailer.mp4', 'data/img/the_conjuring_img.jpg', 'Chiếu rạp', 7.5, '2025-11-12 07:41:09', 'published', NULL, NULL, 0, 'data/img/the_conjuring.jpg', 'Mỹ', 'Tiếng Anh', 'R', 'phimle', NULL, 90000, 120000, 180000),
(5, 'Toy Story 4', 5, 'Free', 100, 'Cuộc phiêu lưu mới của Woody và Buzz', 'Josh Cooley', 'Tom Hanks, Tim Allen', 'https://example.com/toystory.mp4', 'https://example.com/toystory-trailer.mp4', 'data/img/toy_story_img.jpg', 'Chiếu rạp', NULL, '2025-11-12 07:41:09', 'published', NULL, NULL, 0, 'data/img/toy_story.jpg', 'Mỹ', 'Tiếng Anh', 'G', 'phimle', NULL, 90000, 120000, 180000),
(6, 'Interstellar', 6, 'Premium', 169, 'Cuộc hành trình không gian để cứu nhân loại', 'Christopher Nolan', 'Matthew McConaughey, Anne Hathaway', 'data/phim/phimle/Interstellar\r\n.mp4', 'https://example.com/interstellar-trailer.mp4', 'data/img/Interstellar_img.jpg', 'Chiếu online', 8.6, '2025-11-12 07:41:09', 'published', NULL, NULL, 0, 'data/img/Interstellar.jpg', 'Mỹ', 'Tiếng Anh', 'PG-13', 'phimle', NULL, 90000, 120000, 180000),
(7, 'Indiana Jones', 7, 'Gold', 122, 'Cuộc phiêu lưu tìm kiếm cổ vật', 'Steven Spielberg', 'Harrison Ford', 'https://example.com/indiana.mp4', 'https://example.com/indiana-trailer.mp4', 'data/img/indiana_jones_img.jpg', 'Chiếu online', 8.2, '2025-11-12 07:41:09', 'published', NULL, NULL, 0, 'data/img/indiana_jones.jpg', 'Mỹ', 'Tiếng Anh', 'PG-13', 'phimle', NULL, 90000, 120000, 180000),
(8, 'Game of Thrones', 7, 'Premium', 60, 'Cuộc chiến giành quyền lực giữa các dòng họ ở vùng đất Westeros. Bộ phim kể về cuộc đấu tranh của các gia đình quý tộc để giành lấy Ngai Sắt Sắt và cai trị bảy vương quốc.', 'David Benioff, D.B. Weiss', 'Emilia Clarke, Kit Harington, Peter Dinklage, Lena Headey', 'data/phim/phimbo/gameofthrones', 'https://example.com/got-trailer.mp4', 'data/img/game_of_thrones_img.webp', 'Chiếu online', 9.3, '2025-11-17 01:17:59', 'published', NULL, NULL, 0, 'data/img/game_of_thrones.jpg', 'Mỹ', 'Tiếng Anh', 'TV-MA', 'phimbo', NULL, 90000, 120000, 180000),
(9, 'Breaking Bad', 1, 'Gold', 47, 'Câu chuyện về giáo viên hóa học trung học Walter White, người bắt đầu sản xuất và bán methamphetamine sau khi được chẩn đoán ung thư phổi giai đoạn cuối.', 'Vince Gilligan', 'Bryan Cranston, Aaron Paul, Anna Gunn, Dean Norris', 'data/phim/phimbo/breaking_bad', 'https://example.com/breaking-bad-trailer.mp4', 'data/img/breaking_bad_img.jpg', 'Chiếu online', 9.5, '2025-11-17 01:17:59', 'published', NULL, NULL, 0, 'data/img/breaking_bad.jpg', 'Mỹ', 'Tiếng Anh', 'TV-14', 'phimbo', NULL, 90000, 120000, 180000),
(10, 'The Walking Dead', 4, 'Gold', 45, 'Sheriff Deputy Rick Grimes tỉnh dậy sau một chấn thương và phát hiện ra thế giới đã bị tàn phá bởi đại dịch zombie. Anh phải dẫn dắt nhóm người sống sót tìm nơi trú ẩn.', 'Frank Darabont', 'Andrew Lincoln, Norman Reedus, Melissa McBride, Danai Gurira', 'data/phim/phimbo/the_walking_dead', 'https://example.com/walking-dead-trailer.mp4', 'data/img/the_walking_dead_img.jpg', 'Chiếu online', 8.2, '2025-11-17 01:17:59', 'published', NULL, NULL, 0, 'data/img/the_walking_dead.jpg', 'Mỹ', 'Tiếng Anh', 'TV-MA', 'phimbo', NULL, 90000, 120000, 180000),
(11, 'Stranger Things', 6, 'Premium', 50, 'Khi một cậu bé 12 tuổi biến mất, một thị trấn nhỏ ở Indiana tiết lộ một bí mật liên quan đến thí nghiệm bí mật, siêu năng lực đáng sợ và một cô gái nhỏ lạ thường.', 'The Duffer Brothers', 'Millie Bobby Brown, Finn Wolfhard, Winona Ryder, David Harbour', 'data/phim/phimbo/stranger_things', 'https://example.com/stranger-things-trailer.mp4', 'data/img/stranger_things_img.jpg', 'Chiếu online', 8.7, '2025-11-17 01:17:59', 'published', NULL, NULL, 0, 'data/img/stranger_things.jpg', 'Mỹ', 'Tiếng Anh', 'TV-14', 'phimbo', NULL, 90000, 120000, 180000),
(12, 'House of Cards', 2, 'Gold', 58, 'Một chính trị gia khôn ngoan và không khoan nhượng làm bất cứ điều gì để giành quyền lực ở Washington D.C.', 'Beau Willimon', 'Kevin Spacey, Robin Wright, Kate Mara, Michael Kelly', 'data/phim/phimbo/house_of_cards', 'https://example.com/house-of-cards-trailer.mp4', 'data/img/house_of_cards_img.png', 'Chiếu online', 8.8, '2025-11-17 01:17:59', 'published', NULL, NULL, 0, 'data/img/banners/House_of_Cards_banner_1765628600.jpg', 'Mỹ', 'Tiếng Anh', 'TV-MA', 'phimbo', NULL, 90000, 120000, 180000),
(13, 'The Crown', 2, 'Premium', 58, 'Dòng thời gian về triều đại của Nữ hoàng Elizabeth II của Vương quốc Anh, từ những năm 1950 đến những năm 2000.', 'Peter Morgan', 'Claire Foy, Olivia Colman, Matt Smith, Tobias Menzies', 'data/phim/phimbo/the_crown', 'https://example.com/the-crown-trailer.mp4', 'data/img/the_crown.jpg', 'Chiếu online', 8.6, '2025-11-17 01:17:59', 'published', NULL, NULL, 0, NULL, 'Anh', 'Tiếng Anh', 'TV-MA', 'phimbo', NULL, 90000, 120000, 180000),
(14, 'Sherlock', 1, 'Gold', 90, 'Phiên bản hiện đại của các câu chuyện điều tra nổi tiếng của Sir Arthur Conan Doyle, với Sherlock Holmes và Dr. John Watson giải quyết các vụ án ở London thế kỷ 21.', 'Mark Gatiss, Steven Moffat', 'Benedict Cumberbatch, Martin Freeman, Rupert Graves, Mark Gatiss', 'data/phim/phimbo/sherlock', 'https://example.com/sherlock-trailer.mp4', 'data/img/Sherlock_img.jpg', 'Chiếu online', 9.1, '2025-11-17 01:17:59', 'published', NULL, NULL, 0, 'data/img/sherlock.jpg', 'Anh', 'Tiếng Anh', 'TV-14', 'phimbo', NULL, 90000, 120000, 180000),
(15, 'The Office', 3, 'Silver', 22, 'Một mockumentary về nhóm nhân viên văn phòng hàng ngày tại văn phòng chi nhánh Scranton của công ty giấy Dunder Mifflin.', 'Greg Daniels', 'Steve Carell, Rainn Wilson, John Krasinski, Jenna Fischer', 'data/phim/phimbo/the_office', 'https://example.com/the-office-trailer.mp4', 'data/img/the_office.png', 'Chiếu online', 8.9, '2025-11-17 01:17:59', 'published', NULL, NULL, 0, NULL, 'Mỹ', 'Tiếng Anh', 'TV-14', 'phimbo', NULL, 90000, 120000, 180000),
(16, 'Friends', 3, 'Silver', 22, 'Cuộc sống và tình yêu của sáu người bạn ở Manhattan, New York, khi họ cố gắng tìm ra con đường của mình trong cuộc sống.', 'David Crane, Marta Kauffman', 'Jennifer Aniston, Courteney Cox, Lisa Kudrow, Matt LeBlanc, Matthew Perry, David Schwimmer', 'data/phim/phimbo/friends', 'https://example.com/friends-trailer.mp4', 'data/img/friends.jpg', 'Chiếu online', 9, '2025-11-17 01:17:59', 'published', NULL, NULL, 0, NULL, 'Mỹ', 'Tiếng Anh', 'TV-14', 'phimbo', NULL, 90000, 120000, 180000),
(17, 'The Witcher', 7, 'Premium', 60, 'Geralt of Rivia, một thợ săn quái vật đột biến đi khắp đất liền để tìm nơi thuộc về mình trong một thế giới nơi con người thường tồi tệ hơn quái vật.', 'Lauren Schmidt Hissrich', 'Henry Cavill, Anya Chalotra, Freya Allan, Joey Batey', 'data/phim/phimbo/the_witcher', 'https://example.com/the-witcher-trailer.mp4', 'data/img/the_witcher.jpg', 'Chiếu online', 8.2, '2025-11-17 01:17:59', 'published', NULL, NULL, 0, NULL, 'Mỹ/ Ba Lan', 'Tiếng Anh', 'TV-MA', 'phimbo', NULL, 90000, 120000, 180000),
(18, 'Hai Phượng', 1, 'Premium', 98, 'Mẹ đơn thân từng là dân giang hồ phải chiến đấu với băng nhóm bắt cóc con gái mình.', 'Lê Văn Kiệt', 'Ngô Thanh Vân, Mai Cát Vi', 'data/phim/phimle/hai_phuong.mp4', 'https://example.com/hai-phuong-trailer.mp4', 'data/img/hai_phuong_img.jpg', 'Chiếu rạp', 7.5, '2025-11-24 09:19:09', 'published', NULL, NULL, 0, 'data/img/hai_phuong.jpg', 'Việt Nam', 'Tiếng Việt', 'C16', 'phimle', NULL, 90000, 150000, 190000),
(19, 'Mắt Biếc', 2, 'Gold', 117, 'Câu chuyện tình đơn phương lãng mạn và đầy hoài niệm ở thập niên 70.', 'Victor Vũ', 'Trần Nghĩa, Trúc Anh, Trần Phong', 'data/phim/phimle/mat_biec.mp4', 'https://example.com/mat-biec-trailer.mp4', 'data/img/mat_biec_img.jpg', 'Chiếu rạp', NULL, '2025-11-24 09:19:09', 'published', NULL, NULL, 0, 'data/img/mat_biec.webp', 'Việt Nam', 'Tiếng Việt', 'C13', 'phimle', NULL, 90000, 120000, 180000),
(20, 'Bố Già', 3, 'Premium', 128, 'Phim về tình cha con đầy cảm xúc và những mâu thuẫn trong gia đình.', 'Trấn Thành, Vũ Ngọc Đãng', 'Trấn Thành, Lê Giang, Tuấn Trần', 'data/phim/phimle/bo_gia.mp4', 'https://example.com/bo-gia-trailer.mp4', 'data/img/bo_gia_img.jpg', 'Chiếu rạp', 8.5, '2025-11-24 09:19:09', 'published', NULL, NULL, 0, 'data/img/bo_gia.webp', 'Việt Nam', 'Tiếng Việt', 'C13', 'phimle', NULL, 90000, 120000, 180000),
(21, 'Tiệc Trăng Máu', 3, 'Gold', 100, 'Bảy người bạn cùng chơi một trò chơi công khai tin nhắn và cuộc gọi điện thoại, dẫn đến những bí mật bị phanh phui.', 'Nguyễn Quang Dũng', 'Thái Hòa, Thu Trang, Hồng Ánh, Hứa Vĩ Văn', 'data/phim/phimle/tiec_trang_mau.mp4', 'https://example.com/tiec-trang-mau-trailer.mp4', 'data/img/tiec_trang_mau.jpg', 'Chiếu rạp', 7.8, '2025-11-24 09:19:09', 'published', NULL, NULL, 0, NULL, 'Việt Nam', 'Tiếng Việt', 'C16', 'phimle', NULL, 90000, 120000, 180000),
(22, 'Lật Mặt 4: Nhà Có Khách', 4, 'Silver', 90, 'Phim hài kinh dị với những tình huống dở khóc dở cười và yếu tố ma quái.', 'Lý Hải', 'Lý Hải, Katleen Phan Võ, Huy Khánh', 'data/phim/phimle/lat_mat_4.mp4', 'https://example.com/lat-mat-4-trailer.mp4', 'data/img/lat_mat_4.jpg', 'Chiếu rạp', 7, '2025-11-24 09:19:09', 'published', NULL, NULL, 0, NULL, 'Việt Nam', 'Tiếng Việt', 'C13', 'phimle', NULL, 90000, 120000, 180000),
(23, 'Em Chưa 18', 3, 'Free', 95, 'Chuyện tình hài hước giữa một cô gái tuổi teen và một chàng trai đã trưởng thành.', 'Lê Thanh Sơn', 'Kaity Nguyễn, Kiều Minh Tuấn', 'data/phim/phimle/em_chua_18.mp4', 'https://example.com/em-chua-18-trailer.mp4', 'data/img/em_chua_18_img.jpg', 'Chiếu rạp', 7.2, '2025-11-24 09:19:09', 'published', NULL, NULL, 0, 'data/img/em_chua_18.jpg', 'Việt Nam', 'Tiếng Việt', 'C16', 'phimle', NULL, 90000, 120000, 180000),
(24, 'Chị Trợ Lý Của Anh', 2, 'Gold', 105, 'Giám đốc trẻ phải thuê một cô trợ lý bí ẩn để cứu công ty của mình.', 'Lý Minh Thắng', 'Mỹ Tâm, Mai Tài Phến', 'data/phim/phimle/chi_tro_ly_cua_anh.mp4', 'https://example.com/chi-tro-ly-cua-anh-trailer.mp4', 'data/img/chi_tro_ly_cua_anh_img.jpg', 'Chiếu online', 6.8, '2025-11-24 09:19:09', 'published', NULL, NULL, 0, 'data/img/chi_tro_ly_cua_anh.jpg', 'Việt Nam', 'Tiếng Việt', 'C13', 'phimle', NULL, 90000, 120000, 180000),
(25, 'Gái Già Lắm Chiêu 3', 3, 'Premium', 108, 'Cuộc chiến mẹ chồng nàng dâu đầy xa hoa và kịch tính ở Huế.', 'Nam Cito, Bảo Nhân', 'Ninh Dương Lan Ngọc, Lê Khanh, Hồng Vân', 'data/phim/phimle/gai_gia_lam_chieu_3.mp4', 'https://example.com/gai-gia-lam-chieu-3-trailer.mp4', 'data/img/gai_gia_lam_chieu_img.jpg', 'Chiếu rạp', 7.4, '2025-11-24 09:19:09', 'published', NULL, NULL, 0, 'data/img/gai_gia_lam_chieu.png', 'Việt Nam', 'Tiếng Việt', 'C16', 'phimle', NULL, 90000, 120000, 180000),
(26, 'Quỳnh Hoa Nhất Dạ', 7, 'Premium', 120, 'Phim cổ trang, dã sử về cuộc đời đầy sóng gió của Thái hậu Dương Vân Nga.', 'Lý Minh Thắng', 'Nhã Phương, Thuý Ngân, Lương Thế Thành', 'data/phim/phimle/quynh_hoa_nhat_da.mp4', 'https://example.com/quynh-hoa-nhat-da-trailer.mp4', 'data/img/quynh_hoa_nhat_da_img.jpg', 'Chiếu rạp', 7.7, '2025-11-24 09:19:09', 'published', NULL, NULL, 0, 'data/img/quynh_hoa_nhat_da.webp', 'Việt Nam', 'Tiếng Việt', 'C13', 'phimle', NULL, 90000, 120000, 180000),
(27, 'Tấm Cám: Chuyện Chưa Kể', 5, 'Gold', 116, 'Phiên bản cải biên của truyện cổ tích Tấm Cám, kết hợp yếu tố giả tưởng và hành động.', 'Ngô Thanh Vân', 'Hạ Vi, Isaac, Ngô Thanh Vân', 'data/phim/phimle/tam_cam.mp4', 'https://example.com/tam-cam-trailer.mp4', 'data/img/tam_cam.jpg', 'Chiếu rạp', 7.1, '2025-11-24 09:19:09', 'published', NULL, NULL, 0, NULL, 'Việt Nam', 'Tiếng Việt', 'C13', 'phimle', NULL, 90000, 120000, 180000),
(28, 'Hương Vị Tình Thân (Phần 1)', 2, 'Free', 45, 'Phim truyền hình về cuộc đời đầy thử thách của Phương Nam, người luôn khát khao tình cảm gia đình.', 'Nguyễn Danh Dũng', 'Phương Oanh, Mạnh Trường, Công Lý', 'data/phim/phimbo/huong_vi_tinh_than_p1', 'https://example.com/hvtt-trailer.mp4', 'data/img/huong_vi_tinh_than_p1_img.jpg', 'Chiếu online', 8.4, '2025-11-25 09:25:05', 'published', NULL, NULL, 0, 'data/img/huong_vi_tinh_than_p1.jpg', 'Việt Nam', 'Tiếng Việt', 'P', 'phimbo', NULL, 90000, 120000, 180000),
(29, 'Về Nhà Đi Con', 2, 'Gold', 45, 'Phim về tình cảm gia đình, đặc biệt là tình cha và ba cô con gái có tính cách khác nhau.', 'Nguyễn Danh Dũng', 'NSND Hoàng Dũng, Thu Quỳnh, Bảo Thanh, Bảo Hân', 'data/phim/phimbo/venhadicon', 'https://example.com/vndc-trailer.mp4', 'data/img/venhadicon_img.webp', 'Chiếu online', 9, '2025-11-25 09:25:05', 'published', NULL, NULL, 0, 'data/img/venhadicon.jpg', 'Việt Nam', 'Tiếng Việt', 'P', 'phimbo', NULL, 90000, 120000, 180000),
(30, 'Người Phán Xử', 1, 'Premium', 45, 'Ông trùm Phan Quân và những cuộc chiến tranh giành quyền lực trong thế giới ngầm.', 'Nguyễn Mai Hiền, Nguyễn Khải Anh, Bùi Quốc Việt', 'NSND Hoàng Dũng, Việt Anh, Hồng Đăng', 'data/phim/phimbo/nguoi_phan_xu', 'https://example.com/npx-trailer.mp4', 'data/img/nguoi_phan_xu_img.jpg', 'Chiếu online', NULL, '2025-11-25 09:25:05', 'published', NULL, NULL, 0, 'data/img/nguoi_phan_xu.webp', 'Việt Nam', 'Tiếng Việt', 'C18', 'phimbo', NULL, 90000, 120000, 180000),
(31, 'Sống Chung Với Mẹ Chồng', 2, 'Gold', 45, 'Những mâu thuẫn nảy sinh khi nàng dâu và mẹ chồng sống chung dưới một mái nhà.', 'Vũ Trường Khoa', 'NSND Lan Hương, Bảo Thanh, Anh Dũng', 'data/phim/phimbo/song_chung_voi_me_chong', 'https://example.com/scvmc-trailer.mp4', 'data/img/song_chung_voi_me_chong_img.jpg', 'Chiếu online', 8.1, '2025-11-25 09:25:05', 'published', NULL, NULL, 0, 'data/img/song_chung_voi_me_chong.jpg', 'Việt Nam', 'Tiếng Việt', 'P', 'phimbo', NULL, 90000, 120000, 180000),
(32, 'Thương Ngày Nắng Về (Phần 2)', 2, 'Premium', 45, 'Câu chuyện về bà mẹ đơn thân cùng ba cô con gái, xoay quanh tình yêu, sự nghiệp và những mâu thuẫn.', 'Bùi Tiến Huy', 'NSƯT Thanh Quý, Phan Minh Huyền, Lan Phương', 'data/phim/phimbo/thuong_ngay_nang_ve_p2', 'https://example.com/tnnv-trailer.mp4', 'data/img/thuong_ngay_nang_ve_img.jpg', 'Chiếu online', 8.5, '2025-11-25 09:25:05', 'published', NULL, NULL, 0, 'data/img/thuong_ngay_nang_ve.jpg', 'Việt Nam', 'Tiếng Việt', 'P', 'phimbo', NULL, 90000, 120000, 180000),
(33, 'Phim ngắn: 20 Năm 20 Món Ăn', 5, 'Free', 15, 'Loạt phim ngắn ẩm thực về hành trình tìm kiếm hương vị đã mất sau 20 năm xa quê.', 'Nguyễn Hoàng Điệp', 'Nhiều diễn viên', 'data/phim/phimle/20_nam_20_mon_an.mp4', 'https://example.com/20nam-trailer.mp4', 'data/img/20_nam.webp', 'Chiếu online', 7, '2025-11-25 09:25:05', 'published', NULL, NULL, 0, NULL, 'Việt Nam', 'Tiếng Việt', 'P', 'phimle', NULL, 90000, 120000, 180000),
(34, 'Series: Ai Là Hung Thủ (Mùa 1)', 1, 'Gold', 40, 'Series trinh thám điều tra các vụ án mạng phức tạp tại thành phố Hồ Chí Minh.', 'Lý Hải (Đóng vai trò sản xuất)', 'Trương Thế Vinh, Nhan Phúc Vinh', 'data/phim/phimbo/ai_la_hung_thu_s1', 'https://example.com/alht-trailer.mp4', 'data/img/ai_la_hung_thu_img.jpg', 'Chiếu online', 7.9, '2025-11-25 09:25:05', 'published', NULL, NULL, 0, 'data/img/ai_la_hung_thu.jpg', 'Việt Nam', 'Tiếng Việt', 'C16', 'phimbo', NULL, 90000, 120000, 180000),
(35, 'Phim lẻ: Cô Ba Sài Gòn', 2, 'Premium', NULL, 'Phim lãng mạn, giả tưởng về thời trang áo dài và câu chuyện xuyên không giữa hai thế hệ.', 'Trần Bửu Lộc, Nguyễn Lê Minh', 'Ninh Dương Lan Ngọc, Diễm My 9x, Ngô Thanh Vân', 'data/phim/phimle/co_ba_sai_gon.mp4', 'https://example.com/cbsg-trailer.mp4', 'data/img/co_ba_sai_gon_img.jpg', 'Chiếu online', 7.6, '2025-11-25 09:25:05', 'published', NULL, NULL, 0, 'data/img/co_ba_sai_gon.jpg', 'Việt Nam', 'Tiếng Việt', 'C13', 'phimle', NULL, 90000, 120000, 180000),
(36, 'Series: Tình Yêu và Tham Vọng', 7, 'Gold', 45, 'Phim thương trường với những cuộc chiến khốc liệt giữa các tập đoàn và câu chuyện tình yêu phức tạp.', 'Bùi Tiến Huy', 'Nhan Phúc Vinh, Diễm My 9x, Lã Thanh Huyền', 'data/phim/phimbo/tinh_yeu_va_tham_vong', 'https://example.com/tyvtc-trailer.mp4', 'data/img/tinh_yeu_va_tham_vong_img.jpg', 'Chiếu online', 7.8, '2025-11-25 09:25:05', 'published', NULL, NULL, 0, 'data/img/tinh_yeu_va_tham_vong.jpg', 'Việt Nam', 'Tiếng Việt', 'P', 'phimbo', NULL, 90000, 120000, 180000),
(37, 'Phim ngắn: Ngã Ba Đồng Lộc', 7, 'Free', 30, 'Phim tài liệu/chiến tranh về sự hy sinh anh dũng của 10 cô gái thanh niên xung phong.', 'Nguyễn Minh Chung', 'Nhiều diễn viên', 'data/phim/phimle/nga_ba_dong_loc.mp4', 'https://example.com/nbdl-trailer.mp4', 'data/img/nga_ba_dong_loc_img.jpg', 'Chiếu online', 8.2, '2025-11-25 09:25:05', 'published', NULL, NULL, 0, 'data/img/nga_ba_dong_loc.jpg', 'Việt Nam', 'Tiếng Việt', 'P', 'phimle', NULL, 90000, 120000, 180000),
(38, 'hheheh', 1, 'Free', 124, 'sdd', '', 'fgd', '', '', 'data/img/hai_phuong_img.jpg', 'Chiếu rạp', 0, '2025-12-04 02:26:18', 'published', NULL, NULL, 0, 'data/img/hai_phuong.jpg', 'fdg', 'dfg', 'p', 'phimle', NULL, 90000, 120000, 180000),
(51, 'Phàm Nhân Tu Tiên', 1, 'Free', NULL, 'Khúc Hồn thành công kết đan! hai người ở Loạn Tinh hải còn gặp được cơ duyên gì gì? “Tinh Hải Phi Trì” , xin đợi trở về\r\nĐồng thời Manhua cũng đang trong quá trình khẩn trương remake, ngày nào đó sẽ gặp lại', '', 'Hàn Lập, ', 'data/storage/data/phim/phimbo/phamnhantutien/', '', 'data/img/posters/Pha__m_Nh__n_Tu_Ti__n_poster_1765762815.jpg', 'Chiếu online', 0, '2025-12-15 01:40:15', 'draft', NULL, NULL, 0, 'data/img/banners/Pha__m_Nh__n_Tu_Ti__n_banner_1765762815.webp', 'Trung Quốc', '', '12', 'phimbo', NULL, 90000, 120000, 180000),
(52, 'mưa đỏ', NULL, 'Gold', 120, 'abc', 'Vince Gilligan', 'Hàn Lập, ', 'data/phim/phimle/m__a___o__.mp4', 'https://www.youtube.com/watch?v=BD6PoZJdt_M', 'data/img/posters/m__a___o___poster_1769519309.jpg', 'Chiếu rạp', 0, '2025-12-15 03:22:07', 'draft', NULL, NULL, 0, 'data/img/banners/m__a___o___banner_1769519309.jpg', 'viet nam', 'tieng viet', '16', 'phimle', NULL, 90000, 120000, 180000),
(53, 'Tiên nghịch', 1, 'Silver', NULL, '', 'Hàn Lập', 'Vương Lâm', '', 'data/phim/trailers/Ti__n_ngh___ch_trailer_1770606401.mp4', 'data/img/tiennghich.webp', 'Chiếu online', 0, '2026-02-09 03:06:41', 'published', NULL, NULL, 0, 'data/img/tiennghich.webp', 'Trung Quốc', 'Tiếng Trung, Tiếng Việt', '20', 'phimbo', NULL, 90000, 120000, 180000);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `movie_categories`
--

CREATE TABLE `movie_categories` (
  `id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `movie_categories`
--

INSERT INTO `movie_categories` (`id`, `movie_id`, `category_id`, `created_at`) VALUES
(1, 1, 1, '2025-12-14 13:34:32'),
(3, 14, 1, '2025-12-14 13:34:32'),
(4, 18, 1, '2025-12-14 13:34:32'),
(5, 30, 1, '2025-12-14 13:34:32'),
(6, 34, 1, '2025-12-14 13:34:32'),
(8, 2, 2, '2025-12-14 13:34:32'),
(9, 12, 2, '2025-12-14 13:34:32'),
(10, 13, 2, '2025-12-14 13:34:32'),
(11, 19, 2, '2025-12-14 13:34:32'),
(12, 24, 2, '2025-12-14 13:34:32'),
(13, 28, 2, '2025-12-14 13:34:32'),
(14, 29, 2, '2025-12-14 13:34:32'),
(15, 31, 2, '2025-12-14 13:34:32'),
(16, 32, 2, '2025-12-14 13:34:32'),
(17, 35, 2, '2025-12-14 13:34:32'),
(18, 3, 3, '2025-12-14 13:34:32'),
(19, 15, 3, '2025-12-14 13:34:32'),
(20, 16, 3, '2025-12-14 13:34:32'),
(21, 20, 3, '2025-12-14 13:34:32'),
(22, 21, 3, '2025-12-14 13:34:32'),
(23, 23, 3, '2025-12-14 13:34:32'),
(24, 25, 3, '2025-12-14 13:34:32'),
(25, 4, 4, '2025-12-14 13:34:32'),
(26, 10, 4, '2025-12-14 13:34:32'),
(27, 22, 4, '2025-12-14 13:34:32'),
(28, 5, 5, '2025-12-14 13:34:32'),
(29, 27, 5, '2025-12-14 13:34:32'),
(30, 33, 5, '2025-12-14 13:34:32'),
(31, 6, 6, '2025-12-14 13:34:32'),
(32, 11, 6, '2025-12-14 13:34:32'),
(33, 7, 7, '2025-12-14 13:34:32'),
(34, 8, 7, '2025-12-14 13:34:32'),
(35, 17, 7, '2025-12-14 13:34:32'),
(36, 26, 7, '2025-12-14 13:34:32'),
(37, 36, 7, '2025-12-14 13:34:32'),
(38, 37, 7, '2025-12-14 13:34:32'),
(64, 38, 1, '2025-12-14 14:09:10'),
(65, 38, 2, '2025-12-14 14:09:10'),
(68, 51, 1, '2025-12-15 01:40:44'),
(69, 51, 5, '2025-12-15 01:40:44'),
(71, 9, 1, '2025-12-15 02:43:45'),
(74, 53, 1, '2026-02-09 03:07:26'),
(75, 53, 5, '2026-02-09 03:07:26');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'info',
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `title`, `message`, `link`, `is_read`, `created_at`) VALUES
(1, 9, 'warning', 'Yêu cầu thay đổi quyền thành viên', 'Admin Admin Mới muốn thay đổi quyền của jack thành Moderator cho rạp Lotte Cinema. Vui lòng xem xét và phê duyệt.', '?route=moderator/permissionRequests&id=1', 1, '2025-11-30 17:57:49'),
(2, 9, 'success', 'Đặt vé thành công', 'Bạn đã đặt thành công 3 vé xem phim \"Bố Già\" tại ghế E7, E8, E9. Vé và QR code đã được gửi đến email của bạn.', '?route=profile/index', 1, '2025-11-30 18:12:26'),
(3, 9, 'success', 'Đặt vé thành công', 'Bạn đã đặt thành công 2 vé xem phim \"Bố Già\" tại ghế G7, G8. Vé và QR code đã được gửi đến email của bạn.', '?route=booking/myTickets', 1, '2025-12-02 01:47:59'),
(4, 9, 'success', 'Đặt vé thành công', 'Bạn đã đặt thành công 2 vé xem phim \"Bố Già\" tại ghế F7, F8. Vé và QR code đã được gửi đến email của bạn. Bạn có thể tải vé PDF tại đây.', '?route=booking/myTickets', 1, '2025-12-02 02:13:55'),
(5, 9, 'success', 'Đặt vé thành công', 'Bạn đã đặt thành công 2 vé xem phim \"Bố Già\" tại ghế E7, E8. QR code đã được tạo, bạn có thể xem tại trang \'Vé của tôi\'.', '?route=booking/myTickets', 1, '2025-12-02 03:38:42'),
(6, 9, 'success', 'Đặt vé thành công', 'Bạn đã đặt thành công 2 vé xem phim \"Mắt Biếc\" tại ghế I5, I6. QR code đã được tạo, bạn có thể xem tại trang \'Vé của tôi\'.', '?route=booking/myTickets', 1, '2025-12-02 03:58:40'),
(7, 9, 'success', 'Đặt vé thành công', 'Bạn đã đặt thành công 3 vé xem phim \"Hai Phượng\" tại ghế I6, I7, I8. QR code đã được tạo, bạn có thể xem tại trang \'Vé của tôi\'.', '?route=booking/myTickets', 1, '2025-12-02 04:10:00'),
(8, 9, 'success', 'Đặt vé thành công', 'Bạn đã đặt thành công 3 vé xem phim \"Avengers: Endgame\" tại ghế G4, G5, G6. QR code đã được tạo, bạn có thể xem tại trang \'Vé của tôi\'.', '?route=booking/myTickets', 1, '2025-12-02 04:16:43'),
(9, 9, 'success', 'Đặt vé thành công', 'Bạn đã đặt thành công 3 vé xem phim \"Bố Già\" tại ghế I4, I5, I6. QR code đã được tạo, bạn có thể xem tại trang \'Vé của tôi\'.', '?route=booking/myTickets', 1, '2025-12-03 01:45:39'),
(10, 1, 'success', 'Đặt vé thành công', 'Bạn đã đặt thành công 1 vé xem phim \"Hai Phượng\" tại ghế I6. QR code đã được tạo, bạn có thể xem tại trang \'Vé của tôi\'.', '?route=booking/myTickets', 1, '2025-12-04 03:18:57'),
(11, 1, 'success', 'Đặt vé thành công', 'Bạn đã đặt thành công 1 vé xem phim \"hheheh\" tại ghế H6. QR code đã được tạo, bạn có thể xem tại trang \'Vé của tôi\'.', '?route=booking/myTickets', 1, '2025-12-04 04:07:19'),
(12, 1, 'success', 'Đặt vé thành công', 'Bạn đã đặt thành công 2 vé xem phim \"hheheh\" tại ghế L5, L6. QR code đã được tạo, bạn có thể xem tại trang \'Vé của tôi\'.', '?route=booking/myTickets', 1, '2025-12-04 04:20:58'),
(13, 1, 'success', 'Đặt vé thành công', 'Bạn đã đặt thành công 1 vé xem phim \"Tấm Cám: Chuyện Chưa Kể\" tại ghế H7. QR code đã được tạo, bạn có thể xem tại trang \'Vé của tôi\'.', '?route=booking/myTickets', 1, '2025-12-04 13:57:34'),
(14, 16, 'success', 'Đặt vé thành công', 'Bạn đã đặt thành công 8 vé xem phim \"Avengers: Endgame\" tại ghế F3, F4, F5, F6, F7, F8, F9, F10. QR code đã được tạo, bạn có thể xem tại trang \'Vé của tôi\'.', '?route=booking/myTickets', 1, '2025-12-04 18:35:41'),
(15, 16, 'success', 'Đặt vé thành công', 'Bạn đã đặt thành công 8 vé xem phim \"Em Chưa 18\" tại ghế C7, D7, E7, F7, G7, H7, I7, J7. QR code đã được tạo, bạn có thể xem tại trang \'Vé của tôi\'.', '?route=booking/myTickets', 1, '2025-12-04 18:39:25'),
(16, 9, 'warning', 'Yêu cầu thay đổi quyền thành viên', 'Admin Admin Mới muốn thay đổi quyền của Thanh thành Moderator cho rạp Lotte Cinema. Vui lòng xem xét và phê duyệt.', '?route=moderator/permissionRequests&id=2', 1, '2025-12-04 18:40:04'),
(17, 3, 'error', 'Yêu cầu bị từ chối', 'Yêu cầu thay đổi quyền của Thanh đã bị từ chối bởi moderator của rạp.', '?route=admin/users', 0, '2025-12-04 18:41:27'),
(18, 9, 'success', 'Đặt vé thành công', 'Bạn đã đặt thành công 2 vé xem phim \"The Conjuring\" tại ghế H5, H6. QR code đã được tạo, bạn có thể xem tại trang \'Vé của tôi\'.', '?route=booking/myTickets', 1, '2025-12-06 15:23:29'),
(19, 9, 'success', 'Đặt vé thành công', 'Bạn đã đặt thành công 5 vé xem phim \"Toy Story 4\" tại ghế G5, G6, G7, G8, G9. QR code đã được tạo, bạn có thể xem tại trang \'Vé của tôi\'.', '?route=booking/myTickets', 1, '2025-12-06 16:22:45'),
(20, 1, 'success', 'Đặt vé thành công', 'Bạn đã đặt thành công 2 vé xem phim \"Avengers: Endgame\" tại ghế C4, C5. QR code đã được tạo, bạn có thể xem tại trang \'Vé của tôi\'.', '?route=booking/myTickets', 1, '2025-12-06 17:20:33'),
(21, 3, 'error', 'Yêu cầu bị từ chối', 'Yêu cầu thay đổi quyền của jack đã bị từ chối bởi moderator của rạp.', '?route=admin/users', 0, '2025-12-07 14:37:29'),
(22, 1, 'success', 'Đặt vé thành công', 'Bạn đã đặt thành công 2 vé xem phim \"Avengers: Endgame\" tại ghế H3, H4. QR code đã được tạo, bạn có thể xem tại trang \'Vé của tôi\'.', '?route=booking/myTickets', 1, '2025-12-08 04:05:25'),
(23, 9, 'success', 'Đặt vé thành công', 'Bạn đã đặt thành công 3 vé xem phim \"Bố Già\" tại ghế I5, I6, I7. QR code đã được tạo, bạn có thể xem tại trang \'Vé của tôi\'.', '?route=booking/myTickets', 1, '2025-12-08 19:03:06'),
(24, 9, 'success', 'Đặt vé thành công', 'Bạn đã đặt thành công 2 vé xem phim \"Avengers: Endgame\" tại ghế F3, F4. QR code đã được tạo, bạn có thể xem tại trang \'Vé của tôi\'.', '?route=booking/myTickets', 1, '2025-12-08 19:04:25'),
(25, 9, 'success', 'Đặt vé thành công', 'Bạn đã đặt thành công 2 vé xem phim \"Avengers: Endgame\" tại ghế F1, F2. QR code đã được tạo, bạn có thể xem tại trang \'Vé của tôi\'.', '?route=booking/myTickets', 1, '2025-12-08 19:17:15'),
(26, 9, 'success', 'Đặt vé thành công', 'Bạn đã đặt thành công 3 vé xem phim \"Avengers: Endgame\" tại ghế G3, G4, G5. QR code đã được tạo, bạn có thể xem tại trang \'Vé của tôi\'.', '?route=booking/myTickets', 1, '2025-12-09 00:32:15'),
(27, 21, 'info', 'Có người trả lời comment của bạn', 'vanlinh đã trả lời comment của bạn về phim \"Phim ngắn: 20 Năm 20 Món Ăn\"', '?route=movie/watch&id=33', 1, '2025-12-09 01:56:22'),
(28, 9, 'info', 'Có người trả lời comment của bạn', 'Lotte đã trả lời comment của bạn về phim \"Phim ngắn: 20 Năm 20 Món Ăn\"', '?route=movie/watch&id=33', 1, '2025-12-09 01:59:39'),
(29, 25, 'success', 'Đặt vé thành công', 'Bạn đã đặt thành công 2 vé xem phim \"Bố Già\" tại ghế L7, L8. QR code đã được tạo, bạn có thể xem tại trang \'Vé của tôi\'.', '?route=booking/myTickets', 1, '2025-12-09 03:36:07'),
(30, 26, 'success', 'Nạp điểm thành công', 'Bạn đã nạp thành công 10.000.000 điểm vào tài khoản.', '?route=profile/index', 1, '2025-12-09 03:41:56'),
(31, 26, 'info', 'Có người trả lời comment của bạn', 'vanlinh đã trả lời comment của bạn về phim \"Game of Thrones\"', '?route=movie/watch&id=8', 1, '2025-12-09 03:44:16'),
(32, 3, 'success', 'Đặt vé thành công', 'Bạn đã đặt thành công 3 vé xem phim \"Bố Già\" tại ghế H3, H4, H5. QR code đã được tạo, bạn có thể xem tại trang \'Vé của tôi\'.', '?route=booking/myTickets', 1, '2025-12-09 04:57:37'),
(33, 27, 'success', 'Đặt vé thành công', 'Bạn đã đặt thành công 2 vé xem phim \"Avengers: Endgame\" tại ghế C7, C8. QR code đã được tạo, bạn có thể xem tại trang \'Vé của tôi\'.', '?route=booking/myTickets', 0, '2025-12-09 11:48:06'),
(34, 27, 'success', 'Đặt vé thành công', 'Bạn đã đặt thành công 8 vé xem phim \"Avengers: Endgame\" tại ghế A1, A2, A3, A4, A5, A6, A7, A8. QR code đã được tạo, bạn có thể xem tại trang \'Vé của tôi\'.', '?route=booking/myTickets', 0, '2025-12-09 11:54:01'),
(35, 9, 'success', 'Đặt vé thành công', 'Bạn đã đặt thành công 2 vé xem phim \"Bố Già\" tại ghế I4, I5. QR code đã được tạo, bạn có thể xem tại trang \'Vé của tôi\'.', '?route=booking/myTickets', 1, '2025-12-09 15:29:37'),
(36, 25, 'success', 'Đặt vé thành công', 'Bạn đã đặt thành công 2 vé xem phim \"Bố Già\" tại ghế I2, I3. QR code đã được tạo, bạn có thể xem tại trang \'Vé của tôi\'.', '?route=booking/myTickets', 0, '2025-12-09 15:31:18'),
(37, 3, 'success', 'Đặt vé thành công', 'Bạn đã đặt thành công 2 vé xem phim \"Mắt Biếc\" tại ghế L8, L9. QR code đã được tạo, bạn có thể xem tại trang \'Vé của tôi\'.', '?route=booking/myTickets', 1, '2025-12-10 07:31:04'),
(38, 9, 'success', 'Đặt vé thành công', 'Bạn đã đặt thành công 4 vé xem phim \"Bố Già\" tại ghế I1, I2, I3, I4. QR code đã được tạo, bạn có thể xem tại trang \'Vé của tôi\'.', '?route=booking/myTickets', 1, '2025-12-10 07:49:42'),
(39, 9, 'success', 'Đặt vé thành công', 'Bạn đã đặt thành công 5 vé xem phim \"Gái Già Lắm Chiêu 3\" tại ghế H1, H2, H3, H4, H5. QR code đã được tạo, bạn có thể xem tại trang \'Vé của tôi\'.', '?route=booking/myTickets', 1, '2025-12-10 08:42:34'),
(40, 9, 'success', 'Đặt vé thành công', 'Bạn đã đặt thành công 3 vé xem phim \"Avengers: Endgame\" tại ghế H4, H5, H6. QR code đã được tạo, bạn có thể xem tại trang \'Vé của tôi\'.', '?route=booking/myTickets', 1, '2025-12-10 08:55:52'),
(41, 10, 'success', 'Đặt vé thành công', 'Bạn đã đặt thành công 2 vé xem phim \"Em Chưa 18\" tại ghế J6, J7. QR code đã được tạo, bạn có thể xem tại trang \'Vé của tôi\'.', '?route=booking/myTickets', 1, '2025-12-12 02:05:25'),
(42, 22, 'success', 'Đặt vé thành công', 'Bạn đã đặt thành công 2 vé xem phim \"Bố Già\" tại ghế E1, E2. QR code đã được tạo, bạn có thể xem tại trang \'Vé của tôi\'.', '?route=booking/myTickets', 1, '2025-12-15 03:06:58'),
(43, 10, 'success', 'Đặt vé thành công', 'Bạn đã đặt thành công 2 vé xem phim \"Em Chưa 18\" tại ghế G5, G6. QR code đã được tạo, bạn có thể xem tại trang \'Vé của tôi\'.', '?route=booking/myTickets', 1, '2025-12-27 18:24:09'),
(44, 9, 'success', 'Đặt vé thành công', 'Bạn đã đặt thành công 2 vé xem phim \"mưa đỏ\" tại ghế I3, I4. QR code đã được tạo, bạn có thể xem tại trang \'Vé của tôi\'.', '?route=booking/myTickets', 1, '2025-12-29 14:37:36'),
(45, 9, 'success', 'Nạp điểm thành công', 'Bạn đã nạp thành công 10.000.000 điểm vào tài khoản.', '?route=profile/index', 0, '2026-02-04 07:14:59');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `module` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `description`, `module`, `created_at`) VALUES
(1, 'users.view', 'Xem danh sách người dùng', 'users', '2025-11-10 16:41:17'),
(2, 'users.create', 'Tạo người dùng mới', 'users', '2025-11-10 16:41:17'),
(3, 'users.edit', 'Sửa thông tin người dùng', 'users', '2025-11-10 16:41:17'),
(4, 'users.delete', 'Xóa người dùng', 'users', '2025-11-10 16:41:17'),
(5, 'users.block', 'Chặn/Mở khóa người dùng', 'users', '2025-11-10 16:41:17'),
(6, 'users.reset_password', 'Reset mật khẩu', 'users', '2025-11-10 16:41:17'),
(7, 'movies.view', 'Xem danh sách phim', 'movies', '2025-11-10 16:41:17'),
(8, 'movies.create', 'Thêm phim mới', 'movies', '2025-11-10 16:41:17'),
(9, 'movies.edit', 'Sửa thông tin phim', 'movies', '2025-11-10 16:41:17'),
(10, 'movies.delete', 'Xóa phim', 'movies', '2025-11-10 16:41:17'),
(11, 'movies.publish', 'Xuất bản phim', 'movies', '2025-11-10 16:41:17'),
(12, 'bookings.view', 'Xem đặt vé', 'bookings', '2025-11-10 16:41:17'),
(13, 'bookings.create', 'Tạo vé thủ công', 'bookings', '2025-11-10 16:41:17'),
(14, 'bookings.edit', 'Sửa vé', 'bookings', '2025-11-10 16:41:17'),
(15, 'bookings.cancel', 'Hủy vé', 'bookings', '2025-11-10 16:41:17'),
(16, 'bookings.refund', 'Hoàn tiền', 'bookings', '2025-11-10 16:41:17'),
(17, 'theaters.view', 'Xem rạp', 'theaters', '2025-11-10 16:41:17'),
(18, 'theaters.create', 'Thêm rạp', 'theaters', '2025-11-10 16:41:17'),
(19, 'theaters.edit', 'Sửa rạp', 'theaters', '2025-11-10 16:41:17'),
(20, 'theaters.delete', 'Xóa rạp', 'theaters', '2025-11-10 16:41:17'),
(21, 'analytics.view', 'Xem báo cáo', 'analytics', '2025-11-10 16:41:17'),
(22, 'analytics.export', 'Xuất báo cáo', 'analytics', '2025-11-10 16:41:17'),
(23, 'system.config', 'Cấu hình hệ thống', 'system', '2025-11-10 16:41:17'),
(24, 'system.logs', 'Xem logs', 'system', '2025-11-10 16:41:17'),
(25, 'support.view', 'Xem ticket', 'support', '2025-11-10 16:41:17'),
(26, 'support.assign', 'Gán ticket', 'support', '2025-11-10 16:41:17'),
(27, 'support.resolve', 'Giải quyết ticket', 'support', '2025-11-10 16:41:17');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `promotions`
--

CREATE TABLE `promotions` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `type` enum('discount','bundle','free_trial') NOT NULL,
  `discount_value` decimal(10,2) DEFAULT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `status` enum('draft','active','ended') DEFAULT 'draft',
  `target_audience` enum('all','new_users','premium') DEFAULT 'all',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `promotions`
--

INSERT INTO `promotions` (`id`, `name`, `description`, `type`, `discount_value`, `start_date`, `end_date`, `status`, `target_audience`, `created_at`) VALUES
(1, 'Khuyến mãi Black Friday', 'Giảm giá lớn nhân dịp Black Friday', 'discount', 30.00, '2025-11-20 00:00:00', '2025-11-30 23:59:59', 'draft', 'all', '2025-11-12 07:41:09'),
(2, 'Gói Premium ưu đãi', 'Mua gói Premium được tặng thêm 1 tháng', 'bundle', 0.00, '2025-11-01 00:00:00', '2025-12-31 23:59:59', 'active', 'all', '2025-11-12 07:41:09'),
(3, 'Dùng thử miễn phí', '7 ngày dùng thử miễn phí cho người dùng mới', 'free_trial', 0.00, '2025-11-01 00:00:00', '2026-01-31 23:59:59', 'active', 'new_users', '2025-11-12 07:41:09'),
(4, 'Giảm giá cuối tuần', 'Giảm 15% cho tất cả gói dịch vụ cuối tuần', 'discount', 15.00, '2025-11-15 00:00:00', '2025-12-31 23:59:59', 'active', 'all', '2025-11-12 07:41:09'),
(5, 'Ưu đãi thành viên Premium', 'Thành viên Premium được giảm thêm 10%', 'discount', 10.00, '2025-11-01 00:00:00', '2026-12-31 23:59:59', 'active', 'premium', '2025-11-12 07:41:09');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `rating` tinyint(4) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_pinned` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `movie_id`, `rating`, `comment`, `created_at`, `is_pinned`) VALUES
(1, 1, 1, 5, 'Phim tuyệt vời! Diễn xuất xuất sắc và cốt truyện hấp dẫn.', '2025-11-12 07:41:09', 0),
(2, 2, 2, 5, 'Titanic là một kiệt tác điện ảnh, tình yêu vĩnh cửu.', '2025-11-12 07:41:09', 0),
(3, 3, 3, 4, 'Phim hài rất vui nhộn, giải trí tốt.', '2025-11-12 07:41:09', 0),
(4, 4, 4, 4, 'Kinh dị đúng nghĩa, rùng rợn từ đầu đến cuối.', '2025-11-12 07:41:09', 0),
(5, 5, 5, 5, 'Hoạt hình hay, phù hợp cho cả gia đình.', '2025-11-12 07:41:09', 0),
(6, 1, 6, 5, 'Interstellar là một tác phẩm khoa học viễn tưởng xuất sắc.', '2025-11-12 07:41:09', 0),
(7, 2, 7, 4, 'Cuộc phiêu lưu thú vị với Indiana Jones.', '2025-11-12 07:41:09', 0),
(8, 9, 1, 1, 'phim hay qua\r\n', '2025-11-19 03:15:34', 0),
(9, 9, 1, 1, 'ok đó\r\n', '2025-12-04 19:53:53', 0),
(10, 9, 35, 5, '', '2025-12-04 20:12:53', 0),
(11, 21, 33, 5, '', '2025-12-09 01:33:22', 0),
(12, 21, 28, 5, '', '2025-12-09 01:35:25', 0),
(13, 26, 8, 1, '', '2025-12-09 03:43:08', 0),
(14, 3, 53, 5, '', '2026-02-09 03:08:02', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Super Admin', 'Quyền cao nhất, toàn quyền hệ thống', '2025-11-10 16:41:17'),
(2, 'Admin', 'Quản trị viên, quản lý nội dung và người dùng', '2025-11-10 16:41:17'),
(3, 'Moderator', 'Quản lý từng rạp, quản lý lịch chiếu và vé của rạp được gán', '2025-11-10 16:41:17'),
(4, 'Content Manager', 'Quản lý nội dung phim', '2025-11-10 16:41:17'),
(5, 'Support Staff', 'Nhân viên hỗ trợ khách hàng', '2025-11-10 16:41:17'),
(6, 'Theater Manager', 'Quản lý rạp, quản lý lịch chiếu, bán vé và phim của rạp', '2025-11-10 16:41:17'),
(7, 'Admin Rạp', 'Quản lý rạp, chỉ có 1 tài khoản duy nhất cho mỗi rạp', '2025-12-06 14:43:25'),
(8, 'Nhân viên đứng quầy', 'Nhân viên đứng quầy, được tạo bởi Admin Rạp hoặc Super Admin', '2025-12-06 14:43:25');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `role_permissions`
--

INSERT INTO `role_permissions` (`id`, `role_id`, `permission_id`, `created_at`) VALUES
(1, 1, 22, '2025-11-10 16:41:17'),
(2, 1, 21, '2025-11-10 16:41:17'),
(3, 1, 15, '2025-11-10 16:41:17'),
(6, 1, 16, '2025-11-10 16:41:17'),
(10, 1, 9, '2025-11-10 16:41:17'),
(12, 1, 7, '2025-11-10 16:41:17'),
(13, 1, 26, '2025-11-10 16:41:17'),
(14, 1, 27, '2025-11-10 16:41:17'),
(15, 1, 25, '2025-11-10 16:41:17'),
(16, 1, 23, '2025-11-10 16:41:17'),
(17, 1, 24, '2025-11-10 16:41:17'),
(18, 1, 18, '2025-11-10 16:41:17'),
(19, 1, 20, '2025-11-10 16:41:17'),
(20, 1, 19, '2025-11-10 16:41:17'),
(21, 1, 17, '2025-11-10 16:41:17'),
(22, 1, 5, '2025-11-10 16:41:17'),
(23, 1, 2, '2025-11-10 16:41:17'),
(24, 1, 4, '2025-11-10 16:41:17'),
(25, 1, 3, '2025-11-10 16:41:17'),
(26, 1, 6, '2025-11-10 16:41:17'),
(27, 1, 1, '2025-11-10 16:41:17');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `seat_reservations`
--

CREATE TABLE `seat_reservations` (
  `id` int(11) NOT NULL,
  `showtime_id` int(11) NOT NULL,
  `seat` varchar(10) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `reserved_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `seat_selection_logs`
--

CREATE TABLE `seat_selection_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `showtime_id` int(11) NOT NULL,
  `seat_count` int(11) NOT NULL,
  `seats` text DEFAULT NULL,
  `is_spam` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `seat_selection_logs`
--

INSERT INTO `seat_selection_logs` (`id`, `user_id`, `ip_address`, `showtime_id`, `seat_count`, `seats`, `is_spam`, `created_at`) VALUES
(119, 10, '42.118.61.65', 1005, 2, '[\"H3\",\"H4\"]', 0, '2025-12-08 04:04:57'),
(120, 9, '117.2.114.182', 41713, 3, '[\"I5\",\"I6\",\"I7\"]', 0, '2025-12-08 19:01:57'),
(121, 9, '117.2.114.182', 1008, 2, '[\"F3\",\"F4\"]', 0, '2025-12-08 19:04:03'),
(122, 9, '117.2.114.182', 1009, 2, '[\"F1\",\"F2\"]', 0, '2025-12-08 19:15:34'),
(123, 9, '42.118.61.65', 1010, 3, '[\"G3\",\"G4\",\"G5\"]', 0, '2025-12-09 00:30:01'),
(124, 9, '117.2.114.199', 41716, 3, '[\"H2\",\"H3\",\"H4\"]', 0, '2025-12-09 00:46:13'),
(125, 9, '117.2.114.199', 41716, 3, '[\"G6\",\"G7\",\"G8\"]', 0, '2025-12-09 00:46:24'),
(126, 25, '42.118.61.65', 41726, 2, '[\"L7\",\"L8\"]', 0, '2025-12-09 03:35:11'),
(127, 3, '59.153.231.212', 41727, 3, '[\"H3\",\"H4\",\"H5\"]', 0, '2025-12-09 04:56:53'),
(128, 27, '1.55.108.99', 1004, 2, '[\"C4\",\"C5\"]', 0, '2025-12-09 11:36:01'),
(129, 27, '1.55.108.99', 1001, 2, '[\"B5\",\"B6\"]', 0, '2025-12-09 11:38:07'),
(130, 27, '1.55.108.99', 1001, 2, '[\"B5\",\"B6\"]', 0, '2025-12-09 11:39:02'),
(131, 27, '1.55.108.99', 1001, 2, '[\"C5\",\"C6\"]', 0, '2025-12-09 11:42:02'),
(132, 27, '1.55.108.99', 1001, 3, '[\"H7\",\"L3\",\"L4\"]', 0, '2025-12-09 11:44:10'),
(133, 27, '1.55.108.99', 1005, 2, '[\"C7\",\"C8\"]', 0, '2025-12-09 11:47:19'),
(134, 27, '1.55.108.99', 1005, 8, '[\"A1\",\"A2\",\"A3\",\"A4\",\"A5\",\"A6\",\"A7\",\"A8\"]', 0, '2025-12-09 11:53:19'),
(135, 25, '116.97.107.168', 41728, 2, '[\"K10\",\"K12\"]', 0, '2025-12-09 15:20:44'),
(136, 25, '116.97.107.168', 41728, 2, '[\"K1\",\"K12\"]', 0, '2025-12-09 15:20:59'),
(137, 25, '116.97.107.168', 41728, 2, '[\"C1\",\"E12\"]', 0, '2025-12-09 15:21:24'),
(138, 25, '116.97.107.168', 41728, 3, '[\"E12\",\"K10\",\"K11\"]', 0, '2025-12-09 15:22:33'),
(139, 25, '116.97.107.168', 41728, 3, '[\"E12\",\"K10\",\"K11\"]', 0, '2025-12-09 15:22:46'),
(140, 9, '103.156.42.11', 41728, 2, '[\"F10\",\"F11\"]', 0, '2025-12-09 15:22:55'),
(141, 9, '103.156.42.11', 41728, 2, '[\"F2\",\"F3\"]', 0, '2025-12-09 15:23:10'),
(142, 9, '103.156.42.11', 41728, 2, '[\"F10\",\"F11\"]', 0, '2025-12-09 15:23:35'),
(143, 9, '103.156.42.11', 41728, 2, '[\"G2\",\"G3\"]', 0, '2025-12-09 15:24:14'),
(144, 9, '103.156.42.11', 1073, 2, '[\"G2\",\"G3\"]', 0, '2025-12-09 15:25:05'),
(145, 9, '103.156.42.11', 1074, 4, '[\"H2\",\"H3\",\"H4\",\"H5\"]', 0, '2025-12-09 15:25:16'),
(146, 9, '103.156.42.11', 41728, 2, '[\"G2\",\"G3\"]', 0, '2025-12-09 15:26:02'),
(147, 9, '103.156.42.11', 41729, 2, '[\"I2\",\"I3\"]', 0, '2025-12-09 15:26:54'),
(148, 25, '116.97.107.168', 41729, 2, '[\"L2\",\"L3\"]', 0, '2025-12-09 15:26:56'),
(149, 9, '103.156.42.11', 41730, 2, '[\"I2\",\"I3\"]', 0, '2025-12-09 15:28:05'),
(150, 25, '116.97.107.168', 41730, 2, '[\"H2\",\"H3\"]', 0, '2025-12-09 15:28:15'),
(151, 9, '103.156.42.11', 41731, 2, '[\"H2\",\"H3\"]', 0, '2025-12-09 15:28:55'),
(152, 25, '116.97.107.168', 41731, 2, '[\"I2\",\"I3\"]', 0, '2025-12-09 15:28:57'),
(153, 9, '103.156.42.11', 41731, 2, '[\"I4\",\"I5\"]', 0, '2025-12-09 15:29:10'),
(154, 25, '116.97.107.168', 41731, 2, '[\"I2\",\"I3\"]', 0, '2025-12-09 15:30:08'),
(155, 25, '116.97.107.168', 41731, 2, '[\"I2\",\"I3\"]', 0, '2025-12-09 15:30:19'),
(156, 9, '103.156.42.11', 41731, 1, '[\"I1\"]', 0, '2025-12-09 15:31:27'),
(157, 9, '103.156.42.11', 41732, 5, '[\"H2\",\"H3\",\"H4\",\"H5\",\"H6\"]', 0, '2025-12-09 15:32:30'),
(158, 9, '103.156.42.11', 41731, 4, '[\"H2\",\"H3\",\"H4\",\"H5\"]', 0, '2025-12-09 15:33:50'),
(159, 9, '103.156.42.11', 41723, 3, '[\"J2\",\"J3\",\"J4\"]', 0, '2025-12-09 15:34:53'),
(160, 9, '103.156.42.11', 41733, 3, '[\"K1\",\"K2\",\"K3\"]', 0, '2025-12-09 15:50:43'),
(161, 3, '42.118.61.65', 41717, 3, '[\"I2\",\"I3\",\"I4\"]', 0, '2025-12-10 00:41:44'),
(162, 3, '42.118.61.65', 41734, 3, '[\"H2\",\"H3\",\"H4\"]', 0, '2025-12-10 00:44:56'),
(163, 9, '42.118.61.65', 41735, 3, '[\"H2\",\"H3\",\"H4\"]', 0, '2025-12-10 00:45:55'),
(164, 9, '42.118.61.65', 41736, 2, '[\"H2\",\"H3\"]', 0, '2025-12-10 01:03:41'),
(165, 9, '42.118.61.65', 41737, 2, '[\"I2\",\"I3\"]', 0, '2025-12-10 01:10:27'),
(166, 9, '42.118.61.65', 41739, 2, '[\"H2\",\"H3\"]', 0, '2025-12-10 01:29:40'),
(167, 9, '42.118.61.65', 41739, 2, '[\"G2\",\"G3\"]', 0, '2025-12-10 01:43:51'),
(168, 9, '42.118.61.65', 41739, 2, '[\"F2\",\"F4\"]', 0, '2025-12-10 01:44:00'),
(169, 9, '42.118.61.65', 41739, 2, '[\"F1\",\"F2\"]', 0, '2025-12-10 01:44:07'),
(170, 9, '42.118.61.65', 41731, 3, '[\"H2\",\"H3\",\"H4\"]', 0, '2025-12-10 07:25:43'),
(171, 3, '42.118.61.65', 41729, 2, '[\"H5\",\"H9\"]', 0, '2025-12-10 07:29:05'),
(172, 3, '42.118.61.65', 41729, 3, '[\"L7\",\"L9\",\"L10\"]', 0, '2025-12-10 07:29:15'),
(173, 3, '42.118.61.65', 41729, 2, '[\"I7\",\"I8\"]', 0, '2025-12-10 07:29:35'),
(174, 3, '42.118.61.65', 41729, 2, '[\"L8\",\"L9\"]', 0, '2025-12-10 07:30:40'),
(175, 9, '42.118.61.65', 41740, 2, '[\"H2\",\"H3\"]', 0, '2025-12-10 07:44:41'),
(176, 9, '42.118.61.65', 41740, 2, '[\"H2\",\"H3\"]', 0, '2025-12-10 07:46:58'),
(177, 9, '42.118.61.65', 41740, 2, '[\"I3\",\"I4\"]', 0, '2025-12-10 07:48:49'),
(178, 9, '42.118.61.65', 41740, 4, '[\"I1\",\"I2\",\"I3\",\"I4\"]', 0, '2025-12-10 07:49:02'),
(179, 9, '42.118.61.65', 41714, 3, '[\"F3\",\"F4\",\"F5\"]', 0, '2025-12-10 08:36:42'),
(180, 10, '42.118.61.65', 1008, 2, '[\"F5\",\"F6\"]', 0, '2025-12-10 08:38:44'),
(181, 9, '42.118.61.65', 41746, 5, '[\"H1\",\"H2\",\"H3\",\"H4\",\"H5\"]', 0, '2025-12-10 08:42:07'),
(182, 9, '42.118.61.65', 1000, 3, '[\"H4\",\"H5\",\"H6\"]', 0, '2025-12-10 08:55:19'),
(183, 22, '171.255.57.66', 1104, 2, '[\"G3\",\"G4\"]', 0, '2025-12-11 08:32:04'),
(184, 22, '171.255.57.66', 41728, 2, '[\"J4\",\"J5\"]', 0, '2025-12-11 08:47:19'),
(185, 9, '59.153.224.177', 1092, 2, '[\"G3\",\"G4\"]', 0, '2025-12-11 14:38:53'),
(186, 10, '42.118.61.65', 41758, 2, '[\"J6\",\"J7\"]', 0, '2025-12-12 02:03:39'),
(187, 10, '42.118.61.65', 41758, 2, '[\"J6\",\"J7\"]', 0, '2025-12-12 02:04:05'),
(188, 31, '123.18.81.83', 41798, 2, '[\"J5\",\"J6\"]', 0, '2025-12-14 14:45:42'),
(189, 22, '42.118.61.65', 41801, 2, '[\"E1\",\"E2\"]', 0, '2025-12-15 03:06:10'),
(190, 9, '42.118.61.65', 41801, 2, '[\"G3\",\"G4\"]', 0, '2025-12-15 03:15:15'),
(191, 10, '14.180.20.93', 41824, 2, '[\"G5\",\"G6\"]', 0, '2025-12-27 18:23:41'),
(192, 9, '116.106.98.133', 41825, 2, '[\"I3\",\"I4\"]', 0, '2025-12-29 14:36:52'),
(193, 32, '58.186.230.31', 41826, 2, '[\"H7\",\"H8\"]', 0, '2026-01-27 13:18:09'),
(194, 32, '58.186.230.31', 41826, 2, '[\"I8\",\"I9\"]', 0, '2026-01-27 13:28:02'),
(195, 32, '58.186.230.31', 41826, 2, '[\"I8\",\"I9\"]', 0, '2026-01-27 13:28:51'),
(196, 33, '58.186.230.31', 41827, 2, '[\"L7\",\"L8\"]', 0, '2026-01-27 13:58:14'),
(197, 33, '58.186.230.31', 41828, 2, '[\"H8\",\"H9\"]', 0, '2026-01-27 17:25:56'),
(198, 9, '113.184.55.92', 41828, 4, '[\"K4\",\"K5\",\"K6\",\"K7\"]', 0, '2026-01-29 03:11:54'),
(199, 33, '42.119.72.51', 41829, 2, '[\"K8\",\"K9\"]', 0, '2026-02-03 02:08:45'),
(200, 33, '42.119.72.51', 41829, 2, '[\"K8\",\"K9\"]', 0, '2026-02-03 03:11:46'),
(201, 9, '27.78.7.126', 41830, 8, '[\"E5\",\"F5\",\"F6\",\"F7\",\"F8\",\"F9\",\"N5\",\"N6\"]', 0, '2026-02-04 07:12:51');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `showtimes`
--

CREATE TABLE `showtimes` (
  `id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `theater_id` int(11) NOT NULL,
  `show_date` date NOT NULL,
  `show_time` time NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `screen_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `showtimes`
--

INSERT INTO `showtimes` (`id`, `movie_id`, `theater_id`, `show_date`, `show_time`, `price`, `created_at`, `screen_id`) VALUES
(1000, 1, 1, '2025-12-10', '09:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1001, 1, 1, '2025-12-10', '13:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1002, 1, 1, '2025-12-10', '17:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1003, 1, 1, '2025-12-10', '21:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1004, 1, 1, '2025-12-10', '09:00:00', 90000.00, '2025-12-10 10:00:00', 2),
(1005, 1, 1, '2025-12-10', '13:00:00', 90000.00, '2025-12-10 10:00:00', 2),
(1006, 1, 1, '2025-12-10', '17:00:00', 90000.00, '2025-12-10 10:00:00', 2),
(1007, 1, 1, '2025-12-10', '21:00:00', 90000.00, '2025-12-10 10:00:00', 2),
(1008, 1, 2, '2025-12-10', '10:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1009, 1, 2, '2025-12-10', '14:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1010, 1, 2, '2025-12-10', '18:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1011, 1, 2, '2025-12-10', '22:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1012, 1, 3, '2025-12-10', '09:00:00', 90000.00, '2025-12-10 10:00:00', 5),
(1013, 1, 3, '2025-12-10', '13:00:00', 90000.00, '2025-12-10 10:00:00', 5),
(1014, 1, 3, '2025-12-10', '17:00:00', 90000.00, '2025-12-10 10:00:00', 5),
(1015, 1, 3, '2025-12-10', '21:00:00', 90000.00, '2025-12-10 10:00:00', 5),
(1016, 2, 1, '2025-12-11', '10:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1017, 2, 1, '2025-12-11', '14:30:00', 90000.00, '2025-12-10 10:00:00', 1),
(1018, 2, 1, '2025-12-11', '19:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1019, 2, 1, '2025-12-11', '10:00:00', 90000.00, '2025-12-10 10:00:00', 2),
(1020, 2, 1, '2025-12-11', '14:30:00', 90000.00, '2025-12-10 10:00:00', 2),
(1021, 2, 1, '2025-12-11', '19:00:00', 90000.00, '2025-12-10 10:00:00', 2),
(1022, 2, 2, '2025-12-11', '11:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1023, 2, 2, '2025-12-11', '15:30:00', 90000.00, '2025-12-10 10:00:00', 3),
(1024, 2, 2, '2025-12-11', '20:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1025, 2, 3, '2025-12-11', '10:00:00', 90000.00, '2025-12-10 10:00:00', 5),
(1026, 2, 3, '2025-12-11', '14:30:00', 90000.00, '2025-12-10 10:00:00', 5),
(1027, 2, 3, '2025-12-11', '19:00:00', 90000.00, '2025-12-10 10:00:00', 5),
(1028, 3, 1, '2025-12-12', '09:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1029, 3, 1, '2025-12-12', '12:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1030, 3, 1, '2025-12-12', '15:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1031, 3, 1, '2025-12-12', '18:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1032, 3, 1, '2025-12-12', '21:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1033, 3, 2, '2025-12-12', '09:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1034, 3, 2, '2025-12-12', '12:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1035, 3, 2, '2025-12-12', '15:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1036, 3, 2, '2025-12-12', '18:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1037, 3, 2, '2025-12-12', '21:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1038, 3, 3, '2025-12-12', '09:00:00', 90000.00, '2025-12-10 10:00:00', 5),
(1039, 3, 3, '2025-12-12', '12:00:00', 90000.00, '2025-12-10 10:00:00', 5),
(1040, 3, 3, '2025-12-12', '15:00:00', 90000.00, '2025-12-10 10:00:00', 5),
(1041, 3, 3, '2025-12-12', '18:00:00', 90000.00, '2025-12-10 10:00:00', 5),
(1042, 3, 3, '2025-12-12', '21:00:00', 90000.00, '2025-12-10 10:00:00', 5),
(1043, 4, 1, '2025-12-13', '10:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1044, 4, 1, '2025-12-13', '13:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1045, 4, 1, '2025-12-13', '16:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1046, 4, 1, '2025-12-13', '19:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1047, 4, 1, '2025-12-13', '22:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1048, 4, 2, '2025-12-13', '10:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1049, 4, 2, '2025-12-13', '13:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1050, 4, 2, '2025-12-13', '16:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1051, 4, 2, '2025-12-13', '19:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1052, 4, 2, '2025-12-13', '22:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1053, 4, 3, '2025-12-13', '10:00:00', 90000.00, '2025-12-10 10:00:00', 5),
(1054, 4, 3, '2025-12-13', '13:00:00', 90000.00, '2025-12-10 10:00:00', 5),
(1055, 4, 3, '2025-12-13', '16:00:00', 90000.00, '2025-12-10 10:00:00', 5),
(1056, 4, 3, '2025-12-13', '19:00:00', 90000.00, '2025-12-10 10:00:00', 5),
(1057, 4, 3, '2025-12-13', '22:00:00', 90000.00, '2025-12-10 10:00:00', 5),
(1058, 5, 1, '2025-12-14', '09:30:00', 90000.00, '2025-12-10 10:00:00', 1),
(1059, 5, 1, '2025-12-14', '12:30:00', 90000.00, '2025-12-10 10:00:00', 1),
(1060, 5, 1, '2025-12-14', '15:30:00', 90000.00, '2025-12-10 10:00:00', 1),
(1061, 5, 1, '2025-12-14', '18:30:00', 90000.00, '2025-12-10 10:00:00', 1),
(1062, 5, 1, '2025-12-14', '21:30:00', 90000.00, '2025-12-10 10:00:00', 1),
(1063, 5, 2, '2025-12-14', '09:30:00', 90000.00, '2025-12-10 10:00:00', 3),
(1064, 5, 2, '2025-12-14', '12:30:00', 90000.00, '2025-12-10 10:00:00', 3),
(1065, 5, 2, '2025-12-14', '15:30:00', 90000.00, '2025-12-10 10:00:00', 3),
(1066, 5, 2, '2025-12-14', '18:30:00', 90000.00, '2025-12-10 10:00:00', 3),
(1067, 5, 2, '2025-12-14', '21:30:00', 90000.00, '2025-12-10 10:00:00', 3),
(1068, 5, 3, '2025-12-14', '09:30:00', 90000.00, '2025-12-10 10:00:00', 5),
(1069, 5, 3, '2025-12-14', '12:30:00', 90000.00, '2025-12-10 10:00:00', 5),
(1070, 5, 3, '2025-12-14', '15:30:00', 90000.00, '2025-12-10 10:00:00', 5),
(1071, 5, 3, '2025-12-14', '18:30:00', 90000.00, '2025-12-10 10:00:00', 5),
(1072, 5, 3, '2025-12-14', '21:30:00', 90000.00, '2025-12-10 10:00:00', 5),
(1073, 18, 1, '2025-12-15', '09:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1074, 18, 1, '2025-12-15', '12:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1075, 18, 1, '2025-12-15', '15:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1076, 18, 1, '2025-12-15', '18:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1077, 18, 1, '2025-12-15', '21:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1078, 18, 2, '2025-12-15', '09:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1079, 18, 2, '2025-12-15', '12:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1080, 18, 2, '2025-12-15', '15:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1081, 18, 2, '2025-12-15', '18:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1082, 18, 2, '2025-12-15', '21:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1083, 18, 3, '2025-12-15', '09:00:00', 90000.00, '2025-12-10 10:00:00', 5),
(1084, 18, 3, '2025-12-15', '12:00:00', 90000.00, '2025-12-10 10:00:00', 5),
(1085, 18, 3, '2025-12-15', '15:00:00', 90000.00, '2025-12-10 10:00:00', 5),
(1086, 18, 3, '2025-12-15', '18:00:00', 90000.00, '2025-12-10 10:00:00', 5),
(1087, 18, 3, '2025-12-15', '21:00:00', 90000.00, '2025-12-10 10:00:00', 5),
(1088, 19, 1, '2025-12-16', '10:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1089, 19, 1, '2025-12-16', '13:30:00', 90000.00, '2025-12-10 10:00:00', 1),
(1090, 19, 1, '2025-12-16', '17:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1091, 19, 1, '2025-12-16', '20:30:00', 90000.00, '2025-12-10 10:00:00', 1),
(1092, 19, 2, '2025-12-16', '10:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1093, 19, 2, '2025-12-16', '13:30:00', 90000.00, '2025-12-10 10:00:00', 3),
(1094, 19, 2, '2025-12-16', '17:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1095, 19, 2, '2025-12-16', '20:30:00', 90000.00, '2025-12-10 10:00:00', 3),
(1096, 19, 3, '2025-12-16', '10:00:00', 90000.00, '2025-12-10 10:00:00', 5),
(1097, 19, 3, '2025-12-16', '13:30:00', 90000.00, '2025-12-10 10:00:00', 5),
(1098, 19, 3, '2025-12-16', '17:00:00', 90000.00, '2025-12-10 10:00:00', 5),
(1099, 19, 3, '2025-12-16', '20:30:00', 90000.00, '2025-12-10 10:00:00', 5),
(1100, 20, 1, '2025-12-17', '09:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1101, 20, 1, '2025-12-17', '12:30:00', 90000.00, '2025-12-10 10:00:00', 1),
(1102, 20, 1, '2025-12-17', '16:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1103, 20, 1, '2025-12-17', '19:30:00', 90000.00, '2025-12-10 10:00:00', 1),
(1104, 20, 2, '2025-12-17', '09:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1105, 20, 2, '2025-12-17', '12:30:00', 90000.00, '2025-12-10 10:00:00', 3),
(1106, 20, 2, '2025-12-17', '16:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1107, 20, 2, '2025-12-17', '19:30:00', 90000.00, '2025-12-10 10:00:00', 3),
(1108, 20, 3, '2025-12-17', '09:00:00', 90000.00, '2025-12-10 10:00:00', 11),
(1109, 20, 3, '2025-12-17', '12:30:00', 90000.00, '2025-12-10 10:00:00', 11),
(1110, 20, 3, '2025-12-17', '16:00:00', 90000.00, '2025-12-10 10:00:00', 11),
(1111, 20, 3, '2025-12-17', '19:30:00', 90000.00, '2025-12-10 10:00:00', 11),
(1112, 21, 1, '2025-12-18', '10:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1113, 21, 1, '2025-12-18', '13:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1114, 21, 1, '2025-12-18', '16:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1115, 21, 1, '2025-12-18', '19:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1116, 21, 1, '2025-12-18', '22:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1117, 21, 2, '2025-12-18', '10:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1118, 21, 2, '2025-12-18', '13:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1119, 21, 2, '2025-12-18', '16:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1120, 21, 2, '2025-12-18', '19:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1121, 21, 2, '2025-12-18', '22:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1122, 22, 1, '2025-12-19', '09:30:00', 90000.00, '2025-12-10 10:00:00', 1),
(1123, 22, 1, '2025-12-19', '12:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1124, 22, 1, '2025-12-19', '14:30:00', 90000.00, '2025-12-10 10:00:00', 1),
(1125, 22, 1, '2025-12-19', '17:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1126, 22, 1, '2025-12-19', '19:30:00', 90000.00, '2025-12-10 10:00:00', 1),
(1127, 22, 2, '2025-12-19', '09:30:00', 90000.00, '2025-12-10 10:00:00', 3),
(1128, 22, 2, '2025-12-19', '12:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1129, 22, 2, '2025-12-19', '14:30:00', 90000.00, '2025-12-10 10:00:00', 3),
(1130, 22, 2, '2025-12-19', '17:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1131, 22, 2, '2025-12-19', '19:30:00', 90000.00, '2025-12-10 10:00:00', 3),
(1132, 23, 1, '2025-12-20', '10:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1133, 23, 1, '2025-12-20', '13:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1134, 23, 1, '2025-12-20', '16:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1135, 23, 1, '2025-12-20', '19:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1136, 23, 2, '2025-12-20', '10:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1137, 23, 2, '2025-12-20', '13:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1138, 23, 2, '2025-12-20', '16:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1139, 23, 2, '2025-12-20', '19:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1140, 25, 1, '2025-12-21', '09:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1141, 25, 1, '2025-12-21', '12:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1142, 25, 1, '2025-12-21', '15:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1143, 25, 1, '2025-12-21', '18:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1144, 25, 1, '2025-12-21', '21:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1145, 25, 2, '2025-12-21', '09:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1146, 25, 2, '2025-12-21', '12:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1147, 25, 2, '2025-12-21', '15:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1148, 25, 2, '2025-12-21', '18:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1149, 25, 2, '2025-12-21', '21:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1150, 26, 1, '2025-12-22', '10:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1151, 26, 1, '2025-12-22', '13:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1152, 26, 1, '2025-12-22', '16:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1153, 26, 1, '2025-12-22', '19:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1154, 26, 1, '2025-12-22', '22:00:00', 90000.00, '2025-12-10 10:00:00', 1),
(1155, 26, 2, '2025-12-22', '10:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1156, 26, 2, '2025-12-22', '13:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1157, 26, 2, '2025-12-22', '16:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1158, 26, 2, '2025-12-22', '19:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1159, 26, 2, '2025-12-22', '22:00:00', 90000.00, '2025-12-10 10:00:00', 3),
(1160, 27, 1, '2025-12-23', '09:30:00', 90000.00, '2025-12-10 10:00:00', 1),
(1161, 27, 1, '2025-12-23', '12:30:00', 90000.00, '2025-12-10 10:00:00', 1),
(1162, 27, 1, '2025-12-23', '15:30:00', 90000.00, '2025-12-10 10:00:00', 1),
(1163, 27, 1, '2025-12-23', '18:30:00', 90000.00, '2025-12-10 10:00:00', 1),
(1164, 27, 1, '2025-12-23', '21:30:00', 90000.00, '2025-12-10 10:00:00', 1),
(1165, 27, 2, '2025-12-23', '09:30:00', 90000.00, '2025-12-10 10:00:00', 3),
(1166, 27, 2, '2025-12-23', '12:30:00', 90000.00, '2025-12-10 10:00:00', 3),
(1167, 27, 2, '2025-12-23', '15:30:00', 90000.00, '2025-12-10 10:00:00', 3),
(1168, 27, 2, '2025-12-23', '18:30:00', 90000.00, '2025-12-10 10:00:00', 3),
(1169, 27, 2, '2025-12-23', '21:30:00', 90000.00, '2025-12-10 10:00:00', 3),
(41713, 20, 6, '2025-12-10', '14:00:00', 120000.00, '2025-12-08 18:56:18', 31),
(41714, 18, 6, '2025-12-10', '15:00:00', 150000.00, '2025-12-08 19:26:04', 32),
(41715, 18, 6, '2025-12-10', '17:00:00', 200000.00, '2025-12-08 19:26:44', 31),
(41716, 18, 6, '2025-12-10', '20:00:00', 120000.00, '2025-12-08 19:27:21', 31),
(41717, 20, 3, '2025-12-10', '08:00:00', 120000.00, '2025-12-09 01:28:40', 29),
(41718, 20, 3, '2025-12-10', '09:30:00', 200000.00, '2025-12-09 01:29:03', 29),
(41719, 23, 3, '2025-12-10', '08:40:00', 120000.00, '2025-12-09 01:39:12', 29),
(41720, 1, 3, '2025-12-09', '08:00:00', 120000.00, '2025-12-09 03:28:36', 5),
(41721, 18, 3, '2025-12-09', '12:00:00', 120000.00, '2025-12-09 03:29:17', 5),
(41722, 23, 3, '2025-12-09', '15:00:00', 130000.00, '2025-12-09 03:29:46', 5),
(41723, 25, 3, '2025-12-09', '18:00:00', 130000.00, '2025-12-09 03:30:04', 5),
(41724, 23, 3, '2025-12-09', '08:00:00', 130000.00, '2025-12-09 03:30:27', 29),
(41725, 20, 3, '2025-12-09', '09:00:00', 120000.00, '2025-12-09 03:30:44', 27),
(41726, 20, 3, '2025-12-09', '12:00:00', 140000.00, '2025-12-09 03:31:57', 27),
(41727, 20, 3, '2025-12-09', '08:00:00', 150000.00, '2025-12-09 03:32:23', 12),
(41728, 23, 3, '2025-12-12', '08:00:00', 200000.00, '2025-12-09 15:16:43', 12),
(41729, 19, 3, '2025-12-12', '08:00:00', 120000.00, '2025-12-09 15:26:38', 5),
(41730, 25, 3, '2025-12-12', '11:00:00', 145000.00, '2025-12-09 15:27:39', 12),
(41731, 20, 3, '2025-12-12', '08:00:00', 120000.00, '2025-12-09 15:28:31', 6),
(41732, 25, 3, '2025-12-12', '08:00:00', 120000.00, '2025-12-09 15:32:01', 11),
(41733, 19, 3, '2025-12-10', '08:00:00', 200000.00, '2025-12-09 15:50:04', 5),
(41734, 23, 3, '2025-12-15', '08:00:00', 122000.00, '2025-12-10 00:44:10', 27),
(41735, 20, 3, '2025-12-15', '08:00:00', 130000.00, '2025-12-10 00:45:29', 28),
(41736, 21, 3, '2025-12-15', '11:00:00', 120000.00, '2025-12-10 01:03:16', 28),
(41737, 20, 3, '2025-12-15', '08:00:00', 122000.00, '2025-12-10 01:10:07', 12),
(41738, 23, 3, '2025-12-17', '08:00:00', 122000.00, '2025-12-10 01:28:40', 12),
(41739, 20, 3, '2025-12-12', '14:00:00', 122222.00, '2025-12-10 01:29:26', 12),
(41740, 20, 6, '2025-12-10', '08:00:00', 120000.00, '2025-12-10 07:41:42', 30),
(41741, 20, 6, '2025-12-10', '08:00:00', 90000.00, '2025-12-10 07:41:56', 31),
(41742, 23, 6, '2025-12-10', '11:00:00', 80000.00, '2025-12-10 07:42:14', 30),
(41744, 23, 6, '2025-12-10', '14:00:00', 75000.00, '2025-12-10 07:42:44', 30),
(41745, 18, 6, '2025-12-10', '08:00:00', 100000.00, '2025-12-10 07:42:58', 33),
(41746, 25, 6, '2025-12-10', '18:00:00', 75000.00, '2025-12-10 07:43:14', 33),
(41747, 23, 6, '2025-12-10', '08:00:00', 75000.00, '2025-12-10 07:43:28', 34),
(41748, 25, 6, '2025-12-10', '08:00:00', 75000.00, '2025-12-10 07:44:03', 32),
(41749, 20, 6, '2025-12-10', '11:00:00', 75000.00, '2025-12-10 07:44:19', 34),
(41750, 23, 6, '2025-12-11', '08:00:00', 75000.00, '2025-12-11 08:38:53', 30),
(41751, 20, 6, '2025-12-11', '11:00:00', 75000.00, '2025-12-11 14:40:18', 30),
(41752, 23, 6, '2025-12-11', '08:00:00', 80000.00, '2025-12-11 14:40:33', 31),
(41753, 19, 6, '2025-12-11', '08:00:00', 36000.00, '2025-12-11 14:40:49', 32),
(41754, 19, 6, '2025-12-11', '14:00:00', 100000.00, '2025-12-11 14:41:09', 30),
(41755, 22, 6, '2025-12-11', '11:00:00', 80000.00, '2025-12-11 14:41:27', 32),
(41756, 23, 6, '2025-12-11', '22:00:00', 80000.00, '2025-12-11 14:42:21', 30),
(41757, 20, 6, '2025-12-12', '09:00:00', 100000.00, '2025-12-12 02:02:39', 30),
(41758, 23, 6, '2025-12-12', '10:00:00', 120000.00, '2025-12-12 02:02:51', 31),
(41759, 25, 6, '2025-12-12', '13:00:00', 120000.00, '2025-12-12 02:03:07', 31),
(41760, 23, 3, '2025-12-14', '08:00:00', 100000.00, '2025-12-13 12:04:06', 41),
(41761, 20, 3, '2025-12-14', '11:00:00', 120000.00, '2025-12-13 12:04:18', 41),
(41762, 38, 2, '2025-12-24', '10:00:00', 90000.00, '2025-12-14 14:09:10', 3),
(41763, 38, 2, '2025-12-24', '10:00:00', 90000.00, '2025-12-14 14:09:10', 4),
(41764, 38, 2, '2025-12-24', '10:00:00', 90000.00, '2025-12-14 14:09:10', 13),
(41765, 38, 2, '2025-12-24', '14:00:00', 90000.00, '2025-12-14 14:09:10', 3),
(41766, 38, 2, '2025-12-24', '14:00:00', 90000.00, '2025-12-14 14:09:10', 4),
(41767, 38, 2, '2025-12-24', '14:00:00', 90000.00, '2025-12-14 14:09:10', 13),
(41768, 38, 2, '2025-12-24', '18:00:00', 90000.00, '2025-12-14 14:09:10', 3),
(41769, 38, 2, '2025-12-24', '18:00:00', 90000.00, '2025-12-14 14:09:10', 4),
(41770, 38, 2, '2025-12-24', '18:00:00', 90000.00, '2025-12-14 14:09:10', 13),
(41771, 38, 2, '2025-12-24', '20:30:00', 90000.00, '2025-12-14 14:09:10', 3),
(41772, 38, 2, '2025-12-24', '20:30:00', 90000.00, '2025-12-14 14:09:10', 4),
(41773, 38, 2, '2025-12-24', '20:30:00', 90000.00, '2025-12-14 14:09:10', 13),
(41774, 38, 1, '2025-12-24', '10:00:00', 90000.00, '2025-12-14 14:09:10', 1),
(41775, 38, 1, '2025-12-24', '10:00:00', 90000.00, '2025-12-14 14:09:10', 2),
(41776, 38, 1, '2025-12-24', '10:00:00', 90000.00, '2025-12-14 14:09:10', 15),
(41777, 38, 1, '2025-12-24', '14:00:00', 90000.00, '2025-12-14 14:09:10', 1),
(41778, 38, 1, '2025-12-24', '14:00:00', 90000.00, '2025-12-14 14:09:10', 2),
(41779, 38, 1, '2025-12-24', '14:00:00', 90000.00, '2025-12-14 14:09:10', 15),
(41780, 38, 1, '2025-12-24', '18:00:00', 90000.00, '2025-12-14 14:09:10', 1),
(41781, 38, 1, '2025-12-24', '18:00:00', 90000.00, '2025-12-14 14:09:10', 2),
(41782, 38, 1, '2025-12-24', '18:00:00', 90000.00, '2025-12-14 14:09:10', 15),
(41783, 38, 1, '2025-12-24', '20:30:00', 90000.00, '2025-12-14 14:09:10', 1),
(41784, 38, 1, '2025-12-24', '20:30:00', 90000.00, '2025-12-14 14:09:10', 2),
(41785, 38, 1, '2025-12-24', '20:30:00', 90000.00, '2025-12-14 14:09:10', 15),
(41786, 38, 3, '2025-12-24', '10:00:00', 90000.00, '2025-12-14 14:09:10', 6),
(41787, 38, 3, '2025-12-24', '10:00:00', 90000.00, '2025-12-14 14:09:10', 11),
(41788, 38, 3, '2025-12-24', '10:00:00', 90000.00, '2025-12-14 14:09:10', 12),
(41789, 38, 3, '2025-12-24', '14:00:00', 90000.00, '2025-12-14 14:09:10', 6),
(41790, 38, 3, '2025-12-24', '14:00:00', 90000.00, '2025-12-14 14:09:10', 11),
(41791, 38, 3, '2025-12-24', '14:00:00', 90000.00, '2025-12-14 14:09:10', 12),
(41792, 38, 3, '2025-12-24', '18:00:00', 90000.00, '2025-12-14 14:09:10', 6),
(41793, 38, 3, '2025-12-24', '18:00:00', 90000.00, '2025-12-14 14:09:10', 11),
(41794, 38, 3, '2025-12-24', '18:00:00', 90000.00, '2025-12-14 14:09:10', 12),
(41795, 38, 3, '2025-12-24', '20:30:00', 90000.00, '2025-12-14 14:09:10', 6),
(41796, 38, 3, '2025-12-24', '20:30:00', 90000.00, '2025-12-14 14:09:10', 11),
(41797, 38, 3, '2025-12-24', '20:30:00', 90000.00, '2025-12-14 14:09:10', 12),
(41798, 23, 3, '2025-12-15', '08:00:00', 121000.00, '2025-12-14 14:21:59', 5),
(41799, 25, 3, '2025-12-15', '08:00:00', 100000.00, '2025-12-14 14:54:21', 6),
(41800, 23, 6, '2025-12-15', '08:00:00', 100000.00, '2025-12-15 02:19:12', 30),
(41801, 20, 6, '2025-12-15', '11:00:00', 100000.00, '2025-12-15 02:19:27', 30),
(41802, 20, 6, '2025-12-15', '14:00:00', 80000.00, '2025-12-15 02:19:44', 30),
(41803, 23, 6, '2025-12-15', '08:00:00', 100000.00, '2025-12-15 02:19:59', 31),
(41804, 4, 6, '2025-12-15', '08:00:00', 75000.00, '2025-12-15 02:20:19', 32),
(41805, 4, 6, '2025-12-15', '08:00:00', 100000.00, '2025-12-15 02:20:37', 33),
(41806, 18, 6, '2025-12-15', '08:00:00', 100000.00, '2025-12-15 02:20:55', 34),
(41807, 25, 6, '2025-12-15', '17:00:00', 100000.00, '2025-12-15 02:21:15', 30),
(41808, 25, 6, '2025-12-15', '11:00:00', 100000.00, '2025-12-15 02:21:32', 31),
(41809, 19, 6, '2025-12-15', '14:00:00', 200000.00, '2025-12-15 02:21:56', 31),
(41810, 19, 6, '2025-12-15', '17:00:00', 150000.00, '2025-12-15 02:22:16', 31),
(41811, 23, 6, '2025-12-15', '20:00:00', 100000.00, '2025-12-15 02:22:32', 31),
(41812, 18, 6, '2025-12-15', '20:00:00', 130000.00, '2025-12-15 02:22:55', 30),
(41813, 25, 6, '2025-12-15', '23:00:00', 80000.00, '2025-12-15 02:23:17', 30),
(41814, 18, 6, '2025-12-15', '11:00:00', 100000.00, '2025-12-15 02:23:33', 32),
(41815, 22, 6, '2025-12-15', '14:00:00', 80000.00, '2025-12-15 02:23:53', 32),
(41816, 18, 6, '2025-12-15', '11:00:00', 100000.00, '2025-12-15 02:24:28', 33),
(41817, 19, 6, '2025-12-15', '14:00:00', 100000.00, '2025-12-15 02:24:47', 33),
(41818, 19, 6, '2025-12-15', '17:00:00', 100000.00, '2025-12-15 02:25:02', 33),
(41819, 19, 6, '2025-12-15', '20:00:00', 100000.00, '2025-12-15 02:25:24', 33),
(41820, 19, 6, '2025-12-15', '11:00:00', 80000.00, '2025-12-15 02:25:40', 34),
(41821, 19, 6, '2025-12-15', '14:00:00', 200000.00, '2025-12-15 02:26:30', 34),
(41822, 20, 6, '2025-12-15', '17:00:00', 120000.00, '2025-12-15 02:26:46', 34),
(41823, 52, 3, '2025-12-17', '08:00:00', 100000.00, '2025-12-15 03:29:55', 5),
(41824, 23, 2, '2025-12-28', '08:00:00', 120000.00, '2025-12-27 18:19:03', 3),
(41825, 52, 3, '2025-12-30', '08:00:00', 120000.00, '2025-12-29 14:36:00', 5),
(41826, 1, 3, '2026-01-28', '12:00:00', 30000.00, '2026-01-27 13:16:21', 5),
(41827, 20, 3, '2026-01-28', '10:00:00', 12000.00, '2026-01-27 13:47:11', 5),
(41828, 19, 3, '2026-01-29', '15:00:00', 36000.00, '2026-01-27 17:24:51', 5),
(41829, 1, 3, '2026-02-04', '18:00:00', 36000.00, '2026-02-03 02:07:49', 5),
(41830, 1, 3, '2026-02-04', '08:00:00', 36000.00, '2026-02-03 02:08:04', 5),
(41861, 1, 1, '2026-06-09', '10:00:00', 90000.00, '2026-06-08 22:40:55', 1),
(41862, 1, 1, '2026-06-09', '14:00:00', 90000.00, '2026-06-08 22:40:55', 1),
(41863, 1, 1, '2026-06-09', '18:00:00', 90000.00, '2026-06-08 22:40:55', 1),
(41864, 1, 1, '2026-06-09', '21:00:00', 90000.00, '2026-06-08 22:40:55', 1),
(41865, 1, 1, '2026-06-10', '10:00:00', 90000.00, '2026-06-08 22:40:55', 1),
(41866, 1, 1, '2026-06-10', '14:00:00', 90000.00, '2026-06-08 22:40:55', 1),
(41867, 1, 1, '2026-06-10', '18:00:00', 90000.00, '2026-06-08 22:40:55', 1),
(41868, 1, 1, '2026-06-10', '21:00:00', 90000.00, '2026-06-08 22:40:55', 1),
(41869, 1, 1, '2026-06-11', '10:00:00', 90000.00, '2026-06-08 22:40:55', 1),
(41870, 1, 1, '2026-06-11', '14:00:00', 90000.00, '2026-06-08 22:40:55', 1),
(41871, 1, 1, '2026-06-11', '18:00:00', 90000.00, '2026-06-08 22:40:55', 1),
(41872, 1, 1, '2026-06-11', '21:00:00', 90000.00, '2026-06-08 22:40:55', 1),
(41873, 1, 1, '2026-06-12', '10:00:00', 90000.00, '2026-06-08 22:40:55', 1),
(41874, 1, 1, '2026-06-12', '14:00:00', 90000.00, '2026-06-08 22:40:55', 1),
(41875, 1, 1, '2026-06-12', '18:00:00', 90000.00, '2026-06-08 22:40:55', 1),
(41876, 1, 1, '2026-06-12', '21:00:00', 90000.00, '2026-06-08 22:40:55', 1),
(41877, 1, 1, '2026-06-13', '10:00:00', 90000.00, '2026-06-08 22:40:55', 1),
(41878, 1, 1, '2026-06-13', '14:00:00', 90000.00, '2026-06-08 22:40:55', 1),
(41879, 1, 1, '2026-06-13', '18:00:00', 90000.00, '2026-06-08 22:40:55', 1),
(41880, 1, 1, '2026-06-13', '21:00:00', 90000.00, '2026-06-08 22:40:55', 1),
(41881, 1, 1, '2026-06-14', '10:00:00', 90000.00, '2026-06-08 22:40:55', 1),
(41882, 1, 1, '2026-06-14', '14:00:00', 90000.00, '2026-06-08 22:40:55', 1),
(41883, 1, 1, '2026-06-14', '18:00:00', 90000.00, '2026-06-08 22:40:55', 1),
(41884, 1, 1, '2026-06-14', '21:00:00', 90000.00, '2026-06-08 22:40:55', 1),
(41885, 1, 1, '2026-06-15', '10:00:00', 90000.00, '2026-06-08 22:40:55', 1),
(41886, 1, 1, '2026-06-15', '14:00:00', 90000.00, '2026-06-08 22:40:55', 1),
(41887, 1, 1, '2026-06-15', '18:00:00', 90000.00, '2026-06-08 22:40:55', 1),
(41888, 1, 1, '2026-06-15', '21:00:00', 90000.00, '2026-06-08 22:40:55', 1),
(41889, 1, 1, '2026-06-09', '09:00:00', 90000.00, '2026-06-09 00:39:24', 25),
(41890, 1, 1, '2026-06-09', '15:00:00', 90000.00, '2026-06-09 00:39:24', 25),
(41891, 1, 1, '2026-06-09', '18:00:00', 90000.00, '2026-06-09 00:39:24', 25),
(41892, 1, 2, '2026-06-09', '09:00:00', 90000.00, '2026-06-09 00:39:24', 3),
(41893, 1, 2, '2026-06-09', '12:00:00', 90000.00, '2026-06-09 00:39:24', 3),
(41894, 1, 2, '2026-06-09', '21:00:00', 90000.00, '2026-06-09 00:39:24', 3),
(41895, 1, 1, '2026-06-10', '09:00:00', 90000.00, '2026-06-09 00:39:24', 40),
(41896, 1, 1, '2026-06-10', '12:00:00', 90000.00, '2026-06-09 00:39:24', 40),
(41897, 1, 1, '2026-06-10', '15:00:00', 90000.00, '2026-06-09 00:39:24', 40),
(41898, 1, 2, '2026-06-10', '12:00:00', 90000.00, '2026-06-09 00:39:24', 13),
(41899, 1, 2, '2026-06-10', '15:00:00', 90000.00, '2026-06-09 00:39:24', 13),
(41900, 1, 2, '2026-06-10', '18:00:00', 90000.00, '2026-06-09 00:39:24', 13),
(41901, 1, 1, '2026-06-11', '09:00:00', 90000.00, '2026-06-09 00:39:24', 40),
(41902, 1, 1, '2026-06-11', '12:00:00', 90000.00, '2026-06-09 00:39:24', 40),
(41903, 1, 1, '2026-06-11', '18:00:00', 90000.00, '2026-06-09 00:39:24', 40),
(41904, 1, 2, '2026-06-11', '09:00:00', 90000.00, '2026-06-09 00:39:24', 13),
(41905, 1, 2, '2026-06-11', '18:00:00', 90000.00, '2026-06-09 00:39:24', 13),
(41906, 1, 2, '2026-06-11', '21:00:00', 90000.00, '2026-06-09 00:39:24', 13),
(41907, 1, 1, '2026-06-12', '09:00:00', 90000.00, '2026-06-09 00:39:24', 15),
(41908, 1, 1, '2026-06-12', '18:00:00', 90000.00, '2026-06-09 00:39:24', 15),
(41909, 1, 1, '2026-06-12', '21:00:00', 90000.00, '2026-06-09 00:39:24', 15),
(41910, 1, 2, '2026-06-12', '09:00:00', 90000.00, '2026-06-09 00:39:24', 24),
(41911, 1, 2, '2026-06-12', '12:00:00', 90000.00, '2026-06-09 00:39:24', 24),
(41912, 1, 2, '2026-06-12', '18:00:00', 90000.00, '2026-06-09 00:39:24', 24),
(41913, 1, 1, '2026-06-13', '12:00:00', 90000.00, '2026-06-09 00:39:24', 2),
(41914, 1, 1, '2026-06-13', '15:00:00', 90000.00, '2026-06-09 00:39:24', 2),
(41915, 1, 1, '2026-06-13', '18:00:00', 90000.00, '2026-06-09 00:39:24', 2),
(41916, 1, 2, '2026-06-13', '12:00:00', 90000.00, '2026-06-09 00:39:24', 24),
(41917, 1, 2, '2026-06-13', '18:00:00', 90000.00, '2026-06-09 00:39:24', 24),
(41918, 1, 2, '2026-06-13', '21:00:00', 90000.00, '2026-06-09 00:39:24', 24),
(41919, 1, 1, '2026-06-14', '09:00:00', 90000.00, '2026-06-09 00:39:24', 40),
(41920, 1, 1, '2026-06-14', '12:00:00', 90000.00, '2026-06-09 00:39:24', 40),
(41921, 1, 1, '2026-06-14', '15:00:00', 90000.00, '2026-06-09 00:39:24', 40),
(41922, 1, 2, '2026-06-14', '09:00:00', 90000.00, '2026-06-09 00:39:24', 3),
(41923, 1, 2, '2026-06-14', '15:00:00', 90000.00, '2026-06-09 00:39:24', 3),
(41924, 1, 2, '2026-06-14', '18:00:00', 90000.00, '2026-06-09 00:39:24', 3),
(41925, 1, 1, '2026-06-15', '09:00:00', 90000.00, '2026-06-09 00:39:24', 9),
(41926, 1, 1, '2026-06-15', '15:00:00', 90000.00, '2026-06-09 00:39:24', 9),
(41927, 1, 1, '2026-06-15', '21:00:00', 90000.00, '2026-06-09 00:39:24', 9),
(41928, 1, 2, '2026-06-15', '09:00:00', 90000.00, '2026-06-09 00:39:24', 4),
(41929, 1, 2, '2026-06-15', '15:00:00', 90000.00, '2026-06-09 00:39:24', 4),
(41930, 1, 2, '2026-06-15', '21:00:00', 90000.00, '2026-06-09 00:39:24', 4),
(41931, 2, 1, '2026-06-09', '09:00:00', 90000.00, '2026-06-09 00:39:24', 1),
(41932, 2, 1, '2026-06-09', '15:00:00', 90000.00, '2026-06-09 00:39:24', 1),
(41934, 1, 1, '2026-06-09', '12:00:00', 90000.00, '2026-06-09 00:40:02', 1),
(41935, 1, 1, '2026-06-09', '12:00:00', 90000.00, '2026-06-09 00:40:02', 2),
(41936, 1, 1, '2026-06-09', '15:00:00', 90000.00, '2026-06-09 00:40:02', 2),
(41937, 1, 2, '2026-06-09', '18:00:00', 90000.00, '2026-06-09 00:40:02', 3),
(41938, 1, 2, '2026-06-09', '09:00:00', 90000.00, '2026-06-09 00:40:02', 4),
(41939, 1, 2, '2026-06-09', '12:00:00', 90000.00, '2026-06-09 00:40:02', 4),
(41940, 1, 1, '2026-06-10', '12:00:00', 90000.00, '2026-06-09 00:40:02', 1),
(41941, 1, 1, '2026-06-10', '15:00:00', 90000.00, '2026-06-09 00:40:02', 1),
(41942, 1, 1, '2026-06-10', '15:00:00', 90000.00, '2026-06-09 00:40:02', 2),
(41943, 1, 1, '2026-06-10', '18:00:00', 90000.00, '2026-06-09 00:40:02', 2),
(41944, 1, 2, '2026-06-10', '12:00:00', 90000.00, '2026-06-09 00:40:02', 3),
(41945, 1, 2, '2026-06-10', '21:00:00', 90000.00, '2026-06-09 00:40:02', 3),
(41946, 1, 2, '2026-06-10', '12:00:00', 90000.00, '2026-06-09 00:40:02', 4),
(41947, 1, 2, '2026-06-10', '18:00:00', 90000.00, '2026-06-09 00:40:02', 4),
(41948, 1, 1, '2026-06-11', '09:00:00', 90000.00, '2026-06-09 00:40:02', 1),
(41949, 1, 1, '2026-06-11', '15:00:00', 90000.00, '2026-06-09 00:40:02', 1),
(41950, 1, 1, '2026-06-11', '12:00:00', 90000.00, '2026-06-09 00:40:02', 2),
(41951, 1, 1, '2026-06-11', '15:00:00', 90000.00, '2026-06-09 00:40:02', 2),
(41952, 1, 2, '2026-06-11', '09:00:00', 90000.00, '2026-06-09 00:40:02', 3),
(41953, 1, 2, '2026-06-11', '12:00:00', 90000.00, '2026-06-09 00:40:02', 3),
(41954, 1, 2, '2026-06-11', '15:00:00', 90000.00, '2026-06-09 00:40:02', 4),
(41955, 1, 2, '2026-06-11', '18:00:00', 90000.00, '2026-06-09 00:40:02', 4),
(41956, 1, 1, '2026-06-12', '09:00:00', 90000.00, '2026-06-09 00:40:02', 1),
(41957, 1, 1, '2026-06-12', '12:00:00', 90000.00, '2026-06-09 00:40:02', 1),
(41958, 1, 1, '2026-06-12', '12:00:00', 90000.00, '2026-06-09 00:40:02', 2),
(41959, 1, 1, '2026-06-12', '18:00:00', 90000.00, '2026-06-09 00:40:02', 2),
(41960, 1, 2, '2026-06-12', '09:00:00', 90000.00, '2026-06-09 00:40:02', 3),
(41961, 1, 2, '2026-06-12', '18:00:00', 90000.00, '2026-06-09 00:40:02', 3),
(41962, 1, 2, '2026-06-12', '15:00:00', 90000.00, '2026-06-09 00:40:02', 4),
(41963, 1, 2, '2026-06-12', '21:00:00', 90000.00, '2026-06-09 00:40:02', 4),
(41964, 1, 1, '2026-06-13', '09:00:00', 90000.00, '2026-06-09 00:40:02', 1),
(41965, 1, 1, '2026-06-13', '15:00:00', 90000.00, '2026-06-09 00:40:02', 1),
(41966, 1, 1, '2026-06-13', '09:00:00', 90000.00, '2026-06-09 00:40:02', 2),
(41967, 1, 2, '2026-06-13', '09:00:00', 90000.00, '2026-06-09 00:40:02', 3),
(41968, 1, 2, '2026-06-13', '18:00:00', 90000.00, '2026-06-09 00:40:02', 3),
(41969, 1, 2, '2026-06-13', '12:00:00', 90000.00, '2026-06-09 00:40:02', 4),
(41970, 1, 2, '2026-06-13', '21:00:00', 90000.00, '2026-06-09 00:40:02', 4),
(41971, 1, 1, '2026-06-14', '09:00:00', 90000.00, '2026-06-09 00:40:02', 1),
(41972, 1, 1, '2026-06-14', '12:00:00', 90000.00, '2026-06-09 00:40:02', 1),
(41973, 1, 1, '2026-06-14', '15:00:00', 90000.00, '2026-06-09 00:40:02', 2),
(41974, 1, 1, '2026-06-14', '21:00:00', 90000.00, '2026-06-09 00:40:02', 2),
(41975, 1, 2, '2026-06-14', '21:00:00', 90000.00, '2026-06-09 00:40:02', 3),
(41976, 1, 2, '2026-06-14', '09:00:00', 90000.00, '2026-06-09 00:40:02', 4),
(41977, 1, 2, '2026-06-14', '12:00:00', 90000.00, '2026-06-09 00:40:02', 4),
(41978, 1, 1, '2026-06-15', '12:00:00', 90000.00, '2026-06-09 00:40:02', 1),
(41979, 1, 1, '2026-06-15', '12:00:00', 90000.00, '2026-06-09 00:40:02', 2),
(41980, 1, 1, '2026-06-15', '18:00:00', 90000.00, '2026-06-09 00:40:02', 2),
(41981, 1, 2, '2026-06-15', '15:00:00', 90000.00, '2026-06-09 00:40:02', 3),
(41982, 1, 2, '2026-06-15', '18:00:00', 90000.00, '2026-06-09 00:40:02', 3),
(41983, 1, 2, '2026-06-15', '18:00:00', 90000.00, '2026-06-09 00:40:02', 4),
(41984, 2, 1, '2026-06-09', '09:00:00', 90000.00, '2026-06-09 00:40:02', 2),
(41985, 2, 2, '2026-06-09', '18:00:00', 90000.00, '2026-06-09 00:40:02', 4),
(41986, 2, 2, '2026-06-09', '21:00:00', 90000.00, '2026-06-09 00:40:02', 4),
(41987, 2, 1, '2026-06-10', '09:00:00', 90000.00, '2026-06-09 00:40:02', 1),
(41988, 2, 2, '2026-06-10', '09:00:00', 90000.00, '2026-06-09 00:40:02', 3),
(41989, 2, 2, '2026-06-10', '15:00:00', 90000.00, '2026-06-09 00:40:02', 3),
(41990, 2, 1, '2026-06-11', '12:00:00', 90000.00, '2026-06-09 00:40:02', 1),
(41991, 2, 2, '2026-06-11', '15:00:00', 90000.00, '2026-06-09 00:40:02', 3),
(41992, 2, 2, '2026-06-11', '18:00:00', 90000.00, '2026-06-09 00:40:02', 3),
(41993, 2, 2, '2026-06-11', '09:00:00', 90000.00, '2026-06-09 00:40:02', 4),
(41994, 2, 1, '2026-06-12', '15:00:00', 90000.00, '2026-06-09 00:40:02', 2),
(41995, 2, 2, '2026-06-12', '12:00:00', 90000.00, '2026-06-09 00:40:02', 3),
(41996, 2, 2, '2026-06-12', '09:00:00', 90000.00, '2026-06-09 00:40:02', 4),
(41997, 2, 2, '2026-06-13', '21:00:00', 90000.00, '2026-06-09 00:40:02', 3),
(41998, 2, 2, '2026-06-13', '15:00:00', 90000.00, '2026-06-09 00:40:02', 4),
(41999, 2, 1, '2026-06-14', '12:00:00', 90000.00, '2026-06-09 00:40:02', 2),
(42000, 2, 1, '2026-06-15', '09:00:00', 90000.00, '2026-06-09 00:40:02', 2),
(42001, 2, 1, '2026-06-15', '15:00:00', 90000.00, '2026-06-09 00:40:02', 2),
(42002, 2, 2, '2026-06-15', '12:00:00', 90000.00, '2026-06-09 00:40:02', 3),
(42003, 2, 2, '2026-06-15', '21:00:00', 90000.00, '2026-06-09 00:40:02', 3),
(42004, 3, 1, '2026-06-09', '18:00:00', 90000.00, '2026-06-09 00:40:02', 2),
(42005, 3, 2, '2026-06-09', '15:00:00', 90000.00, '2026-06-09 00:40:02', 4),
(42006, 3, 1, '2026-06-10', '12:00:00', 90000.00, '2026-06-09 00:40:02', 2),
(42007, 3, 1, '2026-06-10', '21:00:00', 90000.00, '2026-06-09 00:40:02', 2),
(42008, 3, 2, '2026-06-10', '09:00:00', 90000.00, '2026-06-09 00:40:02', 4),
(42009, 3, 1, '2026-06-11', '18:00:00', 90000.00, '2026-06-09 00:40:02', 2),
(42010, 3, 2, '2026-06-11', '21:00:00', 90000.00, '2026-06-09 00:40:02', 4),
(42011, 3, 1, '2026-06-12', '09:00:00', 90000.00, '2026-06-09 00:40:02', 2),
(42012, 3, 2, '2026-06-12', '12:00:00', 90000.00, '2026-06-09 00:40:02', 4),
(42013, 3, 1, '2026-06-13', '12:00:00', 90000.00, '2026-06-09 00:40:02', 1),
(42014, 3, 1, '2026-06-13', '21:00:00', 90000.00, '2026-06-09 00:40:02', 2),
(42015, 3, 2, '2026-06-13', '18:00:00', 90000.00, '2026-06-09 00:40:02', 4),
(42016, 3, 1, '2026-06-14', '15:00:00', 90000.00, '2026-06-09 00:40:02', 1),
(42017, 3, 2, '2026-06-14', '15:00:00', 90000.00, '2026-06-09 00:40:02', 4),
(42018, 3, 2, '2026-06-14', '21:00:00', 90000.00, '2026-06-09 00:40:02', 4),
(42019, 3, 1, '2026-06-15', '09:00:00', 90000.00, '2026-06-09 00:40:02', 1),
(42020, 3, 1, '2026-06-15', '15:00:00', 90000.00, '2026-06-09 00:40:02', 1),
(42021, 3, 2, '2026-06-15', '09:00:00', 90000.00, '2026-06-09 00:40:02', 3),
(42022, 4, 2, '2026-06-09', '15:00:00', 90000.00, '2026-06-09 00:40:02', 3),
(42023, 4, 1, '2026-06-10', '09:00:00', 90000.00, '2026-06-09 00:40:02', 2),
(42024, 4, 1, '2026-06-11', '09:00:00', 90000.00, '2026-06-09 00:40:02', 2),
(42025, 4, 2, '2026-06-11', '21:00:00', 90000.00, '2026-06-09 00:40:02', 3),
(42026, 4, 1, '2026-06-12', '15:00:00', 90000.00, '2026-06-09 00:40:02', 1),
(42027, 4, 2, '2026-06-12', '15:00:00', 90000.00, '2026-06-09 00:40:02', 3),
(42028, 4, 1, '2026-06-14', '18:00:00', 90000.00, '2026-06-09 00:40:02', 2),
(42029, 4, 2, '2026-06-14', '18:00:00', 90000.00, '2026-06-09 00:40:03', 4),
(42030, 4, 1, '2026-06-15', '21:00:00', 90000.00, '2026-06-09 00:40:03', 2),
(42031, 4, 2, '2026-06-15', '12:00:00', 90000.00, '2026-06-09 00:40:03', 4),
(42032, 5, 2, '2026-06-10', '15:00:00', 90000.00, '2026-06-09 00:40:03', 4),
(42033, 5, 1, '2026-06-12', '21:00:00', 90000.00, '2026-06-09 00:40:03', 2),
(42034, 18, 1, '2026-06-09', '21:00:00', 90000.00, '2026-06-09 00:40:03', 2),
(42035, 18, 2, '2026-06-10', '21:00:00', 90000.00, '2026-06-09 00:40:03', 4),
(42036, 18, 2, '2026-06-11', '12:00:00', 90000.00, '2026-06-09 00:40:03', 4),
(42037, 18, 2, '2026-06-12', '21:00:00', 90000.00, '2026-06-09 00:40:03', 3),
(42038, 18, 2, '2026-06-13', '12:00:00', 90000.00, '2026-06-09 00:40:03', 3),
(42039, 18, 2, '2026-06-13', '15:00:00', 90000.00, '2026-06-09 00:40:03', 3),
(42040, 19, 2, '2026-06-10', '18:00:00', 90000.00, '2026-06-09 00:40:03', 3),
(42041, 19, 1, '2026-06-11', '21:00:00', 90000.00, '2026-06-09 00:40:03', 2),
(42042, 20, 2, '2026-06-12', '18:00:00', 90000.00, '2026-06-09 00:40:03', 4),
(42043, 20, 1, '2026-06-14', '09:00:00', 90000.00, '2026-06-09 00:40:03', 2),
(42044, 23, 2, '2026-06-13', '09:00:00', 90000.00, '2026-06-09 00:40:03', 4),
(42045, 27, 2, '2026-06-14', '12:00:00', 90000.00, '2026-06-09 00:40:03', 3),
(42046, 20, 3, '2026-06-17', '08:00:00', 120000.00, '2026-06-15 15:42:54', 5);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `benefits` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `subscriptions`
--

INSERT INTO `subscriptions` (`id`, `name`, `price`, `description`, `benefits`, `created_at`) VALUES
(1, 'Free', 0.00, 'Xem trailer, phim miễn phí', 'Giới hạn nội dung, có quảng cáo', '2025-11-09 16:03:14'),
(2, 'Silver', 79000.00, 'Xem phim HD không quảng cáo', 'HD quality, không quảng cáo', '2025-11-09 16:03:14'),
(3, 'Gold', 129000.00, 'Full HD, nội dung độc quyền', 'Full HD, nội dung mới', '2025-11-09 16:03:14'),
(4, 'Premium', 199000.00, '4K, xem sớm, ưu đãi vé rạp', '4K, early access, ưu đãi vé', '2025-11-09 16:03:14'),
(5, 'Basic', 49000.00, 'Gói cơ bản với chất lượng SD', 'SD quality, có quảng cáo', '2025-11-12 07:41:09');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('Mới','Đang xử lý','Đã giải quyết','Đã đóng') DEFAULT 'Mới',
  `priority` enum('Thấp','Trung bình','Cao','Khẩn cấp') DEFAULT 'Trung bình',
  `tags` varchar(255) DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `support_tickets`
--

INSERT INTO `support_tickets` (`id`, `user_id`, `subject`, `message`, `status`, `priority`, `tags`, `assigned_to`, `created_at`, `updated_at`) VALUES
(1, 1, 'Không thể đăng nhập', 'Tôi không thể đăng nhập vào tài khoản của mình', 'Mới', 'Cao', NULL, NULL, '2025-11-12 07:41:09', '2025-11-12 07:41:09'),
(2, 2, 'Vấn đề thanh toán', 'Giao dịch của tôi bị lỗi khi thanh toán', 'Đang xử lý', 'Trung bình', NULL, NULL, '2025-11-12 07:41:09', '2025-11-12 07:41:09'),
(3, 3, 'Yêu cầu hoàn tiền', 'Tôi muốn hoàn tiền cho vé đã mua', 'Mới', 'Cao', NULL, NULL, '2025-11-12 07:41:09', '2025-11-12 07:41:09'),
(4, 4, 'Câu hỏi về gói dịch vụ', 'Tôi muốn biết thêm về gói Premium', 'Đã giải quyết', 'Thấp', NULL, NULL, '2025-11-12 07:41:09', '2025-11-12 07:41:09'),
(5, 5, 'Lỗi phát video', 'Video không phát được trên trình duyệt của tôi', 'Đang xử lý', 'Trung bình', NULL, NULL, '2025-11-12 07:41:09', '2025-11-12 07:41:09'),
(6, 1, 'Thay đổi thông tin tài khoản', 'Tôi muốn thay đổi email đăng nhập', 'Mới', 'Thấp', NULL, NULL, '2025-11-12 07:41:09', '2025-11-12 07:41:09'),
(7, 9, 'Khác', 'rạp xa quá', 'Mới', 'Thấp', 'Mua bán vé - Khác', NULL, '2025-12-09 02:01:13', '2025-12-09 02:01:13');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `system_config`
--

CREATE TABLE `system_config` (
  `id` int(11) NOT NULL,
  `config_key` varchar(100) NOT NULL,
  `config_value` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `system_config`
--

INSERT INTO `system_config` (`id`, `config_key`, `config_value`, `description`, `updated_by`, `updated_at`) VALUES
(1, 'maintenance_mode', '0', 'Chế độ bảo trì (0=off, 1=on)', NULL, '2025-11-10 16:41:17'),
(2, 'max_upload_size', '500', 'Kích thước upload tối đa (MB)', NULL, '2025-11-10 16:41:17'),
(3, 'payment_gateway', 'vnpay', 'Cổng thanh toán mặc định', NULL, '2025-11-10 16:41:17'),
(4, 'default_currency', 'VND', 'Đơn vị tiền tệ', NULL, '2025-11-10 16:41:17'),
(5, 'site_name', 'CineHub', 'Tên website', NULL, '2025-11-10 16:41:17');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `theaters`
--

CREATE TABLE `theaters` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_screens` int(11) DEFAULT 1,
  `address` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `theaters`
--

INSERT INTO `theaters` (`id`, `name`, `location`, `phone`, `created_at`, `total_screens`, `address`, `is_active`, `latitude`, `longitude`) VALUES
(1, 'CGV Vincom Center', 'Hà Nội', '0241234567', '2025-11-12 07:41:09', NULL, '72 Lê Thánh Tôn, Hoàn Kiếm, Hà Nội', 1, 21.02851100, 105.80481700),
(2, 'CGV Landmark', 'Hà Nội', '0242345678', '2025-11-12 07:41:09', 6, '72A Nguyễn Trãi, Thanh Xuân, Hà Nội', 1, 20.99839400, 105.79438800),
(3, 'Lotte Cinema', 'Hồ Chí Minh', '0283456789', '2025-11-12 07:41:09', 9, '469 Nguyễn Hữu Thọ, Quận 7, TP.HCM', 1, 10.72988800, 106.72286200),
(4, 'Galaxy Cinema', 'Đà Nẵng', '0236456789', '2025-11-12 07:41:09', 7, '910A Ngô Quyền, Sơn Trà, Đà Nẵng', 1, 16.05440700, 108.20216700),
(5, 'BHD Star Cineplex', 'Hồ Chí Minh', '0284567890', '2025-11-12 07:41:09', 9, 'L3-Vincom Center, 72 Lê Thánh Tôn, Quận 1, TP.HCM', 1, 10.77690000, 106.70090000),
(6, 'Lotte Cinema Thanh Hóa', 'Thanh Hóa', '0237378888', '2025-12-10 10:00:00', 5, 'Tầng 3, Lotte Mart Thanh Hóa, Đường Trần Phú, TP. Thanh Hóa', 1, 19.80670000, 105.77500000),
(7, 'Beta Cinema Thanh Hóa', 'Thanh Hóa', '0237379999', '2025-12-10 10:00:00', 4, 'Tầng 4, Trung tâm thương mại Beta, Đường Lê Lợi, TP. Thanh Hóa', 1, 19.81000000, 105.78000000);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `theater_managers`
--

CREATE TABLE `theater_managers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `theater_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `theater_screens`
--

CREATE TABLE `theater_screens` (
  `id` int(11) NOT NULL,
  `theater_id` int(11) NOT NULL,
  `screen_name` varchar(100) NOT NULL,
  `total_seats` int(11) NOT NULL,
  `seat_layout` text DEFAULT NULL,
  `seat_layout_config` text DEFAULT NULL COMMENT 'JSON config for seat layout: rows, cols, vip_rows, couple_rows, etc.',
  `screen_type` enum('2D','3D','IMAX','4DX') DEFAULT '2D',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `theater_screens`
--

INSERT INTO `theater_screens` (`id`, `theater_id`, `screen_name`, `total_seats`, `seat_layout`, `seat_layout_config`, `screen_type`, `is_active`, `created_at`) VALUES
(1, 1, 'Phòng 1', 120, NULL, '{\"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\", \"L\"], \"cols\": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12], \"vip_rows\": [\"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\"], \"couple_rows\": [\"L\"], \"layout_type\": \"standard\"}', '2D', 1, '2025-11-12 07:41:09'),
(2, 1, 'Phòng 2', 150, NULL, '{\"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\", \"L\", \"M\"], \"cols\": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14], \"vip_rows\": [\"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\", \"L\"], \"couple_rows\": [\"M\"], \"layout_type\": \"standard\"}', '3D', 1, '2025-11-12 07:41:09'),
(3, 2, 'Phòng 1', 100, NULL, '{\"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\"], \"cols\": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10], \"vip_rows\": [\"D\", \"E\", \"F\"], \"couple_rows\": [\"J\"], \"layout_type\": \"standard\"}', '2D', 1, '2025-11-12 07:41:09'),
(4, 2, 'Phòng 2', 120, NULL, '{\"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\", \"L\"], \"cols\": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10], \"vip_rows\": [\"E\", \"F\", \"G\"], \"couple_rows\": [\"L\"], \"layout_type\": \"standard\"}', '3D', 1, '2025-11-12 07:41:09'),
(5, 3, 'Phòng 1', 210, NULL, '{\"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\", \"L\", \"M\", \"N\"], \"cols\": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15], \"vip_rows\": [\"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\"], \"couple_rows\": [\"N\"], \"maintenance_seats\": [], \"layout_type\": \"standard\"}', '4DX', 1, '2025-11-12 07:41:09'),
(6, 3, 'Phòng 2', 180, NULL, '{\"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\", \"L\", \"M\"], \"cols\": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14], \"vip_rows\": [\"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\", \"L\"], \"couple_rows\": [\"M\"], \"layout_type\": \"standard\"}', '3D', 1, '2025-11-12 07:41:09'),
(7, 4, 'Phòng 1', 110, NULL, '{\"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\"], \"cols\": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10], \"vip_rows\": [\"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\"], \"couple_rows\": [\"K\"], \"layout_type\": \"standard\"}', '2D', 1, '2025-11-12 07:41:09'),
(8, 5, 'Phòng 1', 130, NULL, '{\"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\", \"L\"], \"cols\": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11], \"vip_rows\": [\"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\"], \"couple_rows\": [\"L\"], \"layout_type\": \"standard\"}', '3D', 1, '2025-11-12 07:41:09'),
(9, 1, 'Phòng 3', 200, NULL, '{\"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\"], \"cols\": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17], \"vip_rows\": [\"D\", \"E\", \"F\", \"G\", \"H\", \"I\"], \"couple_rows\": [], \"layout_type\": \"complex\", \"seat_groups\": [{\"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\"], \"cols\": [1, 2]}, {\"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\"], \"cols\": [3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15]}, {\"rows\": [\"H\", \"I\"], \"cols\": [16, 17]}]}', 'IMAX', 1, '2025-11-12 07:41:09'),
(11, 3, 'Phòng 3', 180, NULL, '{\r\n  \"layout_type\": \"grouped\",\r\n  \"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\"],\r\n  \"seat_groups\": [\r\n    {\r\n      \"name\": \"Khối trái\",\r\n      \"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\"],\r\n      \"cols\": [1, 2, 3, 4]\r\n    },\r\n    {\r\n      \"name\": \"Khối giữa\",\r\n      \"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\"],\r\n      \"cols\": [5, 6, 7, 8]\r\n    },\r\n    {\r\n      \"name\": \"Khối phải\",\r\n      \"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\"],\r\n      \"cols\": [9, NULL, NULL, 12]\r\n    },\r\n    {\r\n      \"name\": \"Ghế riêng lẻ\",\r\n      \"rows\": [\"H\", \"I\"],\r\n      \"cols\": [13, 14]\r\n    }\r\n  ],\r\n  \"vip_rows\": [\"D\", \"E\", \"F\", \"G\", \"H\"],\r\n  \"couple_rows\": [],\r\n  \"normal_price\": 120000,\r\n  \"vip_price\": 180000,\r\n  \"couple_price\": 240000\r\n}', '2D', 1, '2025-11-28 01:03:25'),
(12, 3, 'Phòng 4', 224, NULL, '{\r\n  \"layout_type\": \"grouped\",\r\n  \"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\"],\r\n  \"seat_groups\": [\r\n    {\r\n      \"name\": \"Khối 1\",\r\n      \"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\"],\r\n      \"cols\": [1, 2, 3, 4, 5, 6]\r\n    },\r\n    {\r\n      \"name\": \"Khối 2\",\r\n      \"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\"],\r\n      \"cols\": [7, NULL, 9, NULL, NULL, NULL, NULL, NULL, 15, 16]\r\n    },\r\n    {\r\n      \"name\": \"Khối 3\",\r\n      \"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\"],\r\n      \"cols\": [17, 18, 19]\r\n    },\r\n    {\r\n      \"name\": \"Khối 4\",\r\n      \"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\"],\r\n      \"cols\": [20, 21, 22, 23, 24, 25]\r\n    }\r\n  ],\r\n  \"vip_rows\": [\"E\", \"F\", \"G\"],\r\n  \"couple_rows\": [],\r\n  \"normal_price\": 130000,\r\n  \"vip_price\": 200000,\r\n  \"couple_price\": 260000\r\n}', '3D', 1, '2025-11-28 01:03:25'),
(13, 2, 'Phòng 2', 144, NULL, '{\"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\", \"L\"], \"cols\": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12], \"vip_rows\": [\"E\", \"F\", \"G\"], \"couple_rows\": [\"L\"], \"layout_type\": \"standard\"}', '2D', 1, '2025-12-04 02:26:18'),
(14, 2, 'Phòng 3', 144, NULL, '{\"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\", \"L\"], \"cols\": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12], \"vip_rows\": [\"E\", \"F\", \"G\"], \"couple_rows\": [\"L\"], \"layout_type\": \"standard\"}', '3D', 1, '2025-12-04 02:26:18'),
(15, 1, 'Phòng 3', 144, NULL, '{\"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\", \"L\"], \"cols\": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12], \"vip_rows\": [\"E\", \"F\", \"G\"], \"couple_rows\": [\"L\"], \"layout_type\": \"standard\"}', '2D', 1, '2025-12-04 02:26:18'),
(16, 1, 'Phòng 4', 144, NULL, '{\"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\", \"L\"], \"cols\": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12], \"vip_rows\": [\"E\", \"F\", \"G\"], \"couple_rows\": [\"L\"], \"layout_type\": \"standard\"}', '3D', 1, '2025-12-04 02:26:18'),
(17, 5, 'Phòng 2', 144, NULL, '{\"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\", \"L\"], \"cols\": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12], \"vip_rows\": [\"E\", \"F\", \"G\"], \"couple_rows\": [\"L\"], \"layout_type\": \"standard\"}', '3D', 1, '2025-12-04 02:59:28'),
(18, 5, 'Phòng 3', 144, NULL, '{\"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\", \"L\"], \"cols\": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12], \"vip_rows\": [\"E\", \"F\", \"G\"], \"couple_rows\": [\"L\"], \"layout_type\": \"standard\"}', '2D', 1, '2025-12-04 02:59:28'),
(19, 5, 'Phòng 4', 144, NULL, '{\"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\", \"L\"], \"cols\": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12], \"vip_rows\": [\"E\", \"F\", \"G\"], \"couple_rows\": [\"L\"], \"layout_type\": \"standard\"}', '3D', 1, '2025-12-04 02:59:28'),
(20, 5, 'Phòng 5', 144, NULL, '{\"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\", \"L\"], \"cols\": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12], \"vip_rows\": [\"E\", \"F\", \"G\"], \"couple_rows\": [\"L\"], \"layout_type\": \"standard\"}', '2D', 1, '2025-12-04 02:59:28'),
(21, 5, 'Phòng 6', 144, NULL, '{\"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\", \"L\"], \"cols\": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12], \"vip_rows\": [\"E\", \"F\", \"G\"], \"couple_rows\": [\"L\"], \"layout_type\": \"standard\"}', '3D', 1, '2025-12-04 02:59:28'),
(22, 2, 'Phòng 4', 144, NULL, '{\"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\", \"L\"], \"cols\": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12], \"vip_rows\": [\"E\", \"F\", \"G\"], \"couple_rows\": [\"L\"], \"layout_type\": \"standard\"}', '3D', 1, '2025-12-04 02:59:28'),
(23, 2, 'Phòng 5', 144, NULL, '{\"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\", \"L\"], \"cols\": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12], \"vip_rows\": [\"E\", \"F\", \"G\"], \"couple_rows\": [\"L\"], \"layout_type\": \"standard\"}', '2D', 1, '2025-12-04 02:59:28'),
(24, 2, 'Phòng 6', 144, NULL, '{\"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\", \"L\"], \"cols\": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12], \"vip_rows\": [\"E\", \"F\", \"G\"], \"couple_rows\": [\"L\"], \"layout_type\": \"standard\"}', '3D', 1, '2025-12-04 02:59:28'),
(25, 1, 'Phòng 5', 144, NULL, '{\"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\", \"L\"], \"cols\": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12], \"vip_rows\": [\"E\", \"F\", \"G\"], \"couple_rows\": [\"L\"], \"layout_type\": \"standard\"}', '2D', 1, '2025-12-04 02:59:28'),
(26, 1, 'Phòng 6', 144, NULL, '{\"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\", \"L\"], \"cols\": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12], \"vip_rows\": [\"E\", \"F\", \"G\"], \"couple_rows\": [\"L\"], \"layout_type\": \"standard\"}', '3D', 1, '2025-12-04 02:59:28'),
(27, 3, 'Phòng 5', 144, NULL, '{\"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\", \"L\"], \"cols\": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12], \"vip_rows\": [\"E\", \"F\", \"G\"], \"couple_rows\": [\"L\"], \"layout_type\": \"standard\"}', '3D', 1, '2025-12-04 02:59:28'),
(28, 3, 'Phòng 6', 144, NULL, '{\"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\", \"L\"], \"cols\": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12], \"vip_rows\": [\"E\", \"F\", \"G\"], \"couple_rows\": [\"L\"], \"layout_type\": \"standard\"}', '2D', 1, '2025-12-04 02:59:28'),
(29, 3, 'Phòng 7', 144, NULL, '{\"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\", \"L\"], \"cols\": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12], \"vip_rows\": [\"E\", \"F\", \"G\", \"H\"], \"couple_rows\": [\"L\"], \"maintenance_seats\": [], \"layout_type\": \"standard\"}', '3D', 1, '2025-12-04 02:59:28'),
(30, 6, 'Phòng 1', 144, NULL, '{\"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\", \"L\"], \"cols\": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12], \"vip_rows\": [\"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\"], \"couple_rows\": [\"L\"], \"layout_type\": \"standard\"}', '2D', 1, '2025-12-10 10:00:00'),
(31, 6, 'Phòng 2', 150, NULL, '{\"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\", \"L\", \"M\"], \"cols\": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14], \"vip_rows\": [\"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\", \"L\"], \"couple_rows\": [\"M\"], \"layout_type\": \"standard\"}', '3D', 1, '2025-12-10 10:00:00'),
(32, 6, 'Phòng 3', 144, NULL, '{\"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\", \"L\"], \"cols\": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12], \"vip_rows\": [\"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\"], \"couple_rows\": [\"L\"], \"layout_type\": \"standard\"}', '2D', 1, '2025-12-10 10:00:00'),
(33, 6, 'Phòng 4', 150, NULL, '{\"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\", \"L\", \"M\"], \"cols\": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14], \"vip_rows\": [\"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\", \"L\"], \"couple_rows\": [\"M\"], \"layout_type\": \"standard\"}', '3D', 1, '2025-12-10 10:00:00'),
(34, 6, 'Phòng 5', 144, NULL, '{\"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\", \"L\"], \"cols\": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12], \"vip_rows\": [\"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\"], \"couple_rows\": [\"L\"], \"layout_type\": \"standard\"}', '2D', 1, '2025-12-10 10:00:00'),
(35, 7, 'Phòng 1', 144, NULL, '{\"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\", \"L\"], \"cols\": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12], \"vip_rows\": [\"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\"], \"couple_rows\": [\"L\"], \"layout_type\": \"standard\"}', '2D', 1, '2025-12-10 10:00:00'),
(36, 7, 'Phòng 2', 150, NULL, '{\"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\", \"L\", \"M\"], \"cols\": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14], \"vip_rows\": [\"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\", \"L\"], \"couple_rows\": [\"M\"], \"layout_type\": \"standard\"}', '3D', 1, '2025-12-10 10:00:00'),
(37, 7, 'Phòng 3', 144, NULL, '{\"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\", \"L\"], \"cols\": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12], \"vip_rows\": [\"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\"], \"couple_rows\": [\"L\"], \"layout_type\": \"standard\"}', '2D', 1, '2025-12-10 10:00:00'),
(38, 7, 'Phòng 4', 150, NULL, '{\"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\", \"L\", \"M\"], \"cols\": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14], \"vip_rows\": [\"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\", \"L\"], \"couple_rows\": [\"M\"], \"layout_type\": \"standard\"}', '3D', 1, '2025-12-10 10:00:00'),
(39, 3, 'Phòng 8', 140, NULL, NULL, '3D', 1, '2025-12-09 15:13:15'),
(40, 1, 'Phòng 8', 247, NULL, '{\"layout_type\": \"custom_groups\", \"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"J\", \"K\", \"L\"], \"vip_rows\": [\"F\", \"G\", \"H\"], \"vip_cols_start\": 9, \"vip_cols_end\": 20, \"couple_rows\": [\"L\"], \"row_configs\": {\"A\": {\"groups\": [{\"cols\": [19, 18], \"type\": \"left\"}, {\"cols\": [17, 16, 15, 14, 13, 12, 11, 10, 9, 8, 7, 6, 5, 4, 3], \"type\": \"center\"}, {\"cols\": [2, 1], \"type\": \"right\"}]}, \"B\": {\"groups\": [{\"cols\": [21, 20, 19], \"type\": \"left\"}, {\"cols\": [18, 17, 16, 15, 14, 13, 12, 11, 10, 9, 8, 7, 6, 5, 4], \"type\": \"center\"}, {\"cols\": [3, 2, 1], \"type\": \"right\"}]}, \"C\": {\"groups\": [{\"cols\": [24, 23, 22, 21], \"type\": \"left\"}, {\"cols\": [20, 19, 18, 17, 16, 15, 14, 13, 12, 11, 10, 9, 8, 7, 6], \"type\": \"center\"}, {\"cols\": [5, 4, 3, 2], \"type\": \"right\"}]}, \"D\": {\"groups\": [{\"cols\": [26, 25, 24, 23], \"type\": \"left\"}, {\"cols\": [22, 21, 20, 19, 18, 17, 16, 15, 14, 13, 12, 11, 10, 9, 8], \"type\": \"center\"}, {\"cols\": [7, 6, 5, 4], \"type\": \"right\"}]}, \"E\": {\"groups\": [{\"cols\": [27, 26, 25, 24], \"type\": \"left\"}, {\"cols\": [23, 22, 21, 20, 19, 18, 17, 16, 15, 14, 13, 12, 11, 10, 9], \"type\": \"center\"}, {\"cols\": [8, 7, 6, 5], \"type\": \"right\"}]}, \"F\": {\"groups\": [{\"cols\": [27, 26, 25, 24], \"type\": \"left\"}, {\"cols\": [23, 22, 21, 20, 19, 18, 17, 16, 15, 14, 13, 12, 11, 10, 9], \"type\": \"center\"}, {\"cols\": [8, 7, 6, 5], \"type\": \"right\"}]}, \"G\": {\"groups\": [{\"cols\": [27, 26, 25, 24], \"type\": \"left\"}, {\"cols\": [23, 22, 21, 20, 19, 18, 17, 16, 15, 14, 13, 12, 11, 10, 9], \"type\": \"center\"}, {\"cols\": [8, 7, 6, 5], \"type\": \"right\"}]}, \"H\": {\"groups\": [{\"cols\": [27, 26, 25, 24], \"type\": \"left\"}, {\"cols\": [23, 22, 21, 20, 19, 18, 17, 16, 15, 14, 13, 12, 11, 10, 9], \"type\": \"center\"}, {\"cols\": [8, 7, 6, 5], \"type\": \"right\"}]}, \"J\": {\"groups\": [{\"cols\": [27, 26, 25, 24], \"type\": \"left\"}, {\"cols\": [23, 22, 21, 20, 19, 18, 17, 16, 15, 14, 13, 12, 11, 10, 9], \"type\": \"center\"}, {\"cols\": [8, 7, 6, 5], \"type\": \"right\"}]}, \"K\": {\"groups\": [{\"cols\": [27, 26, 25, 24], \"type\": \"left\"}, {\"cols\": [23, 22, 21, 20, 19, 18, 17, 16, 15, 14, 13, 12, 11, 10, 9], \"type\": \"center\"}, {\"cols\": [8, 7, 6, 5], \"type\": \"right\"}]}, \"L\": {\"groups\": [{\"cols\": [27, 26, 25, 24], \"type\": \"left\"}, {\"cols\": [23, 22, 21, 20, 19, 18, 17, 16, 15, 14, 13, 12, 11, 10, 9], \"type\": \"center\"}, {\"cols\": [8, 7, 6, 5], \"type\": \"right\"}]}}}', '2D', 1, '2025-12-13 11:08:29'),
(41, 3, 'Phòng 9', 216, NULL, '{\"rows\":[\"A\",\"B\",\"C\",\"D\",\"E\",\"F\",\"G\",\"H\",\"I\",\"J\",\"K\",\"L\"],\"cols\":[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22],\"vip_rows\":[\"E\",\"F\",\"G\"],\"couple_rows\":[\"L\"],\"maintenance_seats\":[],\"normal_price\":120000,\"vip_price\":180000,\"couple_price\":240000,\"layout_type\":\"grouped\",\"seat_groups\":[{\"rows\":[\"A\",\"B\",\"C\",\"D\",\"E\",\"F\",\"G\",\"H\",\"I\",\"J\",\"K\",\"L\"],\"cols\":[1,2,3,4,5,6]},{\"rows\":[\"A\",\"B\",\"C\",\"D\",\"E\",\"F\",\"G\",\"H\",\"I\",\"J\",\"K\",\"L\"],\"cols\":[9,10,11,12,13,14]},{\"rows\":[\"A\",\"B\",\"C\",\"D\",\"E\",\"F\",\"G\",\"H\",\"I\",\"J\",\"K\",\"L\"],\"cols\":[17,18,19,20,21,22]}]}', '3D', 1, '2025-12-13 11:58:23');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `showtime_id` int(11) NOT NULL,
  `booking_pending_id` int(11) DEFAULT NULL COMMENT 'Liên kết với booking_pending nếu vé được tạo từ pending booking',
  `seat` varchar(10) NOT NULL,
  `seat_type` enum('normal','vip','couple') DEFAULT 'normal',
  `qr_code` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `status` enum('Đã đặt','Đã hủy') DEFAULT 'Đã đặt',
  `is_picked_up` tinyint(1) DEFAULT 0 COMMENT 'Vé đã được lấy tại quầy chưa',
  `picked_up_at` datetime DEFAULT NULL COMMENT 'Thời gian vé được lấy',
  `picked_up_by` int(11) DEFAULT NULL COMMENT 'ID nhân viên xác nhận vé đã lấy',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `tickets`
--

INSERT INTO `tickets` (`id`, `user_id`, `showtime_id`, `booking_pending_id`, `seat`, `seat_type`, `qr_code`, `price`, `status`, `is_picked_up`, `picked_up_at`, `picked_up_by`, `created_at`) VALUES
(60, 9, 1000, NULL, 'E9', 'vip', 'TICKET_692c890a2c7c6_9_1000_1764526346_E9', 200000.00, 'Đã đặt', 0, NULL, NULL, '2025-11-30 18:12:26'),
(61, 9, 1001, NULL, 'G5', 'vip', 'TICKET_692e43e81f834_9_1001_1764639720_G5', 180000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-02 01:42:00'),
(62, 9, 1001, NULL, 'G6', 'vip', 'TICKET_692e43e824faa_9_1001_1764639720_G6', 180000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-02 01:42:00'),
(63, 9, 1001, NULL, 'G7', 'vip', 'TICKET_692e454f87f53_9_1001_1764640079_G7', 180000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-02 01:47:59'),
(64, 9, 1001, NULL, 'G8', 'vip', 'TICKET_692e454f88fbb_9_1001_1764640079_G8', 180000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-02 01:47:59'),
(65, 9, 1001, NULL, 'F5', 'vip', 'TICKET_692e4a40e5a02_9_1001_1764641344_F5', 180000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-02 02:09:04'),
(66, 9, 1001, NULL, 'F6', 'vip', 'TICKET_692e4a40e6a61_9_1001_1764641344_F6', 180000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-02 02:09:04'),
(67, 9, 1001, NULL, 'F7', 'vip', 'TICKET_692e4b6354dea_9_1001_1764641635_F7', 180000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-02 02:13:55'),
(68, 9, 1001, NULL, 'F8', 'vip', 'TICKET_692e4b635572e_9_1001_1764641635_F8', 180000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-02 02:13:55'),
(69, 9, 1001, NULL, 'E7', 'vip', 'TICKET_692e5f425d498_9_359_1764646722_E7', 180000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-02 03:38:42'),
(70, 9, 1001, NULL, 'E8', 'vip', 'TICKET_692e5f425dc79_9_359_1764646722_E8', 180000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-02 03:38:42'),
(84, 10, 1002, NULL, 'L5', 'couple', 'TICKET_69310c2a89f96_10_364_1764822058_L5', 120000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-04 04:20:58'),
(85, 10, 1002, NULL, 'L6', 'couple', 'TICKET_69310c2a8b138_10_364_1764822058_L6', 120000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-04 04:20:58'),
(103, 3, 1000, NULL, 'D1', 'normal', NULL, 200000.00, 'Đã đặt', 1, '2025-12-02 06:43:26', 5, '2025-12-01 14:43:26'),
(104, 3, 1000, NULL, 'I2', 'normal', NULL, 200000.00, 'Đã đặt', 1, '2025-12-02 04:43:26', 2, '2025-12-01 14:43:26'),
(105, 3, 1000, NULL, 'H3', 'normal', NULL, 200000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-01 14:43:26'),
(106, 3, 1000, NULL, 'I4', 'normal', NULL, 200000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-01 14:43:26'),
(107, 3, 1000, NULL, 'E5', 'normal', NULL, 200000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-01 14:43:26'),
(108, 3, 1000, NULL, 'B6', 'normal', NULL, 200000.00, 'Đã đặt', 1, '2025-12-02 03:43:26', 1, '2025-12-01 14:43:26'),
(109, 3, 1000, NULL, 'J7', 'normal', NULL, 200000.00, 'Đã đặt', 1, '2025-12-02 01:43:26', 3, '2025-12-01 14:43:26'),
(110, 3, 1000, NULL, 'E8', 'normal', NULL, 200000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-01 14:43:26'),
(111, 3, 1000, NULL, 'B9', 'normal', NULL, 200000.00, 'Đã đặt', 1, '2025-12-02 02:43:26', 4, '2025-12-01 14:43:26'),
(112, 3, 1000, NULL, 'C10', 'normal', NULL, 200000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-01 14:43:26'),
(113, 5, 1001, NULL, 'A1', 'normal', NULL, 200000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-02 14:43:26'),
(114, 5, 1001, NULL, 'F2', 'normal', NULL, 200000.00, 'Đã đặt', 1, '2025-12-03 05:43:26', 1, '2025-12-02 14:43:26'),
(115, 5, 1001, NULL, 'J3', 'normal', NULL, 200000.00, 'Đã đặt', 1, '2025-12-02 16:43:26', 2, '2025-12-02 14:43:26'),
(116, 5, 1001, NULL, 'D4', 'normal', NULL, 200000.00, 'Đã đặt', 1, '2025-12-02 15:43:26', 3, '2025-12-02 14:43:26'),
(117, 5, 1001, NULL, 'E5', 'normal', NULL, 200000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-02 14:43:26'),
(118, 5, 1001, NULL, 'H6', 'normal', NULL, 200000.00, 'Đã đặt', 1, '2025-12-03 03:43:26', 3, '2025-12-02 14:43:26'),
(119, 5, 1001, NULL, 'C7', 'normal', NULL, 200000.00, 'Đã đặt', 1, '2025-12-02 22:43:26', 3, '2025-12-02 14:43:26'),
(120, 5, 1001, NULL, 'F8', 'normal', NULL, 200000.00, 'Đã đặt', 1, '2025-12-03 06:43:26', 4, '2025-12-02 14:43:26'),
(121, 5, 1001, NULL, 'B9', 'normal', NULL, 200000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-02 14:43:26'),
(122, 5, 1001, NULL, 'C10', 'normal', NULL, 200000.00, 'Đã đặt', 1, '2025-12-03 06:43:26', 1, '2025-12-02 14:43:26'),
(123, 2, 1000, NULL, 'I1', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-05 09:43:26', 4, '2025-12-04 14:43:26'),
(124, 2, 1000, NULL, 'H2', 'normal', NULL, 120000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-04 14:43:26'),
(125, 2, 1000, NULL, 'B3', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-04 15:43:26', 5, '2025-12-04 14:43:26'),
(126, 2, 1000, NULL, 'E4', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-04 21:43:26', 5, '2025-12-04 14:43:26'),
(127, 2, 1000, NULL, 'G5', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-05 09:43:26', 1, '2025-12-04 14:43:26'),
(128, 2, 1000, NULL, 'C6', 'normal', NULL, 120000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-04 14:43:26'),
(129, 2, 1000, NULL, 'J7', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-04 20:43:26', 3, '2025-12-04 14:43:26'),
(130, 2, 1000, NULL, 'H8', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-05 12:43:26', 1, '2025-12-04 14:43:26'),
(131, 4, 1001, NULL, 'I1', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-04 23:43:26', 5, '2025-12-04 14:43:26'),
(132, 4, 1001, NULL, 'I2', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-05 07:43:26', 5, '2025-12-04 14:43:26'),
(133, 4, 1001, NULL, 'I3', 'normal', NULL, 120000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-04 14:43:26'),
(134, 4, 1001, NULL, 'B4', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-05 03:43:26', 4, '2025-12-04 14:43:26'),
(135, 4, 1001, NULL, 'I5', 'normal', NULL, 120000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-04 14:43:26'),
(156, 10, 1002, NULL, 'F1', 'normal', NULL, 120000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-04 14:43:26'),
(157, 10, 1002, NULL, 'E2', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-04 23:43:26', 2, '2025-12-04 14:43:26'),
(158, 10, 1002, NULL, 'H3', 'normal', NULL, 120000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-04 14:43:26'),
(159, 10, 1002, NULL, 'G4', 'normal', NULL, 120000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-04 14:43:26'),
(160, 10, 1002, NULL, 'C5', 'normal', NULL, 120000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-04 14:43:26'),
(161, 10, 1002, NULL, 'H6', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-05 11:43:26', 2, '2025-12-04 14:43:26'),
(162, 10, 1002, NULL, 'H7', 'normal', NULL, 120000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-04 14:43:26'),
(163, 10, 1002, NULL, 'J8', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-04 20:43:26', 4, '2025-12-04 14:43:26'),
(164, 10, 1002, NULL, 'G9', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-05 04:43:26', 4, '2025-12-04 14:43:26'),
(165, 10, 1002, NULL, 'I10', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-05 05:43:26', 1, '2025-12-04 14:43:26'),
(166, 10, 1002, NULL, 'H11', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-05 13:43:26', 2, '2025-12-04 14:43:26'),
(167, 1, 1002, NULL, 'J1', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-05 01:43:26', 4, '2025-12-04 14:43:26'),
(168, 1, 1002, NULL, 'I2', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-04 23:43:26', 2, '2025-12-04 14:43:26'),
(169, 1, 1002, NULL, 'G3', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-04 21:43:26', 4, '2025-12-04 14:43:26'),
(214, 4, 1003, NULL, 'A1', 'normal', NULL, 120000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-04 14:43:26'),
(215, 4, 1003, NULL, 'F2', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-05 09:43:26', 4, '2025-12-04 14:43:26'),
(216, 4, 1003, NULL, 'I3', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-04 17:43:26', 2, '2025-12-04 14:43:26'),
(217, 4, 1003, NULL, 'D4', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-04 17:43:26', 5, '2025-12-04 14:43:26'),
(218, 4, 1003, NULL, 'I5', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-05 12:43:26', 2, '2025-12-04 14:43:26'),
(219, 4, 1003, NULL, 'C6', 'normal', NULL, 120000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-04 14:43:26'),
(220, 4, 1003, NULL, 'I7', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-05 10:43:26', 1, '2025-12-04 14:43:26'),
(221, 4, 1003, NULL, 'I8', 'normal', NULL, 120000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-04 14:43:26'),
(222, 4, 1003, NULL, 'D9', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-04 18:43:26', 1, '2025-12-04 14:43:26'),
(223, 4, 1003, NULL, 'G10', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-04 21:43:26', 3, '2025-12-04 14:43:26'),
(224, 4, 1003, NULL, 'C11', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-05 03:43:26', 3, '2025-12-04 14:43:26'),
(225, 4, 1003, NULL, 'A12', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-04 23:43:26', 3, '2025-12-04 14:43:26'),
(226, 4, 1003, NULL, 'J13', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-05 07:43:26', 1, '2025-12-04 14:43:26'),
(227, 4, 1003, NULL, 'A14', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-04 19:43:26', 1, '2025-12-04 14:43:26'),
(228, 4, 1003, NULL, 'C15', 'normal', NULL, 120000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-04 14:43:26'),
(1392, 2, 1000, 244, 'B1', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-06 22:43:26', 4, '2025-12-06 14:43:26'),
(1393, 2, 1000, 244, 'A2', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-07 09:43:26', 5, '2025-12-06 14:43:26'),
(1394, 2, 1000, 244, 'I3', 'normal', NULL, 120000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-06 14:43:26'),
(1395, 2, 1000, 244, 'E4', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-06 16:43:26', 3, '2025-12-06 14:43:26'),
(1396, 2, 1000, 244, 'D5', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-06 19:43:26', 2, '2025-12-06 14:43:26'),
(1397, 2, 1000, 244, 'I6', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-07 01:43:26', 5, '2025-12-06 14:43:26'),
(1398, 2, 1000, 244, 'J7', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-06 17:43:26', 1, '2025-12-06 14:43:26'),
(1399, 2, 1000, 244, 'A8', 'normal', NULL, 120000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-06 14:43:26'),
(1400, 2, 1000, 244, 'A9', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-06 16:43:26', 4, '2025-12-06 14:43:26'),
(1401, 2, 1000, 244, 'B10', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-07 11:43:26', 1, '2025-12-06 14:43:26'),
(1402, 2, 1000, 244, 'C11', 'normal', NULL, 120000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-06 14:43:26'),
(1403, 2, 1000, 244, 'I12', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-06 21:43:26', 5, '2025-12-06 14:43:26'),
(1404, 2, 1000, 244, 'A13', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-07 10:43:26', 4, '2025-12-06 14:43:26'),
(1405, 2, 1000, 244, 'G14', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-07 11:43:26', 5, '2025-12-06 14:43:26'),
(1493, 1, 1001, 258, 'G1', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-07 02:43:26', 4, '2025-12-06 14:43:26'),
(1494, 1, 1001, 258, 'H2', 'normal', NULL, 120000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-06 14:43:26'),
(1495, 1, 1001, 258, 'E3', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-06 16:43:26', 5, '2025-12-06 14:43:26'),
(1496, 1, 1001, 258, 'D4', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-07 05:43:26', 4, '2025-12-06 14:43:26'),
(1497, 1, 1001, 258, 'E5', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-06 23:43:26', 3, '2025-12-06 14:43:26'),
(5894, 3, 1004, 807, 'C1', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-07 08:43:27', 5, '2025-12-06 14:43:27'),
(5895, 3, 1004, 807, 'D2', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-06 21:43:27', 2, '2025-12-06 14:43:27'),
(5896, 3, 1004, 807, 'D3', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-07 08:43:27', 2, '2025-12-06 14:43:27'),
(5897, 3, 1004, 807, 'J4', 'normal', NULL, 120000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-06 14:43:27'),
(5898, 3, 1004, 807, 'I5', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-06 19:43:27', 3, '2025-12-06 14:43:27'),
(5899, 3, 1004, 807, 'I6', 'normal', NULL, 120000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-06 14:43:27'),
(5900, 3, 1004, 807, 'B7', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-07 03:43:27', 4, '2025-12-06 14:43:27'),
(5901, 3, 1004, 807, 'E8', 'normal', NULL, 120000.00, 'Đã đặt', 1, '2025-12-07 01:43:27', 2, '2025-12-06 14:43:27'),
(5902, 3, 1004, 807, 'B9', 'normal', NULL, 120000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-06 14:43:27'),
(5980, 1, 1000, NULL, 'A1', 'normal', 'TICKET_10_001', 120000.00, 'Đã đặt', 1, '2025-10-15 11:00:00', 4, '2025-10-15 10:15:30'),
(5981, 2, 1001, NULL, 'B3', 'normal', 'TICKET_10_002', 120000.00, 'Đã đặt', 1, '2025-10-15 15:00:00', 4, '2025-10-15 14:20:15'),
(5982, 1, 1000, NULL, 'A1', 'normal', 'TICKET_11_001', 120000.00, 'Đã đặt', 1, '2025-11-15 11:00:00', 4, '2025-11-15 10:15:30'),
(5983, 2, 1001, NULL, 'B4', 'normal', 'TICKET_11_002', 120000.00, 'Đã đặt', 1, '2025-11-15 15:00:00', 4, '2025-11-15 14:20:15'),
(5984, 1, 1000, NULL, 'A1', 'normal', 'TICKET_12_001', 120000.00, 'Đã đặt', 1, '2025-12-15 11:00:00', 4, '2025-12-15 10:15:30'),
(5986, 10, 1005, 819, 'H3', 'normal', 'TICKET_69364e8505449_10_33532_1765166725_H3', 120000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-08 04:05:25'),
(5987, 10, 1005, 819, 'H4', 'normal', 'TICKET_69364e85055a6_10_33532_1765166725_H4', 120000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-08 04:05:25'),
(5988, 1, 1012, NULL, 'D5', 'vip', 'TICKET_1765220243_1_1012_D5', 140000.00, 'Đã đặt', 1, '2025-12-10 08:30:00', NULL, '2025-12-09 03:00:00'),
(5989, 1, 1012, NULL, 'D6', 'vip', 'TICKET_1765220243_1_1012_D6', 140000.00, 'Đã đặt', 1, '2025-12-10 08:30:00', NULL, '2025-12-09 03:00:00'),
(5990, 2, 1012, NULL, 'E5', 'vip', 'TICKET_1765220243_2_1012_E5', 140000.00, 'Đã đặt', 1, '2025-12-10 08:35:00', NULL, '2025-12-09 04:00:00'),
(5991, 2, 1012, NULL, 'E6', 'vip', 'TICKET_1765220243_2_1012_E6', 140000.00, 'Đã đặt', 1, '2025-12-10 08:35:00', NULL, '2025-12-09 04:00:00'),
(5992, 3, 1012, NULL, 'F5', 'vip', 'TICKET_1765220243_3_1012_F5', 140000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-09 07:00:00'),
(5993, 4, 1012, NULL, 'A3', 'normal', 'TICKET_1765220243_4_1012_A3', 140000.00, 'Đã đặt', 1, '2025-12-10 08:40:00', NULL, '2025-12-09 08:00:00'),
(5994, 4, 1012, NULL, 'A4', 'normal', 'TICKET_1765220243_4_1012_A4', 140000.00, 'Đã đặt', 1, '2025-12-10 08:40:00', NULL, '2025-12-09 08:00:00'),
(5995, 5, 1013, NULL, 'D7', 'vip', 'TICKET_1765220243_5_1013_D7', 140000.00, 'Đã đặt', 1, '2025-12-10 12:30:00', NULL, '2025-12-09 09:00:00'),
(5996, 5, 1013, NULL, 'D8', 'vip', 'TICKET_1765220243_5_1013_D8', 140000.00, 'Đã đặt', 1, '2025-12-10 12:30:00', NULL, '2025-12-09 09:00:00'),
(5997, 6, 1013, NULL, 'E7', 'vip', 'TICKET_1765220243_6_1013_E7', 140000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-09 10:00:00'),
(5998, 7, 1013, NULL, 'B5', 'normal', 'TICKET_1765220243_7_1013_B5', 140000.00, 'Đã đặt', 1, '2025-12-10 12:35:00', NULL, '2025-12-09 11:00:00'),
(5999, 8, 1014, NULL, 'F6', 'vip', 'TICKET_1765220243_8_1014_F6', 140000.00, 'Đã đặt', 1, '2025-12-10 16:30:00', NULL, '2025-12-09 12:00:00'),
(6000, 9, 1014, NULL, 'G5', 'vip', 'TICKET_1765220243_9_1014_G5', 140000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-09 13:00:00'),
(6001, 10, 1014, NULL, 'C3', 'normal', 'TICKET_1765220243_10_1014_C3', 140000.00, 'Đã đặt', 1, '2025-12-10 16:40:00', NULL, '2025-12-10 01:00:00'),
(6002, 10, 1014, NULL, 'C4', 'normal', 'TICKET_1765220243_10_1014_C4', 140000.00, 'Đã đặt', 1, '2025-12-10 16:40:00', NULL, '2025-12-10 01:00:00'),
(6003, 1, 1015, NULL, 'H5', 'vip', 'TICKET_1765220243_1_1015_H5', 140000.00, 'Đã đặt', 1, '2025-12-10 20:30:00', NULL, '2025-12-10 02:00:00'),
(6004, 2, 1015, NULL, 'H6', 'vip', 'TICKET_1765220243_2_1015_H6', 140000.00, 'Đã đặt', 1, '2025-12-10 20:30:00', NULL, '2025-12-10 02:00:00'),
(6005, 3, 1015, NULL, 'I5', 'vip', 'TICKET_1765220243_3_1015_I5', 140000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-10 03:00:00'),
(6006, 4, 1025, NULL, 'D9', 'vip', 'TICKET_1765220243_4_1025_D9', 140000.00, 'Đã đặt', 1, '2025-12-11 09:30:00', NULL, '2025-12-10 04:00:00'),
(6007, 4, 1025, NULL, 'D10', 'vip', 'TICKET_1765220243_4_1025_D10', 140000.00, 'Đã đặt', 1, '2025-12-11 09:30:00', NULL, '2025-12-10 04:00:00'),
(6008, 5, 1025, NULL, 'E9', 'vip', 'TICKET_1765220243_5_1025_E9', 140000.00, 'Đã đặt', 1, '2025-12-11 09:35:00', NULL, '2025-12-10 05:00:00'),
(6009, 6, 1025, NULL, 'F9', 'vip', 'TICKET_1765220243_6_1025_F9', 140000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-10 06:00:00'),
(6010, 7, 1025, NULL, 'A5', 'normal', 'TICKET_1765220243_7_1025_A5', 140000.00, 'Đã đặt', 1, '2025-12-11 09:40:00', NULL, '2025-12-10 07:00:00'),
(6011, 8, 1026, NULL, 'G6', 'vip', 'TICKET_1765220243_8_1026_G6', 140000.00, 'Đã đặt', 1, '2025-12-11 14:00:00', NULL, '2025-12-10 08:00:00'),
(6012, 9, 1026, NULL, 'G7', 'vip', 'TICKET_1765220243_9_1026_G7', 140000.00, 'Đã đặt', 1, '2025-12-11 14:05:00', NULL, '2025-12-10 09:00:00'),
(6013, 10, 1026, NULL, 'H6', 'vip', 'TICKET_1765220243_10_1026_H6', 140000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-10 10:00:00'),
(6014, 1, 1027, NULL, 'I7', 'vip', 'TICKET_1765220243_1_1027_I7', 140000.00, 'Đã đặt', 1, '2025-12-11 18:30:00', NULL, '2025-12-10 11:00:00'),
(6015, 2, 1027, NULL, 'I8', 'vip', 'TICKET_1765220243_2_1027_I8', 140000.00, 'Đã đặt', 1, '2025-12-11 18:30:00', NULL, '2025-12-10 11:00:00'),
(6016, 3, 1027, NULL, 'B6', 'normal', 'TICKET_1765220243_3_1027_B6', 140000.00, 'Đã đặt', 1, '2025-12-11 18:35:00', NULL, '2025-12-10 12:00:00'),
(6017, 3, 1027, NULL, 'B7', 'normal', 'TICKET_1765220243_3_1027_B7', 140000.00, 'Đã đặt', 1, '2025-12-11 18:35:00', NULL, '2025-12-10 12:00:00'),
(6018, 4, 1038, NULL, 'D11', 'vip', 'TICKET_1765220243_4_1038_D11', 140000.00, 'Đã đặt', 1, '2025-12-12 08:30:00', NULL, '2025-12-11 03:00:00'),
(6019, 5, 1038, NULL, 'E11', 'vip', 'TICKET_1765220243_5_1038_E11', 140000.00, 'Đã đặt', 1, '2025-12-12 08:35:00', NULL, '2025-12-11 04:00:00'),
(6020, 6, 1038, NULL, 'F11', 'vip', 'TICKET_1765220243_6_1038_F11', 140000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-11 05:00:00'),
(6021, 7, 1039, NULL, 'G8', 'vip', 'TICKET_1765220243_7_1039_G8', 140000.00, 'Đã đặt', 1, '2025-12-12 11:30:00', NULL, '2025-12-11 06:00:00'),
(6022, 8, 1039, NULL, 'H8', 'vip', 'TICKET_1765220243_8_1039_H8', 140000.00, 'Đã đặt', 1, '2025-12-12 11:35:00', NULL, '2025-12-11 07:00:00'),
(6023, 9, 1040, NULL, 'I9', 'vip', 'TICKET_1765220243_9_1040_I9', 140000.00, 'Đã đặt', 1, '2025-12-12 14:30:00', NULL, '2025-12-11 08:00:00'),
(6024, 10, 1040, NULL, 'C5', 'normal', 'TICKET_1765220243_10_1040_C5', 140000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-11 09:00:00'),
(6025, 1, 1041, NULL, 'D12', 'vip', 'TICKET_1765220243_1_1041_D12', 140000.00, 'Đã đặt', 1, '2025-12-12 17:30:00', NULL, '2025-12-11 10:00:00'),
(6026, 2, 1041, NULL, 'E12', 'vip', 'TICKET_1765220243_2_1041_E12', 140000.00, 'Đã đặt', 1, '2025-12-12 17:30:00', NULL, '2025-12-11 10:00:00'),
(6027, 3, 1042, NULL, 'F12', 'vip', 'TICKET_1765220243_3_1042_F12', 140000.00, 'Đã đặt', 1, '2025-12-12 20:30:00', NULL, '2025-12-11 11:00:00'),
(6028, 4, 1053, NULL, 'G9', 'vip', 'TICKET_1765220243_4_1053_G9', 140000.00, 'Đã đặt', 1, '2025-12-13 09:30:00', NULL, '2025-12-12 03:00:00'),
(6029, 5, 1053, NULL, 'H9', 'vip', 'TICKET_1765220243_5_1053_H9', 140000.00, 'Đã đặt', 1, '2025-12-13 09:35:00', NULL, '2025-12-12 04:00:00'),
(6030, 6, 1053, NULL, 'I10', 'vip', 'TICKET_1765220243_6_1053_I10', 140000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-12 05:00:00'),
(6031, 7, 1054, NULL, 'A6', 'normal', 'TICKET_1765220243_7_1054_A6', 140000.00, 'Đã đặt', 1, '2025-12-13 12:30:00', NULL, '2025-12-12 06:00:00'),
(6032, 8, 1054, NULL, 'A7', 'normal', 'TICKET_1765220243_8_1054_A7', 140000.00, 'Đã đặt', 1, '2025-12-13 12:30:00', NULL, '2025-12-12 06:00:00'),
(6033, 9, 1055, NULL, 'B8', 'normal', 'TICKET_1765220243_9_1055_B8', 140000.00, 'Đã đặt', 1, '2025-12-13 15:30:00', NULL, '2025-12-12 07:00:00'),
(6034, 10, 1055, NULL, 'C6', 'normal', 'TICKET_1765220243_10_1055_C6', 140000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-12 08:00:00'),
(6035, 1, 1056, NULL, 'D13', 'vip', 'TICKET_1765220243_1_1056_D13', 140000.00, 'Đã đặt', 1, '2025-12-13 18:30:00', NULL, '2025-12-12 09:00:00'),
(6036, 2, 1056, NULL, 'E13', 'vip', 'TICKET_1765220243_2_1056_E13', 140000.00, 'Đã đặt', 1, '2025-12-13 18:30:00', NULL, '2025-12-12 09:00:00'),
(6037, 3, 1057, NULL, 'F13', 'vip', 'TICKET_1765220243_3_1057_F13', 140000.00, 'Đã đặt', 1, '2025-12-13 21:30:00', NULL, '2025-12-12 10:00:00'),
(6038, 4, 1068, NULL, 'G10', 'vip', 'TICKET_1765220243_4_1068_G10', 140000.00, 'Đã đặt', 1, '2025-12-14 09:00:00', NULL, '2025-12-13 03:00:00'),
(6039, 5, 1068, NULL, 'H10', 'vip', 'TICKET_1765220243_5_1068_H10', 140000.00, 'Đã đặt', 1, '2025-12-14 09:05:00', NULL, '2025-12-13 04:00:00'),
(6040, 6, 1068, NULL, 'I11', 'vip', 'TICKET_1765220243_6_1068_I11', 140000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-13 05:00:00'),
(6041, 7, 1069, NULL, 'A8', 'normal', 'TICKET_1765220243_7_1069_A8', 140000.00, 'Đã đặt', 1, '2025-12-14 12:00:00', NULL, '2025-12-13 06:00:00'),
(6042, 8, 1069, NULL, 'A9', 'normal', 'TICKET_1765220243_8_1069_A9', 140000.00, 'Đã đặt', 1, '2025-12-14 12:00:00', NULL, '2025-12-13 06:00:00'),
(6043, 9, 1070, NULL, 'B9', 'normal', 'TICKET_1765220243_9_1070_B9', 140000.00, 'Đã đặt', 1, '2025-12-14 15:00:00', NULL, '2025-12-13 07:00:00'),
(6044, 10, 1070, NULL, 'C7', 'normal', 'TICKET_1765220243_10_1070_C7', 140000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-13 08:00:00'),
(6045, 1, 1071, NULL, 'D14', 'vip', 'TICKET_1765220243_1_1071_D14', 140000.00, 'Đã đặt', 1, '2025-12-14 18:00:00', NULL, '2025-12-13 09:00:00'),
(6046, 2, 1071, NULL, 'E14', 'vip', 'TICKET_1765220243_2_1071_E14', 140000.00, 'Đã đặt', 1, '2025-12-14 18:00:00', NULL, '2025-12-13 09:00:00'),
(6047, 3, 1072, NULL, 'F14', 'vip', 'TICKET_1765220243_3_1072_F14', 140000.00, 'Đã đặt', 1, '2025-12-14 21:00:00', NULL, '2025-12-13 10:00:00'),
(6048, 4, 1083, NULL, 'G11', 'vip', 'TICKET_1765220243_4_1083_G11', 140000.00, 'Đã đặt', 1, '2025-12-15 08:30:00', NULL, '2025-12-14 03:00:00'),
(6049, 5, 1083, NULL, 'H11', 'vip', 'TICKET_1765220243_5_1083_H11', 140000.00, 'Đã đặt', 1, '2025-12-15 08:35:00', NULL, '2025-12-14 04:00:00'),
(6050, 6, 1083, NULL, 'I12', 'vip', 'TICKET_1765220243_6_1083_I12', 140000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-14 05:00:00'),
(6051, 7, 1084, NULL, 'A10', 'normal', 'TICKET_1765220243_7_1084_A10', 140000.00, 'Đã đặt', 1, '2025-12-15 11:30:00', NULL, '2025-12-14 06:00:00'),
(6052, 8, 1084, NULL, 'A11', 'normal', 'TICKET_1765220243_8_1084_A11', 140000.00, 'Đã đặt', 1, '2025-12-15 11:30:00', NULL, '2025-12-14 06:00:00'),
(6053, 9, 1085, NULL, 'B10', 'normal', 'TICKET_1765220243_9_1085_B10', 140000.00, 'Đã đặt', 1, '2025-12-15 14:30:00', NULL, '2025-12-14 07:00:00'),
(6054, 10, 1085, NULL, 'C8', 'normal', 'TICKET_1765220243_10_1085_C8', 140000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-14 08:00:00'),
(6055, 1, 1086, NULL, 'D15', 'vip', 'TICKET_1765220243_1_1086_D15', 140000.00, 'Đã đặt', 1, '2025-12-15 17:30:00', NULL, '2025-12-14 09:00:00'),
(6056, 2, 1086, NULL, 'E15', 'vip', 'TICKET_1765220243_2_1086_E15', 140000.00, 'Đã đặt', 1, '2025-12-15 17:30:00', NULL, '2025-12-14 09:00:00'),
(6057, 3, 1087, NULL, 'F15', 'vip', 'TICKET_1765220243_3_1087_F15', 140000.00, 'Đã đặt', 1, '2025-12-15 20:30:00', NULL, '2025-12-14 10:00:00'),
(6058, 4, 1096, NULL, 'G12', 'vip', 'TICKET_1765220243_4_1096_G12', 140000.00, 'Đã đặt', 1, '2025-12-16 09:30:00', NULL, '2025-12-15 03:00:00'),
(6059, 5, 1096, NULL, 'H12', 'vip', 'TICKET_1765220243_5_1096_H12', 140000.00, 'Đã đặt', 1, '2025-12-16 09:35:00', NULL, '2025-12-15 04:00:00'),
(6060, 6, 1096, NULL, 'I13', 'vip', 'TICKET_1765220243_6_1096_I13', 140000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-15 05:00:00'),
(6061, 7, 1097, NULL, 'A12', 'normal', 'TICKET_1765220243_7_1097_A12', 140000.00, 'Đã đặt', 1, '2025-12-16 13:00:00', NULL, '2025-12-15 06:00:00'),
(6062, 8, 1097, NULL, 'A13', 'normal', 'TICKET_1765220243_8_1097_A13', 140000.00, 'Đã đặt', 1, '2025-12-16 13:00:00', NULL, '2025-12-15 06:00:00'),
(6063, 9, 1098, NULL, 'B11', 'normal', 'TICKET_1765220243_9_1098_B11', 140000.00, 'Đã đặt', 1, '2025-12-16 16:30:00', NULL, '2025-12-15 07:00:00'),
(6064, 10, 1098, NULL, 'C9', 'normal', 'TICKET_1765220243_10_1098_C9', 140000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-15 08:00:00'),
(6065, 1, 1099, NULL, 'D16', 'vip', 'TICKET_1765220243_1_1099_D16', 140000.00, 'Đã đặt', 1, '2025-12-16 20:00:00', NULL, '2025-12-15 09:00:00'),
(6066, 2, 1099, NULL, 'E16', 'vip', 'TICKET_1765220243_2_1099_E16', 140000.00, 'Đã đặt', 1, '2025-12-16 20:00:00', NULL, '2025-12-15 09:00:00'),
(6067, 3, 1099, NULL, 'F16', 'vip', 'TICKET_1765220243_3_1099_F16', 140000.00, 'Đã đặt', 1, '2025-12-16 20:05:00', NULL, '2025-12-15 10:00:00'),
(6078, 9, 41713, 820, 'I5', 'vip', 'TICKET_693720ea9eb6e_9_41713_1765220586_I5', 200000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-08 19:03:06'),
(6079, 9, 41713, 820, 'I6', 'vip', 'TICKET_693720ea9edb7_9_41713_1765220586_I6', 200000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-08 19:03:06'),
(6080, 9, 41713, 820, 'I7', 'vip', 'TICKET_693720ea9ef1f_9_41713_1765220586_I7', 200000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-08 19:03:06'),
(6081, 9, 1008, 821, 'F3', 'vip', 'TICKET_6937213937b6c_9_1008_1765220665_F3', 170000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-08 19:04:25'),
(6082, 9, 1008, 821, 'F4', 'vip', 'TICKET_6937213937cdc_9_1008_1765220665_F4', 170000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-08 19:04:25'),
(6083, 9, 1009, 822, 'F1', 'vip', 'TICKET_6937243b102ed_9_1009_1765221435_F1', 170000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-08 19:17:15'),
(6084, 9, 1009, 822, 'F2', 'vip', 'TICKET_6937243b10426_9_1009_1765221435_F2', 170000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-08 19:17:15'),
(6085, 9, 1010, 823, 'G3', 'normal', 'TICKET_69376e0f5a5b4_9_1010_1765240335_G3', 110000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-09 00:32:15'),
(6086, 9, 1010, 823, 'G4', 'normal', 'TICKET_69376e0f5a6e9_9_1010_1765240335_G4', 110000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-09 00:32:15'),
(6087, 9, 1010, 823, 'G5', 'normal', 'TICKET_69376e0f5a7e0_9_1010_1765240335_G5', 110000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-09 00:32:15'),
(6088, 25, 41726, 825, 'L7', 'couple', 'TICKET_69379927d47b1_25_41726_1765251367_L7', 120000.00, 'Đã đặt', 1, '2025-12-09 10:37:29', 19, '2025-12-09 03:36:07'),
(6089, 25, 41726, 825, 'L8', 'couple', 'TICKET_69379927d49e5_25_41726_1765251367_L8', 120000.00, 'Đã đặt', 1, '2025-12-09 10:37:29', 19, '2025-12-09 03:36:07'),
(6090, 3, 41727, 826, 'H3', 'normal', 'TICKET_6937ac4150a2e_3_41727_1765256257_H3', 90000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-09 04:57:37'),
(6091, 3, 41727, 826, 'H4', 'normal', 'TICKET_6937ac4150bc3_3_41727_1765256257_H4', 90000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-09 04:57:37'),
(6092, 3, 41727, 826, 'H5', 'normal', 'TICKET_6937ac4150c9c_3_41727_1765256257_H5', 90000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-09 04:57:37'),
(6093, 27, 1005, 832, 'C7', 'normal', 'TICKET_69380c762e473_27_1005_1765280886_C7', 130000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-09 11:48:06'),
(6094, 27, 1005, 832, 'C8', 'normal', 'TICKET_69380c762e5fb_27_1005_1765280886_C8', 130000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-09 11:48:06'),
(6095, 27, 1005, 833, 'A1', 'normal', 'TICKET_69380dd96b19b_27_1005_1765281241_A1', 130000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-09 11:54:01'),
(6096, 27, 1005, 833, 'A2', 'normal', 'TICKET_69380dd96b2f9_27_1005_1765281241_A2', 130000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-09 11:54:01'),
(6097, 27, 1005, 833, 'A3', 'normal', 'TICKET_69380dd96b42c_27_1005_1765281241_A3', 130000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-09 11:54:01'),
(6098, 27, 1005, 833, 'A4', 'normal', 'TICKET_69380dd96b518_27_1005_1765281241_A4', 130000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-09 11:54:01'),
(6099, 27, 1005, 833, 'A5', 'normal', 'TICKET_69380dd96b5f8_27_1005_1765281241_A5', 130000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-09 11:54:01'),
(6100, 27, 1005, 833, 'A6', 'normal', 'TICKET_69380dd96b6d6_27_1005_1765281241_A6', 130000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-09 11:54:01'),
(6101, 27, 1005, 833, 'A7', 'normal', 'TICKET_69380dd96b79e_27_1005_1765281241_A7', 130000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-09 11:54:01'),
(6102, 27, 1005, 833, 'A8', 'normal', 'TICKET_69380dd96b870_27_1005_1765281241_A8', 130000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-09 11:54:01'),
(6103, 9, 41731, 844, 'I4', 'vip', 'TICKET_6938406186fca_9_41731_1765294177_I4', 200000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-09 15:29:37'),
(6104, 9, 41731, 844, 'I5', 'vip', 'TICKET_6938406187124_9_41731_1765294177_I5', 200000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-09 15:29:37'),
(6105, 25, 41731, 846, 'I2', 'vip', 'TICKET_693840c619ed0_25_41731_1765294278_I2', 200000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-09 15:31:18'),
(6106, 25, 41731, 846, 'I3', 'vip', 'TICKET_693840c61a016_25_41731_1765294278_I3', 200000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-09 15:31:18'),
(6107, 3, 41729, 854, 'L8', 'normal', 'TICKET_693921b87cc54_3_41729_1765351864_L8', 140000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-10 07:31:04'),
(6108, 3, 41729, 854, 'L9', 'normal', 'TICKET_693921b87cdaa_3_41729_1765351864_L9', 140000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-10 07:31:04'),
(6109, 9, 41740, 856, 'I1', 'vip', 'TICKET_69392616a19e2_9_41740_1765352982_I1', 180000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-10 07:49:42'),
(6110, 9, 41740, 856, 'I2', 'vip', 'TICKET_69392616a1b7b_9_41740_1765352982_I2', 180000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-10 07:49:42'),
(6111, 9, 41740, 856, 'I3', 'vip', 'TICKET_69392616a1c77_9_41740_1765352982_I3', 180000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-10 07:49:42'),
(6112, 9, 41740, 856, 'I4', 'vip', 'TICKET_69392616a1d64_9_41740_1765352982_I4', 180000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-10 07:49:42'),
(6113, 9, 41746, 859, 'H1', 'vip', 'TICKET_6939327a4624a_9_41746_1765356154_H1', 200000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-10 08:42:34'),
(6114, 9, 41746, 859, 'H2', 'vip', 'TICKET_6939327a463b7_9_41746_1765356154_H2', 200000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-10 08:42:34'),
(6115, 9, 41746, 859, 'H3', 'vip', 'TICKET_6939327a4649d_9_41746_1765356154_H3', 200000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-10 08:42:34'),
(6116, 9, 41746, 859, 'H4', 'vip', 'TICKET_6939327a4656b_9_41746_1765356154_H4', 200000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-10 08:42:34'),
(6117, 9, 41746, 859, 'H5', 'vip', 'TICKET_6939327a46651_9_41746_1765356154_H5', 200000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-10 08:42:34'),
(6118, 9, 1000, 860, 'H4', 'vip', 'TICKET_69393597e981c_9_1000_1765356951_H4', 180000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-10 08:55:51'),
(6119, 9, 1000, 860, 'H5', 'vip', 'TICKET_69393597e9941_9_1000_1765356951_H5', 180000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-10 08:55:51'),
(6120, 9, 1000, 860, 'H6', 'vip', 'TICKET_69393597e9a27_9_1000_1765356951_H6', 180000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-10 08:55:51'),
(6121, 10, 41758, 865, 'J6', 'vip', 'TICKET_693b7865d5117_10_41758_1765505125_J6', 200000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-12 02:05:25'),
(6122, 10, 41758, 865, 'J7', 'vip', 'TICKET_693b7865d5318_10_41758_1765505125_J7', 200000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-12 02:05:25'),
(6123, 22, 41801, 867, 'E1', 'vip', 'TICKET_693f7b52434a0_22_41801_1765768018_E1', 130000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-15 03:06:58'),
(6124, 22, 41801, 867, 'E2', 'vip', 'TICKET_693f7b5243614_22_41801_1765768018_E2', 130000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-15 03:06:58'),
(6125, 10, 41824, 869, 'G5', 'normal', 'TICKET_69502449c489c_10_41824_1766859849_G5', 120000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-27 18:24:09'),
(6126, 10, 41824, 869, 'G6', 'normal', 'TICKET_69502449c49dc_10_41824_1766859849_G6', 120000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-27 18:24:09'),
(6127, 9, 41825, 870, 'I3', 'vip', 'TICKET_69529230a2b5f_9_41825_1767019056_I3', 156000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-29 14:37:36'),
(6128, 9, 41825, 870, 'I4', 'vip', 'TICKET_69529230a2cda_9_41825_1767019056_I4', 156000.00, 'Đã đặt', 0, NULL, NULL, '2025-12-29 14:37:36');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('ticket','subscription','deposit') NOT NULL,
  `related_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` enum('Momo','ZaloPay','Stripe','Bank','Cash') DEFAULT 'Momo',
  `status` enum('Thành công','Thất bại','Đang xử lý') DEFAULT 'Thành công',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `type`, `related_id`, `amount`, `method`, `status`, `created_at`) VALUES
(1, 1, 'subscription', 2, 79000.00, 'Momo', 'Thành công', '2025-11-12 07:41:09'),
(2, 2, 'subscription', 3, 129000.00, 'ZaloPay', 'Thành công', '2025-11-12 07:41:09'),
(3, 3, 'ticket', 1, 240000.00, 'Momo', 'Thành công', '2025-11-12 07:41:09'),
(4, 4, 'subscription', 4, 199000.00, 'Bank', 'Thành công', '2025-11-12 07:41:09'),
(5, 5, 'ticket', 5, 110000.00, 'Momo', 'Thành công', '2025-11-12 07:41:09'),
(6, 1, 'ticket', 2, 120000.00, 'ZaloPay', 'Thành công', '2025-11-12 07:41:09'),
(7, 9, 'subscription', 4, 199000.00, '', 'Thành công', '2025-11-19 01:09:14'),
(8, 1, 'subscription', 2, 79000.00, '', 'Thành công', '2025-11-25 02:26:14'),
(9, 1, 'subscription', 3, 129000.00, '', 'Thành công', '2025-11-25 02:27:04'),
(10, 9, 'ticket', 1, 400000.00, '', 'Thành công', '2025-11-28 02:09:29'),
(11, 9, 'ticket', NULL, 600000.00, '', 'Thành công', '2025-11-30 18:12:26'),
(12, 9, 'ticket', NULL, 360000.00, '', 'Thành công', '2025-12-02 01:42:00'),
(13, 9, 'ticket', NULL, 360000.00, '', 'Thành công', '2025-12-02 01:47:59'),
(14, 9, 'ticket', NULL, 360000.00, '', 'Thành công', '2025-12-02 02:09:04'),
(15, 9, 'ticket', NULL, 360000.00, '', 'Thành công', '2025-12-02 02:13:55'),
(16, 9, 'ticket', NULL, 360000.00, '', 'Thành công', '2025-12-02 03:38:42'),
(17, 9, 'ticket', 15, 400000.00, '', 'Thành công', '2025-12-02 03:58:40'),
(18, 9, 'ticket', 16, 630000.00, '', 'Thành công', '2025-12-02 04:10:00'),
(19, 9, 'ticket', 17, 510000.00, '', 'Thành công', '2025-12-02 04:16:43'),
(20, 9, 'ticket', 18, 630000.00, '', 'Thành công', '2025-12-03 01:45:37'),
(21, 1, 'ticket', 36, 90000.00, '', 'Thành công', '2025-12-04 03:18:54'),
(22, 1, 'ticket', 60, 175000.00, '', 'Thành công', '2025-12-04 04:07:19'),
(23, 1, 'ticket', NULL, 325000.00, '', 'Thành công', '2025-12-04 04:20:58'),
(24, 1, 'ticket', 75, 270000.00, '', 'Thành công', '2025-12-04 13:57:34'),
(25, 16, 'ticket', 79, 1360000.00, '', 'Thành công', '2025-12-04 18:35:41'),
(26, 16, 'ticket', 80, 3655000.00, '', 'Thành công', '2025-12-04 18:39:25'),
(27, 9, 'ticket', 816, 505000.00, '', 'Thành công', '2025-12-06 15:23:29'),
(28, 9, 'ticket', 817, 1220000.00, '', 'Thành công', '2025-12-06 16:22:45'),
(29, 1, 'ticket', 818, 220000.00, '', 'Thành công', '2025-12-06 17:20:33'),
(30, 1, 'ticket', NULL, 240000.00, 'Momo', 'Thành công', '2025-10-15 10:15:30'),
(31, 2, 'ticket', NULL, 360000.00, 'ZaloPay', 'Thành công', '2025-10-15 14:20:15'),
(32, 3, 'ticket', NULL, 480000.00, 'Momo', 'Thành công', '2025-10-30 09:30:45'),
(33, 1, 'ticket', NULL, 360000.00, 'Momo', 'Thành công', '2025-11-15 10:15:30'),
(34, 2, 'ticket', NULL, 240000.00, 'ZaloPay', 'Thành công', '2025-11-15 14:20:15'),
(35, 3, 'ticket', NULL, 480000.00, 'Momo', 'Thành công', '2025-11-30 09:30:45'),
(36, 1, 'ticket', NULL, 480000.00, 'Momo', 'Thành công', '2025-12-15 10:15:30'),
(37, 2, 'ticket', NULL, 360000.00, 'ZaloPay', 'Thành công', '2025-12-15 14:20:15'),
(38, 3, 'ticket', NULL, 240000.00, 'Momo', 'Thành công', '2025-12-30 09:30:45'),
(39, 1, 'subscription', 2, 79000.00, 'Momo', 'Thành công', '2025-10-15 10:20:30'),
(40, 2, 'subscription', 3, 129000.00, 'ZaloPay', 'Thành công', '2025-11-15 14:15:45'),
(41, 3, 'subscription', 4, 199000.00, 'Bank', 'Thành công', '2025-12-15 11:30:20'),
(42, 1, 'ticket', 819, 325000.00, '', 'Thành công', '2025-12-08 04:05:25'),
(43, 1, 'ticket', 5988, 140000.00, 'Momo', 'Thành công', '2025-12-09 03:00:00'),
(44, 1, 'ticket', 5989, 140000.00, 'Momo', 'Thành công', '2025-12-09 03:00:00'),
(45, 2, 'ticket', 5990, 140000.00, 'ZaloPay', 'Thành công', '2025-12-09 04:00:00'),
(46, 2, 'ticket', 5991, 140000.00, 'ZaloPay', 'Thành công', '2025-12-09 04:00:00'),
(47, 3, 'ticket', 5992, 140000.00, 'Bank', 'Thành công', '2025-12-09 07:00:00'),
(48, 4, 'ticket', 5993, 140000.00, 'Momo', 'Thành công', '2025-12-09 08:00:00'),
(49, 4, 'ticket', 5994, 140000.00, 'Momo', 'Thành công', '2025-12-09 08:00:00'),
(50, 5, 'ticket', 5995, 140000.00, 'ZaloPay', 'Thành công', '2025-12-09 09:00:00'),
(51, 5, 'ticket', 5996, 140000.00, 'ZaloPay', 'Thành công', '2025-12-09 09:00:00'),
(52, 6, 'ticket', 5997, 140000.00, 'Bank', 'Thành công', '2025-12-09 10:00:00'),
(53, 7, 'ticket', 5998, 140000.00, 'Momo', 'Thành công', '2025-12-09 11:00:00'),
(54, 8, 'ticket', 5999, 140000.00, 'ZaloPay', 'Thành công', '2025-12-09 12:00:00'),
(55, 9, 'ticket', 6000, 140000.00, 'Bank', 'Thành công', '2025-12-09 13:00:00'),
(56, 10, 'ticket', 6001, 140000.00, 'Momo', 'Thành công', '2025-12-10 01:00:00'),
(57, 10, 'ticket', 6002, 140000.00, 'Momo', 'Thành công', '2025-12-10 01:00:00'),
(58, 1, 'ticket', 6003, 140000.00, 'ZaloPay', 'Thành công', '2025-12-10 02:00:00'),
(59, 2, 'ticket', 6004, 140000.00, 'Bank', 'Thành công', '2025-12-10 02:00:00'),
(60, 3, 'ticket', 6005, 140000.00, 'Momo', 'Thành công', '2025-12-10 03:00:00'),
(61, 4, 'ticket', 6006, 140000.00, 'ZaloPay', 'Thành công', '2025-12-10 04:00:00'),
(62, 4, 'ticket', 6007, 140000.00, 'ZaloPay', 'Thành công', '2025-12-10 04:00:00'),
(63, 5, 'ticket', 6008, 140000.00, 'Bank', 'Thành công', '2025-12-10 05:00:00'),
(64, 6, 'ticket', 6009, 140000.00, 'Momo', 'Thành công', '2025-12-10 06:00:00'),
(65, 7, 'ticket', 6010, 140000.00, 'ZaloPay', 'Thành công', '2025-12-10 07:00:00'),
(66, 8, 'ticket', 6011, 140000.00, 'Bank', 'Thành công', '2025-12-10 08:00:00'),
(67, 9, 'ticket', 6012, 140000.00, 'Momo', 'Thành công', '2025-12-10 09:00:00'),
(68, 10, 'ticket', 6013, 140000.00, 'ZaloPay', 'Thành công', '2025-12-10 10:00:00'),
(69, 1, 'ticket', 6014, 140000.00, 'Bank', 'Thành công', '2025-12-10 11:00:00'),
(70, 2, 'ticket', 6015, 140000.00, 'Momo', 'Thành công', '2025-12-10 11:00:00'),
(71, 3, 'ticket', 6016, 140000.00, 'ZaloPay', 'Thành công', '2025-12-10 12:00:00'),
(72, 3, 'ticket', 6017, 140000.00, 'ZaloPay', 'Thành công', '2025-12-10 12:00:00'),
(73, 4, 'ticket', 6018, 140000.00, 'Bank', 'Thành công', '2025-12-11 03:00:00'),
(74, 5, 'ticket', 6019, 140000.00, 'Momo', 'Thành công', '2025-12-11 04:00:00'),
(75, 6, 'ticket', 6020, 140000.00, 'ZaloPay', 'Thành công', '2025-12-11 05:00:00'),
(76, 7, 'ticket', 6021, 140000.00, 'Bank', 'Thành công', '2025-12-11 06:00:00'),
(77, 8, 'ticket', 6022, 140000.00, 'Momo', 'Thành công', '2025-12-11 07:00:00'),
(78, 9, 'ticket', 6023, 140000.00, 'ZaloPay', 'Thành công', '2025-12-11 08:00:00'),
(79, 10, 'ticket', 6024, 140000.00, 'Bank', 'Thành công', '2025-12-11 09:00:00'),
(80, 1, 'ticket', 6025, 140000.00, 'Momo', 'Thành công', '2025-12-11 10:00:00'),
(81, 2, 'ticket', 6026, 140000.00, 'ZaloPay', 'Thành công', '2025-12-11 10:00:00'),
(82, 3, 'ticket', 6027, 140000.00, 'Bank', 'Thành công', '2025-12-11 11:00:00'),
(83, 4, 'ticket', 6028, 140000.00, 'Momo', 'Thành công', '2025-12-12 03:00:00'),
(84, 5, 'ticket', 6029, 140000.00, 'ZaloPay', 'Thành công', '2025-12-12 04:00:00'),
(85, 6, 'ticket', 6030, 140000.00, 'Bank', 'Thành công', '2025-12-12 05:00:00'),
(86, 7, 'ticket', 6031, 140000.00, 'Momo', 'Thành công', '2025-12-12 06:00:00'),
(87, 7, 'ticket', 6032, 140000.00, 'Momo', 'Thành công', '2025-12-12 06:00:00'),
(88, 8, 'ticket', 6033, 140000.00, 'ZaloPay', 'Thành công', '2025-12-12 07:00:00'),
(89, 9, 'ticket', 6034, 140000.00, 'Bank', 'Thành công', '2025-12-12 08:00:00'),
(90, 10, 'ticket', 6035, 140000.00, 'Momo', 'Thành công', '2025-12-12 09:00:00'),
(91, 1, 'ticket', 6036, 140000.00, 'ZaloPay', 'Thành công', '2025-12-12 09:00:00'),
(92, 2, 'ticket', 6037, 140000.00, 'Bank', 'Thành công', '2025-12-12 10:00:00'),
(93, 3, 'ticket', 6038, 140000.00, 'Momo', 'Thành công', '2025-12-13 03:00:00'),
(94, 4, 'ticket', 6039, 140000.00, 'ZaloPay', 'Thành công', '2025-12-13 04:00:00'),
(95, 5, 'ticket', 6040, 140000.00, 'Bank', 'Thành công', '2025-12-13 05:00:00'),
(96, 6, 'ticket', 6041, 140000.00, 'Momo', 'Thành công', '2025-12-13 06:00:00'),
(97, 7, 'ticket', 6042, 140000.00, 'ZaloPay', 'Thành công', '2025-12-13 06:00:00'),
(98, 8, 'ticket', 6043, 140000.00, 'Bank', 'Thành công', '2025-12-13 07:00:00'),
(99, 9, 'ticket', 6044, 140000.00, 'Momo', 'Thành công', '2025-12-13 08:00:00'),
(100, 10, 'ticket', 6045, 140000.00, 'ZaloPay', 'Thành công', '2025-12-13 09:00:00'),
(101, 1, 'ticket', 6046, 140000.00, 'Bank', 'Thành công', '2025-12-13 09:00:00'),
(102, 2, 'ticket', 6047, 140000.00, 'Momo', 'Thành công', '2025-12-13 10:00:00'),
(103, 3, 'ticket', 6048, 140000.00, 'ZaloPay', 'Thành công', '2025-12-14 03:00:00'),
(104, 4, 'ticket', 6049, 140000.00, 'Bank', 'Thành công', '2025-12-14 04:00:00'),
(105, 5, 'ticket', 6050, 140000.00, 'Momo', 'Thành công', '2025-12-14 05:00:00'),
(106, 6, 'ticket', 6051, 140000.00, 'ZaloPay', 'Thành công', '2025-12-14 06:00:00'),
(107, 7, 'ticket', 6052, 140000.00, 'Bank', 'Thành công', '2025-12-14 06:00:00'),
(108, 8, 'ticket', 6053, 140000.00, 'Momo', 'Thành công', '2025-12-14 07:00:00'),
(109, 9, 'ticket', 6054, 140000.00, 'ZaloPay', 'Thành công', '2025-12-14 08:00:00'),
(110, 10, 'ticket', 6055, 140000.00, 'Bank', 'Thành công', '2025-12-14 09:00:00'),
(111, 1, 'ticket', 6056, 140000.00, 'Momo', 'Thành công', '2025-12-14 09:00:00'),
(112, 2, 'ticket', 6057, 140000.00, 'ZaloPay', 'Thành công', '2025-12-14 10:00:00'),
(113, 3, 'ticket', 6058, 140000.00, 'Bank', 'Thành công', '2025-12-15 03:00:00'),
(114, 4, 'ticket', 6059, 140000.00, 'Momo', 'Thành công', '2025-12-15 04:00:00'),
(115, 5, 'ticket', 6060, 140000.00, 'ZaloPay', 'Thành công', '2025-12-15 05:00:00'),
(116, 6, 'ticket', 6061, 140000.00, 'Bank', 'Thành công', '2025-12-15 06:00:00'),
(117, 7, 'ticket', 6062, 140000.00, 'Momo', 'Thành công', '2025-12-15 06:00:00'),
(118, 8, 'ticket', 6063, 140000.00, 'ZaloPay', 'Thành công', '2025-12-15 07:00:00'),
(119, 9, 'ticket', 6064, 140000.00, 'Bank', 'Thành công', '2025-12-15 08:00:00'),
(120, 10, 'ticket', 6065, 140000.00, 'Momo', 'Thành công', '2025-12-15 09:00:00'),
(121, 1, 'ticket', 6066, 140000.00, 'ZaloPay', 'Thành công', '2025-12-15 09:00:00'),
(122, 2, 'ticket', 6067, 140000.00, 'Bank', 'Thành công', '2025-12-15 10:00:00'),
(123, 3, 'ticket', 6068, 150000.00, 'Momo', 'Thành công', '2025-12-24 03:00:00'),
(124, 4, 'ticket', 6069, 150000.00, 'ZaloPay', 'Thành công', '2025-12-24 04:00:00'),
(125, 5, 'ticket', 6070, 150000.00, 'Bank', 'Thành công', '2025-12-24 05:00:00'),
(126, 6, 'ticket', 6071, 100000.00, 'Momo', 'Thành công', '2025-12-24 06:00:00'),
(127, 7, 'ticket', 6072, 100000.00, 'ZaloPay', 'Thành công', '2025-12-24 06:00:00'),
(128, 8, 'ticket', 6073, 100000.00, 'Bank', 'Thành công', '2025-12-24 07:00:00'),
(129, 9, 'ticket', 6074, 100000.00, 'Momo', 'Thành công', '2025-12-24 08:00:00'),
(130, 10, 'ticket', 6075, 150000.00, 'ZaloPay', 'Thành công', '2025-12-24 09:00:00'),
(131, 1, 'ticket', 6076, 150000.00, 'Bank', 'Thành công', '2025-12-24 09:00:00'),
(132, 2, 'ticket', 6077, 150000.00, 'Momo', 'Thành công', '2025-12-24 10:00:00'),
(133, 9, 'ticket', 820, 685000.00, '', 'Thành công', '2025-12-08 19:03:06'),
(134, 9, 'ticket', 821, 425000.00, '', 'Thành công', '2025-12-08 19:04:25'),
(135, 9, 'ticket', 822, 425000.00, '', 'Thành công', '2025-12-08 19:17:15'),
(136, 9, 'ticket', 823, 415000.00, '', 'Thành công', '2025-12-09 00:32:15'),
(137, 25, 'ticket', 825, 3855000.00, '', 'Thành công', '2025-12-09 03:36:07'),
(138, 26, 'deposit', 0, 10000000.00, '', 'Thành công', '2025-12-09 03:41:56'),
(139, 26, 'subscription', 4, 199000.00, '', 'Thành công', '2025-12-09 03:42:33'),
(140, 3, 'ticket', 826, 355000.00, '', 'Thành công', '2025-12-09 04:57:37'),
(141, 27, 'ticket', 832, 2450000.00, '', 'Thành công', '2025-12-09 11:48:06'),
(142, 27, 'ticket', 833, 2355000.00, '', 'Thành công', '2025-12-09 11:54:01'),
(143, 9, 'ticket', 844, 485000.00, '', 'Thành công', '2025-12-09 15:29:37'),
(144, 25, 'ticket', 846, 400000.00, '', 'Thành công', '2025-12-09 15:31:18'),
(145, 3, 'ticket', 854, 280000.00, '', 'Thành công', '2025-12-10 07:31:04'),
(146, 9, 'ticket', 856, 805000.00, '', 'Thành công', '2025-12-10 07:49:42'),
(147, 9, 'ticket', 859, 1085000.00, '', 'Thành công', '2025-12-10 08:42:34'),
(148, 9, 'ticket', 860, 895000.00, '', 'Thành công', '2025-12-10 08:55:51'),
(149, 10, 'ticket', 865, 485000.00, '', 'Thành công', '2025-12-12 02:05:25'),
(150, 22, 'ticket', 867, 345000.00, '', 'Thành công', '2025-12-15 03:06:58'),
(151, 10, 'ticket', 869, 240000.00, '', 'Thành công', '2025-12-27 18:24:09'),
(152, 9, 'ticket', 870, 537000.00, '', 'Thành công', '2025-12-29 14:37:36'),
(153, 9, 'deposit', 0, 10000000.00, '', 'Thành công', '2026-02-04 07:14:59');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(120) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `rank` enum('Bronze','Silver','Gold','Platinum') DEFAULT 'Bronze',
  `points` int(11) DEFAULT 0,
  `subscription_id` int(11) DEFAULT NULL,
  `status` enum('active','inactive','banned') DEFAULT 'active',
  `email_verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `role` varchar(50) DEFAULT 'user',
  `theater_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `avatar`, `birthdate`, `rank`, `points`, `subscription_id`, `status`, `email_verified`, `created_at`, `updated_at`, `role`, `theater_id`, `is_active`, `last_login`) VALUES
(1, 'Tuan Anh', 'noble.toad.nict@letterguard.net', NULL, '$2y$10$lOJtx0GSp2xgBlX1cKw1LuTf90z0qfuXcrVlz6fiGQn1QM3kwl.fW', NULL, NULL, 'Bronze', 0, 1, 'active', 0, '2025-11-10 15:10:16', '2025-11-10 15:10:16', 'user', NULL, 1, NULL),
(2, 'Super Admin', 'admin@cinehub.com', NULL, '$2y$10$Q516uBkFiAAoP9sABaJJRebPWUFZjqKI9370ZLqFxlhtFE1L1r9ba', NULL, NULL, 'Bronze', 0, NULL, 'active', 0, '2025-11-10 16:41:17', '2025-11-10 16:45:54', 'admin', NULL, 1, NULL),
(3, 'Admin Mới', 'admin2@cinehub.com', NULL, '$2y$12$/AeKoaDJ.CzbUovU0x9F1.U54BECa20QKSYuRc.O./WJZTt1b/bFG', NULL, NULL, 'Bronze', 0, NULL, 'active', 0, '2025-11-12 02:39:06', '2026-06-08 10:58:18', 'admin', NULL, 1, NULL),
(4, 'Nguyễn Văn A', 'nguyenvana@example.com', NULL, '$2y$10$lOJtx0GSp2xgBlX1cKw1LuTf90z0qfuXcrVlz6fiGQn1QM3kwl.fW', NULL, NULL, 'Silver', 500, 2, 'active', 0, '2025-11-12 07:41:09', '2025-11-12 07:41:09', 'user', NULL, 1, NULL),
(5, 'Trần Thị B', 'tranthib@example.com', NULL, '$2y$10$lOJtx0GSp2xgBlX1cKw1LuTf90z0qfuXcrVlz6fiGQn1QM3kwl.fW', NULL, NULL, 'Gold', 1200, 3, 'active', 0, '2025-11-12 07:41:09', '2025-11-12 07:41:09', 'user', NULL, 1, NULL),
(6, 'Lê Văn C', 'levanc@example.com', NULL, '$2y$10$lOJtx0GSp2xgBlX1cKw1LuTf90z0qfuXcrVlz6fiGQn1QM3kwl.fW', NULL, NULL, 'Bronze', 100, 1, 'active', 0, '2025-11-12 07:41:09', '2025-11-12 07:41:09', 'user', NULL, 1, NULL),
(7, 'Phạm Thị D', 'phamthid@example.com', NULL, '$2y$10$lOJtx0GSp2xgBlX1cKw1LuTf90z0qfuXcrVlz6fiGQn1QM3kwl.fW', NULL, NULL, 'Platinum', 2500, 4, 'active', 0, '2025-11-12 07:41:09', '2025-11-12 07:41:09', 'user', NULL, 1, NULL),
(8, 'Hoàng Văn E', 'hoangvane@example.com', NULL, '$2y$10$lOJtx0GSp2xgBlX1cKw1LuTf90z0qfuXcrVlz6fiGQn1QM3kwl.fW', NULL, NULL, 'Silver', 800, 2, 'active', 0, '2025-11-12 07:41:09', '2025-11-12 07:41:09', 'user', NULL, 1, NULL),
(9, 'vanlinh', 'nguyenvanlinh25062006@gmail.com', NULL, '$2y$12$X9rtzLgIJYy3cWi4VLsth.GZaihA0lIw6ZXMQoK7CXyb6Xi3OrMQ2', 'avatars/avatar_9_1781598424.png', NULL, 'Bronze', 10101000, 4, 'active', 0, '2025-11-14 01:35:37', '2026-06-16 01:27:05', 'moderator', 3, 1, NULL),
(10, 'Tuan_awh', 'tuanawh@gmail.com', NULL, '$2y$10$5NwNHefnp5jwjr1Vls5HG.dnt4SWC1newqSkuV8X4QTcwZ0Ok1JQ.', 'data/avatars/avatar_10_1765506885.jpg', NULL, 'Bronze', 0, 1, 'active', 0, '2025-11-14 01:45:51', '2025-12-12 02:34:45', 'moderator', 2, 1, NULL),
(11, 'Hoang Son', 'hsson97805@gmail.com', NULL, '$2y$10$4OBk1HA71jEhbVPP7FA7VueQ8B30EgEy9eB9tAHRFmUvA8I7lwAPe', NULL, NULL, 'Bronze', 0, 1, 'active', 0, '2025-11-24 08:52:25', '2025-11-24 08:52:25', 'user', NULL, 1, NULL),
(12, 'jack', 'jack@gmail.com', NULL, '$2y$10$4OPMx0NC7sXIg23/hWQt1u0t52jEDgc5grk/LZAOmmFw8a3DAy.BW', NULL, NULL, 'Bronze', 297000, 3, 'active', 0, '2025-11-25 02:20:46', '2025-11-25 02:27:04', 'user', NULL, 1, NULL),
(13, 'huung', 'nguyenconghung954@gmail.com', NULL, '$2y$10$0aCzLlyOsSw4IZeDM8Vr8uC.1zWUY/F0SZTjwU8hrS9jxzvUvTgeG', NULL, NULL, 'Bronze', 0, 1, 'active', 0, '2025-11-25 12:43:00', '2025-11-25 12:43:00', 'user', NULL, 1, NULL),
(14, 'bom', 'vlinh25062006@gmail.com', NULL, '$2y$10$SGQNRO1gcjuJy76tKCWx7e/9boVMyK2kkgK5D4PMepeswkveVa2qa', NULL, NULL, 'Bronze', 0, 1, 'active', 0, '2025-11-26 04:03:42', '2025-11-26 04:03:42', 'user', NULL, 1, NULL),
(15, 'Hải Nam', 'natgao0001@gmail.com', NULL, '$2y$10$62PMj1vSUIjXo4.d8EJ8J.JJVrnO764zQvDZorn2BsfiT9ecJCzGe', NULL, NULL, 'Bronze', 0, 1, 'active', 0, '2025-12-04 18:01:00', '2025-12-04 18:01:00', 'user', NULL, 1, NULL),
(16, 'Thanh', 'le3221981@gmail.com', NULL, '$2y$10$bomnjBXwqML823EJUbdDsOh22J4vmfXElcK0M.CMzk9X2NrcpIyGm', 'data/avatars/avatar_16_1764872833.jpeg', NULL, 'Bronze', 400000, 1, 'active', 0, '2025-12-04 18:22:20', '2025-12-04 18:30:11', 'user', NULL, 1, NULL),
(17, 'Admin Rạp CGV Vincom', 'admin_rap1@cinehub.com', NULL, '$2y$10$Q516uBkFiAAoP9sABaJJRebPWUFZjqKI9370ZLqFxlhtFE1L1r9ba', NULL, NULL, 'Bronze', 0, NULL, 'active', 0, '2025-12-06 14:43:27', '2025-12-06 14:43:27', 'moderator', 1, 1, NULL),
(18, 'Nhân viên Quầy 1', 'nhanvien1@cinehub.com', NULL, '$2y$10$Q516uBkFiAAoP9sABaJJRebPWUFZjqKI9370ZLqFxlhtFE1L1r9ba', NULL, NULL, 'Bronze', 0, NULL, 'active', 0, '2025-12-06 14:43:27', '2025-12-06 14:43:27', 'user', 1, 1, NULL),
(19, 'Le Van Phat', 'plv@gmail.com', NULL, '$2y$12$sgkGlJx7H08Fi/uiIBtnGujFi5dbkr7Tdh1px6fPGJ7iAkS/G443u', NULL, NULL, 'Bronze', 0, NULL, 'active', 0, '2025-12-06 15:08:04', '2026-06-15 20:00:42', 'user', 3, 1, NULL),
(20, 'Tuan Anh', 'awhtuan@gmail.com', NULL, '$2y$10$Gs3zYtOxwS7L4M11Ad0dMOUADYr5Bg.oPl6TJHjqWKBD38FTGLK.u', NULL, NULL, 'Bronze', 0, NULL, 'active', 0, '2025-12-06 17:27:56', '2025-12-06 17:27:56', 'user', 2, 1, NULL),
(21, 'Nguyễn Hoàng Sơn', 'hson97805@gmail.com', NULL, '$2y$10$42gA6q4czX5DAA4JsKseSe0uFxBXkI4leaoL9Hbi.iQqdys9RF2q2', NULL, '2025-12-06', 'Bronze', 0, 1, 'active', 0, '2025-12-08 04:08:50', '2025-12-08 04:10:34', 'user', NULL, 1, NULL),
(22, 'Lotte', 'lotte@gmail.com', NULL, '$2y$10$kDPiqXj7ZfEYGBvrQIHY5eODeYV2y7SWN5EwHytjQWOfK/dpXwx1C', NULL, NULL, 'Bronze', 0, 1, 'active', 0, '2025-12-08 18:53:32', '2025-12-08 18:54:55', 'moderator', 6, 1, NULL),
(23, 'betaTH', 'betath@gmail.com', NULL, '$2y$12$nvjw65xDMuV/0RC.jMM5H./9XPIB/Xm0lcH/MZGGfPYQdjk9XSF9u', NULL, NULL, 'Bronze', 0, 1, 'active', 0, '2025-12-08 18:54:08', '2026-06-15 20:05:19', 'moderator', 7, 1, NULL),
(24, 'trang', 'thutrang1@gmail.com', NULL, '$2y$10$syT/.T7JUcAhEqFYLS9bfObvzSqkYvueKcalkYQBp5hZF1IA4Hwk.', NULL, NULL, 'Bronze', 0, 1, 'active', 0, '2025-12-09 02:30:58', '2025-12-09 02:30:58', 'user', NULL, 1, NULL),
(25, 'Trùm thiên hạ', 'vanquan2006k@gmail.com', NULL, '$2y$10$Q1jFMT5RHDxdT7nijhzTMOlhOOkPWBrVj2nr2I/azcwg.Gmvt.8xC', 'data/avatars/avatar_25_1765294523.jpg', NULL, 'Bronze', 0, 1, 'active', 0, '2025-12-09 03:33:26', '2025-12-10 02:30:40', 'user', NULL, 1, NULL),
(26, 'FAN ANH JACK', 'khoiphuc255@gmail.com', NULL, '$2y$10$mBkdgi4XcgLU6rLbADRfeOmowDvXpsBgRND2L6D5dL8Z2a6dFr8ai', 'data/avatars/avatar_26_1765251965.jpg', NULL, 'Bronze', 9801000, 4, 'active', 0, '2025-12-09 03:36:19', '2025-12-09 03:46:05', 'user', NULL, 1, NULL),
(27, 'trang', 'thutrang12@gmail.com', NULL, '$2y$10$wKoi2tCHC4qWkYxWyP6VZuMu36aN.04xRNQhdO/CcU1wNYjmW.M76', NULL, NULL, 'Bronze', 0, 1, 'active', 0, '2025-12-09 11:33:45', '2025-12-09 11:33:45', 'user', NULL, 1, NULL),
(28, 'trang', 'trang12@gmail.com', NULL, '$2y$10$M5m.jX22AFNgTpKObCbyPeOwsI9Yk.M/vTChWKCwBKJYPxLWsJ4SG', NULL, NULL, 'Bronze', 0, 1, 'active', 0, '2025-12-09 12:35:23', '2025-12-09 12:35:23', 'user', NULL, 1, NULL),
(29, 'Vũ Đình Tư', 'vtu8531@gmail.com', NULL, '$2y$10$bTIw0qpazioAhDk31ZXQNeRqaVA/qzau6ji0Gff9ucbuhwaP8Ebd6', NULL, NULL, 'Bronze', 0, 1, 'active', 0, '2025-12-11 08:55:37', '2025-12-11 08:55:37', 'user', NULL, 1, NULL),
(30, 'Linh', 'nguyenvanlinh250606@gmail.com', NULL, '$2y$10$qGBli/hydJYruPcWlQ3KweLQhLsFXysE8vNsBQsuOtvndfppC80EG', 'data/avatars/avatar_30_1765767345.jpeg', NULL, 'Bronze', 0, NULL, 'active', 0, '2025-12-12 02:06:48', '2025-12-15 02:55:45', 'user', 6, 1, NULL),
(31, 'Dương Hải Cường', 'sccuong5222@gmail.com', NULL, '$2y$10$UmMuWQ0pSc.PaoElXDS2m.h7h4oiPyCYzpoBXUzfLCmM7YQulr.KK', NULL, NULL, 'Bronze', 0, 1, 'active', 0, '2025-12-14 14:43:40', '2025-12-14 14:43:40', 'user', NULL, 1, NULL),
(32, 'trung', 'ledinhtrung35@gmail.com', NULL, '$2y$10$/0rHIDMSPL6g3JJwW75CUOoU6H8Po9vLtQhUgCDzZavFUDW9T9RKu', NULL, NULL, 'Bronze', 0, 1, 'active', 0, '2026-01-27 12:36:40', '2026-01-27 12:36:40', 'user', NULL, 1, NULL),
(33, 'coca', 'ledinhtrungkm35@gmail.com', NULL, '$2y$10$pzCzNTULdcpPXQaY5GlKlOGKPN/DwTLhGguEAtkBjM3rB5zfISGVy', NULL, NULL, 'Bronze', 0, 1, 'active', 0, '2026-01-27 13:34:52', '2026-01-27 13:34:52', 'user', NULL, 1, NULL),
(34, 'Lê Đình Trung', 'ledinhtrung12a1@gmail.com', NULL, '$2y$10$3do8eryE78u/sJzquhBSaeTFsEmnUyC2tnfIR1CqibwCVEk4Bod76', NULL, NULL, 'Bronze', 0, 1, 'active', 0, '2026-01-27 13:45:23', '2026-01-27 13:45:23', 'user', NULL, 1, NULL),
(36, 'Nhân viên Test', 'staff@test.com', NULL, '$2y$12$AdgTudGlcDVX9bZq8iy5mO86/U2FS99uOD4C3gfwpFheO7eyRSvze', NULL, NULL, 'Bronze', 0, NULL, 'active', 0, '2026-06-15 20:25:51', '2026-06-15 20:25:51', 'user', 3, 1, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_roles`
--

CREATE TABLE `user_roles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `user_roles`
--

INSERT INTO `user_roles` (`id`, `user_id`, `role_id`, `created_at`) VALUES
(4, 2, 1, '2025-11-10 16:45:54'),
(7, 3, 1, '2025-11-12 02:39:39'),
(8, 4, 3, '2025-11-12 07:41:09'),
(9, 5, 4, '2025-11-12 07:41:09'),
(10, 1, 5, '2025-11-12 07:41:09'),
(11, 17, 7, '2025-12-06 14:43:27'),
(12, 18, 8, '2025-12-06 14:43:27'),
(13, 19, 8, '2025-12-06 15:08:04'),
(14, 20, 8, '2025-12-06 17:27:56'),
(15, 30, 8, '2025-12-12 02:06:48');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_tokens`
--

CREATE TABLE `user_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `device_info` varchar(500) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `user_tokens`
--

INSERT INTO `user_tokens` (`id`, `user_id`, `token`, `device_info`, `ip_address`, `expires_at`, `created_at`) VALUES
(0, 1, '7b32aadef21999c3720213862d37bc018ae475e3e822f3e22b4dab43af082cd6', 'Google Chrome on Windows', '::1', '2025-12-25 22:03:42', '2025-11-26 04:03:42');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `watch_history`
--

CREATE TABLE `watch_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `last_time` int(11) DEFAULT 0,
  `rating` tinyint(4) DEFAULT NULL,
  `favorite` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `watch_history`
--

INSERT INTO `watch_history` (`id`, `user_id`, `movie_id`, `last_time`, `rating`, `favorite`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 3600, 5, 1, '2025-11-12 07:41:09', NULL),
(2, 2, 2, 7200, 5, 1, '2025-11-12 07:41:09', NULL),
(3, 3, 3, 1800, 4, 0, '2025-11-12 07:41:09', NULL),
(4, 4, 4, 2400, 4, 0, '2025-11-12 07:41:09', NULL),
(5, 5, 5, 3000, 5, 1, '2025-11-12 07:41:09', NULL),
(6, 1, 6, 5400, 5, 1, '2025-11-12 07:41:09', NULL),
(7, 2, 7, 2100, 4, 0, '2025-11-12 07:41:09', NULL),
(8, 9, 2, 0, NULL, 0, '2025-11-14 01:37:54', NULL),
(9, 9, 5, 0, NULL, 0, '2025-11-17 08:57:34', NULL),
(12, 9, 1, 0, NULL, 1, '2025-11-19 01:17:46', NULL),
(61, 9, 29, 0, NULL, 0, '2025-12-04 19:02:14', NULL),
(80, 3, 30, 0, NULL, 0, '2025-11-25 13:17:35', NULL),
(81, 3, 35, 0, NULL, 0, '2025-11-25 13:30:54', NULL),
(83, 3, 37, 0, NULL, 0, '2025-11-25 14:13:19', NULL),
(84, 3, 33, 0, NULL, 0, '2025-12-09 01:57:06', NULL),
(85, 3, 34, 0, NULL, 0, '2025-11-25 14:20:48', NULL),
(87, 3, 36, 0, NULL, 0, '2025-11-25 14:33:55', NULL),
(92, 9, 28, 0, NULL, 0, '2025-11-26 02:47:13', NULL),
(93, 9, 6, 0, NULL, 1, '2025-12-04 19:36:37', NULL),
(95, 2, 38, 0, NULL, 0, '2025-12-04 03:57:28', NULL),
(99, 9, 35, 0, NULL, 0, '2025-12-09 01:56:13', NULL),
(102, 9, 7, 0, NULL, 0, '2025-12-04 19:01:49', NULL),
(104, 9, 37, 0, NULL, 0, '2025-12-04 19:01:56', NULL),
(111, 9, 30, 0, NULL, 0, '2025-12-04 19:02:54', NULL),
(124, 21, 28, 0, NULL, 0, '2025-12-09 01:35:31', NULL),
(125, 21, 33, 0, NULL, 0, '2025-12-09 01:58:36', NULL),
(136, 9, 33, 0, NULL, 0, '2025-12-14 15:40:08', NULL),
(145, 22, 33, 0, NULL, 0, '2025-12-09 01:59:40', NULL),
(149, 26, 8, 0, NULL, 0, '2025-12-09 03:45:06', NULL),
(156, 9, 8, 0, NULL, 0, '2026-02-09 02:58:03', NULL),
(164, 26, 28, 0, NULL, 0, '2025-12-09 03:45:34', NULL),
(165, 27, 37, 0, NULL, 0, '2025-12-09 11:34:34', NULL),
(168, 29, 37, 0, NULL, 0, '2025-12-11 08:56:08', NULL),
(170, 29, 28, 0, NULL, 0, '2025-12-11 08:59:28', NULL),
(174, 3, 12, 0, NULL, 0, '2025-12-13 12:23:36', NULL),
(178, 9, 9, 0, NULL, 1, '2025-12-14 15:11:49', NULL),
(180, 3, 51, 0, NULL, 0, '2025-12-23 14:30:31', NULL),
(184, 10, 28, 0, NULL, 0, '2025-12-15 02:09:33', NULL),
(186, 3, 8, 0, NULL, 0, '2025-12-15 02:10:36', NULL),
(188, 3, 6, 0, NULL, 0, '2026-01-27 13:00:39', NULL),
(191, 3, 9, 0, NULL, 0, '2025-12-15 02:43:01', NULL),
(200, 3, 38, 0, NULL, 0, '2025-12-23 14:31:08', NULL),
(202, 10, 33, 0, NULL, 0, '2025-12-26 18:17:59', NULL),
(203, 9, 51, 0, NULL, 0, '2026-02-09 02:58:53', NULL),
(208, 32, 37, 0, NULL, 0, '2026-01-27 12:37:03', NULL),
(209, 32, 33, 0, NULL, 0, '2026-01-27 12:37:33', NULL),
(211, 32, 51, 0, NULL, 0, '2026-01-27 13:03:01', NULL),
(215, 10, 51, 0, NULL, 0, '2026-02-03 02:28:47', NULL),
(227, 3, 53, 0, NULL, 0, '2026-02-09 03:08:09', NULL),
(233, 9, 53, 0, NULL, 0, '2026-06-08 23:48:31', '2026-06-08 23:48:31');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_module` (`module`);

--
-- Chỉ mục cho bảng `booking_food_items`
--
ALTER TABLE `booking_food_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`),
  ADD KEY `food_item_id` (`food_item_id`),
  ADD KEY `fk_booking_food_pending` (`booking_pending_id`);

--
-- Chỉ mục cho bảng `booking_pending`
--
ALTER TABLE `booking_pending`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vnp_txn_ref` (`vnp_txn_ref`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_showtime` (`showtime_id`),
  ADD KEY `idx_txn_ref` (`vnp_txn_ref`),
  ADD KEY `idx_booking_code` (`booking_code`),
  ADD KEY `idx_status` (`status`);

--
-- Chỉ mục cho bảng `booking_session_tracking`
--
ALTER TABLE `booking_session_tracking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `showtime_id` (`showtime_id`),
  ADD KEY `screen_id` (`screen_id`),
  ADD KEY `session_start` (`session_start`),
  ADD KEY `is_banned` (`is_banned`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Chỉ mục cho bảng `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `idx_movie_id` (`movie_id`),
  ADD KEY `idx_status` (`status`);

--
-- Chỉ mục cho bảng `comment_likes`
--
ALTER TABLE `comment_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_comment` (`user_id`,`comment_id`),
  ADD KEY `idx_comment_id` (`comment_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Chỉ mục cho bảng `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_code` (`code`),
  ADD KEY `idx_status` (`status`);

--
-- Chỉ mục cho bảng `episodes`
--
ALTER TABLE `episodes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_movie_episode` (`movie_id`,`episode_number`),
  ADD KEY `idx_movie_id` (`movie_id`);

--
-- Chỉ mục cho bảng `food_items`
--
ALTER TABLE `food_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `theater_id` (`theater_id`);

--
-- Chỉ mục cho bảng `ip_blocks`
--
ALTER TABLE `ip_blocks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_ip` (`ip_address`),
  ADD KEY `idx_expires_at` (`expires_at`),
  ADD KEY `idx_ip_expires` (`ip_address`,`expires_at`);

--
-- Chỉ mục cho bảng `ip_room_tracking`
--
ALTER TABLE `ip_room_tracking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ip_screen_showtime` (`ip_address`,`screen_id`,`showtime_id`),
  ADD KEY `idx_ip_screen` (`ip_address`,`screen_id`),
  ADD KEY `idx_is_banned` (`is_banned`,`ban_until`);

--
-- Chỉ mục cho bảng `ip_spam_logs`
--
ALTER TABLE `ip_spam_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ip_address` (`ip_address`),
  ADD KEY `idx_action_type` (`action_type`),
  ADD KEY `idx_is_spam` (`is_spam`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_ip_action_spam` (`ip_address`,`action_type`,`is_spam`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Chỉ mục cho bảng `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `moderator_permission_requests`
--
ALTER TABLE `moderator_permission_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `theater_id` (`theater_id`),
  ADD KEY `moderator_id` (`moderator_id`),
  ADD KEY `requested_by` (`requested_by`),
  ADD KEY `target_user_id` (`target_user_id`);

--
-- Chỉ mục cho bảng `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category_id`);

--
-- Chỉ mục cho bảng `movie_categories`
--
ALTER TABLE `movie_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_movie_category` (`movie_id`,`category_id`),
  ADD KEY `movie_id` (`movie_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Chỉ mục cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `is_read` (`is_read`);

--
-- Chỉ mục cho bảng `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Chỉ mục cho bảng `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_rev_user` (`user_id`),
  ADD KEY `idx_rev_movie` (`movie_id`);

--
-- Chỉ mục cho bảng `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Chỉ mục cho bảng `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_role_permission` (`role_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Chỉ mục cho bảng `seat_reservations`
--
ALTER TABLE `seat_reservations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_seat_reservation` (`showtime_id`,`seat`),
  ADD KEY `idx_showtime_seat` (`showtime_id`,`seat`),
  ADD KEY `idx_expires_at` (`expires_at`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_active_reservations` (`showtime_id`,`expires_at`);

--
-- Chỉ mục cho bảng `seat_selection_logs`
--
ALTER TABLE `seat_selection_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_ip_address_seat_logs` (`ip_address`),
  ADD KEY `showtime_id` (`showtime_id`),
  ADD KEY `created_at` (`created_at`),
  ADD KEY `is_spam` (`is_spam`);

--
-- Chỉ mục cho bảng `showtimes`
--
ALTER TABLE `showtimes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_showtime` (`screen_id`,`show_date`,`show_time`),
  ADD KEY `idx_movie` (`movie_id`),
  ADD KEY `idx_theater` (`theater_id`),
  ADD KEY `screen_id` (`screen_id`),
  ADD KEY `idx_theater_screen` (`theater_id`,`screen_id`);

--
-- Chỉ mục cho bảng `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `assigned_to` (`assigned_to`);

--
-- Chỉ mục cho bảng `system_config`
--
ALTER TABLE `system_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `config_key` (`config_key`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Chỉ mục cho bảng `theaters`
--
ALTER TABLE `theaters`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `theater_managers`
--
ALTER TABLE `theater_managers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_theater` (`user_id`,`theater_id`),
  ADD KEY `theater_id` (`theater_id`);

--
-- Chỉ mục cho bảng `theater_screens`
--
ALTER TABLE `theater_screens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `theater_id` (`theater_id`);

--
-- Chỉ mục cho bảng `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_showtime` (`showtime_id`),
  ADD KEY `idx_showtime_status` (`showtime_id`,`status`),
  ADD KEY `idx_user_showtime` (`user_id`,`showtime_id`),
  ADD KEY `idx_showtime_seat_status` (`showtime_id`,`seat`,`status`),
  ADD KEY `idx_booking_pending` (`booking_pending_id`),
  ADD KEY `idx_is_picked_up` (`is_picked_up`),
  ADD KEY `idx_picked_up_at` (`picked_up_at`),
  ADD KEY `idx_picked_up_by` (`picked_up_by`);

--
-- Chỉ mục cho bảng `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tx_user` (`user_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_subscription` (`subscription_id`),
  ADD KEY `idx_theater_id` (`theater_id`);

--
-- Chỉ mục cho bảng `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_role` (`user_id`,`role_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Chỉ mục cho bảng `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `expires_at` (`expires_at`);

--
-- Chỉ mục cho bảng `watch_history`
--
ALTER TABLE `watch_history`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_movie` (`user_id`,`movie_id`),
  ADD KEY `idx_wh_user` (`user_id`),
  ADD KEY `idx_wh_movie` (`movie_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT cho bảng `booking_food_items`
--
ALTER TABLE `booking_food_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT cho bảng `booking_pending`
--
ALTER TABLE `booking_pending`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=880;

--
-- AUTO_INCREMENT cho bảng `booking_session_tracking`
--
ALTER TABLE `booking_session_tracking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=204;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT cho bảng `comment_likes`
--
ALTER TABLE `comment_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `episodes`
--
ALTER TABLE `episodes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT cho bảng `food_items`
--
ALTER TABLE `food_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT cho bảng `ip_blocks`
--
ALTER TABLE `ip_blocks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `ip_room_tracking`
--
ALTER TABLE `ip_room_tracking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=195;

--
-- AUTO_INCREMENT cho bảng `ip_spam_logs`
--
ALTER TABLE `ip_spam_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT cho bảng `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `moderator_permission_requests`
--
ALTER TABLE `moderator_permission_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `movies`
--
ALTER TABLE `movies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT cho bảng `movie_categories`
--
ALTER TABLE `movie_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT cho bảng `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT cho bảng `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT cho bảng `promotions`
--
ALTER TABLE `promotions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT cho bảng `seat_reservations`
--
ALTER TABLE `seat_reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1004;

--
-- AUTO_INCREMENT cho bảng `seat_selection_logs`
--
ALTER TABLE `seat_selection_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=202;

--
-- AUTO_INCREMENT cho bảng `showtimes`
--
ALTER TABLE `showtimes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42047;

--
-- AUTO_INCREMENT cho bảng `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `system_config`
--
ALTER TABLE `system_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `theaters`
--
ALTER TABLE `theaters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `theater_managers`
--
ALTER TABLE `theater_managers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `theater_screens`
--
ALTER TABLE `theater_screens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT cho bảng `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6129;

--
-- AUTO_INCREMENT cho bảng `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=154;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT cho bảng `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT cho bảng `watch_history`
--
ALTER TABLE `watch_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=234;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD CONSTRAINT `admin_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `booking_food_items`
--
ALTER TABLE `booking_food_items`
  ADD CONSTRAINT `fk_booking_food_item` FOREIGN KEY (`food_item_id`) REFERENCES `food_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_booking_food_pending` FOREIGN KEY (`booking_pending_id`) REFERENCES `booking_pending` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_booking_food_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `booking_pending`
--
ALTER TABLE `booking_pending`
  ADD CONSTRAINT `booking_pending_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_pending_ibfk_2` FOREIGN KEY (`showtime_id`) REFERENCES `showtimes` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `booking_session_tracking`
--
ALTER TABLE `booking_session_tracking`
  ADD CONSTRAINT `booking_session_tracking_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_session_tracking_ibfk_2` FOREIGN KEY (`showtime_id`) REFERENCES `showtimes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_session_tracking_ibfk_3` FOREIGN KEY (`screen_id`) REFERENCES `theater_screens` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `episodes`
--
ALTER TABLE `episodes`
  ADD CONSTRAINT `fk_episodes_movie` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `moderator_permission_requests`
--
ALTER TABLE `moderator_permission_requests`
  ADD CONSTRAINT `moderator_permission_requests_ibfk_1` FOREIGN KEY (`theater_id`) REFERENCES `theaters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `moderator_permission_requests_ibfk_2` FOREIGN KEY (`moderator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `moderator_permission_requests_ibfk_3` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `movies`
--
ALTER TABLE `movies`
  ADD CONSTRAINT `movies_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `movie_categories`
--
ALTER TABLE `movie_categories`
  ADD CONSTRAINT `fk_movie_categories_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_movie_categories_movie` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `seat_reservations`
--
ALTER TABLE `seat_reservations`
  ADD CONSTRAINT `fk_seat_reservations_showtime` FOREIGN KEY (`showtime_id`) REFERENCES `showtimes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_seat_reservations_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `seat_selection_logs`
--
ALTER TABLE `seat_selection_logs`
  ADD CONSTRAINT `seat_selection_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `seat_selection_logs_ibfk_2` FOREIGN KEY (`showtime_id`) REFERENCES `showtimes` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `showtimes`
--
ALTER TABLE `showtimes`
  ADD CONSTRAINT `showtimes_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `showtimes_ibfk_2` FOREIGN KEY (`theater_id`) REFERENCES `theaters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `showtimes_ibfk_3` FOREIGN KEY (`screen_id`) REFERENCES `theater_screens` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD CONSTRAINT `support_tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `support_tickets_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `system_config`
--
ALTER TABLE `system_config`
  ADD CONSTRAINT `system_config_ibfk_1` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `theater_managers`
--
ALTER TABLE `theater_managers`
  ADD CONSTRAINT `theater_managers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `theater_managers_ibfk_2` FOREIGN KEY (`theater_id`) REFERENCES `theaters` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `theater_screens`
--
ALTER TABLE `theater_screens`
  ADD CONSTRAINT `theater_screens_ibfk_1` FOREIGN KEY (`theater_id`) REFERENCES `theaters` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`showtime_id`) REFERENCES `showtimes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tickets_ibfk_3` FOREIGN KEY (`booking_pending_id`) REFERENCES `booking_pending` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD CONSTRAINT `user_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `watch_history`
--
ALTER TABLE `watch_history`
  ADD CONSTRAINT `watch_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `watch_history_ibfk_2` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
