<?php

//viewdaystat.php  /home/httpd/logs/blog_manage/viewdaystat_log.log
define('ROOT', dirname(__FILE__));
require_once ROOT . '/ketfilter_com.php';
$sql = "select sum(TodayClick) as TODAYVISIT, sum(RealTodayClick) as REALTODAYVISIT  from tbBlogMemberDayClick where Dates='" . date('Ymd') . "'";
$query = $mysqli->GetRow($sql);
unset($sql);
$sql = "select sum(TodayClick) as TODAYVISIT1, sum(RealTodayClick) as REALTODAYVISIT1, sum(RealTotalClick) as REALTOTALVISIT, sum(TotalClick) as TOTALVISIT from tbBlogMemberDayClick ";
$queryAll = $mysqli->GetRow($sql);
//echo $sql;
//print_r($query);print_r($queryAll);
$date = date('Ymd');
$rs = array();
$rs[$date]['TODAYVISIT'] = $query['TODAYVISIT'];
$rs[$date]['REALTODAYVISIT'] = $query['REALTODAYVISIT'];
$rs[$date]['TODAYVISIT_COM'] = $queryAll['TODAYVISIT1'];
$rs[$date]['REALTODAYVISIT_COM'] = $queryAll['REALTODAYVISIT1'];
$rs[$date]['TOTALVISIT'] = $queryAll['TOTALVISIT'];
$rs[$date]['REALTOTALVISIT'] = $queryAll['REALTOTALVISIT'];
print_r($rs);
//file_put_contents(BLOG_MANAGE_LOG . 'viewdaystat.php', serialize($rs));
error_log(date("Y-m-d H:i:s", time()) . " ||参数：" . print_r($rs, true) . "\r\n", 3, BLOG_MANAGE_LOG. 'viewdaystat_log.log');
       