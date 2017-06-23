<?php if (!defined('THINK_PATH')) exit();?>    <div class="links"><strong>友情链接</strong> 
        <p>
            <?php if(is_array($links)): $i = 0; $__LIST__ = $links;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a href='<?php echo ($vo["url"]); ?>' ><?php echo ($vo["title"]); ?></a><?php endforeach; endif; else: echo "" ;endif; ?>
        </p>
    </div>