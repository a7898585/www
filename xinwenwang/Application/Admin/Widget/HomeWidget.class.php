<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Home\Widget;
use Think\Controller;
class HomeWidget extends Controller{
    public $city_id = 0;
    public function _initialize(){
      //  $this->city_id = cookie('CITY')['id'];
    }

    public function xd_index($type){
        
    }
}

