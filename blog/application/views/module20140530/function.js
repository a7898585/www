
var baseurl = "http://new.blog.cnfol.com";
var varifyurl = baseurl+"/register/varify?";

function indexrecmmond(ischeck)
{
	
    if($.cookie('cookie\\[passport\\]\\[userId\\]') == 0){
		
        showiframe("iframeSrc","popupIframe",baseurl+"/index.php/widget/login",'','');
        return;
    }
	
    if($('#author').val()==''&&$('#arttitle').val()==''&&$('#arturl').val()=='')
    {
        showalert('请正确输入');
        return false;
    }
	
    var pattern = new RegExp("http:\/\/.*blog\.cnfol\.com\/([0-9a-zA-Z\-\_]+)\/article\/([0-9\-]+)\.html");
    var match	= pattern.exec($('#arturl').val());
    if(match == null){
        showalert('您推荐的并非中金文章，推荐失败');
        return false;
    }
    var checkurl = baseurl+'/ajaxrecmmoendart/'+match[1];
    $.post(checkurl,{
        articleid:match[2], 
        title:$('#arttitle').val(), 
        author:$('#author').val(), 
        arturl:$('#arturl').val(), 
        check:ischeck
    },function(data){
        showalert(data.error); 
        if(data.errno == 'success' && ischeck == 1){
            $('#arttitle').val(''); 
            $('#author').val('');
            $('#arturl').val('');
        }
    },'json');
}

function addarticle(uid){
    var req = baseurl+'/ajaxgetbloglistbyuid/'+uid;
    $.post(req,{
        userid:uid
    },function(data){
        if(data.errno == 'login' || data.errno == 'nouser'){
            showalert(data.error);
            setTimeout(function(){
                window.location.reload();
            },1000);
            return;
        }else{
            var domain = $.trim(data.error);
            var jumpurl = '';
            if(domain == ''){
                jumpurl = baseurl+'/register/Register'; 
            }else{
                jumpurl = baseurl+'/'+domain+'/article/Add';
            }
            //window.location.href = jumpurl; 
            window.open(jumpurl); 
			
            return;
        }
    },'json');
}
function Valipic(){
    var Now = new Date();
    $('#varifycode').attr('src',varifyurl+Now.getTime());
    $('#validate').val('');
}
function mb_strlen(str) {
    var len = 0;
    for(var i = 0; i < str.length; i++) {
        len += (str.charCodeAt(i) < 0 || str.charCodeAt(i) > 255)? 2 : 1;
    }
    return len;
}

function TabShow(di,num,divn,activecss,unactivecss){
    var obj = document.getElementById(divn+di);
    obj.className = activecss;
    var divobj = document.getElementById(divn+di+'_nr');
    divobj.style.display = 'block';
   
    for(i=0;i<num;i++)
    {
        if(di!=i)
        {
            temobj = document.getElementById(divn+i);
            temobj.className = unactivecss;
            divobj = document.getElementById(divn+i+'_nr');
            divobj.style.display = 'none';
        }
    }
}

function FunctionAjaxUpdate(url,id){
    $(id).load(url);
}

function selectall(formid, type, val){
    $('#'+formid+' input[type="'+type+'"]').each(function(){
        $(this).attr("checked",val);
    });
}
function SelectAll(form){
    var e = document.getElementById(form).elements["DelId[]"];
    if(undefined == e.length){
        e.checked=(e.checked==true)?false:true;
    }
    else{
        for(i = 0; i < e.length; i ++){
            e[i].checked=(e[i].checked==true)?false:true;
        }
    }
} 

function unSelectAll(form){
    var e = document.getElementById(form).elements["DelId[]"];
    if(undefined == e.length){
        e.checked=false;
    }
    else{
        for(i = 0; i < e.length; i ++){
            e[i].checked = false;
        }
    }
}


;
(function($) {
    $.fn.extend({
        myPopup: function() {
            var $this = this;
            var htmlStr = '<style type="text/css" id="tmpStyle">#overlay { width:100%; height:100%; filter:alpha(opacity=50); -moz-opacity:0.5; -khtml-opacity: 0.5; opacity:0.5; position:absolute; background:white; top:0; left:0; z-index:1000;}';
            htmlStr += '.popup{ position:absolute; z-index:1001 }</style>';
            htmlStr += '<div id="overlay">';
            if (isIE6()) {
                htmlStr += '<div id="iframeWrap" style="position:absolute; z-index:-1; left:0px; top:0;">';
                htmlStr += '<iframe style="background:white; width:100%; height:100%; filter:alpha(opacity=0); -moz-opacity:0"></iframe></div>';
            }
            htmlStr += '</div>';
			
            var $html = $(htmlStr);
			
            closePopups();
			
            $('body').append($html);
			
            var $overlay = $('#overlay'), $popup = $this, $pClose = $('.pClose', $this);

            function showPopup() {
                var docHeight = $(document).height();
                var winWidth = $(window).width();
                var winHeight = $(window).height();
                var winScrollTop = $(window).scrollTop();

                $overlay.css({
                    height: docHeight, 
                    width: winWidth
                }).show();
				
                if (isIE6()) {
                    $('#iframeWrap').css({
                        height: docHeight, 
                        width: winWidth
                    }).show();
                }
				
                $popup.css({
                    top: winScrollTop + winHeight / 2 - $popup.height() / 2,
                    left: (winWidth / 2) - ($popup.width() / 2), 
                    position: 'absolute', 
                    zIndex: 1006
                }).show();
            }
			
            showPopup();
			
            $(window).scroll(function() {
                if (!$this.is(':hidden')) {
                    showPopup();
                }
            }).resize(function () {
                if (!$this.is(':hidden')) {
                    showPopup();
                }
            });
			
            $pClose.click(function() {
                $this.remove();
                $('#myPopupStyle').remove();
                $html.remove();
            });
        }
    });
})(jQuery);


