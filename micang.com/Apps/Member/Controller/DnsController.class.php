<?php
/**
 * DnsController.class.php
 * Author: Jansen
 * QQ: 6206574
 * Date: 2015-12-15
 */

namespace Member\Controller;


use Common\Extend\DnsApi;
use Common\Extend\Domain;
use Common\Extend\DomainApi;

class DnsController extends MemberCommonController{
    static $defaultDns = array('ns1.micang.com', 'ns2.micang.com');
    //解析记录
    public function record($domain){
        $domainInfo = M('MembersDomains')->where(array('domain'=>$domain))->find();
        if (!is_array($domainInfo) || empty($domainInfo)){
            $this->error('查无此域名。');
        }
        $dnsRecords = M('MembersDomainDns')->where(array('domain'=>$domain))->select();
        $this->assign('dnsRecords', $dnsRecords);
        $this->display();
    }
    //保存解析记录
    public function ajax_record_save(){
        if (trim(I('post.domain'))=='' || trim(I('post.host'))=='' || I('post.value')==''){
            $this->ajaxReturn(array('status'=>500, 'message'=>'主机头、IP地址/主机名不能为空。'));
        }
        $data['host'] = trim(I('post.host', '@'));
        $data['type'] = I('post.type', 'A');
        $data['route'] = I('post.route', '0');
        $data['value'] = I('post.value');
        if (I('post.type') === 'MX'){
            $data['mx'] = I('post.mx', 10, 'intval')>0?I('post.mx', 10, 'intval'):10;
        }
        $dnsApi = new DnsApi(C('DNS_CONFIG'));
        M('MembersDomainDns')->startTrans();
        try {
            if (I('post.id', 0, 'intval') > 0) {
                //更新数据库
                $where['id'] = I('post.id', 0, 'intval');
                $where['domain'] = trim(I('post.domain'));
                $result = M('MembersDomainDns')->where($where)->data($data)->save();
                $recordInfo = M('MembersDomainDns')->where($where)->find();
                if ($result) {
                    //同步到dns.com
                    $isSynDns = $dnsApi->record_edit($recordInfo['domain'], $recordInfo['rid'], $recordInfo['value'], $recordInfo['type'], $recordInfo['route'], $recordInfo['host'], 600, $recordInfo['mx']);
                    if (!$isSynDns) {
                        M('MembersDomainDns')->rollback();
                        $error = $dnsApi->get_error();
                        $this->ajaxReturn(array('status' => 500, 'message' => '保存失败，' . $error['message'] . '。'));
                    }
                }
            } else {
                //插入数据库
                $data['domain'] = trim(I('post.domain'));
                $recordId = M('MembersDomainDns')->data($data)->add();
                if (!$recordId){
                    $this->ajaxReturn(array('status' => 500, 'message' => '保存失败，请重试。'));
                }
                //同步到dns.com
                $domainInfo = M('MembersDomains')->where(array('domain'=>$data['domain']))->find();
                if ($domainInfo['dns_analytic'] == '0'){
                    //更新状态
                    $result = M('MembersDomains')->where(array('domain'=>$data['domain']))->data(array('dns_analytic'=>'1'))->save();
                    if (!$result){
                        M('MembersDomainDns')->rollback();
                        $this->ajaxReturn(array('status' => 500, 'message' => '保存失败，请重试。'));
                    }
                    unset($result);
                    $result = $dnsApi->domain_add($data['domain']);
                    if (!$result){
                        M('MembersDomainDns')->rollback();
                        $error = $dnsApi->get_error();
                        $this->ajaxReturn(array('status' => 500, 'message' => '保存失败，'.$error['message'].'。'));
                    }
                }
                //添加解析记录
                unset($result);
                $result = $dnsApi->record_add($data['domain'], $data['value'], $data['type'], $data['route'], $data['host'], 600, $data['mx']);
                if (!$result){
                    M('MembersDomainDns')->rollback();
                    $error = $dnsApi->get_error();
                    $this->ajaxReturn(array('status' => 500, 'message' => '保存失败，'.$error['message'].'。'));
                }
                $recordIdForDns = $dnsApi->get_result();
                M('MembersDomainDns')->where(array('id'=>$recordId))->data(array('rid'=>$recordIdForDns['recordID']))->save();
                $recordInfo = M('MembersDomainDns')->where(array('id'=>$recordId))->find();
            }
        }catch (\Exception $e){
            list($dbCode, $dbMessage) = explode(':', $e->getMessage());
            if ($dbCode == '1062') {
                $this->ajaxReturn(array('status' => 500, 'message' => '保存失败，解析记录已存在。'));
            } else {
                $this->ajaxReturn(array('status' => 500, 'message' => '保存失败，请重试。'));
            }
        }
        M('MembersDomainDns')->commit();
        $this->ajaxReturn(array('status'=>200, 'message'=>$recordInfo));
    }
    //获取解析记录
    public function ajax_get_record($id, $domain){
        $where['id'] = $id;
        $where['domain'] = $domain;
        $result = M('MembersDomainDns')->where($where)->find();
        if (!is_array($result) || empty($result)){
            $this->ajaxReturn(array('status'=>404, 'message'=>'查无此记录。'));
        }
        $this->ajaxReturn(array('status'=>200, 'message'=>$result));
    }
    //删除解析记录
    public function ajax_delete_record($id, $domain){
        $where['id'] = $id;
        $where['domain'] = $domain;
        $recordInfo = M('MembersDomainDns')->where($where)->find();
        if (!is_array($recordInfo) || empty($recordInfo)){
            $this->ajaxReturn(array('status'=>404, 'message'=>'查无此记录。'));
        }
        M('MembersDomainDns')->startTrans();
        try {
            $result = M('MembersDomainDns')->where($where)->delete();
        }catch (\Exception $e) {
            $this->ajaxReturn(array('status' => 500, 'message' => '删除失败，请重试。'));
        }
        if (!$result) {
            $this->ajaxReturn(array('status' => 500, 'message' => '删除失败，请重试。'));
        }
        $dnsApi = new DnsApi(C('DNS_CONFIG'));
        unset($result);
        $result = $dnsApi->record_delete($recordInfo['domain'], $recordInfo['rid']);
        if (!$result){
            M('MembersDomainDns')->rollback();
            $error = $dnsApi->get_error();
            $this->ajaxReturn(array('status'=>500, 'message'=>'删除失败，'.$error['message'].'。'));
        }
        M('MembersDomainDns')->commit();
        $this->ajaxReturn(array('status'=>200, 'message'=>'删除成功。'));
    }
    //域名服务器
    public function server($domain){
        $domainInfo = M('MembersDomains')->where(array('domain'=>$domain))->find();
        if (!is_array($domainInfo) || empty($domainInfo)){
            $this->error('查无此域名。');
        }
        $this->assign('domainInfo', $domainInfo);
        $this->display();
    }
    public function ajax_set_server($domain, $dns1, $dns2){
        if (!Domain::is($domain)){
            $this->ajaxReturn(array('status'=>500, 'message'=>'域名格式错误。'));
        }
        if (empty($dns1) && empty($dns2)){
            $this->ajaxReturn(array('status'=>500, 'message'=>'两个DNS不能全部为空。'));
        }
        //被修改为非默认dns
        $where['mid'] = session('MEMBERINFO.id');
        $where['domain'] = $domain;
        $domainInfo = M('MembersDomains')->where($where)->find();
        if (!is_array($domainInfo) || empty($domainInfo)){
            $this->ajaxReturn(array('status'=>404, 'message'=>'查无此域名。'));
        }
        if (!empty($dns1)) {
            $data['dns_host1'] = $dns1;
            $ns[0]['ns'] = $dns1;
        }
        if (!empty($dns2)) {
            $data['dns_host2'] = $dns2;
            $ns[1]['ns'] = $dns2;
        }
        M('MembersDomains')->startTrans();
        try {
            M('MembersDomains')->where($where)->data($data)->save();
        } catch (\Exception $e) {
            $this->ajaxReturn(array('status' => 500, 'message' => '保存失败，请重试1。'));
        }
        $domainApi = new DomainApi($domainInfo['registrar']);
        if (!$domainApi->dnsEdit($domain, $ns)){
            M('MembersDomains')->rollback();
            $error = $domainApi->getError();
            $this->ajaxReturn(array('status'=>500, 'message'=>'保存失败，请重试2。'.$error['message']));
        }
        M('MembersDomains')->commit();
        $this->ajaxReturn(array('status'=>200, 'message'=>'保存成功。'));
    }
}