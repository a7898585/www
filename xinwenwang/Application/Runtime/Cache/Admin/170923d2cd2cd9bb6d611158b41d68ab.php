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
            <script type="text/javascript" src="/Public/Common/js/kindeditor/kindeditor-all-min.js"></script>
<link type="text/css" rel="stylesheet" href="/Public/Common/js/kindeditor/themes/default/default.css"/>
<script type="text/javascript">
    var editor;
    KindEditor.ready(function(K) {
        editor =K.create('textarea[edit="true"]', {
            autoHeightMode : true,
            allowPreviewEmoticons : false,
            urlType : 'absolute',
            allowUpload : false, //允许上传图片
            uploadJson : '/public/upload_img',
            filterMode:false,
            afterCreate : function() {
                this.loadPlugin('autoheight');
            }
        });
    });
    function insertImage(url){
        editor.insertHtml('<img src="'+up_path+url+'" />');
    }
    function uploadFile(id,buttonText){
        $('#'+id).uploadify({
            'buttonClass' : 'am-btn am-btn-default',
            'swf'      : '/Public/Common/js/uploadify/uploadify.swf',
            'uploader' : '/public/upload_photo',
            'fileSizeLimit':'2MB',
            'fileTypeExts' : '*.gif; *.jpg; *.png',
            'fileTypeDesc': 'image files',
            'onUploadProgress' : function(file, bytesUploaded, bytesTotal, totalBytesUploaded, totalBytesTotal) {
                progress = parseInt(totalBytesUploaded / totalBytesTotal * 100, 10);
                progress = progress>100?100:progress;
                if(progress>0&&progress<=100){
                    $('#progress').css('width',progress+'%');
                    if(progress<=100){
                        setTimeout(function(){$('#progress').parent().hide();},1800);
                    }
                }
            },
            'onUploadSuccess' : function(file, data, response) {
                console.log(data);
                data = eval('(' + data + ')');
                addImg(data.url);
                //                $('#'+id+'_val').val(data.url);
                //                $('#'+id+'_show').attr('src',up_path+data.url).show();
                //                $('#'+id+'_show').parent().show();
                //                $('#'+id+'_progress_value').hide();

            },
            'onUploadStart' : function(file) {
                $('#progress').css('width','0%');
                $('#progress').parent().show();
            },
            'onUploadError' : function(file, errorCode, errorMsg, errorString) {
                alert('文件： '+file.name+' 文件失败，请刷新页面后重试。');
            }
        });
    }
    function addImg(img_url){
        var str = '<li>'+
            '<div class="am-gallery-item">'+
            '<input type="hidden" name="img_list[]" value="'+img_url+'" />'+
            '<img src="'+up_path+img_url+'!100x65" style="width:100px;height: 65px"/>'+
            '<h3 class="am-gallery-title">'+
            '<div><a class="am-fl" onclick="insertImage(\''+img_url+'\')">插入</a><a class="am-fr" onclick="$(this).parents(\'li\').remove()">删除</a></div></h3></div></li>';
        $('#img_list').prepend(str);
    }
</script>
<script type="text/javascript">
    $(document).ready(function(){
        uploadFile('photo_url','上传Logo');
    });
