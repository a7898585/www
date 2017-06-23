var baseurl = "http://new.blog.cnfol.com";
var passporturl = "http://passport.cnfol.com";
//智能加载js文件，并执行指定函数
function doWhenJsExists(funExistsInJs, file, doFun) {
    var jsLoaded = false;
	
    setTimeout(function() {
        if (eval('typeof(' + funExistsInJs + ')') == 'undefined') {
            var head = document.getElementsByTagName('head')[0];
            var js = document.createElement('script');
            js.setAttribute('type', 'text/javascript');
            js.setAttribute('src', file);
            head.appendChild(js);
			
            setInterval(function() {
                if (eval('typeof(' + funExistsInJs + ')') != 'undefined' && jsLoaded == false) {
                    jsLoaded = true;
					
                    setTimeout(function() {
                        doFun();
                    }, 100);
                }
            }, 100);			
			
            js.onreadystatechange = function() {
                if (js.readyState == 'complete') {
                    if (jsLoaded == false) {
                        jsLoaded = true;
						
                        setTimeout(function() {
                            doFun();
                        }, 100);
                    }
                }
            }
		
            js.onload = function() {
                if (jsLoaded == false) {
                    jsLoaded = true;
					
                    setTimeout(function() {
                        doFun();
                    }, 100);
                }
            }
        }
        else {
            doFun();
        }
    }, 100);
}

//读取或设置cookie
function jCookie(cookieName, cookieVal) {
    if (arguments.length == 2) {
        var expTime = new Date((new Date()).getTime() + 99999 * 60000); //15分钟
		
        document.cookie = cookieName + '=' + escape(cookieVal) + ';expires=' + expTime.toGMTString() + ';path=/;' + ';domain=.cnfol.com';
		
        return cookieVal;
    }
    else {
        var allCookies = document.cookie, cookiePos = allCookies.indexOf(cookieName);
		
        if (cookiePos != -1) {
            cookiePos += cookieName.length + 1;
            var cookieEnd = allCookies.indexOf(';', cookiePos);
            if (cookieEnd == -1) {
                cookieEnd = allCookies.length;
            }
            var value = unescape(allCookies.substring(cookiePos, cookieEnd));
        }
        return value;
    }
}

//处理包含汉字的字符串的截断
String.prototype.len = function() {
    return this.replace(/[^\x00-\xff]/g,"rr").length;
}

String.prototype.sub = function(n) {
    var r = /[^\x00-\xff]/g;
	
    if(this.replace(r, "mm").length <= n) {
        return this;
    }
	
    var m = Math.floor(n / 2);    
	
    for(var i = m; i < this.length; i++) {
        if(this.substr(0, i).replace(r, "mm").length >= n) {
            return this.substr(0, i);
        }
    }
	
    return this; 
};

// 获取鼠标坐标
function getMousePos(e) {
    var e = e || window.event;
	
    if (e.pageX || e.pageY) {
        return {
            x: e.pageX,
            y: e.pageY
        };
    }
    else {
        return {
            x: e.clientX + document.body.scrollLeft - document.body.clientLeft,
            y: e.clientY + document.body.scrollTop - document.body.clientTop
        };
    }
}

var js1 = 'http://img.cnfol.com/core/js/jquery-1.4.4.min.js';
//var js2 = 'http://img.cnfol.com/newblog/js/popup.js';
var js2 =baseurl+'/application/views/module/popup.js';
var js3 = 'http://images.cnfol.com/uploads/v5.0/passportweb/script/tipswindown.js';

var userId      = typeof jCookie('cookie[passport][userId]') != 'undefined' ? jCookie('cookie[passport][userId]') : 0; //当前用户id
var ajaxBaseUrl = 'http://passport.cnfol.com/'; //关注

