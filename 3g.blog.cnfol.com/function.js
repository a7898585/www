var Bk={};

Bk.tools={};
    
Bk.tools.getByClass=function(oParent,sClass)

{
        var aEle=oParent.getElementsByTagName('*');
        var reg=new RegExp("\\b"+sClass+"\\b","gi");
        var aResult=[];
        for(var i=0;i<aEle.length;i++){
            if(reg.test(aEle[i].className)){
                aResult.push(aEle[i]);
            };
    
        }
        return aResult;
    };

Bk.tools.getPos=function getPos(ele){
    var p=ele.offsetParent;
    var left=ele.offsetLeft;
    var top=ele.offsetTop;
    while(p){
        if(window.navigator.userAgent.indexOf('MSIE 8')>-1){
            left+=p.offsetLeft;
            top+=p.offsetTop;
        }else{
            left+=p.offsetLeft+p.clientLeft;
            top+=p.offsetTop+p.clientTop;
        }
        p=p.offsetParent;
    }
    var obj={};
    
    obj.l=left;
    obj.t=top;
    return obj;
};

Bk.tools.addClass=function(ele,className){
    if(ele&&ele.nodeType&&ele.nodeType===1&&className&&typeof className=='string'){
        var reg=new RegExp('\\b'+className+'\\b');
        if(!reg.test(ele.className)){
            ele.className+=" "+className;
        }
    }
}
Bk.tools.removeClass=function(ele,className){
    if(ele&&ele.nodeType&&ele.nodeType===1&&className&&typeof className=='string'){
        var reg=new RegExp('\\b'+className+'\\b','g');
        ele.className=ele.className.replace(reg,'');
    }
}
Bk.tools.getStyle=function(obj,attr){
    if(obj.currentStyle){
        return obj.currentStyle[attr];
    }
    else{
        return getComputedStyle(obj,false)[attr];
    }
}
Bk.ui={};
    
Bk.ui.toggle=function(obj1,obj2){
    obj1.onclick=function(){
        if(Bk.tools.getStyle(obj2,"display")=="none"){
            Bk.tools.removeClass(obj2,"Hide");
            Bk.tools.addClass(obj2,"Show");
        }else{
            Bk.tools.removeClass(obj2,"Show");
            Bk.tools.addClass(obj2,"Hide");
        };
    
    }
};

Bk.ui.toggleGZ=function(){
    document.onclick=function(e){
        var e=e||window.event;
        var target=e.target||e.srcElement;
        if(target.tagName.toUpperCase()=='A'&&target.className=='Fr GuanZhuBtn2 Tc'){
            Bk.app.toDialog();
            if(flag===1){
                target.className='Fr GuanZhuBtn3 Tc';
                target.innerHTML='加关注';
            }
            return false;
        }else if(target.tagName.toUpperCase()=='A'&&target.className=='Fr GuanZhuBtn3 Tc'){
            var flag=parseInt(Bk.app.toDialog());
            alert(flag);
            if(flag===1){
                target.className='Fr GuanZhuBtn2 Tc';
                target.innerHTML='取消关注';
            }
            return false;
        }
    };

}
Bk.ui.tsina_a=function(){
    var s=screen;
    var d=document;
    var e=encodeURIComponent;
    var f='http://v.t.sina.com.cn/share/share.php?',u=d.location.href,p=['url=',e(u),'&title=',e(d.title)].join('');
    if(!window.open([f,p].join(''),'mb',['toolbar=0,status=0,resizable=1,width=620,height=450,left=',(s.width-620)/2,',top=',(s.height-450)/2].join('')))u.href=[f,p].join('');
}
Bk.ui.tTengXun_a=function(){
    var s=screen;
    var d=document;
    var e=encodeURIComponent;
    var f='http://share.v.t.qq.com/index.php?c=share&a=index&',u=d.location.href,p=['url=',e(u),'&title=',e(d.title)].join('');
    if(!window.open([f,p].join(''),'mb',['toolbar=0,status=0,resizable=1,width=620,height=450,left=',(s.width-620)/2,',top=',(s.height-450)/2].join('')))u.href=[f,p].join('');
}
Bk.ui.tqq_a=function(){
    var s=screen;
    var d=document;
    var e=encodeURIComponent;
    var f='http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=',u=d.location.href,p=['url=',e(u),'&title=',e(d.title)].join('');
    if(!window.open([f,p].join(''),'mb',['toolbar=0,status=0,resizable=1,width=620,height=450,left=',(s.width-620)/2,',top=',(s.height-450)/2].join('')))u.href=[f,p].join('');
}
Bk.ui.tWX_a=function(){
    var s=screen;
    var d=document;
    var e=encodeURIComponent;
    var f='https://open.weixin.qq.com/oauth?response_type=token&appid=<APPID>',u=d.location.href,p=['url=',e(u),'&title=',e(d.title)].join('');
    if(!window.open([f,p].join(''),'mb',['toolbar=0,status=0,resizable=1,width=620,height=450,left=',(s.width-620)/2,',top=',(s.height-450)/2].join('')))u.href=[f,p].join('');
}
Bk.ui.ShareToTsina=function(){
    if(/Firefox/.test(navigator.userAgent)){
        setTimeout(Bk.ui.tsina_a,0)
    }else{
        Bk.ui.tsina_a()
    };
    
}
Bk.ui.ShareToqq=function(){
    if(/Firefox/.test(navigator.userAgent)){
        setTimeout(Bk.ui.tqq_a,0)
    }else{
        Bk.ui.tqq_a()
    };
    
}
Bk.ui.ShareToTengXun=function(){
    if(/Firefox/.test(navigator.userAgent)){
        setTimeout(Bk.ui.tTengXun_a,0)
    }else{
        Bk.ui.tTengXun_a()
    };
    
}
Bk.ui.ShareToWX=function(){
    if(/Firefox/.test(navigator.userAgent)){
        setTimeout(Bk.ui.tWX_a,0)
    }else{
        Bk.ui.tWX_a()
    };
    
}
Bk.app={};
    
