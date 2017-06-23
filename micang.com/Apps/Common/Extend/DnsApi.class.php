<?php
/**
 * DnsApi.class.php
 * Author: Jansen
 * QQ: 6206574
 * Date: 2015-12-05
 */

namespace Common\Extend;


class DnsApi{
    static $apiHost = '';
    static $apiKey = '';
    static $apiSecret = '';
    static $apiType = 'API';
    static $username = '';
    static $password = '';
    static $error = array();
    static $result = array();
    public function __construct(array $config){
        self::$apiHost = $config['API_HOST'];
        self::$apiKey = $config['API_KEY'];
        self::$username = $config['API_USER'];
        self::$password = $config['API_PWD'];
        self::$apiSecret = $config['API_SECRET'];
    }

    /**
     * 添加域名
     * @param string $domain
     * @return bool
     */
    public function domain_add($domain){
        $url = self::$apiHost.'/api/domain/create/';
        $data['domain'] = $domain;
        $result = $this->_post($url, $this->_data($data));
        if ($result['status'] == 200){
            $this->_result($result['data']);
            return true;
        }
        $this->_error($result);
        return false;
    }

    /**
     * 锁定域名
     * @param string $domain
     * @return bool
     */
    public function domain_lock($domain){
        $url = self::$apiHost.'/api/domain/lock/';
        $data['domain'] = $domain;
        $result = $this->_post($url, $this->_data($data));
        if ($result['status'] == 200)   return true;
        $this->_error($result);
        return false;
    }

    /**
     * 解锁域名
     * @param string $domain
     * @return bool
     */
    public function domain_unlock($domain){
        $url = self::$apiHost.'/api/domain/unlock/';
        $data['domain'] = $domain;
        $result = $this->_post($url, $this->_data($data));
        if ($result['status'] == 200)   return true;
        $this->_error($result);
        return false;
    }

    /**
     * 开启域名
     * @param string $domain
     * @return bool
     */
    public function domain_start($domain){
        $url = self::$apiHost.'/api/domain/start/';
        $data['domain'] = $domain;
        $result = $this->_post($url, $this->_data($data));
        if ($result['status'] == 200)   return true;
        $this->_error($result);
        return false;
    }

    /**
     * 暂停域名
     * @param string $domain
     * @return bool
     */
    public function domain_pause($domain){
        $url = self::$apiHost.'/api/domain/pause/';
        $data['domain'] = $domain;
        $result = $this->_post($url, $this->_data($data));
        if ($result['status'] == 200)   return true;
        $this->_error($result);
        return false;
    }

    /**
     * 删除域名
     * @param string $domain
     * @return bool
     */
    public function domain_delete($domain){
        $url = self::$apiHost.'/api/domain/remove/';
        $data['domain'] = $domain;
        $result = $this->_post($url, $this->_data($data));
        if ($result['status'] == 200)   return true;
        $this->_error($result);
        return false;
    }

    /**
     * 批量删除域名
     * @param array $domain
     * @return bool
     */
    public function domain_delete_batch(array $domain){
        $url = self::$apiHost.'/api/domain/batchdelete/';
        $data['domainID'] = implode(',', $domain);
        $result = $this->_post($url, $this->_data($data));
        if ($result['status'] == 200)   return true;
        $this->_error($result);
        return false;
    }

    /**
     * 域名查找
     * @param string $domain
     * @param int $page
     * @param int $size
     * @return bool|array
     */
    public function domain_query($domain, $page=1, $size=20){
        $url = self::$apiHost.'/api/domain/search/';
        $data['query'] = $domain;
        $data['page'] = $page;
        $data['pageSize'] = $size;
        $result = $this->_post($url, $this->_data($data));
        if ($result['status'] == 200)   return $result['data'];
        $this->_error($result);
        return false;
    }

    /**
     * 域名列表
     * @param int $page
     * @param int $size
     * @return bool|array
     */
    public function domain_list($page=1, $size=20){
        $url = self::$apiHost.'/api/domain/list/';
        $data['page'] = $page;
        $data['pageSize'] = $size;
        $result = $this->_post($url, $this->_data($data));
        if ($result['status'] == 200)   return $result['data'];
        $this->_error($result);
        return false;
    }

    /**
     * 域名详情
     * @param string $domain
     * @return bool|array
     */
    public function domain_detail($domain){
        $url = self::$apiHost.'/api/domain/getsingle/';
        $data['domainID'] = $domain;
        $result = $this->_post($url, $this->_data($data));
        if ($result['status'] == 200)   return $result['data'];
        $this->_error($result);
        return false;
    }

    /**
     * 域名操作日志
     * @param string $domain
     * @param int $page
     * @param int $size
     * @return bool|array
     */
    public function domain_log($domain, $page=1, $size=20){
        $url = self::$apiHost.'/api/domain/log/';
        $data['domainID'] = $domain;
        $data['page'] = $page;
        $data['pageSize'] = $size;
        $result = $this->_post($url, $this->_data($data));
        if ($result['status'] == 200)   return $result['data'];
        $this->_error($result);
        return false;
    }