//便于外部调用，在头像图片上需设置class="refid"，refid="会员id"，调用方式：doShowCard('.refid');
function doShowCard(o) {
	
    var cDocReady = false;
	
    if (document.all) {
        setInterval(function() {
			
            if (cDocReady == false) {
                if (document.readyState == 'complete') {
                    cDocReady = true;
	
                    doWhenJsExists('jQuery', js1, function() {
                        jQuery(function() {
                            doWhenJsExists('G', js2, function() {
                                doWhenJsExists('tipsWindown', js3, function() {
                                    showCard(o);
                                });
                            });
                        });
                    });
					
                }
            }
        }, 500);
    }
    else {
		
        doWhenJsExists('jQuery', js1, function() {
            jQuery(function() {
                doWhenJsExists('G', js2, function() {
                    doWhenJsExists('tipsWindown', js3, function() {
                        showCard(o);
                    });
                });
            });
        });
    }
}




//名片主函数
function showCard(o) {
    if (jQuery(o).length == 0) {
        return;
    }
	
    var cImgBaseUrl   = 'http://img.cnfol.com/osapi/v2.0/osapi-image/card/'; //名片图片基本路径
    var errHeadUrl    = 'http://images.cnfol.com/uploads/v5.0/passportweb/images/man.png'; //头像图片加载错误时替代的头像
    var getCardUrl    = ''; //获取名片数据
    var hoverOutTime  = null; //鼠标移开
    var cardWidth     = 310; //名片宽
    var cardHeight    = 172; //名片高	
	
	
    function getCard(json, cardArr, uId) {
		
        var cHtml = '<!--[if IE 6]><iframe class="Ifr6" src="####" scrolling="no" frameborder="0"></iframe><![endif]-->';
        cHtml += '<div class="p_wrap">';
        cHtml += '<div class="p_wrap_s_bg"></div>';
        cHtml += '<div class="p_wrap_s">';
        cHtml += '<div class="card_arr">' + cardArr + '</div>';
        cHtml += '<div class="p_u_info">';
        cHtml += '<div class="p_header p_l"><a target="_blank" href="'+passporturl+'/otherpersoninfo'+uId+'.html">';
        cHtml += '<img title="' + json.nickname + '" src="' + json.head + '" ';
        cHtml += ' onerror="this.src=\'http://head.cnfolimg.com/man_48.png\'" /></a>';
        cHtml += '<div class="p_widget" style="z-index:1001">';
        //cHtml += '<a title="发信" href="javascript:sendMsg(' + uId + ');" class="p_message"></a>';
        cHtml += '<a title="发信" href=javascript:OpenMessage("2","'+json.nickname+'","'+json.nickname+'","'+uId+'"); class="p_message"></a>';
        //cHtml += '<a title="发微博" href="javascript:sendG(\'' + json.nickname + '\');" class="p_chat"></a>';
        cHtml += '<a title="送鲜花" href="javascript:tipsWindown(\'给' + json.nickname + '送鲜花\', \'ToUserID=' + uId + '&GiftID=1&SourceTypeID=7&SourceURL='+encodeURI(encodeURIComponent(window.location.href))+'\', 320, 200, \'true\', \'\', \'false\', \'\')" class="p_flower"></a>';
        cHtml += '</div>';
        cHtml += '</div>';
        cHtml += '<div class="p_info p_l">';
        cHtml += '<div class="p_info_c" style="margin:0; padding:0">';
        cHtml += '<ul>';
        cHtml += '<li class="list1"><span class="p_username"><a target="_blank"';
        cHtml += ' href="'+passporturl+'/otherpersoninfo'+uId+'.html" title="' + json.nickname + '">';
        cHtml += json.nickname.len() > 14 ? json.nickname.sub(14) + '...' : json.nickname;
        cHtml += '</a>&nbsp;&nbsp;<img style="cursor:pointer" title="' + (json.online=='1' ? '在线':'不在线') + '" src="' + cImgBaseUrl + 'ico1' + (json.online=='1' ? '':'-press') + '.gif" /></span> </li>';
        cHtml += '<li class="list4">';
		
        cHtml += '<div><div class="c_img"><img src="http://img.cnfol.com/osapi/v2.0/osapi-image/card/gold.gif" /></div><div class="c_num">' + json.money + '</div></div>';
        cHtml += '<div><div class="c_img"><img src="http://img.cnfol.com/osapi/v2.0/osapi-image/card/point.gif" /></div><div class="c_num">' + json.usepoint + '</div></div>';
        if(uId != '5727426' && uId != '9253493' && uId != '4027469' && uId != '5764661')
        {
            cHtml += '<div><div class="c_img"><img src="http://img.cnfol.com/osapi/v2.0/osapi-image/card/flower.gif" /></div><div class="c_num">' + json.giftcount + '</div></div>';
        }
        cHtml += '<div style="clear:both"></div>';
		
        cHtml += '</li>';
        cHtml += '<li class="list2">共同好友</li>';
        cHtml += '<li class="list3">';
		
        //共同好友
        var sameFriArr = json.together_friends, sameFriLen = sameFriArr.length;
		
        for (var i = 0; i < sameFriLen; i++) {
            cHtml += '<a target="_blank" href="'+passporturl+'/otherpersoninfo' + sameFriArr[i]['UserID'] + '.html">';
            cHtml += '<img title="' + sameFriArr[i]['NickName'] + '"';
            cHtml += ' src="' + sameFriArr[i]['head'] + '" width="25" height="25" /></a>';
        }
		
        cHtml += '</li>';
        cHtml += '</ul>';
        cHtml += '</div>';
        cHtml += '</div>';
        cHtml += '<div class="p_clear"></div>';
        cHtml += '</div>';
        cHtml += '<div class="p_btm">';
        cHtml += '<div class="p_btm_c">';
        cHtml += '<div class="p_action">';
		
        //互相关注
        cHtml += '<div class="p_friend p_r" style="display:';
        cHtml += (json.verify == '3' ? 'block' : 'none') + ';"><span class="p_ygz_s">互为好友</span>';
        cHtml += '<span class="p_cancel_s"><a href="javascript:';
        cHtml += 'attention(' + uId + ', \'del\');';
        cHtml += '">取消</a></span></div>';
		
        //我关注他
        cHtml += '<div class="p_ygz p_r" style="display:';
        cHtml += (json.verify == '1' ? 'block' : 'none') + '"><span class="p_ygz_s">已关注</span>';
        cHtml += '<span class="p_cancel_s"><a href="javascript:';
        cHtml += 'attention(' + uId + ', \'del\');';
        cHtml += '">取消</a></span></div>';
		
        //关注他
        cHtml += '<div class="p_gz p_r" style="display:';
        cHtml += (json.verify != '3' && json.verify != '1' ? 'block' : 'none') + ';" onclick="';
        cHtml += 'attention(' + uId + ', \'add\')';
        cHtml += '"></div>';
		
        cHtml += '<div class="p_clear"></div>';
        cHtml += '</div>';
        cHtml += '</div>';
        cHtml += '</div>';
        cHtml += '</div>';
        cHtml += '</div>';
		
        cHtml += '<input type="hidden" class="mpVerify" value="' + json.verify + '" />';
        cHtml += '<input type="hidden" class="sendMsgTag" value="' + (json.verify == '3' ? '1' : '0') + '" />';
		
        return cHtml;
    }
	
    var _self = jQuery(o);
	
    _self.error(function() {
        _self.attr('src', errHeadUrl);
    }).live('mouseover', function() {
        var _this = jQuery(this), _thisId = _this.attr('refid'), _thisCardId = 'card' + _thisId;
		
        _thisId=_thisId.match(/^[0-9]+/);
		
        var _thisLeft = 0;
		
        jQuery('#tip' + _thisId).remove();
		
        //获取窗口和文档各个尺寸
        var s = {
            dHeight   : jQuery(document).height(),
            wWidth    : jQuery(window).width(),
            wHeight   : jQuery(window).height(),
            wScrollTop: jQuery(window).scrollTop()
        };
		
        _thisLeft = _this.offset().left;
        var _thisTop = _this.offset().top;
        var _thisWidth = _this.width(), _thisHeight = _this.height();
        var leftIf = _thisLeft + cardWidth > s['wWidth'];
        var topIf  = _thisTop + 38 + cardHeight > s['dHeight'];
		
        function getLayer($str) {
            var newStr = '<div class="layer_arrow_' + $str + '"><img src="' + cImgBaseUrl;
            newStr += 'arrow_' + $str.slice(0, -1) + '.gif" width="9" height="6" /></div>';
            return newStr;
        }		
		
        var layerArr = {
            tl: getLayer('tl'),
            br: getLayer('br'),
            tr: getLayer('tr'),
            bl: getLayer('bl')
        };
		
        //设置名片显示位置
		
        var cardPos = {
            cardLeft: leftIf ? _thisLeft - 224 + (_thisWidth - 48) : _thisLeft - 12,
            cardTop : topIf ? _thisTop - 173 : _thisTop + 41 + (_thisHeight - 48)
        };
		
        //获取箭头方向
        function getCardArr() {	
            var cardArr = '';
			
            //设置箭头位置
            switch (true) {
                case leftIf && topIf:
                    cardArr = layerArr['br'];
                    break;
                case leftIf && !topIf:
                    cardArr = layerArr['tr'];
                    break;
                case !leftIf && topIf:
                    cardArr = layerArr['bl'];
                    break;
                case !leftIf && !topIf:
                    cardArr = layerArr['tl'];
                    break;
                default:
                    break;
            }
			
            return cardArr;
			
        }
		
        hideCard();
		
        clearTimeout(hoverOutTime);
		
        if (!jQuery('#' + _thisCardId)[0]) {
            //生成名片，并为其添加事件
            var cardHtml = '<div class="card" id="' + _thisCardId + '"></div>';
			
            var _thisCard = jQuery(cardHtml).css({
                position: 'absolute',
                left    : cardPos['cardLeft'],
                top     : cardPos['cardTop']
            }).appendTo('body');
			
            //名片加载中
            var cardLoading = '<div class="cardLoading" style="width:305px; padding-left:5px; height:30px; line-height:30px; ';
            cardLoading += 'font-size:12px; border:2px solid #EBEBEB; background:white">正在加载数据...</div>';
			
            //名片不存在
            var noCard = '<div class="noCard" style="width:305px; padding-left:5px; height:30px; line-height:30px; ';
            noCard += 'font-size:12px; border:2px solid #EBEBEB; background:white">名片不存在！</div>';
			
            _thisCard.html(cardLoading);
			
			
            //动态获取名片
            jQuery.getJSON(
                //ajaxBaseUrl + 'index.php/usercard/ajaxGetInfo?uid=' + _thisId + '&cuid=' + userId + '&callback=?',//登陆后打开名片慢
                ajaxBaseUrl + 'index.php/usercard/ajaxGetInfo?uid=' + _thisId + '&callback=?',
                function(json) {
                    if (json.nickname == null) {
                        _thisCard.html(noCard);
                        return;
                    }
					
                    var cardArr = getCardArr();
					
                    var cHtml = getCard(json, cardArr, _thisId);
					
					
                    _thisCard
                    .html(cHtml)
                    .hover(
                        function() {
                            clearTimeout(hoverOutTime);
                            jQuery('#' + _thisCardId).css({
                                left    : cardPos['cardLeft'],
                                top     : cardPos['cardTop']
                            });
							
                        },
                        function() {
                            //鼠标移开名片时，名片延迟消失
                            hoverOutTime = setTimeout(function() {
                                hideCard();
                            }, 600);
                        }
                        ).click(function(e) {
                        //取消冒泡，鼠标点击名片时，不执行document的click事件
                        var e = e || window.event;
                        e.stopPropagation();					
                    });
                }
                );
        }
        else {
            var cardArr = getCardArr();
			
            if (jQuery('.cardLoading', '#' + _thisCardId).length > 0) {
                //动态获取名片
                jQuery.getJSON(
                    ajaxBaseUrl + 'index.php/usercard/ajaxGetInfo?uid=' + _thisId + '&cuid=' + userId + '&callback=?',
                    function(json) {
                        var cHtml = getCard(json, cardArr, _thisId);
						
                        jQuery('#' + _thisCardId)
                        .html(cHtml);
                    }
                    );
            }
            else {					
                jQuery('.card_arr', '#' + _thisCardId).html(cardArr);
            }
			
            jQuery('#' + _thisCardId).css({
                left    : cardPos['cardLeft'],
                top     : cardPos['cardTop']
            });
        }
    }).live('mouseout', function() {
        var _this = jQuery(this), _thisId = _this.attr('refid'), _thisCardId = 'card' + _thisId;
        var _thisLeft = 0;
        _thisId=_thisId.match(/^[0-9]+/);
		
        //获取窗口和文档各个尺寸
        var s = {
            dHeight   : jQuery(document).height(),
            wWidth    : jQuery(window).width(),
            wHeight   : jQuery(window).height(),
            wScrollTop: jQuery(window).scrollTop()
        };
		
        //鼠标移开头像时，名片延迟消失
        hoverOutTime = setTimeout(function() {
            hideCard();
        }, 600);
    });
}

