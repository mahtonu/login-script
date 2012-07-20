--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `useremail` varchar(50) NOT NULL,
  `password` char(32) NOT NULL,
  `userhash` char(32) NOT NULL,
  `userlevel` tinyint(4) NOT NULL,
  `username` varchar(100) NOT NULL,
  `phone` varchar(20) NULL,
  `timestamp` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `useremail` (`useremail`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

