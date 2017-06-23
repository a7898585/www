var def_title = '这里填写标题';
var def_content = '这里输入文字内容';
var def_summary = '请输入摘要';
//var baseurl = "http://blog.cnfol.com";
var ibaseurl = "http://i.blog.cnfol.com";

$(document).ready(function(){
    $('input[name=title]').bind('blur focus',function(currentEvent){
        if(currentEvent.type == 'blur'){
        //if($(this).val() == ''){
        //	$(this).val(def_title).css({'color':'#999'});
        //}
        }else{
            //if($(this).val() == def_title){
            //	$(this).val('');
            //}
            $(this).css({
                'color':'#333'
            });
        }
    });//.val(def_title);
	
    $('input[name=title]').css({
        'color':'#333'
    });
	
    $('select[name=tagid]').bind('change',function(){
        $(this).css({
            'color':'#333'
        });
    });
	
    $('#simContent').bind('blur focus change',function(currentEvent){
        if(currentEvent.type == 'blur'){
        //if($(this).val() == ''){
        //$(this).val(def_content).css({'color':'#999'});
        //}
        }else if(currentEvent.type == 'change'){
			
        }else{
        //if($(this).val() == def_content){
        //$(this).val('').css({'color':'#333'});
        //}
        }
    });//.val(def_content);
	
    /* 鲜花功能初始化 */
    //    $('input[name=GiftPrice]').val('0');
    $('.cnfolck').bind('click',function(){
        if($(this).attr("checked") == "checked"){
            $('input[name=GiftPrice]').attr('disabled',false).removeClass('lock');
            $('.lockBtn').removeClass('lock');
            $('input[name=GiftPrice]').select();
        }else{
            $('input[name=GiftPrice]').attr('disabled',true).addClass('lock');
            $('.lockBtn').addClass('lock');
        }
    });
	
    /* 摘要框初始化 */
    $('textarea[name=summary]').css({
        'color':'#333'
    });
    /* $('textarea[name=summary]').bind('blur focus',function(currentEvent){
		if(currentEvent.type == 'blur'){
			if($(this).val() == ''){
				$(this).val(def_summary).css({'color':'#999'});
			}
		}else{
			if($(this).val() == def_summary){
				$(this).val('').css({'color':'#333'});
			}
		}
	}).val('请输入摘要'); */
	
    $('input[name=tag]').bind('click',function(){
        $(this).css({
            'color':'#333'
        });
    });
	
    /* 简易、高级编辑器验证码窗口隐藏 */
    //$('#simVarifySpan,#supVarifySpan').hide();
	
    /* 个人分类动态加载 */
    $('#supMemberid').bind('change',function(){
        var mid = $(this).val();
//        $('#sortid').load(baseurl+'?c=channel&m=selfsort&memberid='+mid+'&r='+new Date().getTime());
         $('#sortid').load(baseurl+"/ajaxsort/list/"+mid);
    });
	
    /* 系统文章分类初始化 */
    $('select[name=tagid]').each(function(){
        $(this).val('0');
    });
	
    //首页更换博客名发布博文时用
    $('select[name=memberid]').bind('change click',function(){
        var memberid=$(this).val();
        if($(this).attr('id')=='simMemberid')
        {
            $('#supMemberid').val($('#simMemberid').val());
        }
        else if($(this).attr('id')=='supMemberid')
        {
            $('#simMemberid').val($('#supMemberid').val());
        }
				
        $.getJSON(baseurl+'?c=channel&m=isvalidateajax&memberid='+memberid+'&r='+new Date().getTime(), function(json) {
            if(json.error==0)
            {
                $('#simLastIt').css('display','none');
					
                $('#supLastIt').css('display','none');
                //$('#supLastIt_2').css('display','');
					
                $('input[name=isvalidate]').val(0);
					
                $('.commentverifycode').css('display','none');
            }
            else if(json.error==1)
            {
                $('#simLastIt').css('display','');
					
                $('#supLastIt').css('display','');
                //$('#supLastIt_2').css('display','none');
					
                $('input[name=isvalidate]').val(1);
					
                $('.commentverifycode').css('display','');
            }
        });
											 
    });
	
	
    $('#simCnfolck,#supCnfolck').live('click',function(){
		
        if($(this).attr('checked'))
        {
            $('#simCnfolck').attr('checked',true);
            $('#simGiftPrice').attr('disabled','');
			
            $('#supCnfolck').attr('checked',true);
            $('#supGiftPrice').attr('disabled','');
        }
        else
        {
            $('#simCnfolck').attr('checked',false);
            $('#simGiftPrice').attr('disabled','disabled');
			
            $('#supCnfolck').attr('checked',false);
            $('#supGiftPrice').attr('disabled','disabled');
        }
		
    });
	
	
    $('#simGiftPrice,#supGiftPrice').live('blur',function(){
		
        $('#supGiftPrice').val($(this).val());
        $('#simGiftPrice').val($(this).val());
		
			
    });
	
	
});

