-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 18, 2026 at 11:29 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `diemdanh_sv`
--

-- --------------------------------------------------------

--
-- Table structure for table `anh_train_svs`
--

DROP TABLE IF EXISTS `anh_train_svs`;
CREATE TABLE IF NOT EXISTS `anh_train_svs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `sinh_vien_id` bigint UNSIGNED NOT NULL,
  `hinh_anh` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `face_id` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `file_hash` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trang_thai` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `anh_train_svs_sinh_vien_id_foreign` (`sinh_vien_id`)
) ENGINE=InnoDB AUTO_INCREMENT=194 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `anh_train_svs`
--

INSERT INTO `anh_train_svs` (`id`, `sinh_vien_id`, `hinh_anh`, `face_id`, `file_hash`, `file_name`, `trang_thai`, `created_at`, `updated_at`) VALUES
(108, 24, 'sinhvien/DH52200346/DH52200346_6a84e3d8a3b2b.jpg', '[\"7ad54a8e-a77a-48ff-a50c-009404e2bf92\"]', '61d56b302ab341d0a655693d4e1eb3cb', 'DH52200346.jpg', 'trained', '2026-08-18 22:59:36', '2026-08-18 22:59:41'),
(109, 34, 'sinhvien/DH52200335/DH52200335_6a84e3de40325.jpg', '[\"5c9609af-b4a7-4056-85b1-5efba226e264\"]', 'a03366d3546d01ba3dc6e1e9315a8f38', 'DH52200335.jpg', 'trained', '2026-08-18 22:59:42', '2026-08-18 22:59:45'),
(110, 35, 'sinhvien/DH52200343/DH52200343_6a84e3e131a7c.jpg', '[\"4eed5631-1c2f-447e-83b2-be09682e1f08\"]', '5075c21f1740a8a76c036bd4da603d71', 'DH52200343.jpg', 'trained', '2026-08-18 22:59:45', '2026-08-18 22:59:47'),
(111, 36, 'sinhvien/DH52200353/DH52200353_6a84e3e3e5554.jpg', '[\"8399b5c7-e504-4ab3-ab68-a3403c668a08\"]', '6121c326edee2e581328315b3a757d7c', 'DH52200353.jpg', 'trained', '2026-08-18 22:59:47', '2026-08-18 22:59:50'),
(112, 37, 'sinhvien/DH52200363/DH52200363_6a84e3e684d46.jpg', '[\"536bc44d-ffc8-4828-bf0e-1304d5b3711c\"]', '929f672719c5d38bae6bf8f17f94038a', 'DH52200363.jpg', 'trained', '2026-08-18 22:59:50', '2026-08-18 22:59:52'),
(113, 38, 'sinhvien/DH52200370/DH52200370_6a84e3e91f50d.jpg', '[\"93d4f518-e9b0-462a-92ed-ed79a868910d\"]', 'fc1a89b24957c1db39d00d17cbc53f37', 'DH52200370.jpg', 'trained', '2026-08-18 22:59:53', '2026-08-18 22:59:55'),
(114, 39, 'sinhvien/DH52200386/DH52200386_6a84e3ebac140.jpg', '[\"602b72a4-9301-4618-bccc-4a32075853f8\"]', 'c9f1fa6529834c55c17d97d6aa68de95', 'DH52200386.jpg', 'trained', '2026-08-18 22:59:55', '2026-08-18 22:59:58'),
(115, 40, 'sinhvien/DH52200402/DH52200402_6a84e3ee54517.jpg', '[\"e3450a9c-2c13-4d1e-a419-537d7842ec32\"]', '3b832236bcc8b24427233197be2ea135', 'DH52200402.jpg', 'trained', '2026-08-18 22:59:58', '2026-08-18 23:00:00'),
(116, 41, 'sinhvien/DH52200417/DH52200417_6a84e3f11461c.jpg', '[\"26db84b7-f848-429c-8a36-7ab175a6f72d\"]', '7b6b7cc599a397f729434f3378b15135', 'DH52200417.jpg', 'trained', '2026-08-18 23:00:01', '2026-08-18 23:00:03'),
(117, 42, 'sinhvien/DH52200418/DH52200418_6a84e3f3cf008.jpg', '[\"36054d6d-9378-492b-8aea-60beda29ebe2\"]', 'c18aa9fd50a011670f2ddc34f1b36cbf', 'DH52200418.jpg', 'trained', '2026-08-18 23:00:03', '2026-08-18 23:00:06'),
(118, 43, 'sinhvien/DH52200476/DH52200476_6a84e3f6a7084.jpg', '[\"b5c36e3c-9416-4fbd-9b82-bef9b98ffb3d\"]', '1f0d1050fda7ca33c2b3ab158e70df92', 'DH52200476.jpg', 'trained', '2026-08-18 23:00:06', '2026-08-18 23:00:09'),
(119, 44, 'sinhvien/DH52200514/DH52200514_6a84e3f93fff3.jpg', '[\"16fbaa65-6994-4c81-8d8f-92bcbcf00d50\"]', '1d88832c9a3c68b5b8e15727b87c43aa', 'DH52200514.jpg', 'trained', '2026-08-18 23:00:09', '2026-08-18 23:00:11'),
(120, 46, 'sinhvien/DH52200568/DH52200568_6a84e3fbe5a5e.jpg', '[\"1469225f-8cc6-4fc3-b851-bf2caea46593\"]', 'a13a7a5e0f285988ee39dabe4017c129', 'DH52200568.jpg', 'trained', '2026-08-18 23:00:11', '2026-08-18 23:00:14'),
(121, 45, 'sinhvien/DH52200516/DH52200516_6a84e3fe60e2f.jpg', '[\"e3ad2c7d-3a7b-4127-ab26-d54515d17f96\"]', '343dad66fa2cb0644ff2171de23f0b45', 'DH52200516.jpg', 'trained', '2026-08-18 23:00:14', '2026-08-18 23:00:16'),
(122, 47, 'sinhvien/DH52200577/DH52200577_6a84e4011c2d6.jpg', '[\"8fcff784-4b11-45c2-acfe-2e6a13f7123e\"]', 'bcf71f706041564d27ac2e1fd1bb89a5', 'DH52200577.jpg', 'trained', '2026-08-18 23:00:17', '2026-08-18 23:00:19'),
(123, 48, 'sinhvien/DH52200589/DH52200589_6a84e40402952.jpg', '[\"227e206f-ae49-4fbc-bff7-9e7b84aa60b3\"]', 'c57c9ee2daa53a2b8f03ef2cbe4b1a0a', 'DH52200589.jpg', 'trained', '2026-08-18 23:00:20', '2026-08-18 23:00:22'),
(124, 49, 'sinhvien/DH52200594/DH52200594_6a84e406a2af2.jpg', '[\"bbd1a0ae-33f0-4afb-a81d-ab3d50e87023\"]', '7e9764d0430177b5099288f53c646b3c', 'DH52200594.jpg', 'trained', '2026-08-18 23:00:22', '2026-08-18 23:00:25'),
(125, 50, 'sinhvien/DH52200605/DH52200605_6a84e40943b33.jpg', '[\"eafa5c5e-81cc-499e-bcad-572260a8b49e\"]', '66a44c33ec67e9920b03620898a892eb', 'DH52200605.jpg', 'trained', '2026-08-18 23:00:25', '2026-08-18 23:00:27'),
(126, 51, 'sinhvien/DH52200606/DH52200606_6a84e40bd7f5e.jpg', '[\"eda58f20-e878-401c-a6d9-1207d1cfcfd2\"]', '088a7f2f7d02aef934c5f328b778ab2d', 'DH52200606.jpg', 'trained', '2026-08-18 23:00:27', '2026-08-18 23:00:30'),
(127, 52, 'sinhvien/DH52200613/DH52200613_6a84e40e64fdd.jpg', '[\"bf38ca32-49af-49f2-a276-7cbac618315b\"]', 'd39fc9bf999a4c018a4e0271c1834b5e', 'DH52200613.jpg', 'trained', '2026-08-18 23:00:30', '2026-08-18 23:00:32'),
(128, 53, 'sinhvien/DH52200638/DH52200638_6a84e41121018.jpg', '[\"59753e3b-218a-48e0-95b6-f16ffed900ac\"]', 'a2cf8947a6b4b1bd1db85a80dfcbb4d4', 'DH52200638.jpg', 'trained', '2026-08-18 23:00:33', '2026-08-18 23:00:35'),
(129, 54, 'sinhvien/DH52200649/DH52200649_6a84e413c1e7c.jpg', '[\"fe6caec5-5063-46a6-b053-bc716c1a1498\"]', '660ae9eb3b87b0e79011af372c92e387', 'DH52200649.jpg', 'trained', '2026-08-18 23:00:35', '2026-08-18 23:00:38'),
(130, 55, 'sinhvien/DH52200688/DH52200688_6a84e4167348c.jpg', '[\"e84b0c51-0326-4d5f-8524-be5dc826b11c\"]', '9c85c3e2f53e39e98dc671b148bb6762', 'DH52200688.jpg', 'trained', '2026-08-18 23:00:38', '2026-08-18 23:00:41'),
(131, 56, 'sinhvien/DH52200704/DH52200704_6a84e41955b73.jpg', '[\"be3162b6-d994-4d34-9c03-03aa4ecbb958\"]', '4f6d2353f7683fc808f1dbee204bd607', 'DH52200704.jpg', 'trained', '2026-08-18 23:00:41', '2026-08-18 23:00:43'),
(132, 57, 'sinhvien/DH52200734/DH52200734_6a84e41be896e.jpg', '[\"1d5eadc4-95b9-4a13-bd96-7aea60b514ed\"]', '1279525a6a2a5cff5cfa53534041ab12', 'DH52200734.jpg', 'trained', '2026-08-18 23:00:43', '2026-08-18 23:00:46'),
(133, 58, 'sinhvien/DH52200737/DH52200737_6a84e41ea1156.jpg', '[\"6731dfa1-1477-4fd0-a43a-8963d7bc21ff\"]', '65ea817adfe55cd9fa5c4b29315ba3c9', 'DH52200737.jpg', 'trained', '2026-08-18 23:00:46', '2026-08-18 23:00:49'),
(134, 59, 'sinhvien/DH52200746/DH52200746_6a84e4217e735.jpg', '[\"e5203f5c-b055-472b-ae97-42bd108806c0\"]', '0943d82dc129197536900085b6be5839', 'DH52200746.jpg', 'trained', '2026-08-18 23:00:49', '2026-08-18 23:00:52'),
(135, 60, 'sinhvien/DH52200764/DH52200764_6a84e42443dc1.jpg', '[\"1d59236f-2fbc-4abf-9b1e-c11baa0a6bc4\"]', '1e279e7d3a475402420e277c3c298246', 'DH52200764.jpg', 'trained', '2026-08-18 23:00:52', '2026-08-18 23:00:54'),
(136, 61, 'sinhvien/DH52200781/DH52200781_6a84e42703d98.jpg', '[\"d5bbf25d-183f-4ed9-81c3-e8131cfbfe7e\"]', '8f90420a9fbdc131b0cc5c2bf2a3d440', 'DH52200781.jpg', 'trained', '2026-08-18 23:00:55', '2026-08-18 23:00:57'),
(137, 62, 'sinhvien/DH52200795/DH52200795_6a84e429acb3c.jpg', '[\"6488104e-e399-48c6-bd30-7db05666936a\"]', '4122f50115059da77a105e5c75cb0549', 'DH52200795.jpg', 'trained', '2026-08-18 23:00:57', '2026-08-18 23:01:00'),
(138, 63, 'sinhvien/DH52200837/DH52200837_6a84e42c5a43a.jpg', '[\"b138c975-0f3d-4be5-9cb6-434a49b4190b\"]', 'da929139fef96867748d98f122b22b16', 'DH52200837.jpg', 'trained', '2026-08-18 23:01:00', '2026-08-18 23:01:02'),
(139, 64, 'sinhvien/DH52200856/DH52200856_6a84e42f193ed.jpg', '[\"1212f8fe-dbea-4874-b29b-c671547e9f4c\"]', 'c696df6e6f2998da7a6b9a64e833c84d', 'DH52200856.jpg', 'trained', '2026-08-18 23:01:03', '2026-08-18 23:01:05'),
(140, 65, 'sinhvien/DH52200873/DH52200873_6a84e431d4e42.jpg', '[\"4b061d2b-409b-469b-952f-eb399c6189a2\"]', 'cda593a8db7d98f119db62dd6bf9b06e', 'DH52200873.jpg', 'trained', '2026-08-18 23:01:05', '2026-08-18 23:01:08'),
(141, 66, 'sinhvien/DH52200874/DH52200874_6a84e43472526.jpg', '[\"a5385334-c712-404b-8b50-6ddf869f6432\"]', '2f1a48c8ccff62b6b51da592f9578260', 'DH52200874.jpg', 'trained', '2026-08-18 23:01:08', '2026-08-18 23:01:11'),
(142, 67, 'sinhvien/DH52200881/DH52200881_6a84e437354d0.jpg', '[\"40002792-3032-4b6e-8ac0-9885a9d7fd84\"]', '722b306bf06064f9e69cbfd302431bfe', 'DH52200881.jpg', 'trained', '2026-08-18 23:01:11', '2026-08-18 23:01:13'),
(143, 68, 'sinhvien/DH52200913/DH52200913_6a84e439e2107.jpg', '[\"5bda3e82-dfc5-4f2f-a1f2-403d56d6e14d\"]', 'e1526e00333ce415959d6bf0ed728b48', 'DH52200913.jpg', 'trained', '2026-08-18 23:01:13', '2026-08-18 23:01:16'),
(144, 69, 'sinhvien/DH52200928/DH52200928_6a84e43c95378.jpg', '[\"71efcf5b-47f7-4065-8092-628d13b467c0\"]', '19cd445e3dce3e7c1f33d0c85f27bb02', 'DH52200928.jpg', 'trained', '2026-08-18 23:01:16', '2026-08-18 23:01:19'),
(145, 70, 'sinhvien/DH52200939/DH52200939_6a84e43f3602b.jpg', '[\"0fd02059-a2e6-4670-a5d2-af0bb9e6a7b5\"]', '248c6d26f3103e83543ddcb62ce6ca8c', 'DH52200939.jpg', 'trained', '2026-08-18 23:01:19', '2026-08-18 23:01:22'),
(146, 71, 'sinhvien/DH52200960/DH52200960_6a84e442443c9.jpg', '[\"d14259ba-8955-4be1-875d-189a5b5b6a52\"]', '9ce735ee02b34c5a76d141e469c10fa6', 'DH52200960.jpg', 'trained', '2026-08-18 23:01:22', '2026-08-18 23:01:25'),
(147, 25, 'sinhvien/DH52200965/DH52200965_6a84e4453c8e7.jpg', '[\"7ae0963e-a89b-4704-882b-8964b6130863\"]', 'c0a0fee0068f36d9ec1d8c6df166788a', 'DH52200965.jpg', 'trained', '2026-08-18 23:01:25', '2026-08-18 23:01:27'),
(148, 72, 'sinhvien/DH52200978/DH52200978_6a84e447ecdc2.jpg', '[\"af64ea4f-fa3c-4465-89d1-ed2502f5ed8e\"]', 'd3502ba484338ef56a87b68167eda19d', 'DH52200978.jpg', 'trained', '2026-08-18 23:01:27', '2026-08-18 23:01:30'),
(149, 73, 'sinhvien/DH52200999/DH52200999_6a84e44a52cf9.jpg', '[\"77454e19-4dca-42ee-82bc-7af2b63d8e4e\"]', 'fa918a19de4b1e9a156a322f2e3a148c', 'DH52200999.jpg', 'trained', '2026-08-18 23:01:30', '2026-08-18 23:01:32'),
(150, 74, 'sinhvien/DH52201003/DH52201003_6a84e44cbaf12.jpg', '[\"3aa7621e-5fc6-4149-836c-441ae6251b2b\"]', '555fe67c370f9ebb997e5e7b8ee15329', 'DH52201003.jpg', 'trained', '2026-08-18 23:01:32', '2026-08-18 23:01:35'),
(151, 75, 'sinhvien/DH52201006/DH52201006_6a84e44f3fcc8.jpg', '[\"af67dc73-afba-4338-b768-c04b01f0746e\"]', '8ff9ff7d5ed208a0b706fcf35ba25bb9', 'DH52201006.jpg', 'trained', '2026-08-18 23:01:35', '2026-08-18 23:01:37'),
(152, 23, 'sinhvien/DH52201014/DH52201014_6a84e4518daec.jpg', '[\"88519836-c33c-4d32-8bdb-2488747c82a6\"]', 'e955e0eb2d2c3cc0b2cf4ebe2473fbe6', 'DH52201014.jpg', 'trained', '2026-08-18 23:01:37', '2026-08-18 23:01:39'),
(153, 76, 'sinhvien/DH52201026/DH52201026_6a84e453ef5a6.jpg', '[\"da898c1b-b163-427a-9868-23e22cad3f0c\"]', 'c03f843d10103bb14a3b4c8aae1c72bf', 'DH52201026.jpg', 'trained', '2026-08-18 23:01:39', '2026-08-18 23:01:42'),
(154, 77, 'sinhvien/DH52201044/DH52201044_6a84e4564386e.jpg', '[\"2e16095b-ecc2-477d-9926-ccce59791efd\"]', 'c92bfd76fa56097a1f2f191fe93a1377', 'DH52201044.jpg', 'trained', '2026-08-18 23:01:42', '2026-08-18 23:01:44'),
(155, 78, 'sinhvien/DH52201048/DH52201048_6a84e458a6891.jpg', '[\"e14810c3-bf1a-4b51-935b-500102e0e46f\"]', '328d1ec829acc9576cfb04fca7483c69', 'DH52201048.jpg', 'trained', '2026-08-18 23:01:44', '2026-08-18 23:01:46'),
(156, 8, 'sinhvien/DH52201086/DH52201086_6a84e45b115d4.jpg', '[\"350e1580-88c7-4707-bce7-4c8039189e68\"]', '5c913219d05f3b625bf1245cc90be0de', 'DH52201086.jpg', 'trained', '2026-08-18 23:01:47', '2026-08-18 23:01:49'),
(157, 79, 'sinhvien/DH52201052/DH52201052_6a84e45d575f6.jpg', '[\"4a3e7eac-1df2-4119-a1b8-cdc56983deac\"]', '39a2d8f665d5e24cd0697993f5b99773', 'DH52201052.jpg', 'trained', '2026-08-18 23:01:49', '2026-08-18 23:01:51'),
(158, 22, 'sinhvien/DH52201112/DH52201112_6a84e45fc5388.jpg', '[\"bd16429e-64f7-41eb-b351-ee3c06ae3332\"]', 'aec35d0da3f44092d59d92b13359ddd6', 'DH52201112.jpg', 'trained', '2026-08-18 23:01:51', '2026-08-18 23:01:54'),
(159, 9, 'sinhvien/DH52201131/DH52201131_6a84e4624c627.jpg', '[\"db25ecdf-6890-4434-a6e1-b2e25690ed89\"]', 'd1cd81986655d61bb1cb6883bb4c7997', 'DH52201131.jpg', 'trained', '2026-08-18 23:01:54', '2026-08-18 23:01:56'),
(160, 80, 'sinhvien/DH52201127/DH52201127_6a84e464d339e.jpg', '[\"b1df27bf-0085-4c31-89e5-18850da184d4\"]', 'bd1ab5c1b31bde1f8502a00166a6fe13', 'DH52201127.jpg', 'trained', '2026-08-18 23:01:56', '2026-08-18 23:01:58'),
(161, 81, 'sinhvien/DH52201189/DH52201189_6a84e46716b66.jpg', '[\"4cec12eb-82c2-4f98-ab00-8fc97b1c3f53\"]', 'b2d6e8e2ec5af4a7926eeb42ffad9770', 'DH52201189.jpg', 'trained', '2026-08-18 23:01:59', '2026-08-18 23:02:01'),
(162, 82, 'sinhvien/DH52201209/DH52201209_6a84e4696a74e.jpg', '[\"4f458fbc-05a2-4e01-9a0c-9b068b806028\"]', '33ff95f48bff9e79aa6bbd0a4eefd27b', 'DH52201209.jpg', 'trained', '2026-08-18 23:02:01', '2026-08-18 23:02:03'),
(163, 83, 'sinhvien/DH52201321/DH52201321_6a84e46bdd84e.jpg', '[\"e0f28542-5a93-4c37-8bf3-58559ad8de9b\"]', '3959b788d0dc8e89358b1893e9e42760', 'DH52201321.jpg', 'trained', '2026-08-18 23:02:03', '2026-08-18 23:02:06'),
(164, 84, 'sinhvien/DH52201329/DH52201329_6a84e46e3d0d0.jpg', '[\"d092e228-a01e-4c5a-a9d5-eb58efcab7e7\"]', '4a95f512619dbd52cc5bf391a252e284', 'DH52201329.jpg', 'trained', '2026-08-18 23:02:06', '2026-08-18 23:02:08'),
(165, 19, 'sinhvien/DH52201357/DH52201357_6a84e47094534.jpg', '[\"68ebb151-5d5a-44c8-8076-e1c7796cd536\"]', '17f2148f2943f96c8fd78d8a76de62fb', 'DH52201357.jpg', 'trained', '2026-08-18 23:02:08', '2026-08-18 23:02:10'),
(166, 85, 'sinhvien/DH52201355/DH52201355_6a84e473029c2.jpg', '[\"11b327de-33c7-491f-b046-fe99fb9ae6e7\"]', '2a63a516ddbd49249489ae21e34e433a', 'DH52201355.jpg', 'trained', '2026-08-18 23:02:11', '2026-08-18 23:02:13'),
(167, 86, 'sinhvien/DH52201368/DH52201368_6a84e475edc91.jpg', '[\"1abc1092-367e-453f-ae0a-262aa0435b8a\"]', 'e41cc62c7dc21fd4cd50567e94fb3c3e', 'DH52201368.jpg', 'trained', '2026-08-18 23:02:13', '2026-08-18 23:02:16'),
(168, 18, 'sinhvien/DH52201371/DH52201371_6a84e47880a9b.jpg', '[\"66771e5b-b5f9-4286-b732-7ec56fa07e67\"]', '412a956c7c3aa7ab40fa942ec7b3f8e3', 'DH52201371.jpg', 'trained', '2026-08-18 23:02:16', '2026-08-18 23:02:18'),
(169, 87, 'sinhvien/DH52201386/DH52201386_6a84e47a8c5d6.jpg', '[\"d06b92ed-ed3f-4a04-8012-6e2458d03b8e\"]', '06e072b493aa1f984e801a0af9f2f612', 'DH52201386.jpg', 'trained', '2026-08-18 23:02:18', '2026-08-18 23:02:20'),
(170, 20, NULL, NULL, '3d037ddb0e32241fb506d3304ce96209', 'DH52201381.jpg', 'failed', '2026-08-18 23:02:20', '2026-08-18 23:02:23'),
(171, 88, 'sinhvien/DH52201397/DH52201397_6a84e47f45d22.jpg', '[\"d83e14c9-4a6c-4fae-8df5-46b7b73ce526\"]', '5d686b3a0f9d721976cff92a6ac8b004', 'DH52201397.jpg', 'trained', '2026-08-18 23:02:23', '2026-08-18 23:02:25'),
(172, 89, 'sinhvien/DH52201398/DH52201398_6a84e4818afb6.jpg', '[\"3b897e00-a027-4ae1-8ece-1601b53664b0\"]', 'f6b2553eb6ebd66bf5a19516a2b63686', 'DH52201398.jpg', 'trained', '2026-08-18 23:02:25', '2026-08-18 23:02:27'),
(173, 90, 'sinhvien/DH52201413/DH52201413_6a84e483f0d52.jpg', '[\"410d8230-2add-4c72-b988-e1ade7d6d4fc\"]', '0ed5ebacf34d6ce70ecc499aefb76d8d', 'DH52201413.jpg', 'trained', '2026-08-18 23:02:27', '2026-08-18 23:02:30'),
(174, 91, 'sinhvien/DH52201414/DH52201414_6a84e4866f473.jpg', '[\"7b4039d4-214c-4be5-b3a1-8d0769fe0410\"]', 'c1a41118f7d5fdc3a6135564948761b0', 'DH52201414.jpg', 'trained', '2026-08-18 23:02:30', '2026-08-18 23:02:32'),
(175, 92, 'sinhvien/DH52201438/DH52201438_6a84e488bf36c.jpg', '[\"f8fc3999-8b2a-4cd6-bbfd-efff4b569412\"]', '94f1c2b1d097bcea0714532cf4a8ed18', 'DH52201438.jpg', 'trained', '2026-08-18 23:02:32', '2026-08-18 23:02:35'),
(176, 93, 'sinhvien/DH52201470/DH52201470_6a84e48b2335d.jpg', '[\"b39d8d68-e3b6-48fa-9e6b-0446163fffc5\"]', '75a434e8a64dc3252b9863e4881c638d', 'DH52201470.jpg', 'trained', '2026-08-18 23:02:35', '2026-08-18 23:02:37'),
(177, 94, 'sinhvien/DH52201474/DH52201474_6a84e48d7c248.jpg', '[\"06e565cc-ce0f-4e1a-9996-7fe0cd35e005\"]', 'ffa067a93350217d575ea0671cf88e7d', 'DH52201474.jpg', 'trained', '2026-08-18 23:02:37', '2026-08-18 23:02:39'),
(178, 95, 'sinhvien/DH52201488/DH52201488_6a84e48fd1a7c.jpg', '[\"8ff1bab5-85fe-4a6b-85b4-29529c6ae517\"]', '8066c8523d71e1d75395644855d8bb65', 'DH52201488.jpg', 'trained', '2026-08-18 23:02:39', '2026-08-18 23:02:42'),
(179, 96, 'sinhvien/DH52201510/DH52201510_6a84e492395cf.jpg', '[\"b0f5cf1d-0b66-40b3-9e27-e21033c42feb\"]', '0e968827cf6891109391e33cf9ee18ef', 'DH52201510.jpg', 'trained', '2026-08-18 23:02:42', '2026-08-18 23:02:44'),
(180, 7, NULL, NULL, 'eb90de0ab8469c86f948764edea06d2f', 'DH52201535.jpg', 'failed', '2026-08-18 23:02:44', '2026-08-18 23:02:46'),
(181, 97, 'sinhvien/DH52201565/DH52201565_6a84e496b2f7c.jpg', '[\"50ea8743-9503-4a2f-8200-2f89c56c2d3a\"]', '1d22874b5de5adaaa725f64efe52f8e0', 'DH52201565.jpg', 'trained', '2026-08-18 23:02:46', '2026-08-18 23:02:48'),
(182, 98, 'sinhvien/DH52201641/DH52201641_6a84e4990f309.jpg', '[\"ce0a7495-d67c-442f-966b-94a64c33f5e4\"]', 'fd24072c035c38c343e110b5883ff6a1', 'DH52201641.jpg', 'trained', '2026-08-18 23:02:49', '2026-08-18 23:02:51'),
(183, 99, 'sinhvien/DH52201740/DH52201740_6a84e49b600c7.jpg', '[\"4a9f10d3-0ba1-40fc-b283-fba5c8ea140c\"]', '22d1f651604c53a37da030eb81471d38', 'DH52201740.jpg', 'trained', '2026-08-18 23:02:51', '2026-08-18 23:02:53'),
(184, 100, 'sinhvien/DH52201743/DH52201743_6a84e49da1de8.jpg', '[\"b0fcff5e-2e14-4649-8bd2-daebf56a7505\"]', 'c431212d1cd217235d1cf77beaa83486', 'DH52201743.jpg', 'trained', '2026-08-18 23:02:53', '2026-08-18 23:02:56'),
(185, 107, 'sinhvien/DH52201569/DH52201569_6a84e536d3ba3.jpg', '[\"d59f1235-8c9c-47f6-bccf-71caac10070a\"]', '2294bcd1d89dcd402e7536980841f630', 'DH52201569_2.jpg', 'trained', '2026-08-18 23:05:26', '2026-08-18 23:05:29'),
(186, 107, 'sinhvien/DH52201569/DH52201569_6a84e539be6a1.jpg', '[\"945b8f1d-bae1-484e-af43-f0d312e1dc80\"]', '6b3e341952941592fd3bbfa35085cdad', 'DH52201569_1.jpg', 'trained', '2026-08-18 23:05:29', '2026-08-18 23:05:32'),
(187, 105, 'sinhvien/DH52104533/DH52104533_6a84e53c9fc4b.jpg', '[\"706d4ea1-bd27-411a-9bed-57cb57a86d90\"]', 'edcdfd334d8aaf9ff1f0b8aa7e89f91c', 'DH52104533_2.jpg', 'trained', '2026-08-18 23:05:32', '2026-08-18 23:05:35'),
(188, 104, 'sinhvien/DH52100514/DH52100514_6a84e53fcc437.jpg', '[\"6e246b31-676b-4421-b178-6fc914af3143\"]', 'c6e3cff8934f9effb4a10a9d3687bcd1', 'DH52100514.jpg', 'trained', '2026-08-18 23:05:35', '2026-08-18 23:05:37'),
(189, 105, 'sinhvien/DH52104533/DH52104533_6a84e541e710b.jpg', '[\"009039a3-0781-4d23-b3ce-85bfadaf711e\"]', 'a2bbb6c528395008221f91705cc1d44c', 'DH52104533_1.jpg', 'trained', '2026-08-18 23:05:37', '2026-08-18 23:05:40'),
(190, 73, 'sinhvien/DH52200999/DH52200999_6a84e544e2e64.jpg', '[\"a6952777-fd3c-48d9-b4ac-414f24915a8f\"]', 'dae70a3f136a3331c7611ff6008b8a08', 'DH52200999.jpg', 'trained', '2026-08-18 23:05:40', '2026-08-18 23:05:43'),
(191, 106, 'sinhvien/DH52200392/DH52200392_6a84e547dc16d.jpg', '[\"eb030f34-bc11-4e88-b38d-fa8ee8c32c17\"]', '003a74486ee84e51f0ab34222063ee68', 'DH52200392.JPG', 'trained', '2026-08-18 23:05:43', '2026-08-18 23:05:46'),
(193, 18, 'sinhvien/DH52201371/DH52201371_6a84e60bafce3.jpg', '[\"a9198b03-cc8d-45bd-a024-c76331d47918\"]', '945ac620a52a0a1ef7ad38e75d5e0f27', 'dh52201371.png', 'trained', '2026-08-18 23:08:59', '2026-08-18 23:09:02');

