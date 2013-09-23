-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Machine: localhost
-- Genereertijd: 23 sep 2013 om 19:52
-- Serverversie: 5.5.24-log
-- PHP-versie: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databank: `bclandegem`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `intra_seizoen`
--

CREATE TABLE IF NOT EXISTS `intra_seizoen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `seizoen` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Gegevens worden uitgevoerd voor tabel `intra_seizoen`
--

INSERT INTO `intra_seizoen` (`id`, `seizoen`) VALUES
(1, '2013 - 2014');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `intra_spelerperseizoen`
--

CREATE TABLE IF NOT EXISTS `intra_spelerperseizoen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `speler_id` int(11) NOT NULL,
  `seizoen_id` int(11) NOT NULL,
  `basispunten` double NOT NULL,
  `gespeelde_sets` int(11) NOT NULL,
  `gewonnen_sets` int(11) NOT NULL,
  `gespeelde_punten` int(11) NOT NULL,
  `gewonnen_punten` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `speler_id` (`speler_id`),
  KEY `seizoen_id` (`seizoen_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=56 ;

--
-- Gegevens worden uitgevoerd voor tabel `intra_spelerperseizoen`
--

INSERT INTO `intra_spelerperseizoen` (`id`, `speler_id`, `seizoen_id`, `basispunten`, `gespeelde_sets`, `gewonnen_sets`, `gespeelde_punten`, `gewonnen_punten`) VALUES
(1, 1, 1, 19.61, 0, 0, 0, 0),
(2, 2, 1, 19.09, 0, 0, 0, 0),
(3, 3, 1, 18.52, 0, 0, 0, 0),
(4, 4, 1, 18.39, 0, 0, 0, 0),
(5, 5, 1, 18.36, 0, 0, 0, 0),
(6, 6, 1, 18.31, 0, 0, 0, 0),
(7, 7, 1, 18.27, 0, 0, 0, 0),
(8, 8, 1, 18.27, 0, 0, 0, 0),
(9, 9, 1, 18.22, 0, 0, 0, 0),
(10, 10, 1, 18.2, 0, 0, 0, 0),
(11, 11, 1, 18.15, 0, 0, 0, 0),
(12, 12, 1, 18.01, 0, 0, 0, 0),
(13, 13, 1, 17.86, 0, 0, 0, 0),
(14, 14, 1, 17.8, 0, 0, 0, 0),
(15, 15, 1, 17.71, 0, 0, 0, 0),
(16, 16, 1, 17.59, 0, 0, 0, 0),
(17, 17, 1, 17.54, 0, 0, 0, 0),
(18, 18, 1, 17.54, 0, 0, 0, 0),
(19, 19, 1, 17.52, 0, 0, 0, 0),
(20, 20, 1, 17.51, 0, 0, 0, 0),
(21, 21, 1, 17.51, 0, 0, 0, 0),
(22, 22, 1, 17.46, 0, 0, 0, 0),
(23, 23, 1, 17.3, 0, 0, 0, 0),
(24, 24, 1, 17.24, 0, 0, 0, 0),
(25, 25, 1, 17.21, 0, 0, 0, 0),
(26, 26, 1, 17.18, 0, 0, 0, 0),
(27, 27, 1, 17.15, 0, 0, 0, 0),
(28, 28, 1, 17.14, 0, 0, 0, 0),
(29, 29, 1, 17.06, 0, 0, 0, 0),
(30, 30, 1, 17.01, 0, 0, 0, 0),
(31, 31, 1, 17.01, 0, 0, 0, 0),
(32, 32, 1, 17, 0, 0, 0, 0),
(33, 33, 1, 16.94, 0, 0, 0, 0),
(34, 34, 1, 16.89, 0, 0, 0, 0),
(35, 35, 1, 16.86, 0, 0, 0, 0),
(36, 36, 1, 16.76, 0, 0, 0, 0),
(37, 37, 1, 16.66, 0, 0, 0, 0),
(38, 38, 1, 16.54, 0, 0, 0, 0),
(39, 39, 1, 16.5, 0, 0, 0, 0),
(40, 40, 1, 16.43, 0, 0, 0, 0),
(41, 41, 1, 16.4, 0, 0, 0, 0),
(42, 42, 1, 16.38, 0, 0, 0, 0),
(43, 43, 1, 16.33, 0, 0, 0, 0),
(44, 44, 1, 16.33, 0, 0, 0, 0),
(45, 45, 1, 16.3, 0, 0, 0, 0),
(46, 46, 1, 16.18, 0, 0, 0, 0),
(47, 47, 1, 16.16, 0, 0, 0, 0),
(48, 48, 1, 16.02, 0, 0, 0, 0),
(49, 49, 1, 15.73, 0, 0, 0, 0),
(50, 50, 1, 17.3, 0, 0, 0, 0),
(51, 51, 1, 17.3, 0, 0, 0, 0),
(52, 52, 1, 17.3, 0, 0, 0, 0),
(53, 53, 1, 17.3, 0, 0, 0, 0),
(54, 54, 1, 17.31, 0, 0, 0, 0),
(55, 55, 1, 17, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `intra_spelers`
--

CREATE TABLE IF NOT EXISTS `intra_spelers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `voornaam` varchar(32) NOT NULL,
  `naam` varchar(32) NOT NULL,
  `is_lid` tinyint(1) NOT NULL,
  `geslacht` enum('Man','Vrouw') NOT NULL,
  `jeugd` tinyint(1) NOT NULL,
  `klassement` enum('Recreant','D','C2','C1','B2','B1','A') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=56 ;

--
-- Gegevens worden uitgevoerd voor tabel `intra_spelers`
--

INSERT INTO `intra_spelers` (`id`, `voornaam`, `naam`, `is_lid`, `geslacht`, `jeugd`, `klassement`) VALUES
(1, 'Thijs', 'Ducheyne', 1, 'Man', 1, 'B2'),
(2, 'Luc', 'Van Tornhout', 1, 'Man', 0, 'C2'),
(3, 'Gert', 'Brantegem', 1, 'Man', 0, 'D'),
(4, 'Lennart', 'Martens', 1, 'Man', 0, 'C2'),
(5, 'Nathan', 'Mogensen', 1, 'Man', 1, 'C2'),
(6, 'Jan', 'Bollaert', 1, 'Man', 0, 'C2'),
(7, 'Bart', 'Ivens', 1, 'Man', 0, 'C1'),
(8, 'Jackie', 'Snauwaert', 1, 'Man', 0, 'C2'),
(9, 'Niels', 'Bollaert', 1, 'Man', 1, 'D'),
(10, 'Stef', 'Van Cauwenberge', 1, 'Man', 0, 'C2'),
(11, 'Filip', 'Vydt', 1, 'Man', 0, 'D'),
(12, 'Emma', 'Mussche', 1, 'Vrouw', 0, 'B1'),
(13, 'Christophe', 'Verbeke', 1, 'Man', 0, 'C2'),
(14, 'Wim', 'Maerevoet', 1, 'Man', 0, 'D'),
(15, 'Nick', 'Bultinck', 1, 'Man', 0, 'D'),
(16, 'Bruno', 'Vereecke', 1, 'Man', 0, 'D'),
(17, 'Steven', 'De Buck', 1, 'Man', 0, 'C1'),
(18, 'Frank', 'Boudry', 1, 'Man', 0, 'C1'),
(19, 'Jana', 'Van Tornhout', 1, 'Vrouw', 0, 'C1'),
(20, 'Pieterjan', 'Claeys', 1, 'Man', 0, 'C1'),
(21, 'Geert', 'De Clercq', 1, 'Man', 0, 'C2'),
(22, 'Lieven', 'Smissaert', 1, 'Man', 0, 'Recreant'),
(23, 'Gino', 'Snoeck', 1, 'Man', 0, 'Recreant'),
(24, 'Linda', 'Bossier', 1, 'Vrouw', 0, 'B2'),
(25, 'Cindy', 'Vergult', 1, 'Vrouw', 0, 'C2'),
(26, 'Evelien', 'Verhelst', 1, 'Vrouw', 0, 'C2'),
(27, 'Dirk', 'Morel', 1, 'Man', 0, 'Recreant'),
(28, 'Sarina', 'Coussement', 1, 'Vrouw', 1, 'B1'),
(29, 'Jonathan', 'Servayge', 1, 'Man', 1, 'Recreant'),
(30, 'Lieve', 'Roose', 1, 'Vrouw', 0, 'D'),
(31, 'Kelly', 'Van Cauwenberge', 1, 'Vrouw', 0, 'C2'),
(32, 'Joke', 'Snauwaert', 1, 'Vrouw', 0, 'C2'),
(33, 'Andreas', 'Ducheyne', 1, 'Man', 1, 'C2'),
(34, 'Michiel', 'De Paepe', 1, 'Man', 0, 'Recreant'),
(35, 'Sylvie', 'Van den Berge', 1, 'Vrouw', 0, 'D'),
(36, 'Bart', 'Couck', 1, 'Man', 0, 'D'),
(37, 'Béa', 'Lintermans', 1, 'Vrouw', 0, 'C2'),
(38, 'Michiel', 'Morel', 1, 'Man', 1, 'Recreant'),
(39, 'Elias', 'De Boeck', 1, 'Man', 0, 'Recreant'),
(40, 'Laura', 'Maebe', 1, 'Vrouw', 0, 'D'),
(41, 'Jean-Marie', 'Telleir', 1, 'Man', 0, 'D'),
(42, 'Kaat', 'Snauwaert', 1, 'Vrouw', 1, 'D'),
(43, 'Pascale', 'De Boever', 1, 'Vrouw', 0, 'D'),
(44, 'Johan', 'Devreese', 1, 'Man', 0, 'Recreant'),
(45, 'Niels', 'Soens', 1, 'Man', 0, 'Recreant'),
(46, 'Jelle', 'Van Tornhout', 1, 'Man', 1, 'D'),
(47, 'Jasper', 'Paelinck', 1, 'Man', 0, 'Recreant'),
(48, 'Katie', 'Van Vooren', 1, 'Vrouw', 0, 'Recreant'),
(49, 'Karel', 'Vandenbussche', 1, 'Man', 1, 'Recreant'),
(50, 'Erwin', 'De Ley', 1, 'Man', 0, 'Recreant'),
(51, 'Annelies', 'De Winter', 1, 'Vrouw', 0, 'Recreant'),
(52, 'Luc', 'Heirbrandt', 1, 'Man', 0, 'Recreant'),
(53, 'Herman', 'Van Damme', 1, 'Man', 0, 'Recreant'),
(54, 'Frank', 'Van Hove', 1, 'Man', 0, 'C1'),
(55, 'Sander', 'Vandewalle', 1, 'Man', 0, 'Recreant');

--
-- Beperkingen voor gedumpte tabellen
--

--
-- Beperkingen voor tabel `intra_spelerperseizoen`
--
ALTER TABLE `intra_spelerperseizoen`
  ADD CONSTRAINT `resultatenSeizoenFK` FOREIGN KEY (`seizoen_id`) REFERENCES `intra_seizoen` (`id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `resultatenSpelerFK2` FOREIGN KEY (`speler_id`) REFERENCES `intra_spelers` (`id`) ON DELETE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
