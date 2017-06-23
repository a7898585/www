<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
|--------------------------------------------------------------------------
| Cnfol_wap 扩展库  0.1
|--------------------------------------------------------------------------
|
| 此文件放于system/libraries/下
|
| 使用方法:
| $this->load->library('Cnfol_wap');
| $this->cnfol_wap->wap_header();
|--------------------------------------------------------------------------
*/

class CI_Cnfol_wap
{

    function CI_Cnfol_wap()
    {
    }


    /**
     * wap头信息
     */
    function wap_header()
    {
        $header = '<?xml version="1.0"?>'.chr(13).chr(10);
        $header .='<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN""http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">'.chr(13).chr(10);
        return $header;
    }

    /**
     * utf8转gb2312,支持数组或字符
     *
     * 参数$data是数组，返回值也为数组
     *          是字符串，返回值也为字符串
     */

    function utf2gb($data)
    {
        if(is_array($data))
        {
            foreach($data as $key=>$value)
            {
                $data[$key] = iconv('utf-8','gb2312//IGNORE',$value);
            }
            return $data;
        }else{
            return iconv('utf-8','gb2312//IGNORE',$data);
        }
    }

    /**
     * gb2312转utf8,支持数组或字符
     *
     * 参数是数组，返回值也为数组
     * 参数是字符串，返回值也为字符串
     */

    function gb2utf($data)
    {
        if(is_array($data))
        {
            foreach($data as $key=>$value)
            {
                $data[$key] = iconv('gb2312','utf-8',$value);
            }
            return $data;
        }else{
            return iconv('gb2312','utf-8',$data);
        }
    }

    /**
     * 按字来切分字符,需要mb_string扩展模块
     * $str        被截取的字符串
     * $start      要截取的字符串的开始位置
     * $strlen     要截取的字符串的字数
     * $encoding   要截取的字符串编码
     */
    function mb_substr($str,$start=0,$strlen,$encoding='utf-8')
    {
        return mb_substr($str,$start,$strlen,$encoding);
    }

    /**
     * 按字节来切分字符,需要mb_string扩展模块
     * $str        被截取的字符串
     * $start      要截取的字符串的开始位置
     * $strlen     要截取的字符串的字节数
     * $encoding   要截取的字符串编码
     */
    function mb_strcut($str,$start=0,$strlen,$encoding='utf-8')
    {
        return mb_strcut($str,$start,$strlen,$encoding);
    }


    /**
     * 半角替换全角符号
     */

    function replace_codechars($content)
    {
        $search  = array('　','。','，','；','＇','：','“','”','•','★','！');
        $replace = array(" " ,"." ,"," ,";" ,"'",":",'','','','','!');

        $content   = str_replace($search,$replace,$content);
        return $content;
    }

    /**
     * 替换特殊符号
     */
    function replace_specialchars($content)
    {
        $search  = array('&ldquo;','&rdquo;','&middot;','&bull;','&larr;','&uarr;','&rarr;','&darr;'
                ,'&#65377;','&#65378;','&#65379;','&#65380;','&#65381;','quot;','&nbsp;','[-PAGE-]');
        $replace = array('"','"','.' ,' ',' ',' ',' ',' ','.','[',']',' ',' ',' ',' ',' ');
        $content = str_replace($search,$replace,$content);
        return $content;
    }

    /**
     * 去除html代码
     */

    function strip_html($str,$allowable_tags,$encoding='utf-8')
    {
        $str = $this->replace_specialchars($str);
        $str = $this->strip_jscript($str);
        $str = $this->strip_iframe($str);
        $str = $this->strip_meta($str);
        #$str = htmlspecialchars($str, ENT_QUOTES,$encoding);
        return strip_tags($str,$allowable_tags);
    }

    /**
     * html转换成文本
     */

