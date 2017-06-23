<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');
$models = getcache('model', 'commons');
$sitelist = getcache('sitelist', 'commons');
?>

<div class="pad_10">
<form action="?m=tags&c=tags&a=create" method="post" name="myform" >
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">
	<tr>
		<th width="20%">请选择需要重建的站点：</th>
		<td><select name="siteid">
		<option value="<?php echo $sitelist['siteid'];?>"><?php echo $sitelist['name'];?></option>
		</select></td>
	</tr>
	<tr>
		<th width="20%">请选择需要重建的模型：</th>
		<td><select name="modelid" id="modelid" >
		<option value="0">所有模型</option>
		<?php foreach($models as $model_v){ ?>
		<option value="<?php echo $model_v['modelid'] ?>"><?php echo $model_v['name'];?></option>
		<?php } ?>
		</select></td>
	</tr>
	<tr>
		<th></th>
		<td><input type="submit" name="dosubmit" id="dosubmit" value=" <?php echo L('submit')?> "></td>
	</tr>

</table>
</form>
</div>
</body>
</html> 