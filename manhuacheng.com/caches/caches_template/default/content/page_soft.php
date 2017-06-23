<?php defined('IN_OLCMS') or exit('No permission resources.'); ?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<title><?php if(isset($SEO['title']) && !empty($SEO['title'])) { ?><?php echo $SEO['title'];?><?php } ?><?php echo $SEO['site_title'];?></title>
<meta name="keywords" content="<?php echo $SEO['keyword'];?>"/>
<meta name="description" content="<?php echo $SEO['description'];?>"/>
<LINK media=screen href="<?php echo CSS_PATH;?>3dprotect.css" type=text/css rel=stylesheet >
<LINK media=screen href="<?php echo CSS_PATH;?>style.css" type=text/css rel=stylesheet >
<LINK media=screen href="<?php echo CSS_PATH;?>welcome_css.css" type=text/css rel=stylesheet >
<link rel="stylesheet" href="<?php echo CSS_PATH;?>css_soft.css" type="text/css"/><!--http://static.bengou.com/css2/style.css-->

<script type="text/javascript" src="<?php echo JS_PATH;?>jquery.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>jquery.sGallery.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>search_common.js"></script>

</head>
<body style="background:none;">

<script type="text/javascript" src="<?php echo JS_PATH;?>udb.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>ajax.js"></script> 

<!-- 漫画头部 -->
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH;?>ychange.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH;?>indexjs.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH;?>util2.js"></script>
<script src="<?php echo JS_PATH;?>jquery1.4.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>jquery.jstore.js"></script>

<script language="javascript" type="text/javascript" src="<?php echo JS_PATH;?>tag.js"></script>



<div>
<!-- HTML代码 start-->
<div id="haosou_searchbox">
    <div class="haosou_top">
        <div class="haoso_links" id="haosou_links">
            <a href="http://news.haosou.com/?src=lm&ls=s5fded5a37a">新闻</a>
            <a href="http://www.haosou.com/?src=lm&ls=s5fded5a37a" class="haosou_active">网页</a>
            <a href="http://wenda.haosou.com/?src=lm&ls=s5fded5a37a">问答</a>
            <a href="http://video.haosou.com/?src=lm&ls=s5fded5a37a">视频</a>
            <a href="http://image.haosou.com/?src=lm&ls=s5fded5a37a">图片</a>
            <a href="http://music.haosou.com/?src=lm&ls=s5fded5a37a">音乐</a>
            <a href="http://maps.haosou.com/?src=lm&ls=s5fded5a37a">地图</a>
            <a href="http://ly.haosou.com/?src=lm&ls=s5fded5a37a">良医</a>
            <a href="http://baike.haosou.com/?src=lm&ls=s5fded5a37a">百科</a>
        </div>
    </div>
    <form action="http://www.haosou.com/s" class="haosou_form" method="get" id="haosou_search_form" accept-charset="UTF-8" onsubmit="document.charset='UTF-8'">
        <input type="hidden" name="src" value="lm">
        <input type="hidden" name="ls" value="s5fded5a37a">
        <input type="text" name="q" id="haosou_input" autocomplete="off" x-webkit-speech><input type="submit" id="haosou_submit" value="好搜一下">
    </form>
</div>
<!-- HTML代码 end-->

</div>
<div class="v_main newmh" style="float:none; clear:both; margin:0 auto; width:750px; ">



<div style="clear:both">