    function strip_all_html($html)
    {
        $html = $this->strip_jscript($html);
        $html = $this->strip_iframe($html);
        $html = $this->strip_meta($html);
        $search = array (
                         "'<[\/\!]*?[^<>]*?>'si",           // 去掉 HTML 标记
                         "'([\r\n])[\s]+'",                 // 去掉空白字符
                         "'&(quot|#34);'i",                 // 替换 HTML 实体
                         "'&(amp|#38);'i",
                         "'&(lt|#60);'i",
                         "'&(gt|#62);'i",
                         "'&(nbsp|#160);'i",
                         "'&(iexcl|#161);'i",
                         "'&(cent|#162);'i",
                         "'&(pound|#163);'i",
                         "'&(copy|#169);'i",
                         "'&#(\d+);'e"
                         );

        $replace = array (
                          "",
                          "\\1",
                          "\"",
                          "&",
                          "<",
                          ">",
                          " ",
                          chr(161),
                          chr(162),
                          chr(163),
                          chr(169),
                          "chr(\\1)"
                            );

        $text = preg_replace ($search, $replace, $html);
        $text = strip_tags($text);
        return $text;
    }

    /**
     * 去除js代码
     */

    function strip_jscript($str)
    {
        $str = preg_replace("/<script[^>]*?>.*?<\/script>/si", "", $str);
        return $str;
    }

    /**
     * 去除iframe代码
     */

    function strip_iframe($str)
    {
        $str = preg_replace("/<iframe[^>]*?><\/iframe>/si", "", $str);
        return $str;
    }

    /**
     * 去除meta代码
     */
    function strip_meta($str)
    {
        $str = preg_replace("/<meta[^>]*?>/si", "", $str);
        return $str;
    }

    /**
     * 获取每页的显示内容 【需要mb_string扩展模块】
     * $content  所有的内容字符串
     * $page     页码，表示第几页
     * $type     截取类型，1表示按字截取，0表示按字节截取
     * $size     每页显示字数或字节数
     * $encoding      要截取的字符串编码 utf-8|gb2312|gbk|big5
     * $more        每页多截取的字数或字节数
     *
     * 返回值，数组格式
     * totalpage     总页数
     * content     根据需求截取后的内容
     */
    function get_content($content,$page=1,$size=200,$type=1,$encoding='utf-8',$more = 3)
    {
        $start = ($page-1)*$size;
        $length = $size+$more;
        if(1 == $type)
        {
            $totalstrlen = mb_strlen ($content,$encoding);
            $totalpage   = ceil($totalstrlen/$size);
            $content     = mb_substr($content,$start,$length,$encoding);
        }else{
            $totalstrlen = strlen ($content);
            $totalpage   = ceil($totalstrlen/$size);
            $content     = mb_strcut($content,$start,$length,$encoding);
        }
        return array('totalpage'=>$totalpage,'content'=>$content);
    }

    /**
     * 分页函数，CI专用
     * $url         跳转url，格式：http://passport2.cnfol.com/index.php/wap/get_content/[fpage]/
     *              [fpage]为替换页码
     * $page        页码，表示当前页的页码
     * $totalpage   总页码
     *
     * 返回值，字符串类型
     *  1、只有一页，格式为 (当前页码/总页码)
     *  2、总页数不止1页，在首页时，格式为  下一页 末页(当前页码/总页码)
     *  3、总页数不止1页，在末页时， 格式为  首页 上一页 (当前页码/总页码)
     *  4、总页数超过3页，在中间页时，格式为  首页 上一页 下一页 末页(当前页码/总页码)
     */
    function get_page($url,$totalpage,$page=1)
    {
        $rtn = "";
        if($totalpage>1)
        {
            $PrePageNumber = $page - 1;
            $NextPageNumber = $page + 1;
            if($page>1 and $totalpage>1)
            {
                $rtn .= "<a href=\"".$this->replace_page($url,1)."\">首页</a>";
                $rtn .= "<a href=\"".$this->replace_page($url,$PrePageNumber)."\"> 上一页</a>";
            }
            if($page<$totalpage and $totalpage>1)
            {
                $rtn .= "<a href=\"".$this->replace_page($url,$NextPageNumber)."\">下一页</a> ";
                $rtn .= "<a href=\"".$this->replace_page($url,$totalpage)."\">末页</a>";
            }
        }
        $rtn .= "({$page}/{$totalpage})<br />";

        return $rtn;
    }

