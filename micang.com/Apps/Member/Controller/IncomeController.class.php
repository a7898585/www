<?php
/**
 * IncomeController.class.php
 * Author: Jansen
 * QQ: 6206574
 * Date: 2015-10-03
 */

namespace Member\Controller;

use Common\Extend\PageForMember;
class IncomeController extends MemberCommonController{
    /**
     * 收入明细
     * @author Jansen
     * @since 2015-10-03
     */
    public function bill($p=1){
        $this->assign('m_tab', 'recharge');
        $total = M('MembersIncomeDetail')->where(array('mid'=>session('MEMBERINFO.id')))->count();
        $incomes = M('MembersIncomeDetail')->where(array('mid'=>session('MEMBERINFO.id')))->order(array('time'=>'DESC'))->page($p)->select();
        $this->assign('incomes', $incomes);
        $pager = new PageForMember($total);
        $pager->url = '/income/bill?p='.urlencode('[PAGE]');
        $this->assign('pager', $pager->show());
        $this->display();
    }
}