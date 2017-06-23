<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no, minimal-ui">
        <link rel="shortcut icon" href="/Public/Home/images/favicon.ico" type="image/x-icon">

        <meta name="baidu-site-verification" content="eKtLexAbn4" />
        <meta property="qc:admins" content="2550121366601675671676375" />
        <?php echo mobile_agent();?>
        <title><?php echo ($seo["t"]); ?></title>
        <meta name="keywords" content="<?php echo ($seo["k"]); ?>"/>
        <meta name="description" content="<?php echo ($seo["d"]); ?>"/>
        <link rel="stylesheet" type="text/css" href="/Public/Home/css/main_news.css" />
        <link rel="stylesheet" type="text/css" href="/Public/Home/css/detail_news.css"/>
        <link rel="stylesheet" type="text/css" href="/Public/Home/css/base_ebda236.css">
        <link rel="stylesheet" type="text/css" href="/Public/Home/css/core_f6e82af.css">
        <link rel="stylesheet" type="text/css" href="/Public/Home/css/home_7d6d27a.css">
        <link rel="stylesheet" type="text/css" href="/Public/Home/css/center.css">
        <link rel="stylesheet" type="text/css" href="/Public/Home/css/hotcomment_23e50de.css">
        <link rel="stylesheet" type="text/css" href="/Public/Mobile/js/laypage/skin/laypage.css" />

        <script type="text/javascript">
            var login_user = <?php echo intval($USER['id']);?>;
        </script>
    </head>
    <body>
<div id="wrapper">
    <div id="pagelet-nav">
        <div class="nav-inner clearfix">
            <div class="nav-logo">
                <a href="<?php echo ($base_url); ?>" class="logo-box" ga_event="nav_icon">
                    <img src="/Public/Home/images/logo.jpg" class="logo">
                </a>
            </div>
            <div class="nav-title">
                <ul class="clearfix">
                    <li>
                        <a data-node="home" ga_event="nav_index" class="btn <?php if($navtap == 'index'): ?>selected<?php endif; ?>" href="<?php echo ($base_url); ?>">
                            <span>首页</span>
                        </a>
                    </li>
                    <li>
                        <a data-node="dynamic" ga_event="nav_dynamic" class="btn <?php if($navtap == 'updates'): ?>selected<?php endif; ?>" href="<?php echo ($base_url); ?>/updates">
                            <span>动态</span>
                        </a>
                    </li>
                    <li>
                        <a data-node="dynamic" ga_event="nav_dynamic" class="btn <?php if($navtap == 'subject'): ?>selected<?php endif; ?>" href="<?php echo ($base_url); ?>/subject">
                            <span>专题</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="nav-subtitle">
                <ul class="nav-list clearfix">
                    <?php if(!empty($USER)): ?><li data-node="username" class="username-box nav-item">
                            <a href="<?php echo ($base_url); ?>/user/subscribe/" class="btn user-head" id="user_head">
                                <img src="<?php echo setUpUrl($USER['head_pic']);?>">
                                <span><?php echo ($USER["username"]); ?></span>
                            </a>
                            <i class="line"></i><div class="layer">
                                <ul>
                                    <li><a ga_event="nav_my_message" href="<?php echo ($base_url); ?>/user/subscribe/?msg=dingyue" class="layer-item">我的订阅</a></li>
                                    <li><a ga_event="nav_my_message" href="<?php echo ($base_url); ?>/user/subscribe/?msg=collect" class="layer-item">我的收藏</a></li>
                                    <li><a ga_event="nav_my_message" href="<?php echo ($base_url); ?>/user/subscribe/?msg=comment" class="layer-item">我的评论</a></li>
                                    <li><a ga_event="nav_my_message" href="<?php echo ($base_url); ?>/user/subscribe/?msg=friends" class="layer-item">我的好友</a></li>
                                    <li><a href="javascript:;" onclick="uppwd()" class="layer-item">修改密码</a></li>
                                    <li><a href="javascript:;" ga_event="nav_loginout" class="layer-item">退出账号</a></li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item"><a class="btn " href="javascript:;" ga_event="nav_loginout">退出</a>
                        </li><?php endif; ?>
                    <?php if(empty($USER)): ?><li class="username-box nav-item" data-node="username">
                            <a ga_event="nav_login" class="btn user-head" href="javascript:;" data-node="login">
                                <span>登录</span>
                            </a>
                            <i class="line"></i>
                            <a ga_event="nav_register" class="btn " href="javascript:;" data-node="register">
                                <span>注册</span>
                            </a>
                        </li><?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
    <?php echo W('Home/nav',array('nav'=>$nav));?>