    /**
     * 替换链接函数，CI专用
     * $url         跳转url，格式：http://passport2.cnfol.com/index.php/wap/get_content/[fpage]/
     *              [fpage]为替换页码
     * $page        页码，表示当前页的页码
     */
    function replace_page($url,$page)
    {
         return str_replace('[fpage]',$page,$url);
    }


    /**
     * 下载功能
     *
     * $filepath  完整路径加文件名
     */
    function wap_download($filepath)
    {
        if (!file_exists($filepath))
        {
            return false;
        }
        $tmpfile  = explode('/',$filepath);
        $filename = array_pop($tmpfile);#获取文件名
        $filesize = filesize($filepath);#获取文件大小
        #$filetype = mime_content_type($filepath);#获取文件类型,要 PECL 扩展库
        error_reporting(0);
        session_write_close();
        ob_end_clean();
        #header("Content-type: application/vnd.symbian.install");
        header("Content-type: application/octet-stream");
        header("Accept-Ranges: bytes");
        header("Content-Length: ".$filesize);
        header("Content-Disposition: attachment; filename={$filename}");
        header('Expires: 0');
        header('Pragma: public');
        readfile($filepath);
        flush();
        exit;
    }

    /**
     * 登录框
     * $tourl           提交路径
     * $returnurl       返回路径,即当前登录
     */
    function login_form($tourl,$returnurl)
    {
        $returnurl = urlencode($returnurl);
        $rtn = "<form action='{$tourl}' id='loginform' name='loginform' method='post'>";
        $rtn .= "<input id='username' name='username' type='text' ><br />";
        $rtn .= "<input id='password' name='password' type='password'><br />";
        $rtn .= "<input id='login' name='login' type='submit' value='登录' >";
        $rtn .= "<input id='returnurl' name='returnurl' type='hidden' value='{$returnurl}' >";
        $rtn .= "</form>";
        return $rtn;
    }

    /**
     * 取得手机号
     */

    function get_mobile()
    {
        if (isset($_SERVER['HTTP_X_NETWORK_INFO']))
        {
            $str1       = $_SERVER['HTTP_X_NETWORK_INFO'];
            $getstr1    = preg_replace('/(.*,)(11[d])(,.*)/i','',$str1);
            return $getstr1;
        }
        elseif (isset($_SERVER['HTTP_X_UP_CALLING_LINE_ID']))
        {
            $getstr2    = $_SERVER['HTTP_X_UP_CALLING_LINE_ID'];
            return $getstr2;
        }
        elseif (isset($_SERVER['HTTP_X_UP_SUBNO']))
        {
            $str3       = $_SERVER['HTTP_X_UP_SUBNO'];
            $getstr3    = preg_replace('/(.*)(11[d])(.*)/i','',$str3);
            return $getstr3;
        }
        elseif (isset($_SERVER['DEVICEID']))
        {
            return $_SERVER['DEVICEID'];
        }else{
            return false;
        }
    }

    /**
     * 判断是否手机访问
     */

    function if_mobile()
    {
        $rs     = $this->get_mobile();
        return $rs===false?false:true;
    }

    /**
     * 获取手机型号
     */
    function get_mobile_type()
    {

        if($this->get_mobile()===false)
        {
            return false;
        }
        if (isset($_SERVER['HTTP_USER_AGENT']))
        {
            $ua = $_SERVER['HTTP_USER_AGENT'];
            $str = explode(' ',$ua);
            return $str[0];
        }
        return false;
    }

    /**
     * 获取客户端IP
     */

    function get_ip()
    {
        $ip     = getenv('REMOTE_ADDR');
        $ip_    = getenv('HTTP_X_FORWARDED_FOR');
        if(($ip_ != "") && ($ip_ != "unknown"))
        {
            $ip=$ip_;
        }
        return $ip;
    }

    /**
     * 处理UBB
     * $txt          要处理的字符串
     * $special       为false则不转换img、iframe和特殊字符等。
     */

