//var baseurl = "http://new.blog.cnfol.com";

//从URL中获取参数
function getUrlParam(url, paramName, defaultValue){
    defaultValue = (typeof defaultValue == 'undefined' || defaultValue == '') ? 0 : defaultValue;

    var paramStartOffset = paramEndOffset = 0;
    var paramTmpValue = '';

    paramName += '=';

    paramStartOffset = url.indexOf(paramName);
    if (paramStartOffset == -1)
    {
        return defaultValue;
    }

    paramStartOffset += paramName.length;
    paramTmpValue = url.substring(paramStartOffset);
    paramEndOffset = paramTmpValue.indexOf('&');
    if (paramEndOffset == -1)
    {
        return paramTmpValue;
    }

    return paramTmpValue.substring(0, paramEndOffset);
}


//未注册博客发表文章时弹出用
function showalertblog(message,displayTime,callback){
    if(displayTime == undefined){
        displayTime = 1000;
    }
	
    if(message != undefined){
        $('#blogmsgContent').html(message);
    }
	
    Dialog('Bg','blogMessagePop');
	
	
    $('span#tips_time').html(displayTime/1000);
    int=setInterval(function(){ 
        if(parseInt($('span#tips_time').html())>0){
            $('span#tips_time').html(parseInt($('span#tips_time').html())-1);
        }
        else
        {
            clearInterval(int);
        }
    }, 1000);
	
	
    setTimeout(function(){ 
        if($('#blogMessagePop').css('display') == 'block'){
            Dialog.Close('blogMessagePop');
        }
        if(callback != undefined){
            callback();
        }
    }, displayTime);
	
}


//显示loading
/*和删除文章冲突
function showloading(msg){
	if(msg == undefined){ msg = '让数据飞一会...'}
	$('#msgContent').html(msg);
	Dialog('Bg','messagePop');
}
*/

/**
 * 根据长度截取先使用字符串，超长部分追加...
 * @param str 对象字符串
 * @param len 目标字节长度
 * @return 处理结果字符串
 */
function cutString(str, len) {
    //length属性读出来的汉字长度为1
    if(str.length*2 <= len) {
        return str;
    }
    var strlen = 0;
    var s = "";
    for(var i = 0;i < str.length; i++) {
        s = s + str.charAt(i);
        if (str.charCodeAt(i) > 128) {
            strlen = strlen + 2;
            if(strlen >= len){
                return s.substring(0,s.length-1);
            }
        } else {
            strlen = strlen + 1;
            if(strlen >= len){
                return s.substring(0,s.length-2);
            }
        }
    }
    return s;
}

/* 自动截取摘要 */
function cutSummary1(){	
	
    var contentvalue = $('textarea[name=summary]').val();
	
    contentvalue = contentvalue.replace(/[\r]/g,"");	 //替换换行 
    contentvalue = contentvalue.replace(/[\n]/g,"");	 //替换回车 
    contentvalue = contentvalue.replace(/[\s]/g,"");	 //替换空格 
	
    if(contentvalue != ''){
        summary = cutString(contentvalue,300);
        $('textarea[name=summary]').val(summary).css({
            'color':'#333'
        });
    }
}

/* 自动截取摘要 */

function cutSummary(){	
    var	contentvalue=htmlTag(editor.html());
    contentvalue=filterAllHtml(contentvalue);

    var summaryMax=150;
    var summaryMax1=summaryMax;
    var summary='';
    if(contentvalue==''){
	
        showalertminute('文章内容长度应该在1-150000个字节之内',1000,'','popupTip','TMDeleteSuccess');
    }else{
	
        for(var i=0; i<=summaryMax; i++)
        {
            txt=contentvalue.substring(i,i+1);
		
            var   re=/[^\x00-\xff]/g;  
 		
            if(re.test(txt)){
            //summaryMax1--;//暂时屏蔽
			
            }
        }
        summary=contentvalue.substring(0,summaryMax1);
	
        $('.brief').val(summary);
    }
}

