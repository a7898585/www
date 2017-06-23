function check_seccode(load_function,function_para){
	var host=window.location.host.replace('www.','member.');
	var check_url="/public/ajax_seccode";	
	$.getJSON('http://'+host+check_url+'?callback=?',function(result){
            if (result.status=='3'){
            		layer.alert("您还未登录，请先登录",function(){
					var login_url='http://'+host.replace('member','www')+"/public/login";
					window.location.href=login_url;
				});
            }else if(result.status=='4'){
				layer.open({
    				type: 1,
					title:'安全码验证',
    				skin: 'layui-layer-rim', //加上边框
    				area: ['450px', '200px'], //宽高
    				content: '<table class="ui-dialog-grid"><tbody><tr><td i="body" class="ui-dialog-body"><div i="content" class="ui-dialog-content"> <table cellpadding="0" cellspacing="0" class="zh_table"><tbody><tr><td>安全码：</td><td align="left" colspan="2"><div class="out_right"><input type="password" id="anquanma" value="" class="wt_input k_w" autocomplete="off"><font class="txt999">请输入您的安全密码</font>[<a href="http://'+host+'/account/password?op=seccode" target="_blank" style="color:blue;">忘记安全码？</a>]</div></td></tr><tr><td>提醒：</td><td align="left" colspan="2"><div class="out_right"><a href="#" target="_blank" style="color: blue;">什么是安全码？</a></div></td></tr></tbody></table></div></td></tr><tr><td i="footer" class="ui-dialog-footer"><div i="statusbar" class="ui-dialog-statusbar" style="display: none;"></div><div i="button" class="ui-dialog-button"><button type="button" style="width:undefined" data-trigger="cancel" onclick="layer.closeAll();">取消</button><button type="button" style="width:undefined" data-trigger="ok" autofocus="" class="ui-dialog-autofocus" onclick="send_secode(\''+load_function+'\',\''+function_para+'\')">确认</button></div></td></tr></tbody></table>'
	});
			}else if(result.status=='1'){
				layer.closeAll();
				if(load_function){
					window[load_function](function_para);
				}
			}else{
				layer.alert('系统出错');
			}
        });
}
function sale_form_submit(){
	$('#sale_form').submit();
}
function send_secode(load_function,function_para){
	var seccode=$('#anquanma').val();
	var host=window.location.host.replace('www','member');
	var check_url="/public/ajax_seccode/seccode/"+seccode;	
	$.getJSON('http://'+host+check_url+'?callback=?',function(result){
			if (result.status=='3'){
            		layer.alert("您还未登录，请先登录",function(){
					var login_url=host.replace('member','www')+"/public/login";
					window.location.href=login_url;
				});
            }else if(result.status=='2'){
				layer.tips('安全码输入错误', '#anquanma');
			}else if(result.status=='1'){
				layer.closeAll();
				if(load_function){
					window[load_function](function_para);
				}
			}else{
				layer.alert('系统出错');
			}
	});
}
function SelextAll(){
		if($('#selectBox').val()=='0'){
			$('#selectBox').val('1');
			$(".checklBox").each(function(){
				if($(this).attr("disabled")!='disabled'){
					$(this).prop("checked",true);
				}
			});
		}else{
			$('#selectBox').val('0'); 
			$(".checklBox").each(function(){
				if($(this).attr("disabled")!='disabled'){
					$(this).prop("checked",false);
				}
			});
		}
}


function yikoujia_submit(num){
		if(!$('#chkRole').attr("checked")){
			layer.alert("请勾选域名交易规则");
			return;
		}
		var num=parseInt(num);
		var from=1;
		while(from<=num){
			var price=$('#price'+from).val();
			if(!price){
				layer.alert("请输入域名一口价价格");
				return;
			}
			if(isNaN(price)){
				layer.alert("价格填写错误，请填写整数");
				return;
			}
			from++;
		}
		check_seccode('sale_form_submit');
}

function chujia_submit(num){
		if(!$('#chkRole').attr("checked")){
			layer.alert("请勾选域名交易规则");
			return;
		}
		var num=parseInt(num);
		var from=1;
		while(from<=num){
			var price=$('#price'+from).val();
			if(!price){
				layer.alert("请输入域名价格");
				return;
			}
			if(isNaN(price)){
				layer.alert("价格填写错误，请填写整数");
				return;
			}
			from++;
		}
		check_seccode('sale_form_submit');
	}
