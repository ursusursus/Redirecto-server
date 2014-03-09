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
-- Štruktúra tabuľky pre tabuľku `redirecto_user`
--

CREATE TABLE IF NOT EXISTS `redirecto_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'regular',
  `token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `changed_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `directory_number` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Sťahujem dáta pre tabuľku `redirecto_user`
--

INSERT INTO `redirecto_user` (`id`, `email`, `password`, `role`, `token`, `created_at`, `changed_at`, `directory_number`) VALUES
(1, 'admin@redirecto.sk', '6e017b5464f820a6c1bb5e9f6d711a667a80d8ea', 'admin', 'f77a9d243976bfe6cebc53861c7fd577a30df546', '2013-11-15 22:14:38', '0000-00-00 00:00:00', 24),
(2, 'user@redirecto.sk', '6e017b5464f820a6c1bb5e9f6d711a667a80d8ea', 'regular', NULL, '2013-11-20 21:10:58', '0000-00-00 00:00:00', 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
