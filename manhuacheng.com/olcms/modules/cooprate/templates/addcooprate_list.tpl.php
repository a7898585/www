<?php 
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
?>

<div class="pad-lr-10">
<form name="myform" action="?m=cooprate&c=admin_cooprate&a=listorder" method="post">
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
            <tr>
            <th width="5" align="center"><input type="checkbox" value="" id="check_box" onclick="selectall('aid[]');"></th>
			<th width='15' align="center">客户顺序</th>
			<th width="35" align="center"><?php echo L('topdomain')?></th>
			<th width='35' align="center"><?php echo L('dlfilename')?></th>
		
		<th width='35' align="center">操作</th>
            </tr>
        </thead>
    <tbody>
 <?php 
if(is_array($data)){
	foreach($data as $cooprate){
?>   
	<tr>
	<td align="center">
	<input type="checkbox" name="cooid[]" value="<?php echo $cooprate['cooid']?>">
	</td>
	<td><?php echo $cooprate['cooid']?></td>
	<td align="center"><?php echo $cooprate['topdomain']?></td>
	<td align="center"><?php echo $cooprate['dlfilename']?></td>

	<td align="center">
	<?php if ($_GET['s']==1) {?><a href="?m=cooprate&c=admin_cooprate&a=delete&cooid=<?php echo $cooprate['cooid']?>" title="<?php echo L('cooprate_delete')?>"  ><?php }?><?php echo L('cooprate_delete')?><?php if ($_GET['s']==1) {?></a><?php }?> | 
	<!--<a href="javascript:edit('<?php echo $cooprate['cooid']?>', '<?php echo safe_replace($cooprate['topdomain'])?>');void(0);"><?php echo L('edit')?></a>-->
	</td>
	</tr>
<?php 
	}
}
?>
</tbody>
    </table>
  
    <div class="btn"><label for="check_box"><?php echo L('selected_all')?>/<?php echo L('cancel')?></label>
        <?php if($_GET['s']==1) {?>

        <?php } elseif($_GET['s']==2) {?>
        <?php }?>&nbsp;&nbsp;
		</div>  </div>
 <div id="pages"><?php echo $pages;?></div>
</form>
</div>
</body>
</html>
<script type="text/javascript">
function edit(id, title) {
	window.top.art.dialog({id:'edit'}).close();
	window.top.art.dialog({title:'<?php echo L('edit_cooprate')?>--'+title, id:'edit', iframe:'?m=cooprate&c=admin_cooprate&a=edit&cooid='+id ,width:'200px',height:'200px'}, function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;
	var form = d.document.getElementById('dosubmit');form.click();return false;}, function(){window.top.art.dialog({id:'edit'}).close()});
}
</script>