function paimai_submit(num){
		if(!$('#chkRole').attr("checked")){
			layer.alert("请勾选域名交易规则");
			return;
		}
		var num=parseInt(num);
		var from=1;
		while(from<=num){
			var price=$('#price'+from).val();
			if(!price){
				layer.alert("请输入域名起拍价格");
				return;
			}
			if(isNaN(price)){
				layer.alert("价格填写错误，请填写整数");
				return;
			}
			from++;
		}
		check_seccode('sale_form_submit');
	}
function batch_edit_submit(num){
		if(!$('#chkRole').attr("checked")){
			alert("请勾选域名交易规则");
			return;
		}
		var num=parseInt(num);
		var from=1;
		while(from<=num){
			var price=$('#price'+from).val();
			if(!price){
				alert("请输入域名价格");
				return;
			}
			if(isNaN(price)){
				alert("价格填写错误，请填写整数");
				return;
			}
			from++;
		}
		check_seccode('sale_form_submit');
	}


	function setText(id,value){
		var text=$('#'+id).text();
		if(text=='请选择'){
			$('#'+id).text(value);
		}
	}
	function bianji(x){
		$('#TOP_BGCOVERDIV').show();
		$('#bei_main_txare').val('');
		$('#hidid').val(x);
	}
	function setSummary(){
		var text=$('#bei_main_txare').val();
		var id=$('#hidid').val();
		$('#summary'+id).val(text);
		$('#tmpid'+id).text(text);
		$('#TOP_BGCOVERDIV').hide();
	}
	function setValue(x,key,num){
		var num=parseInt(num);
		var from=1;
		while(from<=num){
			if($('#checklBox'+from).attr("checked")){
				$('#'+key+from).val(x);
			}
			from++;
		}
	}
	
	function YiKouJia_Buy(sale_id){
		layer.confirm('确认购买此域名吗？', {
    		btn: ['买','不买'] //按钮
		}, function(){
    			$.getJSON("/Sell/ajax",{ d:'yikoujia',sale_id:sale_id},
   				function(data){
    				if(data.status=='1'){
						layer.msg('购买成功',{icon:1,time:2000});
						if(data.jump){
							window.location.href=data.jump;
						}else{
							window.location.reload();
						}
					}else{
						layer.alert(data.message);
					}
   				} 
			);
		}, function(){
    			layer.msg('取消', {
        		time: 1000, //1s后自动关闭
    	});
		});
		
	}
	function PushFormSubmit(){
		var domains=$('#domains').val();
		if(!domains){
			layer.alert('请输入要push的域名');
			return;
		}
		var mid=$('#mid').val();
		if(!mid){
			layer.alert('请输入接收方ID');
			return;
		}
		var email=$('#email').val();
		if(!email){
			layer.alert('请输入接收方Email');
			return;
		}
		var money=$('#money').val();
		if(!checkRate(money) && money!=0){
			layer.alert('索要金额必须是整数');
			return;
		}
		$('#form_domain_push').submit();
	}
	function checkRate(input){  
     	var re = /^[1-9]+[0-9]*]*$/;
     	if (!re.test(input)) return false; 
	 	return true;
	}  
	function PushCanel(id){
		layer.confirm('确定要取消push该域名吗？', {
    		btn: ['确定','取消'] //按钮
		}, function(){
    			$.getJSON("/domain/ajax",{ d:'push_cancel',id:id},
   				function(data){
    				if(data.status=='1'){
						layer.msg('取消push成功',{icon:1,time:2000});
						if(data.jump){
							window.location.href=data.jump;
						}else{
							window.location.reload();
						}
					}else{
						layer.alert(data.message);
					}
   				} 
			);
		}, function(){
    			layer.msg('取消', {
        		time: 1000, //1s后自动关闭
    	});
		});
	}
	
	function XunJia_Buy(sale_id){
		var chujia=parseInt($('#chujia').val());
		if(!chujia){
			layer.alert("请填写出价");
			return;
		}
		var min_price=parseInt($('#seller_price').val());
		if(chujia<min_price){
			layer.alert('价格不能小于'+min_price+'元');
			return;
		}
		layer.confirm('确认出价吗？', {
    		btn: ['出价','不出了'] //按钮
		}, function(){
    			$.getJSON("/Sell/ajax",{ d:'xunjia',sale_id:sale_id,price:chujia},
   				function(data){
    				if(data.status=='1'){
						layer.msg('出价成功',{icon:1,time:2000});
						if(data.jump){
							window.location.href=data.jump;
						}else{
							window.location.reload();
						}
					}else{
						layer.alert(data.message);
					}
   				} 
			);
		}, function(){
    			layer.msg('取消', {
        		time: 1000, //1s后自动关闭
    	});
		});
		
	}
	
	
	
	function TradeBuyer(x){
		layer.confirm('确认？', {
    		btn: ['确定','取消'] //按钮
		}, function(){
    			$.getJSON("/Domain/deal_buyer",{trade_id:x},
   				function(data){
    				if(data.status=='1'){
						layer.msg('操作成功',{icon:1,time:2000});
						if(data.jump){
							window.location.href=data.jump;
						}else{
							window.location.reload();
						}
					}else{
						layer.alert(data.message);
					}
   				} 
			);
		}, function(){
    			layer.msg('取消', {
        		time: 1000, //1s后自动关闭
    	});
		});
	}
	function TradeSeller(x){
		layer.confirm('确认？', {
    		btn: ['确定','取消'] //按钮
		}, function(){
    			$.getJSON("/Domain/deal_seller",{trade_id:x},
   				function(data){
    				if(data.status=='1'){
						layer.msg('操作成功',{icon:1,time:2000});
						window.location.reload();
					}else{
						layer.alert(data.message);
					}
   				} 
			);
		}, function(){
    			layer.msg('取消', {
        		time: 1000, //1s后自动关闭
    	});
		});
	}
	function DomainTransfer(id){
		$.post("/Domain/ajax", { d:"domain_transfer",id:id },
   				function(data){
    				if(data.status=='1'){
						layer.alert("域名转入成功，交易进入等待买家付款");
						if(data.jump){
							window.location.href=data.jump;
						}else{
							window.location.reload();
						}
					}else{
						layer.alert(data.message);
					}
   				} 
		);
	}
	
	function CloseBuyer(id){
		layer.confirm('确定不购买域名吗？', {
    		btn: ['确定','取消'] //按钮
		}, function(){
    			$.getJSON("/Domain/cancel_buyer",{id:id},
   				function(data){
    				if(data.status=='1'){
						layer.msg('成功取消购买',{icon:1,time:2000});
						window.location.reload();
					}else{
						layer.alert(data.message);
					}
   				} 
			);
		}, function(){
    			layer.msg('取消', {
        		time: 1000, //1s后自动关闭
    	});
		});
		
		
		
	}
	function CloseSeller(id){
		layer.confirm('确定不出售域名吗？', {
    		btn: ['确定','取消'] //按钮
		}, function(){
    			$.getJSON("/Domain/cancel_seller",{id:id},
   				function(data){
    				if(data.status=='1'){
						layer.msg('成功取消出售域名',{icon:1,time:1000});
						window.location.reload();
					}else{
						layer.alert(data.message);
					}
   				} 
			);
		}, function(){
    			layer.msg('取消', {
        		time: 1000, //1s后自动关闭
    	});
		});
		
		
	}
	function Notice(x){
		$.post("/Sell/ajax", { d:"notice",sale_id:x },
   				function(data){
    				if(data.status=='1'){
						layer.msg('关注成功');
					}else{
						layer.alert(data.message);
					}
					
   				} 
		);
	}
	function DelNotice(x){
		layer.confirm('确定取消关注该域名吗？', {
    		btn: ['确定','取消'] //按钮
		}, function(){
    			$.getJSON("/domain/ajax",{ d:"del_notice",id:x},
   				function(data){
    				if(data.status=='1'){
						layer.msg('成功取消关注该域名',{icon:1,time:1000});
						window.location.reload();
					}else{
						layer.alert(data.message);
					}
   				} 
			);
		}, function(){
    			layer.msg('取消', {
        		time: 1000, //1s后自动关闭
    	});
		});
	}
	function buy_form_submit(){
		$('#buy_form').submit();
	}