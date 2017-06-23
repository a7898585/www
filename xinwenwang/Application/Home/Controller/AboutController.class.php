<?php
namespace Home\Controller;
use Think\Controller;
class AboutController extends HomeCommonController {
    public function _initialize(){
        
    }
    final public function index(){
        $this->display();
    }

    /**
     * 联系我们
     */
    final public function contact(){
        $this->display();
    }

    /**
     * 信贷员公约
     */
    final public function xdygy(){
        $this->display();
    }

    /**
     * 使用条款与声明
     */
    final public function statement(){
        $this->display();
    }

    /**
     * 投诉建议
     */
    final public function suggest(){
        $this->display();
    }
}