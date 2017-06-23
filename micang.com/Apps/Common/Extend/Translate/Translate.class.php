<?php

/**
 * Translate.class.php
 * Author: Jansen
 * QQ: 6206574
 * Date: 2015-10-09
 */
namespace Common\Extend\Translate;

abstract class Translate{
    abstract public function setConfig($name, $value);
    abstract public function run($content);
    /**
     * 发送HTTP的GET请求
     * @param string $url
     * @return boolean|mixed
     */
    protected function get($url){
        $urls = parse_url($url);
        $opts = array(
            'http'=>array(
                'method' => "GET",
                'header' => "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8\r\n".
                    "Accept-Language:zh-CN,zh;q=0.8\r\n".
                    "Cache-Control:no-cache\r\n".
                    "Pragma:no-cache\r\n".
                    "Host:".$urls['host']."\r\n".
                    "User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/30.0.1599.101 Safari/537.36\r\n\r\n",
                'timeout'=>10
            )
        );
        return file_get_contents($url, false, stream_context_create($opts));
    }
    /**
     * 发送HTTP的POST请求
     * @param string $url
     * @param array|string $data
     * @return boolean|mixed
     */
    protected function post($url, $data){
        if (empty($data))   return false;
        $query = '';
        if (is_string($data)){
            $query = $data;
        } elseif (is_array($data)){
            $query = http_build_query($data);
        }
        $opts = array(
            'http'=>array(
                'method'  => "POST",
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n".
                    "Content-length:".strlen($query)."\r\n\r\n",
                'content' => $query,
                'timeout' => 10
            )
        );
        return file_get_contents($url, false, stream_context_create($opts));
    }
}