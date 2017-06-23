<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Mobile\Widget;
use Think\Controller;
class MobileWidget extends Controller{
    public function _initialize(){
      //  $this->city_id = cookie('CITY')['id'];
    }

    public function nav($nav=''){
        $nav_list = D('NewsType')->getNavList();
        $this->assign('nav_list',$nav_list);

        $this->assign('nav',$nav);
        $this->display('Widget/nav');
    }
    public  function tongji(){
        $this->display('Widget:Mobile/tongji');
    }
}