//名片效果
var myInfo = new Array();
function showuserinfo(userid, nouse, e) {
	
    var $showinfo = $('#showinfo'), showinfoW = $showinfo.width();
    e.stopPropagation();
	
    //弹出名片
    $showinfo.css({
        left: e.pageX + showinfoW > $(document).width() ? e.pageX - showinfoW : e.pageX,
        top : e.pageY
    }).show();
	
    //加载名片内容
    if (myInfo[userid] == undefined) {
        var url = "http://api.passport.cnfol.com/card/" + userid + "/" + $.cookie('cookie\\[passport\\]\\[userId\\]');
        $.get(url, function(result){
            myInfo[userid] = result;
            $showinfo.html(result);
        });
    }
    else {
        $showinfo.html(myInfo[userid]);
    }
	
    //点击名片以外区域隐藏名片
    $(document).click(function() {
        if (!$showinfo.is(':hidden')) {
            $showinfo.hide();
        }
    });
	
    $showinfo.click(function(e) {
        e.stopPropagation();
    });
}

function copy(id){
    var tem=document.getElementById(id);
    if(tem){
        meintext=tem.innerHTML;
    }
    if (window.clipboardData) {
        window.clipboardData.setData("Text", meintext);
    }
    else if (window.netscape) { 
        showalert('您的浏览器还不支持该复制功能！');
    }
}
function Show(id1, id2, id3){
	
    $('#'+id2).hide();
    $('#'+id3).hide();
    $('#'+id1).toggle();
}
//引用通告
var trackbacklist = new Array();
function UpdateTrackbackPage(artid,page){
    if(trackbacklist[artid] == undefined){
        trackbacklist[artid] = new Array();
    }
    if(trackbacklist[artid][page] == undefined){
        var flashcode = $('#co'+artid).attr('co');
        $.post(baseurl+'/tracbacklist/'+artid+'/'+page,{
            'co':flashcode
        },function callback(data){
            $('#tbl'+artid).html(data);
            trackbacklist[artid][page] = data;
        });
    }else{
        $('#tbl'+artid).html(trackbacklist[artid][page]);
    }
}
//删除引用通告
function deltrack(artid,trackid){
    var flashcode = $('#co'+artid).attr('co');
    $.post(baseurl+'/tracbackdel/'+trackid,{
        'co':flashcode,
        'artid':artid
    },function callback(data){
        showalert(data.error);
        if(data.errno == 'succ'){
            delete trackbacklist[artid];
            UpdateTrackbackPage(artid,1);
        }
    },'json');
}
var commentpage = new Array(); //评论cache
//文章评论提交
function checkcmtform(id){
    var centent = $('#content').val();
    if(mb_strlen(centent) < 1 || mb_strlen(centent) > 3000){
        showalert('评论内容长度应该在1-3000个字节之内');
        return false;
    }
    var validatedata = $('#validate').val();
    if(! /[0-9a-z]{4}/i.test(validatedata)){
        $('#spanvalidate').show();
        $('#spanvalidate').focus();
        return false;
    }
    $.post(baseurl+'/ajaxcomment/Action',$('#'+id).serialize(),function callback(data){
        showalert(data.error);
        if((data.errno == '200036') || (data.errno == '200037')){
            $('#content').val('');
            $('#tdvalidate').hide();
            Valipic();
        }
        else if('succ' != data.errno){
            Valipic();
        }else{
            delete commentpage[1];
            updateCommentPage('commentList','1','');
            $('#content').val('');
            $('#tdvalidate').hide();
            Valipic();
        }
    },'json');
    return false;        
}
//ajax文章评论列表
function updateCommentPage(id, page,uid){
    if(commentpage[page] == undefined){
        var flashCode = $('#'+id).attr('co');
        num = $('#'+id).attr('cnum');
        articleid = $('#'+id).attr('artid');
        $.post(baseurl+'/ajaxcomment/list/'+articleid+'/'+page+'?r='+new Date().getTime(),{
            'flashCode':flashCode,
            'num':num, 
            'replaceid':id,
            'articleuserid':uid
        },function callback(data){
            $('#'+id).html(data);
            commentpage[page] = data;
        });
    }else{
        $('#'+id).html(commentpage[page]);
    }
}
//删除评论 memid 是放全局
function DelSingleComment(cid,artid,co,page){ 
    if(page == undefined) page = 1;
    $.post(baseurl+'/ajaxcomment/Action',{
        'act':'delcomment',
        'cid':cid,
        'artid':artid,
        'flashcode':co,
        'memid':memid
    },function callback(data){
        
        showalertminute(data.error,2000,'','popupTip','TMDeleteSuccess');
        if('succ' == data.errno){
            delete commentpage[page];
            updateCommentPage('commentList', page,'');
        }
    },'json');
    return false;    
}
//批量删除
function DelComment(formid,artid,co){
    var comlist = '';
    $('#'+formid+' input[type="checkbox"]').each(function(){
        if(true == $(this).attr("checked")){
            comlist += $(this).val()+',';
        }
    });
    if(comlist == ''){
        showalert('请选择要删除的评论信息');
        return;
    }
    $.post(baseurl+'/ajaxcomment/Action',{
        'act':'delcomment',
        'cid':comlist,
        'artid':artid,
        'flashcode':co,
        'memid':memid
    },function callback(data){
        showalert(data.error);
        if('succ' == data.errno){
            delete commentpage[1];
            updateCommentPage('commentList', 1,'');
        }
    },'json');
    return false;  
}
//open函数
var g_pop=null;
//function pop show alert
function showalert(msg,popupTip,TMDeleteSuccess)
{
    /*
    if(g_pop){
        g_pop.close();
    }
    g_pop=new Popup({
        contentType:4,
        isReloadOnClose: isreload,
        width:320,
        height:80
    });
    g_pop.setContent("title","信息提示");
    g_pop.setContent("alertCon",msg);
    g_pop.build();
    g_pop.show();
    */
    Dialog(TMDeleteSuccess);
    $('#'+popupTip).text(msg);
}



