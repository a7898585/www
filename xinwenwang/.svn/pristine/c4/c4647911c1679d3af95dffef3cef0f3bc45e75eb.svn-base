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

