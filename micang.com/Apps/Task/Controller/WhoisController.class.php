<?php
/**
 * WhoisController.class.php
 * Author: Jansen
 * QQ: 6206574
 * Date: 2015-11-17
 */

namespace Task\Controller;

use Common\Extend\DomainApi;
class WhoisController extends TaskCommonController{
    /**
     * @todo 已经取出的记录,但因为异常中止,需要记录日志或发邮件通知管理员
     */
    public function edit(){
        $queueInfo = D('DomainWhoisQueue')->get();
        if (!is_array($queueInfo) || empty($queueInfo)) exit('empty.');
        //取对应的WHOIS信息
        $whoisInfo = M('MembersDomainTemplate')->where(array('id'=>$queueInfo['template']))->find();
        if (!is_array($whoisInfo) || empty($whoisInfo)) exit('error.1');
        //开始修改whois信息
        unset($result);
        $domainApi = new DomainApi($queueInfo['registrar']);
        $result = $domainApi->whoisEdit($queueInfo['domain'], $whoisInfo);
        if (!$result) exit('error.2');
        //修改成功,清理队列
        unset($result);
        $result = D('DomainWhoisQueue')->pop($queueInfo['id']);
        $result?exit('ok.'):exit('error.3');
    }
}