function showalertminute(msg,displayTime,reload,popupTip,TMDeleteSuccess)
{
	
    if(displayTime == undefined||displayTime==''){
        displayTime = 1000;
    }
    /*
    if(g_pop){
        g_pop.close();
    }
    g_pop=new Popup({
        contentType:4,
        isReloadOnClose: isreload,
        width:320,
        height:80
    });
    g_pop.setContent("title","信息提示");
    g_pop.setContent("alertCon",msg);
    g_pop.build();
    g_pop.show();
    */
    Dialog(TMDeleteSuccess);
    $('#'+popupTip).html(msg);
    setTimeout(function(msg){ 
        Dialog.Close();
        if(reload=='1')
        {
            location.href=location.href;
        }
        else if(reload!='')
        {
            location.href=reload;
        }
    }, displayTime);
	
}

/*
function showloading(isreload)
{
    if(g_pop){
        g_pop.close();
    }
    g_pop=new Popup({
        contentType:5,
        isReloadOnClose: isreload,
        width:220,
        height:60
    });
    g_pop.setContent("title","载入提示");
    g_pop.build();
    g_pop.show();
}
*/
function showloading(isreload)
{
    if(g_pop){
        g_pop.close();
    }
    g_pop=new Popup({
        contentType:5,
        isReloadOnClose: isreload,
        width:220,
        height:60
    });
    g_pop.setContent("title","载入提示");
    g_pop.build();
    g_pop.show();
}


//function pop iframe
function showiframe(id,id2,url,width,height)
{
    Dialog(id);
	 
    var resultW=width.match(/^[0-9]+$/); 
    if(resultW!=''&&resultW!=null)
    {
        $('#'+id2).css('width',width+'px');
    }
	 
    var resultH=height.match(/^[0-9]+$/); 
    if(resultH!=''&&resultH!=null)
    {
        $('#'+id2).css('height',height+'px');
    }
	 
    $('#'+id2).attr('src',url);
/*
    if(g_pop){
        g_pop.close();
    }
    g_pop=new Popup({
        contentType:1,
        isReloadOnClose:isreload,
        width:wwidth,
        height:wheight
    });
   
    g_pop.setContent("title",title);
    g_pop.setContent("contentUrl",encodeURI(url));
    g_pop.build();
    g_pop.show();
    */
}
function Open(type,username,friendNickname,friendUserID){ 
    if($.cookie('cookie\\[passport\\]\\[userId\\]') == 0){
       
        showiframe("iframeSrc","popupIframe",baseurl+"/index.php/widget/login",'','');
        return;
    }
    type = (type==1)?'添加好友':'发送信息';
    page = (type=='添加好友')?'addFriend.php':'sendMessage.php';
    url  = "http://passport.cnfol.com/_blogmodule/"+page+"?friendUsername=";
    url += username+"&friendUserID="+friendUserID+"&friendNickname=";
    url += friendNickname+"&domain="+baseurl+"/ajaxclosegpopup/"+friendUserID+'&r='+new Date().getTime();
    showiframe(url, 350, 210, type, false);
}

function OpenMessage(type,username,friendNickname,friendUserID){ 
	
    if($.cookie('cookie\\[passport\\]\\[userId\\]') == 0){
        
        showiframe("iframeSrc","popupIframe",baseurl+"/index.php/widget/login",'','');
        return;
    }
    type = (type==1)?'添加好友':'发送信息';
    page = (type=='添加好友')?'addFriend.php':'sendMessage';
    url  = baseurl+"/index.php/widget/"+page+"?friendusername=";
    url += username+"&friendnickname=";
    //url += friendNickname+"&domain="+baseurl+"/ajaxclosegpopup/"+friendUserID+'&friendUserID='+friendUserID+'&r='+new Date().getTime();
    url += friendNickname+'&frienduserid='+friendUserID;
    //showiframe(url, 350, 210, type, false);
    url=encodeURI(url);
    
    showiframe("iframeSrc","popupIframe",url+'&r='+new Date().getTime(),'','');
}

