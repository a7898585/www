<?php
/*
 * <?xml version="1.0" encoding="utf-8"?>
*<AD>
*	<Item Title="广告1" Href="www.sina.com" Time="10">
*</AD>
 * 
 */
defined('IN_OLCMS') or exit('No permission resources.');
$db = pc_base::load_model ( 'ad_model' );
$data = $db->select('','*','','`aid` ASC');

$xml='<?xml version="1.0" encoding="utf-8"?>';
switch($_GET['Pos']){
	case 'foot':	
		if($data){
			foreach ($data as $v){
				$adlist.='<Item Title="'.$v['title'].'" Href="'.$v['url'].'" Time="3"/>';
				}
				$xml.='<AD>'.$adlist.'</AD>';
				echo $xml;
		}else echo $xml.'<AD></AD>';
	break;
	
	case 'top':
		/*if($data){
			$str='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf8"><title>漫画城广告位</title>
<body><table width="680" height="80" border="0" cellpadding="0" cellspacing="0" style="text-align:left;margin:auto;border:solid 1px;"><tr>';
			for($i=0;$i<9;$i++){
				if($i%4==3)
				$adtable.='</tr><tr>';
				else
				$adtable.='<td height="25"><a href="'.$data[$i]['url'].'" title="'.$data[$i]['title'].'">'.$data[$i]['title'].'</a></td>';
			}
			$tmp=$str.$adtable.'</table></body></html>';
			$t=$xml.'<AD Width="680" Height="80" Time="10"><![CDATA['.$tmp.']]></AD>';
			echo $t;
		}*/
		$str1='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf8"><title>漫画城广告位</title>
<body scroll="no" style="margin:0;height:auto"><div><a target="_blank" href="http://stat.ziye8.com/index.php/stat/channel_stat/?id=74"><img src="http://www.manhuacheng.com/uploadfile/ad/ad1.gif"/></a></div></body></html>';
      $str3='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf8"><title>漫画城广告位</title>
<body  scroll="no" style="margin:0;height:auto"><div><a  href="http://t.hly.com/?tid=2086&sid=sxdreg" rel="external nofollow"><img src="http://www.manhuacheng.com/uploadfile/ad/shenxian2.jpg"></a></div></body></html>';
		$str='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf8"><title>漫画城广告位</title>
<body><div><script type="text/javascript"><!--
google_ad_client = "pub-8041083238990119";
/* 728x90, 创建于 11-7-25 */
google_ad_slot = "2742544091";
google_ad_width = 728;
google_ad_height = 90;
//-->
</script>
<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script></div></body></html>';
		$str2='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf8"><title>漫画城广告位</title>
<body><div>
<script type="text/javascript">
/*728*90，manhuacheng 2 创建于2011-7-26*/ var cpro_id = "u554465";</script>
<script src="http://cpro.baidu.com/cpro/ui/c.js" type="text/javascript">
</script>
</div></body></html>';
        exit();
		$t=$xml.'<AD Align="top" Width="728" Height="90" Time="10"><![CDATA['.$str3.']]></AD>';
    	echo $t;
	break;
	default:
		echo $xml.'<AD></AD>';
	break;
}
?>
