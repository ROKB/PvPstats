CREATE TABLE `pvpstats_faction` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `faction` BIT NOT NULL,
  `level` TINYINT UNSIGNED NOT NULL,
  `date` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `pvpstats_players` (
  `character_guid` INT UNSIGNED NOT NULL,
  `level` TINYINT UNSIGNED NOT NULL,
  `date` DATETIME NOT NULL,
  PRIMARY KEY (`character_guid`,`date`)
) ENGINE=InnoDB;