-- --------------------------------------------------------

--
-- Table structure for table `diem_danhs`
--

DROP TABLE IF EXISTS `diem_danhs`;
CREATE TABLE IF NOT EXISTS `diem_danhs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `lich_thi_id` bigint UNSIGNED NOT NULL,
  `sinh_vien_id` bigint UNSIGNED NOT NULL,
  `ket_qua` enum('hợp lệ','Vắng mặt') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `do_chinh_xac` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `thoi_gian_dd` datetime DEFAULT NULL,
  `hinh_thuc_dd` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ghi_chu` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `diem_danhs_lich_thi_id_sinh_vien_id_unique` (`lich_thi_id`,`sinh_vien_id`),
  KEY `fk_diemdanh_sinhvien` (`sinh_vien_id`)
) ENGINE=InnoDB AUTO_INCREMENT=527 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `diem_danhs`
--

INSERT INTO `diem_danhs` (`id`, `lich_thi_id`, `sinh_vien_id`, `ket_qua`, `do_chinh_xac`, `thoi_gian_dd`, `hinh_thuc_dd`, `ghi_chu`, `created_at`, `updated_at`) VALUES
(36, 20, 19, 'hợp lệ', '99.907936096191', '2025-11-22 07:26:17', 'Camera', NULL, '2025-11-21 23:36:47', '2025-11-22 00:26:17'),
(53, 32, 18, 'Vắng mặt', NULL, NULL, NULL, NULL, '2025-11-26 03:46:36', '2025-12-03 03:15:57'),
(54, 32, 19, 'Vắng mặt', NULL, NULL, NULL, NULL, '2025-11-26 03:46:36', '2025-12-03 03:15:57'),
(55, 32, 20, 'Vắng mặt', NULL, NULL, NULL, NULL, '2025-11-26 03:46:36', '2025-12-03 03:15:57'),
(56, 32, 22, 'Vắng mặt', NULL, NULL, NULL, NULL, '2025-11-26 03:46:36', '2025-12-03 03:15:57'),
(57, 32, 23, 'Vắng mặt', NULL, NULL, NULL, NULL, '2025-11-26 03:46:36', '2025-12-03 03:15:57'),
(58, 33, 18, 'Vắng mặt', NULL, NULL, NULL, NULL, '2025-11-26 03:51:45', '2025-12-03 03:15:57'),
(59, 33, 19, 'Vắng mặt', NULL, NULL, NULL, NULL, '2025-11-26 03:51:45', '2025-12-03 03:15:57'),
(60, 33, 20, 'hợp lệ', '99.999465942383', '2025-11-26 11:02:30', 'Camera', NULL, '2025-11-26 03:51:45', '2025-11-26 04:02:30'),
(87, 53, 25, 'Vắng mặt', NULL, NULL, NULL, NULL, '2026-03-18 12:06:59', '2026-03-18 14:03:27'),
(88, 53, 18, 'Vắng mặt', NULL, NULL, NULL, NULL, '2026-03-18 12:06:59', '2026-03-18 14:03:27'),
(89, 54, 18, 'hợp lệ', '99.749687194824', '2026-06-06 17:04:18', 'Camera', NULL, '2026-06-06 09:56:41', '2026-06-06 10:04:18'),
(240, 60, 25, 'Vắng mặt', NULL, NULL, NULL, NULL, '2026-07-06 22:56:14', '2026-07-07 01:50:56'),
(241, 60, 18, 'hợp lệ', '94.110473632812', '2026-07-07 06:38:00', 'Camera', NULL, '2026-07-06 22:56:14', '2026-07-06 23:38:00'),
(242, 61, 104, 'Vắng mặt', NULL, NULL, NULL, NULL, '2026-07-08 03:26:01', '2026-07-08 04:30:27'),
(243, 61, 105, 'Vắng mặt', NULL, NULL, NULL, NULL, '2026-07-08 03:26:01', '2026-07-08 04:30:27'),
(244, 61, 106, 'Vắng mặt', NULL, NULL, NULL, NULL, '2026-07-08 03:26:01', '2026-07-08 04:30:27'),
(245, 61, 73, 'hợp lệ', '100', '2026-07-08 11:23:55', 'Thủ công', NULL, '2026-07-08 03:26:01', '2026-07-08 04:23:55'),
(246, 61, 18, 'hợp lệ', '85.32844543457', '2026-07-08 11:20:38', 'Camera', NULL, '2026-07-08 03:26:01', '2026-07-08 04:20:38'),
(247, 61, 107, 'Vắng mặt', NULL, NULL, NULL, NULL, '2026-07-08 03:26:01', '2026-07-08 04:30:27'),
(248, 62, 73, 'hợp lệ', '100', '2026-07-08 11:42:15', 'Thủ công', NULL, '2026-07-08 04:34:05', '2026-07-08 04:42:15'),
(250, 72, 18, 'hợp lệ', '99.890983581543', '2026-07-17 07:33:12', 'Camera', NULL, '2026-07-16 23:36:14', '2026-07-17 00:33:12'),
(251, 72, 25, 'Vắng mặt', NULL, NULL, NULL, NULL, '2026-07-16 23:36:26', '2026-07-17 01:50:10'),
(252, 74, 25, 'Vắng mặt', NULL, NULL, NULL, NULL, '2026-07-23 19:44:32', '2026-07-23 20:46:45'),
(253, 74, 18, 'hợp lệ', '100', '2026-07-24 03:33:34', 'Thủ công', NULL, '2026-07-23 19:44:32', '2026-07-23 20:33:34'),
(477, 78, 104, 'Vắng mặt', NULL, NULL, NULL, NULL, '2026-08-03 04:02:38', '2026-08-18 22:51:29'),
(479, 78, 106, 'Vắng mặt', NULL, NULL, NULL, NULL, '2026-08-03 04:02:38', '2026-08-18 22:51:29'),
(480, 78, 73, 'Vắng mặt', NULL, NULL, NULL, NULL, '2026-08-03 04:02:38', '2026-08-18 22:51:29'),
(481, 78, 18, 'Vắng mặt', NULL, NULL, NULL, NULL, '2026-08-03 04:02:38', '2026-08-18 22:51:29'),
(482, 78, 107, 'Vắng mặt', NULL, NULL, NULL, NULL, '2026-08-03 04:02:38', '2026-08-18 22:51:29'),
(483, 79, 25, 'Vắng mặt', NULL, NULL, NULL, NULL, '2026-08-03 04:04:11', '2026-08-18 22:51:29'),
(484, 79, 22, 'Vắng mặt', NULL, NULL, NULL, NULL, '2026-08-03 04:04:11', '2026-08-18 22:51:29'),
(485, 79, 18, 'Vắng mặt', NULL, NULL, NULL, NULL, '2026-08-03 04:04:11', '2026-08-18 22:51:29'),
(486, 80, 25, 'Vắng mặt', NULL, NULL, NULL, NULL, '2026-08-03 04:26:34', '2026-08-03 22:05:19'),
(487, 80, 22, 'hợp lệ', '100', '2026-08-03 11:41:13', 'Thủ công', NULL, '2026-08-03 04:26:34', '2026-08-03 04:41:13'),
(488, 80, 18, 'hợp lệ', '99.424667358398', '2026-08-03 11:42:52', 'Camera', NULL, '2026-08-03 04:26:34', '2026-08-03 04:42:52'),
(489, 81, 25, 'hợp lệ', '100', '2026-08-04 05:19:18', 'Thủ công', NULL, '2026-08-03 22:07:07', '2026-08-03 22:19:18'),
(490, 81, 22, 'hợp lệ', '100', '2026-08-04 05:19:17', 'Thủ công', NULL, '2026-08-03 22:07:07', '2026-08-03 22:19:17'),
(491, 81, 18, 'hợp lệ', '99.854187011719', '2026-08-04 05:18:59', 'Camera', NULL, '2026-08-03 22:07:07', '2026-08-03 22:18:59'),
(492, 82, 25, 'Vắng mặt', NULL, NULL, NULL, NULL, '2026-08-04 01:32:50', '2026-08-04 02:59:58'),
(493, 82, 22, 'Vắng mặt', NULL, NULL, NULL, NULL, '2026-08-04 01:32:50', '2026-08-04 02:59:58'),
(494, 82, 18, 'Vắng mặt', NULL, NULL, NULL, NULL, '2026-08-04 01:32:50', '2026-08-04 02:59:58'),
(495, 83, 73, 'Vắng mặt', NULL, NULL, NULL, NULL, '2026-08-04 01:34:17', '2026-08-04 02:59:55'),
(496, 83, 104, 'Vắng mặt', NULL, NULL, NULL, NULL, '2026-08-04 01:34:17', '2026-08-04 02:59:55'),
(497, 83, 105, 'Vắng mặt', NULL, NULL, NULL, NULL, '2026-08-04 01:34:17', '2026-08-04 02:59:55'),
(498, 83, 106, 'Vắng mặt', NULL, NULL, NULL, NULL, '2026-08-04 01:34:17', '2026-08-04 02:59:55'),
(499, 83, 107, 'Vắng mặt', NULL, NULL, NULL, NULL, '2026-08-04 01:34:17', '2026-08-04 02:59:55'),
(500, 84, 18, 'Vắng mặt', NULL, NULL, NULL, NULL, '2026-08-04 03:01:52', '2026-08-18 22:51:29'),
(501, 84, 22, 'Vắng mặt', NULL, NULL, NULL, NULL, '2026-08-04 03:01:52', '2026-08-18 22:51:29'),
(502, 84, 73, 'Vắng mặt', NULL, NULL, NULL, NULL, '2026-08-04 03:01:52', '2026-08-18 22:51:29'),
(503, 84, 104, 'Vắng mặt', NULL, NULL, NULL, NULL, '2026-08-04 03:01:52', '2026-08-18 22:51:29'),
(504, 84, 105, 'Vắng mặt', NULL, NULL, NULL, NULL, '2026-08-04 03:01:52', '2026-08-18 22:51:29'),
(505, 84, 106, 'Vắng mặt', NULL, NULL, NULL, NULL, '2026-08-04 03:01:52', '2026-08-18 22:51:29'),
(506, 84, 107, 'Vắng mặt', NULL, NULL, NULL, NULL, '2026-08-04 03:01:52', '2026-08-18 22:51:29'),
(507, 85, 18, 'hợp lệ', '99.882522583008', '2026-08-19 06:26:04', 'Camera', NULL, '2026-08-18 22:56:59', '2026-08-18 23:26:04'),
(508, 85, 20, 'hợp lệ', '99.212342123456', '2026-08-19 06:17:23', 'Camera', NULL, '2026-08-18 22:56:59', '2026-08-18 22:56:59'),
(509, 85, 87, 'hợp lệ', '98.987657423456', '2026-08-19 06:17:45', 'Camera', NULL, '2026-08-18 22:56:59', '2026-08-18 22:56:59'),
(510, 85, 88, NULL, NULL, NULL, NULL, NULL, '2026-08-18 22:56:59', '2026-08-18 22:56:59'),
(511, 85, 89, 'hợp lệ', '97.989985321343', '2026-08-19 06:18:13', 'Camera', NULL, '2026-08-18 22:56:59', '2026-08-18 22:56:59'),
(512, 85, 90, 'hợp lệ', '100', '2026-08-19 06:11:37', 'Thủ công', 'mat_khong_khop', '2026-08-18 22:56:59', '2026-08-18 23:11:41'),
(513, 85, 91, 'hợp lệ', '99.123876547543', '2026-08-19 06:18:36', 'Camera', NULL, '2026-08-18 22:56:59', '2026-08-18 22:56:59'),
(514, 85, 92, 'hợp lệ', '99.897867567888', '2026-08-19 06:19:04', 'Camera', NULL, '2026-08-18 22:56:59', '2026-08-18 23:11:34'),
(515, 85, 93, 'hợp lệ', '99.878763746564', '2026-08-19 06:21:09', 'Camera', NULL, '2026-08-18 22:56:59', '2026-08-18 22:56:59'),
(516, 85, 94, 'hợp lệ', '99.122334542212', '2026-08-19 06:22:11', 'Camera', NULL, '2026-08-18 22:56:59', '2026-08-18 22:56:59'),
(517, 85, 95, 'hợp lệ', '99.898767676543', '2026-08-19 06:20:53', 'Camera', NULL, '2026-08-18 22:56:59', '2026-08-18 22:56:59'),
(518, 85, 96, 'hợp lệ', '99.898675987787', '2026-08-19 06:19:55', 'Camera', NULL, '2026-08-18 22:56:59', '2026-08-18 22:56:59'),
(519, 85, 97, 'hợp lệ', '99.645321234212', '2026-08-19 06:21:41', 'Camera', NULL, '2026-08-18 22:56:59', '2026-08-18 22:56:59'),
(520, 85, 98, 'hợp lệ', '99.898546773885', '2026-08-19 06:19:34', 'Camera', NULL, '2026-08-18 22:56:59', '2026-08-18 22:56:59'),
(521, 85, 99, 'hợp lệ', '100', '2026-08-19 06:10:32', 'Thủ công', 'mat_khong_khop', '2026-08-18 22:56:59', '2026-08-18 23:11:51'),
(522, 85, 100, NULL, NULL, NULL, NULL, NULL, '2026-08-18 22:56:59', '2026-08-18 22:56:59'),
(523, 85, 25, 'hợp lệ', '99.827321234765', '2026-08-19 06:16:22', 'Camera', NULL, '2026-08-18 22:57:10', '2026-08-18 22:57:10'),
(524, 85, 85, 'hợp lệ', '99.123543672134', '2026-08-19 06:15:09', 'Camera', NULL, '2026-08-18 22:57:27', '2026-08-18 22:57:27'),
(525, 85, 19, 'hợp lệ', '99.123543672839', '2026-08-19 06:15:07', 'Camera', NULL, '2026-08-18 22:57:27', '2026-08-18 22:57:27'),
(526, 85, 86, 'hợp lệ', '98.263521523123', '2026-08-19 06:13:05', 'Camera', NULL, '2026-08-18 22:57:27', '2026-08-18 23:12:13');

