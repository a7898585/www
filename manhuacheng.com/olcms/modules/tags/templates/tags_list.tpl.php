<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
?>
<div class="pad_10">
<div class="table-list">
<form action="" method="get">
<input type="hidden" name="m" value="tags" />
<input type="hidden" name="c" value="tags" />
<input type="hidden" name="a" id="action" value="delete" />
    <table width="100%" cellspacing="0">
        <thead>
		<tr>
		<th width="50"><input type="checkbox" value="" id="check_box" onclick="selectall('tagid[]');"></th>
		<th width="50">排序</th>
		<th>关键字</th>
		<th>使用次数</th>
		<th>最后使用时间</th>
		<th>点击次数</th>
		<th>最近访问时间</th>
		<th>相关操作</th>
		</tr>
        </thead>
        <tbody>
<?php 
if(is_array($data))
	foreach($data as $v){
?>
<tr>
<td width="50" align="center"><input type="checkbox" value="<?php echo $v['tagid']?>" name="tagid[]"></td>
<td><input type="text" name="listorder[]" value="<?php echo $v['listorder']?>" size="5" /></td>
<td align="center"><?php echo $v['tag']?></td>
<td align="center"><?php echo $v['usetimes']?></td>
<td align="center"><?php echo date('Y-m-d H:i:s', $v['lastusetime'])?></td>
<td align="center"><?php echo $v['hits']?></td>
<td align="center"><?php echo date('Y-m-d H:i:s', $v['lasthittime'])?></td>
<td align="center"><a href="?m=tags&c=tags&a=edit&tagid=<?php echo $v['tagid']?>">修改</a> | <a href="?m=tags&c=tags&a=delete&tagid=<?php echo $v['tagid']?>" onclick="return confirm('<?php echo htmlspecialchars(new_addslashes(L('confirm', array('message'=>$v['tag']))))?>')">删除</a></td>
</tr>
<?php }  ?>
</tbody>
</table>
<div class="btn">
<label for="check_box"><?php echo L('select_all')?>/<?php echo L('cancel')?></label> <input type="submit" class="button" name="dosubmit" value="<?php echo L('delete')?>" onclick="return confirm('您确认删除么，该操作无法恢复！')"  /> <input type="submit" class="button" name="dosubmit" onclick="$('#action').val('listorder')" value=" 更新排序 " 
</div>
</from>
</div>
</div>
<div id="pages"><?php echo $pages?></div>
</body>
</html>