<?php

namespace Port\Controller;

use Port\Model\DingyueModel;
use Port\Model\NewsModel;
use Think\Log;

class DingyueController extends PortCommonController {

    public function _initialize() {
        parent::_initialize();
    }

    /**
     * 订阅显示列表
     */
    final public function lists() {

        $list = D('Dingyue')->getShowList();

        if ($this->uid) {
            $ids = M('UsersDingyue')->where(array('uid' => $this->uid))->getField('sid', true);
            $ids = implode(',', $ids) . ',';
            foreach ($list['data_list'] as &$item) {

                foreach ($item as &$temp) {
                    if (strstr($ids, $temp['id'] . ',')) {
                        $temp['is_checked'] = 1;
                    }
                }
            }
        }

        responseString(1, $list, '');
    }

    /**
     * 当前订阅下分类
     */
    final public function news_list() {
        $page = $this->responce['page']; //默认1
        $limit = $this->responce['limit']; //默认20
        $dingyue_id = $this->responce['dingyue_id'];

        $dm = new NewsModel();
        $list = $dm->getWhereList(array('dingyue_id' => $dingyue_id), $page, $limit);
        responseString('1', $list, '');
    }

    /**
     * 用户订阅显示的列表
     */
    final public function my_list() {
        $data_list = array();
        if ($this->uid) {
            $data_list = D('UsersDingyue')->getList($this->uid);
        }
        responseString(1, $data_list, '');
    }

    /**
     * 订阅
     */
    final public function add() {
        if (!$this->responce['id'] || !$this->uid) {
            responseString('0', array(), '参数错误');
        }
        $data = array(
            'sid' => $this->responce['id'],
            'uid' => $this->uid,
            'add_time' => time()
        );
        $temp = M('UsersDingyue')->data($data)->add();
        if ($temp) {
            M('UsersDingyue')->where(array('id' => $temp))->save(array('sort_id' => $temp));
        }
        responseString(1, array(), '订阅成功' . $this->responce['id']);
    }

    final public function del() {
        if (empty($this->responce['id'])) {
            responseString('0', array(), '参数错误');
        }
        $where = array(
            'sid' => $this->responce['id'],
            'uid' => $this->uid
        );
        M('UsersDingyue')->where($where)->delete();
        responseString(1, array(), '取消订阅成功');
    }

}

