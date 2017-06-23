<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html class="no-js">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title> Admin 新闻王</title>
        <meta name="description" content="">
        <meta name="keywords" content="user">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta name="renderer" content="webkit">
        <meta http-equiv="Cache-Control" content="no-siteapp" />
        <link rel="icon" type="image/png" href="/Public/Admin/i/favicon.png">
        <link rel="apple-touch-icon-precomposed" href="/Public/Admin/i/app-icon72x72@2x.png">
        <meta name="apple-mobile-web-app-title" content="Amaze UI" />
        <link rel="stylesheet" href="/Public/Admin/css/amazeui.min.css"/>
        <link rel="stylesheet" href="/Public/Admin/css/admin.css">
        <link type="text/css" rel="stylesheet" href="/Public/Common/js/uploadify/uploadify.css"/>

        <!--[if lt IE 9]>
        <script src="http://libs.baidu.com/jquery/1.11.1/jquery.min.js"></script>
        <script src="http://cdn.staticfile.org/modernizr/2.8.3/modernizr.js"></script>
        <script src="/Public/Admin/js/polyfill/rem.min.js"></script>
        <script src="/Public/Admin/js/polyfill/respond.min.js"></script>
        <script src="/Public/Admin/js/amazeui.legacy.js"></script>
        <![endif]-->

        <!--[if (gte IE 9)|!(IE)]><!-->
        <script src="//upcdn.b0.upaiyun.com/libs/jquery/jquery-2.0.3.min.js"></script>
        <script src="/Public/Admin/js/amazeui.min.js"></script>
        <!--<![endif]-->
        <script type="text/javascript" src="/Public/Common/js/uploadify/jquery.uploadify.js"></script>

        <script type="text/javascript">
            var up_path = "<?php echo C('YOU_PAI_YUN');?>";
        </script>
    </head>
    <body>
        <!--[if lte IE 9]>
        <p class="browsehappy">你正在使用<strong>过时</strong>的浏览器，Amaze UI 暂不支持。 请 <a href="http://browsehappy.com/" target="_blank">升级浏览器</a>
            以获得更好的体验！</p>
        <![endif]-->

        <header class="am-topbar admin-header">
            <div class="am-topbar-brand">
                <strong>新闻王</strong> <small>后台管理</small>
            </div>
            <button class="am-topbar-btn am-topbar-toggle am-btn am-btn-sm am-btn-success am-show-sm-only" data-am-collapse="{target: '#topbar-collapse'}"><span class="am-sr-only">导航切换</span> <span class="am-icon-bars"></span></button>

            <div class="am-collapse am-topbar-collapse" id="topbar-collapse">
                <ul class="am-nav am-nav-pills am-topbar-nav am-topbar-right admin-header-list">
                    <li><a href="javascript:;"><span class="am-icon-envelope-o"></span> 收件箱 <span class="am-badge am-badge-warning">5</span></a></li>
                    <li class="am-dropdown" data-am-dropdown>
                        <a class="am-dropdown-toggle" data-am-dropdown-toggle href="javascript:;">
                            <span class="am-icon-users"></span> 管理员 <span class="am-icon-caret-down"></span>
                        </a>
                        <ul class="am-dropdown-content">
                            <li><a href="#"><span class="am-icon-user"></span> 资料</a></li>
                            <li><a href="#"><span class="am-icon-cog"></span> 设置</a></li>
                            <li><a href="#"><span class="am-icon-power-off"></span> 退出</a></li>
                        </ul>
                    </li>
                    <li><a href="javascript:;" id="admin-fullscreen"><span class="am-icon-arrows-alt"></span> <span class="admin-fullText">开启全屏</span></a></li>
                </ul>
            </div>
        </header>

        <div class="am-cf admin-main">
            <!-- sidebar start -->
            <div class="admin-sidebar">
                <ul class="am-list admin-sidebar-list">
                    <li><a href="/"><span class="am-icon-home"></span> 首页</a></li>
                    <li class="admin-parent">
                        <a class="am-cf <?php if(($nav) != "news"): ?>am-collapsed<?php endif; ?>" data-am-collapse="{target: '#collapse-nav'}"><span class="am-icon-file"></span> 新闻管理 <span class="am-icon-angle-right am-fr am-margin-right"></span></a>
                        <ul class="am-list admin-sidebar-sub am-collapse <?php if(($nav) == "news"): ?>am-in<?php endif; ?>" id="collapse-nav" style="<?php if(($nav) != "news"): ?>height: 0px;<?php endif; ?>">
                            <li><a href="/news/news_type" class="am-cf"><span class="am-icon-check"></span> 新闻分类<span class="am-icon-star am-fr am-margin-right admin-icon-yellow"></span></a></li>
                            <li><a href="/news/news_city_type"><span class="am-icon-puzzle-piece"></span> 城市分类</a></li>
                            <li><a href="/news/news_subject"><span class="am-icon-puzzle-piece"></span> 专题分类</a></li>
                            <li><a href="/news/news_list"><span class="am-icon-th"></span> 新闻列表<span class="am-badge am-badge-secondary am-margin-right am-fr">24</span></a></li>

                        </ul>
                    </li>
                    <li class="admin-parent">
                        <a class="am-cf <?php if(($nav) != "dingyue"): ?>am-collapsed<?php endif; ?>" data-am-collapse="{target: '#dingyue-nav'}"><span class="am-icon-file"></span> 订阅管理 <span class="am-icon-angle-right am-fr am-margin-right"></span></a>
                        <ul class="am-list admin-sidebar-sub am-collapse <?php if(($nav) == "dingyue"): ?>am-in<?php endif; ?>" id="dingyue-nav" style="<?php if(($nav) != "dingyue"): ?>height: 0px;<?php endif; ?>">
                            <li><a href="/dingyue/types" class="am-cf"><span class="am-icon-check"></span> 订阅分类<span class="am-icon-star am-fr am-margin-right admin-icon-yellow"></span></a></li>
                            <li><a href="/dingyue/lists"><span class="am-icon-puzzle-piece"></span> 订阅列表</a></li>
                            <!--<li><a href="/news/news_list"><span class="am-icon-th"></span> 新闻列表<span class="am-badge am-badge-secondary am-margin-right am-fr">24</span></a></li>-->

                        </ul>
                    </li>
                    <li class="admin-parent">
                        <a class="am-cf <?php if(($nav) != "dingyue"): ?>am-collapsed<?php endif; ?>" data-am-collapse="{target: '#app-nav'}"><span class="am-icon-file"></span> 其他管理 <span class="am-icon-angle-right am-fr am-margin-right"></span></a>
                        <ul class="am-list admin-sidebar-sub am-collapse <?php if(($nav) == "dingyue"): ?>am-in<?php endif; ?>" id="app-nav" style="<?php if(($nav) != "dingyue"): ?>height: 0px;<?php endif; ?>">
                            <li><a href="/app/lists" class="am-cf"><span class="am-icon-check"></span> 应用更新<span class="am-icon-star am-fr am-margin-right admin-icon-yellow"></span></a></li>
                        </ul>
                    </li>
                    <li class="admin-parent">
                        <a class="am-cf <?php if(($nav) != "user"): ?>am-collapsed<?php endif; ?>" data-am-collapse="{target: '#user-nav'}"><span class="am-icon-user-md"></span> 会员管理 <span class="am-icon-angle-right am-fr am-margin-right"></span></a>
                        <ul class="am-list admin-sidebar-sub am-collapse <?php if(($nav) == "user"): ?>am-in<?php endif; ?>" id="user-nav" style="<?php if(($nav) != "user"): ?>height: 0px;<?php endif; ?>">
                            <li><a href="/users" class="am-cf">会员列表<span class=" am-fr am-margin-right admin-icon-yellow"></span></a></li>
                            <li><a href="/users/tongji" class="am-cf">会员统计<span class=" am-fr am-margin-right admin-icon-yellow"></span></a></li>
                            <li><a href="/users/message" class="am-cf">系统消息<span class=" am-fr am-margin-right admin-icon-yellow"></span></a></li>

                        </ul>
                    </li>
                    <li><a href="/public/log"><span class="am-icon-link"></span> 日志系统</a></li>
                    <li><a href="/friends/all"><span class="am-icon-link"></span> 友情链接</a></li>
                    <li><a href="/feedback/all"><span class="am-icon-table"></span> 反馈中心</a></li>
                    <li><a href="/public/login_out"><span class="am-icon-sign-out"></span> 注销</a></li>
                </ul>

                <div class="am-panel am-panel-default admin-sidebar-panel">
                    <div class="am-panel-bd">
                        <p><span class="am-icon-bookmark"></span> 公告</p>
                        <p>时光静好，与君语；细水流年，与君同。—— Amaze UI</p>
                    </div>
                </div>

                <div class="am-panel am-panel-default admin-sidebar-panel">
                    <div class="am-panel-bd">
                        <p><span class="am-icon-tag"></span> wiki</p>
                        <p>Welcome to the Amaze UI wiki!</p>
                    </div>
                </div>
            </div>
            <!-- sidebar end -->

            <!-- content start -->
            <div class="admin-content">
    <ol class="am-breadcrumb am-breadcrumb-slash" style="padding-bottom: 0;">
        <li><a href="/" class="am-icon-home">首页</a></li>
        <li><a href="/news/news_type">城市分类</a></li>
        <li class="am-active">列表</li>
    </ol>

    <div class="am-g">
        <form action="/news/news_city_type" method="get">
            <div class="am-u-md-6 am-cf">
                <div class="am-fl am-cf">
                    <div class="am-btn-toolbar am-fl">
                        <div class="am-btn-group am-btn-group-xs">
                            <a href="javascript:editNewsType();" class="am-btn am-btn-default"><span class="am-icon-plus"></span> 新增</a>
                        </div>

                    </div>
                </div>
            </div>
            <div class="am-u-md-3 am-cf">
                <div class="am-fr">
                    <div class="am-input-group am-input-group-sm">
                        <input type="text" class="am-form-field" name="name" value="<?php echo ($_GET['name']); ?>">
                        <span class="am-input-group-btn">
                            <button class="am-btn am-btn-default" type="submit">搜索</button>
                        </span>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="am-g">
        <form class="am-form">
            <div class="am-u-sm-12">
                <table class="am-table am-table-striped am-table-hover table-main">
                    <thead>
                        <tr>
                            <th class="table-check"><input type="checkbox" /></th>
                            <th class="table-id">编号</th>
                            <th class="table-title">名称</th>
                            <th class="table-type">默认</th>
                            <th class="table-author">显示</th>
                            <th class="table-date">数量</th>
                            <th class="table-set">操作</th>
                        </tr>

                    </thead>
                    <tbody>
                    <?php if(is_array($list)): foreach($list as $key=>$item): ?><tr>
                            <td><input type="checkbox" /></td>
                            <td><?php echo ($item["id"]); ?></td>
                            <td><?php echo ($item['title']); ?></td>
                            <td><?php echo ($item['is_default']); ?></td>
                            <td><?php echo ($item['is_show']); ?></td>
                            <td><?php echo ($item['count']); ?></td>
                            <td>
                                <div class="am-btn-toolbar">
                                    <div class="am-btn-group am-btn-group-xs">
                                        <a href="javascript:editNewsType(<?php echo ($item["id"]); ?>);" class="am-btn am-btn-default am-btn-xs am-text-secondary"><span class="am-icon-pencil-square-o"></span> 编辑</a>
                                        <a href="javascript:delNewsCityType(<?php echo ($item["id"]); ?>);" class="am-btn am-btn-default am-btn-xs am-text-danger"><span class="am-icon-trash-o"></span> 删除</a>

                                    </div>
                                </div>
                            </td>
                        </tr><?php endforeach; endif; ?>
                    </tbody>
                </table>
                <div class="am-cf">
                    共 <?php echo (count($list)); ?> 条记录
                    <div class="am-fr">
                        <?php echo ($pager); ?>
                    </div>
                </div>
                <hr />
                <p>注：请勿删除!!!</p>
            </div>
        </form>
    </div>