function htmlTag(htmlCode){
    re = /<(\/\s*)?((\w+:)?\w+)(\w+(\s*=\s*((["'])(\\["'tbnr]|[^\7])*?\7|\w+)|.{0})|\s)*?(\/\s*)?>/ig
    htmlCode=htmlCode.replace(re,'');
    htmlCode=htmlCode.replace(new RegExp("&lt;","gm"),"<");
    htmlCode=htmlCode.replace(new RegExp("&gt;","gm"),">");
    htmlCode=htmlCode.replace(new RegExp("&nbsp;","gm")," ");
    htmlCode=htmlCode.replace(new RegExp("nbsp;","gm")," ");
    htmlCode=htmlCode.replace(new RegExp("&amp;","gm")," ");
    htmlCode=htmlCode.replace(new RegExp("&quot;","gm"),'"');
    htmlCode=htmlCode.replace(new RegExp("&middot;","gm"),"·");
    htmlCode=htmlCode.replace(new RegExp("ldquo;","gm"),"");
    return htmlCode;
}

function filterAllHtml(content)
{
    var contents=content.replace(/(\&nbsp\;)+|(^\s*)|(\s*$)|(\<br\s*\/>)+/g,'');
    var rel = /\s+/g;
    return contents.replace(rel,'');
}



function toggle(id,cName){
    var obj = C.G(id);
    var sAddC='';
    if(cName == null){
        sAddC = 'show';
    }else{
        sAddC = cName;
    }
    if (obj.className.indexOf(sAddC)!=-1){
        C.DelClass(obj, sAddC);
        C.DelClass(C.G('comlis1'+id.replace(/[A-Za-z]+/g,"")), sAddC);
    }else{
        C.AddClass(obj, sAddC);
    }
}

function autoHeight(obj){
    $(obj).height(this.scrollHeight);
}

/* 保存用户基本资料 */
function saveUser(){
    showloading();
    $.ajax({
        type: 'post',
        url: baseurl+'?c=manage&m=action&act=save', 
        data: $('#userForm').serialize(), 
        dataType: 'json',
        success: function(data){
            showalert(data.error);
        },
        error:function (XMLHttpRequest, textStatus, errorThrown) {
            showalert('发生错误，请重新提交保存'+XMLHttpRequest.status+','+XMLHttpRequest.readyState+','+textStatus);
        }
    });
}

function validate()
{
    if($('#supisvalidate').val()==0)
    {
        $('input[name=isvalidate]').val(0);	
        $('.commentverifycode').css('display','none');
    }
}

/* 加关注 */
function follow(userid){
    $.get(baseurl+"?c=user&m=action&act=add",{
        "friendIDs":userid
    },function(data){
        showalert(data.error);
    });
}

function addTag(tagname){
    var tag = $.trim($('#supTag').val());
    if(tag.indexOf(tagname) != -1){
        return;
    }
    if(tag == ""){
        tag = tagname;
    }else{
        tag = tag+','+tagname;
    }
    $('#supTag').val(tag).css({
        'color':'#333'
    });
	
}

function isNum(objId, eSpanId, eText, minFig, maxFig) {
    if (arguments.length == 3) {
        var reg = /^\d+$/;
    } else {
        var reg = eval("/^\\d{" + minFig + "," + maxFig + "}$/");
    }
    if (!reg.test($('#' + objId).val())) {
        $('#' + eSpanId).html(eText);
        return false;
    } else {
        $('#' + eSpanId).html('');
        return true;
    }
}

function quote(qid,editid){
    var txt = $('#quote'+qid).text();
    if(txt == ''){
        alert('不能引用纯表情或空的内容');
    }
    else
    {
        $('#repEdit'+editid).val($('#floor_'+qid+'_'+editid).text()+' '+txt+'<br>');
    }
}


//取消好友关注//
function delAttention(userid)
{
    var url = baseurl+"?c=user&m=action&act=del";
    var params = {
        "friendIDs":userid
    };
		
    $.getJSON(url+"&r="+new Date().getTime(), params, function(json){
        showalert(json.error,1000,function(){
            if($('.attention_2').text()==''||$('.attention_2').text()==null)
            {
                $('#attention_'+userid).remove();
                if($('.fanslis').size()==0)
                {
                    window.location.reload();
                }
                Dialog.Close('messagePop');
            }
            else
            {
                $('#'+userid).text('关注');
                $('#isfriend_'+userid).text('1');
                $('.btnBig').text('关注选中用户');
                $('.btnBig').attr('id','moreattention');
                $('.select_btn').css('display','');
                $('.select_btn').text('全部关注');
                $('.select_btn').attr('id','moreattention_all');
                $('#isFrends_'+userid).css('display','none');
                Dialog.Close('messagePop');
				
            }
        });
    });
}

function ShowTag(tagname){
    var tag = $.trim($('#tag').val());
    if(tag.indexOf(tagname) != -1){
        return;
    }
    if(tag == ""){
        tag = tagname;
    }else{
        tag = tag+','+tagname;
    }
    $('#tag').val(tag);
}

//热门标签用
$(document).ready(function(){
    $('.hotTagShow').live('click',function(){
        $(this).parent().css('display','none');
        $('.hotTagHidden').first().attr('class','hotTagShow').parent().css('display','inline');
    })
	
	
	
    /* 新动态加载事件 */
    $('#newlistBtn > a').live('click', function(currentEvent){
		
        currentEvent.preventDefault();
        //$('#newlistBtn').html('<a class="notInd">正在为您加载数据...</a>');
        $.get(this.href+"&r="+new Date().getTime(), function(result){
            if(result.data!=''){
                addmyblog(result.data,result.num);
                $('#newlistBtn').hide();
				
            } else{
            //alert('没有新的动态哦。');
            //alert('系统繁忙!');
            //$('#newlistBtn').hide();
            }
        }, 'json');

        return false;
    });
	
})


function addmyblog(result,num)
{
			
    var $self = $("#artlistMain"); 
    $($self).append(result);
			
    addneweastweibo(num);
}

function addneweastweibo(num)
{
    $(function(){
        var $this = $("#artlistMain");
        scrollNews_weibo($this,num);
    });
}
	
function scrollNews_weibo(obj,num){
    var $self = obj;
    var divNum=$self.find("div[class='ArticleBox']").size();
    var prependToNum='';
    var lineHeight='';
    var amountNum=0;
    for(i=0;i<num;i++)
    {
        prependToNum=divNum-1;
        lineHeight = $self.find("div[class='ArticleBox']").eq(prependToNum).height(); //获取行高
        amountNum+=lineHeight;
        $($self.css({
            marginTop:-amountNum
        }).find("div[class='ArticleBox']")).eq(prependToNum).css('display','').prependTo($self);
    }
		   
    //$($self.find("li:last")).prependTo();//prependTo能直接将元素移动走
		   
    $self.animate({
        "marginTop" : "-1px"
    }, 600 , function(){
        //alert($self.css({marginTop:0}).find("li:first").html());//输出当前显示第一条li内容
        //$self.css({marginTop:0}).find("li:first").appendTo($self); //appendTo能直接将元素移动走,详见如下：$self.css({marginTop:0}).find("li:first").appendTo()
        //alert($self.css({marginTop:0}).find("li:first").html());//输出当前显示第二条li内容
        })
		   
//$($self.find("div[class='ArticleBox']:last")).remove();
//$self.css({marginTop:0}).find("li:first").appendTo();//代替以上
}


/*
	 *  展开
	 */	
function spread(obj,aid) {
		
    var url;
    url = $(obj).prev().val();
    	
    $('#ajaxdiv').load(url+"?"+new Date().getTime());
    //$('#ajaxdiv2').load(baseurl+'/ajaxomcount/art/'+aid+"?"+new Date().getTime());
    var num=$('#atonclick_'+aid).html().match(/\d+/g);
    num=parseInt(num)+1;
    $('#atonclick_'+aid).html('<em>'+num+'</em>');
    $(obj).parent().parent().hide();
    $(obj).parent().parent().next().show();
}

/*
 *  展开2
 */	
function spread2(obj,memberid,aid,appeartime,click,id) {
	
    var url;
    var viewNumControl='viewNumControl_';
    url = $(obj).prev().val();
	
    if(click=='1')
    {
		
        if(getCookie(viewNumControl+aid)==''||getCookie(viewNumControl+aid)!='1')
        {
            $('#ajaxdiv').load(url+"?"+new Date().getTime());
            var num=$('#atonclick_'+aid).html().match(/\d+/g);
            $('#atonclick_'+aid).html(parseInt(num)+1);
			
            setCookie(viewNumControl+aid,'1','3600');
        }
		
    }
	
    $('#'+id+aid).html('内容加载中...');
	
    $.ajax({
        type: 'get',
        url: baseurl+'/index.php/article/getintactarticle?memberid='+memberid+'&aid='+aid+'&appeartime='+appeartime+'&r='+new Date().getTime(),
        dataType: 'json',
        success: function(data){
            //showalert(data.error);
            if(data.error == 0){
                if(data.erron['Content']!='')
                {
                    $('#'+id+aid).html(data.erron['Content']);
                }
                else
                {
                    $('#'+id+aid).html('暂无内容...');
                }
            }else{
                $('#'+id+aid).html(data.erron);
            }
        },
        error:function (XMLHttpRequest, textStatus, errorThrown) {
            //showalert('发生错误，请重新提交保存'+XMLHttpRequest.status+','+XMLHttpRequest.readyState+','+textStatus);
            $('#'+id+aid).html('系统繁忙，请稍后再试...');
        }
    });
	
	
    $(obj).parent().parent().hide();
    $(obj).parent().parent().next().show();
}

function setCookie(c_name,value,expiredays)
{
    var exdate=new Date();
    exdate.setDate(exdate.getDate()+expiredays);
    document.cookie=c_name+ "=" +escape(value)+
    ((expiredays==null) ? "" : ";expires="+exdate.toGMTString()+";path=/");
}

function getCookie(c_name)
{
    if (document.cookie.length>0)
    {
        c_start=document.cookie.indexOf(c_name + "=");
        if (c_start!=-1)
        { 
            c_start=c_start + c_name.length+1 ;
            c_end=document.cookie.indexOf(";",c_start);
            if (c_end==-1) c_end=document.cookie.length;
            return unescape(document.cookie.substring(c_start,c_end));
        } 
    }
    return "";
}


/*
 *  收起
 */
$(".packUp").live('click',function(){
    $(this).parent().hide();
    $(this).parent().prev().show();		
})
	
/*
*  分页跳转页面，首先现在文中放入$(".Inpjump").val()
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

$('#s_hottaglist').live('click',function(){
    if($(this).find('a:visible').size()==0)
    {
        $('#restoreHot').css('display','');
    }
})

function cancelHotTag()
{
    $('#s_hottaglist').load(baseurl+"/ajaxhottaglist/list");
}
