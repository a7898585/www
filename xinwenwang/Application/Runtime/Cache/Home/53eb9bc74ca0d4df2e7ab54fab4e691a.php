<?php if (!defined('THINK_PATH')) exit();?><ul data-node="cList" class="comment-list" style="display: block;">
    <?php if(is_array($comments)): foreach($comments as $key=>$item): ?><li>
            <p class="comment-title">
                <a href="javascript:;" >
                    <?php if(empty($item["head_pic"])): ?><img src="/Public/Home/images/default.png">
                        <?php else: ?>
                        <img src="<?php echo setUpUrl($item['head_pic']);?>"><?php endif; ?>
                </a>
                <a href="javascript:;" class="name" ga_event="hotcomment_name">
                    <?php if(empty($item["username"])): $city=get_city_by_ip($item['ip']); ?>
                        <?php echo ($city['city']); ?> ip为 <?php echo ($item["ip"]); ?>的网友
                        <?php else: ?>
                        <?php echo ($item['username']); endif; ?>
                </a>
                <a data-id="<?php echo ($item["id"]); ?>" data-node="c_digg" href="javascript:;" class="btn" ga_event="hotcomment_dig">
                    <i class="icon icon-like-blue icon-small"></i>
                    <span data-node="likedNum" class="liked-num"><?php echo ($item["good_sum"]); ?></span>
                </a>
            </p>
            <p class="comment">
                <i class="arrow"></i>
                <?php echo ($item["content"]); ?>
            </p>
            <p class="label">评论于：</p>
            <p class="source">
                <a target="_blank" href="<?php echo ($item["url"]); ?>" ga_event="hotcomment_title">
                    <?php echo ($item["title"]); ?>
                </a>
            </p>
        </li><?php endforeach; endif; ?>
</ul>