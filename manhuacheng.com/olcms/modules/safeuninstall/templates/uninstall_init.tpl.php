<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="pad-lr-10">




<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			
			<th align="left">id</th>
			<th align="left">卸载原因</th>
			<th align="left">其他原因</th>
			<th align="left">电话号码</th>
			<th align="left">eMail</th>
			<th align="left">IM</th>
			<th align="left">卸载时间</th>
			
		</tr>
	</thead>
<tbody>
<?php
	foreach($uninstalllist as $k=>$v) {
?>
    <tr>
		<td align="left"><?php echo$v['id']?></td>
		<td align="left"><?php
		$a=unserialize($v['unreason']);
		if(is_array($a))
		foreach($a as $v1){echo $msg_arr[$v1].',';}
		else echo  $v['unreason'];
		unset($a);
		?>
		</td>
		<td align="left"><?php echo $v['otherreason']?></td>
		<td align="left"><?php echo$v['phonecode']?></td>
		<td align="left"><?php echo$v['email']?></td>
		<td align="left"><?php echo$v['IM']?></td>
		<td align="left"><?php echo date('Y-m-d H:i:s',$v['date']);?></td>

    </tr>
<?php
	}

?>
</tbody>
</table>


<div id="pages"><?php echo $pages?></div>
</div>

</div>
<script type="text/javascript">
<!--
function edit(id, name) {
	window.top.art.dialog({id:'edit'}).close();
	window.top.art.dialog({title:'<?php echo L('edit').L('member')?>《'+name+'》',id:'edit',iframe:'?m=member&c=member&a=edit&userid='+id,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'edit'}).close()});
}
function move() {
	var ids='';
	$("input[name='userid[]']:checked").each(function(i, n){
		ids += $(n).val() + ',';
	});
	if(ids=='') {
		window.top.art.dialog({content:'<?php echo L('plsease_select').L('member')?>',lock:true,width:'200',height:'50',time:1.5},function(){});
		return false;
	}
	window.top.art.dialog({id:'move'}).close();
	window.top.art.dialog({title:'<?php echo L('move').L('member')?>',id:'move',iframe:'?m=member&c=member&a=move&ids='+ids,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'move'}).data.iframe;d.$('#dosubmit').click();return false;}, function(){window.top.art.dialog({id:'move'}).close()});
}

function checkuid() {
	var ids='';
	$("input[name='userid[]']:checked").each(function(i, n){
		ids += $(n).val() + ',';
	});
	if(ids=='') {
		window.top.art.dialog({content:'<?php echo L('plsease_select').L('member')?>',lock:true,width:'200',height:'50',time:1.5},function(){});
		return false;
	} else {
		myform.submit();
	}
}

function member_infomation(userid, modelid, name) {
	window.top.art.dialog({id:'modelinfo'}).close();
	window.top.art.dialog({title:'<?php echo L('memberinfo')?>',id:'modelinfo',iframe:'?m=member&c=member&a=memberinfo&userid='+userid+'&modelid='+modelid,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'modelinfo'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'modelinfo'}).close()});
}

//-->
</script>
</body>
</html>