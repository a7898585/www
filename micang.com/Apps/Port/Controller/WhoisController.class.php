<?php
/**
 * WhoisController.class.php
 * Author: Jansen
 * QQ: 6206574
 * Date: 2015-12-18
 */

namespace Port\Controller;

use Common\Extend\Whois;
class WhoisController extends PortCommonController{
    final public function query($domain){
        $whois = new Whois();
        $whoisInfo = $whois->query($domain);
        $this->ajaxReturn($whoisInfo);
    }
}