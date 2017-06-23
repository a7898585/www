<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
  |--------------------------------------------------------------------------
  | Cache setting
  |--------------------------------------------------------------------------
 */
define('ISCACHE', true);      //是否开启缓存
define('EXPIRETIME', 60 * 6);
define('EXPIRETIME_1', 60 * 10);
define('EXPIRETIME_2', 60 * 30);
define('EXPIRETIME_3', 60 * 60);
define('EXPIRETIME_4', 60 * 60 * 24);
define('EXPIRETIME_5', 60 * 60 * 24 * 7);

/*
  |--------------------------------------------------------------------------
  | Memcache setting
  |--------------------------------------------------------------------------
 */

$config['cache']['memcache']['server'] = array(
				    array('host'=>'memcache7.cache.cnfol.com','port'=>11211),
					array('host'=>'memcache8.cache.cnfol.com','port'=>11211)
);
$config['cache']['memcache']['compress'] = false;
$config['cache']['memcache']['expire'] = EXPIRETIME;
$config['cache']['memcache']['prefix'] = 'CNfol_Blog_';
//用户中心缓存
$config['passport_cache'] = array(
    'server' => array(
        array('host'=>'memcache2.cache.cnfol.com','port'=>11211),
        array('host'=>'memcache3.cache.cnfol.com','port'=>11211),
        array('host'=>'memcache4.cache.cnfol.com','port'=>11211),
        array('host'=>'memcache5.cache.cnfol.com','port'=>11211)

    ),
    'compress' => true,
    'expire' => 60 * 60 * 24 * 30,
    'prefix' => '',
    'is_log' => true,
    'log_dir' => '/var/tmp/passport_web/cache',
    'log_name' => 'passport_web_cache'
);
/*
  |--------------------------------------------------------------------------
  | key name
  |--------------------------------------------------------------------------
 */
$config['K1000'] = 'User_getUserGroup_{UserID}';
$config['K1001'] = 'User_getUserBaseInfo_{UserID}';
$config['K1002'] = 'User_getUserInfo_{UserID}';
$config['K1003'] = 'User_getMsgCount_{UserID}';
$config['K1004'] = 'User_getUserGrade_{UserID}';
$config['K1005'] = 'Memberblog_getMemberBlogStat_{MemberID}';
$config['K1006'] = 'Memberblog_getMemberBlogStatDetail_{MemberID}_{AccessBegin}';
$config['K1007'] = 'Memberblog_getArchiveList_{MemberID}_{Type}';
$config['K1008'] = 'Memberblog_getBlogConfig_{MemberID}';
$config['K1009'] = 'Memberblog_getAccessList_{GroupIDs}';
$config['K1010'] = 'Memberblog_getMemberBlogbyDomainName_{QryData}_{Type}';
$config['K1011'] = 'Memberblog_getMemberBlogList_{QryData}_{Type}';
$config['K1012'] = 'Memberblog_getBlogAffiche_{MemberID}';
$config['K1013'] = 'Blogarticle_getBlogTagArticle_Count_{TagID}';
$config['K1014'] = 'Blogarticle_getBlogArticleByID_{MemberID}_{ArticleID}';

$config['K1015'] = 'Blogarticle_getMemberArticleList_allCount_{MemberID}';
//$config['K1015'] 	=  'BlogarticleCount_{MemberID}_{StartDate}_{EndDate}_{SelfRecommend}_{ismul}';
$config['K1016'] = 'Blogarticle_getMemberArticleList_{MemberID}_{PageNo}_{StartDate}_{EndDate}_{SelfRecommend}_{ismul}_{istop}';


