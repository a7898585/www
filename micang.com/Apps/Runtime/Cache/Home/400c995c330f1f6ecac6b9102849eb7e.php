<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <title><?php echo ($seo["title"]); ?></title>
  <meta name="keywords" content="<?php echo ($seo["key"]); ?>"/>
  <meta name="description" content="<?php echo ($seo["des"]); ?>"/>
  <meta name="copyright" content="网站版权申明"/>
  <link href="/Public/Home/css/layout.css" rel="stylesheet" type="text/css"/>
  <script src="/Public/Common/js/jquery.min.js" type="text/javascript"></script>
  <script src="/Public/Common/js/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
  <script type="text/javascript" src="/Public/Common/js/layer/layer.js"></script>
  <script type="text/javascript" src="/Public/Common/js/trade.js"></script>
  <link href="/Public/Member/css/ui-dialog.css" rel="stylesheet"/>
  <?php if($navtap == 'index'): ?><script src="/Public/Home/js/bktb.js" type="text/javascript"></script><?php endif; ?>
</head>

<body>
<!--顶部通条导航 开始-->
<div class="topnav_bj">
  <div class="topnav">
    <div class="topnav_l">
      <?php if((is_array($_SESSION['MEMBERINFO'])) != "1"): ?>欢迎来到<span>米仓</span>
        <?php else: ?>
        欢迎<span><?php echo ($_SESSION['MEMBERINFO']['username']); ?></span>来到米仓<?php endif; ?>
    </div>
    <div class="topnav_r">
      <?php if((is_array($_SESSION['MEMBERINFO'])) != "1"): ?><a href="<?php echo getDoMain();?>public/login" rel="nofollow">登录</a>
        <span>|</span>
        <a href="<?php echo getDoMain();?>public/register" rel="nofollow">注册</a>
        <?php else: ?>
        <a href="/trolley" rel="nofollow">购物车(<b style="color:red;" id="trolley_num"><?php echo ((isset($trolley_num) && ($trolley_num !== ""))?($trolley_num):0); ?></b>)</a>
        <span>|</span>
        <a href="<?php echo getDoMain('member');?>" rel="nofollow">用户中心</a>
        <span>|</span>
        <a href="<?php echo getDoMain('member');?>logout" rel="nofollow">退出</a><?php endif; ?>

    </div>
  </div>
</div>
<!--顶部通条导航 结束-->
<div class="header">
  <a href="<?php echo getDoMain();?>"><img src="/Public/Home/images/logo.jpg" class="logo" alt="米仓网"/></a>
  <!--搜索 开始-->
  <div class="search">
    <div class="nav_b">
      <ul>
        <li><a href="#" class="navxd">域名直达</a></li>
        <li><a href="#">Whois查询</a></li>
        <li><a href="#">域名分析</a></li>
      </ul>
    </div>
    <input type="text" name="" value="请输入你要查询的域名，例如 xxx.com" onblur="if(this.value=='')this.value='请输入你要查询的域名，例如 xxx.com';" onfocus="if(this.value=='请输入你要查询的域名，例如 xxx.com')this.value='';" class="search_txt"/>
    <input type="submit" name="" value="" class="search_btn"/>
  </div>
  <!--搜索 结束-->
  <div class="header_txt">客服热线<span>4008-767-258</span></div>
