<?php

namespace Home\Controller;

use Think\Controller;
use Common\Extend\PageForHome;

// 域名买卖
class SellController extends Controller {

    public function index() {
        $mid = session('MEMBERINFO.id');
        $this->assign('mid', $mid);
        $this->display();
    }

    /*
     * ajax
     */

    public function ajax() {
        header("Content-Type:application/json;charset=utf-8");
        $d = I("request.d");
        switch ($d) {
            case 'xunjia':
                $mid = session('MEMBERINFO.id');
                if (!$mid) {
                    $json['status'] = 2;
                    $json['message'] = "登陆超时，请重新登陆";
                    exit(json_encode($json));
                }
                if (isSeccodeTimeout(false) == 0) {
                    $json['status'] = '2';
                    $json['message'] = '安全码过期，请重试';
                    exit(json_encode($json));
                }
                $sale_id = I('request.sale_id');
                $saleDetail = M("MembersDomainSales")->find($sale_id);
                if (!$saleDetail) {
                    $json['status'] = '2';
                    $json['message'] = '交易信息获取失败，请重试';
                    exit(json_encode($json));
                }
                if ($mid == $saleDetail['mid']) {
                    $json['status'] = '2';
                    $json['message'] = '不可以购买自己出售的域名';
                    exit(json_encode($json));
                }
                if ($saleDetail['seller_price'] > intval(I('request.price')) * 100) {
                    $json['status'] = '2';
                    $json['message'] = "报价不能小于" . Fen2Yuan($saleDetail['seller_price']) . "元";
                    exit(json_encode($json));
                }
                // 检查域名用户已经已经询过价，并且卖家没有回复
                $where['sale_id'] = $sale_id;
                $where['status'] = '0';
                $where['to_mid'] = $mid;
                $check = M("DomainTradeLog")->where($where)->find();
                if ($check) {
                    $json['status'] = '2';
                    $json['message'] = '您已经询过价，等待卖家回复中';
                    exit(json_encode($json));
                }
                $domain_trade_log['sale_id'] = $sale_id;
                $domain_trade_log['domain'] = $saleDetail['domain'];
                $domain_trade_log['from_mid'] = $saleDetail['mid'];
                $domain_trade_log['to_mid'] = session('MEMBERINFO.id');
                $domain_trade_log['type'] = '3';
                $domain_trade_log['status'] = '0';
                $domain_trade_log['update_time'] = time();
                $check = M("DomainTradeLog")->where(array('sale_id' => $sale_id, 'to_mid' => session('MEMBERINFO.id')))->find();
                if ($check) {
                    if ($check['money'] > intval(I('request.price')) * 100) {
                        $json['status'] = '2';
                        $json['message'] = "您这次出价不能小于" . Fen2Yuan($check['money']) . "元";
                        exit(json_encode($json));
                    }
                    $do = "save";
                    $domain_trade_log['id'] = $check['id'];
                } else {
                    $do = "add";
                }
                $domain_trade_log['money'] = intval(I('request.price')) * 100;
                $result = M("DomainTradeLog")->data($domain_trade_log)->$do();
                if ($result) {
                    // 出价次数标记+1
                    $result2 = IncreaseTimes($sale_id);
                    if (!$result2) {
                        M()->rollback();
                        $json['status'] = '2';
                        $json['message'] = "系统错误，请稍后重试,NO:error006";
                        exit(json_encode($json));
                    }
                    M()->commit();
                    $json['status'] = '1';
                    $json['message'] = "询价成功";
                    exit(json_encode($json));
                }
                $json['status'] = '2';
                $json['message'] = "系统出错，请稍后重试";
                exit(json_encode($json));
            case 'yikoujia':
                $mid = session('MEMBERINFO.id');
                if (!$mid) {
                    $json['status'] = 2;
                    $json['message'] = "登陆超时，请重新登陆";
                    exit(json_encode($json));
                }
                if (isSeccodeTimeout(false) == 0) {
                    $json['status'] = '2';
                    $json['message'] = '安全码过期，请重试';
                    exit(json_encode($json));
                }
                $sale_id = I('request.sale_id');
                $saleDetail = M("MembersDomainSales")->find($sale_id);
                if (!$saleDetail) {
                    $json['status'] = '2';
                    $json['message'] = '交易信息获取失败，请重试';
                    exit(json_encode($json));
                }
                if ($mid == $saleDetail['mid']) {
                    $json['status'] = '2';
                    $json['message'] = '不可以购买自己出售的域名';
                    exit(json_encode($json));
                }
                $fromId = $saleDetail['mid'];
                $toId = $mid;
                $remark = "一口价交易：Form:{$fromId},To:{$toId},Money:{$saleDetail['seller_price']}分";
                M()->startTrans();
                // sale表删除，移动到sale_finish
                $reuslt = DelDomainSales($sale_id);
                if (!$reuslt) {
                    M()->rollback();
                    $json['status'] = '2';
                    $json['message'] = '系统出错，请稍后重试,error:001';
                    exit(json_encode($json));
                }
                unset($result);
                // 写入TradeLog
                $data['sale_id'] = $sale_id;
                $data['domain'] = $saleDetail['domain'];
                $data['from_mid'] = $fromId;
                $data['to_mid'] = $toId;
                $data['money'] = $saleDetail['seller_price'];
                $data['type'] = '1';
                $data['status'] = '2';
                $data['update_time'] = time();
                $data['memo'] = $remark;
                $result = M("DomainTradeLog")->data($data)->add();
                if (!$result) {
                    M()->rollback();
                    $json['status'] = '2';
                    $json['message'] = '系统出错，请稍后重试,error:002';
                    exit(json_encode($json));
                }
                unset($result, $data);
                $result = domainTransfer(2, $saleDetail['domain'], $fromId, $toId, $saleDetail['seller_price'], $remark);
                if ($result == 1) {
                    M()->commit();
                    $json['status'] = '1';
                    $json['message'] = '交易成功';
                    $json['jump'] = "/Sell/page";
                    exit(json_encode($json));
                }
                M()->rollback();
                $errorMessage = array('-1' => '余额不足', '-2' => '修改域名表的域名状态失败', '-3' => '付款失败', '-4' => '收款失败', '-5' => '生成消费日志失败', '-6' => '生成收入日志失败');
                $json['status'] = '2';
                $json['message'] = $errorMessage[$result];
                exit(json_encode($json));
            case 'chujia':
                // 拍卖出价
                $mid = session('MEMBERINFO.id');
                if (!$mid) {
                    $json['status'] = 2;
                    $json['message'] = "登陆超时，请重新登陆";
                    exit(json_encode($json));
                }
                if (isSeccodeTimeout(false) == 0) {
                    $json['status'] = '2';
                    $json['message'] = '安全码过期，请重试';
                    exit(json_encode($json));
                }
                $sale_id = intval(I("request.sale_id"));
                if (!$sale_id) {
                    $json['status'] = 2;
                    $json['message'] = "请求出错，请稍后重试，error:001";
                    exit(json_encode($json));
                }
                $check = $saleDetail = M("MembersDomainSales")->find($sale_id);
                if (!$check) {
                    $json['status'] = 2;
                    $json['message'] = "该域名已经交易完毕,error:001";
                    exit(json_encode($json));
                }
                if ($check['mid'] == $mid) {
                    $json['status'] = 2;
                    $json['message'] = "不能竞拍自己出售的域名,error:001";
                    exit(json_encode($json));
                }
                if ($check['end_time'] < time()) {
                    $json['status'] = 2;
                    $json['message'] = "该域名已经交易完毕,error:002";
                    exit(json_encode($json));
                }
                $price = intval(I("request.price")) * 100;
                if (!$price) {
                    $json['status'] = 2;
                    $json['message'] = "请填写出价";
                    exit(json_encode($json));
                }
                if (UserTotalMoney($mid) < Bond($price)) {
                    $json['status'] = 2;
                    $json['message'] = "账户余额不足，可用金额至少需要" . Bond($price, 1) . "元，请先充值";
                    exit(json_encode($json));
                }
                if ($saleDetail[buyer_price]) {
                    $minAdd = IncreaseRange(max($saleDetail[buyer_price], $saleDetail[buyer_price]));
                    $minPrice = $minAdd + $saleDetail[buyer_price];
                } else {
                    $minPrice = $saleDetail[seller_price];
                }
                if ($price < $minPrice) {
                    $json['status'] = 2;
                    $json['message'] = "拍卖最少需要" . Fen2Yuan($minPrice) . "元";
                    exit(json_encode($json));
                }
                M()->startTrans();
                $result = ChuJia($sale_id, $price, $mid);
                if ($result != '1') {
                    M()->rollback();
                    $json['status'] = 2;
                    $json['message'] = $result;
                    exit(json_encode($json));
                }
                // 代理价尝试超过次数出价
                $saleDetail = M("MembersDomainSales")->find($sale_id);
                if ($saleDetail['daili_mid'] && $saleDetail['daili_mid'] != $mid) {
                    $minPrice = $price + IncreaseRange($price);
                    if ($saleDetail['daili_price'] >= $minPrice) {
                        ChuJia($sale_id, $minPrice, $saleDetail['daili_mid'], '（代理价）');
                    }
                }
                if ($result == '1') {
                    M()->commit();
                    $json['status'] = 1;
                    $json['message'] = "出价成功";
                    exit(json_encode($json));
                }

            case 'daili':
                // 域名关注
                M()->startTrans();
                $mid = session('MEMBERINFO.id');
                if (!$mid) {
                    $json['status'] = 2;
                    $json['message'] = "登陆超时，请重新登陆";
                    exit(json_encode($json));
                }
                if (isSeccodeTimeout(false) == 0) {
                    $json['status'] = '2';
                    $json['message'] = '安全码过期，请重试';
                    exit(json_encode($json));
                }
                $sale_id = intval(I("request.sale_id"));
                if (!$sale_id) {
                    $json['status'] = 2;
                    $json['message'] = "请求出错，请稍后重试，error:001";
                    exit(json_encode($json));
                }
                $check = $saleDetail = M("MembersDomainSales")->find($sale_id);
                if (!$check) {
                    $json['status'] = 2;
                    $json['message'] = "该域名已经交易完毕,error:001";
                    exit(json_encode($json));
                }
                if ($check['mid'] == $mid) {
                    $json['status'] = 2;
                    $json['message'] = "不能竞拍自己出售的域名,error:001";
                    exit(json_encode($json));
                }
                if ($check['end_time'] < time()) {
                    $json['status'] = 2;
                    $json['message'] = "该域名已经交易完毕,error:002";
                    exit(json_encode($json));
                }
                $daili = intval(I("request.daili")) * 100;
                if (!$daili) {
                    $json['status'] = 2;
                    $json['message'] = "请设置代理价";
                    exit(json_encode($json));
                }
                //查找已经冻结的金额
                if ($saleDetail['buyer_mid'] == $mid) {
                    $haveLockMoney = $saleDetail['buyer_price'];
                } else {
                    $haveLockMoney = 0;
                }
                if (UserTotalMoney($mid) < (Bond($daili) - $haveLockMoney)) {
                    $json['status'] = 2;
                    $json['message'] = "代理" . Fen2Yuan($daili) . "元，账户至少要有余额" . Fen2Yuan(Bond($daili) - $haveLockMoney) . "元，请先充值";
                    exit(json_encode($json));
                }
                $minAdd = IncreaseRange(max($check[seller_price], $check[buyer_price]));
                if (max($check[buyer_price], $check[seller_price]) + $minAdd > $daili) {
                    $min_daili = max($check[buyer_price], $check[seller_price]) + $minAdd;
                    $json['status'] = 2;
                    $json['message'] = "代理价不能小于" . Fen2Yuan($min_daili) . "元";
                    exit(json_encode($json));
                }
                unset($minAdd);
                if ($saleDetail['daili_price']) {
                    if ($saleDetail['daili_mid'] == $mid) {
                        // 上一任代理自己更新代理价格
                        $result = M("MembersDomainSales")->data(array('daili_mid' => $mid, 'daili_price' => $daili))->where(array('id' => $sale_id))->save();
                        if ($result) {
                            M()->commit();
                            $json['status'] = 1;
                            $json['message'] = "代理出价更新成功";
                            exit(json_encode($json));
                        }
                    } elseif ($daili == $saleDetail['daili_price']) {
                        // 设置代理刚好等于上一任
                        $result = M("MembersDomainSales")->data(array('daili_mid' => $mid, 'daili_price' => $daili))->where(array('id' => $sale_id))->save();
                        if (!$result) {
                            M()->rollback();
                            $json['status'] = 2;
                            $json['message'] = "您的代理价已经设置成功，不用重复设置。";
                            exit(json_encode($json));
                        }
                        $min_add = IncreaseRange($saleDetail[buyer_price]);
                        if ($daili >= $saleDetail[buyer_price] + $min_add) {
                            $result = ChuJia($sale_id, $daili, $mid, '（代理价）');
                            if ($result) {
                                M()->commit();
                                $json['status'] = 1;
                                $json['message'] = "代理出价设置成功";
                                exit(json_encode($json));
                            }
                        }
                    } else if ($daili > $saleDetail['daili_price']) {
                        // 代理大于上一任代理价,标记代理价,竞价一次
                        $result = M("MembersDomainSales")->data(array('daili_mid' => $mid, 'daili_price' => $daili))->where(array('id' => $sale_id))->save();
                        if (!$result) {
                            M()->rollback();
                            $json['status'] = 2;
                            $json['message'] = "系统出错，请重试,error:004";
                            exit(json_encode($json));
                        }
                        // 上一任代理尝试拍一次，如果钱不够，不用拍.钱如果花掉，则拍不下。
                        $min_add = IncreaseRange($saleDetail[buyer_price]);
                        if ($saleDetail[daili_price] >= $saleDetail[buyer_price] + $min_add) {
                            $result = ChuJia($sale_id, $saleDetail[daili_price], $saleDetail['daili_mid'], '（代理价）');
                        }
                        // 当前代理价拍一次
                        $saleDetail = M("MembersDomainSales")->find($sale_id);
                        if ($saleDetail['buyer_mid'] != $mid) {
                            // 当前不是自己领先，尝试拍一下
                            $min_add = IncreaseRange($saleDetail[buyer_price]);
                            if ($daili >= $saleDetail[buyer_price] + $min_add) {
                                $result = ChuJia($sale_id, $min_add + $saleDetail[buyer_price], $mid, '（代理价）');
                            }
                        }
                        M()->commit();
                        $json['status'] = 1;
                        $json['message'] = "代理价设置成功";
                        exit(json_encode($json));
                    } else if ($daili < $check['daili_price']) {
                        // 检查一下代理是否够拍
                        $min_add = IncreaseRange($saleDetail[buyer_price]);
                        if ($daili >= $saleDetail[buyer_price] + $min_add) {
                            ChuJia($sale_id, $daili, $mid, '（代理价）');
                        }
                        // 最高领先的再拍一次
                        $saleDetail = M("MembersDomainSales")->find($sale_id);
                        if ($saleDetail['buyer_mid'] != $saleDetail['daili_mid']) {
                            $min_add = IncreaseRange($saleDetail[buyer_price]);
                            if ($saleDetail['daili_price'] >= $saleDetail[buyer_price] + $min_add) {
                                $result = ChuJia($sale_id, $min_add + $saleDetail[buyer_price], $saleDetail['daili_mid'], '（代理价）');
                            }
                        }
                        M()->commit();
                        $json['status'] = 1;
                        $json['message'] = "代理价设置成功";
                        exit(json_encode($json));
                    }
                } else {
                    M()->startTrans();
                    if ($daili['daili_mid'] == $check['buyer_mid']) {
                        // 代理人领先状态
                        $result = M("MembersDomainSales")->data(array('daili_mid' => $mid, 'daili_price' => $daili))->where(array('id' => $sale_id))->save();
                        if ($result) {
                            M()->commit();
                            $json['status'] = 1;
                            $json['message'] = "代理出价设置成功";
                            exit(json_encode($json));
                        } else {
                            M()->rollback();
                            $json['status'] = 2;
                            $json['message'] = "系统出错，请重试,error:002";
                            exit(json_encode($json));
                        }
                    } else {
                        $result = M("MembersDomainSales")->data(array('daili_mid' => $mid, 'daili_price' => $daili))->where(array('id' => $sale_id))->save();
                        if ($result) {
                            // 自动出价
                            $minAdd = IncreaseRange($saleDetail['buyer_price']);
                            $minPrice = $minAdd + $saleDetail['buyer_price'];
                            if ($daili >= $minPrice) {
                                ChuJia($sale_id, $minPrice, $mid, '（代理价）');
                            }
                            M()->commit();
                            // cookie记录代理价
                            cookie('daili_' . $sale_id, intval(I("request.daili")), 86400);
                            $json['status'] = 1;
                            $json['message'] = "代理出价设置成功";
                            exit(json_encode($json));
                        } else {
                            M()->rollback();
                            $json['status'] = 2;
                            $json['message'] = "系统出错，请重试,error:001";
                            exit(json_encode($json));
                        }
                    }
                }
            case 'notice':
                // 域名关注
                $mid = session('MEMBERINFO.id');
                if (!$mid) {
                    $json['status'] = 2;
                    $json['message'] = "还没登陆米仓，请先登录";
                    exit(json_encode($json));
                }
                $sale_id = intval(I("request.sale_id"));
                if (!$sale_id) {
                    $json['status'] = 2;
                    $json['message'] = "请求出错，请稍后重试";
                    exit(json_encode($json));
                }
                $check = M("MembersDomainSales")->find($sale_id);
                if (!$check) {
                    $json['status'] = 2;
                    $json['message'] = "该域名已经被交易了，无需再关注";
                    exit(json_encode($json));
                }
                unset($check);
                $check = M("MembersDomainNotice")->where(array('sale_id' => $sale_id, 'mid' => $mid))->find();
                if ($check) {
                    $json['status'] = 2;
                    $json['message'] = "该域名您已经关注了";
                    exit(json_encode($json));
                }
                $data['sale_id'] = $sale_id;
                $data['member_id'] = $mid;
                $result = M("MembersDomainNotice")->data($data)->add();
                if (!$result) {
                    $json['status'] = 2;
                    $json['message'] = "系统出错，请稍后重试";
                    exit(json_encode($json));
                }
                $json['status'] = 1;
                $json['message'] = "关注成功";
                exit(json_encode($json));
            default:
                exit("param error");
        }
    }

