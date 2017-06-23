<?php
namespace Port\Controller;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
 class EmptyController extends PortCommonController {
    function _empty(){
//        print_r($_GET);exit;
        header("HTTP/1.0 404 Not Found");
        header('Location:/404.html');
     }

    // 404

     function index() {
        header("HTTP/1.0 404 Not Found");
        header('Location:/404.html');
     }
}

