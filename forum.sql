-- MariaDB dump
-- Compatible with MariaDB 10.x and MySQL 8.x

CREATE TABLE IF NOT EXISTS `forum` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) DEFAULT NULL,
  `times` datetime DEFAULT NULL,
  `subj` varchar(128) DEFAULT NULL,
  `author` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `archive` enum('Y','N') DEFAULT 'N',
  `level` int(11) DEFAULT NULL,
  `parent` enum('Y','N') DEFAULT 'N',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