    public function page() {
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
        $this->display();
    }

    public function detail() {
        $id = I("get.id");
        $detail = M('MembersDomainSales')->find($id);
        if ($detail['end_time'] && time() > $detail['end_time']) {
            // 纯字母域名
            $salesShuZi = M("MembersDomainSales")->where(array('classify1', '1'))->limit(4)->select();
            $this->assign('salesShuZi', $salesShuZi);
            // 纯字母域名
            $salesZiMu = M("MembersDomainSales")->where(array('classify1', '11'))->limit(4)->select();
            $this->assign('salesZiMu', $salesZiMu);
            // 其他
            $salesZaMi = M("MembersDomainSales")->where(array('classify1', '21'))->limit(4)->select();
            $this->assign('salesZaMi', $salesZaMi);
            $this->assign('detail', $detail);

            $domainDetail = M("MembersDomains")->where(array('domain' => $detail['domain']))->find();
            $this->assign('domainDetail', $domainDetail);
            $this->display('detail_finish');
            exit();
        }
        // 结束状态
        if (!$detail) {
            $detail = M('MembersDomainSalesFinish')->find($id);
            if ($detail) {
                // 纯字母域名
                $salesShuZi = M("MembersDomainSales")->where(array('classify1', '1'))->limit(4)->select();
                $this->assign('salesShuZi', $salesShuZi);
                // 纯字母域名
                $salesZiMu = M("MembersDomainSales")->where(array('classify1', '11'))->limit(4)->select();
                $this->assign('salesZiMu', $salesZiMu);
                // 其他
                $salesZaMi = M("MembersDomainSales")->where(array('classify1', '21'))->limit(4)->select();
                $this->assign('salesZaMi', $salesZaMi);
                $domainDetail = M("MembersDomains")->where(array('domain' => $detail['domain']))->find();
                $this->assign('detail', $detail);
                $this->assign('domainDetail', $domainDetail);
                $this->display('detail_finish');
                exit();
            } else {
                exit("非法访问");
            }
        }
        // 正常状态
        if ($detail['type'] == '1') {
            // 一口价
            $tmp = "yikoujia"; // 卖家其他一口价的域名
            $other = M("MembersDomainSales")->where(array('mid' => $detail['mid'], 'type' => '1', 'id' => array('neq', $id)))->limit(4)->select();
            $this->assign('other', $other);
            // 纯字母域名
            $salesShuZi = M("MembersDomainSales")->where(array('classify1', '1'))->limit(4)->select();
            $this->assign('salesShuZi', $salesShuZi);
            // 纯字母域名
            $salesZiMu = M("MembersDomainSales")->where(array('classify1', '11'))->limit(4)->select();
            $this->assign('salesZiMu', $salesZiMu);
            // 其他
            $salesZaMi = M("MembersDomainSales")->where(array('classify1', '21'))->limit(4)->select();
            $this->assign('salesZaMi', $salesZaMi);
        } elseif ($detail['type'] == '2') {
            // 拍卖
            $tmp = "paimai";
            $count = M("DomainAuctionLog")->where(array('pid' => $id))->getfield("count(distinct(uid)) as num");
            $num = M("DomainAuctionLog")->where(array('pid' => $id))->count();
            // TradeLog
            $tradeLog = M("DomainAuctionLog")->where(array("pid" => $id))->order("price desc")->limit(20)->select();
            //米铺
            $detailShop = M('MembersShop')->where(array('mid' => $detail['uid'], 'status' => '1'))->find();
            $this->assign('count', $count);
            $this->assign('num', $num);
            $this->assign('tradeLog', $tradeLog);
            $this->assign('detailShop', $detailShop);
            if ($detail['buyer_price']) {
                $minPrice = $detail['buyer_price'] + IncreaseRange($detail['buyer_price']);
            } else {
                $minPrice = $detail['seller_price'];
            }
            $this->assign("minPrice", $minPrice);
            $this->assign("daili_price", $_COOKIE['daili_' . $id]);
        } elseif ($detail['type'] == '3') {
            // /买家出价
            $tmp = "chujia";
            // 关注人数
            $noticeNum = M("MembersDomainNotice")->where(array("sale_id" => $id))->count();
            $this->assign('noticeNum', $noticeNum);
            // 卖家其他询价的域名
            $other = M("MembersDomainSales")->where(array('mid' => $detail['mid'], 'type' => '3', 'id' => array('neq', $id)))->limit(4)->select();
            $this->assign('other', $other);
            // 纯字母域名
            $salesShuZi = M("MembersDomainSales")->where(array('classify1', '1'))->limit(4)->select();
            $this->assign('salesShuZi', $salesShuZi);
            // 纯字母域名
            $salesZiMu = M("MembersDomainSales")->where(array('classify1', '11'))->limit(4)->select();
            $this->assign('salesZiMu', $salesZiMu);
            // 其他
            $salesZaMi = M("MembersDomainSales")->where(array('classify1', '21'))->limit(4)->select();
            $this->assign('salesZaMi', $salesZaMi);
        }
        $domainDetail = M("MembersDomains")->where(array('domain' => $detail['domain']))->find();
        $this->assign('mid', session('MEMBERINFO.id'));
        $this->assign('detail', $detail);
        $this->assign('domainDetail', $domainDetail);
        $this->display($tmp);
    }

}

