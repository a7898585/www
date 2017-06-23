var ifcheck = true;
$(document).ready(function(){
    $(':checkbox[name="checked_all"]').click(function(){
        var $e = $(':checkbox').not('[name="checked_all"], :disabled');
        for (var i=0;i<$e.length;i++) {
            var e = $e[i];
            e.checked = ifcheck;
        }
        ifcheck = ifcheck == true ? false : true;
    });
});

























