    /**
     * 添加解析记录
     * @param string $domain
     * @param string $value
     * @param string $type
     * @param int $route
     * @param string $host
     * @param int $ttl
     * @param string $mx
     * @return bool
     */
    public function record_add($domain, $value, $type='A', $route=0, $host='@', $ttl=600, $mx=''){
        $url = self::$apiHost.'/api/record/create/';
        $data['domainID'] = $domain;
        $data['type'] = $type;
        $data['viewID'] = $route;
        $data['host'] = $host;
        $data['value'] = $value;
        $data['ttl'] = $ttl>0?$ttl:600;
        if (!empty($mx)) {
            $data['mx'] = $mx;
        }
        $result = $this->_post($url, $this->_data($data));
        if ($result['status'] == 200){
            $this->_result($result['data']);
            return true;
        }
        $this->_error($result);
        return false;
    }

    /**
     * 编辑解析记录
     * @param string $domain
     * @param int $record
     * @param null $value
     * @param null $type
     * @param null $route
     * @param null $host
     * @param null $ttl
     * @param null $mx
     * @return bool
     */
    public function record_edit($domain, $record, $value=null, $type=null, $route=null, $host=null, $ttl=null, $mx=null){
        $url = self::$apiHost.'/api/record/modify/';
        $data['domainID'] = $domain;
        $data['recordID'] = $record;
        if (!is_null($type)) {
            $data['newtype'] = $type;
        }
        if (!is_null($route)) {
            $data['newviewID'] = $route;
        }
        if (!is_null($host)) {
            $data['newhost'] = $host;
        }
        if (!is_null($value)) {
            $data['newvalue'] = $value;
        }
        if (!is_null($ttl)) {
            $data['newttl'] = $ttl;
        }
        if (!is_null($mx)) {
            $data['newmx'] = $mx;
        }
        $result = $this->_post($url, $this->_data($data));
        if ($result['status'] == 200)   return true;
        $this->_error($result);
        return false;
    }

    /**
     * 批量修改解析记录
     * @param array $domain
     * @param array $query 键值对,键只允许type|host|value
     * @param null $value
     * @param null $route
     * @param null $ttl
     * @param null $mx
     * @return bool
     */
    public function record_edit_batch(array $domain, array $query, $value=null, $route=null, $ttl=null, $mx=null){
        $url = self::$apiHost.'/api/record/bacthmodify/';
        $data['domainID'] = implode(',', $domain);
        if (isset($query['type'])){
            $data['searchtype'] = $query['type'];
        }elseif (isset($query['host'])){
            $data['searchhost'] = $query['host'];
        }elseif (isset($query['value'])){
            $data['searchvalue'] = $query['value'];
        }
        if (!is_null($route)) {
            $data['newviewID'] = $route;
        }
        if (!is_null($value)) {
            $data['newvalue'] = $value;
        }
        if (!is_null($ttl)) {
            $data['newttl'] = $ttl;
        }
        if (!is_null($mx)) {
            $data['newmx'] = $mx;
        }
        $result = $this->_post($url, $this->_data($data));
        if ($result['status'] == 200)   return true;
        $this->_error($result);
        return false;
    }

    /**
     * 批量修改解析记录的IP
     * @param array $domain
     * @param string $oldvalue
     * @param string $newvalue
     * @param bool $delete
     * @return bool
     */
    public function record_ip_edit_batch(array $domain, $oldvalue, $newvalue, $delete=false){
        $url = self::$apiHost.'/api/record/bacthmodify/';
        $data['domainID'] = implode(',', $domain);
        $data['searchvalue'] = $oldvalue;
        $data['newvalue'] = $newvalue;
        $data['isdelete'] = $delete?'1':'0';
        $result = $this->_post($url, $this->_data($data));
        if ($result['status'] == 200)   return true;
        $this->_error($result);
        return false;
    }

    /**
     * 删除解析记录
     * @param string $domain
     * @param int $record
     * @return bool
     */
    public function record_delete($domain, $record){
        $url = self::$apiHost.'/api/record/remove/';
        $data['domainID'] = $domain;
        $data['recordID'] = $record;
        $result = $this->_post($url, $this->_data($data));
        if ($result['status'] == 200)   return true;
        $this->_error($result);
        return false;
    }

    /**
     * 批量删除记录
     * @param string $domain
     * @param array $record
     * @return bool
     */
    public function record_delete_batch($domain, array $record){
        $url = self::$apiHost.'/api/record/batchdelete/';
        $data['domainID'] = $domain;
        $data['recordID'] = implode(',', $record);
        $result = $this->_post($url, $this->_data($data));
        if ($result['status'] == 200)   return true;
        $this->_error($result);
        return false;
    }

