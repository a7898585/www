<?php defined('IN_OLCMS') or exit('No permission resources.'); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="x-ua-compatible" content="ie=7" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php if(isset($SEO['title']) && !empty($SEO['title'])) { ?><?php echo $SEO['title'];?><?php } ?><?php echo $SEO['site_title'];?></title>
        <meta name="keywords" content="<?php echo $SEO['keyword'];?>">
            <meta name="description" content="<?php echo $SEO['description'];?>">
                <?php echo mobile_agent();?>
                <link href="<?php echo CSS_PATH;?>css.css" rel="stylesheet" type="text/css" />
                <script type="text/javascript" src="<?php echo JS_PATH;?>jquery.min.js"></script>
                <script type="text/javascript" src="<?php echo JS_PATH;?>jquery.sGallery.js"></script>
                <script type="text/javascript" src="<?php echo JS_PATH;?>search_common.js"></script>
                <script type="text/javascript" src="<?php echo JS_PATH;?>change_tag.js"></script>
                <meta name="baidu_union_verify" content="795e9d13a40af895676c5bb907490857">
                    <meta name="baidu-site-verification" content="WIAY03iL2t" />
                </head>
                <body>
                    <div class="main">
                        <div class="top">
                            <div class="logo"><a href="/"><img src="<?php echo IMG_PATH;?>img/logo.gif" /></a></div>
                            <div class="language">
                                <div class="language_tit">
                                    <!-- <a target="_blank" href="#">登陆</a>&nbsp;|&nbsp;<a target="_blank" href="#">免费注册</a>&nbsp;|&nbsp; --><a href="javascript:window.external.AddFavorite('http://www.manhuacheng.com/', '看漫画软件-漫画城')" target="_self">收藏本站</a>&nbsp;<a href="/index.php?m=feedback" target="_blank">用户反馈</a>
                                </div>

                                <div class="headinfo">
                                    <div class="search_content">					
                                        <form  type="get" name="searchform" id="searchform" action="/index.php">
                                            <input type="hidden" value="search" name="m">
                                                <input type="hidden" value="index" name="c">
                                                    <input type="hidden" value="init" name="a">
                                                        <input type="hidden" value="0" id="search_type" name="search_type">
                                                            <!-- <input type="hidden" id="typeid" value="18" name="typeid"> -->
                                                            <input type="text" value="<?php if(empty($q)) { ?>请输入关键字<?php } else { ?><?php echo $q;?><?php } ?>" onmouseover="javascript:if(this.value=='请输入关键字'){this.value='';this.select();}" name="q" id="q">

                                                                <a id="search_submit" href="javascript:void(0)" onclick="document.getElementById('search_type').value='1';document.getElementById('searchform').submit();" style="right: 85px;">搜名称</a>
                                                                <a id="search_submit" href="javascript:void(0)" onclick="document.getElementById('search_type').value='2';document.getElementById('searchform').submit();">搜作者</a>

                                                                <!-- <a id="search_submit" href="javascript:;">搜作者</a> -->
                                                                </form>
                                                                </div>				
                                                                <div class="">
                                                                </div>
                                                                </div>
                                                                </div>
                                                                <div style="background:none;" class="mainborder">
                                                                    <div class="menu">    	
                                                                        <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=6b0d3485ffde39a464420b24c26f49ac&action=category&catid=0&num=25&order=listorder+ASC\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'category')) {$data = $content_tag->category(array('catid'=>'0','order'=>'listorder ASC','limit'=>'25',));}?>
                                                                        <ul class="nav-site">
                                                                            <li <?php if($_SERVER['REQUEST_URI'] == '/' || $_SERVER['REQUEST_URI'] == '/index.php') { ?>class="on"<?php } ?>><a href="<?php echo siteurl();?>"><span>首页</span></a></li>
                                                                            <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                                                                            <li <?php if($catid == $r[catid] || $top_catid == $r[catid] || $top_parentid == $r[catid]) { ?>class="on"<?php } ?>><a href="<?php echo $r['url'];?>"><span><?php echo $r['catname'];?></span></a></li>
                                                                            <?php $n++;}unset($n); ?>
                                                                        </ul>
                                                                        <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                                                                    </div>
                                                                </div>
                                                                </div>
                                                                <?php if($catid==19 || $top_parentid ==19 || $top_catid ==19) { ?>
                                                                <div class="nav_list">
                                                                    <?php $n=1;if(is_array(subcat(19))) foreach(subcat(19) AS $v) { ?>
                                                                    <?php if($v['type']!=0) continue;?>
                                                                    <a href="<?php echo $v['url'];?>"><?php echo $v['catname'];?></a>
                                                                    <?php $n++;}unset($n); ?>
                                                                </div>
                                                                <?php } ?>

                                                                <script>
                                                                    function tosubmit(){
                                                                        var myform=document.getElementByIdx("searchform");
                                                                        myform.submit();
                                                                    }
                                                                </script>
                                                                <!-- <div class="left c666" style="padding-top:5px;">您现在的位置：&nbsp;<a class="LinkPath" href="<?php echo siteurl();?>">首页</a><?php if(!empty($catid) && empty($manhua)) { ?>&nbsp;&gt;&nbsp;<?php echo catpos($catid);?><?php } ?><?php if(!empty($manhua)) { ?>&nbsp;&gt;&nbsp;<?php echo $manhua;?><?php } ?>
                                                                -->
                                                                
                                                                