//this
//文章转载用
function transshipment(type,articletime,articleid,memberid,loginmemberid,showid,istransshipment){
    if($.cookie('cookie\\[passport\\]\\[userId\\]') == 0){
        showiframe("iframeSrc","popupIframe",baseurl+"/index.php/widget/login",'','');
        return;
    }
    
    if(istransshipment=='1')
    {
        showalertminute('不能转载自己的文章',2000,'','popupTip','TMDeleteSuccess');
        return;
    }
	
    type = (type==1)?'添加好友':'转载';
    page = (type=='添加好友')?'addFriend.php':'articletransshipment';
    url  = baseurl+"/index.php/widget/"+page+"?articletime="+articletime+"&articleid="+articleid+'&memberid='+memberid+'&loginmemberid='+loginmemberid+'&showid='+showid+'&type=json&r='+new Date().getTime();
    
    $.ajax({
        type: 'get',
        url: url, 
        dataType: 'json',
        success: function(data){
            if(data.error=='1')
            {
                showalertminute('转载成功',2000,'','popupTip','TMDeleteSuccess');
                transshipmentnum(showid);
            }
            else
            {
                showalertminute('已经转载过',2000,'','popupTip','TMDeleteSuccess');
            }
			
        },
        error:function (XMLHttpRequest, textStatus, errorThrown) {
            showalertminute('系统繁忙，稍后再试',2000,'','popupTip','TMDeleteSuccess');
        }
    });
//showiframe(url, 350, 110, type, false);
}

//文章转载用(列表用)
function transshipmentlist(type,articletime,articleid,memberid,loginmemberid,showid,istransshipment){
    if($.cookie('cookie\\[passport\\]\\[userId\\]') == 0){
       
        showiframe("iframeSrc","popupIframe",baseurl+"/index.php/widget/login",'','');
        return;
    }
    
    if(istransshipment=='1')
    {
        showalertminute('不能转载自己的文章',2000,'','popupTip','TMDeleteSuccess');
        return;
    }
	
    type = (type==1)?'添加好友':'转载';
    page = (type=='添加好友')?'addFriend.php':'articletransshipment';
    url  = baseurl+"/index.php/widget/"+page+"?articletime="+articletime+"&articleid="+articleid+'&memberid='+memberid+'&loginmemberid='+loginmemberid+'&showid='+showid+'&type=json&r='+new Date().getTime();
	
    $.ajax({
        type: 'get',
        url: url, 
        dataType: 'json',
        success: function(data){
            if(data.error=='1')
            {
                showalertminute('转载成功',2000,'','popupTip','TMDeleteSuccess');
                transshipmentnum(showid);
            }
            else
            {
                showalertminute('已经转载过',2000,'','popupTip','TMDeleteSuccess');
            }
			
        },
        error:function (XMLHttpRequest, textStatus, errorThrown) {
            showalertminute('系统繁忙，稍后再试',2000,'','popupTip','TMDeleteSuccess');
        }
    });
}
//this


//this
//文章收藏用
function articlecollect(type,articletime,articleid,memberid,loginmemberid,showid,iscollect){
    if($.cookie('cookie\\[passport\\]\\[userId\\]') == 0){
        
        showiframe("iframeSrc","popupIframe",baseurl+"/index.php/widget/login",'','');
        return;
    }
    
    if(iscollect=='1')
    {
        showalertminute('同一篇文章不能重复收藏',2000,'','popupTip','TMDeleteSuccess');
        return;
    }
	
    type = (type==1)?'添加好友':'收藏';
    page = (type=='添加好友')?'addFriend.php':'articlecollect';
    url  = baseurl+"/index.php/widget/"+page+"?articletime="+articletime+"&articleid="+articleid+'&memberid='+memberid+'&loginmemberid='+loginmemberid+'&showid='+showid+'&type=json&r='+new Date().getTime();
    
    $.ajax({
        type: 'get',
        url: url, 
        dataType: 'json',
        success: function(data){
            if(data.error=='1')
            {
                showalertminute('收藏成功',2000,'','popupTip','TMDeleteSuccess');
                transshipmentnum(showid);
            }
            else
            {
                showalertminute('已经收藏过',2000,'','popupTip','TMDeleteSuccess');
            }
			
        },
        error:function (XMLHttpRequest, textStatus, errorThrown) {
            showalertminute('系统繁忙，稍后再试',2000,'','popupTip','TMDeleteSuccess');
        }
    });
}


//文章收藏用(列表用)
function articlecollectlist(type,articletime,articleid,memberid,loginmemberid,showid,iscollect){
    if($.cookie('cookie\\[passport\\]\\[userId\\]') == 0){
        
        showiframe("iframeSrc","popupIframe",baseurl+"/index.php/widget/login",'','');
        return;
    }
    
    if(iscollect=='1')
    {
        showalertminute('同一篇文章不能重复收藏',2000,'','popupTip','TMDeleteSuccess');
        return;
    }
	
    type = (type==1)?'添加好友':'收藏';
    page = (type=='添加好友')?'addFriend.php':'articlecollect';
    url  = baseurl+"/index.php/widget/"+page+"?articletime="+articletime+"&articleid="+articleid+'&memberid='+memberid+'&loginmemberid='+loginmemberid+'&showid='+showid+'&type=json&r='+new Date().getTime();
    
    $.ajax({
        type: 'get',
        url: url, 
        dataType: 'json',
        success: function(data){
            if(data.error=='1')
            {
                showalertminute('收藏成功',2000,'','popupTip','TMDeleteSuccess');
                transshipmentnum(showid);
            }
            else
            {
                showalertminute('已经收藏过',2000,'','popupTip','TMDeleteSuccess');
            }
			
        },
        error:function (XMLHttpRequest, textStatus, errorThrown) {
            showalertminute('系统繁忙，稍后再试',2000,'','popupTip','TMDeleteSuccess');
        }
    });
    
}
//this

