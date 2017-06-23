<?php

namespace Home\Controller;

class SearchController extends HomeCommonController {

    final public function xiaofei() {
        $city = cookie('CITY');
        $this->assign('member_company', getMemberCompany());
        $this->assign('member_hashouse', getMemberHashouse());
        $this->assign('member_creditlog', getMemberCreditlog());
        $this->assign('daikuan_assuretype', getDaikuanAssureType());
        $this->assign('jingli_bank_type', getJingliBanktype());
        $this->assign('daikuan_refundtype', getDaikuanRefundType());
        $t = I('get.t');
        $del = I('get.del');
        if ($del) {
            $t = str_replace("-$del", '', $t);
        }
        $params = $this->buildSearchParams($t, I('get.total_interest'), I('get.month_repay'));
        $this->assign('where', $params['where']);
        unset($params['serach_list'][14]);
        unset($params['serach_list'][16]);
        $this->assign('serach_list', $params['serach_list']);
        $this->assign('urls', $params['url']);
        $order = 'add_time desc';
        if (I('get.total_interest')) {
            $order = 'year_rate';
        }
        if (I('get.month_repay')) {
            $order = 'month_manage';
        }
        $params['where']['page'] = 1;
        $data = D('BankProducts')->getList('xiaofei', $params['where'], 10, $order, false);
        $this->assign('data', $data);
        $this->assign('xd_money', getXiaofeiMoney());
        $this->assign('xd_month', getXiaofeiMonth());
        //定义位置
        $position = array(
            array('name' => $city['name_simple'] . '消费贷款', 'link' => '/xiaofei'),
            array('name' => $city['name_simple'] . '消费贷款' . $params['where']['money'] . '万' . $params['where']['month'] . '月期', 'link' => '链接'),
        );
        $seo = array();
        $seo['t'] = $city['name_full'] . "消费贷款" . $params['where']['money'] . "万元" . $params['where']['month'] . "个月_消费贷款搜索平台-万贷好网";
        $seo['k'] = $city['name_full'] . "消费贷款" . $params['where']['money'] . "万元,贷款期限" . $params['where']['month'] . "个月,,装修贷款,助学贷款,无抵押消费贷款,消费贷款产品推荐,消费贷款搜索平台";
        $seo['d'] = "万贷好网" . $city['name_full'] . "消费贷款栏目提供专业消费贷款搜索平台，为您提供各大银行、小贷公司、担保公司等信贷机构的" . $city['name_full'] . "消费贷款" . $params['where']['money'] . "万元,贷款期限" . $params['where']['month'] . "年,普通企业没信用记录的贷款产品，帮您快速找到比较划算的" . $city['name_full'] . "消费贷款产品。免费在线申请" . $city['name_full'] . "消费贷款后，将会获得信贷经理的一对一服务，解决个人消费贷款资金不足的问题。";
        $this->assign("seo", $seo);
        $this->assign('position', $position);
        $this->display();
    }