//隐藏名片
function hideCard() {
    jQuery('.card').css('left', -1000);
}

//点击网页文档时，隐藏名片
jQuery('body').click(function() {
    hideCard();
});

function cardTip(uId) {
    var html = '<div class="cardTip" id="tip' + uId + '" style="background:url(http://img.cnfol.com/osapi/v2.0/osapi-image/card/card_tip.gif); width:177px; height:35px; line-height:40px; font-size:12px; color:#666666; text-indent:5px">只有互为好友才可以发私信噢！</div>';
	
    if (jQuery('#tip' + uId)[0]) {
        jQuery('#tip' + uId).remove();
    }
	
    jQuery(html)
    .css({
        position: 'absolute', 
        left: 20, 
        top: 120, 
        zIndex: 2000
    })
    .appendTo(jQuery('#card' + uId));
		
    setTimeout(function() {
        jQuery('#tip' + uId).fadeOut(1000);
    }, 2000);
}

//发私信
function sendMsg(uId) {	
    var sendMsgTag = jQuery('.sendMsgTag', '#card' + uId).val();
	
    if (!userId) {
        pLogin();
        return;
    }
	
    if (sendMsgTag == '0') {
        cardTip(uId);
        return;
    }
	
    mp_pop = new Popup({
        contentType    :1,
        isReloadOnClose:false,
        width          :350,
        height         :210
    });
	
    mp_pop.setContent("title", '发送信息');
	
    mp_pop.setContent("contentUrl", encodeURI("http://passport.cnfol.com/_blogmodule/sendMessage.php?friendUserID=" + uId));
	
    mp_pop.build();
	
    mp_pop.show();
}


