<table cellpadding="2" cellspacing="1" width="98%">
	<tr> 
      <td>允许上传的图片类型</td>
      <td><input type="text" name="setting[upload_allowext]" value="gif|jpg|jpeg|png|bmp" size="40" class="input-text"></td>
    </tr>
	<tr> 
      <td>是否从已上传中选择</td>
      <td><input type="radio" name="setting[isselectimage]" value="1"> 是 <input type="radio" name="setting[isselectimage]" value="0" checked> 否</td>
    </tr>
	<tr> 
      <td>允许同时上传的个数</td>
      <td><input type="text" name="setting[upload_number]" value="10" size=3></td>
    </tr>
	<tr> 
      <td>更多图片相关属性 <a href="javascript:addItem()" title="<?php echo L('add')?>"><span style="color:red;" >+</span></a></td>
      <td><span id="more">
      	<div>key： <input type="text" name="setting[more][0]" value="" size=6>&nbsp;<a href="javascript:;" onclick="descItem(this);"><span style="color:red;" >-</span></a><br /></div>
      	</span></td>
    </tr>
</table>
<script type="text/javascript">
function addItem() {
	var n = $('#more').find('input[name]').length;
	var newOption =  '<div>key： <input type="text" name="setting[more]['+n+']" value="" size=6>&nbsp;<a href="javascript:;" onclick="descItem(this);"><span style="color:red;" >-</span></a></div>';
	$('#more').append(newOption);
}
function descItem(a) {
	$(a).parent().remove();
}
</script>