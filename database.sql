CREATE TABLE IF NOT EXISTS `intra_seizoen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `seizoen` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `intra_speeldagen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `speeldagnummer` int(11) NOT NULL,
  `datum` DATE NOT NULL,
  `seizoen_id` int(11) NOT NULL,
  `gemiddeld_verliezend` DOUBLE DEFAULT NULL,
  `is_berekend` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `seizoen_id` (`seizoen_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `intra_spelerperspeeldag` (
  `speler_id` int(11) NOT NULL,
  `speeldag_id` int(11) NOT NULL,
  `gemiddelde` DOUBLE NOT NULL,
  PRIMARY KEY (`speler_id`, `speeldag_id`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `intra_spelerperseizoen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `speler_id` int(11) NOT NULL,
  `seizoen_id` int(11) NOT NULL,
  `basispunten` DOUBLE NOT NULL,
  `gespeelde_sets` int(11) NOT NULL,
  `gewonnen_sets` int(11) NOT NULL,
  `gespeelde_punten` int(11) NOT NULL,
  `gewonnen_punten` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `speler_id` (`speler_id`),
  KEY `seizoen_id` (`seizoen_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `intra_spelers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `voornaam` varchar(32) NOT NULL,
  `naam` varchar(32) NOT NULL,
  `is_lid` tinyint(1) NOT NULL,
  `geslacht` enum('Man','Vrouw') NOT NULL,
  `jeugd` tinyint(1) NOT NULL,
  `klassement` enum('Recreant','D','C2','C1','B2','B1','A') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `intra_wedstrijden` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `speeldag_id` int(11) NOT NULL,
  `team1_speler1` int(11) NOT NULL,
  `team1_speler2` int(11) NOT NULL,
  `team2_speler1` int(11) NOT NULL,
  `team2_speler2` int(11) NOT NULL,
  `set1_1` int(11) NOT NULL,
  `set1_2` int(11) NOT NULL,
  `set2_1` int(11) NOT NULL,
  `set2_2` int(11) NOT NULL,
  `set3_1` int(11) NOT NULL,
  `set3_2` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `speeldag_id` (`speeldag_id`),
  KEY `team1_speler1` (`team1_speler1`),
  KEY `team1_speler2` (`team1_speler2`),
  KEY `team2_speler1` (`team2_speler1`),
  KEY `team2_speler2` (`team2_speler2`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `intra_wedstrijden`
	ADD CONSTRAINT `uitslagenSpeeldagFK` FOREIGN KEY (`speeldag_id`) REFERENCES `intra_speeldagen` (`id`) ON DELETE NO ACTION,
	ADD CONSTRAINT `team1_speler1FK` FOREIGN KEY (`team1_speler1`) REFERENCES `intra_spelers` (`id`) ON DELETE NO ACTION,
	ADD CONSTRAINT `team1_speler2FK` FOREIGN KEY (`team1_speler2`) REFERENCES `intra_spelers` (`id`) ON DELETE NO ACTION,
	ADD CONSTRAINT `team2_speler1FK` FOREIGN KEY (`team2_speler1`) REFERENCES `intra_spelers` (`id`) ON DELETE NO ACTION,
	ADD CONSTRAINT `team2_speler2FK` FOREIGN KEY (`team2_speler2`) REFERENCES `intra_spelers` (`id`) ON DELETE NO ACTION;
	
ALTER TABLE `intra_spelerperspeeldag`
		ADD CONSTRAINT `resultatenSpeeldagFK` FOREIGN KEY (`speeldag_id`) REFERENCES `intra_speeldagen` (`id`) ON DELETE NO ACTION,
		ADD CONSTRAINT `resultatenSpelerFK` FOREIGN KEY (`speler_id`) REFERENCES `intra_spelers` (`id`) ON DELETE NO ACTION;

ALTER TABLE `intra_spelerperseizoen`
		ADD CONSTRAINT `resultatenSeizoenFK` FOREIGN KEY (`seizoen_id`) REFERENCES `intra_seizoen` (`id`) ON DELETE NO ACTION,
		ADD CONSTRAINT `resultatenSpelerFK2` FOREIGN KEY (`speler_id`) REFERENCES `intra_spelers` (`id`) ON DELETE NO ACTION;

ALTER TABLE `intra_speeldagen`
		ADD CONSTRAINT `speeldagenSeizoenFK` FOREIGN KEY (`seizoen_id`) REFERENCES `intra_seizoen` (`id`) ON DELETE NO ACTION;
		
		