    /**
     * 启用解析记录
     * @param string $domain
     * @param int $record
     * @return bool
     */
    public function record_start($domain, $record){
        $url = self::$apiHost.'/api/record/start/';
        $data['domainID'] = $domain;
        $data['recordID'] = $record;
        $result = $this->_post($url, $this->_data($data));
        if ($result['status'] == 200)   return true;
        $this->_error($result);
        return false;
    }

    /**
     * 暂停解析记录
     * @param string $domain
     * @param int $record
     * @return bool
     */
    public function record_pause($domain, $record){
        $url = self::$apiHost.'/api/record/pause/';
        $data['domainID'] = $domain;
        $data['recordID'] = $record;
        $result = $this->_post($url, $this->_data($data));
        if ($result['status'] == 200)   return true;
        $this->_error($result);
        return false;
    }

    /**
     * 查找解析记录
     * @param string $domain
     * @param string $query
     * @param int $page
     * @param int $size
     * @return bool|array
     */
    public function record_query($domain, $query, $page=1, $size=20){
        $url = self::$apiHost.'/api/record/list/';
        $data['domainID'] = $domain;
        $data['query'] = $query;
        $data['page'] = $page;
        $data['pageSize'] = $size;
        $result = $this->_post($url, $this->_data($data));
        if ($result['status'] == 200)   return $result['data'];
        $this->_error($result);
        return false;
    }

    /**
     * 多条件检索解析记录
     * @param string $domain
     * @param null $host
     * @param null $value
     * @param null $type
     * @param null $route
     * @param int $page
     * @param int $size
     * @return bool|array
     */
    public function record_query_advance($domain, $host=null, $value=null, $type=null, $route=null, $page=1, $size=20){
        $url = self::$apiHost.'/api/record/search/';
        $data['domainID'] = $domain;
        if (!is_null($host)) {
            $data['host'] = $host;
        }
        if (!is_null($type)) {
            $data['type'] = $type;
        }
        if (!is_null($value)) {
            $data['value'] = $value;
        }
        if (!is_null($route)) {
            $data['viewID'] = $route;
        }
        $data['page'] = $page;
        $data['pageSize'] = $size;
        $result = $this->_post($url, $this->_data($data));
        if ($result['status'] == 200)   return $result['data'];
        $this->_error($result);
        return false;
    }

    /**
     * 解析记录列表
     * @param string $domain
     * @param int $page
     * @param int $size
     * @return bool|array
     */
    public function record_list($domain, $page=1, $size=20){
        $url = self::$apiHost.'/api/record/list/';
        $data['domainID'] = $domain;
        $data['page'] = $page;
        $data['pageSize'] = $size;
        $result = $this->_post($url, $this->_data($data));
        if ($result['status'] == 200)   return $result['data'];
        $this->_error($result);
        return false;
    }

    /**
     * 解析记录详情
     * @param string $domain
     * @param int $record
     * @return bool|array
     */
    public function record_detail($domain, $record){
        $url = self::$apiHost.'/api/record/pause/';
        $data['domainID'] = $domain;
        $data['recordID'] = $record;
        $result = $this->_post($url, $this->_data($data));
        if ($result['status'] == 200)   return $result['data'];
        $this->_error($result);
        return false;
    }
    public function get_error(){
        return self::$error;
    }
    public function get_result(){
        return self::$result;
    }

    /**
     * 生成hash值
     * @param array $param
     * @return string
     */
    private function _hash(array $param){
        ksort($param);
        $str = '';
        foreach ($param as $key => $item){
            $str .= (empty($str)?'':'&').$key.'='.$item;
        }
        return md5($str.self::$apiSecret);
    }
    private function _post($url, array $data){
        $handle = curl_init();
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($handle, CURLOPT_URL, $url);
        if(substr($url, 0, 5) == 'https'){
            curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, true);
        }
        curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($handle, CURLOPT_MAXREDIRS, 2);
        curl_setopt($handle, CURLOPT_TIMEOUT, 20);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($handle);
        if (!$response){
            return array('status'=>500, 'message'=>curl_error().'('.curl_errno().')');
        }
        $header = curl_getinfo($handle);
        if ($header['http_code'] != 200){
            return array('status'=>500, 'message'=>$header['http_code']);
        }
        $response = json_decode($response, true);
        if ($response['code'] > 0){
            return array('status'=>501, 'message'=>$response['message'].'('.$response['code'].')');
        }
        return array('status'=>200, 'data'=>$response['data']);
    }
    private function _data(array $data){
        $data['apiKey'] = self::$apiKey;
        $data['timestamp'] = time();
        $data['hash'] = $this->_hash($data);
        return $data;
    }
    private function _error(array $error){
        self::$error['status'] = $error['status'];
        self::$error['message'] = $error['message'];
    }
    private function _result(array $data){
        self::$result = $data;
    }
}