<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header', 'admin');
?>
<div class="pad-lr-10">

<form name="myform" id="myform" action="?m=link&c=link&a=listorder" method="post" >
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th width="35" align="center"><input type="checkbox" value="" id="check_box" onclick="selectall('id[]');"></th>
			<th width="35%"><?php echo L('feedback_title')?></th>
			<th width="35%"><?php echo L('feedback_customer')?></th>
			<th width="10%" align="center"><?php echo L('IP')?></th>
			<th width="15%" align="center"><?php echo L('feedback_updatetime')?></th>
		</tr>
	</thead>
<tbody>
<?php
if(is_array($infos)){
	foreach($infos as $info){
		?>
	<tr>
		<td align="center" width="35"><input type="checkbox" name="id[]" value="<?php echo $info['id']?>"></td>
		<td><?php echo $info['title']?></td>
		<td><?php echo $info['customer'];?></td>
		<td align="center" width="12%"><?php echo $info['ip'];?></td>
		<td align="center" width="10%"><?php echo date('Y-m-d H:i:s',$info['updatetime']);?></td>
	</tr><tr><td colspan="5"><div class="explain-col"> 内容：<?php echo $info['content'];?></div></td></tr>
	<?php
	}
}
?>
</tbody>
</table>
</div>
<div class="btn"> 
<input type="submit" class="button" name="dosubmit" onClick="document.myform.action='?m=feedback&c=feedback&a=delete'" value="<?php echo L('delete')?>"/></div>
<div id="pages"><?php echo $pages;?></div>
</form>
</div>
<script type="text/javascript">

function edit(id, name) {
	window.top.art.dialog({id:'edit'}).close();
	window.top.art.dialog({title:'<?php echo L('edit')?> '+name+' ',id:'edit',iframe:'?m=link&c=link&a=edit&linkid='+id,width:'700',height:'450'}, function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;var form = d.document.getElementById('dosubmit');form.click();return false;}, function(){window.top.art.dialog({id:'edit'}).close()});
}
function checkuid() {
	var ids='';
	$("input[name='linkid[]']:checked").each(function(i, n){
		ids += $(n).val() + ',';
	});
	if(ids=='') {
		window.top.art.dialog({content:"<?php echo L('before_select_operations')?>",lock:true,width:'200',height:'50',time:1.5},function(){});
		return false;
	} else {
		myform.submit();
	}
}
//向下移动
function listorder_up(id) {
	$.get('?m=link&c=link&a=listorder_up&linkid='+id,null,function (msg) { 
	if (msg==1) { 
	//$("div [id=\'option"+id+"\']").remove(); 
		alert('<?php echo L('move_success')?>');
	} else {
	alert(msg); 
	} 
	}); 
} 
</script>
</body>
</html>
