<table cellpadding="2" cellspacing="1" width="98%">
	<tr> 
      <td width="100">操作&nbsp;&nbsp;<input type="checkbox" name="setting[isfunc]" value="1"></td>
      <td>数据源：<?php echo form::select($exists_field,'','name="setting[source]"')?> 处理函数：<?php echo form::radio($func,'','name="setting[func]"')?></td>
    </tr>
	<tr> 
      <td width="100">取值范围</td>
      <td><input type="text" name="setting[minnumber]" value="1" size="5" class="input-text"> - <input type="text" name="setting[maxnumber]" value="" size="5" class="input-text"></td>
    </tr>
	<tr> 
      <td>小数位数：</td>
      <td>
	  <select name="setting[decimaldigits]">
	  <option value="-1">自动</option>
	  <option value="0" selected>0</option>
	  <option value="1">1</option>
	  <option value="2">2</option>
	  <option value="3">3</option>
	  <option value="4">4</option>
	  <option value="5">5</option>
	  </select>
    </td>
    </tr>
	<tr> 
      <td>默认值</td>
      <td><input type="text" name="setting[defaultvalue]" value="<?php echo $defaultvalue?>" size="40" class="input-text"></td>
    </tr>
</table>