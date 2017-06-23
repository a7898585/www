<?php defined('IN_OLCMS') or exit('No permission resources.'); ?><?php include template('member', 'header'); ?>
<link href="<?php echo CSS_PATH;?>dialog.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH;?>dialog.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH;?>content_addtop.js"></script>
<div id="memberArea">
	<?php include template('member', 'left'); ?>
	<div class="col-auto">
		<div class="col-1 ">
			<h5 class="title"><?php echo L('published_content');?></h5>

			<div class="content">
		
			<table width="100%" cellspacing="0"  class="table-list">
        <thead>
            <tr>
            <th width="30">ID</th>
            <th><?php echo L('title');?></th>
            <th width="80"><?php echo L('category');?></th>
            <th width="80"><?php echo L('adddate');?></th>
            <th width="90"><?php echo L('operation');?></th>
            </tr>
        </thead>
    <tbody>
	<?php $n=1;if(is_array($datas)) foreach($datas AS $info) { ?> 
	<tr>
	<td align="center"><?php echo $info['id'];?></td>
	<td align="left"><a href="<?php echo $info['url'];?>" target="_blank"><?php echo $info['title'];?></a> <?php if($info[status]==0) { ?><font color="red">退稿</font><?php } ?></td>
	<td align="center"><a href="<?php if(strpos($CATEGORYS[$info['catid']][url],'http://')===false) { ?><?php echo $siteurl;?><?php } ?><?php echo $CATEGORYS[$info['catid']]['url'];?>" target="_blank"><?php echo $CATEGORYS[$info['catid']]['catname'];?></a></td>
	<td align="center"><?php echo date('Y-m-d',$info['inputtime']);?></td>
	<td align="center"><?php if($info[status]==99) { ?><font color="#1D94C7"><?php echo L('pass');?></font><?php } else { ?><a href="index.php?m=member&c=content&a=edit&catid=<?php echo $info['catid'];?>&id=<?php echo $info['id'];?>"><font color="red"><?php echo L('edit');?></font></a><?php } ?></td>
	</tr>
	<?php $n++;}unset($n); ?>
    </tbody>
    </table>

 <div id="pages"> <?php echo $pages['lists'];?></div>

			</div>
			<span class="o1"></span><span class="o2"></span><span class="o3"></span><span class="o4"></span>
		</div>
	</div>
</div>
<div class="clear"></div>
<script language="JavaScript">
<!--
	function c_c(catid) {
		location.href='index.php?m=member&c=content&a=published&catid='+catid;
	}
//-->
</script>
<?php include template('member', 'footer'); ?>