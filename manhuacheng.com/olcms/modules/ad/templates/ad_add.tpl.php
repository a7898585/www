<?php 
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
?>
<div class="pad-10">
<form method="post" action="?m=ad&c=admin_ad&a=add" name="myform" id="myform">
<table class="table_form" width="100%" cellspacing="0">
<tbody>
	<tr>
		<th width="80"><strong><?php echo L('ad_title')?></strong></th>
		<td><input name="ad[title]" id="title" class="input-text" type="text" size="50" ></td>
	</tr>
	<tr>
		<th><strong><?php echo L('startdate')?>：</strong></th>
		<td><?php echo form::date('ad[starttime]', date('Y-m-d H:i:s'), 1)?></td>
	</tr>
	<tr>
		<th><strong><?php echo L('enddate')?>：</strong></th>
		<td><?php echo form::date('ad[endtime]', $an_info['endtime'], 1);?></td>
	</tr>
	<tr>
		<th><strong><?php echo L('ad_url')?></strong></th>
		<td><textarea name="ad[url]" id="url"></textarea></td>
	</tr>
	<tr>
  		<th><strong><?php echo L('available_style')?>：</strong></th>
        <td>
		<?php echo form::select($template_list, $info['default_style'], 'name="ad[style]" id="style" onchange="load_file_list(this.value)"', L('please_select'))?> 
		</td>
	</tr>
	<tr>
		<th><strong><?php echo L('template_select')?>：</strong></th>
		<td id="show_template"><script type="text/javascript">$.getJSON('?m=admin&c=category&a=public_tpl_file_list&style=<?php echo $info['default_style']?>&module=ad&templates=show&name=ad, function(data){$('#show_template').html(data.show_template);});</script></td>
	</tr>
	<tr>
		<th><strong><?php echo L('ad_status')?></strong></th>
		<td><input name="ad[passed]" type="radio" value="1" checked>&nbsp;<?php echo L('pass')?>&nbsp;&nbsp;<input name="ad[passed]" type="radio" value="0">&nbsp;<?php echo L('unpass')?></td>
	</tr>
	</tbody>
</table>
<input type="submit" name="dosubmit" id="dosubmit" value=" <?php echo L('ok')?> " class="dialog">&nbsp;<input type="reset" class="dialog" value=" <?php echo L('clear')?> ">
</form>
</div>
</body>
</html>