<div class="middle">
		<div class="comicswitch">
				<div class="comicswitchheader blue" id="tab_c">
						<h3 class="current"><a href="#" target="_blank" onmouseover="secBoard('tab_c','infolistb',1);">热门漫画</a></h3>
						<h3 class="normal"><a href="#" target="_blank" onmouseover="secBoard('tab_c','infolistb',2);">经典完结漫画</a></h3>
						<h3 class="normal"><a href="#" target="_blank" onmouseover="secBoard('tab_c','infolistb',3);">最新上架漫画</a></h3>
				</div>
				<!--选项卡顶部end-->
				<!--选项卡内容-->
				<div class="comicswitchtext blue">
						<div class="current" id="infolistb_1">
								<ul><?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=43716b33efbe61a9ec354782aff8ae24&action=lists&catid=13&num=10&order=views+ASC\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'lists')) {$data = $content_tag->lists(array('catid'=>'13','order'=>'views ASC','limit'=>'10',));}?>
										<?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
										<li>    
												<div class="comicswitchlist">
														<div class="picbox"> <a href="javascript:void(0);" onclick="window.external.DoViewBook(<?php echo $r['id'];?>)"   class="piceffects"> <img src="<?php echo $r['thumb'];?>" border="0" height="75" width="70" /> </a> </div>
														<a href="javascript:void(0);" onclick="window.external.DoViewBook(<?php echo $r['id'];?>)"  alt="<?php echo $r['title'];?> " class="piceffectstxt"><?php echo $r['title'];?></a> </div>
														
										</li>
										<?php $n++;}unset($n); ?>
										<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
								</ul>
							<br class="clearall" />		</div>

						<div class="normal" id="infolistb_2">
								<ul>
									<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=e342dec7f6febdb1d98e5b08041e29d4&action=lists&catid=13&where=state%3D1&num=10&order=id+ASC\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'lists')) {$data = $content_tag->lists(array('catid'=>'13','where'=>'state=1','order'=>'id ASC','limit'=>'10',));}?>
										<?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
										<li>    
												<div class="comicswitchlist">
														<div class="picbox"> <a href="javascript:void(0);" onclick="window.external.DoViewBook(<?php echo $r['id'];?>)"   class="piceffects"> <img src="<?php echo $r['thumb'];?>"  border="0" height="75" width="70" /> </a> </div>
														<a href="javascript:void(0);" onclick="window.external.DoViewBook(<?php echo $r['id'];?>)"  alt="<?php echo $r['title'];?> "  class="piceffectstxt"><?php echo $r['title'];?></a> </div>
														
										</li>
										<?php $n++;}unset($n); ?>
										<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
								</ul>
								<br class="clearall" />
						</div>



						<div class="normal" id="infolistb_3">
								<ul>
										<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=7491945eb022afe1fd3a6bf72be6d370&action=lists&catid=13&num=10&order=updatetime+DESC\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'lists')) {$data = $content_tag->lists(array('catid'=>'13','order'=>'updatetime DESC','limit'=>'10',));}?>
										<?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
										<li>    
												<div class="comicswitchlist">
														<div class="picbox"> <a href="javascript:void(0);" onclick="window.external.DoViewBook(<?php echo $r['id'];?>)"  title="<?php echo $r['title'];?>" class="piceffects"> <img src="<?php echo $r['thumb'];?>" border="0" height="75" width="70" /> </a> </div>
														<a href="javascript:void(0);" onclick="window.external.DoViewBook(<?php echo $r['id'];?>)"   alt="<?php echo $r['title'];?> "  class="piceffectstxt"><?php echo $r['title'];?></a> </div>
														
										</li>
										<?php $n++;}unset($n); ?>
										<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
								</ul>
								<br class="clearall" />
						</div>
					
				</div>
		</div>
	
		<div class="recommended margintop">
				<div class="recommendedtitle">
						<h2 class="allfloatleft titletxt">漫画推荐</h2>
						<span class="columntitlemore gray"><a href="/top/" >更多漫画推荐</a></span> </div>
						<?php $i=1;?>
						<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=f3b5558801c18ac79242638351e3b90a&action=lists&catid=13&num=52&order=updatetime+DESC\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'lists')) {$data = $content_tag->lists(array('catid'=>'13','order'=>'updatetime DESC','limit'=>'52',));}?>
							<?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
							<?php if($i<3) { ?>
				<div class="recommendedcontent">
				
										
						<div class="recommendedpic allfloatleft"><a href="javascript:void(0);" onclick="window.external.DoViewBook(<?php echo $r['id'];?>)"  class="piceffects" title=" <?php echo $r['title'];?> "><img src="<?php echo $r['thumb'];?>" width='120' height='160' border="0" alt="<?php echo $r['title'];?>"/></a></div>
						<div class="recommendedtxt allfloatright ">
								<h1 class="blue"><a href="javascript:void(0);" onclick="window.external.DoViewBook(<?php echo $r['id'];?>)"  title="<?php echo $r['title'];?>" class="allfloatleft"><?php echo $r['title'];?></a></h1>
								<span class="allfloatright red"><a href="lianzai"  title=" 浏览连载漫画 ">[连载]</a></span>
								<p class="recommendedproperty"><img src="<?php echo IMG_PATH;?>mhhst_i5.gif" /></p>
								<span class="recommendedproperty gray">人气：<span id="hits"><?php echo $r['views'];?></span></span> <span class="recommendedpropertya gray">简介：<span><?php echo $r['description'];?></span></span> <span><a href="javascript:void(0);" onclick="window.external.DoViewBook(<?php echo $r['id'];?>)"  class="button">浏览漫画</a></span> </div>
								
						<br class="clearall" />
				</div>
				<?php } elseif ($i==3) { ?>
				<br class="clearall" />
				<div class="recommendedlist center">
						<ul>
								<li style="width:100px; float:left;">
										<div class="allfloatleft recommendedname blue"><a href="javascript:void(0);" onclick="window.external.DoViewBook(<?php echo $r['id'];?>)" title="<?php echo $r['title'];?> " ><?php echo str_cut($r[title],20,"");?> </a></div>
										
								</li>
						<?php } else { ?>
						       <li style="width:100px; float:left;">
										<div class="allfloatleft recommendedname blue"><a href="javascript:void(0);" onclick="window.external.DoViewBook(<?php echo $r['id'];?>)" title="<?php echo $r['title'];?> " ><?php echo str_cut($r[title],20,"");?></a></div>
										
								</li>
						<?php } ?>
						<?php $i++;?>
                       
						<?php $n++;}unset($n); ?> <div style="clear:both"></div>
					<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
								
						</ul>
				</div>
		</div>
		
		