</div>
<!--导航 开始-->
<div class="navbg">
  <div class="nav">
    <ul>
      <li><a href="<?php echo getDoMain();?>" class="navc <?php if(($ navtap) == "index"): ?>navxd<?php endif; ?>">首页</a></li>
      <div class="cbtc_btn">
        <div class="cbtc_b">
          <a href="<?php echo getDoMain();?>domain/" class="navc_b <?php if(($ navtap) == "domain_register_query"): ?>navxd<?php endif; ?>">买域名</a>
        </div>
        <div class="cbtc_c">
          <div class="cbtc_c_l">
            <ul>
              <li><a href="<?php echo getDoMain();?>domain/">域名注册</a></li>
              <li><a href="<?php echo getDoMain();?>sell/page?type=2">限时竞价</a></li>
              <li><a href="<?php echo getDoMain();?>sell/page?type=1">一口价</a></li>
              <li><a href="<?php echo getDoMain();?>sell/page?type=3">买家询价</a></li>
              <li><a href="<?php echo getDoMain();?>domain/purchase">域名代购</a></li>
              <li><a href="<?php echo getDoMain('member');?>agency/buy/">域名中介</a></li>
              <li><a href="#">域名分析</a></li>
            </ul>
          </div>
          <div class="cbtc_c_r">
            <dl>
              <dt>域名注册常见问题</dt>
              <dd><a href="#">如何使用代金劵使用说明</a></dd>
            </dl>
          </div>
        </div>
      </div>
      <li><a href="<?php echo getDoMain();?>sell/" class="navc <?php if(($ navtap) == "sell"): ?>navxd<?php endif; ?>">卖域名</a></li>
      <li><a href="" class="navc">查域名</a></li>
      <li><a href="#" class="navc">养域名</a></li>
      <li><a href="<?php echo getDoMain();?>news/" class="navc <?php if(($ navtap) == "news"): ?>navxd<?php endif; ?>">域名资讯</a></li>
    </ul>
  </div>
</div>
<div class="wrap">
    <!--焦点图 开始-->
    <?php echo W('Cate/index_photo');?>
    <!--焦点图 结束-->
    <div class="tabbox">
        <div class="navhl" id="navhljs1">
            <a href="javascript:void(0);" onclick="jdhddh(this,0)" class="navhl_h">最新资讯</a>
            <a href="javascript:void(0);" onclick="jdhddh(this,1)" class="navhl_q">热门资讯</a>
        </div>
        <div id="navhljs1_c0" class="list" style="display:block;">
            <?php echo W('News/tab_list',array('7'));?>
        </div>
        <div id="navhljs1_c1" class="list" style="display:none;">
            <?php echo W('News/tab_list',array('7', array('click_nums'=>'DESC')));?>
        </div>
    </div>
</div>
<div class="wrap">
    <div class="price_list">
        <div class="title">
            <h3 class="titleh3"><i class="iconfont">&#xe601;</i>限时竞价</h3>
            <a href="<?php echo getDoMain();?>sell/page?type=2" class="more">more</a>
        </div>
        <?php echo W('Sale/sale_list',array('2'));?>
    </div>
    <div class="price_list ml20">
        <div class="title">
            <h3 class="titleh3"><span></span><em class="fl">一口价</em></h3>
            <a href="<?php echo getDoMain();?>sell/page?type=1" class="more">more</a>
        </div>
        <?php echo W('Sale/sale_list',array('1'));?>
    </div>
    <div class="price_list">
        <div class="title">
            <h3 class="titleh3"><i class="iconfont">&#xe60b;</i>一元起拍</h3>
            <a href="<?php echo getDoMain();?>sell/page?type=2&price_from=0&price_to=0" class="more">more</a>
        </div>
        <?php echo W('Sale/certify');?>
    </div>
    <div class="price_list ml20">
        <div class="title">
            <h3 class="titleh3"><i class="iconfont">&#xe605;</i>询价域名</h3>
            <a href="<?php echo getDoMain();?>sell/page?type=3" class="more">more</a>
        </div>
        <?php echo W('Sale/sale_list',array('3'));?>
    </div>
</div>
<div class="mt20"></div>
<div class="wrap adva">
    <img src="/Public/Home/images/74237.jpg" class="pic" />
    <ul>
        <li>
            <p>顶级域名服务商</p>
            <div class="adva_a"><i class="iconfont">&#xe60f;</i></div>
            <div class="adva_b">国家核心注册商</div>
            <div class="adva_b">五星级注册商资质</div>
        </li>
        <li>
            <p>域名自由过户转出</p>
            <div class="adva_c"><i class="iconfont">&#xe60c;</i></div>
            <div class="adva_b">转出可以立即放出</div>
            <div class="adva_b">站内带价PUSH可提现</div>
        </li>
        <li>
            <p>域名隐私保护</p>
            <div class="adva_d"><i class="iconfont">&#xe60a;</i></div>
            <div class="adva_b">完全免费</div>
            <div class="adva_b">保护个人隐私</div>
        </li> 
        <li>
            <p>专业的经纪服务</p>
            <div class="adva_e"><i class="iconfont">&#xe604;</i></div>
            <div class="adva_b">专业、尽责、贴心</div>
            <div class="adva_b">一键委托，省心又省钱</div>
        </li>
        <li>
            <p>安全快捷的交易</p>
            <div class="adva_f"><i class="iconfont">&#xe608;</i></div>
            <div class="adva_b">便捷的交易方式</div>
            <div class="adva_b">完善的安全交易制度</div>
        </li>
        <li>
            <p>免费SSL证书</p>
            <div class="adva_g"><i class="iconfont">&#xe602;</i></div>
            <div class="adva_b">马上领取</div>
            <div class="adva_b">免费赠送SSL证书</div>
        </li>   
    </ul>
