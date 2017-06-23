<?php
defined('IN_ADMIN') or exit('No permission resources.'); 
include $this->admin_tpl('header', 'admin');
?>
<div class="pad-lr-10">
<ul>
            <li style="float:left;margin:20px;"><a href="?m=content&c=create_html&a=category">批量更新栏目页</a></li>
			<li style="float:left;margin:20px;"><a href="?m=content&c=create_html&a=update_desc">批量更新摘要</a></li>
			<li style="float:left;margin:20px;"><a href="?m=content&c=create_html&a=update_urls">批量更新URL</a></li>
			<li style="float:left;margin:20px;"><a href="?m=content&c=create_html&a=show">批量更新内容页</a></li>
			<li style="float:left;margin:20px;"><a href="?m=content&c=create_html&a=batch_hits">批量更新点击数</a></li>
			<li style="float:left;margin:20px;"><a href="?m=content&c=create_html&a=batch_relation">批量更新相关</a></li>
			<li style="float:left;margin:20px;"><a href="?m=content&c=create_html&a=batch_views">更新查看次数</a></li>
			<!-- 
			<li style="float:left;margin:20px;"><a href="?m=content&c=create_html&a=batch_comic">关联采集漫画</a></li>
			<li style="float:left;margin:20px;"><a href="?m=content&c=create_html&a=batch_cartoon">关联采集动漫</a></li> 
			-->
</ul>
</body>
<script type="text/javascript"> 
<!--
window.top.$('#display_center_id').css('display','none');
//-->
</script>
</html>