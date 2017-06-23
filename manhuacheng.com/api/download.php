<?php
/**
 * file name:download.php
 * author:xiezhihai
 * :modify the download url.
 */

/*
 * Function name:getdomain
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
$db = pc_base::load_model ( 'cooprate_model' );
$uhost=getdomain($arr_url['host']);

$udata=$db->get_one(array('topdomain' =>$uhost ));

if(isset($udata['dlfilename'])&&(!empty($udata['dlfilename']))){
	header("location:http://www.manhuacheng.com/download/".$udata['dlfilename']."/manhuacheng.rar");
	exit;
}else{
	$file = OLCMS_PATH."caches/set_cooprate.cache";
	if(file_exists($file)){
			$cacheArray = unserialize(file_get_contents($file));
	}else $cacheArray=array("url"=>"");
	if(isset($cacheArray['url'])&&!empty($cacheArray['url'])){
		header("location:".$cacheArray['url']);
		exit;
	}else{
		header("location:http://www.manhuacheng.com/download/manhuacheng.rar");
		exit;
	}
}

?>