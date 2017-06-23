<?php if (!defined('THINK_PATH')) exit();?><div class="main_r_cont ">
    <h3><a href="<?php echo ($jrdj_list[0]['url']); ?>"><?php echo ($jrdj_list[0]['title']); ?></a></h3>
    <div class="text">
        <?php if(!empty($jrdj_list[0]['img_list'])): ?><span><img rel="nofollow" src="<?php echo setUpUrl($jrdj_list[0]['img_list'][0]);?>"></span>
            <?php else: ?>
            <span><img rel="nofollow" src="/Public/Home/images/xwwicon.png"></span><?php endif; ?>
        <p><?php echo (msubstr($jrdj_list[0]['intro'],0,50)); ?>..<a href="<?php echo ($jrdj_list[0]['url']); ?>">【详情】</a></p>
    </div>
    <ul>
        <?php if(is_array($jrdj_list)): $i = 0; $__LIST__ = $jrdj_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(($i) != "1"): ?><li>
                <a href="<?php echo ($vo["url"]); ?>"><?php echo ($vo["title"]); ?></a>
            </li><?php endif; endforeach; endif; else: echo "" ;endif; ?>            
    </ul>
</div>