    final public function qiye() {
        $city = cookie("CITY");
        $this->assign('member_company', getMemberCompany());
        $this->assign('member_hashouse', getMemberHashouse());
        $this->assign('member_creditlog', getMemberCreditlog());
        $this->assign('daikuan_assuretype', getDaikuanAssureType());
        $this->assign('jingli_bank_type', getJingliBanktype());
        $this->assign('daikuan_refundtype', getDaikuanRefundType());
        $t = I('get.t');
        $del = I('get.del');
        if ($del) {
            $t = str_replace("-$del", '', $t);
        }
        $params = $this->buildSearchParams($t, I('get.total_interest'), I('get.month_repay'));
        $this->assign('where', $params['where']);
        unset($params['serach_list'][14]);
        unset($params['serach_list'][16]);
        $this->assign('serach_list', $params['serach_list']);
        $this->assign('urls', $params['url']);
        $order = 'add_time desc';
        if (I('get.total_interest')) {
            $order = 'year_rate asc';
        }
        if (I('get.month_repay')) {
            $order = 'month_manage asc';
        }
        $params['where']['page'] = 1;
        $data = D('BankProducts')->getList('qiye', $params['where'], 10, $order, false);
        $this->assign('data', $data);
        $this->assign('xd_money', getQiYeMoney());
        $this->assign('xd_month', getQiYeMonth());
        //定义位置
        $position = array(
            array('name' => $city['name_simple'] . '企业贷款', 'link' => '/qiye'),
            array('name' => $city['name_simple'] . '企业贷款' . $params['where']['money'] . '万' . $params['where']['month'] . '月期', 'link' => '链接'),
        );
        $this->assign('position', $position);
        /*         * ***********SEO************* */
        $seo['t'] = $city['name_full'] . "企业贷款" . $params['where']['money'] . "万元" . $params['where']['month'] . "月_企业贷款搜索平台-万贷好网";
        $seo['k'] = $city['name_full'] . "企业贷款" . $params['where']['money'] . "万元,贷款期限" . $params['where']['month'] . "月,,装修贷款,助学贷款,无抵押消费贷款,消费贷款产品推荐,消费贷款搜索平台";
        $seo['d'] = "万贷好网" . $city['name_full'] . "消费贷款栏目提供专业消费贷款搜索平台，为您提供各大银行、小贷公司、担保公司等信贷机构的" . $city['name_full'] . "消费贷款" . $params['where']['money'] . "万元,贷款期限" . $params['where']['month'] . "年,普通企业没信用记录的贷款产品，帮您快速找到比较划算的" . $city['name_full'] . "消费贷款产品。免费在线申请" . $city['name_full'] . "消费贷款后，将会获得信贷经理的一对一服务，解决个人消费贷款资金不足的问题。";
        $this->assign("seo", $seo);

        /*         * ***********SEO************* */
        $this->display();
    }

    final public function maiche() {
        $city = cookie('CITY');
        $this->assign('maiche_shoufu', getMaicheFirst_pay());
        $this->assign('member_hashouse', getMemberHashouse());
        $this->assign('xd_month', getMaicheMonth());
        $this->assign('jingli_bank_type', getJingliBanktype());
        $this->assign('maiche_carnumber', getMaicheCarNumber());
        $this->assign('maiche_caruse', getMaicheCarUse());
        $this->assign('maiche_cartype', getMaicheHascar());
        $this->assign('xd_money', getMaicheMoney());
        $t = I('get.t');
        $del = I('get.del');
        if ($del) {
            $t = str_replace("-$del", '', $t);
        }
        $params = $this->buildSearchParams($t, I('get.total_interest'), I('get.month_repay'));

        unset($params['serach_list'][9]);
        $this->assign('where', $params['where']);

        $this->assign('serach_list', $params['serach_list']);
        $this->assign('urls', $params['url']);
        $order = 'add_time desc';
        if (I('get.total_interest')) {
            $order = 'year_rate asc';
        }
        if (I('get.month_repay')) {
            $order = 'month_manage asc';
        }
        $params['where']['page'] = 1;
        $data = D('BankProducts')->getList('maiche', $params['where'], 10, $order, false);
        $this->assign('data', $data);
        //定义位置
        $position = array(
            array('name' => $city['name_simple'] . '买车贷款', 'link' => '/maiche'),
            array('name' => $city['name_simple'] . '买车贷款' . $params['where']['money'] . '万' . $params['where']['month'] . '月期', 'link' => '链接'),
        );
        $this->assign('position', $position);
        /*         * ***********SEO************* */
        $seo['t'] = $city['name_full'] . "买车贷款" . $params['where']['money'] . "万元" . $params['where']['month'] . "月_消费贷款搜索平台-万贷好网";
        $seo['k'] = $city['name_full'] . "买车贷款" . $params['where']['money'] . "万元,贷款期限" . $params['where']['month'] . "月,,装修贷款,助学贷款,无抵押消费贷款,消费贷款产品推荐,消费贷款搜索平台";
        $seo['d'] = "万贷好网" . $city['name_full'] . "消费贷款栏目提供专业消费贷款搜索平台，为您提供各大银行、小贷公司、担保公司等信贷机构的" . $city['name_full'] . "消费贷款" . $params['where']['money'] . "万元,贷款期限" . $params['where']['month'] . "年,普通企业没信用记录的贷款产品，帮您快速找到比较划算的" . $city['name_full'] . "消费贷款产品。免费在线申请" . $city['name_full'] . "消费贷款后，将会获得信贷经理的一对一服务，解决个人消费贷款资金不足的问题。";
        $this->assign("seo", $seo);
        /*         * ***********SEO************* */
        $this->display();
    }