//this
//文章举报用
function articlereport(type,articleid,memberid,reportuserid){
	
    if($.cookie('cookie\\[passport\\]\\[userId\\]') == 0){
        
        showiframe("iframeSrc","popupIframe",baseurl+"/index.php/widget/login",'','');
        return;
    }

    type = (type==1)?'添加好友':'举报';
    page = (type=='添加好友')?'addFriend.php':'articlereport';
    url  = baseurl+"/index.php/widget/"+page+"?articleid="+articleid+"&memberid="+memberid+"&type=json&reportuserid="+reportuserid;
	
    $.ajax({
        type: 'get',
        url: url, 
        dataType: 'json',
        success: function(data){
            if(data.error=='1')
            {
                showalertminute('举报成功',2000,'','popupTip','TMDeleteSuccess');
				
            }
            else
            {
                showalertminute('系统繁忙，稍后再试',2000,'','popupTip','TMDeleteSuccess');
            }
			
        },
        error:function (XMLHttpRequest, textStatus, errorThrown) {
            showalertminute('系统繁忙，稍后再试',2000,'','popupTip','TMDeleteSuccess');
        }
    });
    
}
//this


//取消好友关注//
function delAttention(userid,act)
{
    var url = baseurl+"/index.php?c=blog&m=action&act="+act;
    var params = {
        "friendIDs":userid
    };
	
    $.getJSON(url+"&r="+new Date().getTime(), params, function(json){
	    
        if(json.errno=='00')
        {
            window.location.reload();
        }
        else
        {
            alert(json.error);
        }
		
    });
}


//送鲜花
function sendgif(userid,nickname, tblid, domian, co, num, source, flush){
    //if($.cookie('cookie\\[passport\\]\\[userId\\]') == 0){
    //	lrPopup('log', 500, 300, 'http://images.cnfol.com/login_js/login.html', 700, 670, 'http://images.cnfol.com/login_js/register.html');return;
    //}
    var FlushUrl = SourceURL = '';
	
    SourceURL = encodeURIComponent(source);
	
    if(flush != ""){
        FlushUrl = '&FlushUrl='+flush;
    }

    var url = 'http://passport.cnfol.com/giftapi/newSend?';
    url += 'SourceTypeDesc=blog&SourceTypeID=1&GiftID=1&ToUserID='+userid;
    //url += '&SourceURL='+SourceURL+'&SourceTabID='+tblid+'&vcode='+co;
    url += '&SourceURL='+SourceURL+'&SourceTabID='+tblid;
    if(num != ""){
        url += '&LimitCnt='+num;
    }
    url += FlushUrl;
	
    var title = '我要给 '+nickname+' 送鲜花';
    showiframe(url, 350, 210, title, false);
}

//设置图片大小
function SetImgWidth(obj,w,h){
    var srcImage = new Image();
    srcImage.src=obj.src;
    var srcW=srcImage.width;  
    var srcH=srcImage.height;
    if(srcW>srcH){
        if(srcW>w){
            obj.width=newW=w;
            obj.height=newH=(w/srcW)*srcH;
        }else{
            obj.width=newW=srcW;
            obj.height=newH=srcH;
        }
    }else{
        if(srcH>h){
            obj.height=newH=h;
            obj.width=newW=(h/srcH)*srcW;
        }else{
            obj.width=newW=srcW;
            obj.height=newH=srcH;
        }
    }
    if(newW>w){
        obj.width=w;
        obj.height=newH*(w/newW);
    }else if(newH>h){
        obj.height=h;
        obj.width=newW*(h/newH);
    }
    obj.style.display='';
}

//添加相册
function addalbum(domain){
    var seturl = baseurl+'/'+domain+'/album/Add';
    //showiframe(seturl, 480, 270, "创建相册", false);
    
    showiframe("iframeSrc","popupIframe",seturl,'495','250');
//return false;
}
//编辑相册
function editalbum(abid, domain){
    var seturl = baseurl+'/'+domain+'/album/Edit'+'?albumID='+abid+'&flag=1';
    
    showiframe("iframeSrc","popupIframe",seturl,'','');
/*
    g_pop=new Popup({
        contentType:1,
        isReloadOnClose:false,
        width:430,
        height:280
    });
    g_pop.setContent("title","编辑相册");
    g_pop.setContent("contentUrl","");
    g_pop.build();
    $("#albumID").val(abid);
    $("#act").val('edit');
    $("#autorform").attr('action',seturl);
    $("#autorform").attr('target', g_pop.iframeIdName);
    $("#autorform").submit();
    g_pop.show();
    return false;
    */
}

//删除相册
function delalbum(abid, domain, co){
	
    Dialog('TMDelete');
    $('#abid').val(abid);
    $('#domain').val(domain);
    $('#co').val(co);
/*
    var seturl = baseurl+'/'+domain+'/album/Action';
    g_pop=new Popup({
        contentType:1,
        isReloadOnClose:false,
        width:320,
        height:165
    });
    g_pop.setContent("title","删除相册");
    g_pop.setContent("contentUrl","");
    g_pop.build();
    $("#albumID").val(abid);
    $("#flashCode").val(co);
    $("#act").val('delalbum');
    $("#autorform").attr('action',seturl);
    $("#autorform").attr('target', g_pop.iframeIdName);
    $("#autorform").submit();
    g_pop.show();
    return false;
    */
}

