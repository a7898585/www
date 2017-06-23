<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>
<div class="pad-lr-10">
<form action="?m=tags&c=tags&a=edit&tagid=<?php echo $_GET['tagid']?>" method="post" id="myform">
	<table width="100%"  class="table_form">
	<tr>
    <th width="120">关键字：</th>
    <td class="y-bg"><input type="text" name="info[tag]" value="<?php echo $data['tag']?>" /></td>
  </tr>
  <tr>
    <th width="120">附加状态码</th>
    <td class="y-bg"><input type="text" name="info[style]" value="<?php echo $data['style']?>" /></td>
  </tr>
  <tr>
    <th width="120">使用次数</th>
    <td class="y-bg"><input type="text" name="info[usetimes]" value="<?php echo $data['usetimes']?>" /></td>
  </tr>
    <tr>
    <th>最后使用时间</th>
    <td class="y-bg"><?php echo form::date("info[lastusetime]",date('Y-m-d H:i:s', $data['lastusetime']),1,1)?></td>
  </tr>
  <tr>
    <th>点击量</th>
    <td class="y-bg"><input type="text" name="info[hits]" value="<?php echo $data['hits']?>" /></td>
  </tr>
  <tr>
    <th>最后点击时间</th>
    <td class="y-bg"><?php echo form::date("info[lasthittime]",date('Y-m-d H:i:s', $data['lasthittime']),1,1)?></td>
  </tr>
  <tr>
  <th><input type="hidden"  name="tagid" value="<?php echo $_GET['tagid']?>" /></th>
  <td> <input type="submit" id="dosubmit" name="dosubmit" value="<?php echo L('submit')?>" /></td>
  </tr>
</table>


   
</form>

</div>
</body>
</html>