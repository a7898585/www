<?php defined('IN_OLCMS') or exit('No permission resources.'); ?><?php $SEO['title']= $search_q."|".$search_q."漫画|最新".$search_q."漫画|".$search_q."漫画在线观看 - 漫画城"?>
<?php $SEO['keyword']= $search_q.",".$search_q."漫画,".$search_q."在线漫画,".$search_q."漫画下载,".$search_q."漫画全集";?>
<?php $SEO['description']= $search_q.",".$search_q."漫画,".$search_q."在线漫画,".$search_q."漫画下载,".$search_q."漫画全集为".$search_q."漫画的漫画迷提供最全、最新、最快的连载漫画。";?>
<?php include template('content', 'header'); ?>

    <div class="middle_div1">
    	<div class="middle_div1_le">
        	<div class="xg">
            	<div class="xg_tit"><h3>“<?php echo $search_q;?>”相关信息</h3></div>
            	<div class="xg_mh">
                	<div class="xg_mh_tit">
                    	<i class="icon"></i>
            			<h3>漫画</h3>
                    </div>
                    <div class="xg_mh_txt">
                    	<h3>最近更新</h3>
                        <div class="mh_copy">
							<iframe src="<?php echo APP_PATH;?>index.php?m=search&c=index&a=init&q=<?php echo $search_q;?>&typeid=18&iframe=1&pagesize=12&search_type=<?php echo $search_type;?>" width="100%"  onload="this.height=search_iframe1.document.body.scrollHeight"  name="search_iframe1" id="search_iframe1" frameborder="0" scrolling="no"></iframe>
                        </div>                   
                    </div>
                </div>
            </div>
            <div class="xg_dm">
                	<div class="xg_dm_tit">
                    	<i class="icon"></i>
            			<h3>动漫</h3>
                    </div>
                    <div class="xg_dm_txt">
                    	<h3>最近更新</h3>
                        <div class="dm_copy">
							<iframe src="<?php echo APP_PATH;?>index.php?m=search&c=index&a=init&q=<?php echo $search_q;?>&typeid=21&iframe=1&pagesize=10&search_type=<?php echo $search_type;?>" width="100%" onload="this.height=search_iframe2.document.body.scrollHeight"  name="search_iframe2" id="search_iframe2" frameborder="0" scrolling="no"></iframe>
                        </div>                   
                    </div>
                </div>
                 <div class="xg_dm">
                	<div class="xg_dm_tit">
                    	<i class="icon"></i>
            			<h3>资讯</h3>
                    </div>
                    <div class="zx">
                    	<div class="zx_copy">
                            <div class="zx_copy_txt">
							<iframe src="<?php echo APP_PATH;?>index.php?m=search&c=index&a=init&q=<?php echo $search_q;?>&typeid=24&iframe=1&pagesize=12" width="100%" onload="this.height=search_iframe3.document.body.scrollHeight"  name="search_iframe3" id="search_iframe3" frameborder="0" scrolling="no"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        <div class="middle_div1_ri">
        	<div class="tu"><?php include template("content","250ad"); ?></div>
            <div class="rs_dm">
            	<div class="rs_dm_tit"><h3>热搜漫画</h3></div>
                <div class="rs_dm_txt">
                	<ul>
					<?php $class= array(1=>'first',2=>'second',3=>'third')?>
					<?php $i=1;?>
					<?php $n=1;if(is_array($manhua)) foreach($manhua AS $r) { ?>
                    	<li><i class="<?php echo $class[$i];?>"><?php echo $i;?></i><a title="<?php echo $r['title'];?>" target="_blank" href="manhua<?php echo $r['id'];?>"><?php echo str_cut($r[title],40,'...');?></a></li>
						<?php $i++?>
					<?php $n++;}unset($n); ?>
                    </ul>
                </div>
            </div>
            <div class="rs_dm">
            	<div class="rs_dm_tit"><h3>热搜动漫</h3></div>
                <div class="rs_dm_txt">
                	<ul>
						<?php $class= array(1=>'first',2=>'second',3=>'third')?>
						<?php $i=1;?>
						<?php $n=1;if(is_array($dongman)) foreach($dongman AS $r) { ?>
							<li><i class="<?php echo $class[$i];?>"><?php echo $i;?></i><a title="<?php echo $r['title'];?>" target="_blank" href="dongman<?php echo $r['id'];?>"><?php echo str_cut($r[title],40,'...');?></a></li>
							<?php $i++?>
						<?php $n++;}unset($n); ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<?php include template('content', 'footer'); ?>