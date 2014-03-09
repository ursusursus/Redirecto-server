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
-- Štruktúra tabuľky pre tabuľku `redirecto_room`
--

CREATE TABLE IF NOT EXISTS `redirecto_room` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `floor` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `changed_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `phone_number` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31 ;

--
-- Sťahujem dáta pre tabuľku `redirecto_room`
--

INSERT INTO `redirecto_room` (`id`, `name`, `floor`, `created_at`, `changed_at`, `phone_number`) VALUES
(26, 'Spal', 'meh', '2013-11-24 16:44:24', '0000-00-00 00:00:00', 0),
(25, 'Dets', 'meh', '2013-11-24 16:44:20', '0000-00-00 00:00:00', 0),
(24, 'Obyv', 'meh', '2013-11-24 16:44:16', '0000-00-00 00:00:00', 0),
(23, 'Kuch', 'meh', '2013-11-24 16:44:10', '0000-00-00 00:00:00', 0),
(28, 'B521', 'Admin', '2014-03-01 14:10:55', '0000-00-00 00:00:00', 2553),
(29, 'B521', 'Učebňa', '2014-03-01 14:11:10', '0000-00-00 00:00:00', 7021),
(30, 'BXYZ', 'Skleník', '2014-03-01 14:12:05', '0000-00-00 00:00:00', 7014);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
