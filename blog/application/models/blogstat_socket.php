<?php

/*
  --|author jiagnlw
 * add : 2014.06.04
 */

class Blogstat_socket extends MY_Model {

    function Blogstat_socket() {
        parent::MY_Model();
    }

    //添加参赛文章
    function addStatArticle($data) {
        $type = 'B390';
        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);
        if ($this->_checkrs($rs)) {
            $rs = (isset($rs['Record']) && $rs['Record'] != false) ? $rs['Record'] : array();
            if ($res['IsSucceed'] == 0) {
                $ckey = config_item('K2102');
                $ckey = str_replace('{UserID}', $data['nUserID'], $ckey);
                $ckey = str_replace('{nTimes}', '', $ckey);
                $this->cache->delete($ckey);
            }
            return $rs;
        }
        return false;
    }

    //  获取个人参赛信息
    function getInfoStat($data) {
        $type = 'B393';
        $ckey = config_item('K2102');
        $ckey = str_replace('{UserID}', $data['UserID'], $ckey);
        $ckey = str_replace('{nTimes}', $data['Times'], $ckey);

        $rs = $this->cache->get($ckey);
        if (!$rs) {
            $rs = $this->socket['newblog']->senddata($type, $data);
            $rs = xmltoarray($rs);
            if ($this->_checkrs($rs) && $rs['TtlRecords'] > 0) {
                $rs = (isset($rs['Record']) && $rs['Record'] != false) ? $rs['Record'] : array();
                $this->cache->set($ckey, $rs, EXPIRETIME_1);
                return $rs;
            }
            return false;
        }
        return $rs;
    }

    //查询列表
    function getSearchList($data) {
        $type = 'B392';
        $data['nTimes'] = isset($data['nTimes']) ? $data['nTimes'] : 1;
        $data['Title'] = isset($data['Title']) ? $data['Title'] : '';
        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);
        $getData = array();
        if ($this->_checkrs($rs, $type, $data) && $rs['TtlRecords'] > 0) {
            if ($rs['TtlRecords'] == 1) {
                $rs['Record'] = array(0 => $rs['Record']);
            }
            $getData = $rs;
            unset($rs);
            return $getData;
        }
        return false;
    }

    //获取文章排行
    function getStatList($data) {
        $type = 'B392';
        $data['nTimes'] = isset($data['nTimes']) ? $data['nTimes'] : 1;
        $data['nStart'] = isset($data['nStart']) ? $data['nStart'] : 0;
        $data['nCount'] = isset($data['nCount']) ? $data['nCount'] : 30;
        if (ISCACHE) {
            if ($data['nStart'] == -1) {
                $ckey = config_item('K2100');
                $rs = $this->cache->get($ckey);
                if (!$rs) {
                    $rs = $this->socket['newblog']->senddata($type, $data);
                    $rs = xmltoarray($rs);
//                    print_r($rs); print_r($data);
                    if ($this->_checkrs($rs, $type, $data) && $rs['TtlRecords'] > 0) {
                        $getData = array();
                        $getData['FlagCode'] = md5(time());
                        $getData['UTopCnt'] = $rs['TtlRecords'];
                        $this->cache->set($ckey, $getData, EXPIRETIME_1);
                        return $getData;
                    } else {
                        return false;
                    }
                }
                return $rs;
            } else {
                $ckey = config_item('K2101');
                $ckey = str_replace('{nTimes}', $data['nTimes'], $ckey);
                $ckey = str_replace('{PageNo}', $data['nStart'], $ckey);
                $ckey = str_replace('{PageSize}', $data['nCount'], $ckey);
                $rs = $this->cache->get($ckey);

                if (!$rs || ($data['FlagCode'] != $rs['FlagCode'])) {
                    $rs = $this->socket['newblog']->senddata($type, $data);
                    $rs = xmltoarray($rs);
//                    print_r($rs);
                    if ($this->_checkrs($rs, $type, $data) && $rs['RetRecords'] > 0) {
                        $getData['RetRecords'] = $rs['RetRecords'];
                        $getData['Record'] = $rs['Record'];
                        $getData['FlagCode'] = $data['FlagCode'];
                        $this->cache->set($ckey, $getData, EXPIRETIME_1);
                        unset($rs);
                        return $getData;
                    } else {
                        return false;
                    }
                }
                return $rs;
            }
        } else {
            $rs = $this->socket['newblog']->senddata($type, $data);
            $rs = xmltoarray($rs);

            $getData = array();
            if ($this->_checkrs($rs, $type, $data) && $rs['RetRecords'] > 0) {
                $getData = $rs;
                unset($rs);
                return $getData;
            }
            return false;
        }
    }

    //获取文章历史排行  /home/www/html/logs/blogindex/goldstat
    function getStatHisList($data) {
        $result = array();
        if (!file_exists(BLOG_INDEX_LOG . 'goldstat')) {
            x_mkdir(BLOG_INDEX_LOG . 'goldstat', 0775);
        }
        $total = $data['nCount'] + $data['nStart'];
        for ($i = $data['nStart']; $i < $total; $i++) {
            $str = '<div class="Mterm">';
            $fileurl = BLOG_INDEX_LOG . 'goldstat/statper_' . ($i + 1) . '.html';
            if (file_exists($fileurl)) {
                $strhtml = file_get_contents($fileurl);
            } else {
                $time = $this->getTimesForPer($i + 1);
                $strhtml .= '<h5>第' . ($i + 1) . '期(' . $time . '.01-' . $time . '.31)</h5>';
                $res = $this->getStatListForTime(array('nTimes' => $i + 1));
                if (!empty($res)) {
                    foreach ($res as $key => $art) {
                        $url = config_item('base_url') . '/' . $art['DomainName'] . '/article/' . strtotime($art['AppearTime']) . '-' . $art['ArticleID'] . '.html';
                        $strhtml .= '<div class="MRankTop10"><span class="MRankNum">' . ($key + 1) . '.</span>';
                        $strhtml .= '<a target="_blank" href="' . config_item('base_url') . '/' . $art['DomainName'] . '" class="MRankImg"><img src="' . getUserHead($art['UserID']) . '" title="" /></a>';
                        $strhtml .= '<a target="_blank" href="' . $url . '" class="MRankTitle">' . $art['Title'] . '</a><a href="javascript:;" class="MRankGood ready">' . $art['ClickSum'] . '</a>';
                        $strhtml .= '</div>';
                    }
                    file_put_contents($fileurl, $strhtml);
                } else {
                    $strhtml.='<div class="MRankTop10">内容为空</div>';
                }
            }
            $str .= $strhtml;
            if ($data['UserID']) {
                $param['UserID'] = $data['UserID'];
                $param['Times'] = $i + 1;
                $isownInfo = $this->getInfoStat($param);
                if (!empty($isownInfo)) {
                    if ($art['IsCheck'] == 0) {
                        $url = config_item('base_url') . '/' . $isownInfo['DomainName'] . '/article/' . strtotime($isownInfo['AppearTime']) . '-' . $isownInfo['ArticleID'] . '.html';
                        $ownstrhtml .= '<div class="MRankTop10 MyPast"><span class="MRankNum">' . $isownInfo['Rank'] . '.</span>';
                        $ownstrhtml .= '<a target="_blank" href="' . config_item('base_url') . '/' . $art['DomainName'] . '" class="MRankImg"><img src="' . getUserHead($data['UserID']) . '" title="" /></a>';
                        $ownstrhtml .= '<a target="_blank" href="' . $url . '" class="MRankTitle">' . $isownInfo['Title'] . '</a><a href="javascript:;" class="MRankGood ready">' . $isownInfo['ClickSum'] . '</a>';
                        $ownstrhtml .= '</div>';
                        $str .= $ownstrhtml;
                        unset($ownstrhtml, $isownInfo);
                    }
                }
            }
            $str .= '</div>';
            $result[$i] = $str;
            unset($str, $strhtml);
        }
//        print_r($result);
        return $result;
    }

    //根据期数查询排行列表
    function getStatListForTime($data) {
        $type = 'B392';
        $data['nTimes'] = isset($data['nTimes']) ? $data['nTimes'] : 1;
        $data['nStart'] = isset($data['nStart']) ? $data['nStart'] : 0;
        $data['nCount'] = isset($data['nCount']) ? $data['nCount'] : 10;
        $rs = $this->socket['newblog']->senddata($type, $data);
        $rs = xmltoarray($rs);

        $getData = array();
        if ($this->_checkrs($rs, $type, $data) && $rs['TtlRecords'] > 0) {
            if ($rs['TtlRecords'] == 1) {
                $rs['Record'] = array(0 => $rs['Record']);
            }
            $getData = $rs;
            unset($rs);
            return $getData['Record'];
        }
        return false;
    }

    //根据期数获取时间
    function getTimesForPer($per) {
        $num = intval(($per + config_item('statday') - 2) / 12);
        $year = $num + 2014;
        $day = $per - 12 * ($num - 1) - (12 - config_item('statday') + 1);
        return $year . '.' . sprintf("%02d", $day);
    }

}

//end class
?>