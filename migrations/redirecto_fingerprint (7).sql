-- phpMyAdmin SQL Dump
-- version 3.4.9
-- http://www.phpmyadmin.net
--
-- Hostiteľ: localhost
-- Vygenerované:: 09.Mar, 2014 - 03:18
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
-- Štruktúra tabuľky pre tabuľku `redirecto_fingerprint`
--

CREATE TABLE IF NOT EXISTS `redirecto_fingerprint` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ap_00:26:cb:a0:93:f1` int(11) DEFAULT '-110',
  `ap_00:26:cb:4d:78:ae` int(11) DEFAULT '-110',
  `ap_00:26:cb:4d:78:a1` int(11) DEFAULT '-110',
  `ap_00:26:cb:4e:19:fe` int(11) DEFAULT '-110',
  `ap_00:26:cb:4e:19:f1` int(11) DEFAULT '-110',
  `ap_00:26:cb:a0:93:fe` int(11) DEFAULT '-110',
  `ap_00:26:cb:4e:18:d1` int(11) DEFAULT '-110',
  `ap_00:26:cb:a0:a1:b1` int(11) DEFAULT '-110',
  `ap_00:26:cb:a0:a1:be` int(11) DEFAULT '-110',
  `ap_58:bc:27:5c:ba:9e` int(11) DEFAULT '-110',
  `ap_00:26:cb:4e:19:11` int(11) DEFAULT '-110',
  `ap_00:26:cb:4e:19:1e` int(11) DEFAULT '-110',
  `ap_00:26:cb:4e:1b:01` int(11) DEFAULT '-110',
  `ap_58:bc:27:5c:ba:91` int(11) DEFAULT '-110',
  `ap_00:26:cb:4d:78:a2` int(11) DEFAULT '-110',
  `ap_00:26:cb:4d:78:ad` int(11) DEFAULT '-110',
  `ap_00:26:cb:4e:19:fd` int(11) DEFAULT '-110',
  `ap_00:26:cb:4e:19:f2` int(11) DEFAULT '-110',
  `ap_00:26:cb:a0:93:f2` int(11) DEFAULT '-110',
  `ap_00:26:cb:a0:93:fd` int(11) DEFAULT '-110',
  `ap_00:26:cb:4e:18:d2` int(11) DEFAULT '-110',
  `ap_00:26:cb:a0:a1:bd` int(11) DEFAULT '-110',
  `ap_00:26:cb:a0:a1:b2` int(11) DEFAULT '-110',
  `ap_00:26:cb:4e:19:12` int(11) DEFAULT '-110',
  `ap_00:26:cb:4e:19:1d` int(11) DEFAULT '-110',
  `ap_58:bc:27:5c:ba:9d` int(11) DEFAULT '-110',
  `ap_00:26:cb:9f:ac:92` int(11) DEFAULT '-110',
  `ap_58:bc:27:5c:ba:92` int(11) DEFAULT '-110',
  `room_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `room_id` (`room_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
