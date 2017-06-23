<?php
namespace Admin\Controller;
use Common\Model\AreaModel;

class InsureController extends AdminCommonController {
    public function _initialize() {
        parent::_initialize();
        $this->assign('nav','pro');
    }
    final public function index() {
        $pm = new ProModel();
        $ptm = new ProTypeModel();
        $p = max(1, I('get.p', 1, 'intval'));
        $type_id = I('get.type_id', 0);
        $title = I('get.title', '','trim');
        $where = array();
        $list = $pm->getLists($where,$p,20,1);
        $this->assign('list',$list);
        $types = $ptm->getShowList();
        $this->assign('pro_types',$types);
        $this->display();
    }
    final public function add(){
        $pm = new ProModel();
        $ppm = new ProProjectsModel();
        $ptm = new ProTypeModel();
        $cm = new CompanyModel();
        $am = new AreaModel();
        $id = I('get.id');
        if(IS_POST){
            $data = array();
            $data['title'] = I('post.title','');
            $data['company_id'] = I('post.company_id','');
            $data['information'] = I('post.information','');
            $data['pro_type_id'] = I('post.pro_type_id','');
            $data['insure_years'] = I('post.insure_years','');
            $data['insure_object'] = I('post.insure_object','');
            $data['price'] = I('post.price',0,'intval');
            $data['coverage'] = I('post.coverage','');
            $data['insure_times'] = I('post.insure_times','');
            $data['pay_type'] = I('post.pay_type','');
            $data['feature'] = I('post.feature','');
            $data['order_id'] = I('post.order_id',0,'intval');
            $data['is_hot'] = I('post.is_hot','','trim');
            $data['update_time'] = time();
            $pm->startTrans();
            if($id){
                $temp = $pm->where(array('id'=>$id))->save($data);
            }else{
                $data['add_time'] = time();
                $temp = $pm->add($data);
            }
            if(!$temp&&$pm->getDbError()){
                $pm->rollback();
                $this->error('操作失败');
            }else{
                $projects_name = I('post.projects_name');
                $projects_desc = I('post.projects_desc');
                $projects_money = I('post.projects_money');
                $dataAll = array();
                foreach($projects_name as $key=>$item){
                    if($item){
                        $dataAll[] = array('pro_id'=>$id,'name'=>$projects_name[$key],'money'=>$projects_money[$key],'desc'=>$projects_desc[$key]);
                    }
                }
                $ppm->where(array('pro_id'=>$id))->delete();
                $ppm->addAll($dataAll);
                $pm->commit();
                $this->success('操作成功');
            }
            exit;

        }
        $info =$pm->getInfo($id);
        $this->assign('info',$info);
        $domian_list = $am->getList(array('is_domain'=>'1'));
        $this->assign('domain_list',$domian_list);
        $types = $pm->getProType();
        $this->assign('types',$types);
        $companys =$cm->getAllList();
        $this->assign('companys',$companys);

        $this->display();
    }
}