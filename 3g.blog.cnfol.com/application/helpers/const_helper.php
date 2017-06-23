<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('DEFAULT_WEB','cnfol.com');
define('BLOG_VERSION','4.0-20120322');

define("LOGIN_LIMIT_KEY",'49b7c8876d8cb85b');//登陆验证

define('article_info_keys','cnfol_blog_common_article_info');

define('blogcssmaxhave'     ,       6);         //每个博客最多拥有的样式
define('userdefinestylepage',       3);         //博客自定义模板页大小
define('cssstylepagesize'	,		9);			//系统模板列表页大小
define('blackuserpagesize'	,		15);		//黑名单列表页大小
define('articletagpagesize'	,		20);		//文章标签列表页大小

define('channelarticlepagesize',    60);        //频道标签文章列表页的大小
define('channelarticlersspagesize', 20);        //频道标签文章列表页Rss的大小

define('articlesortpagesize',		20);		//被使用做普通的页大小
define('commonpagesize',			10);		//普通的页大小
define('photolistpagesize',         50);        //相册图片的页面大小
define('onlinesubjectpagesize',	    25);	    //直播主题页面的大小
define('maxonlineblocklimit' ,      200);       //默认直播内容板块的多少

define('limitarticlemaxtitlelen',	99);		//文章标题最小长度
define('limitarticlemintitlelen',	3);			//文章标题最大长度
define('limitarticlecontentmaxlen', 150000);    //文章内容最大长度
define('limitarticlecontentminlen', 3);		    //文章内容最小
define('limitsumautolen',		    325);		//摘要的长度
define('bublictimelimit',      		15);        //评论文章等的发表频率是15秒限制
define('commentkey',      			'123x');    //评论key，限制评论内容是否相同
define('commenttimelimit',      	1800);      //限制评论内容是否相同，30分钟
define('addarticleip',				10);		//同IP一天内发文章超过(X = 30)篇，出现验证码
define('addarticlemember',			10);		//同一个博客一天内发文章超过(X = 10)篇，出现验证码
define('addarticletime',             5);		//限制发文章间隔，5秒
define('addarticlelimittime',		1800);		//多少时间内不能发表标题相同的文章  30分钟

define('memberbloglimit'			, 1);	//每个用户最多3个博客
define('linksortcntlimit'			, 30);	//每个用户最多30个友情链接分类
define('linkcntlimit'				, 100);	//每个用户最多100个友情链接
define('articlesortcntlimit'		, 15);	//每个用户最多15个文章分类
define('articletagcntlimit'	    	, 1000);	//每个用户最多100个标签
define('eacharticletaglimit'		, 5);	//每篇文章最多5个标签
define('eachtaglengthlimit'	    	, 30);	//每个标签的长度30个字节
define('blogalbumlimit' 	    	, 6);	//每个标签的长度30个字节
define('parentcommentnum'			, 10);	//默认主人回复的数量
define('articletomy'				, true); //是否同步到个人空间
define('managelistsize'				, 20);		//管理页列表长度
define('initializesort'				, 2592000);		//初始化文章分类序号用

define('MYBLOG_ARTICLE_PAGESIZE'		, 30);		//管理页列表长度

define('photosizelimite' 	    	, 2*1024*1024);	//没张图片限定为2M
define('URL_CRONTAB'                , '/home/www/html/newblog/');//上线后修改
//define('URL_CRONTAB'                , '/home/httpd/updateblog/');//上线后修改

define('FRIEND_GROUP_PAGESIZE',		20);
define('FRIEND_GROUP_BUTTON_NUM',	5);
define('LEAVE_MESSAGE_PAGESIZE',	15);

define('FOCUSE_FRIEND_PAGESIZE',	20);
define('BLOG_COMMENT_PAGESIZE',	20);
define('FANS_PAGESIZE',		10);
define('MYBLOG_MORE_PAGESIZE',	10);
define('MYBLOG_INDEX_PAGESIZE',	10);

define('BLOG_URL', 'http://blog.cnfol.com/');//中金博客
define('FRIEND_GROUP_BUTTON_NUM',	BLOG_URL.'index.php/devaddggroup/grouplist/');
define('BLOG_FACE_INFO',	BLOG_URL.'index.php/myfocus/domainInfo/');
define('BLOG_FOCUSE_FRIENDS',	BLOG_URL.'index.php/myfocus/focusAjax');
define('BLOG_FANS_LIST',	BLOG_URL.'index.php/module/rightFans');

define('affiche_defalut_content','<span style="color:#cccccc;">公告长度不能超过2千个字节</span>');
/* End of file config.php */
/* Location: ./system/application/config/config.php */
