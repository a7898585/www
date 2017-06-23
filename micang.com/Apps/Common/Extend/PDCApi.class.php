<?php
namespace Common\Extend;
final class PDCApi {
    const URL_HOST = 'http://pdc.258.com';
    const URL_AREA_CHILDREN = '/area/view/getchildren';
    const URL_AREA_INFO = '/area/view/getinfo';
    /**
     * 缓存时间
     * @var number
     */
    const CACHE_TIME = 86400;
    /**
     * 取省份
     * @return array
     */
    public static function province(){
        return self::area('001');
    }
    /**
     * 取指定省份下的城市
     * @param string $provinceId
     * @return array
     */
    public static function city($provinceId){
        return self::area($provinceId);
    }
    /**
     * 取指定城市下的县区
     * @param string $cityId
     * @return array
     */
    public static function county($cityId){
        return self::area($cityId);
    }
    public static function getSimpleByAreaId($areaId){
        $datas = self::area($areaId);
        foreach ($datas as $k => $v){
            $result[$k] = $v['simple'];
        }
        return $result;
    }
    public static function getInfoByAreaId(array $areaIds){
        $result = array();
        //循环所有ID，筛选出没有缓存的ID
        foreach ($areaIds as $areaId){
            $cache = S('PDC_DATA_AREA_INFO_'.$areaId);
            if (is_array($cache))   $result[$areaId] = $cache;
            else $tAreaIds[] = $areaId;
        }
        if (is_array($tAreaIds)){
            //组装没有缓存的ID，查询结果
            $query = array('areaids'=>implode(',', $tAreaIds));
            $url = self::URL_HOST.self::URL_AREA_INFO.'?'.http_build_query($query);
            $response = self::get($url);
            if (is_array($response)){
                foreach ($response as $key => $item){
                    $result[$key] = $item;
                    //$result[$key]['fullPath'] = explode('|--|', $item['fullPath']);
                    //array_shift($result[$key]['fullPath']);
                    S('PDC_DATA_AREA_INFO_'.$key, $result[$key], self::CACHE_TIME);
                }
            }
        }
        return $result;
    }
    private static function area($areaId='001'){
        $result = S('PDC_DATA_AREA_CHILDREN_'.$areaId);
        if (!is_array($result)){
            $query = array('areaid'=>$areaId);
            $url = self::URL_HOST.self::URL_AREA_CHILDREN.'?'.http_build_query($query);
            $response = self::get($url);
            foreach ($response as $k => $v){
                $result[$v['areaID']] = $v;
            }
            S('PDC_DATA_AREA_CHILDREN_'.$areaId, $result, self::CACHE_TIME);
        }
        return $result;
    }
    /**
     * 发送HTTP的GET请求
     * @param string $url
     * @return boolean|mixed
     */
    private static function get($url){
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
        $result = file_get_contents($url, false, stream_context_create($opts));
        if (!$result)   return false;
        return json_decode($result, true);
    }
    /**
     * 发送HTTP的POST请求
     * @param string $url
     * @param array|string $data
     * @return boolean|mixed
     */
    private static function post($url, $data){
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
        $result = file_get_contents($url, false, stream_context_create($opts));
        if (!$result)   return false;
        return json_decode($result, true);
    }
}

?>