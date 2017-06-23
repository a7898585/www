<?php
header("Pragma: no-cache");
header("Cache-Control:no-store, max-age=0, must-revalidate, post-check=0, pre-check=0");

define('OLCMS_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);
include OLCMS_PATH.'/olcms/base.php';
require OLCMS_PATH.'/olcms/Include/httpsqs_client.php'; // 需要包含这个类文件
$DB = pc_base::load_model('content_model');
$id = intval($_GET['id']);

// 设置查询规则

$sql = "select a.catid,a.id,a.title,a.updatetime,b.content from tt_news a left join tt_news_data b on a.id=b.id where a.id>$id AND a.title !='' AND b.content !='' limit 1";
$query = $DB->query($sql);
$Detail =  $DB->fetch_array($query);
// 合并2个表，输出文章内容
//$Detail = array_merge($Info, $content);

if (! $Detail) die('ok');
$httpsqs = new httpsqs();
$Data['site'] = 49; // 网站标识ID，具体ID以OA系统的站点ID为准
if($Detail[0]['catid'] =='55'){
	$url = 'http://www.manhuacheng.com/jiebao'.$Detail[0]['id'].'/';
}else{
	$url = 'http://www.manhuacheng.com/zixun'.$Detail[0]['id'].'/';
}
$Data['url'] = $url;

$Data['title'] = $Detail[0]['title'];

$nohtml = _strip_tags($Detail[0]['content']);
$nohtml = preg_replace ( '/&([a-z]+);/s', '', $nohtml );
$Data ['check'] =  'BSC258';                                                                                  
$Data['description'] = $nohtml; // 文章内容去除HTML标签
$Data['updateTime'] = strval($Detail[0]['updatetime']); // 文章更新时间的时间戳
$Data ['category'] = '001'.$Detail[0]['catid'];
$httpsqs->put('articlestore', json_encode($Data));
echo 'id=' . $Detail[0]['id'];
echo '<br><br>' . date('H:i:s');

echo '<meta http-equiv="refresh" content="1;URL=?id=' . $Detail[0]['id'] . '" />';

// 补充函数
function _strip_tags ($string, $allowable_tags = null)
{
    if (is_array($string)) {
        foreach ($string as $key => $value) {
            $string[$key] = _strip_tags($value, $allowable_tags);
        }
        return $string;
    } else
        return strip_tags($string, $allowable_tags);
}
?>