    final public function maifang() {
        $city = cookie("CITY");
        $this->assign('xd_money', getMaifangMoney());
        $this->assign('xd_month', getMaifangMonth());
        $this->assign('member_hashouse', getMemberHashouse());
        $this->assign('maifang_suite', getMaifangFitsrSuite());
        $this->assign('maifang_house', getMaifangSecondHandHouse());
        $t = I('get.t');
        $del = I('get.del');
        if ($del) {
            $t = str_replace("-$del", '', $t);
        }
        $params = $this->buildSearchParams($t, I('get.total_interest'), I('get.month_repay'));
        $this->assign('where', $params['where']);
        $this->assign('serach_list', $params['serach_list']);
        $this->assign('urls', $params['url']);
        $order = 'add_time desc';
        if (I('get.total_interest')) {
            $order = 'year_rate asc';
        }
        if (I('get.month_repay')) {
            $order = 'month_manage asc';
        }
        $params['where']['page'] = 1;
        $data = D('BankProducts')->getList('maifang', $params['where'], 10, $order, false);
        $this->assign('data', $data);
        //定义位置

        $position = array(
            array('name' => $city['name_simple'] . '买房贷款', 'link' => '/maifang'),
            array('name' => $city['name_simple'] . '买房贷款' . $params['where']['money'] . '万' . $params['where']['month'] . '月期', 'link' => '链接'),
        );
        /*         * ***********SEO************* */
        $seo['t'] = "{$city['name_full']}买房贷款" . $params['where']['money'] . "万元" . $params['where']['month'] . "月_消费贷款搜索平台-万贷好网";
        $seo['k'] = "{$city['name_full']}买房贷款" . $params['where']['money'] . "万元,贷款期限" . $params['where']['month'] . "月,,装修贷款,助学贷款,无抵押消费贷款,消费贷款产品推荐,消费贷款搜索平台";
        $seo['d'] = "万贷好网{$city['name_full']}消费贷款栏目提供专业消费贷款搜索平台，为您提供各大银行、小贷公司、担保公司等信贷机构的{$city['name_full']}消费贷款12万元,贷款期限1年,普通企业没信用记录的贷款产品，帮您快速找到比较划算的{$city['name_full']}消费贷款产品。免费在线申请{$city['name_full']}消费贷款后，将会获得信贷经理的一对一服务，解决个人消费贷款资金不足的问题。";
        $this->assign("seo", $seo);
        /*         * ***********SEO************* */
        $this->assign('position', $position);
        $this->display();
    }

    final public function info() {
        //定义位置
        $position = array(
        );
        $city = cookie('CITY');
        $temp = explode('x', I('get.t'));
        $id = $temp[0];
        $params['money'] = $temp[1];
        $params['month'] = $temp[2];
        $this->assign('params', $params);
        $info = D('BankProducts')->info($id, $params);
        $this->assign('info', $info);
        $this->assign('xd_type_name', getDaikuanXdType($info['xd_type']));
        $xd_type = '';
        switch ($info['xd_type']) {
            case 1:
                $xd_type = 'qiye';
                $type = 'qy';
                $position[] = array('name' => '企业贷款', 'link' => '/qiye');
                break;
            case 3:
                $xd_type = 'maifang';
                $type = 'mf';
                $position[] = array('name' => '买房贷款', 'link' => '/maifang');
                break;
            case 2:
                $xd_type = 'maiche';
                $type = 'mc';
                $position[] = array('name' => '买车贷款', 'link' => '/maiche');
                break;
            case 4:
                $xd_type = 'xiaofei';
                $type = 'xf';
                $position[] = array('name' => '消费贷款', 'link' => '/xiaofei');
                break;
        }
        $position[] = array('name' => $info['bank']['name'], 'link' => '');
        $position[] = array('name' => $info['name'], 'link' => '');
        $this->assign('position', $position);
        $this->assign('xd_type', $xd_type);
        $this->assign('type', $type);
        $params['info_id'] = $info['id'];
        $pro_list = D('BankProducts')->getList($xd_type, $params, 3, $order = 'add_time desc');
        $this->assign('pro_list', $pro_list);
        $xindai = D('MemberXindai')->getListByBankID();
        $this->assign('xindai', $xindai);
        $this->assign('xd_money', getXiaofeiMoney());
        $this->assign('xd_month', getXiaofeiMonth());
        /*         * ***********SEO************* */
        $seo = array();
        $seo['t'] = $info['name'] . "-" . $info['bank']['name'];
        $seo['k'] = "";
        $seo['d'] = "";
        $this->assign('seo', $seo);
        /*         * ***********SEO************* */
        $this->display();
    }

