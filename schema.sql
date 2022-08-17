DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `ID` int(11) NOT NULL,
  `access_token` varchar(255) DEFAULT NULL,
  `username` varchar(128) NOT NULL,
  `rss_password_hash` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

