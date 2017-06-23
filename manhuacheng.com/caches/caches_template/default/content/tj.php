<?php defined('IN_OLCMS') or exit('No permission resources.'); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=7" />
        <title>olcms - 后台管理中心</title>
        <link href="/statics/css/reset.css" rel="stylesheet" type="text/css" />
        <link href="/statics/css/system.css" rel="stylesheet" type="text/css" />
        <link href="/statics/css/table_form.css" rel="stylesheet" type="text/css" />
        <link href="/statics/css/dialog.css" rel="stylesheet" type="text/css" />
        <script language="javascript" type="text/javascript" src="/statics/js/dialog.js"></script>
        <link rel="stylesheet" type="text/css" href="/statics/css/style/styles1.css" title="styles1" media="screen" />
        <link rel="alternate stylesheet" type="text/css" href="/statics/css/style/styles2.css" title="styles2" media="screen" />
        <link rel="alternate stylesheet" type="text/css" href="/statics/css/style/styles3.css" title="styles3" media="screen" />
        <link rel="alternate stylesheet" type="text/css" href="/statics/css/style/styles4.css" title="styles4" media="screen" />
        <script language="javascript" type="text/javascript" src="/statics/js/jquery.min.js"></script>
        <script language="javascript" type="text/javascript" src="/statics/js/admin_common.js"></script>
        <script language="javascript" type="text/javascript" src="/statics/js/styleswitch.js"></script>
    </head>
    <body>
        <div class="pad-10">
            <div class="content-menu ib-a blue line-x">
                <a class="add fb" href="javascript:;" onclick=javascript:openwinx('?m=content&c=content&a=add&menuid=&modelid=3&catid=0', '')><em>添加内容</em></a>　
                <a href="?m=content&c=content&a=init&modelid=3&catid=0" class=on><em>审核通过222</em></a><span>|</span>
                <a href="javascript:;" onclick="javascript:$('#searchid').css('display', '');"><em>搜索</em></a> 
            </div>
            <form name="myform" id="myform" action="" method="post" >
                <div class="table-list">
                    <table width="100%">
                        <thead>
                            <tr>
                            <th>搜索内容</th>
                            <th>来源</th>
                            <th>类型</th>
                            <th>结果</th>
                            <th>时间</th>
                        </tr>
                            
                        <?php $n=1;if(is_array($list)) foreach($list AS $r) { ?>
                        <tr >
                            <td align='center' ><?php echo $r['search'];?></td>
                            <td align='center'><?php echo $r['froms'];?></td>
                            <td align='center'><?php echo $r['types'];?></td>
                            <td align='center'><?php echo $r['res'];?></td>
                            <td align='center'><?php echo $r['times'];?></td>
                        </tr>
                        <?php $n++;}unset($n); ?>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <div class="btn"><label for="check_box">全选/取消</label>
                        <input type="button" class="button" value="排序" onclick="myform.action = '?m=content&c=content&a=listorder&dosubmit=1&modelid=3&catid=0&steps=0';
                                myform.submit();"/>
                        <input type="button" class="button" value="删除" onclick="myform.action = '?m=content&c=content&a=delete&dosubmit=1&modelid=3&catid=0&steps=0';
                                return confirm_delete()"/>
                        <input type="button" class="button" value="推送" onclick="push();" disabled/>
                        <input type="button" class="button" value="批量移动" onclick="myform.action = '?m=content&c=content&a=remove&modelid=3&catid=0';
                                myform.submit();"/>
                    </div>
                    <div id="pages"></div>
                </div>
            </form>
        </div>

    </body>
</html>
