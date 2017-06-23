<?php
namespace Mobile\Controller;

use Common\Extend\PinYin;
use Common\Model\NewsModel;
use Think\Log;

class IndexController extends MobileCommonController {
    public function _initialize() {
        parent::_initialize();
    }
    final public function index(){
        $nav_list = D('NewsType')->getNavList();
        $this->assign('nav_list',$nav_list);

        $page = max(I('request.p'),1);
        $where = array(
            'is_show'=>'1'
        );
        $model = new NewsModel();
        $data = $model->getWhereList($where,$page,20,'update_time desc');
        if(IS_POST){
            $this->ajaxReturn(array('code'=>200,'data'=>$data['data_list']));
        }
        $this->assign('list',$data['data_list']);
        $this->assign('total',$data['total']);
        $this->display();
    }

}