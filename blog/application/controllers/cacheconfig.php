<?php

/* * **********************
 * 功能：   个人博客设置
 * author： leicc
 * add：    2010-11-16
 * modify   2010-11-16
 * *********************** */

class Cacheconfig extends MY_Controller {

    var $socket;
    var $cache;
    var $expire;

    function Cacheconfig() {
        parent::MY_Controller();
        $this->cache = &load_class('Memcache');
        $this->cache->addServer();
    }

    /**
     * @ 个人博客配置的修改
     * */
    function Edit($domainname) {
        $this->_checkLogin();
        //通过博客名获取博客信息	
        $data['bloginfo'] = $this->_getBlogInfoByDomain($domainname);

        //验证权限
        $this->_checkUser($data['bloginfo']['UserID']);
        $param['GroupIDs'] = trim($data['bloginfo']['GroupID'], ',');
        $blogaccess = $this->memberblog_socket->getAccessList($param);
        $this->_checkAccess($blogaccess, 'ConfigShow');

        //创建点击统计url
        $data['viewurl'] = $this->_getviewURL($data['bloginfo'], true);

        //获取个人博客列表
        $data['bloglist'] = $this->_getBlogListByUid();
        //获取博客配置信息
        $data['blogconfig'] = $this->_getBlogConfig($data['bloginfo']['MemberID']);

        $data['user'] = $this->user;

        $blocks = &$this->config->item('block');
        $data['block'] = $blocks['configcache'];
        $data['title'] = $data['bloginfo']['BlogName'] . '-' . $blocks['configcachetitle'];
        $data['peronalhead'] = $blocks['personalhead'];
        $data['peronalfoot'] = $blocks['personalfoot'];
        $data['baseurl'] = &config_item('base_url');
        $data['isconfig'] = 1;
        $this->load->view('manage/manage_index.shtml', $data);
    }

    /**
     * @ 个人博客的配置
     *   修改提交动作
     * */
    function Action($domainname) {
        $this->_checkLogin();

        $param['MemberID'] = $this->input->post('MemberID');
        $param['DomainName'] = $this->input->post('DomainName');
        $param['flashCode'] = $this->input->post('flashCode');
        $param['CacheAct'] = $this->input->post('CacheAct');
        $param['ArticleID'] = $this->input->post('ArticleID');

        $data = array();

        if ($param['flashCode'] != getVerifyStr($param['MemberID'] . $this->user['userid'] . $domainname)) {
            $data['error'] = "传递参数信息不合法！";
            $data['errno'] = "verify";
        }

        if (empty($param['ArticleID']) && $param['CacheAct'] == 'articleinfo') {
            $data['error'] = "传递参数信息不合法！";
            $data['errno'] = "ArticleID";
        }

        if (!empty($data)) {
            echo json_encode($data);
            exit(-1);
        }
        unset($data);

        $flag = $this->_clearCache($param);
        if ($flag === 0) {
            unset($data);
            $data['error'] = "清除缓存失败! ";
            $data['errno'] = "clearCacheError";
            echo json_encode($data);
            exit(-1);
        }
        unset($data);
        $data['error'] = "清除缓存成功";
        $data['errno'] = "success";

        echo json_encode($data);
    }

    //清除缓存
    private function _clearCache($data) {

        $flag = 0;
        $data['SelfRecommend'] = isset($data['SelfRecommend']) ? $data['SelfRecommend'] : -1;

        switch ($data['CacheAct']) {
            case 'articlelist':
                $StartDate = isset($data['StartDate']) ? date("Ymd", strtotime($data['StartDate'])) : '';
                $EndDate = isset($data['EndDate']) ? date("Ymd", strtotime($data['EndDate'])) : '';
                $ckey = config_item('K1015');
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $ckey = str_replace('{StartDate}', $StartDate, $ckey);
                $ckey = str_replace('{EndDate}', $EndDate, $ckey);

                $ismul = isset($data['IsMultimedia']) ? $data['IsMultimedia'] : '0';
                $ckey = str_replace('{ismul}', $ismul, $ckey);

                $flag = $this->cache->delete($ckey);

                $ckey = config_item('K1040');
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $ckey = str_replace('{SelfRecommend}', $data['SelfRecommend'], $ckey);
                $flag = $this->cache->delete($ckey);
                break;

            case 'articleinfo':
                $ckey = config_item('K1014');
                $ckey = str_replace('{ArticleID}', $data['ArticleID'], $ckey);
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $flag = $this->cache->delete($ckey);

                $ckey = config_item('K1017');
                $ckey = str_replace('{ArticleID}', $data['ArticleID'], $ckey);
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $flag = $this->cache->delete($ckey);

                $StartDate = isset($data['StartDate']) ? date("Ymd", strtotime($data['StartDate'])) : '';
                $EndDate = isset($data['EndDate']) ? date("Ymd", strtotime($data['EndDate'])) : '';
                $ckey = config_item('K1015');
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $ckey = str_replace('{StartDate}', $StartDate, $ckey);
                $ckey = str_replace('{EndDate}', $EndDate, $ckey);

                $ismul = isset($data['IsMultimedia']) ? $data['IsMultimedia'] : '0';
                $ckey = str_replace('{ismul}', $ismul, $ckey);
                $flag = $this->cache->delete($ckey);
                break;

            case 'affiche':
                $ckey = config_item('K1012');
                $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
                $flag = $this->cache->delete($ckey);
                break;

            case 'blogconfig':

                break;

            case 'articlecomment':

                break;
            case 'bloglink';

                break;
        }
        return $flag;
    }

    //验证cache信息
    private function _checkcache($rs, $c_key) {
        if ($rs == "x" || $rs == "a" || isset($rs['info'])) {
            $filename = date('Ym') . 'errordbcache.log';
            log_write('dbcache->get返回值错误：' . $rs . ",key:" . $c_key . "\n", $filename, __METHOD__);
            return false;
        }
        return true;
    }

    //清空列表
    private function _dellistkey($id, $type) {
        $dbcache = &load_class('Dbcache');
        $dbcache->addServer();
        $cachekey = config_item('cachekey');

        if ($type == 'article') {
            $pagekey = str_replace('{MemberID}', $id, $cachekey['1013']);
            $rs = $dbcache->get($pagekey);

            if ($rs && $this->_checkcache($rs, $pagekey)) {
                foreach ($rs as $r) {
                    $dbcache->delete($r);
                }
            }

            $c_key = str_replace('{MemberID}', $id, $cachekey['1015']);
            $dbcache->delete($c_key);
        } elseif ($type == 'comment') {
            $pagekey = str_replace('{ArticleID}', $id, $cachekey['1029']);
            $rs = $dbcache->get($pagekey);

            if ($rs && $this->_checkcache($rs, $pagekey)) {
                foreach ($rs as $r) {
                    $dbcache->delete($r);
                }
            }

            $c_key = str_replace('{ArticleID}', $id, $cachekey['1027']);
            $dbcache->delete($c_key);
        }
    }

}

//end class
?>