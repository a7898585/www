<?php if(!defined('ROOT')) exit('access deny!');

global $config;
//定义socket
$_config['blog_db'] = array(
                                    'hostname' => '172.20.6.222',
                                    'username' => 'fol_www88',
                                    'password' => 'JKJKFU787JJ3ljh',
                                    'database' => 'blog',
                                    
                                    'char_set' => 'latin1',
                                    'dbcollat' => 'utf8_general_ci',
									'db_debug' => true
                                );
                                
$_config['recommendgroup']				 = array('63'=>1,'65'=>2,'66'=>3,'105'=>4); 	//采用，名家，高手，已审

$config['cache']['server']     = array(
							//array('host'=>'memcache7.cache.cnfol.com','port'=>11211),
						   //array('host'=>'memcache8.cache.cnfol.com','port'=>11211)
						   array('host'=>'192.168.15.107','port'=>11211)
                           );
$config['cache']['compress']   =  false;
$config['cache']['expire']     =  86400;     //15分钟过期
$config['cache']['prefix']     =  'CNfol_Blog_';

$config['K1027']	=  'Articlecomment_getArtCommentList_Count_{ArticleID}';
$config['K1014'] 	=  'Blogarticle_getBlogArticleByID_{MemberID}_{ArticleID}';
$config['K1017'] 	=  'Blogarticle_getBlogArticleStatByID_{MemberID}_{ArticleID}';
$config['K1015'] 	=  'Blogarticle_getMemberArticleList_Count_{MemberID}_{StartDate}_{EndDate}';
$config['K1040'] 	=  'Blogarticle_getMemberArticleListIndex_{MemberID}_{SelfRecommend}'; 
$config['K1005']  	=  'Memberblog_getMemberBlogStat_{MemberID}'; 
$config['K1023']	=  'Articlesort_getArticleSortList_AjaxList_{MemberID}';
$config['K1042'] 	=  'Blogarticle_getMemberArticleListSort_Count_{MemberID}_{SortID}';

$config['B307']='B307';//删除评论
$config['B244']='B244';//删除文章

$config['blog_socket']	=  array(
									'key'  => str_pad('',32), 
									//'host' => '172.20.1.107',
									'host' => '172.20.2.198',
									'port' => 6666
								 );
?>