</div>
<div class="am-modal " tabindex="-1" id="your-modal">
    <div class="am-modal-dialog">
        <div class="am-modal-hd">Modal 标题
            <a href="javascript: void(0)" class="am-close am-close-spin" data-am-modal-close>&times;</a>
        </div>
        <div class="am-modal-bd">
            Modal 内容。
        </div>
    </div>
</div>



<script type="text/javascript">
    function editNewsType(id){
        $.ajax({
            type: "GET",
            url: "/news/add",
            dataType: "html",
            data: { id: id,is_city:'1'},
            success:function(html){
                $('#your-modal').find('.am-modal-bd').html(html);
                $('#your-modal').modal('toggle');
            }
        });
    }
    function delNewsCityType(id){
        if(confirm('是否删除')){
            location.href="/news/delNewsCityType?id="+id;
        }
    }
    $(document).ready(function(){

    });
</script>
            <!-- content end -->

        </div>

        <footer>
            <hr>
            <p class="am-padding-left">© 2014 AllMobilize, Inc. Licensed under MIT license.</p>
        </footer>
        <div class="am-modal am-modal-loading am-modal-no-btn" tabindex="-1" id="loading">
            <div class="am-modal-dialog">
                <div class="am-modal-hd">正在载入...</div>
                <div class="am-modal-bd">
                    <span class="am-icon-spinner am-icon-spin"></span>
                </div>
            </div>
        </div>
        <script src="/Public/Admin/js/app.js"></script>

        <script type="text/javascript">
            $(document).ready(function(){
                //        uploadFile('photo_url','上传Logo');
                $('body').ajaxStart(function(){
                    $('#loading').modal('open');
                });
                $("#loading").ajaxStop(function(){
                    $('#loading').modal('close');
                });
            });

        </script>
    </body>
</html>