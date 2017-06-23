<?php
namespace Admin\Controller;
use Common\Model\AreaModel;
use Common\Model\CompanyModel;
use Common\Model\CompanyTypeModel;
use Think\Upload;
class CompanyController extends AdminCommonController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('nav','company');
    }
    final public function index() {
        $where = array();
        $name = I('get.name','','trim');
        $company_type_id = I('get.company_type_id',0,'intval');
        if($name){
            $where['bxm.name']=array('like','%'.$name.'%');
        }
        if($company_type_id){
            $where['bxm.company_type_id'] = $company_type_id;
        }
        $page['pageNum'] = max(1, I('get.p', 1, 'intval'));
        $page['numPerPage'] = I('get.numPerPage', 20, 'intval');
        $conn = M('Company bxm');
        $list = $conn->where($where) ->join('LEFT JOIN bx_company_type bxct ON bxct.id=bxm.company_type_id')->field("bxm.id,bxm.name,bxm.short_name,bxm.company_type_id,bxct.name as company_type_name,bxm.tel")
            ->page($page['pageNum'], $page['numPerPage'])
            ->select();
        $page['totalCount'] =$conn->where($where) ->join('LEFT JOIN bx_company_type bxct ON bxct.id=bxm.company_type_id')->count();
        $pager = showWebPage($page['totalCount'],$page['numPerPage']);

        $this->assign('list',$list);
        $this->assign('pager',$pager);
        $this->assign('page',$page);
        $type_list = M('CompanyType')->field('id,name')->select();
        $this->assign('type_list',$type_list);
        $this->display();
    }
    final public function add(){
        $am = new AreaModel();
        $cm = new CompanyModel();
        $ctm = new CompanyTypeModel();
        $id = I('get.id');
        if(IS_POST){
            $data['name'] = I('post.name','','trim');
            $data['short_name'] = I('post.short_name','','trim');
            $data['company_type_id'] = I('post.company_type_id','','trim');
            $data['province_id'] = I('post.province_id','','trim');
            $data['city_id'] = I('post.city_id','','trim');
            $data['website'] = I('post.website','','trim');
            $data['address'] = I('post.address','','trim');
            $data['photo_url'] = I('post.photo_url','','trim');
            $data['tel'] = I('post.tel','','trim');
            $data['order_id'] = I('post.order_id','0','intval');
            if($id){
                $temp = $cm->where(array('id'=>$id))->save($data);
            }else{
                $temp = $cm->add($data);
            }
            if(!$temp&&$cm->getDbError()){
                $this->error('操作失败');
            }else{
                $this->success('操作成功');
                exit;
            }
        }
        $info =M('Company')->where(array('id'=>$id))->find();
        $this->assign('info',$info);
        $province = $am->province();
        $this->assign('province',$province);
        if($info['province_id']){
            $city = $am->city($info['province_id']);
            $this->assign('city',$city);
        }
        $type_list = $ctm->getAllList();
        $this->assign('type_list',$type_list);
        $this->display();
    }
}