function lrPopup(lrType, lWidth, lHeight, lUrl, rWidth, rHeight, rUrl) {	
    /*
	 * -- 弹出登陆注册框函数 --
	 *
	 * 参数说明：
	 *  lrType: 登陆或注册，如果点击的是登陆按钮，则该值为“log”
	 *  lWidth: 登陆框的宽度
	 * lHeight: 登陆框的高度
	 *    lUrl: 登陆框中框架页的src属性值
	 * rWidth, rHeight, rUrl：同上
	 */
	 
    //构造一个由html代码组成的jquery对象
    var htmlObj = $('<style type="text/css">#lrMenu { position:relative; float:left; width:100%; height:29px; overflow:hidden; background:url(http://images.cnfol.com/login_js/login_image/lrRepeat.gif) }#lrMenu div { float:left; margin:0 5px; width:77px; height:29px; font-size:13px; text-align:center; line-height:33px; cursor:pointer }#ifrm { width:100%; _width:102%; height:100%; overflow:hidden }.lrNormal { background:url(http://images.cnfol.com/login_js/login_image/lrNormal.gif) }.lrHover { background:url(http://images.cnfol.com/login_js/login_image/lrHover.gif); font-weight:bold }#lrMenu #lrClose { position:absolute; top:6px; right:0; background:url(http://images.cnfol.com/login_js/login_image/lrClose.gif); width:17px; height:16px; float:right }#dialog-overlay { width:100%; height:100%; filter:alpha(opacity=20); -moz-opacity:0.2; -khtml-opacity: 0.2; opacity: 0.2; position:absolute; background:#000; top:0; left:0; z-index:3000; display:none; }</style><div id="dialog-overlay"></div><div id="lrWrap" style="position:absolute; border:1px solid #333333; overflow:hidden; z-index:5000; background:white; display:none"><div id="lrMenu"><div id="lMenu">登录</div><div id="rMenu">注册</div><div id="lrClose"></div></div><div style="clear:both"></div><iframe id="ifrm" frameborder="0" name="frame_logreg"></iframe></div>');
	
    //向body中添加htmlObj对象包含的html代码
    $('body').append(htmlObj);
	
    var lMenuObj = $('#lMenu');
    rMenuObj = $('#rMenu');
    ifrmObj = $('#ifrm'), lrObj = $('#lrWrap');
	
    //根据lrType的值设置各初始值
    if (lrType == 'log') {
        lMenuObj.addClass('lrHover');
        rMenuObj.addClass('lrNormal');
        lrObj.width(lWidth).height(lHeight);
        var url = window.location.href;
        var end = url.indexOf("#");
        if(end > 0){
            url = url.substring(0,end);
        }
        ifrmObj.attr('src', lUrl+'#'+url);
    } else {
        lMenuObj.addClass('lrNormal');
        rMenuObj.addClass('lrHover');
        lrObj.width(rWidth).height(rHeight);
        ifrmObj.attr('src', rUrl);
    }
	
    //弹出层函数
    function popup() {
        var docH = $(document).height();
        var winW = $(window).width();
        var winH = $(window).height();
        var winST = $(window).scrollTop();
        var dialogTop = winST + 10;
        var dialogLeft = (winW / 2) - (lrObj.width() / 2);
		
        $('#dialog-overlay').css({
            height: docH, 
            width: winW
        }).show();
        lrObj.css({
            top: dialogTop, 
            left: dialogLeft
        }).show();
    }
	
    popup();
	
    //浏览器窗口大小改变时执行
    $(window).resize(function () {
        if (!lrObj.is(':hidden')) popup();
    });
	
    //点击关闭按钮，移除htmlObj对象
    $('#lrClose').click(function() {
        htmlObj.remove();
    });
	
    //在登陆注册之间切换
    if (lrType == 'reg') {
        $('#lMenu').click(function() {
            htmlObj.remove();
            lrPopup('log', lWidth, lHeight, lUrl, rWidth, rHeight, rUrl);
        });
    } else {
        $('#rMenu').click(function() {
            htmlObj.remove();
            lrPopup('reg', lWidth, lHeight, lUrl, rWidth, rHeight, rUrl);
        });
    }
}
