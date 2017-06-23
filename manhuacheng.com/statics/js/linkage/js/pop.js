function open_linkage(id,name,container,linkageid) {
	returnid= id;
	returnkeyid = linkageid;
	var content = '<div class="linkage-menu"><h6><a href="javascript:;"  onclick="get_parent(this,0)" class="rt"><<返回主菜单</a><span>'+name+'</span> <a href="javascript:;" onclick="get_parent(this)" id="parent_'+id+'" parentid="0"><img src="statics/images/icon/old-edit-redo.png" width="16" height="16" alt="返回上一级" /></a></h6><div class="ib-a menu" id="ul_'+id+'">';
	for (i=0; i < container.length; i++)
	{
		content += '<a href="javascript:;" onclick="get_child(\''+container[i][0]+'\',\''+container[i][1]+'\',\''+container[i][2]+'\')">'+container[i][1]+'</a>';
	}
	content += '</div></div>';
	art.dialog({title:name,id:'edit_'+id,content:content,width:'422',height:'200',style:'none_icon ui_content_m'});
}
var level = 0;
function get_child(nodeid,nodename,parentid) {
	var content = container = '';
	var url = "api.php?op=get_linkage&act=ajax_getlist&parentid="+nodeid+"&keyid="+returnkeyid;
	$.getJSON(url+'&callback=?',function(data){
		if(data) {
			level = 1;
			$.each(data, function(i,data){ 
				container = data.split(',');
				content += '<a href="javascript:;" onclick="get_child(\''+container[0]+'\',\''+container[1]+'\',\''+container[2]+'\')">'+container[1]+'</a>';	
			})
			$("#ul_"+returnid).html(content);
			get_path(container[2],returnid);
			$("#parent_"+returnid).attr('parentid',container[2]);
		} else {
			set_val(nodename);
			$("input[name='info["+returnid+"]']").val(nodeid);
			//$("#"+returnid).after('<input type="hidden" name="info['+returnid+']" value="'+nodeid+'">');
			art.dialog({id:'edit_'+returnid}).close();
		}
	})
}

function get_parent(obj,parentid) {
	var linkageid = parentid ? parentid : $(obj).attr('parentid');
	var content = container = '';
	var url = "api.php?op=get_linkage&act=ajax_getlist&linkageid="+linkageid+"&keyid="+returnkeyid;
	$.getJSON(url+'&callback=?',function(data){
		if(data) {
			$.each(data, function(i,data){ 
				container = data.split(',');
				content += '<a href="javascript:;" onclick="get_child(\''+container[0]+'\',\''+container[1]+'\',\''+container[2]+'\')">'+container[1]+'</a>';
			})
			get_path(container[2],returnid);
			$("#parent_"+returnid).attr('parentid',container[2]);
			$("#ul_"+returnid).html(content);

		} else {
			$("#"+returnid).val(nodename);
			art.dialog({id:'edit_'+returnid}).close();
		}
	})
}

function get_path(nodeid,returnid) {
	var show_path = '';
	var url = "api.php?op=get_linkage&act=ajax_getpath&parentid="+nodeid+"&keyid="+returnkeyid;
	$.getJSON(url+'&callback=?',function(data){
		if(data) {
			$.each(data, function(i,data){ 
				show_path += data+" > ";
			})
			$("#parent_"+returnid).siblings('span').html(show_path);
		}
	})
}

function set_val(nodename) {
	var path = $("#parent_"+returnid).siblings('span').html();
	if(level==0) $("#"+returnid).html(nodename);
	else $("#"+returnid).html(path+nodename);
	level = 0;
}

//linkage 多选
function open_linkage_selected(id,name,container,linkageid,inputname) {
	returnid= id;
	returnkeyid = linkageid;
	selectinput=inputname;
	var content = '<div class="linkage-menu1"><h6><a href="javascript:;"  onclick="back_parent_more(this,0)" class="rt"><<返回主菜单</a><span>'+name+'</span> <a href="javascript:;" onclick="back_parent_more(this)" id="parent_'+id+'" parentid="0"><img src="statics/images/icon/old-edit-redo.png" width="16" height="16" alt="返回上一级" /></a></h6><div class="ib-a menu" id="ul_'+id+'">';
	for (i=0; i < container.length; i++)
	{
		content += '<li><a href="javascript:;" onclick="selected_parent(this,\''+container[i][0]+'\',\''+container[i][1]+'\',\''+container[i][2]+'\')">'+container[i][1]+'</a> <span onclick="selected_list(this,\''+container[i][0]+'\',\''+container[i][1]+'\',\''+container[i][2]+'\')">子级</span></li>';
	}
	content += '<div style="clear:both"></div></div><div style="overflow:hidden;_float:left;margin-top:10px;*margin-top:0;_margin-top:0;">';
    content += '<fieldset>';
    content += '    <legend>已选行业</legend>';
    content += '<ul class=\'list-dot-othors\' id=\'catname\'></ul>';
    content += '</fieldset>';
    content += '</div></div>';
	art.dialog({title:name,id:'edit_'+id,content:content,width:'422',height:'200',style:'none_icon ui_content_m'},function(){
			art.dialog({id:id}).close()
	},
	function(){
			art.dialog({id:id}).close()
	});
}