/* 重置表单 */
function clearform(){
	
    $('textarea[name=summary]').css({
        'color':'black'
    });
	
    $('#varifycode').hide();
    $('.cnfolck').attr('checked',false);
    $('#simGiftPrice').val('0').attr('disabled',true);
    $('#supGiftPrice').val('0').attr('disabled',true);
	
    editorsimple.html('');
    editor.html('');
	
    $('#simTitle').val('');
    $('#supTitle').val('');
	
    $('#simTimingSave').html('').css('display','none');
    $('#supTimingSave').html('').css('display','none');
	
    $('select[name=tagid]').each(function(){
        $(this).val('0');
    }).css({
        'color':'black'
    });
	
    $('#simSaveEditControl').val('0');
    $('#simSaveEditTimeControl').val('0');
    $('#supSaveEditControl').val('0');
    $('#supSaveEditTimeControl').val('0');
}

function getTagShow(tagName){
    var ele = document.getElementsByTagName(tagName);
    if (ele != null) {
        for (var i = 0; i < ele.length; i++) {
            if (ele[i].style.visibility == "hidden") {
                ele[i].style.visibility = "visible";
            }
        }
    }
}



/* 发表文章 */
function submitfun(type,uid,alone){
	
    if(type == 'sup'){
        var tags =$('#tag').val().replace(/(^,)|(,$)/g,'');
        tags = tags.split(',');
        if(tags.length > 5){
           
            showalertminute('标签数不能超过5个',2000,'','popupTip','TMDeleteSuccess');
            return;
        }
		
        editor.sync();
    }
    if(type == 'sim')
    {
        editorsimple.sync();
    }
	
	
    if($('#'+type+'Title').val() == ''){// || $('#'+type+'Title').val() == def_title
        
        showalertminute('请认真填写博文的标题',2000,'','popupTip','TMDeleteSuccess');
        getTagShow('select');
        return;
    }
    if($('#'+type+'Content').val() == ''){//|| $('#'+type+'Content').val() == def_content
		
        showalertminute('请认真撰写博文',2000,'','popupTip','TMDeleteSuccess');
        getTagShow('select');
        return;
    }
    if($('#'+type+'Memberid').val() == ''){
        
        showalertminute('请选择要发表的博客',2000,'','popupTip','TMDeleteSuccess');
        getTagShow('select');
        return;
    }
    if($('#'+type+'Tagid').val() == '0'){
        
        showalertminute('请选择博文系统分类',2000,'','popupTip','TMDeleteSuccess');
        getTagShow('select');
        return;
    }
    if($('#'+type+'Cnfolck').attr('checked') == true){
        //$('#'+type+'GiftPrice')
        var reg = /^\d+$/;
        if (!reg.test($('#'+type+'GiftPrice').val())) {
            
            showalertminute('鲜花数只允许是整数',2000,'','popupTip','TMDeleteSuccess');
            getTagShow('select');
            return;
        }
    }
    if($('#'+type+'isvalidate').val()=='0')
    {
        if($('#'+type+'Validate').val() == ''){
           
            showalertminute('请输入验证码',2000,'','popupTip','TMDeleteSuccess');
            getTagShow('select');
            return;
        }
    }
    
	
    //showloading();
    showalert('提交中...','popupTip','TMDeleteSuccess');
    
   
    $.ajax({
        type: 'post',
        contentType:'application/x-www-form-urlencoded',
        url:action,
        data:$('#'+type+'Form').serialize(), 
        dataType:'json',
        //async: false,//影响  showalert函数调用
        success: function(data){
        	
            showalertminute(data.error,2000,'','popupTip','TMDeleteSuccess');
        	
            if(alone!='1'||data.errno!='success')
            {
                showalertminute(data.error,2000,'','popupTip','TMDeleteSuccess');
            }
			
            if(data.errno == 'success'){
                if(alone!='1')
                {
                    afterSubmit();
                    clearform();
                }
                
                if(type!='sup')
                {
                    seoLast(data.memberid,data.articleid,data.appeartime);//提取并保存关键字
                    var arturl  = baseuri + "/article/" +data.appeartime+"-"+data.articleid+".html";
                    addArticleApi(arturl,data.appeartime,data.userid,data.title,data.content,data.articleid);
                }
                	
            }
			
            if($('#'+type+'isvalidate').val()=='0')
            {
                vifdata(type);
            }
			
            if(type=='sup')
            {
				
                if(data.errno=='success')
                {
                    toggleEditor('editBox','super','');
                    $('#supTag').val('');
                    $('.brief').val('');
                    $('#sortid').val('18295');
                    $('#supGiftPrice').val('0');
                    $('input[name=trackback]').val('');
                    $('#tag').val('');
					
                    if(alone=='1')
                    {
                
                        Dialog.Close();
                        var arturl  = baseuri + "/article/" +data.appeartime+"-"+data.articleid+".html";
						 
						
                        var strHtml = '\<div style="padding:10px;">\<div style="background: none repeat scroll 0 0 rgb(239,243,195);text-align:left;height:45px;padding:5px 0px 5px 10px;">\<b style="font-size:16px">'+data.error+'</b><br />系统将在 <span id="secs">3</span> 秒钟后自动跳转\
	                                                            </div>\
	                                                            <div style="padding-left:10px;padding-top:5px">\
	                                                                    <div style="float:left;">我们推荐您：<br />或者您可以：</div>\
	                                                                    <div style="float:left;padding:0px;margin:0px;">\
	                                                                            <ul>\
	                                                                                    <li><a target="_blank" href="http://bbs.cnfol.com"><b>逛逛论坛</b></a>、<a target="_blank" href="http://g.cnfol.com"><b>逛逛圈子</b></a></li>\
	                                                                                    <li><a href="'+arturl+'" target="_blank">查看刚才发表的文章</a></li>\
	                                                                                    <li><a href="'+baseuri+'/article/Add" target="_blank">撰写一篇新博文</a></li>\
	                                                                                    <li><a href="'+baseuri+'/manage/article/List" target="_blank">查看文章列表</a></li>\
	                                                                                    <li><a href="'+baseuri+'" target="_blank">查看我的博客首页</a></li>\
	                                                                                    <li><a href="'+baseuri+'" target="_blank">查看中金博客首页</a></li>\
	                                                                            </ul>\
	                                                                    </div>\
	                                                            </div>\
	                                                    </div>';
	                              
	                                                    
                        showalertminute(strHtml,4000,'','publishTip','articlePublishSuccess');
                        countDown(arturl, 3);
                        addArticleApi(arturl,data.appeartime,data.userid,data.title,data.content,data.articleid);
                        seoLast(data.memberid,data.articleid,data.appeartime);//提取并保存关键字
                    }
                   
                }
                else
                {
                //toggleEditor('editBox','super','submit');
                }
            }
			
			
        },
        error:function (XMLHttpRequest, textStatus, errorThrown) {
            //showalert('系统繁忙，请稍后...'+XMLHttpRequest.status+','+XMLHttpRequest.readyState+','+textStatus);
            //showalert('系统繁忙，请稍后...');
			
			
			
            //showalert('文章已成功发表', 1000,function(){
            //    clearform();
            //});
            
            showalertminute('文章已成功发表',2000,'','popupTip','TMDeleteSuccess');
            clearform();
			
            if($('#'+type+'isvalidate').val()=='0')
            {
                vifdata(type);
            }
			
            if(type=='sup')
            {
                toggleEditor('editBox','super','');
                $('#supTag').val('');
                $('.brief').val('');
                $('#sortid').val('18295');
                $('#supGiftPrice').val('0');
                $('input[name=trackback]').val('');
            }
			
			
			
        }
    });
	 
}
function addArticleApi(arturl,time,userid,title,content,aid)
{
    var action=baseurl+'/index.php/userblogapi/addArticleApi';

    $.ajax({
        type: 'post',
        contentType:'application/x-www-form-urlencoded',
        url:action,
        data:{
            'arturl':arturl,
            'time':time,
            'userid':userid,
            'title':title,
            'content':content,
            'articleid':aid
        }, 
        dataType:'json',
        async: false,
        success: function(data){
            if(data.error=='success'){
            }	
        },
        error:function (XMLHttpRequest, textStatus, errorThrown) {
            showalert('系统繁忙，请稍后...','popupTip','TMDeleteSuccess');	
        }
    });
}
function seoLast(memberid,articleid,appeartime)
{
    var url=baseurl+'/index.php/widget/saveseowords?memberid='+memberid+'&articleid='+articleid+'&appeartime='+appeartime;
    $.get(url,function(result){
        //alert(result);
        });
}


