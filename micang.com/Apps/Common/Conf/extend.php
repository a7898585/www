<?php

/**
 * extend.php
 * Author: Jansen
 * QQ: 6206574
 * Date: 2015-11-20
 */
return array(
    'SAFE_KEY' => 'micang&!@#$&Booksir258',
    'ALIPAY_CONFIG' => array(
        'PARTNER' => '2088021942235930',
        'SELLER_EMAIL' => 'yao@micang.com',
        'KEY' => 'g0iasnzzgdbitlpemjhf9c0ctdugzp9d'
    ),
    'DOMAIN_CONFIG' => array(
        'WEBNIC' => array(
            'USERNAME' => 'webcc-yaohongxue',
            'PASSWORD' => '258#$%@micang',
            'NS1' => 'ns1.micang.com',
            'NS2' => 'ns2.micang.com',
            'NS1IP' => '218.66.171.171',
            'NS2IP' => '218.66.171.172'
        ),
        'BIZCN' => array(
            'USERNAME' => 'booksir2',
            'PASSWORD' => 'Dwe65d', //'Wei+-*/sensen258',
            'NS1' => 'ns1.micang.com',
            'NS2' => 'ns2.micang.com',
            'NS1IP' => '218.66.171.171',
            'NS2IP' => '218.66.171.172'
        )
    ),
    'MAIL_CONFIG' => array(
        'MAIL_ADDRESS' => 'service@mail.258.com', // 邮箱地址
        'MAIL_SMTP' => 'mail.258.com', // 邮箱SMTP服务器
        'MAIL_LOGINNAME' => 'service@mail.micang.com', // 邮箱登录帐号
        'MAIL_PASSWORD' => 'lu615nmdvHAcvmos60ae', // 邮箱密码
        'MAIL_CHARSET' => 'UTF-8', //编码
        'MAIL_AUTH' => true, //邮箱认证
        'MAIL_HTML' => true, //true HTML格式 false TXT格式
        'VERIFY_EMAIL_TITLE' => '米仓网whois邮箱验证',
        'VERIFY_EMAIL_CONTENT' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>米仓网域名邮箱认证</title>
</head>
<body>
<div style="width:800px; height:auto; overflow:hidden;">
	<div style="width:100%; height:85px; background:#FF7F0D;">
    	<div style="width:680px; height:63px; overflow:hidden; margin:0 auto;">
        	<div style="float:left; line-height:63px; padding-left:20px; color:#fff; font-size:14px; font-family:\'宋体\';">感谢您对米仓网的支持！</div>
            <div style="float:right; line-height:63px; color:#fff; font-size:32px; font-family:\'constantia\';">400-8767-258</div>
            <div style="float:right; font-family:Arial, Helvetica, sans-serif; font-size:19px; color:#9F4B00; line-height:63px; padding-right:15px;">客服热线</div>
        </div>
        <div style="width:680px; height:22px; overflow:hidden; background:#FAEFE4; margin:0 auto;"></div>
    </div>
    <div style="width:678px; height:auto; overflow:hidden; border-left:#EEEEEE solid 1px; border-right:#EEEEEE solid 1px; border-bottom:#EEEEEE solid 1px; border-top:#FF570D solid 2px; margin:0 auto;">
    	<table width="680" border="0" cellpadding="0" cellspacing="0">
            <tbody border="0">
                <tr>
                    <td><a href="http://www.micang.com/"><img src="http://www.micang.com/Public/Home/images/logo.png" style="width:160px; float:left; margin:20px;" /></a><img src="http://www.micang.com/Public/Home/images/bg_mail.jpg" style="float:right;" /></td>
                </tr>
                <tr><td style="font-family:\'宋体\'; color:#585858; font-size:14px; line-height:30px; padding-left:20px;">尊敬的米仓网会员,您好！</td></tr>
                <tr><td style="font-family:\'宋体\'; color:#FF570D; font-size:14px; line-height:30px; padding:20px 0 0 20px; font-weight:600;">
                您的域名{%DOMAIN%}处于待认证状态
                </td></tr>
                <tr><td style="font-family:\'宋体\'; color:#8B8888; font-size:14px; line-height:30px; padding-left:20px;"> 请 <span style="color:#FF570D;"><a href="%ACTIVATE_URL%" target="_blank">点击这里</a></span> ，或者复制以下链接到浏览器地址栏验证您的域名所有人!</td></tr>
                <tr><td style="font-family:\'宋体\'; font-size:14px; line-height:22px; padding:10px 0; padding-left:20px; font-family:Arial, Helvetica, sans-serif;"><a href="%ACTIVATE_URL%" style=" color:#2E78FF; text-decoration:none;">%ACTIVATE_URL%</a></td></tr>
                <tr><td style="font-family:\'宋体\'; color:#8B8888; font-size:14px; line-height:30px; padding-left:20px;">您收到本邮件，是因为您的邮箱地址被用于<a href="%SYSTEM_URL%" target="_blank">米仓网</a>。如果您未在米仓网操作过，请忽略本邮件。</td></tr>
                <tr><td style="font-family:\'宋体\'; color:#8B8888; font-size:12px; line-height:30px; padding-left:20px; background:#F8F8F8; border-top:#EEEEEE solid 1px;">邮件中包含您的个人信息，建议您保管好本邮件！如您登陆时忘记密码，请点此 <a href="http://www.micang.com/public/forget/" style=" color:#585858; text-decoration:underline; font-weight:600;">找回密码</a> 。</td></tr>
                <tr><td style="font-family:\'宋体\'; color:#8B8888; font-size:12px; line-height:30px; padding-left:20px; background:#F8F8F8;">若您没有申请过验证邮箱 ，请您忽略此邮件，由此给您带来的不便请谅解。</td></tr>
                <tr><td style="font-family:\'宋体\'; color:#8B8888; font-size:12px; line-height:30px; padding-left:20px; background:#F8F8F8;"><span style="float:right; margin:0 20px 0 0;">%DATE%</span></td></tr>
                <tr><td style="font-family:\'宋体\'; color:#8B8888; font-size:12px; line-height:40px; padding:20px 0 0 20px; background:#F4F5F5; border-top:#EEEEEE solid 1px;"><img src="http://www.micang.com/Public/Home/images/mail_icon_a.gif" style="float:left; margin:9px 5px 0 0;" /><span style="float:left; font-size:16px; font-family:Arial, Helvetica, sans-serif;">米仓官网: http://www.micang.com</span></td></tr>
                <tr><td style="font-family:\'宋体\'; font-size:14px; line-height:22px; padding-left:20px; font-family:Arial, Helvetica, sans-serif; background:#F4F5F5;"><span style="float:right; font-size:14px;"><a href="http://www.micang.com/sell/page?type=2" style=" color:#2E78FF; text-decoration:none; margin-right:15px;">域名竞拍</a><a href="http://www.micang.com/sell/page?type=2&price_from=0&price_to=0" style=" color:#2E78FF; text-decoration:none; margin-right:15px;">一元起拍</a>
                <a href="http://www.micang.com/sell/page?type=1" style="color:#2E78FF; text-decoration:none; margin-right:15px;">一口价</a><a href="http://www.micang.com/sell/page?type=3" style=" color:#2E78FF; text-decoration:none; margin-right:15px;">买家询价</a><a href="http://member.micang.com/agency/buy/" style=" color:#2E78FF; text-decoration:none; margin-right:15px;">域名中介</a><a href="http://www.micang.com/domain/purchase/" style=" color:#2E78FF; text-decoration:none; margin-right:15px;">域名代购</a></span></td></tr>
                <tr><td style="font-family:\'宋体\'; color:#8B8888; font-size:12px; line-height:40px; padding:0 0 20px 20px; background:#F4F5F5;"><img src="http://www.micang.com/Public/Home/images/mail_icon_b.gif" style="float:left; margin:9px 5px 0 0;" /><span style="float:left; font-size:16px; font-family:Arial, Helvetica, sans-serif;">客服邮箱: service@micang.com</span></td></tr>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>',
        'ACTIVATE_EMAIL_TITLE' => '米仓网帐号激活邮件',
        'ACTIVATE_EMAIL_CONTENT' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>米仓网帐号激活邮件</title>
</head>
<body>
<div style="width:800px; height:auto; overflow:hidden;">
	<div style="width:100%; height:85px; background:#FF7F0D;">
    	<div style="width:680px; height:63px; overflow:hidden; margin:0 auto;">
        	<div style="float:left; line-height:63px; padding-left:20px; color:#fff; font-size:14px; font-family:\'宋体\';">感谢您对米仓网的支持！</div>
            <div style="float:right; line-height:63px; color:#fff; font-size:32px; font-family:\'constantia\';">400-8767-258</div>
            <div style="float:right; font-family:Arial, Helvetica, sans-serif; font-size:19px; color:#9F4B00; line-height:63px; padding-right:15px;">客服热线</div>
        </div>
        <div style="width:680px; height:22px; overflow:hidden; background:#FAEFE4; margin:0 auto;"></div>
    </div>
    <div style="width:678px; height:auto; overflow:hidden; border-left:#EEEEEE solid 1px; border-right:#EEEEEE solid 1px; border-bottom:#EEEEEE solid 1px; border-top:#FF570D solid 2px; margin:0 auto;">
    	<table width="680" border="0" cellpadding="0" cellspacing="0">
            <tbody border="0">
                <tr>
                    <td><a href="http://www.micang.com/"><img src="http://www.micang.com/Public/Home/images/logo.png" style="width:160px; float:left; margin:20px;" /></a><img src="http://www.micang.com/Public/Home/images/bg_mail.jpg" style="float:right;" /></td>
                </tr>
                <tr><td style="font-family:\'宋体\'; color:#585858; font-size:14px; line-height:30px; padding-left:20px;">尊敬的米仓网会员,您好！</td></tr>
                <tr><td style="font-family:\'宋体\'; color:#FF570D; font-size:14px; line-height:30px; padding:20px 0 0 20px; font-weight:600;">
                恭喜您即将注册成为米仓网的会员，我们将为你提供最专注的服务！
                </td></tr>
                <tr><td style="font-family:\'宋体\'; color:#8B8888; font-size:14px; line-height:30px; padding-left:20px;"> 您的帐号目前处于 <span style="color:#FF570D;"><a href="%ACTIVATE_URL%" target="_blank">待激活状态</a></span> ，请点击或者复制以下链接到浏览器地址栏进行访问</td></tr>
                <tr><td style="font-family:\'宋体\'; font-size:14px; line-height:22px; padding:10px 0; padding-left:20px; font-family:Arial, Helvetica, sans-serif;"><a href="%ACTIVATE_URL%" style=" color:#2E78FF; text-decoration:none;">%ACTIVATE_URL%</a></td></tr>
                <tr><td style="font-family:\'宋体\'; color:#8B8888; font-size:14px; line-height:30px; padding-left:20px;">链接有效期请为24小时，请在24小时内点击或访问以上链接完成激活操作。如果您没有注册过米仓账户，请忽略本邮件。</td></tr>
                <tr><td style="font-family:\'宋体\'; color:#8B8888; font-size:12px; line-height:30px; padding-left:20px; background:#F8F8F8; border-top:#EEEEEE solid 1px;">邮件中包含您的个人信息，建议您保管好本邮件！如您登陆时忘记密码，请点此 <a href="http://www.micang.com/public/forget/" style=" color:#585858; text-decoration:underline; font-weight:600;">找回密码</a> 。</td></tr>
                <tr><td style="font-family:\'宋体\'; color:#8B8888; font-size:12px; line-height:30px; padding-left:20px; background:#F8F8F8;">若您没有申请过验证邮箱 ，请您忽略此邮件，由此给您带来的不便请谅解。</td></tr>
                <tr><td style="font-family:\'宋体\'; color:#8B8888; font-size:12px; line-height:30px; padding-left:20px; background:#F8F8F8;"><span style="float:right; margin:0 20px 0 0;">%DATE%</span></td></tr>
                <tr><td style="font-family:\'宋体\'; color:#8B8888; font-size:12px; line-height:40px; padding:20px 0 0 20px; background:#F4F5F5; border-top:#EEEEEE solid 1px;"><img src="http://www.micang.com/Public/Home/images/mail_icon_a.gif" style="float:left; margin:9px 5px 0 0;" /><span style="float:left; font-size:16px; font-family:Arial, Helvetica, sans-serif;">米仓官网: http://www.micang.com</span></td></tr>
                <tr><td style="font-family:\'宋体\'; font-size:14px; line-height:22px; padding-left:20px; font-family:Arial, Helvetica, sans-serif; background:#F4F5F5;"><span style="float:right; font-size:14px;"><a href="http://www.micang.com/sell/page?type=2" style=" color:#2E78FF; text-decoration:none; margin-right:15px;">域名竞拍</a><a href="http://www.micang.com/sell/page?type=2&price_from=0&price_to=0" style=" color:#2E78FF; text-decoration:none; margin-right:15px;">一元起拍</a>
                <a href="http://www.micang.com/sell/page?type=1" style="color:#2E78FF; text-decoration:none; margin-right:15px;">一口价</a><a href="http://www.micang.com/sell/page?type=3" style=" color:#2E78FF; text-decoration:none; margin-right:15px;">买家询价</a><a href="http://member.micang.com/agency/buy/" style=" color:#2E78FF; text-decoration:none; margin-right:15px;">域名中介</a><a href="http://www.micang.com/domain/purchase/" style=" color:#2E78FF; text-decoration:none; margin-right:15px;">域名代购</a></span></td></tr>
                <tr><td style="font-family:\'宋体\'; color:#8B8888; font-size:12px; line-height:40px; padding:0 0 20px 20px; background:#F4F5F5;"><img src="http://www.micang.com/Public/Home/images/mail_icon_b.gif" style="float:left; margin:9px 5px 0 0;" /><span style="float:left; font-size:16px; font-family:Arial, Helvetica, sans-serif;">客服邮箱: service@micang.com</span></td></tr>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>',
        'ACTIVE_PASSWORD_EMAIL_TITLE' => '米仓网找回密码邮件',
        'ACTIVE_PASSWORD_EMAIL_CONTENT' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>米仓网忘记密码修改邮件</title>
</head>
<body>
<div style="width:800px; height:auto; overflow:hidden;">
	<div style="width:100%; height:85px; background:#FF7F0D;">
    	<div style="width:680px; height:63px; overflow:hidden; margin:0 auto;">
        	<div style="float:left; line-height:63px; padding-left:20px; color:#fff; font-size:14px; font-family:\'宋体\';">感谢您对米仓网的支持！</div>
            <div style="float:right; line-height:63px; color:#fff; font-size:32px; font-family:\'constantia\';">400-8767-258</div>
            <div style="float:right; font-family:Arial, Helvetica, sans-serif; font-size:19px; color:#9F4B00; line-height:63px; padding-right:15px;">客服热线</div>
        </div>
        <div style="width:680px; height:22px; overflow:hidden; background:#FAEFE4; margin:0 auto;"></div>
    </div>
    <div style="width:678px; height:auto; overflow:hidden; border-left:#EEEEEE solid 1px; border-right:#EEEEEE solid 1px; border-bottom:#EEEEEE solid 1px; border-top:#FF570D solid 2px; margin:0 auto;">
    	<table width="680" border="0" cellpadding="0" cellspacing="0">
            <tbody border="0">
                <tr>
                    <td><a href="http://www.micang.com/"><img src="http://www.micang.com/Public/Home/images/logo.png" style="width:160px; float:left; margin:20px;" /></a><img src="http://www.micang.com/Public/Home/images/bg_mail.jpg" style="float:right;" /></td>
                </tr>
                <tr><td style="font-family:\'宋体\'; color:#585858; font-size:14px; line-height:30px; padding-left:20px;">尊敬的米仓网会员,您好！</td></tr>
                <tr><td style="font-family:\'宋体\'; color:#FF570D; font-size:14px; line-height:30px; padding:20px 0 0 20px; font-weight:600;">
                安全提示：您在米仓网（micang.com）点击了“忘记密码”按钮，故系统自动为您发送了这封邮件。
                </td></tr>
                <tr><td style="font-family:\'宋体\'; color:#8B8888; font-size:14px; line-height:30px; padding-left:20px;"> 您可以点击以下链接修改您的密码：<span style="color:#FF570D;"><a href="%ACTIVATE_URL%" target="_blank">点击这里</a></span> ，或者复制以下链接到浏览器地址栏进行访问：</td></tr>
                <tr><td style="font-family:\'宋体\'; font-size:14px; line-height:22px; padding:10px 0; padding-left:20px; font-family:Arial, Helvetica, sans-serif;"><a href="%ACTIVATE_URL%" style=" color:#2E78FF; text-decoration:none;">%ACTIVATE_URL%</a></td></tr>
                <tr><td style="font-family:\'宋体\'; color:#8B8888; font-size:14px; line-height:30px; padding-left:20px;">此链接有效期为两个小时，请在两小时内点击链接进行修改，每天最多允许找回5次密码。如果您不需要修改密码，或者您从未点击过“忘记密码”按钮，请忽略本邮件。</td></tr>
                <tr><td style="font-family:\'宋体\'; color:#8B8888; font-size:12px; line-height:30px; padding-left:20px; background:#F8F8F8; border-top:#EEEEEE solid 1px;">邮件中包含您的个人信息，建议您保管好本邮件！如您登陆时忘记密码，请点此 <a href="http://www.micang.com/public/forget/" style=" color:#585858; text-decoration:underline; font-weight:600;">找回密码</a> 。</td></tr>
                <tr><td style="font-family:\'宋体\'; color:#8B8888; font-size:12px; line-height:30px; padding-left:20px; background:#F8F8F8;">若您没有申请过验证邮箱 ，请您忽略此邮件，由此给您带来的不便请谅解。</td></tr>
                <tr><td style="font-family:\'宋体\'; color:#8B8888; font-size:12px; line-height:30px; padding-left:20px; background:#F8F8F8;"><span style="float:right; margin:0 20px 0 0;">%DATE%</span></td></tr>
                <tr><td style="font-family:\'宋体\'; color:#8B8888; font-size:12px; line-height:40px; padding:20px 0 0 20px; background:#F4F5F5; border-top:#EEEEEE solid 1px;"><img src="http://www.micang.com/Public/Home/images/mail_icon_a.gif" style="float:left; margin:9px 5px 0 0;" /><span style="float:left; font-size:16px; font-family:Arial, Helvetica, sans-serif;">米仓官网: http://www.micang.com</span></td></tr>
                <tr><td style="font-family:\'宋体\'; font-size:14px; line-height:22px; padding-left:20px; font-family:Arial, Helvetica, sans-serif; background:#F4F5F5;"><span style="float:right; font-size:14px;"><a href="http://www.micang.com/sell/page?type=2" style=" color:#2E78FF; text-decoration:none; margin-right:15px;">域名竞拍</a><a href="http://www.micang.com/sell/page?type=2&price_from=0&price_to=0" style=" color:#2E78FF; text-decoration:none; margin-right:15px;">一元起拍</a>
                <a href="http://www.micang.com/sell/page?type=1" style="color:#2E78FF; text-decoration:none; margin-right:15px;">一口价</a><a href="http://www.micang.com/sell/page?type=3" style=" color:#2E78FF; text-decoration:none; margin-right:15px;">买家询价</a><a href="http://member.micang.com/agency/buy/" style=" color:#2E78FF; text-decoration:none; margin-right:15px;">域名中介</a><a href="http://www.micang.com/domain/purchase/" style=" color:#2E78FF; text-decoration:none; margin-right:15px;">域名代购</a></span></td></tr>
                <tr><td style="font-family:\'宋体\'; color:#8B8888; font-size:12px; line-height:40px; padding:0 0 20px 20px; background:#F4F5F5;"><img src="http://www.micang.com/Public/Home/images/mail_icon_b.gif" style="float:left; margin:9px 5px 0 0;" /><span style="float:left; font-size:16px; font-family:Arial, Helvetica, sans-serif;">客服邮箱: service@micang.com</span></td></tr>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>',
    ),
    'UPAIYUN_CONFIG' => array(
        'UPYUN_UPLOAD_URL' => 'http://v0.api.upyun.com/micang-com/',
        'UPYUN_URL' => 'http://micang-com.b0.upaiyun.com',
        'UPYUN_UPLOAD_CONFIG' => array(
            'secret_key' => '97A+gdF5b+I6aC1MULcw9s0tVZk=',
            'buckets' => 'micang-com',
            'host' => 'v0.api.upyun.com',
            'expired' => 7200,
            'suffix' => 'jpg,png,gif',
            'username' => 'micang',
            'password' => 'pw8z!Qn9Pb'
        )
    ),
    'DNS_CONFIG' => array(
        'API_HOST' => 'http://demo.dns.com',
        'API_KEY' => 'f605c29f54ef18c573a224a91eddd471', //'0cdcab2ea837f6f0854e4b4d1fc435ce',
        'API_SECRET' => 'be3ee36d1c0921bf67a62ac80706497b',
        'API_USER' => 'shijingxian@micang.com', //'dns@micang.com',
        'API_PWD' => '1qaz2wsx', //'rcis8wikw9r1JV3u'
    ),
    'WEIXIN_CONFIG' => array(
        'TOKEN' => 'HpTsN8ebpLw44S5rdKyuawjCoVua',
        'ASE_KEY' => 'V5Dq9JisNMXbpLw44S5rdjCoVuaoMlsRgG56JkU6bLy',
        'APP_ID' => 'wxe96ae26592239981',
        'APP_SECRET' => '137ea3823f682ea5f2dd5aed44f178b4'
    ),
    'WEIXIN_TEMPLATE' => array(
        'text_tpl' => '<xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[%s]]></MsgType>
    <Content><![CDATA[%s]]></Content>
    </xml>'),
    'WEIXIN_TEMPLATE_ID' => array(
        'confirm_code' => 'MT307qD8w9UJgkEjJ1URRDKdSE0bszt22lVq4QodJF8', //确认验证码模板
        'send_code' => 'CzPcuS9uB0Xe9mLjLshyVkpyqzU3PR8iVSUSmKOoA3o', //发送验证码模板
    )
);