</script>
<div class="admin-content">
    <ol class="am-breadcrumb am-breadcrumb-slash" style="padding-bottom: 0;">
        <li><a href="/" class="am-icon-home">首页</a></li>
        <li><a href="/news/news_list">新闻内容</a></li>
        <li class="am-active">详情</li>
    </ol>
    <form id="add_from" method="post" action="/news/news_info?id=2404374">
        <div class="am-margin">
            <ul class="am-tabs-nav am-nav am-nav-tabs">
                <li class="am-active"><a href="#tab1">基本信息</a></li>
            </ul>
            <input type="hidden" value="<?php echo ($info["id"]); ?>" name="id">
            <div class="am-tabs-bd">
                <div class="am-tab-panel am-fade am-in am-active" id="tab1">

                    <div class="am-g am-margin-top">
                        <div class="am-u-sm-2 am-text-right">
                            标题
                        </div>
                        <div class="am-u-sm-6">
                            <input type="text" class="am-form-field am-input-sm" name="title" value="<?php echo ($info["title"]); ?>">
                        </div>
                        <div class="am-u-sm-4">*必填</div>
                    </div>
                    <div class="am-g am-margin-top">
                        <div class="am-u-sm-2 am-text-right">
                            分类
                        </div>
                        <div class="am-u-sm-6">
                            <!--                            <input id="search" type="text" name="type_id" list="searchlist" class="am-form-field am-input-sm" value="<?php echo ($info["type_name"]); ?>+<?php echo ($info["type_id"]); ?>" required />
                                                        <datalist id="searchlist">
                                                            <?php if(is_array($type_list)): foreach($type_list as $key=>$item): ?><option value="<?php echo ($item["title"]); ?>+<?php echo ($item["id"]); ?>" label="<?php echo ($item["title"]); ?>+<?php echo ($item["id"]); ?>" /><?php endforeach; endif; ?>
                                                        </datalist>-->
                            <select name="type_id" class="am-form-field am-input-sm">
                                <option value="0">请选择</option>
                                <?php if(is_array($type_list)): foreach($type_list as $key=>$item): ?><option value="<?php echo ($item["title"]); ?>+<?php echo ($item["id"]); ?>" <?php if(($item["id"]) == $info["type_id"]): ?>selected<?php endif; ?> ><?php echo ($item["title"]); ?>+<?php echo ($item["id"]); ?></option>
                                    <!--<option value="<?php echo ($item["title"]); ?>+<?php echo ($item["id"]); ?>" label="<?php echo ($item["title"]); ?>+<?php echo ($item["id"]); ?>" <?php if(($key) == $info["show_type"]): ?>selected<?php endif; ?> />--><?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="am-u-sm-4">*必填</div>
                    </div>
                    <div class="am-g am-margin-top">
                        <div class="am-u-sm-2 am-text-right">
                            专题分类
                        </div>
                        <div class="am-u-sm-6">
                            <select name="subject_id" class="am-form-field am-input-sm">
                                <option value="0">请选择</option>
                                <?php if(is_array($subject_type_list)): foreach($subject_type_list as $key=>$item): ?><option value="<?php echo ($item["id"]); ?>" <?php if(($item["id"]) == $info["subject_id"]): ?>selected<?php endif; ?> ><?php echo ($item["title"]); ?></option><?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="am-u-sm-4">*必填</div>
                    </div>
                    <div class="am-g am-margin-top">
                        <div class="am-u-sm-2 am-text-right">
                            关键词
                        </div>
                        <div class="am-u-sm-6">
                            <input type="text" class="am-form-field am-input-sm" name="keyword" value="<?php echo ($info["keyword"]); ?>">
                        </div>
                        <div class="am-u-sm-4"></div>
                    </div>
                    <div class="am-g am-margin-top">
                        <div class="am-u-sm-2 am-text-right">
                            来源网站名称
                        </div>
                        <div class="am-u-sm-6">
                            <input type="text" class="am-form-field am-input-sm" name="source_name" value="<?php echo ($info["source_name"]); ?>">
                        </div>
                        <div class="am-u-sm-4">*必填</div>
                    </div>
                    <div class="am-g am-margin-top">
                        <div class="am-u-sm-2 am-text-right">
                            来源地址
                        </div>
                        <div class="am-u-sm-6">
                            <input type="text" class="am-form-field am-input-sm" name="source_url" value="<?php echo ($info["source_url"]); ?>">
                        </div>
                        <div class="am-u-sm-4">*必填</div>
                    </div>

                    <div class="am-g am-margin-top">
                        <div class="am-u-sm-2 am-text-right">
                            图片展示类型
                        </div>
                        <div class="am-u-sm-6">
                            <select name="show_type" class="am-form-field am-input-sm">
                                <option value="0">请选择</option>
                                <?php if(is_array($show_type)): foreach($show_type as $key=>$item): ?><option value="<?php echo ($key); ?>" <?php if(($key) == $info["show_type"]): ?>selected<?php endif; ?> ><?php echo ($item); ?></option><?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="am-u-sm-4"></div>
                    </div>
                    <div class="am-g am-margin-top">
                        <div class="am-u-sm-2 am-text-right">
                            图片上传
                        </div>
                        <div class="am-u-sm-6">
                            <input id="photo_url" type="file" class="btn_pic" value="上传" />
                            <div class="am-progress" style="display: none;">
                                <div class="am-progress-bar" id="progress" style="width: 0%"></div>
                            </div>
                        </div>
                        <div class="am-u-sm-4">*必填</div>
                    </div>
                    <div class="am-g am-margin-top">
                        <div class="am-u-sm-2 am-text-right">
                            &nbsp;
                        </div>
                        <div class="am-u-sm-6">
                            <ul data-am-widget="gallery" class="am-gallery am-avg-sm-2
                                am-avg-md-3 am-avg-lg-4 am-gallery-imgbordered" data-am-gallery="{  }" id="img_list">
                                <?php if(is_array($info["img_list"])): foreach($info["img_list"] as $key=>$item): ?><li>
                                        <div class="am-gallery-item">
                                            <input type="hidden" name="img_list[]" value="<?php echo ($item); ?>" />
                                            <img src="<?php echo ($item); ?>!100x65" style="width:100px;height: 65px"/>
                                            <h3 class="am-gallery-title">
                                                <div><a class="am-fl" onclick="insertImage('<?php echo ($item); ?>')">插入</a><a class="am-fr" onclick="$(this).parents('li').remove()">删除</a></div></h3>
                                        </div>
                                    </li><?php endforeach; endif; ?>
                            </ul>

                        </div>
                        <div class="am-u-sm-4"></div>
                    </div>
                    <div class="am-g am-margin-top">
                        <div class="am-u-sm-2 am-text-right">
                            内容
                        </div>
                        <div class="am-u-sm-10">
                            <textarea name="html" edit="true" id="aaaa" class="am-form-field"><?php echo ($info["html"]); ?></textarea>
                        </div>
                    </div>
                    <div class="am-g am-margin-top">
                        <div class="am-u-sm-2 am-text-right">显示状态</div>
                        <div class="am-u-sm-10">
                            <div class="am-btn-group" data-am-button>
                                <label class="am-btn am-btn-default am-btn-xs <?php if(($info["is_show"]) == "1"): ?>am-active<?php endif; ?>">
                                    <input type="radio" name="is_show" value="1" <?php if(($info["is_show"]) == "1"): ?>checked="checked"<?php endif; ?> id="option1"> Yes
                                </label>
                                <label class="am-btn am-btn-default am-btn-xs <?php if(($info["is_show"]) == "0"): ?>am-active<?php endif; ?>">
                                    <input type="radio" name="is_show" value="0" <?php if(($info["is_show"]) == "0"): ?>checked="checked"<?php endif; ?> id="option3"> No
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="am-g am-margin-top">
                        <div class="am-u-sm-2 am-text-right">最热</div>
                        <div class="am-u-sm-10">
                            <div class="am-btn-group" data-am-button>
                                <label class="am-btn am-btn-default am-btn-xs <?php if(($info["is_hot"]) == "1"): ?>am-active<?php endif; ?>">
                                    <input type="radio" name="is_hot" value="1" <?php if(($info["is_hot"]) == "1"): ?>checked="checked"<?php endif; ?> id="option1"> Yes
                                </label>
                                <label class="am-btn am-btn-default am-btn-xs <?php if(($info["is_hot"]) == "0"): ?>am-active<?php endif; ?>">
                                    <input type="radio" name="is_hot" value="0" <?php if(($info["is_hot"]) == "0"): ?>checked="checked"<?php endif; ?> id="option3"> No
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="am-g am-margin-top">
                        <div class="am-u-sm-2 am-text-right">推荐</div>
                        <div class="am-u-sm-10">
                            <div class="am-btn-group" data-am-button>
                                <label class="am-btn am-btn-default am-btn-xs <?php if(($info["is_tuijian"]) == "1"): ?>am-active<?php endif; ?>">
                                    <input type="radio" name="is_tuijian" value="1" <?php if(($info["is_tuijian"]) == "1"): ?>checked="checked"<?php endif; ?> id="option1"> Yes
                                </label>
                                <label class="am-btn am-btn-default am-btn-xs <?php if(($info["is_tuijian"]) == "0"): ?>am-active<?php endif; ?>">
                                    <input type="radio" name="is_tuijian" value="0" <?php if(($info["is_tuijian"]) == "0"): ?>checked="checked"<?php endif; ?> id="option3"> No
                                </label>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="am-margin">
                <button type="submit" class="am-btn am-btn-primary am-btn-xs">提交保存</button>
                <button type="button" onclick="window.history.back();" class="am-btn am-btn-primary am-btn-xs">返回</button>
            </div>
        </div>
    </form>

</div>




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