function vifdataInput(obj,type,id){
    if($(obj).val() == ''){
        $('#'+type+'VifdataImg'+id).show();
        vifdata(type,id);
    }
}

function vifdata(type,id){
	
    if(type == undefined) {
        alert('error vifdata function!');
        return false;
    }
	
    if(id == undefined) {
        id='';
    }
	
    if($('#'+type+'VarifySpan'+id).css('display') == 'none'){
        $('#'+type+'VarifySpan'+id).show();
    }
	
    var nowtime = new Date().getTime();
    $('#'+type+'Timestamp'+id).val(nowtime);
    setTimeout(function(){
        $('#'+type+'VifdataImg'+id).attr('src',baseurl+'/register/varify?r='+nowtime);
    },100);
	
    $('input[name=validate]').val('');
	
}

function vifdata_class(type,id){
    var vifdata=$('#repEdit'+id).attr('class');
    if(vifdata!='vifdata_class')
    {
        return false;
    }
    if(type == undefined) {
        alert('error vifdata function!');
        return false;
    }
	
    if(id == undefined) {
        id='';
    }
	
    if($('#'+type+'VarifySpan'+id).css('display') == 'none'){
        $('#'+type+'VarifySpan'+id).show();
    }
	
    var nowtime = new Date().getTime();
    $('#'+type+'Timestamp'+id).val(nowtime);
	
    setTimeout(function(){
        $('#'+type+'VifdataImg'+id).attr('src',ibaseurl+'?c=article&m=verifycode&r='+nowtime);
    },100);
	
    $('input[name=validate]').val('');
    $('#repEdit'+id).attr('class','');
	
}


