<?php

/**
 * TemplateController.class.php
 * Author: Jansen
 * QQ: 6206574
 * Date: 2015-10-07
 */

namespace Member\Controller;

use Common\Extend\PDCApi;
use Common\Extend\PageForMember;

class TemplateController extends MemberCommonController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('m_tab', 'domain');
    }

    /**
     * 国内域名模板
     */
    public function internal() {
        $this->display();
    }

    /**
     * 我的模板
     */
    public function index() {
        $total = M('MembersDomainTemplate')->where(array('mid' => session('MEMBERINFO.id')))->count();
        $templates = M('MembersDomainTemplate')->where(array('mid' => session('MEMBERINFO.id')))->order(array('id' => 'asc'))->page($p)->select();
        $this->assign('templates', $templates);
        $pager = new PageForMember($total, 10);
        $pager->url = '/template?p=' . urlencode('[PAGE]');
        $this->assign('pager', $pager->show());
        $this->display();
    }

    public function add() {
        if (IS_POST) {
            $pdcApi = new PDCApi();
            $templateId = I('post.template', null, 'intval');
            $data['title'] = I('post.title');
            $data['cn_reg_company'] = I('post.cn_reg_firstname') . I('post.cn_reg_lastname');
            $data['cn_reg_firstname'] = I('post.cn_reg_firstname');
            $data['cn_reg_lastname'] = I('post.cn_reg_lastname');
            $data['cn_reg_country'] = 'cn';
            $data['cn_reg_province'] = I('post.cn_reg_province');
            $data['cn_reg_city'] = I('post.cn_reg_city');
            $cnRegProvinceInfo = $pdcApi->getInfoByAreaId(array(I('post.cn_reg_province')));
            $data['cn_reg_province_zh'] = $cnRegProvinceInfo[I('post.cn_reg_province')]['simple'];
            $cnRegCityInfo = $pdcApi->getInfoByAreaId(array(I('post.cn_reg_city')));
            $data['cn_reg_city_zh'] = $cnRegCityInfo[I('post.cn_reg_city')]['simple'];
            $data['cn_reg_address'] = I('post.cn_reg_address');
            $data['cn_reg_postcode'] = I('post.cn_reg_postcode');
            $data['cn_reg_telephone'] = '+86.' . I('post.cn_reg_telephone');
            $data['cn_reg_fax'] = '+86.' . I('post.cn_reg_fax');
            $data['cn_reg_email'] = I('post.cn_reg_email');
            $data['cn_reg_idcard'] = I('post.cn_reg_idcard');
            $data['cn_reg_birthday'] = date('Y-m-d', strtotime(substr(I('post.cn_reg_idcard'), 6, 8)));
            $data['status'] = '1';
            $data['type'] = I('post.type', 0, 'intval');
            try {
                if ($templateId > 0) {
                    $where['id'] = $templateId;
                    $where['mid'] = session('MEMBERINFO.id');
                    $result = M('MembersDomainTemplate')->where($where)->data($data)->save();
                } else {
                    $data['mid'] = session('MEMBERINFO.id');
                    $result = M('MembersDomainTemplate')->data($data)->add();
                }
            } catch (\Exception $e) {
                $this->error('保存模板失败，请重试。');
            }
            if (!$result && $templateId == 0) {
                $this->error('保存模板失败，请重试。');
            }
            $this->success('保存模板成功。', '/template/international');
            exit();
        }
    }

    /**
     * 国际域名模板
     */
    public function international() {
        if (IS_POST) {
            $pdcApi = new PDCApi();
            $templateId = I('post.template', null, 'intval');
            $data['title'] = I('post.title');
            $data['cn_reg_company'] = I('post.cn_reg_firstname') . I('post.cn_reg_lastname');
            $data['cn_reg_firstname'] = I('post.cn_reg_firstname');
            $data['cn_reg_lastname'] = I('post.cn_reg_lastname');
            $data['cn_reg_country'] = 'cn';
            $data['cn_reg_province'] = I('post.cn_reg_province');
            $data['cn_reg_city'] = I('post.cn_reg_city');
            $cnRegProvinceInfo = $pdcApi->getInfoByAreaId(array(I('post.cn_reg_province')));
            $data['cn_reg_province_zh'] = $cnRegProvinceInfo[I('post.cn_reg_province')]['simple'];
            $cnRegCityInfo = $pdcApi->getInfoByAreaId(array(I('post.cn_reg_city')));
            $data['cn_reg_city_zh'] = $cnRegCityInfo[I('post.cn_reg_city')]['simple'];
            $data['cn_reg_address'] = I('post.cn_reg_address');
            $data['cn_reg_postcode'] = I('post.cn_reg_postcode');
            $data['cn_reg_telephone'] = '+86.' . I('post.cn_reg_telephone');
            $data['cn_reg_fax'] = '+86.' . I('post.cn_reg_fax');
            $data['cn_reg_email'] = I('post.cn_reg_email');
            $data['cn_reg_idcard'] = I('post.cn_reg_idcard');
            $data['cn_reg_birthday'] = date('Y-m-d', strtotime(substr(I('post.cn_reg_idcard'), 6, 8)));
            $data['cn_adm_company'] = I('post.cn_adm_firstname') . I('post.cn_adm_lastname');
            $data['cn_adm_firstname'] = I('post.cn_adm_firstname');
            $data['cn_adm_lastname'] = I('post.cn_adm_lastname');
            $data['cn_adm_country'] = 'cn';
            $data['cn_adm_province'] = I('post.cn_adm_province');
            $data['cn_adm_city'] = I('post.cn_adm_city');
            $cnAdmProvinceInfo = $pdcApi->getInfoByAreaId(array(I('post.cn_adm_province')));
            $data['cn_adm_province_zh'] = $cnAdmProvinceInfo[I('post.cn_adm_province')]['simple'];
            $cnAdmCityInfo = $pdcApi->getInfoByAreaId(array(I('post.cn_adm_city')));
            $data['cn_adm_city_zh'] = $cnAdmCityInfo[I('post.cn_adm_city')]['simple'];
            $data['cn_adm_address'] = I('post.cn_adm_address');
            $data['cn_adm_postcode'] = I('post.cn_adm_postcode');
            $data['cn_adm_telephone'] = '+86.' . I('post.cn_adm_telephone');
            $data['cn_adm_fax'] = '+86.' . I('post.cn_adm_fax');
            $data['cn_adm_email'] = I('post.cn_adm_email');
            $data['cn_adm_idcard'] = I('post.cn_adm_idcard');
            $data['cn_adm_birthday'] = date('Y-m-d', strtotime(substr(I('post.cn_adm_idcard'), 6, 8)));
            $data['cn_tec_company'] = I('post.cn_tec_firstname') . I('post.cn_tec_lastname');
            $data['cn_tec_firstname'] = I('post.cn_tec_firstname');
            $data['cn_tec_lastname'] = I('post.cn_tec_lastname');
            $data['cn_tec_country'] = 'cn';
            $data['cn_tec_province'] = I('post.cn_tec_province');
            $data['cn_tec_city'] = I('post.cn_tec_city');
            $cnTecProvinceInfo = $pdcApi->getInfoByAreaId(array(I('post.cn_tec_province')));
            $data['cn_tec_province_zh'] = $cnTecProvinceInfo[I('post.cn_tec_province')]['simple'];
            $cnTecCityInfo = $pdcApi->getInfoByAreaId(array(I('post.cn_tec_city')));
            $data['cn_tec_city_zh'] = $cnTecCityInfo[I('post.cn_tec_city')]['simple'];
            $data['cn_tec_address'] = I('post.cn_tec_address');
            $data['cn_tec_postcode'] = I('post.cn_tec_postcode');
            $data['cn_tec_telephone'] = '+86.' . I('post.cn_tec_telephone');
            $data['cn_tec_fax'] = '+86.' . I('post.cn_tec_fax');
            $data['cn_tec_email'] = I('post.cn_tec_email');
            $data['cn_tec_idcard'] = I('post.cn_tec_idcard');
            $data['cn_tec_birthday'] = date('Y-m-d', strtotime(substr(I('post.cn_tec_idcard'), 6, 8)));
            $data['cn_bil_company'] = I('post.cn_bil_firstname') . I('post.cn_bil_lastname');
            $data['cn_bil_firstname'] = I('post.cn_bil_firstname');
            $data['cn_bil_lastname'] = I('post.cn_bil_lastname');
            $data['cn_bil_country'] = 'cn';
            $data['cn_bil_province'] = I('post.cn_bil_province');
            $data['cn_bil_city'] = I('post.cn_bil_city');
            $cnBilProvinceInfo = $pdcApi->getInfoByAreaId(array(I('post.cn_bil_province')));
            $data['cn_bil_province_zh'] = $cnBilProvinceInfo[I('post.cn_bil_province')]['simple'];
            $cnBilCityInfo = $pdcApi->getInfoByAreaId(array(I('post.cn_bil_city')));
            $data['cn_bil_city_zh'] = $cnBilCityInfo[I('post.cn_bil_city')]['simple'];
            $data['cn_bil_address'] = I('post.cn_bil_address');
            $data['cn_bil_postcode'] = I('post.cn_bil_postcode');
            $data['cn_bil_telephone'] = '+86.' . I('post.cn_bil_telephone');
            $data['cn_bil_fax'] = '+86.' . I('post.cn_bil_fax');
            $data['cn_bil_email'] = I('post.cn_bil_email');
            $data['cn_bil_idcard'] = I('post.cn_bil_idcard');
            $data['cn_bil_birthday'] = date('Y-m-d', strtotime(substr(I('post.cn_bil_idcard'), 6, 8)));
            $data['en_reg_company'] = I('post.en_reg_lastname') . ' ' . I('post.en_reg_firstname');
            $data['en_reg_firstname'] = I('post.en_reg_firstname');
            $data['en_reg_lastname'] = I('post.en_reg_lastname');
            $data['en_reg_address'] = I('post.en_reg_address');
            $enRegProvinceInfo = $pdcApi->getInfoByAreaId(array(I('post.cn_reg_province')));
            $data['en_reg_province'] = $enRegProvinceInfo[I('post.cn_reg_province')]['cNSpellSimple'];
            $enRegCityInfo = $pdcApi->getInfoByAreaId(array(I('post.cn_reg_city')));
            $data['en_reg_city'] = $enRegCityInfo[I('post.cn_reg_city')]['cNSpellSimple'];
            $data['en_adm_company'] = I('post.en_adm_lastname') . ' ' . I('post.en_adm_firstname');
            $data['en_adm_firstname'] = I('post.en_adm_firstname');
            $data['en_adm_lastname'] = I('post.en_adm_lastname');
            $data['en_adm_address'] = I('post.en_adm_address');
            $enAdmProvinceInfo = $pdcApi->getInfoByAreaId(array(I('post.cn_adm_province')));
            $data['en_adm_province'] = $enAdmProvinceInfo[I('post.cn_adm_province')]['cNSpellSimple'];
            $enAdmCityInfo = $pdcApi->getInfoByAreaId(array(I('post.cn_adm_city')));
            $data['en_adm_city'] = $enAdmCityInfo[I('post.cn_adm_city')]['cNSpellSimple'];
            $data['en_tec_company'] = I('post.en_tec_lastname') . ' ' . I('post.en_tec_firstname');
            $data['en_tec_firstname'] = I('post.en_tec_firstname');
            $data['en_tec_lastname'] = I('post.en_tec_lastname');
            $data['en_tec_address'] = I('post.en_tec_address');
            $enTecProvinceInfo = $pdcApi->getInfoByAreaId(array(I('post.cn_tec_province')));
            $data['en_tec_province'] = $enTecProvinceInfo[I('post.cn_tec_province')]['cNSpellSimple'];
            $enTecCityInfo = $pdcApi->getInfoByAreaId(array(I('post.cn_tec_city')));
            $data['en_tec_city'] = $enTecCityInfo[I('post.cn_tec_city')]['cNSpellSimple'];
            $data['en_bil_company'] = I('post.en_bil_lastname') . ' ' . I('post.en_bil_firstname');
            $data['en_bil_firstname'] = I('post.en_bil_firstname');
            $data['en_bil_lastname'] = I('post.en_bil_lastname');
            $data['en_bil_address'] = I('post.en_bil_address');
            $enBilProvinceInfo = $pdcApi->getInfoByAreaId(array(I('post.cn_bil_province')));
            $data['en_bil_province'] = $enBilProvinceInfo[I('post.cn_bil_province')]['cNSpellSimple'];
            $enBilCityInfo = $pdcApi->getInfoByAreaId(array(I('post.cn_bil_city')));
            $data['en_bil_city'] = $enBilCityInfo[I('post.cn_bil_city')]['cNSpellSimple'];
            $data['status'] = '1';
            $data['type'] = I('post.type', 0, 'intval');
            try {
                if ($templateId > 0) {
                    $where['id'] = $templateId;
                    $where['mid'] = session('MEMBERINFO.id');
                    $result = M('MembersDomainTemplate')->where($where)->data($data)->save();
                } else {
                    $data['mid'] = session('MEMBERINFO.id');
                    $result = M('MembersDomainTemplate')->data($data)->add();
                }
            } catch (\Exception $e) {
                $this->error('保存模板失败，请重试。');
            }
            if (!$result && $templateId == 0) {
                $this->error('保存模板失败，请重试。');
            }
            $this->success('保存模板成功。', '/template/international');
            exit();
        }
        $type = I('get.type') ? I('get.type') : '0';
        $templates = M('MembersDomainTemplate')->where(array('mid' => session('MEMBERINFO.id'), 'type' => $type))->getField('id,title');
        $this->assign('templates', $templates);
        $id = I('get.id');
        $cid = I('get.cid');
        $this->assign('id', $id);
        $this->assign('cid', $cid);
        $type ? $this->display('international' . $type) : $this->display('international');
    }

    /**
     * 通过模板ID取模板内容
     * @param $id 模板ID
     */
    public function ajax_get_template_by_id($id) {
        $where['id'] = $id;
        $where['mid'] = session('MEMBERINFO.id');
        $detail = M('MembersDomainTemplate')->where($where)->find();
        if (is_array($detail)) {
            $detail['cn_reg_telephone'] = str_replace('+86.', '', $detail['cn_reg_telephone']);
            $detail['cn_reg_fax'] = str_replace('+86.', '', $detail['cn_reg_fax']);
            $detail['cn_adm_telephone'] = str_replace('+86.', '', $detail['cn_adm_telephone']);
            $detail['cn_adm_fax'] = str_replace('+86.', '', $detail['cn_adm_fax']);
            $detail['cn_tec_telephone'] = str_replace('+86.', '', $detail['cn_tec_telephone']);
            $detail['cn_tec_fax'] = str_replace('+86.', '', $detail['cn_tec_fax']);
            $detail['cn_bil_telephone'] = str_replace('+86.', '', $detail['cn_bil_telephone']);
            $detail['cn_bil_fax'] = str_replace('+86.', '', $detail['cn_bil_fax']);
        }
        $this->ajaxReturn($detail);
    }

    /**
     * 域名隐私模板
     */
    public function privacy() {
        $this->display();
    }

}