$config['K1017'] = 'Blogarticle_getBlogArticleStatByID_{MemberID}_{ArticleID}';
$config['K1018'] = 'Blogarticle_getBlogArticleArchive_{MemberID}_{Type}';
$config['K1019'] = 'Bloglink_getLinkList_{MemberID}_{IsPublic}_{PageNo}';
$config['K1020'] = 'Bloglink_getLinkList_Count_{MemberID}_{IsPublic}';
$config['K1021'] = 'Bloglink_getLinkSortList_Count_{MemberID}';
$config['K1022'] = 'Bloglink_getLinkSortList_{MemberID}';
$config['K1023'] = 'Articlesort_getArticleSortList_AjaxList_{MemberID}';
$config['K1024'] = 'Articlesort_getArticleSortList_{MemberID}_{PageNo}';
$config['K1025'] = 'Articlesort_getArticleSortList_Count_{MemberID}';
$config['K1026'] = 'Articlesort_getSortInfoByID_{MemberID}_{SortID}';
$config['K1027'] = 'Articlecomment_getArtCommentList_Count_{ArticleID}';
$config['K1028'] = 'Articlecomment_getArtCommentList_{ArticleID}_{PageNo}_{PageSize}';
$config['K1029'] = 'Blogarticle_getBlogTagArticle_{TagID}_{PageNo}_{SelfRecommend}_{ismul}_{istop}';
$config['K1030'] = 'Articlecomment_getArtParentCommentList_Count_{ParentCommentID}_{ArticleID}';
$config['K1031'] = 'Articlecomment_getArtParentCommentList_{ParentCommentID}_{ArticleID}';
$config['K1032'] = 'Blogalbum_getPhotoById_{PhotoID}_{AlbumID}_{UserID}';
$config['K1033'] = 'Blogalbum_getblogalbum_Count_{MemberID}';
$config['K1034'] = 'Blogalbum_getblogalbum_{MemberID}';
$config['K1035'] = 'Blogalbum_getAlubmInfoById_{MemberID}_{AlbumID}';
$config['K1036'] = 'Blogalbum_getAlbumPhoteList_{MemberID}_{AlbumID}';
$config['K1037'] = 'Blogalbum_getPhotoComment_Count_{AlbumID}_{PhotoID}';
$config['K1038'] = 'Blogalbum_getPhotoComment_{AlbumID}_{PhotoID}_{PageNo}';
$config['K1039'] = 'Memberblog_getMemberBlogList_Count_{QryData}_{Type}';
$config['K1040'] = 'Blogarticle_getMemberArticleListIndex_{MemberID}_{SelfRecommend}';
$config['K1041'] = 'Channel_getSyaTagArticleList_{TagID}_{PageNo}_{IsPrime}';
$config['K1042'] = 'Blogarticle_getMemberArticleListSort_allCount_{MemberID}';
//$config['K1042'] 	=  'Blogarticle_getMemberArticleListSort_Count_{MemberID}_{SortID}_{SelfRecommend}_{ismul}';
$config['K1043'] = 'Blogarticle_getMemberArticleListSort_{MemberID}_{PageNo}_{SortID}_{SelfRecommend}_{ismul}_{istop}_{kind}';
$config['K1044'] = 'Channel_getRecommendArticle_{Recomend}_{PageNo}';
//$config['K1045'] 	=  'channel_getRecommendArticle_keys_{Recomend}'; 
$config['K1046'] = 'Channel_getUserRecommendArticle_Count';
$config['K1047'] = 'Channel_getUserRecommendArticle_{PageNo}';
//$config['K1048'] 	=  'channel_getUserRecommendArticle_keys'; 
//$config['K1049'] 	=  'channel_getSyaTagArticleList_count_p_{IsPrime}'; 
//$config['K1050'] 	=  'channel_getSyaTagArticleList_p_{IsPrime}_{PageNo}';
//$config['K1051'] 	=  'channel_getSyaTagArticleList_keys_p_{IsPrime}';
//$config['K1052'] 	=  'user_getUserGroup_{UserID}';
$config['K1053'] = 'Articletags_getArticleTagList_Count_{UserID}';
$config['K1054'] = 'Articletags_getArticleTagList_{UserID}_{PageNo}';
$config['K1055'] = 'Articletags_getArticleTagList_AjaxList_{UserID}';
$config['K1056'] = 'Articletractback_getTrackBackList_Count_{ArticleID}';
$config['K1057'] = 'Articletractback_getTrackBackList_{ArticleID}_{PageNo}';
$config['K1058'] = 'Blogarticle_getApBlogArticleByID_{ArticleID}';
$config['K1059'] = 'BlackUser_getBlackUserList_Count_{MemberID}';
$config['K1060'] = 'BlackUser_getBlackUserList_{MemberID}_{PageNo}';
$config['K1061'] = 'Blogarticle_getCommentArticleList_Count_{MemberID}';
$config['K1062'] = 'Blogarticle_getCommentArticleList_{MemberID}_{PageNo}';