</div>
<div class="logolink">
    <div class="logolink_title"><p>合作伙伴</p></div>
    <?php echo W('Cate/index_partner');?>
</div>
<?php echo W('Cate/friend_links');?>
<div class="about">
  <div class="wrap">
    <dl>
      <dt>关于我们</dt>
      <dd><i class="iconfont">&#xe613;</i><a href="<?php echo getDoMain();?>about/">米仓网简介</a></dd>
      <dd><i class="iconfont">&#xe613;</i><a href="<?php echo getDoMain();?>about/fee/">收费标准</a></dd>
      <dd><i class="iconfont">&#xe613;</i><a href="<?php echo getDoMain();?>about/state/">网站声明</a></dd>
      <dd><i class="iconfont">&#xe613;</i><a href="<?php echo getDoMain();?>about/contact/">联系我们</a></dd>
    </dl>
    <dl>
      <dt>帮助中心</dt>
      <dd><i class="iconfont">&#xe613;</i><a href="<?php echo getDoMain();?>help/">新手上路</a></dd>
      <dd><i class="iconfont">&#xe613;</i><a href="<?php echo getDoMain();?>help/oper/">操作引导</a></dd>
      <dd><i class="iconfont">&#xe613;</i><a href="<?php echo getDoMain();?>help/protocol/">网站协议</a></dd>
    </dl>
    <dl>
      <dt>支付方式</dt>
      <dd><i class="iconfont">&#xe613;</i><a href="<?php echo getDoMain();?>help/zf/">在线支付</a></dd>
      <dd><i class="iconfont">&#xe613;</i><a href="<?php echo getDoMain();?>help/yhhk/">银行汇款</a></dd>
    </dl>
    <dl>
      <dt>服务支持</dt>
      <dd><i class="iconfont">&#xe613;</i><a href="<?php echo getDoMain();?>help/ask/">在线提问</a></dd>
      <dd><i class="iconfont">&#xe613;</i><a href="<?php echo getDoMain();?>help/relation/">合作联系</a></dd>
      <dd><i class="iconfont">&#xe613;</i><a href="<?php echo getDoMain();?>help/download/">文档下载</a></dd>
      <dd><i class="iconfont">&#xe613;</i><a href="javascript:;">服务电话</a></dd>
    </dl>
    <div class="phone_txt"><span>4008-767-258</span></div>
  </div>
</div>
<!--版权 开始-->
<div class="copyright">
  <ul>
    <li>Copyright © <?php echo date('Y');?> 厦门米仓网版权所有</li>
    <li>地址：厦门湖里万达写字楼C1栋501 客服电话：4008-767-258 传真：</li>
    <li>国家域名注册服务投诉中心投诉受理电话:010-58813000</li>
    <li>邮件supervise@cnnic.cn 传真：010-58812666</li>
  </ul>
</div>
<script src="/Public/Home/js/interactive.js" type="text/javascript"></script>
<script src="/Public/Home/js/common.js" type="text/javascript"></script>
<!--版权 结束-->
</body>
<script>
  var _hmt = _hmt || [];
  (function () {
    var hm = document.createElement("script");
    hm.src = "//hm.baidu.com/hm.js?eca024a0fffa8edfb8952d00fec8f101";
    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(hm, s);
  })();
</script>
</html>