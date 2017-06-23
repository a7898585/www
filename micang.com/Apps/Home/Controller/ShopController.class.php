<?php

namespace Home\Controller;

use Common\Extend\PageForHome;

class ShopController extends HomeCommonController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('m_tab', 'sell_domain');
    }

    /**
     * 米铺首页
     */
    public function index() {
        $data = M()->query('SELECT s.title,s.id,s.main_category,f.total from mc_members_shop  s  LEFT JOIN (SELECT  mid,COUNT(1) total from mc_members_domain_sales_finish GROUP BY mid   ) f  on s.mid=f.mid  ORDER BY total desc LIMIT 8 ');
        $this->assign('data', $data);
        $topData = M('MembersShop')->field('id,mid,title,view_count')->order('view_count desc')->limit(10)->select();
        if (!empty($topData) && session('MEMBERINFO.id') > 0) {
            foreach ($topData as &$value) {
                $value['notice_id'] = M('MembersShopNotice')->where(array('member_id' => session('MEMBERINFO.id'), 'shop_id' => $value['id']))->getField('id');
            }
        }
        $seo = array(
            'title' => C('SHOP_TITLE'),
            'key' => C('SHOP_KEYWORDS'),
            'des' => C('SHOP_DESC')
        );
        $this->assign('seo', $seo);
        $this->assign('topData', $topData);
        $this->display();
    }

    /**
     * 米铺ajax调用列表
     * @param type $p
     */
    public function all($p = 1) {
        $limit = 8;
        $param = I('post.');
        $where = array();

        if ($param['key']) {
            $where['title'] = array('like', '%' . $param['key'] . '%');
        }
        if ($param['time'] == 'asc') {
            $order = 'addtime ' . $param['time'];
            $order_val = 'desc';
        } else {
            $order = 'addtime desc';
            $order_val = 'asc';
        }
        $order_val3 = 'asc';
        $order_val2 = 'asc';
        $order_val1 = 'asc';
        if ($param['view'] == 'asc') {
            $order = 'view_count asc';
            $order_val1 = 'desc';
        } elseif ($param['view'] == 'desc') {
            $order = 'view_count desc';
        }
        if ($param['xl']) {
            if ($param['xl'] == 'asc') {
                $order_val2 = 'desc';
            }
            $total = M('MembersShop')->where($where)->count();
            $shopData = M()->query('SELECT s.id,s.mid,s.title,s.view_count,s.addtime,f.total from mc_members_shop  s  LEFT JOIN (SELECT  mid,COUNT(1) total from mc_members_domain_sales_finish GROUP BY mid) f  on s.mid=f.mid  ORDER BY total ' . $order_val2 . ' LIMIT ' . $limit);
            if (!empty($shopData)) {
                foreach ($shopData as &$value) {
                    $value['d_count'] = M('MembersDomains')->where(array('mid' => $value['mid']))->count();
                }
            }
        } elseif ($param['count']) {
            if ($param['count'] == 'asc') {
                $order_val3 = 'desc';
            }
            $total = M('MembersShop')->where($where)->count();
            $shopData = M()->query('SELECT s.id,s.mid,s.title,s.view_count,s.addtime,f.total from mc_members_shop  s  LEFT JOIN (SELECT  mid,COUNT(1) total from mc_members_domains ) f  on s.mid=f.mid  GROUP BY mid  ORDER BY total ' . $order_val3 . '  LIMIT ' . $limit);
            if (!empty($shopData)) {
                foreach ($shopData as &$value) {
                    $value['d_sell_count'] = M('MembersDomainSalesFinish')->where(array('mid' => $value['mid']))->count();
                }
            }
        } else {
            $total = M('MembersShop')->where($where)->count();
            $shopData = M('MembersShop')->field('id,mid,title,view_count,addtime')->where($where)->order($order)->page($p)->limit($limit)->select();
            if (!empty($shopData)) {
                foreach ($shopData as &$value) {
                    $value['d_count'] = M('MembersDomains')->where(array('mid' => $value['mid']))->count();
                    $value['d_sell_count'] = M('MembersDomainSalesFinish')->where(array('mid' => $value['mid']))->count();
                }
            }
        }
        $pager = new \Common\Extend\PageForHome($total, $limit);
        $pager->url = 'javascript:getData(\'' . urlencode('[PAGE]') . '\',\'' . $param['key'] . '\',\'' . ($param['xl'] ? $order_val2 : '') . '\',\'' . ($param['view'] ? $order_val1 : '') . '\',\'' . ($param['count'] ? $order_val3 : '') . '\',\'' . $order_val . '\')';
        $this->assign('pager', $pager->show());
        $this->assign('shopData', $shopData);
        $this->assign('order', $order_val);
        $this->assign('order1', $order_val1);
        $this->assign('order2', $order_val2);
        $this->assign('order3', $order_val3);
        $html = $this->fetch('shop/all');
        $this->ajaxReturn(array('status' => 200, 'html' => $html));
    }

    /**
     * 米铺关注
     */
    public function notice() {
        if (IS_POST) {
            $data['member_id'] = session('MEMBERINFO.id');
            $data['shop_id'] = I('post.id', '', intval);
            $id = I('post.nid', '', intval);
            $note = $id ? '取消关注' : '关注';
            try {
                if ($id) {
                    $result = M('MembersShopNotice')->where(array('id' => $id, 'member_id' => $data['member_id']))->delete();
                } else {
                    $result = M('MembersShopNotice')->add($data);
                }
            } catch (\Exception $e) {
                $this->ajaxReturn(array('status' => 500, 'message' => $note . '失败，请重试'));
            }
            if (!$result) {
                $this->ajaxReturn(array('status' => 500, 'message' => $note . '失败，请重试'));
            }
            $this->ajaxReturn(array('status' => 200, 'message' => $note . '成功', 'id' => $result));
        }
    }

    /**
     * 我的米铺
     */
    public function detail($id) {
        M('MembersShop')->where(array('id' => $id))->save(array('view_count' => array('exp', 'view_count+1')));
        $detail = M('MembersShop')->where(array('id' => $id))->find();
        $detail['d_count'] = M('MembersDomains')->where(array('mid' => $detail['mid']))->count();
        $detail['d_sell_count'] = M('MembersDomainSalesFinish')->where(array('mid' => $detail['mid']))->count();
        $detail['notice_id'] = M('MembersShopNotice')->where(array('member_id' => session('MEMBERINFO.id'), 'shop_id' => $detail['id']))->getField('id');
        $sales_finish = M('MembersDomainSalesFinish')->where(array('mid' => $detail['mid']))->order('create_time desc')->find();
        if ($sales_finish) {
            switch ($sales_finish['type']) {
                case 1:
                    $dynamic = '成功通过一口价方式售出' . $sales_finish['domain'];
                    break;
                case 2:
                    $dynamic = '成功通过竞价方式售出' . $sales_finish['domain'];
                    break;
                case 3:
                    $dynamic = '成功通过询价方式售出' . $sales_finish['domain'];
                    break;
                default:
                    break;
            }
            $sales_finish['dynamic'] = $dynamic;
        }
        $seo = array(
            'title' => $detail['title'] . '的米铺|' . C('SHOP_TITLE'),
            'key' => $detail['title'] . '的米铺,' . C('SHOP_KEYWORDS'),
            'des' => C('SHOP_DESC')
        );
        $this->assign('seo', $seo);
        $this->assign('detail', $detail);

        $p = max(1, intval($_GET['p']));
        $url = '';
        // 域名包含
        if (I('request.domain')) {
            $domain = I('request.domain');
            $url .= "&domain={$domain}";
            if (!I('request.domain_tou') && !I('request.domain_wei')) {
                $where['base'] = array('like', "%{$domain}%");
            }
            if (I('request.domain_tou')) {
                $where['base'] = array('like', "{$domain}%");
            }
            if (I('request.domain_wei')) {
                $where['base'] = array('like', "%{$domain}");
            }
        }
        // 域名过滤
        if (I('request.filter')) {
            $filter = I('request.filter');
            $url .= "&filter={$filter}";
            if (!I('request.filter_tou') && !I('request.filter_wei')) {
                $where['base'] = array('notlike', "%{$filter}%");
            }
            if (I('request.filter_tou')) {
                $where['base'] = array('notlike', "{$filter}%");
                $url .= "&filter_tou=1";
            }
            if (I('request.filter_wei')) {
                $where['base'] = array('notlike', "%{$filter}");
                $url .= "&filter_wei=1";
            }
        }
        // 域名分类
        if (I("request.classify")) {
            $classify = I("request.classify");
            $mt = M("DomainClassify")->find($classify);
            if ($mt[pid] > 0) {
                $where['classify2'] = $classify;
            } else {
                $where['classify1'] = $classify;
            }
            unset($mt);
            $url .= "&classify={$classify}";
        }
        // 域名后缀
        if (I("request.suffix")) {
            $suffix = I("request.suffix");
            $where['suffix'] = $suffix;
            $url .= "&suffix={$suffix}";
        }

        // 域名长度
        if (I("request.length_from")) {
            $length_from = I("request.length_from");
            $where['length'] = array('egt', $length_from);
            $url .= "&length_from={$length_from}";
        }
        if (I("request.length_to")) {
            $length_to = I("request.length_to");
            $where['length'] = array('elt', $length_to);
            $url .= "&length_to={$length_to}";
        }
        // 价格范围
        if (is_numeric(I('request.price_from')) && is_numeric(I('request.price_to'))) {
            $price_from = max(1, I('request.price_from')) * 100;
            $price_to = max(1, I('request.price_to')) * 100;
            $where['seller_price'] = array(array('egt', $price_from), array('elt', $price_to));
            $url .= "&price_from=" . I('request.price_from') . "&price_to=" . I('request.price_to');
        }
        // 交易类型
        if (I("request.type")) {
            $trade_type = I("request.type");
            $where['type'] = $trade_type;
            $url .= "&type={$trade_type}";
            if ($trade_type == '1') {
                $tradeName = "一口价";
            } elseif ($trade_type == '2') {
                $tradeName = "拍卖";
            } elseif ($trade_type == '3') {
                $tradeName = "询价";
            }
        } else {
            $tradeName = "全部出售";
        }
        // 域名行业
        if (I("request.trade_type")) {
            $trade_type = intval(I("request.trade_type"));
            $where['trade_type'] = array('like', "%{$trade_type}%");
        }
        $total = M('MembersDomainSales')->where($where)->count();
        $domains = M('MembersDomainSales')->where($where)->page($p)->select();
        $this->assign('domains', $domains);
        $this->assign('tradeName', $tradeName);
        $pager = new PageForHome($total);
        $pager->url = '/Sell/page?p=' . urlencode('[PAGE]') . $url;
        $this->assign('pager', $pager->show());
        $this->assign('mid', session('MEMBERINFO.id'));
        $this->assign("domainClassify", M("DomainClassify")->select());
        $this->assign("domainTradeType", M("DomainTradeType")->select());
        $suffixs = M('DomainSuffix')->where(array('status' => '1'))->field('name')->select();
        $this->assign('suffixs', $suffixs);
        $this->assign('id', $id);
        $this->display();
    }

}