/* 同步编辑器已赋值元素 */
function syncEdit(type,editor){
    if(type == 'sup2sim'){
        /* var text = editor.text().replace(/<(?:img|embed).*?>/ig, '')
		if(text != ''){
			$('#simContent').val(text);
		} */
        if($('#supTitle').val() != ''&&$('#supTitle').val()!=undefined){//&& $('#supTitle').val()!=def_title
            $('#simTitle').val($('#supTitle').val());
            $('#simTagid').val($('#supTagid').val());
			
        } 
    }else{
        /* if($('#simContent').val() != '' && $('#simContent').val()!=def_content){
			editor.html($('#simContent').val());
		} */
        if($('#simTitle').val() != ''&&$('#simTitle').val()!=undefined){//&& $('#simTitle').val()!=def_title
        	
            $('#supTitle').val($('#simTitle').val());
            $('#supTagid').val($('#simTagid').val());
        }
    }
}


/* 编辑器模式切换 */
function toggleEditor(id,cName,isSubmit){
    var obj = C.G(id);
    var sAddC='';
    if(cName == null){
        sAddC = 'show';
    }else{
        sAddC = cName;
    }
	
    if(isSubmit=='simTagid')
    {
        $('#supTagid').val($('#simTagid').val());
        $('#simsupCut').val('sup');
        editcontentcopy('simple','');
    }
    else if(isSubmit=='supTagid')
    {
        $('#simTagid').val($('#supTagid').val());
        $('#simsupCut').val('sim');
        editcontentcopy('','');
    }
	
    if (obj.className.indexOf(sAddC)!=-1){
        if(isSubmit!='submit')
        {
            C.DelClass(obj, sAddC);
        }
        syncEdit('sup2sim',editor);
    //$('textarea[name=summary]').css({'color':'#999'}).val('');
    }else{
        //alert($('#simTagid').val());
        syncEdit('sup1sim',editor);
        C.AddClass(obj, sAddC);
    }
}


