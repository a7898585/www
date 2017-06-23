<?php if (!defined('THINK_PATH')) exit();?><div id="pagelet-toolbar" class="">
    <div data-node="toolbarInner" class="inner">
        <div class="toolbar-inner clearfix">
            <div class="container clearfix">
                <ul class="hv-list left clearfix" data-node="curCategory">
                    <ul id="defaultCategory" class="hv-list left clearfix">
                        <li data-category="__all__" data-node="category">
                            <a class="item <?php if(($nav) == ""): ?>selected<?php endif; ?>" href="/" ga_label="推荐" ga_event="feed_category">
                                <span class="font-icon-add">+</span>推荐
                            </a>
                        </li>
                        <?php if(is_array($defaultCategory)): foreach($defaultCategory as $key=>$item): ?><li ><a class="item <?php if(($nav) == $key): ?>selected<?php endif; ?>" data_key="<?php echo ($key); ?>" data_value="<?php echo ($item); ?>" href="<?php echo ($base_url); ?>/<?php echo ($key); ?>"><?php echo ($item); ?><span class="font-icon-add">+</span><i class="icon icon-customize-close"></i></a></li><?php endforeach; endif; ?>
                    </ul>
                </ul>
                <ul class="hv-list btn-group clearfix">
                    <li>
                        <a ga_event="feed_setting" href="javascript:;" class="btn" data-node="custom">
                            <i class="icon icon-customize btn-more"></i>
                        </a>
                    </li>
                    <li style="margin-right:0;">
                        <a ga_event="feed_refresh" href="javascript:;" class="btn" data-node="refresh">

                            <i class="icon icon-refresh"></i>
                        </a>
                    </li>
                </ul>
            </div>
            <div data-node="searchBox" class="search-box">
                <form action="/search/" method="get" data-node="searchForm" target="_blank">
                    <div class="input-group clearfix">
                        <input autocomplete="off" ga_event="nav_search_input" data-node="searchInput" name="keyword" type="text" placeholder="请输入搜索关键词" value="<?php echo ($_GET['keyword']); ?>">
                        <div class="btn-submit">
                            <button ga_event="nav_search" type="submit" href="javascript:;">
                                <i class="icon icon-search icon-large"></i>
                            </button>
                        </div>
                    </div>
                </form>
                <div class="layer">
                    <ul data-node="searchMenu">
                    </ul>
                </div>
            </div>
        </div>
        <div class="custom-box" data-node="customBox" style="display: none;">
            <div class="custom-inner">
                <p data-node="msg" class="msg ban-msg">点击添加新闻分类</p>

                <ul id="otherCategory" class="hv-list clearfix">
                    <?php if(is_array($otherCategory)): foreach($otherCategory as $key=>$item): ?><li ><a class="item" data_key="<?php echo ($key); ?>" data_value="<?php echo ($item); ?>"  href="<?php echo ($base_url); ?>/<?php echo ($key); ?>"><?php echo ($item); ?><span class="font-icon-add">+</span><i class="icon icon-customize-close"></i></a></li><?php endforeach; endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>