function delalbumSure()
{
    Dialog.Close();

    $.ajax({
        type: 'post',
        url: baseurl+'/'+$('#domain').val()+'/album/Action', 
        data: 'flashCode='+$('#co').val()+'&albumID='+$('#abid').val()+'&act=delalbum', 
        dataType: 'json',
        success: function(data){
            showalertminute(data.error,2000,1,'popupTip','TMDeleteSuccess');
        },
        error:function (XMLHttpRequest, textStatus, errorThrown) {
            showalertminute('发生错误，请重新提交保存'+XMLHttpRequest.status+','+XMLHttpRequest.readyState+','+textStatus,2000,1,'popupTip','TMDeleteSuccess');
        }
    });
}

//添加图片
function addphoto(domain,photoId){
	
    var seturl = baseurl+'/'+domain+'/photo/upload?AlbumID='+photoId;
    //showiframe(seturl, 480, 310, "上传照片", false);	
    showiframe("iframeSrc","popupIframe",seturl,'495','280');
    return false;
}
var a_cssurl= new Array();
var a_cssid = new Array();
var a_imgid = new Array();
function getCssimg(img,url,id)
{
    var timg = img.src;
    img.src='http://images.cnfol.com/uploads/mod_blog/1/nothing.gif';
    a_cssurl[a_cssurl.length] = url;
    a_cssid[a_cssid.length] = id;
    a_imgid[a_imgid.length] = timg;
}

function use(styleid){
    g_pop =new Popup({
        contentType:3,
        isReloadOnClose:false,
        width:340,
        height:80
    });
    g_pop.setContent("title","信息提示");
    g_pop.setContent("confirmCon","您确定要使用此样式吗？");
    g_pop.setContent("callBack",function(para){
        g_pop.close();
        $('#templateID').val(styleid);
        $.post(actionurl,$('#submitform').serialize(),function(data){
            showalert(data.error, false);
            if(data.errno == 'success'){
                setTimeout(function(){
                    top.window.location.href=returnbackurl;
                },3000);
            }
        },'json');
    }); 
    g_pop.build();
    g_pop.show();
}
//delete  article
function del(artid,ismut,recommend,sortid) {
    g_pop = new Popup({
        contentType:3, 
        isReloadOnClose:false, 
        width:340, 
        height:80
    });
    g_pop.setContent("title", "删除文章");
    g_pop.setContent("confirmCon", "您确定要删除此文章吗？");
    g_pop.setContent("callBack", delCallback);
    g_pop.setContent("parameter", {
        id:artid,
        ismut:ismut,
        recommend:recommend,
        sortid:sortid,
        memid:memberid,
        flashCode:flashCode,
        act:'del'
    });
    g_pop.build();
    g_pop.show();
}
function delCallback(para){
    showloading();
    $.post(action,jQuery.param(para),function(data){
        /*
		if(data.errno != "success"){
			showalert(data.error);
		}
		*/
        showalert(data.error);
        setTimeout(function(){
            window.location.reload();
        },1000);
    },'json');
}
//this

function delThisArticle(artid,domainname)
{
    Dialog('TMDelete');
    $('#delArtId').val(artid);
    $('#deldomainname').val(domainname);
}
function delSure()
{
    Dialog.Close();
    var formId=$('#delArtId').val();

    $.ajax({
        type: 'post',
        url: baseurl+'/index.php/article/Action/'+$('#deldomainname').val(), 
        data: $('#article_action_form_'+formId).serialize(), 
        dataType: 'json',
        success: function(data){
            showalertminute(data.error,2000,1,'popupTip','TMDeleteSuccess');
        },
        error:function (XMLHttpRequest, textStatus, errorThrown) {
            showalertminute('发生错误，请重新提交保存'+XMLHttpRequest.status+','+XMLHttpRequest.readyState+','+textStatus,2000,1,'popupTip','TMDeleteSuccess');
        }
    });
}

function isTopSure()
{
    Dialog.Close();
    var formId=$('#delArtId').val();
	
    $.ajax({
        type: 'post',
        url: baseurl+'/index.php/article/Action/'+$('#deldomainname').val(), 
        data: $('#article_action_form_'+formId).serialize()+'&act=top', 
        dataType: 'json',
        success: function(data){
            showalertminute(data.error,2000,1,'popupTip','TMDeleteSuccess');
        },
        error:function (XMLHttpRequest, textStatus, errorThrown) {
            showalertminute('发生错误，请重新提交保存'+XMLHttpRequest.status+','+XMLHttpRequest.readyState+','+textStatus,2000,1,'popupTip','TMDeleteSuccess');
        }
    });
}

function submit_form(formid,artid){
    $('#'+formid).attr('action', editurl);
    $('#'+formid + ' #articleid').val(artid);
    $('#'+formid).submit();
}

function transshipmentnum(id)
{
    $('#'+id).html(parseInt($('#'+id).html())+1);
}

