<?php defined('IN_OLCMS') or exit('No permission resources.'); ?><?php $rg = getboxname('12','region');?>
<?php $SEO['title']="漫画_".$rg[$area]."漫画_最新".$rg[$area]."漫画_".$rg[$area]."在线漫画-漫画城";?>
<?php $SEO['keyword']="漫画,".$rg[$area]."漫画,最新".$rg[$area]."漫画,".$rg[$area]."在线漫画，好看的".$rg[$area]."漫画";?>
<?php $SEO['description']="漫画城在线漫画网为您提供最全、最新的".$rg[$area]."漫画、最新".$rg[$area]."漫画、".$rg[$area]."在线漫画、好看的".$rg[$area]."漫画，漫画城在线漫画网是国内更新速度最快、漫画资源最全的专业在线漫画网站！";?>
<?php $footer_txt = "漫画城".$rg[$area]."频道为您提供最全、最新的".$rg[$area]."漫画、最新".$rg[$area]."漫画、".$rg[$area]."在线漫画、好看的".$rg[$area]."漫画，漫画城在线漫画网是国内更新速度最快、漫画资源最全的专业漫画网站！"?>
<?php include template("content","header"); ?>
<?php include template("content","mh_nav"); ?>
	<div class="left c666">您现在的位置：&nbsp;<a class="LinkPath" href="/">首页</a>&nbsp;&gt;&nbsp;<a class="LinkPath" href="/manhua">在线漫画</a>&nbsp;&gt;&nbsp;<?php echo $rg[$area];?>漫画列表</div>
    <div class="middle_div1">
    	<div class="middle_div1_le" style="border:1px solid #AEDD5E">
        	<div class="jingdian_tit">
            	<i class="icon"></i>
            	<h3><?php echo $rg[$area];?>漫画列表</h3>
            </div>
            <div class="jingdian_txt">
            	<ul>
			 <?php $n=1;if(is_array($mhdata)) foreach($mhdata AS $r) { ?>
				<li>
                       <a target="_blank" title="<?php echo $r['title'];?>" href="/manhua<?php echo $r['id'];?>"><img width='140' height='170' src="<?php echo $r['thumb'];?>"></a>
                       <a target="_blank" title="<?php echo $r['title'];?>" href="/manhua<?php echo $r['id'];?>"><?php echo str_cut($r[title],35,'');?></a>
                </li>
			 <?php $n++;}unset($n); ?>
                </ul>
            </div>
            <div id="search_fy" class="sy_tbb" style="margin:0;"><?php echo $pages;?></div>
        </div>
		<?php include template("content","block_right"); ?>
    </div>

<?php include template("content","footer"); ?>