#博客访问统计信息
$config['K1071'] = 'stat_click_{MemberID}';

//文章转载数用
$config['K1072'] = 'get_transshipment_num_{articleid}';

//文章收藏数用
$config['K1073'] = 'get_articlecollect_num_{articleid}';

//文章举报用
$config['K1074'] = 'get_articlereport_{articleid}';

//获取黑名单列表用
$config['K1075'] = 'get_blacklist_{userid}';

$config['K1076'] = 'Blogarticle_getMemberArticleIndexNum_Count_{MemberID}'; //个人博客首页文章数用
$config['K1077'] = 'Blogarticle_getMemberArticleIndexList_Count_{MemberID}'; //个人博客首页文章列表用

$config['articleeverbrowse'] = 'articleeverbrowse_article_articleid'; //文章内用户浏览过的文章
$config['guesteverbrowse'] = 'guesteverbrowse_article_userid'; //个人浏览过的文章
$config['articlevisitor'] = 'visitor_article_articleid'; //文章访客
//分组列表
$config['K1078'] = 'Bloglink_getGroupList_Count_{MemberID}';
$config['K1079'] = 'Bloglink_getGroupList_{MemberID}_{PageNo}';


//博客个人主页我的标签
$config['K2013'] = 'get_myTag_Count_{UserIDs}';
$config['K2014'] = 'get_myTagList_{UserIDs}';

//博客个人主页最新评论
$config['K2015'] = 'get_myNewestComment_Count_{UserIDs}';
$config['K2016'] = 'get_myNewestComment_{UserIDs}';



//留言板
$config['K1091'] = 'Blogarticle_getMemberMessageList_{UserID}_{PageNo}_{StartDate}_{EndDate}';
$config['K1090'] = 'Blogarticle_getMemberMessageList_Count_{UserID}_{StartDate}_{EndDate}';
//留言板
//start我的关注，我的粉丝

$config['K1094'] = 'Friends_GetFriendsList_Count_{UserID}_{FType}';
$config['K1095'] = 'Friends_GetFriendsList_Count_{UserID}_{FType}_{PageNo}';

//end 我的关注，我的粉丝
$config['K1096'] = 'get_userAuth_{UserID}';


//文章发表调用热门标签
$config['K2017'] = 'get_articleHotTag_inPublish';

//共同关注
$config['K2018'] = 'get_jointlyFriendsNum_{hostUserid}_{guestUserid}';
$config['K2019'] = 'get_jointlyFriends_{hostUserid}_{guestUserid}';
//草稿箱
$config['K2020'] = 'Blogarticle_getMemberDraftboxList_{MemberID}';
//博客更多文章列表 
$config['K2021'] = 'Blogarticle_moreArticle_{StartNo}_{QryCount}_{TagID}_{Type}';
//推荐博客列表 
$config['K2022'] = 'Blogarticle_recommendArticle_{StartNo}_{QryCount}';

$config['K7001'] = '3G_Blogarticle_getMemberArticleList_allCount_{MemberID}';
//收藏文章
$config['SC0001'] = '3G_Blogarticle_garticlecollect_{MemberID}_{AticleId}';
/* End of file cache.php */
/* Location: ./system/config/cache.php */

