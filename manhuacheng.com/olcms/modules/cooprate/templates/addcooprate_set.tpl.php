<?php 
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
?>
<div class="pad-10">
<form method="post" action="?m=cooprate&c=admin_cooprate&a=setdefaulturl" name="myform" id="myform">
<table class="table_form" width="100%">
<tbody>
	<tr>
		<th width="80"><?php echo L('defaulturl')?></th>
		<td><input name="defaulturl" id="title" value="" class="input-text" type="text" size="50" >(取消设置的时候可以不填地址)</td>
	</tr>
	<tr>
	<td><input type="submit" name="dosubmit"  value="提交" ></td><td><input type="reset"  value="取消">
	</td></tr>
</tbody>
</table>

</form>
</div>
</body>
</html>