    function parse_UBB($txt, $special=true)
    {

        $txt = preg_replace( "#\{Pages_sel:(.+)\}#is", " ", $txt );
        $txt = preg_replace( "#\[font=([^\]]+)\](.+?)\[/font\]#is", "\\2", $txt );
        $txt = preg_replace( "#\[color=([^\]]+)\](.+?)\[/color\]#is", "\\2", $txt );

        // Align
        $txt = preg_replace( "#\[center\](.+?)\[/center\]#is", "\\1", $txt );
        $txt = preg_replace( "#\[left\](.+?)\[/left\]#is", "\\1", $txt );
        $txt = preg_replace( "#\[right\](.+?)\[/right\]#is", "\\1", $txt );

        // Sub & sup
        $txt = preg_replace( "#\[sup\](.+?)\[/sup\]#is", "\\1", $txt );
        $txt = preg_replace( "#\[sub\](.+?)\[/sub\]#is", "\\1", $txt );

        // email tags
        // [email]avenger@php.net[/email]   [email=avenger@php.net]Email me[/email]
        $txt = preg_replace( "#\[email\](\S+?)\[/email\]#i" , "\\1", $txt );

        $txt = preg_replace( "#\[email\s*=\s*\&quot\;([\.\w\-]+\@[\.\w\-]+\.[\.\w\-]+)\s*\&quot\;\s*\](.*?)\[\/email\]#i", "\\2", $txt );

        $txt = preg_replace( "#\[email\s*=\s*([\.\w\-]+\@[\.\w\-]+\.[\w\-]+)\s*\](.*?)\[\/email\]#i"                       , "\\2", $txt );

        // url tags
        // [url]http://www.phpe.net[/url]   [url=http://www.phpe.net]Exceed PHP![/url]
        $txt = preg_replace( "#\[url\](\S+?)\[/url\]#i", "<a href='\\1' >\\1</a>", $txt );

        $txt = preg_replace( "#\[url\s*=\s*\&quot\;\s*(\S+?)\s*\&quot\;\s*\](.*?)\[\/url\]#i" , "<a href='\\1' >\\2</a>", $txt );

        $txt = preg_replace( "#\[url\s*=\s*(\S+?)\s*\](.*?)\[\/url\]#i"                       , "<a href='\\1' >\\2</a>", $txt );


        // Start off with the easy stuff
        $txt = preg_replace( "#\[b\](.+?)\[/b\]#is", "\\1", $txt );
        $txt = preg_replace( "#\[i\](.+?)\[/i\]#is", "\\1", $txt );
        $txt = preg_replace( "#\[u\](.+?)\[/u\]#is", "\\1", $txt );
        $txt = preg_replace( "#\[s\](.+?)\[/s\]#is", "\\1", $txt );

        if ($special == false){return $txt;}

        $txt = preg_replace( "#\[img\](.+?)\[/img\]#i", "", $txt );
        $txt = preg_replace( "#\[iframe\](.+?)\[/iframe\]#i", "", $txt );
        $txt = preg_replace( "#\[quote\](.+?)\[/quote\]#i", "", $txt );
        $txt = preg_replace( "#\[code\](.+?)\[/code\]#i", "", $txt );
        $txt = preg_replace( "#\(c\)#i"     , "&copy;" , $txt );
        $txt = preg_replace( "#\(tm\)#i"    , "&#153;" , $txt );
        $txt = preg_replace( "#\(r\)#i"     , "&reg;"  , $txt );

        return $txt;
    }

    /**
     * 获取手工编辑内容
     * $id          手工编辑的模块id
     *  $charset     字符集  utf-8和gb2312。
     *  $type       类型，html代码或js代码
     *  $br         是否换行。
     */

    function get_shell($id,$charset='utf-8',$type='html',$br=0)
    {
        $Shell = file_get_contents(SHELL_URL."?id={$id}&charset={$charset}&type={$type}&br={$br}").'<br />';
        return $Shell;
    }


}

// END Cnfol_wap Class

/* End of file Cnfol_wap.php */
/* Location: ./system/libraries/Cnfol_wap.php */
