-- phpMyAdmin SQL Dump
-- version 4.4.10
-- http://www.phpmyadmin.net
--
-- Host: localhost:8889
-- Generation Time: Nov 07, 2016 at 10:07 AM
-- Server version: 5.5.42
-- PHP Version: 7.0.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `winespy`
--

-- --------------------------------------------------------

--
-- Table structure for table `winesold`
--

DROP TABLE IF EXISTS `winesold`;
CREATE TABLE `winesold` (
  `id` bigint(20) NOT NULL,
  `date` datetime NOT NULL,
  `value` int(11) NOT NULL,
  `id_winelist` int(11) NOT NULL,
  `sku` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=176 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Truncate table before insert `winesold`
--

TRUNCATE TABLE `winesold`;
--
-- Dumping data for table `winesold`
--

INSERT INTO `winesold` (`id`, `date`, `value`, `id_winelist`, `sku`) VALUES
(1, '2016-10-10 00:00:00', 1, 1, '1'),
(2, '2016-10-10 00:00:00', 1, 1, '1'),
(3, '2016-10-10 00:00:00', 1, 1, '1'),
(4, '2016-10-10 00:00:00', 1, 1, '1'),
(5, '2016-10-10 00:00:00', 1, 1, '1'),
(6, '2016-10-10 00:00:00', 1, 1, '1'),
(7, '2016-10-10 00:00:00', 1, 1, '1'),
(8, '2016-10-10 00:00:00', 1, 1, '400010078'),
(9, '2016-10-10 00:00:00', 1, 1, '436010123'),
(10, '2016-10-10 00:00:00', 1, 1, '496010077'),
(11, '2016-10-10 00:00:00', 1, 1, '482010087'),
(12, '2016-10-11 00:00:00', 1, 1, '436010004'),
(13, '2016-10-11 00:00:00', 1, 1, '482010087'),
(14, '2016-10-11 00:00:00', 1, 1, '482010087'),
(15, '2016-10-11 00:00:00', 1, 1, '496010077'),
(16, '2016-10-11 00:00:00', 1, 1, '051301VEN15'),
(17, '2016-10-11 00:00:00', 1, 1, '051301CIR15'),
(18, '2016-10-11 00:00:00', 1, 1, '0496010077'),
(19, '2016-10-11 01:16:52', 1, 1, '051301VEG14'),
(20, '2016-10-11 01:17:25', 1, 1, '0482010087'),
(21, '2016-10-11 01:17:26', 1, 1, '051301CIR15'),
(22, '2016-10-11 01:17:27', 1, 1, '051301VEG14'),
(23, '2016-10-11 01:21:29', 1, 1, '043801FOR132'),
(24, '2016-10-11 01:41:41', 1, 1, 'FMACCGRILLO2014'),
(25, '2016-10-11 01:41:45', 1, 1, 'VTRBTARANT14'),
(26, '2016-10-11 01:52:01', 1, 1, '051301VEN15'),
(27, '2016-10-12 03:06:00', 1, 1, '0436010004'),
(28, '2016-10-12 04:19:59', 1, 1, '051301VEG14'),
(29, '2016-10-11 00:00:00', 2, 1, 'asdffadsf'),
(30, '2016-10-17 02:52:53', 6, 1, 'CAUIICHIC1175'),
(31, '2016-10-17 02:54:42', 5, 1, '051301CIR15'),
(32, '2016-10-17 02:57:58', 5, 1, '051301CIR15'),
(33, '2016-10-17 02:58:36', 5, 1, 'CSNROSSOMONTAL75'),
(34, '2016-10-17 02:58:37', 5, 1, 'CSNROSSOMONTAL75'),
(35, '2016-10-17 02:58:37', 5, 1, 'CSNROSSOMONTAL75'),
(36, '2016-10-17 03:00:35', 3, 1, '0496010077'),
(37, '2016-10-17 03:01:10', 2, 1, '051301CIR15'),
(38, '2016-10-17 03:02:56', 3, 1, '051301CIR15'),
(39, '2016-10-17 03:03:18', 3, 1, '051301CIR15'),
(40, '2016-10-17 03:05:55', 5, 1, 'CAUIISANG1175'),
(41, '2016-10-17 03:06:55', 3, 1, '051301CIR15'),
(42, '2016-10-17 03:08:17', 3, 1, 'CAUIICHIC1175'),
(43, '2016-10-17 03:10:15', 4, 1, '0400010078'),
(44, '2016-10-17 03:10:18', 4, 1, '0400010078'),
(45, '2016-10-17 03:10:18', 4, 1, '0400010078'),
(46, '2016-10-17 03:10:19', 4, 1, '0400010078'),
(47, '2016-10-17 03:10:19', 4, 1, '0400010078'),
(48, '2016-10-17 03:10:19', 4, 1, '0400010078'),
(49, '2016-10-17 03:10:31', 4, 1, '0400010078'),
(50, '2016-10-17 03:10:31', 4, 1, '0400010078'),
(51, '2016-10-17 03:10:32', 4, 1, '0400010078'),
(52, '2016-10-17 03:10:32', 4, 1, '0400010078'),
(53, '2016-10-17 03:10:32', 4, 1, '0400010078'),
(54, '2016-10-17 03:10:32', 4, 1, '0400010078'),
(55, '2016-10-17 03:10:32', 4, 1, '0400010078'),
(56, '2016-10-17 03:10:32', 4, 1, '0400010078'),
(57, '2016-10-17 03:10:33', 4, 1, '0400010078'),
(58, '2016-10-17 03:10:50', 3, 1, '0400010078'),
(59, '2016-10-17 03:10:51', 3, 1, '0400010078'),
(60, '2016-10-17 03:10:52', 3, 1, '0400010078'),
(61, '2016-10-17 03:12:16', 5, 1, 'MAJ8032680590115'),
(62, '2016-10-17 03:36:23', 6, 1, '051301VEG14'),
(63, '2016-10-17 03:37:55', 2, 1, '0400010078'),
(64, '2016-10-17 03:37:57', 2, 1, '0400010078'),
(65, '2016-10-17 03:44:45', 5, 1, '051301VEG14'),
(66, '2016-10-17 03:57:59', 3, 1, '0377010056'),
(67, '2016-10-17 03:58:21', 4, 1, '051301VEG14'),
(68, '2016-10-17 04:18:22', 3, 1, 'CAUIISANG1175'),
(69, '2016-10-17 04:22:17', 5, 1, '0436010123'),
(70, '2016-10-17 04:22:42', 4, 1, '051301CIR15'),
(71, '2016-10-17 04:23:02', 2, 1, 'LAMONVB1406'),
(72, '2016-10-17 04:24:26', 3, 1, '051301CIR15'),
(73, '2016-10-17 04:26:52', 3, 1, '0436010004'),
(74, '2016-10-17 04:28:32', 4, 1, '051301VEG14'),
(75, '2016-10-17 04:28:45', 3, 1, '0400010078'),
(76, '2016-10-17 04:34:04', 4, 1, '0400010078'),
(77, '2016-10-17 04:34:17', 5, 1, '0400010078'),
(78, '2016-10-17 04:34:18', 5, 1, '0400010078'),
(79, '2016-10-17 04:34:18', 5, 1, '0400010078'),
(80, '2016-10-17 04:34:19', 5, 1, '0400010078'),
(81, '2016-10-17 04:34:19', 5, 1, '0400010078'),
(82, '2016-10-17 04:39:31', 3, 1, '051301CIR15'),
(83, '2016-10-17 04:44:27', 1, 1, '0436010004'),
(84, '2016-10-17 04:44:30', 1, 1, '0436010004'),
(85, '2016-10-17 04:44:30', 1, 1, '0436010004'),
(86, '2016-10-17 04:46:23', 1, 1, '051301CIR15'),
(87, '2016-10-17 04:46:45', 1, 1, '0400010078'),
(88, '2016-10-17 04:46:47', 1, 1, '051301CIR15'),
(89, '2016-10-17 04:46:49', 1, 1, '0436010004'),
(90, '2016-10-17 04:46:51', 1, 1, '0436010123'),
(91, '2016-10-17 04:46:52', 1, 1, '043801FOR132'),
(92, '2016-10-17 08:31:40', 1, 1, '0292010249'),
(93, '2016-10-17 08:59:08', 1, 1, 'BIO503'),
(94, '2016-10-17 08:59:10', 1, 1, 'BIO503'),
(95, '2016-10-17 09:00:21', 1, 1, 'FMACCGRILLO2014'),
(96, '2016-10-17 09:00:22', 1, 1, 'MAJ8032680590115'),
(97, '2016-10-18 09:41:11', 1, 1, '051301CIR15'),
(98, '2016-10-18 09:50:41', 1, 1, '0377010056'),
(99, '2016-10-18 09:50:45', 1, 1, '0482010087'),
(100, '2016-10-18 10:45:14', 15, 1, '051301VEG14'),
(101, '2016-10-18 11:07:35', 5, 1, '0292010249'),
(102, '2016-10-18 11:20:25', 9, 1, 'BROSPUROVERELLO75'),
(103, '2016-10-18 11:23:19', 1, 1, '043801FOR132'),
(104, '2016-10-18 11:23:47', 1, 1, '0292010249'),
(105, '2016-10-18 11:24:23', 1, 1, '0377010056'),
(106, '2016-10-18 11:24:53', 1, 1, '051301VEN15'),
(107, '2016-10-18 11:25:04', 1, 1, '0377010056'),
(108, '2016-10-18 11:37:07', 3, 1, '051301CIR15'),
(109, '2016-10-18 11:39:09', 3, 1, 'LAMONVB1406'),
(110, '2016-10-19 01:00:16', 1, 1, '0400010078'),
(111, '2016-10-19 01:00:20', 1, 1, '0436010004'),
(112, '2016-10-19 01:00:22', 6, 1, '051301VEG14'),
(113, '2016-10-19 01:06:43', 5, 1, 'CAUIICHIC1175'),
(114, '2016-10-19 01:07:33', 7, 1, 'CSNROSSOMONTAL75'),
(115, '2016-10-19 01:12:51', 1, 1, '0436010004'),
(116, '2016-10-19 01:13:15', 3, 1, '051301VEG14'),
(117, '2016-10-19 09:28:29', 1, 1, '0436010004'),
(118, '2016-10-20 03:35:33', 1, 1, '0073010074'),
(119, '2016-10-20 04:26:49', 2, 1, '0436010123'),
(120, '2016-10-20 04:40:29', 5, 1, '0423010068'),
(121, '2016-10-20 04:42:02', 3, 1, '0051010417'),
(122, '2016-10-20 04:42:49', 6, 1, '0438010775'),
(123, '2016-10-20 05:04:06', 2, 1, '0468010100'),
(124, '2016-10-20 05:12:52', 1, 1, '0353010327'),
(125, '2016-10-20 05:13:36', 3, 1, '0482010104'),
(126, '2016-10-21 09:10:16', 3, 1, '0051010417'),
(127, '2016-10-21 09:11:59', 4, 1, '0438010773'),
(128, '2016-10-21 09:17:31', 1, 1, '0051010417'),
(129, '2016-10-21 09:19:16', 3, 1, '0382010128'),
(130, '2016-10-21 09:32:50', 2, 1, '0370010068'),
(131, '2016-10-21 09:37:26', 3, 1, '0438010773'),
(132, '2016-10-21 11:29:36', 4, 1, '0051010417'),
(133, '2016-10-21 11:30:55', 1, 1, '0400010077'),
(134, '2016-10-21 11:48:56', 3, 1, '0438010775'),
(135, '2016-10-21 11:48:56', 3, 1, '0438010775'),
(136, '2016-10-21 01:11:14', 1, 1, '0051010417'),
(137, '2016-10-21 01:12:21', 2, 1, '0438010773'),
(138, '2016-10-21 01:12:25', 4, 1, '0051010417'),
(139, '2016-10-21 01:12:31', 1, 1, '0073010074'),
(140, '2016-10-25 04:19:16', 3, 6, '0482010104'),
(141, '2016-10-25 05:15:53', 2, 6, '000101180197'),
(142, '2016-10-27 10:22:32', 6, 1, '0330010799'),
(143, '2016-10-27 10:47:31', 5, 1, '0361010405'),
(144, '2016-10-27 10:47:57', 5, 1, '0005010761'),
(145, '2016-10-28 10:36:12', 1, 1, '0429017A4514060001'),
(146, '2016-10-28 10:36:15', 1, 1, '0400010077'),
(147, '2016-10-28 10:36:17', 1, 1, '0361010405'),
(148, '2016-10-28 10:36:19', 1, 1, '0068010101'),
(149, '2016-10-28 04:32:40', 1, 1, '0383010139'),
(150, '2016-10-28 04:32:53', 1, 1, '0383010139'),
(151, '2016-10-28 04:33:01', 1, 1, '04470103310'),
(152, '2016-10-28 05:07:49', 1, 1, '0456010074'),
(153, '2016-10-28 17:08:44', 1, 1, '045601552ETCBB'),
(154, '2016-10-28 17:09:23', 3, 1, 'BIO503'),
(155, '2016-10-28 17:09:26', 2, 1, '0005010761'),
(156, '2016-10-28 17:09:27', 1, 1, 'LAMONVB1406'),
(157, '2016-10-28 17:38:22', 3, 1, 'BIO503'),
(158, '2016-10-28 17:38:32', 2, 1, '04470103310'),
(159, '2016-10-28 17:41:43', 2, 1, 'BIO503'),
(160, '2016-10-28 17:43:07', 5, 1, 'CBLTRECIABOT75'),
(161, '2016-10-28 17:43:50', 4, 1, '0438010775'),
(162, '2016-10-28 17:44:01', 3, 1, '0496010248'),
(163, '2016-10-28 17:44:03', 3, 1, '0438010775'),
(164, '2016-10-28 17:53:27', 6, 1, '04470103310'),
(165, '2016-10-28 17:53:41', 4, 1, 'TBD277'),
(166, '2016-10-31 13:45:50', 6, 1, '045601552ETCBB'),
(167, '2016-10-31 13:45:52', 5, 1, 'BIO503'),
(168, '2016-10-31 13:45:55', 2, 1, 'BIO503'),
(169, '2016-10-31 13:45:57', 1, 1, '0005010761'),
(170, '2016-10-31 13:45:58', 1, 1, '0005010761'),
(171, '2016-10-31 13:46:06', 2, 1, '0005010761'),
(172, '2016-11-02 12:55:20', 1, 1, '045601552ETCBB'),
(173, '2016-11-02 12:55:22', 1, 1, 'ENIOTTBIO75'),
(174, '2016-11-02 12:55:25', 1, 1, '0005010761'),
(175, '2016-11-03 10:15:47', 1, 1, '0068010101');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `winesold`
--
ALTER TABLE `winesold`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `winesold`
--
ALTER TABLE `winesold`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=176;