function movesort(direction,obj)
{
    if(direction=='up')
    {
        if($('.signSort').first().attr('name')==$(obj).parent().parent().attr('name'))
        {
        	
            showalertminute('不可上移',2000,'','popupTip','TMDeleteSuccess');
            return;
        }
		
        var currentRank=$(obj).parent().parent().attr('name');
        var upRank=$(obj).parent().parent().prev().attr('name');
		
        /*--暂时显示--*/
        var currentRankId=$(obj).parent().parent().attr('id');
        var upRankId=$(obj).parent().parent().prev().attr('id');
		
        $('#Rank_'+currentRankId).val(upRank);
        $('#Rank_'+upRankId).val(currentRank);
        /*--暂时显示--*/
		
        $(obj).parent().parent().attr('name',upRank);
        $(obj).parent().parent().prev().attr('name',currentRank);
		
        $(obj).parent().parent().prev().before($(obj).parent().parent());
		
    //$('a[class=upMove]').css('color','#661212');
    //$('a[class=downMove]').css('color','#661212');
    //$('.signSort').first().find('a[class=upMove]').css('color','#666666');
    //$('.signSort').last().find('a[class=downMove]').css('color','#666666');
		
    }
    else if(direction=='down')
    {
        if($('.signSort').last().attr('name')==$(obj).parent().parent().attr('name'))
        {
        	
            showalertminute('不可下移',2000,'','popupTip','TMDeleteSuccess');
            return;
        }
		
		
        var currentRank=$(obj).parent().parent().attr('name');
        var upRank=$(obj).parent().parent().next().attr('name');
		
        /*--暂时显示--*/
        var currentRankId=$(obj).parent().parent().attr('id');
        var upRankId=$(obj).parent().parent().next().attr('id');
		
        $('#Rank_'+currentRankId).val(upRank);
        $('#Rank_'+upRankId).val(currentRank);
        /*--暂时显示--*/
		
        $(obj).parent().parent().attr('name',upRank);
        $(obj).parent().parent().next().attr('name',currentRank);
		
        $(obj).parent().parent().next().after($(obj).parent().parent());
		
    //$('a[class=upMove]').css('color','#661212');
    //$('a[class=downMove]').css('color','#661212');
    //$('.signSort').first().find('a[class=upMove]').css('color','#666666');
    //$('.signSort').last().find('a[class=downMove]').css('color','#666666');
    }
}

function saveSort()
{
    /*
    var sortStr='';
    var sortNum=0;
    $('.signSort').each(function(){
        if($(this).attr("name")==$(this).find('.RankValue').val())
        {
            $(this).find('.SortIDValue').val('');
            sortNum++;
        }
		
    });
    sortNum=parseInt(sortNum)+2;
	
    if($('#sortnum').val()==sortNum)
    {
        alert('排序未变更');
        return;
    }
    */
	
    $.ajax({
        type: 'post',
        url: baseurl+'/index.php/articlesort/editSort', 
        data: $('#editSort').serialize(), 
        dataType: 'json',
        success: function(data){
            showalertminute(data.error,2000,1,'popupTip','TMDeleteSuccess');
        },
        error:function (XMLHttpRequest, textStatus, errorThrown) {
            showalertminute('发生错误，请重新提交保存'+XMLHttpRequest.status+','+XMLHttpRequest.readyState+','+textStatus,2000,1,'popupTip','TMDeleteSuccess');
        	
        }
    });
	
}

function DialogSort(id,sortid,idname)
{
    $('#alertSortID').val(sortid);
    $('#alertName').val($('#'+idname).html());
}

function editSortName(id,Nameid)
{
    Dialog.Close();
    if($('#'+Nameid).val().replace(/(^\s*)|(\s*$)/g,"")=='')
    {
        
        showalertminute('分类名不能为空',2000,'','popupTip','TMDeleteSuccess');
        return;
    }
	
    $.ajax({
        type: 'post',
        url: baseurl+'/index.php/articlesort/action', 
        data: $('#'+id).serialize(), 
        dataType: 'json',
        success: function(data){
            showalertminute(data.error,2000,'','popupTip','TMDeleteSuccess');
        	
            if(data.errno=='success')
            {
                $('#editName_'+$('#alertSortID').val()).html($('#'+Nameid).val());
                $('input[name=NameValue_'+$('#alertSortID').val()+']').val($('#'+Nameid).val());
            }
			
        },
        error:function (XMLHttpRequest, textStatus, errorThrown) {
           
            showalertminute('发生错误，请重新提交保存'+XMLHttpRequest.status+','+XMLHttpRequest.readyState,2000,1,'popupTip','TMDeleteSuccess');
        }
    });
}

