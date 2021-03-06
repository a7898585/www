DROP TABLE IF EXISTS `olcms_special`;
CREATE TABLE IF NOT EXISTS `olcms_special` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '专题ID',
  `title` char(60) NOT NULL COMMENT '专题名称',
  `typeids` char(100) NOT NULL COMMENT '附属分类',
  `thumb` char(100) NOT NULL COMMENT '专题缩略图',
  `banner` char(100) NOT NULL COMMENT '专题横幅',
  `description` char(255) NOT NULL COMMENT '专题简介',
  `url` char(100) NOT NULL COMMENT '专题地址',
  `ishtml` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ispage` tinyint(1) unsigned NOT NULL COMMENT '首页实现分页',
  `filename` char(40) NOT NULL COMMENT '文件名',
  `pics` char(100) NOT NULL COMMENT '组图',
  `voteid` char(60) NOT NULL COMMENT '投票',
  `style` char(20) NOT NULL,
  `index_template` char(40) NOT NULL COMMENT '专题模板',
  `list_template` char(40) NOT NULL COMMENT '列表页模板',
  `show_template` char(60) NOT NULL COMMENT '内容页模板',
  `css` text NOT NULL,
  `username` char(40) NOT NULL COMMENT '创建人',
  `userid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '创建人ID',
  `createtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `listorder` smallint(5) unsigned NOT NULL COMMENT '排序',
  `elite` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '推荐',
  `disabled` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '专题启动开关',
  PRIMARY KEY  (`id`),
  KEY `disabled` (`disabled`)
) TYPE=MyISAM ;