Bk.app.toLoad=function(){
    var data=[{
        "title":"我是新来的数据",
        "content":"周一暴跌缺口压力较大，成...",
        "time":"10:00:00",
        "isContainImg":"1",
        "isZhuan":"1",
        "isJian":"1",
        "isDing":"1"
    },{
        "title":"我是新来的数据2",
        "content":"22222222222大，成...",
        "time":"10:00:00",
        "isContainImg":"0",
        "isZhuan":"0",
        "isJian":"1",
        "isDing":"1"
    },{
        "title":"我是新来的数据2",
        "content":"22222222222大，成...",
        "time":"10:00:00",
        "isContainImg":"0",
        "isZhuan":"0",
        "isJian":"1",
        "isDing":"1"
    },{
        "title":"我是新来的数据",
        "content":"周一暴跌缺口压力较大，成...",
        "time":"10:00:00",
        "isContainImg":"1",
        "isZhuan":"1",
        "isJian":"1",
        "isDing":"1"
    },{
        "title":"我是新来的数据2",
        "content":"22222222222大，成...",
        "time":"10:00:00",
        "isContainImg":"0",
        "isZhuan":"0",
        "isJian":"1",
        "isDing":"1"
    },{
        "title":"我是新来的数据2",
        "content":"22222222222大，成...",
        "time":"10:00:00",
        "isContainImg":"0",
        "isZhuan":"0",
        "isJian":"1",
        "isDing":"1"
    },{
        "title":"我是新来的数据",
        "content":"周一暴跌缺口压力较大，成...",
        "time":"10:00:00",
        "isContainImg":"1",
        "isZhuan":"1",
        "isJian":"1",
        "isDing":"1"
    },{
        "title":"我是新来的数据2",
        "content":"22222222222大，成...",
        "time":"10:00:00",
        "isContainImg":"0",
        "isZhuan":"0",
        "isJian":"1",
        "isDing":"1"
    },];
    var DataFields={
        title:"title",
        content:"content",
        time:"time",
        isContainImg:"isContainImg",
        isZhuan:"isZhuan",
        isJian:"isJian",
        isDing:"isDing"
    };
    
    var url="";
    var oGetMore=C.G('GetMore');
    var oLst_news=C.G('Lst_news')
    var oNews=document.querySelectorAll('#Lst_news .Lst-o-new');
    var oLastNew=oNews[oNews.length-1];
    var Num=0;
    var Btn=true;
    var Limit=10;
    window.onscroll=function(){
        var top=Bk.tools.getPos(oGetMore).t+oGetMore.offsetHeight;
        var Viewh=document.documentElement.clientHeight||document.body.clientHeight;
        var ScrollTop=document.documentElement.scrollTop||document.body.scrollTop;
        if(top<(Viewh+ScrollTop)){
            if(Btn&&oGetMore.style.display!=='none'){
                toLoadDate(data);
            }
        }
    };

    oGetMore.onclick=toLoadDate;
    function toLoadDate(data){
        Btn=false;
        if(data.length<Limit){
            oGetMore.style.display='none';
        }
        for(var i=0;i<data.length;i++){
            var str='<div class="HY_o_new_tit F16 Red_color">'+'{%isDing%}{%isJian%}{%isZhuan%}<a href="#" target="_blank" class="Fl">{%title%}</a>'+'{%isContainImg%}'+'</div>'+'<time class="HY_o_new_time Hui_color">{%time%}</time>'+'<p class="Lst-new-desc F16 Pr10">{%content%}</p>';
            for(var attr in data[i]){
                var row=data[i];
                if(attr==DataFields.title){
                    str=str.replace("{%title%}",row[attr]);
                };
            
                if(attr==DataFields.content){
                    str=str.replace("{%content%}",row[attr]);
                };
            
                if(attr==DataFields.time){
                    str=str.replace("{%time%}",row[attr]);
                };
            
                if(attr==DataFields.isContainImg){
                    if(parseInt(row[attr])){
                        str=str.replace("{%isContainImg%}",'<span class="Bh_img Fl"></span>');
                    }else{
                        str=str.replace("{%isContainImg%}",'');
                    }
                };
            
                if(attr==DataFields.isZhuan){
                    if(parseInt(row[attr])){
                        str=str.replace("{%isType%}",'转载');
                        str=str.replace("{%isZhuan%}",'<font class="Fl">[转]</font>');
                    }else{
                        str=str.replace("{%isZhuan%}","");
                    }
                };
        
                if(attr==DataFields.isJian){
                    if(parseInt(row[attr])){
                        str=str.replace("{%isType%}",'推荐');
                        str=str.replace("{%isJian%}",'<font class="Fl">[荐]</font>');
                    }else{
                        str=str.replace("{%isJian%}",'');
                    }
                };
        
                if(attr==DataFields.isDing){
                    if(parseInt(row[attr])){
                        str=str.replace("{%isType%}",'顶');
                        str=str.replace("{%isDing%}",'<font class="Fl">[顶]</font>');
                    }else{
                        str=str.replace("{%isDing%}",'');
                    }
                };
    
            }
            var oDiv=document.createElement('div');
            oDiv.className="HY_o_new Mt10";
            oDiv.innerHTML=str;
            oLst_news.insertBefore(oDiv,oGetMore);
        };

        Btn=true;
    }
};