function addSortName(id,Nameid,domainName,memberid)
{
	
    var checkurl = baseurl+"/register/Check";
	
    if($('.'+Nameid).val().replace(/(^\s*)|(\s*$)/g,"")=='')
    {
    	
        showalertminute('分类名不能为空',2000,'','popupTip','TMDeleteSuccess');
        return;
    }
   
    if($('.'+Nameid).val().replace(/(^\s*)|(\s*$)/g,"").length>30)
    {
    	
        showalertminute('分类名长度不能超过30个字符',2000,'','popupTip','TMDeleteSuccess');
        return;
    }
    
    if($('.signSort').size()>12)
    {
        showalertminute('分类最多不能超过15个',2000,'','popupTip','TMDeleteSuccess');
        return;
    }
	
    showalert('加载中...','popupTip','TMDeleteSuccess');
    $.post(checkurl,{
        act:'asort',
        memberid:memberid,
        name:$('.'+Nameid).val()
    },function(data){
        if(data.errno!= 'success')
        {
            
            showalertminute(data.error,2000,'','popupTip','TMDeleteSuccess');
        }
        else
        {
            $.ajax({
                type: 'post',
                url: baseurl+'/index.php/articlesort/action', 
                data: $('#'+id).serialize(), 
                dataType: 'json',
                success: function(data){
                    var html='<li class="sort signSort" id="sort'+data.SortID+'" name="'+data.Rank+'"><a href="'+baseurl+'/'+domainName+'/manage/article/SortList/'+data.SortID+'" id="editName_'+data.SortID+'" class="Title" >'+$("."+Nameid).val()+'</a><input type="hidden" name="RankValue_'+data.SortID+'" id="Rank_sort'+data.SortID+'" value="'+data.Rank+'" /><span class="Handle"><input type="hidden" name="SortIDValue[]" class="SortIDValue" value="'+data.SortID+'" /><input type="hidden" class="RankValue" value="'+data.Rank+'" /><input type="hidden" name="NameValue_'+data.SortID+'" class="NameValue" value='+$("."+Nameid).val()+'  /><a href=javascript:DialogSort("DeterminePop","'+data.SortID+'","editName_'+data.SortID+'");Dialog("Update"); >[编辑]</a> <a href="javascript:void(0)" onclick=javascript:Dialog("Confirm");setSortID("'+data.SortID+'"); >[删除]</a>&nbsp;&nbsp;<a href="javascript:void(0)" class="upMove Up" onclick=javascript:movesort("up",this); ></a>&nbsp;&nbsp;<a href="javascript:void(0)" class="downMove Down" onclick=javascript:movesort("down",this); ></a></span></li>';
                    if(data.errno!='success')
                    {
                        showalertminute(data.error,2000,'','popupTip','TMDeleteSuccess');
                        return false;
                    }
                    
                    if($('.signSort').size()!=0)
                    {
                        $('.signSort').first().before(html);
                    }
                    else
                    {
                        $('.sort').first().before(html);
                    }
							
                    //$('a[class=upMove]').css('color','#661212');
                    //$('a[class=downMove]').css('color','#661212');
                    //$('.signSort').first().find('a[class=upMove]').css('color','#666666');
                    //$('.signSort').last().find('a[class=downMove]').css('color','#666666');
                    
                    $('.'+Nameid).val('');
                   
                    showalertminute(data.error,2000,'','popupTip','TMDeleteSuccess');
                    
							
                },
                error:function (XMLHttpRequest, textStatus, errorThrown) {
                    
                    showalertminute('发生错误，请重新提交保存'+XMLHttpRequest.status+','+XMLHttpRequest.readyState+','+textStatus,2000,1,'popupTip','TMDeleteSuccess');
                }
            });
        }
    },'json');
}
//this

//this
//加入/解除黑名单
function blacklist(userid,type,friendid,FType,sign){
    var url  = baseurl+"/index.php/widget/addblacklist?userid="+userid+"&type="+type+"&friendid="+friendid+"&ftype="+FType+'&sign='+sign+'&r='+new Date().getTime();
	
    $.ajax({
        type: 'get',
        url: url, 
        dataType: 'json',
        success: function(data){
            if(data.error=='00')
            {
                if(sign=='1')
                {
                    showalertminute('加入黑名单成功',false,2000);
					
                    $('.delblack_'+friendid).css('display','');
                    $('.addblack_'+friendid).css('display','none');
                }
                else
                {
                    showalertminute('解除黑名单成功',false,2000);
                    $('.delblack_'+friendid).css('display','none');
                    $('.addblack_'+friendid).css('display','');
                }
            }
            else
            {
                if(sign=='1')
                {
                    showalertminute('加入黑名单失败',false,2000);
                }
                else
                {
                    showalertminute('解除黑名单失败',false,2000);
                }
            }
			
        },
        error:function (XMLHttpRequest, textStatus, errorThrown) {
            showalertminute('系统繁忙，稍后再试',false,2000);
        }
    });
	
//showiframe(url, 350, 110, type, false);
}
//this
/*
*  跳转页面
*/
function jump($url)
{	
    if($(".Inpjump").val()=="")
    {
        return false;
    }
    var url = $url+$(".Inpjump").val();
    window.location.href = url;		
}
function enterPress($url,e)
{
    var e = e || window.event;
    if(e.keyCode==13) {
        jump($url);
    }
}

function jiaarray(url,title)
{
    jiathis_config.url=url;
    jiathis_config.title=title;
}


function showAll(own,id,idname1,idname2)
{
	
    if($('#'+idname2+id).val()=='0')
    {
	
        $('#'+idname1+id).children().eq(1).css('display','');
        if($('#'+idname1+id).find('a').length>4)
        {
            $('#'+idname1+id).children().eq(1).after('|');
        }
	
        if(own!='none')
        {
            $('#'+idname1+id).children().eq(0).css('display','').after('|');
        }
        $('#'+idname2+id).val('1');
    }
	
}

$(document).ready(function(){
    $('.refid').live('mouseover',function(){
        var id=$(this).attr('refid');
        id=id.match(/^[0-9]+/);
		
        $(this).attr('refid',id+'_'+new Date().getTime());
    })
})
