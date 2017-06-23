DROP TABLE IF EXISTS `tt_tags_content`;
CREATE TABLE `tt_tags_content` (
  `tag` char(20) NOT NULL,
  `url` varchar(255),
  `title` varchar(80),
  `siteid` tinyint(3) unsigned NOT NULL,
  `modelid` tinyint(3) unsigned NOT NULL,
  `contentid` mediumint(8) unsigned NOT NULL default '0',
  `catid` SMALLINT( 5 ) UNSIGNED NOT NULL ,
  `updatetime` INT( 10 ) UNSIGNED NOT NULL,
  KEY `modelid` (`modelid`, `siteid`, `contentid`),
  KEY `tag` (`tag`(10))
) TYPE=MyISAM;