Bk.app.toggleBkName=function(){
    var oGZ_top=C.G('GZ_top');
    var oH4=oGZ_top.getElementsByTagName('h4')[0];
    var oSpan=oH4.getElementsByTagName('span')[0];
    var oShow=oH4.getElementsByTagName('font')[0];
    var text=oSpan.innerHTML;
    var text2=text.substr(0,10);
    if(text.length>10){
        oSpan.innerHTML=text2;
    }
    oShow.onclick=function(){
        var reg=/^\s+|\s+$/g;
        if(this.innerHTML.replace(reg,"")=='[展开]'){
            oSpan.innerHTML=text;
            this.innerHTML='[收起]';
        }else{
            oSpan.innerHTML=text2;
            this.innerHTML='[展开]';
        }
    };

};

Bk.app.addGuanZhu=function(){
    Bk.ui.toggleGZ();
}
Bk.app.toLoadPL=function(){
    var data=[{
        "name":"小李",
        "content":"周一暴跌缺口压力较大，成...",
        "time":"10:00:00"
    },{
        "name":"我是新来的数据2",
        "content":"22222222222大，成...",
        "time":"10:00:00"
    },{
        "name":"小王2",
        "content":"22222222222大，成...",
        "time":"10:00:00"
    },{
        "name":"我是的数据",
        "content":"周一暴跌缺口压力较大，成...",
        "time":"10:00:00"
    },{
        "name":"小黑2",
        "content":"22222222222大，成...",
        "time":"10:00:00"
    },{
        "name":"我是新数据2",
        "content":"22222222222大，成...",
        "time":"10:00:00"
    },{
        "name":"小白",
        "content":"周一暴跌缺口压力较大，成...",
        "time":"10:00:00"
    },{
        "name":"的数据2",
        "content":"22222222222大，成...",
        "time":"10:00:00"
    },];
    var DataFields={
        name:"name",
        content:"content",
        time:"time"
    };
    
    var url="";
    var oGetMore=C.G('GetMore');
    var oPL_Box=C.G('PL_Box')
    var oNews=document.querySelectorAll('#PL_Box .List_o_pl');
    var oLastNew=oNews[oNews.length-1];
    var Num=0;
    var Btn=true;
    var Limit=10;
    window.onscroll=function(){
        var top=Bk.tools.getPos(oGetMore).t+oGetMore.offsetHeight;
        var Viewh=document.documentElement.clientHeight||document.body.clientHeight;
        var ScrollTop=document.documentElement.scrollTop||document.body.scrollTop;
        if(top<(Viewh+ScrollTop)){
            if(Btn&&oGetMore.style.display!=='none'){
                toLoadDate(data);
            }
        }
    };

    oGetMore.onclick=toLoadDate;
    function toLoadDate(data){
        Btn=false;
        if(data.length<Limit){
            oGetMore.style.display='none';
        }
        for(var i=0;i<data.length;i++){
            var str='<div class="Pl_o_title">'+'<span class="Pl_o_name F14">{%name%}</span>'+'<time class="Pl_o_time Tr Hui_color">{%time%}</time>'+'</div>'+'<p class="Pl_o_content Pl10 Pr10 F14" >{%content%}</p>';
            for(var attr in data[i]){
                var row=data[i];
                if(attr==DataFields.name){
                    str=str.replace("{%name%}",row[attr]);
                };
            
                if(attr==DataFields.content){
                    str=str.replace("{%content%}",row[attr]);
                };
            
                if(attr==DataFields.time){
                    str=str.replace("{%time%}",row[attr]);
                };
        
            }
            var oDiv=document.createElement('div');
            oDiv.className="List_o_pl";
            oDiv.innerHTML=str;
            oPL_Box.appendChild(oDiv);
        };
    
        Btn=true;
    }
}
Bk.app.addPL=function(){
    var oMoreModules=C.G('MoreModules');
    var aA=oMoreModules.getElementsByTagName('a');
    var oPL_form=C.G('PL_form');
    aA[1].onclick=function(){
        if(oPL_form.className=='PL_form Wrp Hide Feedback'){
            oPL_form.className='PL_form Wrp Show Feedback';
        }else{
            oPL_form.className='PL_form Wrp Hide Feedback';
        };
    
    }
    aA[0].onclick=function(){
        this.className="ZanImg2 ToBlock Wrp";
        this.innerHTML='222';
    }
    aA[2].onclick=function(){
        alert(this.innerHTML);
    }
    aA[3].onclick=function(){
        alert(this.innerHTML);
    }
}
Bk.app.toShare=function(){
    var oFenXiang=C.G('FenXiang');
    var aSpan=oFenXiang.getElementsByTagName('span');
    aSpan[1].onclick=Bk.ui.ShareToTsina;
    aSpan[3].onclick=Bk.ui.ShareToTengXun;
    aSpan[2].onclick=Bk.ui.ShareToqq;
    aSpan[4].onclick=Bk.ui.ShareToWX
}
Bk.app.toLoadFenSi=function(){
    var data=[{
        "name":"小李",
        "bkName":"周一暴跌缺口压力较大，成...",
        "topSrc":"Images/BK03.jpg",
        "isGuanZhu":"1"
    },{
        "name":"我是新来的数据2",
        "bkName":"22222222222大，成...",
        "topSrc":"Images/BK03.jpg",
        "isGuanZhu":"1"
    },{
        "name":"小王2",
        "bkName":"22222222222大，成...",
        "topSrc":"Images/BK03.jpg",
        "isGuanZhu":"0"
    },{
        "name":"我是的数据",
        "bkName":"周一暴跌缺口压力较大，成...",
        "topSrc":"Images/BK03.jpg",
        "isGuanZhu":"1"
    },{
        "name":"小黑2",
        "bkName":"22222222222大，成...",
        "topSrc":"Images/BK03.jpg",
        "isGuanZhu":"0"
    },{
        "name":"我是新数据2",
        "bkName":"22222222222大，成...",
        "topSrc":"Images/BK03.jpg",
        "isGuanZhu":"1"
    },{
        "name":"小白",
        "bkName":"周一暴跌缺口压力较大，成...",
        "topSrc":"Images/BK03.jpg",
        "isGuanZhu":"1"
    },{
        "name":"的数据2",
        "bkName":"22222222222大，成...",
        "topSrc":"Images/BK03.jpg",
        "isGuanZhu":"1"
    },];
    var DataFields={
        name:"name",
        bkName:"bkName",
        topSrc:"topSrc",
        isGuanZhu:"isGuanZhu"
    };
    
    var url="";
    var oGetMore=C.G('GetMore');
    var oFenSiList=C.G('FenSiList')
    var oNews=document.querySelectorAll('#FenSiList .GZ_top');
    var oLastNew=oNews[oNews.length-1];
    var Num=0;
    var Btn=true;
    var Limit=10;
    window.onscroll=function(){
        var top=Bk.tools.getPos(oGetMore).t+oGetMore.offsetHeight;
        var Viewh=document.documentElement.clientHeight||document.body.clientHeight;
        var ScrollTop=document.documentElement.scrollTop||document.body.scrollTop;
        if(top<(Viewh+ScrollTop)){
            if(Btn&&oGetMore.style.display!=='none'){
                toLoadDate(data);
            }
        }
    };

    oGetMore.onclick=toLoadDate;
    function toLoadDate(data){
        Btn=false;
        if(data.length<Limit){
            oGetMore.style.display='none';
        }
        for(var i=0;i<data.length;i++){
            var str='<a href="#" class="GZ_top_img Fl Tc"><img src="{%topSrc%}" width="48" height="48"></a>'+'<div class="GZ_top_zi Fl">'+'<h3 class="F16 Oh"><a href="#" class="Fl" >{%name%}</a></h3>'+'<p class=" F14">{%bkName%}</p>'+'</div>'+'{%isGuanZhu%}';
            for(var attr in data[i]){
                var row=data[i];
                if(attr==DataFields.name){
                    str=str.replace("{%name%}",row[attr]);
                };
            
                if(attr==DataFields.bkName){
                    str=str.replace("{%bkName%}",row[attr]);
                };
            
                if(attr==DataFields.topSrc){
                    str=str.replace("{%topSrc%}",row[attr]);
                };
            
                if(attr==DataFields.isGuanZhu){
                    if(parseInt(row[attr])){
                        str=str.replace("{%isGuanZhu%}",'<a href="#" class="Fr GuanZhuBtn2 Tc" >取消关注</a>');
                    }else{
                        str=str.replace("{%isGuanZhu%}",'<a href="#" class="Fr GuanZhuBtn3 Tc" >加关注</a>');
                    }
                };
        
            }
            var oDiv=document.createElement('div');
            oDiv.className="GZ_top Pb10";
            oDiv.innerHTML=str;
            oFenSiList.insertBefore(oDiv,oGetMore);;
        };

        Btn=true;
    }
};

