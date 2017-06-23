<?php

//计算时间
function _time($time) {
    if ($time < 0)
        return false;
    $time = time() - $time;
    $minute = ceil($time / 60);
    if ($minute < 60)
        return $minute . '分钟前';
    $hour = floor($minute / 60);
    if ($hour < 24)
        return $hour . '小时前';
    $day = floor($hour / 24);
    if ($day < 30)
        return $day . '天前';
    $month = floor($day / 30);
    if ($month < 12)
        return $month . '月前';
    return floor($month / 12) . '年前';
}

/**
 * 获取产品特色
 */
function getProFeature($id=0){
    $data = array(
        '1'=>'消费型','2'=>'红利返还','3'=>'满期返钱','4'=>'保单贷款','5'=>'保费豁免',
        '6'=>'可返钱','7'=>'年金转换','8'=>'领取灵活','9'=>'可续保','10'=>'津贴型',
        '11'=>'报销型','12'=>'可调保额保费','13'=>'体检可优惠','14'=>'灵活缴纳保费',
    );
    return $id?$data[$id]:$data;
}
function checkemail($inaddress){
    return (ereg("^([a-za-z0-9_-])+@([a-za-z0-9_-])+(\.[a-za-z0-9_-])+",$inaddress));
}