function MyOpenMessage(type,username,friendNickname,friendUserID){ 


	
    var type = (type==1)?'添加好友':'发送信息';
    var page = (type=='添加好友')?'addFriend.php':'sendMessage';
    var url  = "http://my.cnfol.com/index.php/send/"+page+"?friendUsername=";
    url += username+"&friendUserID="+friendUserID+"&friendNickname=";
    url += friendNickname;
	
    mp_pop = new Popup({
        contentType    :1,
        isReloadOnClose:false,
        width          :350,
        height         :210
    });
	
    mp_pop.setContent("title", '发送信息');
	
    mp_pop.setContent("contentUrl", encodeURI(url));
	
    mp_pop.build();
	
    mp_pop.show();
	
	
}

//发微薄
function sendG(nickname) {
    if (userId) {
        open('http://my.cnfol.com/?tname=' + nickname);
    }
    else {
        pLogin();
    }
}

//关注
function attention(uId, type) {
    if (!userId) {
        pLogin();
        return;
    }
	
    if (uId == '' || uId == 0) {
        return;
    }
	
    if (type != 'add' && type != 'del') {
        return;
    }
	
    var $AttentionUrl = ajaxBaseUrl + 'follow/' + type + '.php?r=' + Math.random() + '&fuid=' + uId + '&callback=?';
    if (type == 'add') {
    //$AttentionUrl = 'http://my.cnfol.com/apps/action?type=adduserbyid&userid=' + uId + '&callback=?';
    }
	
	
    jQuery.getJSON(
        $AttentionUrl,
        function(json) {
            var div_content = '', msg = json.msg;
		
            //if ( type == 'add')
            //{
            //msg = json.error;
            //}
		
            if(msg.indexOf('取消关注成功！') != -1 ||msg.indexOf('添加关注成功！') != -1 || msg.indexOf('success') != -1) {
                alert(msg);
                location.href = location.href;
                return;
                var $mpVerify   = jQuery('.mpVerify', '#card' + uId);
                var $sendMsgTag = jQuery('.sendMsgTag', '#card' + uId);
			
                var $rs         = jQuery('.p_r', '#card' + uId);
                var $friend     = jQuery('.p_friend', '#card' + uId);
                var $ygz        = jQuery('.p_ygz', '#card' + uId);
                var $gz         = jQuery('.p_gz', '#card' + uId);
			
                switch($mpVerify.val()) {
                    case '0'://互不关注
                        $rs.hide();
                        $ygz.show();
                        $mpVerify.val(1);
                        break;
                    case '1'://我关注的
                        $rs.hide();
                        $gz.show();
                        $mpVerify.val(0);
                        break;
                    case '2'://关注我的
                        $rs.hide();
                        $friend.show();
                        $mpVerify.val(3);
                        $sendMsgTag.val(1);					
                        break;
                    case '3'://好友
                        $rs.hide();
                        $gz.show();
                        $mpVerify.val(2);
                        $sendMsgTag.val(0);
                        break;
                    default:
                        break;
                }
            }
            else {
                alert(msg);
            }
        });
}