function ShowHtmlStringAlone(strHtml, nwidth, nheight)
{
    if(g_pop){
        g_pop.close();
    }
    g_pop=new Popup({
        contentType:2,
        isReloadOnClose:false,
        width:nwidth,
        height:nheight
    });
    g_pop.setContent("contentHtml",strHtml);
    g_pop.setContent("title","消息提示");
    g_pop.build();
    g_pop.show();
}



//弹出表情层
var faceArr = new Array();
faceArr[0] = '微笑';
faceArr[1] = '撇嘴';
faceArr[2] = '色';
faceArr[3] = '发呆';
faceArr[4] = '得意';
faceArr[5] = '流泪';
faceArr[6] = '害羞';
faceArr[7] = '闭嘴';
faceArr[8] = '睡';
faceArr[9] = '大哭';
faceArr[10] = '尴尬';
faceArr[11] = '发怒';
faceArr[12] = '调皮';
faceArr[13] = '呲牙';
faceArr[14] = '惊讶';
faceArr[15] = '难过';
faceArr[16] = '酷';
faceArr[17] = '冷汗';
faceArr[18] = '抓狂';
faceArr[19] = '吐';
faceArr[20] = '偷笑';
faceArr[21] = '可爱';
faceArr[22] = '白眼';
faceArr[23] = '傲慢';
faceArr[24] = '饥饿';
faceArr[25] = '困';
faceArr[26] = '惊恐';
faceArr[27] = '流汗';
faceArr[28] = '憨笑';
faceArr[29] = '大兵';
faceArr[30] = '奋斗';
faceArr[31] = '咒骂';
faceArr[32] = '疑问';
faceArr[33] = '嘘';
faceArr[34] = '晕';
faceArr[35] = '折磨';
faceArr[36] = '衰';
faceArr[37] = '骷髅';
faceArr[38] = '敲打';
faceArr[39] = '再见';
faceArr[40] = '擦汗';
faceArr[41] = '抠鼻';
faceArr[42] = '鼓掌';
faceArr[43] = '糗大了';
faceArr[44] = '坏笑';
faceArr[45] = '左哼哼';
faceArr[46] = '右哼哼';
faceArr[47] = '哈欠';
faceArr[48] = '鄙视';
faceArr[49] = '委屈';
faceArr[50] = '快哭了';
faceArr[51] = '阴险';
faceArr[52] = '亲亲';
faceArr[53] = '吓';
faceArr[54] = '可怜';

