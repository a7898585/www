//tab切换
function jdhddh(thisObj,Num){
    if(thisObj.className == "active")return;
    var tabObj = thisObj.parentNode.id;
    var tabList = document.getElementById(tabObj).getElementsByTagName("a");
    for(i=0; i <tabList.length; i++){
        if (i == Num){
            thisObj.className = "navhl_h"; 
            document.getElementById(tabObj+"_c"+i).style.display = "block";
        }else{
            tabList[i].className = "navhl_q"; 
            document.getElementById(tabObj+"_c"+i).style.display = "none";
        }
    } 
}
//js仿表单下拉
$(document).ready(function(){
    $(".select_bg_div").mouseout(function(){
        $(this).prev().hide();
        $(this).hide();
    });
    $('.select_main_click').click(function(){
        $(this).next().toggle();
        $(this).next().next().toggle();
    });
    $(".dzdlsz_btn").click(function(){
        $(this).parent().find(".dzdlsz_c").slideToggle("slow");
        $(this).toggleClass("dzdlsz_btn_h");
        return false;
    });
});


//导航下拉
$(document).ready(function(){
    $('.cbtc_btn').mousemove(function(){
	$(this).find('.cbtc_c').slideDown();
	$(this).find('.cbtc_b').addClass('cbtc_xd');
	});
	$('.cbtc_btn').mouseleave(function(){
	$(this).find('.cbtc_c').slideUp(100);
	$(this).find('.cbtc_b').removeClass('cbtc_xd');
	});
});

























