    private function buildSearchParams($t, $buildSearchParams, $month_repay) {
        $urls = array(); //链接 full_url 完整链接  f_url 去除机构类型前部分链接  last 去除机构后部分链接
        $where = array(); //查询字段数组
        $params = self::analyzeUrlToArray($t, $buildSearchParams, $month_repay);
        $xd_type = $params['xd_type'];
        $where['money'] = $params['money'];
        $where['month'] = $params['month'];
        $where['bank_type'] = $params['bank_type'];
        $where['first_pay'] = $params['first_pay'];
        $urls['f_url'] = $xd_type . "-" . $params['money'] . "x" . $params['month']; //完整链接
        $urls['full_url'] = $urls['f_url'] . "-0x0x" . $params['bank_type']; //前部分链接 用来解决前端有构造机构的问题
        $serach_list = array();

        foreach ($params['urls'] as $key => $item) {
            $temp1 = self::getUrlName($xd_type, $key, $item['val']);
            $item['name'] = $temp1['name'];
            $serach_list[$key] = $item;
            $where[$temp1['where_key']] = $item['val'];
            $urls['l_url'].="-" . $item['url'];
            $urls['full_url'].="-" . $item['url'];
        }
        return array('where' => $where, 'serach_list' => $serach_list, 'url' => $urls);
    }

    /**
     * 根据传输过来的值解析
     * @param $t
     * @param $buildSearchParams
     * @param $month_repay
     * @return array
     */
    private function analyzeUrlToArray($t, $buildSearchParams, $month_repay) {
        $params = array();
        $urls = explode('-', $t);
        $params['xd_type'] = $urls[0];
        $temp = explode('x', $urls[1]);
        $params['money'] = $temp[0];
        $params['month'] = $temp[1];
        $params['first_pay'] = $temp[2];
        $temp = explode('x', $urls[2]);
        $params['bank_type'] = $temp[2];
        unset($urls[0], $urls[1], $urls[2]);
        $data_url = array();
        foreach ($urls as $item) {
            $temp = array();
            $temp['url'] = $item;
            $temp1 = explode('x', $item);
            $temp['val'] = $temp1[1];
            $data_url[$temp1[0]] = $temp;
        }
        $params['urls'] = $data_url;
        return $params;
    }

    private function getUrlName($xd_type, $key, $val) {
        $where_key = '';
        $name = "";
        switch ($key) {
            case 0:
                $where_key = "bank_type";
                $name = getJingliBanktype($val);
                break;
            case 8:
                $where_key = "profession";
                $name = getMemberCompany($val);
                break;
            case 13:
                $where_key = "has_house";
                $name = getMemberHashouse($val);
                break;
            case 14:
                $where_key = "assure_type";
                $name = getDaikuanAssureType($val);
                break;
            case 16:
                $where_key = "refund_type";
                $name = getDaikuanRefundType($val);
                break;
            case 17:
                $where_key = "credit_record";
                $name = getMemberCreditlog($val);
                break;
            case 9;
                $where_key = 'first_pay';
                $name = getMaicheFirst_Pay($val);
                break;
            case 12:
                $where_key = 'car_number';
                $name = getMaicheCarNumber($val);
                break;
            case 15:
                $where_key = 'car_use';
                $name = getMaicheCarUse($val);
                break;
            case 18:
                $where_key = 'has_car';
                $name = getMaicheHascar($val);
                break;
            case 11;
                $where_key = 'second_hand_house';
                $name = getMaifangSecondHandHouse($val);
                break;
            case 19;
                $where_key = 'fitsr_suite';
                $name = getMaifangFitsrSuite($val);
                break;
        }

        return array('where_key' => $where_key, 'name' => $name);
    }

}