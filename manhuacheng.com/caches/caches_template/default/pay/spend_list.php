<?php defined('IN_OLCMS') or exit('No permission resources.'); ?><?php include template('member', 'header'); ?>
<div id="memberArea">
<?php include template('member', 'left'); ?>
<div class="col-auto">
<div class="col-1 ">
<h6 class="title">消费记录</h6>
<div class="content">
<form name="searchform" action="<?php echo APP_PATH;?>index.php?m=pay&c=spend_list&a=init" method="get" >
<input type="hidden" value="pay" name="m">
<input type="hidden" value="spend_list" name="c">
<input type="hidden" value="init" name="a">
		<div class="search">
		订单时间  <?php echo form::date('starttime',format::date($starttime));?> 到   <?php echo form::date('endtime',format::date($endtime));?>
		<?php echo form::select(array(''=>'消费类型', '1'=>'金钱', '2'=>'积分'),$type,'name="type"');?>  
		<input type="submit" value="查询" class="button" name="dosubmit">
		</div>
</form>
<table width="100%" cellspacing="0"  class="table-list">
        <thead>
            <tr>
            <th width="20%">消费内容</th>
            <th width="20%">时间</th>
            <th width="15%">消费类型</th>
            <th width="8%">数量</th>
            </tr>
        </thead>
    <tbody>
	<?php $n=1;if(is_array($list)) foreach($list AS $info) { ?> 
	<tr>
	<td width="20%" align="center"><?php echo $info['msg'];?></td>
	<td  width="20%" align="center"><?php echo format::date($info['creat_at'], 1);?></td>
	<td width="15%" align="center"><?php if($info[type]==1) { ?>金钱<?php } elseif ($info[type]==2) { ?>积分<?php } ?></td>
	<td width="8%" align="center"><?php echo $info['value'];?></td>
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
<?php include template('member', 'footer'); ?>