//登录
function pLogin() {
    var olHtml = '<div id="ol"></div>';
	
    var pLoginHtml = '<div id="pLogin" class="pLoginDiv">';
    pLoginHtml += '<div class="pLoginHead">';
    pLoginHtml += '<div class="pLoginTitle">登录</div>';
    pLoginHtml += '<div class="pLoginClose"></div>';
    pLoginHtml += '</div>';
    pLoginHtml += '<div class="pLoginBody">';
    pLoginHtml += '<iframe frameborder="0" width="340px" height="260px" style="overflow:hidden"';
    pLoginHtml += ' scrolling="no" src="http://images.cnfol.com/login_js/login.html#' + window.location.href + '"></iframe>';
    pLoginHtml += '</div>';
    pLoginHtml += '</div>';
	
    if (jQuery('#ol')[0]) {
        jQuery('#ol').remove();
    }
	
    if (jQuery('#pLogin')[0]) {
        jQuery('#pLogin').remove();
    }
	
    jQuery(olHtml + pLoginHtml).appendTo('body');
	
    var $pLogin = jQuery('#pLogin'), $pLoginClose = jQuery('.pLoginClose'), $ol = jQuery('#ol');
	
    function wdSize() {
        return {
            dHeight:    jQuery(document).height(),
            wWidth:     jQuery(window).width(),
            wHeight:    jQuery(window).height(),
            wScrollTop: jQuery(window).scrollTop()
        };
    }
	
    var s = wdSize();
	
    $ol.css({
        height: s['dHeight'],
        width : s['wWidth']
    }).show();
	
    $pLogin.css({
        top: s['wScrollTop'] + s['wHeight'] / 2 - $pLogin.height() / 2,
        left: (s['wWidth'] / 2) - ($pLogin.width() / 2),
        position: 'absolute',
        zIndex: 1001
    }).show();
	
    function showPLogin(){
        if (!$pLogin.is(':hidden')) {
            var s = wdSize();
			
            $pLogin.stop(true, false).animate({
                top: s['wScrollTop'] + s['wHeight'] / 2 - $pLogin.height() / 2,
                left: (s['wWidth'] / 2) - ($pLogin.width() / 2)
            });
        }
    }
	
    jQuery(window).scroll(function() {
        showPLogin();
    }).resize(function(){
        showPLogin();
    });
	
    $pLoginClose.click(function() {
        $pLogin.remove();
        $ol.remove();
    });
}
