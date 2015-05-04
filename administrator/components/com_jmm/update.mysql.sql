CREATE TABLE IF NOT EXISTS `#__jmm_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `published` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `#__jmm_templates` (`id`, `title`, `datetime`, `published`) VALUES
(1, 'default', '2013-05-12 07:15:02', 1),
(2, 'jtable2', '2013-05-12 07:15:02', 1),
(3, 'blog4', '2013-05-12 07:15:02', 1),
(4, 'piechart', '2013-05-12 07:15:02', 1),
(5, 'test', '2013-05-12 07:15:02', 1);