<?php
/**
 * file:download.php
 * author:xiezhihai
 * :modify the download url.
 */

/*
 * @getdomain
 * @param: domain host
 * @return:topleveldomain
 */
function getdomain($host){
    $host=strtolower($host);
    if(strpos($host,'/')!==false){
        $parse = @parse_url($host);
        $host = $parse['host'];
    }
    $topleveldomaindb=array('com','edu','gov','int','mil','net','org','biz','info','pro','name','museum','coop','aero','xxx','idv','mobi','cc','me');
    $str='';
    foreach($topleveldomaindb as $v){
        $str.=($str ? '|' : '').$v;
    }
    $matchstr="[^.]+.(?:(".$str.")|w{2}|((".$str.").w{2}))$";
    if(preg_match("/".$matchstr."/ies",$host,$matchs)){
        $domain=$matchs['0'];
    }else{
        $domain=$host;
    }
    return $domain;
}
//获取来路
$arr_url = parse_url($_GET['u']);
switch(getdomain($arr_url['host'])){
	case '2345.com':
		header("location:http://www.manhuacheng.com/download/2345/manhuacheng.rar");
		exit;
		break;
	case '1616.net':
	    header("location:http://www.manhuacheng.com/download/1616/manhuacheng.rar");
		exit;
		break;
	case 'duote.com':
		header("location:http://www.manhuacheng.com/download/duote/manhuacheng.rar");
		exit;
		break;
		case 'tao123.com':
		header("location:http://www.manhuacheng.com/download/tao123/manhuacheng.rar");
		exit;
		break;
	default:
		header("location:http://www.manhuacheng.com/download/manhuacheng.rar");
		break;
}
?>