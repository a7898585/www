<?php
header("Pragma: no-cache");
header("Cache-Control:no-store, max-age=0, must-revalidate, post-check=0, pre-check=0");

define('OLCMS_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);
include OLCMS_PATH.'/olcms/base.php';

$DB = pc_base::load_model('content_model');
$author = array("蔡志忠","敖幼祥","王泽","郑问","游龙辉","张武顺","何志文","马荣成","黄玉郎","李志清","刘云杰","林祥焜","陈佳华","曾月泉","何志文","常胜","吴毓琦","GRACE","黄佳莉","赖安","依欢");

foreach($author as $val){
	$sql = 'select a.id,b.Author from tt_Cartoon_data b LEFT JOIN tt_Cartoon a ON b.id=a.id WHERE b.Author Like "%'.$val.'%"';
	$query = $DB->query($sql);
	$List =  $DB->fetch_array($query);
	foreach($List as $val){
		$delete_Ids[] = $val['id'];
	}
	$str = "'";
	$str .= implode("','",$delete_Ids);
	$str .= "'";
	$del_sql1 = 'DELETE tt_Cartoon,tt_Cartoon_data from tt_Cartoon LEFT JOIN tt_Cartoon_data ON tt_Cartoon.id=tt_Cartoon_data.id WHERE tt_Cartoon.id IN('.$str.')';
	$query = $DB->query($del_sql1);
	$del_sql2 = 'DELETE tt_CartoonDetail,tt_CartoonDetail_data from tt_CartoonDetail LEFT JOIN tt_CartoonDetail_data ON tt_CartoonDetail.id=tt_CartoonDetail_data.id WHERE tt_CartoonDetail.manhuaid IN('.$str.')';
	$query = $DB->query($del_sql2);
	echo "ok";
}
?>