<div id="container" class="bg-white clearfix">
    <div class="container-main">
        <div class="container-main">
            <div id="pagelet-feedlist">
                <p data-node="alertMsg" class="alert-msg" style="">
                    <img src="/Public/Home/images/loading_50c5e3e.gif">
                    <span>推荐数据加载中...</span>
                </p>
                <div data-node="unread" class="unread" style="display: none;">
                    <div class="inner">
                        <span>您有未读新闻，点击查看</span>
                    </div>
                </div>
                <ul data-node="listBox" id="listBox">
                    <?php if(is_array($list)): foreach($list as $key=>$item): switch($item["show_type"]): case "1": ?><li data-node="item" class="item2 clearfix">
                                <div ga_event="feed_item" style="width: 100%;" class="info">
                                    <p class="title ">
                                        <a target="_blank" ga_event="feed_title" href="<?php echo ($item["url"]); ?>">
                                            <?php echo ($item["title"]); ?>
                                        </a>
                                    <?php if(item.is_hot == 1 ): ?><img src="/Public/Home/images/images/hot.png" class="hoticon">
                                        <?php elseif(item.is_tuijian == 1): ?><img src="/Public/Home/images/jian.png" class="hoticon"><?php endif; ?>
                                    
                                    </p>
                                    <p class="desc">
                                        <?php echo ($item["intro"]); ?>
                                    </p>
                                </div><?php break;?>

                        <?php default: ?>
                        <li data-node="item" class="item clearfix">
                            <div ga_label="hasImg" ga_event="feed_item" class="info">
                                <p class="title ">
                                    <a target="_blank" ga_event="feed_title" href="<?php echo ($item["url"]); ?>">
                                        <?php echo ($item["title"]); ?>
                                    </a>
                                </p>
                                <p class="desc">
                                    <?php echo ($item["intro"]); ?>
                                </p>
                            </div>
                            <div class="thumbnail">
                                <div class="img-box">
                                    <a target="_blank" href="<?php echo ($item["url"]); ?>" ga_event="feed_img" class="feed-img">
                                        <img rel="nofollow" src="<?php echo setUpUrl($item['img_list'][0]);?>">
                                    </a>
                                </div>
                            </div><?php endswitch;?>

                            <p <?php if(($item['show_type'] == 1) ): ?>style="width: 100%;"<?php endif; ?> class="footer">
                        <span class="other">
                            <span class="btn-group" data-node="likeGroup">
                                <a title="顶" href="javascript:;" class="btn line" data-groupid="<?php echo ($item["id"]); ?>" data-node="digg" ga_event="feed_item_digg">
                                    <i class="icon icon-like icon-small"></i>
                                    <span data-node="num" class="liked-num"><?php echo ($item["good_sum"]); ?></span>
                                </a><a title="踩" href="javascript:;" class="btn" data-groupid="<?php echo ($item["id"]); ?>" data-node="bury" ga_event="feed_item_bury">
                                    <i class="icon icon-dislike icon-small"></i>
                                    <span data-node="num" class="disliked-num"><?php echo ($item["bad_sum"]); ?></span>
                                </a>
                            </span>
                        </span>
                        <span class="footer-right">
                            <a target="_blank" href="<?php echo ($item["url"]); ?>" class="btn datetime">
                                <span data-publishtime="<?php echo ($item["update_time"]); ?>"><?php echo ($item["show_time"]); ?></span>
                            </a>
                            <a href="javascript:;" class="btn share" data-pic="" data-text="【<?php echo ($item["title"]); ?>】（分享来自 @新闻王官网）" data-desc="来自新闻王 http://www.xinwenwang.com" data-url="<?php echo ($item["url"]); ?>" data-node="share">
                                <i class="icon icon-share"></i>
                                <span class="share-list">
                                    <i title="分享到新浪微博" href="javascript:;" class="icon icon-tsina" data-type="tsina" ga_label="tsina" ga_event="feed_item_share"></i>
                                    <i title="分享到腾讯微博" href="javascript:;" class="icon icon-tqq" data-type="tqq" ga_label="tqq" ga_event="feed_item_share"></i>
                                    <i title="分享到ＱＱ空间" href="javascript:;" class="icon icon-qzone" data-type="qzone" ga_label="qzone" ga_event="feed_item_share"></i>
                                </span>
                            </a>
                        </span>
                        </p>
                        <a data-groupid="<?php echo ($item["id"]); ?>" data-node="close" href="javascript:;" title="不感兴趣" class="btn close" ga_event="feed_item_dislike">
                            <i class="icon icon-close icon-small"></i>
                        </a>
                        </li><?php endforeach; endif; ?>
                </ul>
                <?php echo ($page_html); ?>
            </div>

        </div>
    </div>

    <div class="container-right">
        <div class="main_r_box app">
            <div class="title"><h2>手机应用</h2></div>
            <div class="main_r_cont phoneapp">
                <span><img src="/Public/Home/images/xwwicon.png"></span>
                <p>使用《新闻王》，它会根据你的兴趣自动推荐你喜欢的内容，给你阅读。</p>
                <a href="<?php echo ($base_url); ?>/app_server/" class="btn_download">Anroid</a><a href="<?php echo ($base_url); ?>/app_server/" class="btn_download">iPhone</a>
            </div>
        </div>
        <!--今日点击-->
        <div class="main_r_box clicktoday">
            <div class="title"><h2>点击今日</h2><a href="#">+</a></div>
            <?php echo W('Home/hotNews');?>
        </div>
        <!--广告位-->
        <div class="main_r_box adpic">
            <?php echo W('Ad/common');?>
        </div>
        <!--今日热评-->
        <div id="pagelet-hotcomment">
            <div class="inner main_r_box" data-node="inner">
                <div class="title"><h2>今日热评</h2><a href="#">+</a></div>
                <?php echo W('Home/hotComments',array($type_id));?>
            </div>
        </div>
        <div id="pagelet-feedback">
            <ul>
                <li><a ga_event="gotop" class="btn" href="javascript:;" data-node="back"><i class="icon icon-back"></i> </a></li>
                <li><a ga_event="feedback" class="btn" href="javascript:;" data-node="feedback"><i class="icon icon-comment"></i></a></li>
            </ul>
        </div>
    </div>