Bk.app.YzPL=function(){
    var oPLtext=C.G("PLtxt");
    oPLtext.onfocus=function(){
        if(this.value=='请输入评论内'){
            this.value='';
        }
    }
}
Bk.app.toViewBottom=function(){
    var oFooter=Bk.tools.getByClass(document,'Footer')[0];
    oFooter.style.position="fixed";
    oFooter.style.bottom='0px';
}
Bk.app.toggleClass=function(){
    var oWzc_btn=C.G('Wzc_btn');
    var oWzc_tit=oWzc_btn.children[0];
    var oWzc_Content=oWzc_btn.children[1];
    var aA=oWzc_Content.getElementsByTagName('a');
    var oFenLei=C.G('FenLei');
    var oBt_txt=C.G('Bt_txt');
    var oWzc_textarea=C.G('Wzc_textarea');
    function showBorder(obj,defaultValue,addClassName){
        Bk.tools.addClass(obj,addClassName);
        if(obj.value==defaultValue){
            obj.value='';
        }
    }
    function HideBorder(obj,defaultValue,addClassName){
        Bk.tools.removeClass(obj,addClassName);
        if(obj.value==''){
            obj.value=defaultValue;
        }
    }
    oBt_txt.onfocus=function(){
        showBorder(this,'点击输入标题','Showborder');
    }
    oWzc_textarea.onfocus=function(){
        showBorder(this,'添加内容','Showborder');
    }
    oBt_txt.onblur=function(){
        HideBorder(this,'点击输入标题','Showborder');
    }
    oWzc_textarea.onblur=function(){
        HideBorder(this,'添加内容','Showborder');
    }
    oWzc_tit.onclick=function(){
        if(oWzc_Content.className=='Wzc_content Hide'){
            oWzc_Content.className='Wzc_content Show';
        }else{
            oWzc_Content.className='Wzc_content Hide';
        }
    };

    for(var i=0;i<aA.length;i++){
        aA[i].nIndex=i;
        aA[i].onclick=function(){
            var classValue=this.getAttribute("data-value");
            oFenLei.value=classValue;
            oWzc_tit.innerHTML=this.innerHTML;
            oWzc_Content.className='Wzc_content Hide';
        }
    }
};

