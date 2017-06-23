<?php 
	defined('IN_ADMIN') or exit('No permission resources.');
	include $this->admin_tpl('header','admin');
?>
<div class="pad_10">
<div class="table-list">
<form name="userform" action="/index.php" method="get" >
<input type="hidden" value="usercount" name="m">
<input type="hidden" value="index" name="c">
<input type="hidden" value="statbytime" name="a">
<div class="explain-col search-form">
请选择时间(默认是当天)  <?php echo form::date('start_time',$start_addtime)?><?php echo L('to')?><?php echo form::date('end_time',$end_addtime)?> 
<input type="submit" value="查看" class="button" name="dosubmit">
</div>
</form>
    <table width="100%" cellspacing="0">
        <thead>
            <tr>
            <th width="10%">渠道商</th>
            <th width="20%">安装量</th>
            <th width="20%">卸载量</th>
           
            </tr>
        </thead>
    <tbody style="text-align:center">
   
<?php
 
foreach ($rbc as $k=>$v){  
	?>
	<tr>
		<td><?php echo $canal[$v['parentID']]; ?></td>
		
		<td><?php echo  $v['num'] ?></td>
		
		<td><?php echo  $v['num1'] ?></td>
	</tr>
<?php 	 } ?>
	 <tr style="border-top:2px solid black">
		 <td>总共</td>
		 <td><?php echo $m['num'] ?></td>
		 <td><?php echo $n['num'] ?></td>
	 </tr>
    </tbody>
    </table>

 <div id="pages"> <?php echo $pages ?></div>
</div>
</div>
</form>
</body>
</html>
<script type="text/javascript">
<!--
	function discount(id, name) {
	window.top.art.dialog({title:'<?php echo L('discount')?>--'+name, id:'discount', iframe:'?m=pay&c=payment&a=discount&id='+id ,width:'500px',height:'200px'}, 	function(){var d = window.top.art.dialog({id:'discount'}).data.iframe;
	var form = d.document.getElementById('dosubmit');form.click();return false;}, function(){window.top.art.dialog({id:'discount'}).close()});
}
function detail(id, name) {
	window.top.art.dialog({title:'<?php echo L('discount')?>--'+name, id:'discount', iframe:'?m=pay&c=payment&a=public_pay_detail&id='+id ,width:'500px',height:'550px'});
}
//-->
</script>