</div>		

<div class="right">
		
		
	
		<!-- 最近更新漫画 -->
		<div class="rightlist margintop">
				<div class="columntitle">
						<h2 class="allfloatleft titletxta">最近更新漫画</h2>
						</div>
				<div class="updateconten center">
                
						<ul>
						
						<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=7491945eb022afe1fd3a6bf72be6d370&action=lists&catid=13&num=10&order=updatetime+DESC\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'lists')) {$data = $content_tag->lists(array('catid'=>'13','order'=>'updatetime DESC','limit'=>'10',));}?>
										<?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
								<li> 
								<span class="bluepoint"></span> <span class="allfloatleft"> <a  href="javascript:void(0);" onclick="window.external.DoViewBook(<?php echo $r['id'];?>)" ><?php echo str_cut($r[manhua],42,"");?></a> <span class="red"> <a  href="javascript:void(0);" onclick="window.external.DoViewBook(<?php echo $r['id'];?>)" ><?php echo str_cut($r[title],60,"");?></a> </span> </span> <span class="red allfloatright font11"><?php echo date("m-d",$r[updatetime]);?></span> </li>
									<?php $n++;}unset($n); ?>
								<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
						</ul>
                        
                        
						<br class="clearall" />
				</div>
		</div>
		<!-- 最近更新漫画结束 -->
		<!-- 最新收录漫画推荐 -->
		<div class="rightlist margintop">
				<div class="columntitle">
						<h2 class="allfloatleft titletxta">最新收录漫画推荐</h2>
						<span class="columntitlemore gray"><a href="lianzai/" title="连载">连载</a> <a href="wanjie/" title="完结">完结</a></span> </div>
				<div class="updateconten center">
						<ul>

                                <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=7da13c537560631e98a0500b6bf29e14&action=lists&catid=14&num=18&order=id+DESC\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'lists')) {$data = $content_tag->lists(array('catid'=>'14','order'=>'id DESC','limit'=>'18',));}?>
										<?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
										<?php $w="id=$r[manhuaid]"?>
								<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=3deb7069bb13d1a7c902b1bbe007771e&action=lists&catid=13&num=1&where=%24w&order=id+DESC\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'lists')) {$data = $content_tag->lists(array('catid'=>'13','where'=>$w,'order'=>'id DESC','limit'=>'1',));}?>
								<?php $n=1;if(is_array($data)) foreach($data AS $r1) { ?>
								<li><span class="bluepoint"></span><span class="allfloatleft"><a  href="javascript:void(0);" onclick="window.external.DoViewBook(<?php echo $r['manhuaid'];?>)" ><?php echo $r1['title'];?> <?php echo $r['title'];?></a> <span class="blue"><a  href="javascript:void(0);" onclick="window.external.DoViewBook(<?php echo $r['manhuaid'];?>)" ><?php echo $r['listid'];?></a></span></span><span class="gray allfloatright font11"><?php echo date("m-d",$r[updatetime]);?></span></li>
                                 <?php $n++;}unset($n); ?>
								<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
								 <?php $n++;}unset($n); ?>
								<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>

						</ul>

						<br class="clearall" />
				</div>
		</div>
		<!-- 最新收录漫画推荐结束 -->
</div>



   <div class="clearfix"></div>
                     </div>

                  

</div>


<div style=" clear:both"></div>
</div>
 
</div>
<div style="display:none"><script type="text/javascript">
var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3F3fd524fae420e2deb5df42977b294b7c' type='text/javascript'%3E%3C/script%3E"));
</script></div>
</body>