var imgPath ='http://img.cnfol.com/newblog/emoticons/';


/* 提交评论 */
function submitComment(type,aid,cid,index,uid){
	
	
    if($('#simMemberid').html()==''&&uid!='')
    {
        $.getJSON(baseurl+'?c=channel&m=getbloglistbyuid&uid='+uid+'&r='+new Date().getTime(), function(json){
															  
            if(json.erron=='01')
            {
                //here
                showalertblog('您未开通博客或已被关闭，点击<a href="'+baseurl+'/register/Register" target="_blank" >"创建博客"</a><br />然后返回个人中心提交评论吧！<br /><a href="'+baseurl+'/register/Register" onclick=javascript:Dialog.Close("blogMessagePop"); class="btnDiaY btn" target="_blank" >确 定</a>', 7000,function(){
                    //here
                    });
																   
                return;
            }
            else
            {
																   
                showloading();
                if(type == 'rep'){
                    //var smile='<img alt="" src="'+imgPath+$(this).attr('num')+'.gif" border="0" />';
                    //var replaceValue='[~'+faceArr[$(this).attr('num')]+'~]';
		
                    if(index=='articlelist')
                    {
                        if($('#repEdit'+aid).val()=='')
                        {
                            showalert("评论不能为空");
                            return;
                        }
                        if($('#commentisvalidate').val()=='1')
                        {
                            if($('#validatevalue_'+aid).val()=='')
                            {
                                showalert("请输入验证码");
                                return;
                            }
                        }
                    }
		
                    var str=$('#repEdit'+aid).val();
                    for (var i in faceArr)
                    {
                        var re=new RegExp("\\[~"+faceArr[i]+"~\\]","g"); 
                        str=str.replace(re,'<img alt="" src="'+imgPath+i+'.gif" border="0" />');
                    }
                    $('#copyrepEdit'+aid).val(str);
		
                    $.post(baseurl+'?c=comment&m=action&act=addcomment',$('#repForm'+aid).serialize(),function(data){
                        showalert(data.error);
                        vifdata('rep',aid);
                        if(data.errno == 'succ'){
                            $('#repEdit'+aid).val('');
                            updateComment(aid);
                            $('.comCount'+aid).html(parseInt($('.comCount'+aid).html())+1);
                            $('#showComlis_'+aid).css('display','none');
                        }
                    },'json');
                }else if(type == 'adm'){
                    $.post(baseurl+'?c=comment&m=action&act=ownercomment',$('#admForm'+cid).serialize(),function(data){
                        showalert(data.error);
                        if(data.errno == 'succ'){
                            $('#admEdit'+cid).val('');
                            updateComment(aid);
				
                            if(index=='index')
                            {
                                showComlis(aid);
                                $('.comCount'+aid).html(parseInt($('.comCount'+aid).html())+1);
                            }
                            else
                            {
                                location.reload();
                            }
                        }
                    },'json');
                }else{
                    alert('error submitcomment!!');
                }
																   
																   
            }
        });
        return;
    }
    else
    {
        showloading();
        if(type == 'rep'){
            //var smile='<img alt="" src="'+imgPath+$(this).attr('num')+'.gif" border="0" />';
            //var replaceValue='[~'+faceArr[$(this).attr('num')]+'~]';
		
            if(index=='articlelist')
            {
                if($('#repEdit'+aid).val()=='')
                {
                    showalert("评论不能为空");
                    return;
                }
                if($('#commentisvalidate').val()=='1')
                {
                    if($('#validatevalue_'+aid).val()=='')
                    {
                        showalert("请输入验证码");
                        return;
                    }
                }
            }
		
            var str=$('#repEdit'+aid).val();
            for (var i in faceArr)
            {
                var re=new RegExp("\\[~"+faceArr[i]+"~\\]","g"); 
                str=str.replace(re,'<img alt="" src="'+imgPath+i+'.gif" border="0" />');
            }
            $('#copyrepEdit'+aid).val(str);
		
            $.post(baseurl+'?c=comment&m=action&act=addcomment',$('#repForm'+aid).serialize(),function(data){
                showalert(data.error);
                vifdata('rep',aid);
                if(data.errno == 'succ'){
                    $('#repEdit'+aid).val('');
                    updateComment(aid);
                    $('.comCount'+aid).html(parseInt($('.comCount'+aid).html())+1);
                    $('#showComlis_'+aid).css('display','none');
                }
            },'json');
        }else if(type == 'adm'){
            $.post(baseurl+'?c=comment&m=action&act=ownercomment',$('#admForm'+cid).serialize(),function(data){
                showalert(data.error);
                if(data.errno == 'succ'){
                    $('#admEdit'+cid).val('');
                    updateComment(aid);
				
                    if(index=='index')
                    {
                        showComlis(aid);
                        $('.comCount'+aid).html(parseInt($('.comCount'+aid).html())+1);
                    }
                    else
                    {
                        location.reload();
                    }
                }
            },'json');
        }else{
            alert('error submitcomment!!');
        }
    }
}

