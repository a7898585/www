<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<div class="pad-10">
<div class="bk10"></div>

<div class="table-list">
<table width="100%" cellspacing="0">

<form action="?m=content&c=create_html&a=batch_cartoon" method="post" name="myform">
  <input type="hidden" name="dosubmit" value="1"> 
<thead>
</thead>
<tbody style="text-align: center;" height="200" class="nHover td-line">
	<tr> 
      <td><?php echo L('every_time');?> <input type="text" name="pagesize" value="100" size="5"> <?php echo L('information_items');?><input type="button" class="button" name="dosubmit2" value=" <?php echo L('submit_start_update');?> " onclick="myform.submit();"></td>
    </tr>
	</tbody>
	</form>
</table>

</div>
</div>
<script language="JavaScript">
<!--
	window.top.$('#display_center_id').css('display','none');
	function change_model(modelid) {
	
		window.location.href='?m=content&c=create_html&a=batch_views&modelid='+modelid;
	}
//-->
</script>