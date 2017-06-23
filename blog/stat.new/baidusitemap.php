<?php

/*
 * 百度统计实时推送
 * baidusitemap
 */
define('ROOT', dirname(__FILE__));
define('DEFAULT_PATH', '/home/www/html');
define('BLOG_MANAGE_LOG', DEFAULT_PATH . '/logs/'); //后台日志   /home/www/html/logs/baidusitemap 
require_once ROOT . '/inc/config.inc.php';
require_once ROOT . '/library/ADODB/adodb.inc.php';

header('Content-Type: text/html; charset=utf-8');
global $mysqli;

$mysqli = NewADOConnection($config['blog_db']['type']);
if (!$mysqli->Connect($config['blog_db']['hostname'], $config['blog_db']['username'], $config['blog_db']['password'], $config['blog_db']['database'])) {
    error_log(date("Y-m-d H:i:s", time()) . " || ------MYSQL could not connect3------|\r\n", 3, BLOG_MANAGE_LOG . 'baidusitemap.log');
    exit;
}
$mysqli->Execute("set names latin1");

$time = date('Y-m-d H:i', time() - 60 * 35);
$sql = "SELECT a.ArticleID,a.AppearTime,a.DomainName  FROM tbBlogArticleChart a join tbBlogMember b on a.MemberID = b.MemberID WHERE IsDel=0 AND Property IN (0,1) and b.Status = 0 and a.AppearTime >'" . $time . "' ";


$rs = $mysqli->GetAll($sql);


$xml = '<?xml version="1.0" encoding="UTF-8"?><urlset>';
foreach ($rs as $arr) {
    $url = $config['newblog'] . $arr['DomainName'] . '/article/' . strtotime($arr['AppearTime']) . '-' . $arr['ArticleID'] . '.html';
    $xml .="<url>
			<loc><![CDATA[" . $url . "]]></loc>
			<lastmod>" . substr($arr['AppearTime'], 0, 10) . "</lastmod>
			<changefreq>daily</changefreq>
			<priority>0.8</priority>
		</url>";
}
$xml .= "</urlset>";


function ping($server, $xml) {
    $ch = curl_init();
    $headers = array(
        "Content-type: text/xml;charset=\"utf-8\"",
        "Accept: text/xml"
    );
    curl_setopt_array(
            $ch, array(
        CURLOPT_URL => $server,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POSTFIELDS => $xml
            )
    );
    return curl_exec($ch);
}

$res = ping('http://ping.baidu.com/sitemap?site=blog.cnfol.com&resource_name=sitemap&access_token=d92Ixooe', $xml);

error_log(date("Y-m-d H:i:s", time()) . " || " . print_r($res, true) . " || " . print_r(count($rs), true) . "-----|\r\n", 3, BLOG_MANAGE_LOG . 'baidusitemap_data.log');

echo date('Y-m-d H:i:s', time()) . '推送成功！' . "\r\n";
exit;
