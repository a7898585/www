DROP TABLE IF EXISTS `tt_tags`;
CREATE TABLE `tt_tags` (
  `tagid` smallint(5) unsigned NOT NULL auto_increment,
  `tag` char(20) NOT NULL,
  `style` char(5) NOT NULL,
  `usetimes` smallint(5) unsigned NOT NULL default '0',
  `lastusetime` int(10) unsigned NOT NULL default '0',
  `hits` mediumint(8) unsigned NOT NULL default '0',
  `lasthittime` int(10) unsigned NOT NULL default '0',
  `listorder` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`tagid`),
  UNIQUE KEY `tag` (`tag`),
  KEY `usetimes` (`usetimes`,`listorder`),
  KEY `hits` (`hits`,`listorder`)
) TYPE=MyISAM;