/* 更新评论列表 */
function updateComment(articleid){
    $('#comlis'+articleid).html('数据更新中...');
    $.get(baseurl+'?c=comment&m=ajaxlist&artid='+articleid,$('repForm'+articleid).serialize(),function(data){
        $('#comlis'+articleid).html(data);
    });
    $('#comlis'+articleid).show();
//location.reload();
}

/* 加载评论列表 */
function showComlis(articleid){
    toggle('comlis'+articleid);
    //http://i.blog.cnfol.com?c=comment&commentlist&artid=79985529
    if($('#comlis'+articleid).css('display') != 'block') {
        $('#comlis'+articleid).empty();
    }else{
        $('#comlis'+articleid).html('正在为您加载数据...');
        $.get(baseurl+'?c=comment&m=ajaxlist&artid='+articleid,$('repForm'+articleid).serialize(),function(data){
            $('#comlis'+articleid).html(data);
            $('#comlis'+articleid).css('display','block');
            $('#showComlis_'+articleid).css('display','none');
        });
    }
}

function toggleReplay(articleid){
    toggle('reply'+articleid);
    if($('#reply'+articleid).css('display') != 'block') {
        $('#comlis'+articleid).empty();
    }
    $('#showComlis_'+articleid).css('display','');
}
/* 删除评论 */
function delComment(cid,flag){
    showloading();
    if(flag != 'action'){
        Dialog('messagePop');
        var html = '确定要删除此评论么？<br /><a href="javascript:delComment(\''+cid+'\',\'action\')" class="btnDiaY btn">确 定</a><a href="javascript:Dialog.Close(\'messagePop\');" class="btnDiaN btn">取 消</a>';
        $('#msgContent').html(html);
    }else{
        $.post(baseurl+'?c=comment&m=action&act=delcomment',$('#admForm'+cid).serialize(),function(data){
            showalert(data.error,2000,function(){
                location.reload();
            });
        },'json');
    }
}

/* 删除评论下的评论 */
function delComments(cid,flag){
    showloading();
    if(flag != 'action'){
        Dialog('messagePop');
        var html = '确定要删除此评论么？<br /><a href="javascript:delComments(\''+cid+'\',\'action\')" class="btnDiaY btn">确 定</a><a href="javascript:Dialog.Close(\'messagePop\');" class="btnDiaN btn">取 消</a>';
        $('#msgContent').html(html);
    }else{
        $.post(baseurl+'?c=comment&m=action&act=delcomment',$('#comForm'+cid).serialize(),function(data){
            showalert(data.error,2000,function(){
                location.reload();
            });
        },'json');
    }
}
