$(document).ready(function(){

    $('.timingkey').live('keyup',function(){
		
        var obj=$(this);
        if(obj.val().replace(/(^\s*)|(\s*$)/g,'')!='')
        {
            if($('#timingSaveControl').val()=='1')
            {
                return;
            }
			
            $('#timingSaveControl').val('1');
            var timing=setInterval(function(){
                if(obj.val().replace(/(^\s*)|(\s*$)/g,'')=='')
                {
                    clearInterval(timing);
                    $('#timingSaveControl').val('0');
                    return;
                }
				
                submitfuntTiming($('#simsupCut').val(),'');
            },300000);
        }
    });
	
})

/* 文章保存到草稿箱 */
function submitfuntTiming(type,mode){
	
    if($('#simSaveEditControl').val()=='0')
    {
        var actions=baseurl+'/index.php/mybloglist/action/'+mydomainname+'?act=add';
    }
    else
    {
        var actions=baseurl+'/index.php/mybloglist/action/'+mydomainname+'?act=edit';
    }
	

    if(type == 'sup'){
        var tags =$('#tag').val().replace(/(^,)|(,$)/g,'');
        tags = tags.split(',');
        if(tags.length > 5){
            if(mode=='handSave')
            {
                showalert('标签数最多不能超过5个','popupTip','TMDeleteSuccess');
            }
            return;
        }
		
        editor.sync();
    }
    if(type == 'sim')
    {
        editorsimple.sync();
    }
	
    if($('#'+type+'Title').val() == ''){
        if(mode=='handSave')
        {
            showalert('请输入文章标题','popupTip','TMDeleteSuccess');
        }
        return;
    }
    if($('#'+type+'Content').val() == ''){
        if(mode=='handSave')
        {
            showalert('请输入文章内容','popupTip','TMDeleteSuccess');
        }
        return;
    }
	
    if($('#'+type+'Cnfolck').attr('checked') == true){
        var reg = /^\d+$/;
        if (!reg.test($('#'+type+'GiftPrice').val())) {
            if(mode=='handSave')
            {
                showalert('鲜花数只能为整数','popupTip','TMDeleteSuccess');
            }
            return;
        }
    }
	
    $.ajax({
        type: 'post',
        contentType:'application/x-www-form-urlencoded',
        url:actions,
        data:$('#'+type+'Form').serialize(), 
        dataType:'json',
        async: false,
        success: function(data){
			
            if(data.errno == 'success'){
                var myDate = new Date();
                var minute=myDate.getMinutes();
                if(minute<10)
                {
                    minute='0'+minute;
                }
                $('#'+type+'TimingSave').css('display','').html(myDate.getHours()+':'+minute+'保存到草稿箱');
                if($('#simSaveEditControl').val()=='0')
                {
                    $('#simSaveEditControl').val(data.articleid);
                    $('#simSaveEditTimeControl').val(data.appeartime);
                    $('#supSaveEditControl').val(data.articleid);
                    $('#supSaveEditTimeControl').val(data.appeartime);
                }
                if(mode=='handSave')
                {
                    showalertminute('保存草稿箱成功',2000,'','popupTip','TMDeleteSuccess');
                }
            //afterSubmit();
            }
            else
            {
                if(mode=='handSave')
                {
                    showalertminute('保存草稿箱失败',2000,'','popupTip','TMDeleteSuccess');
                }
            }
			
			
        },
        error:function (XMLHttpRequest, textStatus, errorThrown) {
			
            if(mode=='handSave')
            {
                showalertminute('文章已保存到草稿箱',2000,'','popupTip','TMDeleteSuccess');
            }
			
        }
    });
	 
}