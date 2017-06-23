<?php defined('IN_OLCMS') or exit('No permission resources.'); ?><?php include template('member', 'header'); ?>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH;?>admin_common.js"></script> 
<link href="<?php echo CSS_PATH;?>dialog.css" rel="stylesheet" type="text/css" /> 
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH;?>dialog.js"></script>
<div id="memberArea">
<?php include template('member', 'left'); ?>
<div class="col-auto">
<div class="col-1 ">
<h6 class="title">收件箱</h6>
<div class="content"> 
<form name="myform" id="myform" action="<?php echo APP_PATH;?>index.php?m=message&c=index&a=delete" method="post" onsubmit="checkuid();return false;">
<table width="100%" cellspacing="0"  class="table-list">
        <thead>
            <tr>
            <th width="5%"><input type="checkbox" value="" id="check_box" onclick="selectall('messageid[]');"></th>
            <th width="35%">标 题</th>
            <th width="8%">回 复</th>
            <th width="15%">发件人</th>
            <th width="15%">发送时间</th>
            </tr>
        </thead>
    <tbody>
	<?php $n=1;if(is_array($infos)) foreach($infos AS $info) { ?> 
	<tr>
	<td width="5%" align="center"><input type="checkbox" name="messageid[]" value="<?php echo $info['messageid'];?>"></td>
	<td  width="35%" align=""><a href="<?php echo APP_PATH;?>index.php?m=message&c=index&a=read&messageid=<?php echo $info['messageid'];?>"><?php if($info['status']==1) { ?><font color=red><b><?php echo $info['subject'];?></b></font><?php } else { ?><?php echo $info['subject'];?><?php } ?></a></td>
	<td width="8%" align="center"><?php if($info['status']!=2) { ?><?php } ?><?php echo $info['reply_num'];?></td>
	<td width="15%" align="center"><?php echo $info['send_from_id'];?></td>
	<td width="15%" align="center"><?php echo date('Y-m-d H:i:s',$info['message_time']);?> </a>
	</tr>
	<?php $n++;}unset($n); ?>
	
    </tbody>
    </table>
<div class="btn"><a href="#" onClick="javascript:$('input[type=checkbox]').attr('checked', true)">全选</a>/<a href="#" onClick="javascript:$('input[type=checkbox]').attr('checked', false)">取消</a> 
<input name="submit" type="submit" class="button" value="删除选中" onClick="return confirm('确认要删除 『 选中 』 吗？')">&nbsp;&nbsp;</div> 

</form>   

<div id="pages"><?php echo $pages['lists'];?></div>
</div>
<span class="o1"></span><span class="o2"></span><span class="o3"></span><span class="o4"></span>
</div>

</div>
</div>
<script type="text/javascript">
function read(id, name) {
	window.top.art.dialog({id:'sell_all'}).close();
	window.top.art.dialog({title:'查看详情'+name+' ',id:'edit',iframe:'?m=message&c=index&a=read&messageid='+id,width:'700',height:'450'}, function(){var d = window.top.art.dialog({id:'see_all'}).data.iframe;var form = d.document.getElementById('dosubmit');form.click();return false;}, function(){window.top.art.dialog({id:'see_all'}).close()});
}
function checkuid() {
	var ids='';
	$("input[name='messageid[]']:checked").each(function(i, n){
		ids += $(n).val() + ',';
	});
	if(ids=='') {
		window.top.art.dialog({content:'请选择再执行操作',lock:true,width:'200',height:'50',time:1.5},function(){});
		return false;
	} else {
		myform.submit();
	}
}

</script>
<?php include template('member', 'footer'); ?>

