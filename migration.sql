 -- DROP DATABASE IF EXISTS <<database>>;
 -- CREATE DATABASE IF NOT EXISTS <<database>>;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `fio` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Defoult table for User entity (by olijen framework)' AUTO_INCREMENT=14 ;

 -- INSERT INTO `users` (`id`, `username`, `password`, `email`, `fio`, `status`) VALUES
 -- (1, 'admin', 'b59c67bf196a4758191e42f76670ceba', 'olijenius@gmail.com', '', 'admin');