-- --------------------------------------------------------

--
-- Table structure for table `giang_viens`
--

DROP TABLE IF EXISTS `giang_viens`;
CREATE TABLE IF NOT EXISTS `giang_viens` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `ma_gv` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ho_ten` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `vai_tro` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Giảng viên',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `giang_viens_ma_gv_unique` (`ma_gv`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `giang_viens`
--

INSERT INTO `giang_viens` (`id`, `ma_gv`, `ho_ten`, `email`, `password`, `vai_tro`, `is_active`, `created_at`, `updated_at`) VALUES
(13, '1', 'Trần Văn Hùng', 'hung.tranvan@stu.edu.vn', '$2y$12$e5WsO2Z2NN9rJST10c5mkuj7BKv3qRhCWkIm7bLMQWS360UoRO0b2', 'admin', 1, '2025-11-15 10:23:51', '2026-07-16 16:49:18'),
(15, '3', 'Giang_vien', 'bb@gmail.com', '$2y$12$q.qeR6t7VVHZwS3ehhaCdejHH/ToszMocsE/SLwnlYEICJ8y1l22G', 'giang_vien', 1, '2025-11-15 11:06:17', '2026-07-19 22:14:36'),
(17, '2', 'Nguyễn Hùng Thanh Sơn', 'test@gmail.com', '$2y$12$xzchUfVfuXjeDhtASkVOA.VgqQqiyq15Hsr56i0MxQUoO0PIJ5pYq', 'admin', 1, '2026-06-06 10:40:01', '2026-07-19 22:12:48'),
(18, '4', 'Thanh Sơn', 'ntth19061998@gmail.com', '$2y$12$zLNQKyET2RAwXJew7D3I4uEyFXc3kYG5Vti/592THxC0WdPz/.kZ.', 'giang_vien', 1, '2026-07-19 22:13:21', '2026-08-04 01:30:36');

-- --------------------------------------------------------

--
-- Table structure for table `lich_this`
--

DROP TABLE IF EXISTS `lich_this`;
CREATE TABLE IF NOT EXISTS `lich_this` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `mon_hoc_id` bigint UNSIGNED NOT NULL,
  `ngay_thi` date NOT NULL,
  `gio_thi` time NOT NULL,
  `thoi_luong_thi` int NOT NULL DEFAULT '60',
  `phong` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ky_thi` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nam_hoc` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `trang_thai` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'chua_dien_ra',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_lichthi_monhoc` (`mon_hoc_id`)
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lich_this`
--

INSERT INTO `lich_this` (`id`, `mon_hoc_id`, `ngay_thi`, `gio_thi`, `thoi_luong_thi`, `phong`, `ky_thi`, `nam_hoc`, `trang_thai`, `created_at`, `updated_at`) VALUES
(20, 1, '2026-06-02', '06:37:00', 60, 'C706', 'Thi giữa kỳ HK1', '2025-2026', 'da_ket_thuc', '2025-11-21 23:36:31', '2025-11-22 00:43:06'),
(32, 2, '2026-06-03', '12:05:00', 60, 'C705', 'Thi cuối kỳ HK1', '2025-2026', 'da_ket_thuc', '2025-11-26 03:35:34', '2025-12-03 03:15:57'),
(33, 1, '2026-06-08', '10:53:00', 60, 'C705', 'Thi cuối kỳ HK1', '2025-2026', 'da_ket_thuc', '2025-11-26 03:51:19', '2025-12-03 03:15:57'),
(53, 2, '2026-06-15', '19:08:00', 60, 'C705', 'Thi giữa kỳ HK2', '2025-2026', 'da_ket_thuc', '2026-03-18 12:06:27', '2026-03-18 14:03:27'),
(54, 4, '2026-06-16', '17:02:00', 60, 'C705', 'Thi cuối kỳ HK2', '2025-2026', 'da_ket_thuc', '2026-06-06 09:56:32', '2026-06-06 11:05:46'),
(60, 4, '2026-07-07', '05:57:00', 60, 'C706', 'Thi cuối kỳ HK3', '2025-2026', 'da_ket_thuc', '2026-07-06 22:56:02', '2026-07-07 01:50:56'),
(61, 1, '2026-07-08', '10:30:00', 60, 'C705', 'Thi cuối kỳ HK3', '2025-2026', 'da_ket_thuc', '2026-07-08 03:25:25', '2026-07-08 04:30:27'),
(62, 2, '2026-07-08', '11:34:00', 60, 'C706', 'Thi cuối kỳ HK3', '2025-2026', 'da_ket_thuc', '2026-07-08 04:33:15', '2026-07-14 22:16:30'),
(72, 2, '2026-07-17', '06:38:00', 60, 'C705', 'Thi cuối kỳ HK2', '2025-2026', 'da_ket_thuc', '2026-07-16 23:35:37', '2026-07-17 01:50:10'),
(74, 1, '2026-07-24', '02:45:00', 60, 'C706', 'Thi cuối kỳ HK3', '2025-2026', 'da_ket_thuc', '2026-07-23 19:43:37', '2026-07-23 20:46:45'),
(78, 5, '2026-08-05', '08:00:00', 75, 'C704', 'Thi cuối kỳ HK3', '2025-2026', 'da_ket_thuc', '2026-08-03 03:59:40', '2026-08-18 22:51:29'),
(79, 2, '2026-08-08', '13:00:00', 90, 'C706', 'Thi cuối kỳ HK3', '2025-2026', 'da_ket_thuc', '2026-08-03 04:03:37', '2026-08-18 22:51:29'),
(80, 2, '2026-08-03', '11:30:00', 75, 'C706', 'Thi cuối kỳ HK3', '2025-2026', 'da_ket_thuc', '2026-08-03 04:25:51', '2026-08-03 22:05:19'),
(81, 1, '2026-08-04', '05:10:00', 60, 'C704', 'Thi cuối kỳ HK3', '2025-2026', 'da_ket_thuc', '2026-08-03 22:06:23', '2026-08-04 01:30:07'),
(82, 4, '2026-08-04', '08:35:00', 45, 'C704', 'Thi cuối kỳ HK3', '2025-2026', 'da_ket_thuc', '2026-08-04 01:32:19', '2026-08-04 02:59:58'),
(83, 2, '2026-08-04', '08:35:00', 60, 'C708', 'Thi cuối kỳ HK3', '2025-2026', 'da_ket_thuc', '2026-08-04 01:33:28', '2026-08-04 02:59:55'),
(84, 5, '2026-08-04', '10:10:00', 90, 'C708', 'Thi cuối kỳ HK3', '2025-2026', 'da_ket_thuc', '2026-08-04 03:00:53', '2026-08-18 22:51:29'),
(85, 2, '2026-08-19', '06:00:00', 90, 'C703', 'Thi cuối kỳ HK3', '2025-2026', 'dang_dien_ra', '2026-08-18 22:54:02', '2026-08-18 23:09:09');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2025_10_05_083051_create_sinh_viens_table', 1),
(2, '2025_10_05_083255_create_giang_viens_table', 1),
(29, '2025_11_14_005112_add_hinh_thuc_dd_to_diem_danhs_table', 6),
(24, '2025_10_30_235547_create_mon_hocs_table', 4),
(28, '2025_10_05_083324_create_diem_danhs_table', 5),
(27, '2025_10_05_083313_create_phan_cong_g_v_s_table', 5),
(13, '2025_10_11_154413_add_password_to_giang_viens', 2),
(14, '2025_10_15_132142_add_vai_tro_to_giang_viens_table', 3),
(26, '2025_10_05_083304_create_lich_this_table', 5),
(30, '2025_12_16_034519_add_face_columns_to_sinh_viens_table', 7),
(32, '2025_12_16_040418_add_face_quality_to_sinh_viens', 8),
(33, '2026_07_07_071847_create_anh_train_svs_table', 9),
(34, '2026_07_07_080148_remove_columns_from_sinh_viens_table', 9),
(35, '2026_07_16_220000_add_is_active_to_giang_viens_table', 10),
(36, '2026_07_17_004958_add_thoi_luong_thi_to_lich_this_table', 11),
(37, '2026_08_07_190407_add_file_info_to_anh_train_sv_table', 12),
(38, '2026_08_10_085722_add_ghi_chu_to_diem_danhs_table', 12);

-- --------------------------------------------------------

--
-- Table structure for table `mon_hocs`
--

DROP TABLE IF EXISTS `mon_hocs`;
CREATE TABLE IF NOT EXISTS `mon_hocs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `ma_mon` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ten_mon` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mon_hocs_ma_mon_unique` (`ma_mon`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mon_hocs`
--

INSERT INTO `mon_hocs` (`id`, `ma_mon`, `ten_mon`, `created_at`, `updated_at`) VALUES
(1, 'CS00038', 'Lập trình Web', '2025-10-30 17:31:35', '2025-10-30 17:31:35'),
(2, 'CS00039', 'Lập trình Windows', '2025-11-25 21:37:53', '2025-11-25 21:37:53'),
(4, 'CS00040', 'Mã nguồn mở', '2025-12-17 06:41:37', '2025-12-17 06:41:37'),
(5, 'CS00041', 'Phân tích thiết kế hệ thống', '2026-08-03 03:57:54', '2026-08-03 03:57:54'),
(6, 'CS00042', 'Nhập môn lập trình', '2026-08-03 03:58:41', '2026-08-03 03:58:41');

-- --------------------------------------------------------

--
-- Table structure for table `phan_cong_gvs`
--

DROP TABLE IF EXISTS `phan_cong_gvs`;
CREATE TABLE IF NOT EXISTS `phan_cong_gvs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `lich_thi_id` bigint UNSIGNED NOT NULL,
  `giang_vien_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_unique_phancong` (`lich_thi_id`,`giang_vien_id`),
  KEY `fk_phancong_giangvien` (`giang_vien_id`)
) ENGINE=InnoDB AUTO_INCREMENT=99 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `phan_cong_gvs`
--

