<?php

namespace Member\Widget;

use Think\Controller;

class NewsWidget extends Controller {

    public function _initialize() {
        
    }

    public function tab_list($type = '17', array $order = array('create_time' => 'DESC'), $limit = '6') {
        $data = M('News')->field('id,title')->where(array('cid' => $type))->order($order)->limit($limit)->select();
        $this->assign('data', $data);
        $this->display('Widget:News/tab_list');
    }

}

