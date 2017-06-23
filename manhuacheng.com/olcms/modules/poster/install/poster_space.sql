DROP TABLE IF EXISTS `olcms_poster_space`;
CREATE TABLE IF NOT EXISTS `olcms_poster_space` (
  `spaceid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(50) NOT NULL,
  `type` char(11) NOT NULL,
  `path` char(40) NOT NULL,
  `width` smallint(4) unsigned NOT NULL DEFAULT '0',
  `height` smallint(4) unsigned NOT NULL DEFAULT '0',
  `setting` char(100) NOT NULL,
  `description` char(100) NOT NULL,
  `items` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `disabled` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`spaceid`),
  KEY `disabled` (`disabled`)
) TYPE=MyISAM ;

INSERT INTO `olcms_poster_space` (`spaceid`, `name`, `type`, `path`, `width`, `height`, `setting`, `description`, `items`, `disabled`) VALUES
(1, '顶部搜索右侧广告位', 'banner', 'poster_js/1.js', 430, 63, 'array (\n  ''paddleft'' => '''',\n  ''paddtop'' => '''',\n)', '', 1, 0);
INSERT INTO `olcms_poster_space` (`spaceid`, `name`, `type`, `path`, `width`, `height`, `setting`, `description`, `items`, `disabled`) VALUES
(2, '会员登陆页广告', 'banner', 'poster_js/2.js', 310, 304, 'array (\n  ''paddleft'' => '''',\n  ''paddtop'' => '''',\n)', '会员登陆页广告右侧代替外部通行证广告', 1, 0);
INSERT INTO `olcms_poster_space` (`spaceid`, `name`, `type`, `path`, `width`, `height`, `setting`, `description`, `items`, `disabled`) VALUES
(3, '图片频道图片排行下方', 'banner', 'poster_js/3.js', 249, 87, 'array (\n  ''paddleft'' => '''',\n  ''paddtop'' => '''',\n)', '', 1, 0);
INSERT INTO `olcms_poster_space` (`spaceid`, `name`, `type`, `path`, `width`, `height`, `setting`, `description`, `items`, `disabled`) VALUES
(4, '新闻中心推荐链接左侧', 'banner', 'poster_js/4.js', 748, 91, 'array (\n  ''paddleft'' => '''',\n  ''paddtop'' => '''',\n)', '', 1, 0);
INSERT INTO `olcms_poster_space` (`spaceid`, `name`, `type`, `path`, `width`, `height`, `setting`, `description`, `items`, `disabled`) VALUES
(5, '下载列表页右侧顶部', 'banner', 'poster_js/5.js', 248, 162, 'array (\n  ''paddleft'' => '''',\n  ''paddtop'' => '''',\n)', '', 1, 0);
INSERT INTO `olcms_poster_space` (`spaceid`, `name`, `type`, `path`, `width`, `height`, `setting`, `description`, `items`, `disabled`) VALUES
(6, '下载详情页右侧顶部', 'banner', 'poster_js/6.js', 248, 162, 'array (\n  ''paddleft'' => '''',\n  ''paddtop'' => '''',\n)', '', 1, 0);
INSERT INTO `olcms_poster_space` (`spaceid`, `name`, `type`, `path`, `width`, `height`, `setting`, `description`, `items`, `disabled`) VALUES
(7, '下载详情页右侧下部', 'banner', 'poster_js/7.js', 248, 162, 'array (\n  ''paddleft'' => '''',\n  ''paddtop'' => '''',\n)', '', 1, 0);
INSERT INTO `olcms_poster_space` (`spaceid`, `name`, `type`, `path`, `width`, `height`, `setting`, `description`, `items`, `disabled`) VALUES
(8, '下载频道首页', 'banner', 'poster_js/8.js', 698, 80, 'array (\n  ''paddleft'' => '''',\n  ''paddtop'' => '''',\n)', '', 1, 0);
INSERT INTO `olcms_poster_space` (`spaceid`, `name`, `type`, `path`, `width`, `height`, `setting`, `description`, `items`, `disabled`) VALUES
(9, '下载详情页地址列表右侧', 'banner', 'poster_js/12.js', 330, 50, 'array (\n  ''paddleft'' => '''',\n  ''paddtop'' => '''',\n)', '', 1, 0);
INSERT INTO `olcms_poster_space` (`spaceid`, `name`, `type`, `path`, `width`, `height`, `setting`, `description`, `items`, `disabled`) VALUES
(10, '首页关注下方广告', 'banner', 'poster_js/10.js', 307, 60, 'array (\n  ''paddleft'' => '''',\n  ''paddtop'' => '''',\n)', '', 1, 0);