INSERT INTO `phan_cong_gvs` (`id`, `lich_thi_id`, `giang_vien_id`, `created_at`, `updated_at`) VALUES
(24, 20, 13, '2025-11-21 23:36:57', '2025-11-21 23:36:57'),
(25, 20, 15, '2025-11-21 23:37:00', '2025-11-21 23:37:00'),
(41, 33, 13, '2025-11-26 03:51:25', '2025-11-26 03:51:25'),
(42, 33, 15, '2025-11-26 03:51:28', '2025-11-26 03:51:28'),
(43, 32, 13, '2025-11-26 04:05:57', '2025-11-26 04:05:57'),
(53, 53, 13, '2026-03-18 12:06:32', '2026-03-18 12:06:32'),
(55, 53, 15, '2026-03-18 12:06:39', '2026-03-18 12:06:39'),
(57, 54, 15, '2026-06-06 09:57:03', '2026-06-06 09:57:03'),
(63, 60, 17, '2026-07-06 22:56:20', '2026-07-06 22:56:20'),
(64, 61, 17, '2026-07-08 03:25:33', '2026-07-08 03:25:33'),
(65, 62, 17, '2026-07-08 04:33:36', '2026-07-08 04:33:36'),
(74, 72, 17, '2026-07-16 23:35:50', '2026-07-16 23:35:50'),
(75, 72, 15, '2026-07-16 23:36:05', '2026-07-16 23:36:05'),
(76, 74, 17, '2026-07-23 19:44:12', '2026-07-23 19:44:12'),
(77, 74, 15, '2026-07-23 19:44:15', '2026-07-23 19:44:15'),
(82, 78, 15, '2026-08-03 04:01:57', '2026-08-03 04:01:57'),
(83, 78, 17, '2026-08-03 04:02:00', '2026-08-03 04:02:00'),
(84, 79, 13, '2026-08-03 04:03:46', '2026-08-03 04:03:46'),
(85, 79, 15, '2026-08-03 04:03:49', '2026-08-03 04:03:49'),
(86, 79, 17, '2026-08-03 04:03:51', '2026-08-03 04:03:51'),
(87, 80, 17, '2026-08-03 04:26:04', '2026-08-03 04:26:04'),
(88, 80, 15, '2026-08-03 04:26:08', '2026-08-03 04:26:08'),
(89, 81, 17, '2026-08-03 22:06:29', '2026-08-03 22:06:29'),
(90, 81, 15, '2026-08-03 22:06:40', '2026-08-03 22:06:40'),
(91, 82, 13, '2026-08-04 01:32:25', '2026-08-04 01:32:25'),
(92, 83, 17, '2026-08-04 01:33:41', '2026-08-04 01:33:41'),
(93, 83, 15, '2026-08-04 01:33:43', '2026-08-04 01:33:43'),
(94, 84, 17, '2026-08-04 03:01:07', '2026-08-04 03:01:07'),
(95, 84, 15, '2026-08-04 03:01:10', '2026-08-04 03:01:10'),
(96, 84, 13, '2026-08-04 03:01:18', '2026-08-04 03:01:18'),
(97, 85, 17, '2026-08-18 22:54:20', '2026-08-18 22:54:20'),
(98, 85, 15, '2026-08-18 22:54:23', '2026-08-18 22:54:23');