Bk.app.toDialog=function(){
    var oDialog=C.G('GZ_dialog');
    var oDialogMark=C.G('DialogMark');
    var aA=oDialog.getElementsByTagName('a');
    var viewW=document.documentElement.clientWidth||document.body.clientWidth;
    var viewH=document.documentElement.clientHeight||document.body.clientHeight;
    var srlTop=document.documentElement.scrollTop||document.body.scrollTop;
    var bodyHeight=document.documentElement.offsetHeight||document.body.offsetHeight;
    Bk.tools.removeClass(oDialogMark,"Hide");
    Bk.tools.addClass(oDialogMark,"Show");
    Bk.tools.removeClass(oDialog,"Hide");
    Bk.tools.addClass(oDialog,"Show");
    oDialogMark.style.height=bodyHeight+"px";
    oDialog.style.top=(viewH/2)-oDialog.offsetHeight/2+srlTop+"px";
    oDialog.style.left=(viewW/2)-oDialog.offsetWidth/2+"px";
    aA[0].onclick=function(){
        Bk.tools.removeClass(oDialog,"Show");
        Bk.tools.addClass(oDialog,"Hide");
        Bk.tools.removeClass(oDialogMark,"Show");
        Bk.tools.addClass(oDialogMark,"Hide");
        return 1;
    }
    aA[0].onclick=function(){
        Bk.tools.removeClass(oDialog,"Show");
        Bk.tools.addClass(oDialog,"Hide");
        Bk.tools.removeClass(oDialogMark,"Show");
        Bk.tools.addClass(oDialogMark,"Hide");
        return 0;
    }
};

