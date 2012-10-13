CREATE TABLE IF NOT EXISTS `#__points_points` (
  `id` int(11) NOT NULL auto_increment,
  `points` int(11) NOT NULL,
  `notes` varchar(250) NOT NULL,
  `issuer` int(11) NOT NULL,
  `recipient` int(11) NOT NULL,
  `datetime` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `published` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;