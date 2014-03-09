-- phpMyAdmin SQL Dump
-- version 3.4.9
-- http://www.phpmyadmin.net
--
-- Hostiteľ: localhost
-- Vygenerované:: 01.Mar, 2014 - 17:28
-- Verzia serveru: 5.1.70
-- Verzia PHP: 5.3.28-pl1-gentoo

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databáza: `abs_brecka`
--

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `redirecto_user_room`
--

CREATE TABLE IF NOT EXISTS `redirecto_user_room` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  PRIMARY KEY (`id`,`user_id`,`room_id`),
  KEY `user_id` (`user_id`),
  KEY `room_id` (`room_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=282 ;

--
-- Sťahujem dáta pre tabuľku `redirecto_user_room`
--

INSERT INTO `redirecto_user_room` (`id`, `user_id`, `room_id`) VALUES
(1, 1, 1),
(30, 1, 2),
(31, 1, 3),
(32, 1, 4),
(68, 1, 5),
(69, 1, 6),
(70, 1, 7),
(71, 1, 8),
(72, 1, 9),
(196, 2, 9),
(197, 2, 10),
(198, 2, 11),
(199, 2, 6),
(200, 2, 7),
(205, 2, 24),
(236, 2, 16),
(238, 2, 15),
(242, 2, 14),
(243, 2, 17),
(244, 2, 26),
(254, 1, 25),
(255, 1, 24),
(269, 1, 23),
(273, 1, 17),
(274, 1, 15),
(277, 1, 29),
(278, 1, 30),
(279, 2, 29),
(280, 2, 30),
(281, 1, 28);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
