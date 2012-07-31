CREATE TABLE IF NOT EXISTS `intra_seizoen` (
  `seizoen_id` int(11) NOT NULL AUTO_INCREMENT,
  `seizoen` varchar(32) NOT NULL,
  PRIMARY KEY (`seizoen_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `intra_speeldagen` (
  `speeldag_id` int(11) NOT NULL AUTO_INCREMENT,
  `speeldagnummer` int(11) NOT NULL,
  `seizoen_id` int(11) NOT NULL,
  `gemiddeld_verliezend` int(11) DEFAULT NULL,
  PRIMARY KEY (`speeldag_id`),
  KEY `seizoen_id` (`seizoen_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `intra_spelerperspeeldag` (
  `speler_id` int(11) NOT NULL,
  `speeldag_id` int(11) NOT NULL,
  `ranking` int(11) NOT NULL,
  `gemiddelde` int(11) NOT NULL,
  KEY `speler_id` (`speler_id`),
  KEY `speeldag_id` (`speeldag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `intra_spelerperseizoen` (
  `speler_id` int(11) NOT NULL,
  `seizoen_id` int(11) NOT NULL,
  `basispunten` int(11) NOT NULL,
  `gespeelde_sets` int(11) NOT NULL,
  `gewonnen_sets` int(11) NOT NULL,
  `gespeelde_punten` int(11) NOT NULL,
  `gewonnen_punten` int(11) NOT NULL,
  `aanwezig` int(11) NOT NULL,
  `verbergen` tinyint(1) NOT NULL,
  KEY `speler_id` (`speler_id`),
  KEY `seizoen_id` (`seizoen_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `intra_spelers` (
  `speler_id` int(11) NOT NULL AUTO_INCREMENT,
  `voornaam` varchar(32) NOT NULL,
  `naam` varchar(32) NOT NULL,
  `geslacht` enum('Man','Vrouw') NOT NULL,
  `jeugd` tinyint(1) NOT NULL,
  `klassement` enum('A','B1','B2','C1','C2','D','Recreant') NOT NULL,
  PRIMARY KEY (`speler_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `intra_uitslagen` (
  `uitslag_id` int(11) NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`uitslag_id`),
  KEY `speeldag_id` (`speeldag_id`),
  KEY `team1_speler1` (`team1_speler1`),
  KEY `team1_speler2` (`team1_speler2`),
  KEY `team2_speler1` (`team2_speler1`),
  KEY `team2_speler2` (`team2_speler2`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `intra_uitslagen`
	ADD CONSTRAINT `uitslagenSpeeldagFK` FOREIGN KEY (`speeldag_id`) REFERENCES `intra_speeldagen` (`speeldag_id`) ON DELETE CASCADE,
	ADD CONSTRAINT `team1_speler1FK` FOREIGN KEY (`team1_speler1`) REFERENCES `intra_spelers` (`speler_id`) ON DELETE NO ACTION,
	ADD CONSTRAINT `team1_speler2FK` FOREIGN KEY (`team1_speler2`) REFERENCES `intra_spelers` (`speler_id`) ON DELETE NO ACTION,
	ADD CONSTRAINT `team2_speler1FK` FOREIGN KEY (`team2_speler1`) REFERENCES `intra_spelers` (`speler_id`) ON DELETE NO ACTION,
	ADD CONSTRAINT `team2_speler2FK` FOREIGN KEY (`team2_speler2`) REFERENCES `intra_spelers` (`speler_id`) ON DELETE NO ACTION;
	
ALTER TABLE `intra_spelerperspeeldag`
		ADD CONSTRAINT `resultatenSpeeldagFK` FOREIGN KEY (`speeldag_id`) REFERENCES `intra_speeldagen` (`speeldag_id`) ON DELETE CASCADE,
		ADD CONSTRAINT `resultatenSpelerFK` FOREIGN KEY (`speler_id`) REFERENCES `intra_spelers` (`speler_id`) ON DELETE NO ACTION;

ALTER TABLE `intra_spelerperseizoen`
		ADD CONSTRAINT `resultatenSeizoenFK` FOREIGN KEY (`seizoen_id`) REFERENCES `intra_speeldagen` (`seizoen_id`) ON DELETE CASCADE,
		ADD CONSTRAINT `resultatenSpelerFK2` FOREIGN KEY (`speler_id`) REFERENCES `intra_spelers` (`speler_id`) ON DELETE NO ACTION;

ALTER TABLE `intra_speeldagen`
		ADD CONSTRAINT `speeldagenSeizoenFK` FOREIGN KEY (`seizoen_id`) REFERENCES `intra_seizoen` (`seizoen_id`) ON DELETE CASCADE;
		
		