-- --------------------------------------------------------

--
-- Table structure for table `sinh_viens`
--

DROP TABLE IF EXISTS `sinh_viens`;
CREATE TABLE IF NOT EXISTS `sinh_viens` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `ma_sv` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ho_ten` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `lop` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `do_chinh_xac_tb` double DEFAULT NULL,
  `so_lan_nhan_dien` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sinh_viens_ma_sv_unique` (`ma_sv`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sinh_viens`
--

INSERT INTO `sinh_viens` (`id`, `ma_sv`, `ho_ten`, `lop`, `email`, `do_chinh_xac_tb`, `so_lan_nhan_dien`, `created_at`, `updated_at`) VALUES
(7, 'DH52201535', 'Nguyễn Thành Thuận', 'D22_TH10', 'dh52201535@student.stu.edu.vn', NULL, 0, '2025-10-05 02:14:17', '2025-12-16 02:07:19'),
(8, 'DH52201086', 'Bùi Ngọc Kim Ngân', 'D22_TH09', 'dh52201086@student.stu.edu.vn', NULL, 0, '2025-10-05 02:14:17', '2025-12-15 23:35:21'),
(9, 'DH52201131', 'Phạm Phong Nhã', 'D22_TH09', 'dh52201131@student.stu.edu.vn', NULL, 0, '2025-10-05 02:14:17', '2025-12-15 23:35:19'),
(18, 'DH52201371', 'Nguyễn Hùng Thanh Sơn', 'D22_TH09', 'dh52201371@student.stu.edu.vn', 99.82, 6, '2025-10-08 09:00:59', '2026-08-18 23:26:04'),
(19, 'DH52201357', 'Ngô Hoàng Sang', 'D22_TH09', 'dh52201357@student.stu.edu.vn', NULL, 0, '2025-10-08 09:00:59', '2025-12-15 23:35:23'),
(20, 'DH52201381', 'Lê Nhân Tài', 'D22_TH09', 'dh52201381@student.stu.edu.vn', NULL, 0, '2025-10-08 09:00:59', '2025-12-15 23:30:58'),
(22, 'DH52201112', 'Đoàn Lê Hoàng Nguyên', 'D22_TH10', 'dh52201112@student.stu.edu.vn', NULL, 0, '2025-10-08 09:53:23', '2025-12-20 23:56:21'),
(23, 'DH52201014', 'Đỗ Thành Long', 'D22_TH10', 'dh52201014@student.stu.edu.vn', NULL, 0, '2025-10-18 04:40:22', '2025-12-16 02:06:18'),
(24, 'DH52200346', 'Ngô Xuân Bắc', 'D22_TH09', 'dh52200346@student.stu.edu.vn', NULL, 0, '2025-10-21 09:26:24', '2025-12-15 23:35:16'),
(25, 'DH52200965', 'Huỳnh Nhật Ký', 'D22_TH09', 'dh52200965@student.stu.edu.vn', NULL, 0, '2025-12-08 18:23:56', '2025-12-15 23:30:07'),
(34, 'DH52200335', 'Phạm Đức Anh', 'D22_TH09', 'dh52200335@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2026-01-15 12:35:06'),
(35, 'DH52200343', 'Võ Minh Anh', 'D22_TH09', 'dh52200343@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:04:50'),
(36, 'DH52200353', 'Đỗ Gia Bảo', 'D22_TH09', 'dh52200353@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:04:52'),
(37, 'DH52200363', 'Ngô Gia Bảo', 'D22_TH09', 'dh52200363@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:04:55'),
(38, 'DH52200370', 'Nguyễn Lê Gia Bảo', 'D22_TH09', 'dh52200370@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:04:59'),
(39, 'DH52200386', 'Đặng Duy Bình', 'D22_TH09', 'dh52200386@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:04:57'),
(40, 'DH52200402', 'Đoàn Văn Cần', 'D22_TH09', 'dh52200402@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:05:02'),
(41, 'DH52200417', 'Nguyễn Hồng Cơ', 'D22_TH09', 'dh52200417@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:05:04'),
(42, 'DH52200418', 'Nguyễn Thành Công', 'D22_TH09', 'dh52200418@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:05:09'),
(43, 'DH52200476', 'Nguyễn Công Đạt', 'D22_TH09', 'dh52200476@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:05:06'),
(44, 'DH52200514', 'Phan Võ Minh Đồng', 'D22_TH09', 'dh52200514@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:05:11'),
(45, 'DH52200516', 'Đoàn Tiến Đức', 'D22_TH09', 'dh52200516@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:05:16'),
(46, 'DH52200568', 'Nguyễn Khánh Duy', 'D22_TH09', 'dh52200568@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2026-01-15 12:58:51'),
(47, 'DH52200577', 'Nguyễn Tuấn Duy', 'D22_TH09', 'dh52200577@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:05:18'),
(48, 'DH52200589', 'Trần Khương Duy', 'D22_TH09', 'dh52200589@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:05:21'),
(49, 'DH52200594', 'Đoàn Trần Ngọc Duyên', 'D22_TH09', 'dh52200594@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:05:23'),
(50, 'DH52200605', 'Hoàng Văn Giáp', 'D22_TH09', 'dh52200605@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:05:28'),
(51, 'DH52200606', 'Lê Nguyên Giáp', 'D22_TH09', 'dh52200606@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:05:25'),
(52, 'DH52200613', 'Nguyễn Thị Thu Hà', 'D22_TH09', 'dh52200613@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:05:30'),
(53, 'DH52200638', 'Nguyễn Trường Trí Hào', 'D22_TH09', 'dh52200638@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:05:32'),
(54, 'DH52200649', 'Ngô Công Hậu', 'D22_TH09', 'dh52200649@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:05:34'),
(55, 'DH52200688', 'Phạm Văn Hiếu', 'D22_TH09', 'dh52200688@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:05:39'),
(56, 'DH52200704', 'Lê Cao Việt Hoàng', 'D22_TH09', 'dh52200704@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:05:37'),
(57, 'DH52200734', 'Trần Minh Hùng', 'D22_TH09', 'dh52200734@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:05:42'),
(58, 'DH52200737', 'Nguyễn Hoàng Hưng', 'D22_TH09', 'dh52200737@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:05:44'),
(59, 'DH52200746', 'Trần Huy Khải Hưng', 'D22_TH09', 'dh52200746@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:05:46'),
(60, 'DH52200764', 'Đoàn Hoàng Huy', 'D22_TH09', 'dh52200764@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:05:49'),
(61, 'DH52200781', 'Nguyễn Ngọc Huy', 'D22_TH09', 'dh52200781@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:05:51'),
(62, 'DH52200795', 'Phạm Nguyễn Nhật Huy', 'D22_TH09', 'dh52200795@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:05:53'),
(63, 'DH52200837', 'Lê Duy Khang', 'D22_TH09', 'dh52200837@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:05:56'),
(64, 'DH52200856', 'Vũ Đình Khang', 'D22_TH09', 'dh52200856@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:05:58'),
(65, 'DH52200873', 'Nguyễn Đào Minh Khánh', 'D22_TH09', 'dh52200873@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:06:00'),
(66, 'DH52200874', 'Nguyễn Duy Khánh', 'D22_TH09', 'dh52200874@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:06:02'),
(67, 'DH52200881', 'Nguyễn Xuân Khánh', 'D22_TH09', 'dh52200881@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:06:05'),
(68, 'DH52200913', 'Nguyễn Minh Khoa', 'D22_TH09', 'dh52200913@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:06:07'),
(69, 'DH52200928', 'Nguyễn Đăng Khôi', 'D22_TH09', 'dh52200928@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:06:09'),
(70, 'DH52200939', 'Nguyễn Hữu Kiên', 'D22_TH09', 'dh52200939@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:06:11'),
(71, 'DH52200960', 'Võ Gia Kiệt', 'D22_TH09', 'dh52200960@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:06:14'),
(72, 'DH52200978', 'Phan Công Lập', 'D22_TH09', 'dh52200978@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:06:16'),
(73, 'DH52200999', 'Nguyễn Hữu Lộc', 'D22_TH09', 'dh52200999@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:06:21'),
(74, 'DH52201003', 'Phạm Tấn Lộc', 'D22_TH09', 'dh52201003@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:06:23'),
(75, 'DH52201006', 'Trần Trung Lộc', 'D22_TH09', 'dh52201006@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:06:25'),
(76, 'DH52201026', 'Phan Thành Long', 'D22_TH09', 'dh52201026@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:06:28'),
(77, 'DH52201044', 'Hồ Minh Mẫn', 'D22_TH09', 'dh52201044@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:06:35'),
(78, 'DH52201048', 'Nguyễn Tuấn Mạnh', 'D22_TH09', 'dh52201048@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:06:30'),
(79, 'DH52201052', 'Hồ Sỹ Minh', 'D22_TH09', 'dh52201052@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:06:32'),
(80, 'DH52201127', 'Trương Nhã Nguyên', 'D22_TH09', 'dh52201127@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:28', '2025-12-16 02:06:40'),
(81, 'DH52201189', 'Nguyễn Lê Tiến Phát', 'D22_TH09', 'dh52201189@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:29', '2025-12-16 02:06:44'),
(82, 'DH52201209', 'Lê Ngọc Phong', 'D22_TH09', 'dh52201209@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:29', '2025-12-16 02:06:42'),
(83, 'DH52201321', 'Nguyễn Anh Quốc', 'D22_TH09', 'dh52201321@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:29', '2025-12-16 02:06:49'),
(84, 'DH52201329', 'Mai Anh Quý', 'D22_TH09', 'dh52201329@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:29', '2025-12-16 02:06:46'),
(85, 'DH52201355', 'Huỳnh Minh Sang', 'D22_TH09', 'dh52201355@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:29', '2025-12-16 02:06:51'),
(86, 'DH52201368', 'Lý Quốc Sơn', 'D22_TH09', 'dh52201368@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:29', '2025-12-16 02:06:54'),
(87, 'DH52201386', 'Nguyễn Đức Tài', 'D22_TH09', 'dh52201386@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:29', '2025-12-16 02:06:56'),
(88, 'DH52201397', 'Võ Văn Tài', 'D22_TH09', 'dh52201397@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:29', '2025-12-16 02:06:58'),
(89, 'DH52201398', 'Vương Thái Tài', 'D22_TH09', 'dh52201398@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:29', '2025-12-16 02:07:01'),
(90, 'DH52201413', 'Đặng Mạnh Tấn', 'D22_TH09', 'dh52201413@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:29', '2025-12-16 02:07:03'),
(91, 'DH52201414', 'Nguyễn Công Tấn', 'D22_TH09', 'dh52201414@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:29', '2025-12-16 02:07:05'),
(92, 'DH52201438', 'Nguyễn Quang Thắng', 'D22_TH09', 'dh52201438@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:29', '2025-12-16 02:07:08'),
(93, 'DH52201470', 'Võ Thị Xuân Thao', 'D22_TH09', 'dh52201470@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:29', '2025-12-16 02:07:10'),
(94, 'DH52201474', 'Nguyễn ái Phương Thảo', 'D22_TH09', 'dh52201474@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:29', '2025-12-16 02:07:12'),
(95, 'DH52201488', 'Trần Quang Thiện', 'D22_TH09', 'dh52201488@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:29', '2025-12-16 02:07:15'),
(96, 'DH52201510', 'Võ Thị Kiều Thơ', 'D22_TH09', 'dh52201510@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:29', '2025-12-16 02:07:17'),
(97, 'DH52201565', 'Bùi Tấn Tín', 'D22_TH09', 'dh52201565@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:29', '2025-12-16 02:07:22'),
(98, 'DH52201641', 'Nguyễn Minh Triết', 'D22_TH09', 'dh52201641@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:29', '2025-12-16 02:07:24'),
(99, 'DH52201740', 'Nguyễn Minh Tuyến', 'D22_TH09', 'dh52201740@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:29', '2025-12-16 02:07:26'),
(100, 'DH52201743', 'Nguyễn Thị Hoàng Uyên', 'D22_TH09', 'dh52201743@student.stu.edu.vn', NULL, 0, '2025-12-16 01:20:29', '2025-12-16 02:07:29'),
(101, 'DH52110555', 'Ngô Tuấn Anh', 'D21_TH13', 'dh52110555@student.stu.edu.vn', NULL, 0, '2026-01-15 12:42:14', '2026-06-06 09:45:26'),
(104, 'DH52100514', 'Trần Quốc Nam', 'D22_TH04', 'dh52100514@student.stu.edu.vn', NULL, 0, '2026-07-08 03:02:51', '2026-07-08 03:03:58'),
(105, 'DH52104533', 'Võ Trí Nhân', 'D22_TH04', 'dh52104533@student.stu.edu.vn', NULL, 0, '2026-07-08 03:03:46', '2026-07-08 03:04:07'),
(106, 'DH52200392', 'Lê Trọng Bình', 'D22_TH11', 'dh52200392@student.stu.edu.vn', NULL, 0, '2026-07-08 03:06:29', '2026-08-18 23:06:27'),
(107, 'DH52201569', 'Nguyễn Trọng Tín', 'D22_TH03', 'dh52201569@student.stu.edu.vn', NULL, 0, '2026-07-08 03:14:29', '2026-07-08 03:14:29');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `anh_train_svs`
--
ALTER TABLE `anh_train_svs`
  ADD CONSTRAINT `anh_train_svs_sinh_vien_id_foreign` FOREIGN KEY (`sinh_vien_id`) REFERENCES `sinh_viens` (`id`);

--
-- Constraints for table `diem_danhs`
--
ALTER TABLE `diem_danhs`
  ADD CONSTRAINT `fk_diemdanh_lichthi` FOREIGN KEY (`lich_thi_id`) REFERENCES `lich_this` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_diemdanh_sinhvien` FOREIGN KEY (`sinh_vien_id`) REFERENCES `sinh_viens` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `lich_this`
--
ALTER TABLE `lich_this`
  ADD CONSTRAINT `fk_lichthi_monhoc` FOREIGN KEY (`mon_hoc_id`) REFERENCES `mon_hocs` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `phan_cong_gvs`
--
ALTER TABLE `phan_cong_gvs`
  ADD CONSTRAINT `fk_phancong_giangvien` FOREIGN KEY (`giang_vien_id`) REFERENCES `giang_viens` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_phancong_lichthi` FOREIGN KEY (`lich_thi_id`) REFERENCES `lich_this` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