function selected_list(obj,id,title,parentid) {
	var content = container = '';
	var url = "api.php?op=get_linkage&act=ajax_getlist&parentid="+id+"&keyid="+returnkeyid;
	$.getJSON(url+'&callback=?',function(data){
		if(data) {
			level = 1;
			$.each(data, function(i,data){ 
				container = data.split(',');
				content += '<li><a href="javascript:;" onclick="selected_parent(this,\''+container[0]+'\',\''+container[1]+'\',\''+container[2]+'\')">'+container[1]+'</a> <span onclick="selected_list(this,\''+container[0]+'\',\''+container[1]+'\',\''+container[2]+'\')">子级</span></li>';	
			})
			content += '<div style="clear:both"></div>';
			$("#ul_"+returnid).html(content);
			get_path(container[2],returnid);
			$("#parent_"+returnid).attr('parentid',container[2]);
		} else{
			$(obj).attr('class','line_fbffe4');
			var str = "<li id='l"+id+"'>·<input type='hidden' name='info["+selectinput+"][]' value='"+id+"' /><span>"+title+"</span><a href='javascript:;' class='close' onclick=\"remove_id('l"+id+"')\"></a></li>";
			var str1 = "<li id='cl"+id+"'><span>"+title+"</span><a href='javascript:;' class='close' onclick=\"linkageid_remove('"+id+"')\"></a></li>";
			$('#add_linkages').append(str);
			$('#catname').append(str1);
		}
	})
}
function selected_parent(obj,id,title,parentid) {
	$(obj).attr('class','line_fbffe4');
	var str = "<li id='l"+id+"'>·<input type='hidden' name='info["+selectinput+"][]' value='"+id+"' /><span>"+title+"</span><a href='javascript:;' class='close' onclick=\"remove_id('l"+id+"')\"></a></li>";
	var str1 = "<li id='cl"+id+"'><span>"+title+"</span><a href='javascript:;' class='close' onclick=\"linkageid_remove('"+id+"')\"></a></li>";
	$('#add_linkages').append(str);
	$('#catname').append(str1);
}
//移除ID
function linkageid_remove(id) {
	$('#l'+id).remove();
	$('#cl'+id).remove();
}

function back_parent(obj,parentid) {
	var linkageid = parentid ? parentid : $(obj).attr('parentid');
	var content = container = '';
	var url = "api.php?op=get_linkage&act=ajax_getlist&linkageid="+linkageid+"&keyid="+returnkeyid;
	$.getJSON(url+'&callback=?',function(data){
		if(data) {
			$.each(data, function(i,data){ 
				container = data.split(',');
				content += '<a href="javascript:;" onclick="selected_list(this,\''+container[0]+'\',\''+container[1]+'\',\''+container[2]+'\')">'+container[1]+'</a>';
			})
			get_path(container[2],returnid);
			$("#parent_"+returnid).attr('parentid',container[2]);
			$("#ul_"+returnid).html(content);

		} else {
			$("#"+returnid).val(nodename);
			art.dialog({id:'edit_'+returnid}).close();
		}
	})
}

//linkage 多选
function back_parent_more(obj,parentid) {
	var linkageid = parentid ? parentid : $(obj).attr('parentid');
	var content = container = '';
	var url = "api.php?op=get_linkage&act=ajax_getlist&linkageid="+linkageid+"&keyid="+returnkeyid;
	$.getJSON(url+'&callback=?',function(data){
		if(data) {
			$.each(data, function(i,data){ 
				container = data.split(',');
				content += '<li><a href="javascript:;" onclick="selected_parent(this,\''+container[0]+'\',\''+container[1]+'\',\''+container[2]+'\')">'+container[1]+'</a> <span onclick="selected_list(this,\''+container[0]+'\',\''+container[1]+'\',\''+container[2]+'\')">子级</span></li>';
			})
			content += '<div style="clear:both"></div>';
			get_path(container[2],returnid);
			$("#parent_"+returnid).attr('parentid',container[2]);
			$("#ul_"+returnid).html(content);

		} else {
			$("#"+returnid).val(nodename);
			art.dialog({id:'edit_'+returnid}).close();
		}
	})
}