Bk.app.noLoginSendPL=function(){
    var oNL_send=C.G("NL_send");
    var oPL_form=C.G("PL_form");
    Bk.ui.toggle(oNL_send,oPL_form);
}
Bk.app.toMore=function(){
    var oMore=C.G("More");
    var oM_cnt=C.G("M_cnt");
    var oM_title=C.G("M_title")
    var timer=null;
    oMore.onmouseover=function(){
        clearTimeout(timer);
        toShow();
    }
    oMore.onmouseout=function(){
        timer=setTimeout(toHide,300);
    }
    function toShow(){
        oM_cnt.style.right=parseInt((oMore.offsetWidth-oM_title.offsetWidth)/2)+"px";
        if(Bk.tools.getStyle(oM_cnt,"display")=='none'){
            Bk.tools.removeClass(oM_title,"M_title");
            Bk.tools.addClass(oM_title,"M_title2");
            Bk.tools.removeClass(oM_cnt,"Hide");
            Bk.tools.addClass(oM_cnt,"Show");
        }
    }
    function toHide(){
        if(Bk.tools.getStyle(oM_cnt,"display")=='block'){
            Bk.tools.removeClass(oM_title,"M_title2");
            Bk.tools.addClass(oM_title,"M_title");
            Bk.tools.removeClass(oM_cnt,"Show");
            Bk.tools.addClass(oM_cnt,"Hide");
            clearTimeout(timer);
        }
    }
}