</div>
</div>
<script id="hot_comments" type="text/html">
    {{# for(var i = 0, len = d.data_list.length; i < len; i++){ }}
    <li>
        <p class="comment-title">
            <a href="/u{{d.data_list[i].id}}/" ga_event="hotcomment_headimg">
                <img src="{{d.data_list[i].head_pic}}">
            </a>
            <a href="/u{{d.data_list[i].id}}/" class="name" ga_event="hotcomment_name">
                <?php echo ($item['username']); ?>
            </a>
            <a data-id="{{d.data_list[i].id}}" data-node="digg" href="javascript:;" class="btn" ga_event="hotcomment_dig">
                <i class="icon icon-like-blue icon-small"></i>
                <span data-node="likedNum" class="liked-num">{{d.data_list[i].good_sum}}</span>
            </a>
        </p>
        <p class="comment">
            <i class="arrow"></i>
            {{d.data_list[i].title}}
        </p>
        <p class="label">评论于：</p>
        <p class="source">
            <a target="_blank" href="{{d.data_list[i].url}}" ga_event="hotcomment_title">
                {{d.data_list[i].content}}
            </a>
        </p>
    </li>
    {{# } }}
</script>
<script type="text/javascript" src="/Public/Common/js/jquery.js"></script>
<script type="text/javascript" src="/Public/Home/js/layer/layer.min.js"></script>
<script type="text/javascript" src="/Public/Home/js/layer/laytpl.js"></script>
<script type="text/javascript" src="/Public/Common/js/jquery.cookie.js"></script>
<script type="text/javascript">
    //点击展开分类
    $('a[ga_event="feed_setting"]').click(function () {
        if ($('#pagelet-toolbar').attr('class') == '') {
            $(this).find('i').addClass('icon-customize-up');
            $('#pagelet-toolbar').addClass('customize');
            $('.custom-box').show();
        } else {
            $(this).find('i').removeClass('icon-customize-up');
            $('#pagelet-toolbar').removeClass('customize');
            $('.custom-box').hide();
        }
    });
    //点击头部页面关闭分类
    $('#container,#pagelet-nav').click(function () {
        $('a[ga_event="feed_setting"]').find('i').removeClass('icon-customize-up');
        $('#pagelet-toolbar').removeClass('customize');
        $('.custom-box').hide();
    });

    //重载页面
    $('.icon-refresh').click(function () {
        $('.alert-msg').show();
        window.location.reload();
    });

    //点击删除
    $("#defaultCategory li").live("click", function () {
        if ($('.customize').length && $(this).index()) {
            $("#otherCategory").append("<li>" + $(this).html() + "</li>");
            $(this).remove();
            var i = $("#defaultCategory li").length;
            if (i <= 6) {
                $('.ban-msg').removeClass('error').html('点击添加新闻分类');
                sysNav();
            }
            return false;
        } else {
            return true;
        }
    });
    //点击添加
    $("#otherCategory li").live("click", function () {
        if ($('.customize').length) {
            var i = $("#defaultCategory li").length;
            if (i <= 6) {
                $("#defaultCategory").append("<li>" + $(this).html() + "</li>");
                $(this).remove();
                sysNav();
            } else {
                $('.ban-msg').addClass('error').html('分类已满（最多7个），请先删除一些');
            }
            return false;
        } else {
            return true;
        }
    });
    //未读新闻 点击查看
    $('.unread').click(function () {
        window.location.reload();
    });
    //点赞
    $('a[data-node="digg"]').live('click', function () {
        /*        var temp = is_login();
     //        if (temp == false) return false;
         */
        var a = $(this).parent('span.disliked').length;
        var b = $(this).parent('span.liked').length;
        var gid = $(this).attr('data-groupid');
        var c_id = $.cookie('_xw_dz' + gid);
        if (!c_id) {
            $.cookie('_xw_dz' + gid, gid);
        }
        /*无操作*/
        if (!a && !b && !c_id) {
            $(this).parent('span').addClass('liked');
            var liked_num = $(this).find('.liked-num');
            $(liked_num).html(parseInt($(liked_num).html()) + 1);
            $(this).attr('title', '已顶');
            $.post("/news/good", {id: gid}, function (result) {
            });
        }
    });
    //踩楼
    $('a[data-node="bury"]').live('click', function () {
        /*        var temp = is_login();
     //        if (temp == false) return false;
         */       var a = $(this).parent('span.disliked').length;
        var b = $(this).parent('span.liked').length;
        var gid = $(this).attr('data-groupid');
        var c_id = $.cookie('_xw_dz' + gid);
        if (!c_id) {
            $.cookie('_xw_dz' + gid, gid);
        }
        if (!a && !b && !c_id) {
            $(this).parent('span').addClass('disliked');
            var disliked_num = $(this).find('.disliked-num');
            $(disliked_num).html(parseInt($(disliked_num).html()) + 1);
            $(this).attr('title', '已踩');
            $.post("/news/bad", {id: gid}, function (result) {
            });
        }
    });

    //不喜欢此类文章
    $('a[ga_event="feed_item_dislike"]').live('click', function () {
        news_id = $(this).attr('data-groupid');
        $.layer({
            type: 1,
            shade: [0.5, '#000'],
            area: ['auto', 'auto'],
            closeBtn: false,
            shadeClose: true,
            title: false,
            border: [0],
            page: {dom: '.confirm-dialog'}
        });
        return false;
    });
    //不喜欢取消
    $('div[data-node="cancel"]').live('click', function () {
        news_id = 0;
        layer.closeAll();
    });
    //不喜欢确认
    $('div[data-node="ok"]').live('click', function () {
        $.ajax({
            type: 'POST',
            url: '/public/nolike',
            data: {id: news_id},
            dataType: 'json',
            success: function (msg) {
                if (msg.code == 200) {
                    //成功
                }
            }
        });
        layer.closeAll();
    });

    //评论点赞
    $('a[data-node="c_digg"]').live('click', function () {
        /* var temp = is_login();
     if (temp == false) return false;
         */
        var cid = $(this).attr('data-id');
        var c_id = $.cookie('_cm_dz' + cid);
        if (!c_id) {
            $.cookie('_cm_dz' + cid, cid);
            $.ajax({
                type: 'POST',
                url: '/News/usergb',
                data: {id: cid, 'gb': 'good'},
                dataType: 'json',
                success: function (msg) {
                }
            });
            var liked_num = $(this).find('.liked-num');
            $(liked_num).html(parseInt($(liked_num).html()) + 1);
        }
    });
    //评论下一页
    var comment_page = 1;
    $('a[ga_event="hotcomment_change"]').click(function () {
        comment_page++;
        $.ajax({
            type: 'POST',
            url: '/hot_comments',
            data: {p: comment_page},
            dataType: 'json',
            success: function (msg) {
                if (msg.data.data_list.length < 5) {
                    comment_page = 1;
                }
                if (msg.code == 200) {
                    laytpl($('#hot_comments').html()).render(msg.data, function (render) {
                        $('.comment-list').html(render);
                    });
                }
            }
        });
        return false;
    });

    window.scrollTo(0, 0);
    function sysNav() {
        var str = new Array();
        $('#defaultCategory li>a').each(function (t) {
            var k = $(this).attr('data_key');
            var v = $(this).attr('data_value');
            if (k)
                str += k + '_';
        });
        $.ajax({
            type: 'POST',
            url: '/public/sysnav',
            data: {str: str},
            dataType: 'json',
            success: function (msg) {
            }
        });
    }
</script>
<?php if($link_show == 1 ): echo W('Cate/links'); endif; ?>

<div class="company">
    <p>
        <span>© 2015 新闻王 xinwenwang.com</span>
        闽ICP备11014391号-200
    <p>
        <a href="#" class="icp" target="_blank">网络文化经营许可证</a>
        <a href="#" target="_blank">跟帖评论自律管理承诺书 </a>
        违法和不良信息举报电话：010-58733394
        公司名称：厦门粉酷网络科技有限公司
    </p>
</div>
<!--返回顶部-->
<div class="tools">
    <a href="javascript:;" class="back_top" title="返回顶部" onClick="window.scrollTo(0,0); return false;"></a>
    <a href="javascript:feedback();" class="feed_back hidden-xs" title="提点意见" ></a>
</div>
<!--意见反馈-->
<div style="display: none;" class="login-dialog " id="feedback-dialog">
    <div class="login-dialog-header">
        <h3>意见反馈</h3>
    </div>
    <a class="btn-close" data-node="close" ga_event="login-dialog-close">
        <i class="icon icon-close"></i>
    </a>
    <div data-node="inner" class="login-dialog-inner">
        <div class="login-pannel bottom-line">
            <ul>
                <li>
                    <div class="input-group">
                        <div class="input">
                            <label>联系方式（必填）</label>
                            <input type="text" name="feedback-email" placeholder="您的邮箱/QQ号" class="name" >
                        </div>
                    </div>
                </li>
                <li>
                    <div class="input-group">
                        <div class="input">
                            <label>您的意见（必填）</label>
                            <textarea placeholder="请填写您的意见，不超过140字" maxlength="140" class="name" name="feedback-content" style="height:100px;width:280px;"></textarea>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="input-group">
                        <input type="button" value="提交" class="submit-btn" id="feedback-submit">
                        <!--                        <span data-node="msg" class="msg">333</span>-->
                        <!--<a href="javascript:;" class="close" data-node="close">[关闭]</a>-->
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>

<!--不喜欢文章-->
<div style="width: 300px;display: none;" class="confirm-dialog">
    <div data-node="inner" class="confirm-inner">不喜欢这类文章？</div>
    <div class="btn-group">
        <div data-node="cancel" class="btn cancel">取消</div>
        <div data-node="ok" class="btn">确认</div>
    </div>
</div>

<!--登录框-->
<div style="display: none;" id="login-dialog" class="login-dialog">
    <div class="login-dialog-header">
        <h3>邮箱登录</h3>
    </div>
    <a class="btn-close" data-node="close" ga_event="login-dialog-close">
        <!--<i class="icon icon-close"></i>-->X
    </a>
    <div data-node="inner" class="login-dialog-inner">
        <div class="login-pannel bottom-line">
            <ul>
                <li>
                    <div class="input-group">
                        <div class="input">
                            <label>邮箱</label>
                            <input type="text" spellcheck="false" autocomplete="off" placeholder="请使用您的注册邮箱" name="name_or_email" value="<?php echo ($user_email); ?>" class="name">
                        </div>
                    </div>
                </li>
                <li>
                    <div class="input-group">
                        <div class="input">
                            <label>密码</label>
                            <input type="password" spellcheck="false" autocomplete="off" placeholder="密码" data-node="password" name="password" class="password">
                        </div>
                    </div>
                </li>
                <li>
                    <div class="input-group">
                        <input type="checkbox" id="remember_me" style="vertical-align:top" checked="" name="remember_me">
                        <label class="label" for="remember_me">记住账号</label>
                        <!--                        <a href="javascript:;" onclick="forget()">
                                                    忘记密码
                                                </a>-->
                    </div>
                </li>
                <li>
                    <div style="text-align: center;" class="input-group">
                        <input type="button" value="登录" class="submit-btn" id="btn_login">
                        <p data-node="errorMsg" class="error"></p>
                    </div>
                </li>
            </ul>
        </div>
        <div class="login-dialog-header">
            <h3>合作网站帐号登陆</h3>
        </div>
        <div class="login-pannel">
            <ul class="sns_login_list clearfix">
                <li class="sinaweibo">
                    <a data-pid="sina_weibo" data-node="snsLogin" href="javascript:;">
                        <i class="icon"></i>
                        新浪微博
                    </a>
                </li>
                <li class="qzone">
                    <a data-pid="qzone_sns" data-node="snsLogin" href="javascript:;">
                        <i class="icon"></i>
                        QQ空间
                    </a>
                </li>
                <li class="renren">
                    <a data-pid="renren_sns" data-node="snsLogin" href="javascript:;">
                        <i class="icon"></i>
                        人人网
                    </a>
                </li>
                <li class="qqweibo">
                    <a data-pid="qq_weibo" data-node="snsLogin" href="javascript:;" style="margin-right:0;">
                        <i class="icon"></i>
                        腾讯微博
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<!--注册框-->
<div style="display: none;" class="login-dialog " id="register-dialog">
    <div class="login-dialog-header">
        <h3>用户注册</h3>
    </div>
    <a class="btn-close" data-node="close" ga_event="login-dialog-close">
        <i class="icon icon-close"></i>
    </a>
    <div data-node="inner" class="login-dialog-inner">
        <div class="login-pannel bottom-line">
            <ul>
                <li>
                    <div class="input-group">
                        <div class="input">
                            <label>邮箱</label>
                            <input type="text" spellcheck="false" autocomplete="off" placeholder="请输入您的注册邮箱" name="email" class="name">
                        </div>
                    </div>
                </li>
                <li>
                    <div class="input-group">
                        <div class="input">
                            <label>账户</label>
                            <input type="text" spellcheck="false" autocomplete="off" placeholder="请输入您的账户" name="username" class="name">
                        </div>
                    </div>
                </li>
                <li>
                    <div class="input-group">
                        <div class="input">
                            <label>密码</label>
                            <input type="password" spellcheck="false" autocomplete="off" placeholder="密码" data-node="password" name="password" class="password">
                        </div>
                    </div>
                </li>
                <li>
                    <div style="text-align: center;" class="input-group">
                        <input type="button" value="完成注册" class="submit-btn" id="btn_register">
                        <p data-node="errorMsg" class="error"></p>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
<!--忘记密码框-->
<div style="display: none;" class="login-dialog " id="forget-dialog">
    <div class="login-dialog-header">
        <h3>忘记密码</h3>
    </div>
    <a class="btn-close" data-node="close" ga_event="login-dialog-close">
        <i class="icon icon-close"></i>
    </a>
    <div data-node="inner" class="login-dialog-inner">
        <div class="login-pannel bottom-line">
            <ul>
                <li>
                    <div class="input-group">
                        <div class="input">
                            <label>邮箱</label>
                            <input type="text" spellcheck="false" autocomplete="off" placeholder="请输入您的注册邮箱" name="email" class="name">
                        </div>
                    </div>
                </li>
                <li>
                    <div class="input-group">
                        <div class="input">
                            <label>账户</label>
                            <input type="text" spellcheck="false" autocomplete="off" placeholder="请输入您的账户" name="username" class="name">
                        </div>
                    </div>
                </li>
                <li>
                    <div style="text-align: center;" class="input-group">
                        <input type="button" value="提交" class="submit-btn" id="btn_forget">
                        <p data-node="errorMsg" class="error"></p>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
</body>
</html>
<style>
    /*用户反馈*/
    #feed_back{ position:fixed; right:0; bottom:140px; padding:20px; padding-bottom:0; background:#f5f5f5; border: 1px solid #aaa; border-radius: 5px 0 0 5px; border-right: none; z-index:1000; display: none}
    #feed_back form{ width:320px;}
    #feed_back form .controls{ padding-bottom:10px;}
    #feed_back form .form-control{ width:100%; margin-bottom:0}
    #feed_back .feed_back_close{ float:right; padding-top:5px; }

    .tools{ position:fixed; bottom:50px; right:20px; z-index:1000}
    .feed_back,
    .back_top{ display:block; height:32px; width:32px; background:url(http://s0.pstatp.com/image/pgc/01.gif) no-repeat; background-position:0 -64px;  margin-top:5px;}
    .feed_back:hover{ background-position:0 -96px;}
    .back_top{ background-position:0 0;}
    .back_top:hover{ background-position:0 -32px}
</style>

<!--<script type="text/javascript" src="/Public/Common/js/jquery.js"></script>-->
<!--<script type="text/javascript" src="/Public/Common/js/layer/layer.min.js"></script>-->
<!--<script type="text/javascript" src="/Public/Common/js/layer/laytpl.js"></script>-->

<script type="text/javascript">
    $('li[data-node="username"]').mouseover(function(){
        $(this).children('.layer').show();
    }).mouseout(function(){
        $(this).children('.layer').hide();
    });
    function feedback(){
        layer.closeAll();
        $.layer({
            type: 1,
            shade: [0.5, '#000'],
            area: ['auto', 'auto'],
            closeBtn: false,
            shadeClose: true,
            title: false,
            border: [0],
            page: {dom: '#feedback-dialog'}
        });
    }
    function forget(){
        layer.closeAll();
        $.layer({
            type: 1,
            shade: [0.5, '#000'],
            area: ['auto', 'auto'],
            closeBtn: false,
            shadeClose: true,
            title: false,
            border: [0],
            page: {dom: '#forget-dialog'}
        });
    }
    var news_id = 0;
    $(document).ready(function () {
        //分享
        $('.share-list i').live('click', function () {
            var ga_label = $(this).attr('ga_label');
            var title = $(this).parent().parent('.share').attr('data-text');
            var desc = $(this).parent().parent('.share').attr('data-desc');
            var url = $(this).parent().parent('.share').attr('data-url');
            var open_url = '';
            if (ga_label == 'tsina') {
                open_url = 'http://service.weibo.com/share/share.php?url=' + url + '&title=' + title + '&appkey=&searchPic=true';
            } else if (ga_label == 'tqq') {
                open_url = 'http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=' + url + '&title=' + title + '&desc=' + desc + '&summary=&site=';
            } else if (ga_label == "qzone") {
                open_url = 'http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=' + url + '&title=' + title + '&desc=' + desc + '&summary=&site=';
            }
            window.showModalDialog(open_url, window, "dialogWidth:650px;dialogHeight:570px;center:yes;status:no;scroll:no;help:no;");
        });
        $('a[ga_event="nav_login"]').click(function () {
            is_login();
        });
        //注册框
        $('a[ga_event="nav_register"]').click(function () {
            $.layer({
                type: 1,
                shade: [0.5, '#000'],
                area: ['auto', 'auto'],
                closeBtn: false,
                shadeClose: true,
                title: false,
                border: [0],
                page: {dom: '#register-dialog'}
            });
        });
        
        //注册
        $('#btn_register').click(function () {
            var login_dom = $('#register-dialog');
            var username = $(login_dom).find('input[name="username"]').val();
            var email = $(login_dom).find('input[name="email"]').val();
            var password = $(login_dom).find('input[name="password"]').val();
            layer.load('注册中...');
            $.ajax({
                type: 'POST',
                url: '/register',
                data: {username: username, email: email, password: password},
                dataType: 'json',
                success: function (msg) {
                    if (msg.code == 200) {
                        layer.msg('注册成功,跳转中...', 0, 1);
                        window.location.reload();
                    } else {
                        layer.msg(msg.msg);
                    }
                }
            });
        });
        $('#btn_forget').click(function () {
            var login_dom = $('#forget-dialog');
            var username = $(login_dom).find('input[name="username"]').val();
            var email = $(login_dom).find('input[name="email"]').val();
            layer.load('提交中...');
            $.ajax({
                type: 'POST',
                url: '/user/reset_pwd',
                data: {username: username, email: email},
                dataType: 'json',
                success: function (msg) {
                    if (msg.code == 1) {
                        layer.msg('密码修改成功,注意查收邮件，跳转中...', 0, 1);
                        window.location.reload();
                    } else {
                        layer.msg(msg.msg);
                    }
                }
            });
        });
        $('#feedback-submit').click(function () {
            var login_dom = $('#feedback-dialog');
            var username = $(login_dom).find('input[name="feedback-email"]').val();
            var email = $(login_dom).find('textarea[name="feedback-content"]').val();
            if(username==''){
                layer.msg('联系方式不能为空');
                return false;
            }
            if(email==''){
                layer.msg('反馈内容不能为空');
                return false;
            }
            layer.load('提交中...');
            $.ajax({
                type: 'POST',
                url: '/user/feedback',
                data: {contact: username, msg: email},
                dataType: 'json',
                success: function (msg) {
                    if (msg.code == 200) {
                        layer.msg('您的反馈意见提交成功...', 2, 1);
                        window.location.reload();
                        //                        layer.closeAll();
                    } else {
                        layer.msg(msg.msg);
                    }
                }
            });
        });
        //退出登录
        $('a[ga_event="nav_loginout"]').click(function () {
            $.ajax({
                type: 'POST',
                url: '/loginout',
                dataType: 'text',
                success: function (msg) {
                    layer.msg('退出成功,跳转中...', 0, 1);
                    window.location.reload();
                }
            });
        });

        //登录
        $('#btn_login').click(function () {
            var login_dom = $('#login-dialog');
            var name = $(login_dom).find('input[name="name_or_email"]').val();
            var password = $(login_dom).find('input[name="password"]').val();
            if (!name || !password) {
                return false;
            }
            layer.load('登录中...');
            $.ajax({
                type: 'POST',
                url: '/login',
                data: {u: name, p: password},
                dataType: 'json',
                success: function (msg) {
                    if (msg.code == 200) {
                        layer.msg('登录成功,跳转中...', 0, 1);
                        window.location.reload();
                    } else {
                        layer.msg(msg.msg);
                    }
                }
            });
        });
        function keyDown(e) {
            var currKey = 0, e = e || event;
            if (e.keyCode == 13)
                $('#btn_login').click();
        }

        document.onkeydown = keyDown;


        /*        $('a[ga_event="hotcomment_name"]').live('click', function () {
            if (!$(this).hasClass('liked')) {
                $(this).html(parseInt($(this).text()) + 1).addClass('liked');
            }
            return false;
        });
         */

        //弹出层的关闭按钮
        $('a[ga_event="login-dialog-close"]').on('click', function () {
            layer.closeAll();
        });

    });

    function is_login() {
        if (parseInt(login_user) < 1) {
            $.layer({
                type: 1,
                shade: [0.5, '#000'],
                area: ['auto', 'auto'],
                closeBtn: false,
                shadeClose: true,
                title: false,
                border: [0],
                page: {dom: '#login-dialog'}
            });
            return false;
        } else {
            return true;
        }
    }

    $(document).scroll(function(){
        if($(window).scrollTop() > 70){
            $("#pagelet-toolbar").css({"position":"fixed","top":"0","left":"0"});
        }else{
            $("#pagelet-toolbar").css("position","relative");
        }
    });
</script>
<script src="http://res.chazidian.com/all/xiaozhan.js" type="text/javascript"></script>
<div style="display: none">
    <script language="javascript" type="text/javascript" src="http://js.users.51.la/17700581.js"></script>
</div>
<noscript>
<a href="http://www.51.la/?17700581" target="_blank" style="display:none;">
    <img alt="&#x6211;&#x8981;&#x5566;&#x514D;&#x8D39;&#x7EDF;&#x8BA1;" src="http://img.users.51.la/17700581.asp" style="border:none" />
</a>
</noscript>