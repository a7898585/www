<?php
/**
 * 域名相关公共方法
 * Author: Jansen
 * QQ: 6206574
 * Date: 2015-12-06
 */

namespace Common\Extend;


class Domain{
    /**
     * 检测域名是否合法格式
     * @param string $domain
     * @return bool
     */
    final public static function is($domain){
        $domain = self::toLower($domain);
        //没有后缀的情况
        if (!self::getSuffix($domain))  return false;
        $domain = self::encode($domain);
        //判断转码后的域名是否包含特殊字符
        if (preg_match('/^[a-z0-9\-\.]+$/', $domain) == 0)  return false;
        //判断长度
        $name = self::getDomainName($domain);
        $name = (substr($name, 0, 4)=='xn--')?substr($name, 4):$name;
        if (strlen($name) > 63) return false;
        return true;
    }

    /**
     * 是否中文域名,包括名称为中文或后缀为中文或两者都有
     * @param string $domain
     * @return bool
     */
    final public static function isChinese($domain){
        $domain = self::toLower($domain);
        return (self::isChineseName($domain) || self::isChineseSuffix($domain))?true:false;
    }

    /**
     * 域名名称是否为中文
     * @param string $domain
     * @return bool
     */
    final public static function isChineseName($domain){
        $domain = self::toLower($domain);
        $name = substr($domain, 0, strpos($domain, '.'));
        return (strlen($name)==mb_strlen($name, 'UTF-8'))?false:true;
    }

    /**
     * 后缀是否中文
     * @param string $domain
     * @return bool
     */
    final public static function isChineseSuffix($domain){
        $domain = self::toLower($domain);
        $suffix = self::getSuffix($domain);
        return (preg_match('/^\.[a-z\.]+$/', $suffix))?false:true;
    }

    /**
     * 取完整的后缀,如果是中文域名,则后缀前带idn三个字符
     * @param string $domain
     * @return string
     */
    final public static function getFullSuffix($domain){
        $domain = self::toLower($domain);
        $suffix = self::getSuffix($domain);
        return (self::isChineseName($domain)?'idn':'').$suffix;
    }

    /**
     * 不区分中文取后缀
     * @param string $domain
     * @return string
     */
    final public static function getSuffix($domain){
        $domain = self::toLower($domain);
        return substr($domain, strpos($domain, '.'));
    }

    /**
     * 取域名的名称
     * @param string $domain
     * @return string
     */
    final public static function getDomainName($domain){
        $domain = self::toLower($domain);
        return substr($domain, 0, strpos($domain, '.'));
    }

    /**
     * 取域名名称长度,以字符为单位计算
     * @param string $domain
     * @return int
     */
    final public static function getDomainNameLengthForChar($domain){
        return strlen(self::getDomainName($domain));
    }

    /**
     * 取域名名称长度,以字为单位计算
     * @param string $domain
     * @return int
     */
    final public static function getDomainNameLengthForWord($domain){
        return mb_strlen(self::getDomainName($domain), 'UTF-8');
    }

    /**
     * 转换域名为小写
     * @param string $domain
     * return string
     */
    final public static function toLower($domain){
        return strtolower($domain);
    }

    /**
     * 转换中文域名为punycode编码
     * @param string $domain
     * @return string
     */
    final public static function encode($domain){
        $domain = self::toLower($domain);
        return idn_to_ascii($domain);
    }

    /**
     * 将中文域名的punycode为中文文字
     * @param string $domain
     * @return string
     */
    final public static function decode($domain){
        $domain = self::toLower($domain);
        return idn_to_utf8($domain);
    }

    /**
     * 域名分析
     * @param string $domain
     */
    final public static function parse($domain){

    }
}