function Forms()
{
    this.Rqd=/^.+$/m;
    this.Rqd.No="该项不能为空！";
    this.Ph=/^\((\d{3,4})\)|\1-?\d{5,8}$/;
    this.Ph.No="电话格式不正确！";
    this.Mb=/^\0?\d{11}$/;
    this.Mb.No="手机格式不正确！";
    this.Cn=/^[\u4E00-\u9FA5]{1,}$/;
    this.Cn.No="不允许出现非中文";
    this.Pwd=/^(\w|[^\(\)\{\}\=\:\;\.\s]){1,}$/;
    this.Pwd.No="密码不能包含'(){}=:;. '等敏感字符";
    this.Em=/^[\w|-]+@[\w|-]+(\.[\w|-]+)+$/;
    this.Em.No="邮箱格式不正确";
    this.Url=/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/i;
    this.Url.No="Url格式不正确";
    this.Idn=/^\d{17}\d|X$/;
    this.Idn.No="身份证号码不合法";
    this.Cpt=1;
    this.Cpt.No="两次输入不一致!";
    this.Cpe="两次输入不一致!";
    var that=this;
    Forms.prototype={
        Init:function(F)

        {
            var F=typeof F=="string"?C.G(F):F;
            F.V=undefined;
            for(var i=0;i<F.elements.length;i++)

            {
                var T=F.elements[i],N=T.type.toLowerCase();
                if(C.Attr(T,"Preset"))

                {
                    T.Preset=C.Attr(T,"Preset");
                }
                if(C.Attr(T,"Regex"))
                {
                    T.Regex=C.Attr(T,"Regex");
                }
                if(C.Attr(T,"Cpt"))
                {
                    T.Cpt=C.Attr(T,"Cpt");
                }
                if((N=="textarea"||N=="password"||N=="text")&&(typeof T.Preset=="string"||typeof T.Regex=="string"||typeof T.Cpt=="string"))
                {
                    if(F.id.charAt(0).toLowerCase()=="c")

                    {
                        Forms.prototype.Ve(T);
                    }
                    else
                    {
                        if(!T.Ed)

                        {
                            C.AddEvent(T,"blur",Forms.prototype.Ve,T);
                            T.Ed=true;
                        }
                        else
                        {
                            Forms.prototype.Ve(T);
                        }
                    }
                }
            }
if(F.V==undefined)
{
    F.V=true;
}
},
Ve:function(T)
{
    var T=arguments.length==1?T:arguments[1];
    if((T.Preset&&T.Preset.indexOf("Rqd")>-1)||T.value!=""||T.Regex||T.Cpt)

    {
        var Rs=T.Preset?T.Preset.split(" "):[],Hr=(typeof T.Regex=="string")&&T.Regex.Trim()!="";
        if(Hr){
            Rs.push(eval(T.Regex))
            }
        if(typeof T.Cpt=="string"&&T.Cpt!="undefined")
        {
            that.Cpt=new RegExp("^"+C.G(T.Cpt).value+"$","g");
            Rs.push(that.Cpt);
        }
        T.V=false;
        T.Nt=T.nextSibling;
        while(T.Nt&&T.Nt.nodeType!=1)

        {
            T.Nt=T.Nt.nextSibling;
        }
        T.Rpt=C.G(C.Attr(T,"Rpt"))||T.Nt;
        T.value=T.value.Trim();
        T.Em="";
        for(var j=0;j<Rs.length;j++)

        {
            if(T.Rpt&&(Rs[j]in that||typeof Rs[j]=="object"))

            {
                var Rg=that[Rs[j]]||Rs[j];
                if(T.value.search(Rg)>-1||T.value.search(Rs[j])>-1)

                {
                    T.Cls="Ok";
                    C.DelClass(T.Rpt,"No");
                    T.Rpt.innerHTML="";
                }
                else
                {
                    T.Cls="No";
                    C.DelClass(T.Rpt,"Ok");
                    T.Rpt.innerHTML=(Rs[j]in that||(that.Cpt.source==Rs[j].source))?(that[Rs[j]]?that[Rs[j]].No:that.Cpe):C.Attr(T,"Cem");
                    T.form.V=false;
                    break;
                }
            }
        }
C.AddClass(T.Rpt,T.Cls);
}
}
}
Forms.Vf=Forms.prototype.Init;
C.Batch();
}