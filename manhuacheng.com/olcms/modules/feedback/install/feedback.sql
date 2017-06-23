

CREATE TABLE IF NOT EXISTS `olcms_feedback` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(32) NOT NULL,
  `content` varchar(500) NOT NULL,
  `customer` varchar(64) NOT NULL,
  `ip` char(15) NOT NULL,
  `updatetime` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
