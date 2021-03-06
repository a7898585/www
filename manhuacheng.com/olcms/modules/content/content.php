<?php
defined('IN_OLCMS') or exit('No permission resources.');
//模型缓存路径
define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);
//定义在单独操作内容的时候，同时更新相关栏目页面
//define('RELATION_HTML',true);


pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form','',0);
pc_base::load_app_func('util');
pc_base::load_sys_class('format','',0);

class content extends admin{
    private $db,$priv_db;
    public $categorys;
    public function __construct(){
        parent::__construct();
        $this->db=pc_base::load_model('content_model');
        $this->categorys=getcache('category_content','commons');
        //权限判断
        if(isset($_GET['catid'])&&$_SESSION['roleid']!=1&&ROUTE_A!='pass'&&strpos(ROUTE_A,'public_')===false){
            $catid=intval($_GET['catid']);
            $this->priv_db=pc_base::load_model('category_priv_model');
            $action=$this->categorys[$catid]['type']==0?ROUTE_A:'init';
            $priv_datas=$this->priv_db->get_one(array('catid'=>$catid,'is_admin'=>1,'action'=>$action));
            if(!$priv_datas)
                showmessage(L('permission_to_operate'),'blank');
        }
    }
    public function init(){
        $show_header=$show_dialog='';
        $modelid=intval($_GET['modelid']);
        $catid=$_GET['catid']=intval($_GET['catid']);
        if($modelid||$catid){
            
            $category=$this->categorys[$catid];
            $modelid=$catid>0?$category['modelid']:$modelid;
            $admin_username=param::get_cookie('admin_username');
            //查询当前的工作流
            $setting=string2array($category['setting']);
            $workflowid=$setting['workflowid'];
            $workflows=getcache('workflow','commons');
            $workflows=$workflows[$workflowid];
            $workflows_setting=string2array($workflows['setting']);
            
            //将有权限的级别放到新数组中
            $admin_privs=array();
            foreach($workflows_setting as $_k=>$_v){
                if(empty($_v))
                    continue;
                foreach($_v as $_value){
                    if($_value==$admin_username)
                        $admin_privs[$_k]=$_k;
                }
            }
            //工作流审核级别
            $workflow_steps=$workflows['steps'];
            $workflow_menu='';
            $steps=isset($_GET['steps'])?intval($_GET['steps']):0;
            //工作流权限判断
            if($_SESSION['roleid']!=1&&$steps&&!in_array($steps,$admin_privs))
                showmessage(L('permission_to_operate'));
            
            $this->db->set_model($modelid);
            $status=$steps?$steps:99;
            if($modelid == 12){
                $temp = 100;
            }
            if(isset($_GET['reject']))
                $status=0;
            if($catid)
                $where=' catid='.$catid.' AND';
            if($category['child']){
                $where=" catid IN ({$category ['arrchildid']}) AND ";
            }elseif($catid){
                $where=' catid='.$catid.' AND';
            }
            $where.=' status='.$status;
            
            //得到树型 栏目搜索
            $tree=pc_base::load_sys_class('tree');
            $tree->icon=array('&nbsp;&nbsp;│ ','&nbsp;&nbsp;├─ ','&nbsp;&nbsp;└─ ');
            $tree->nbsp='&nbsp;&nbsp;';
            $categorys=array();
            foreach($this->categorys as $cid=>$r){
                if($r['type'])
                    continue;
                if($modelid&&$modelid!=$r['modelid'])
                    continue;
                $r['disabled']=$r['child']?'disabled':'';
                $r['selected']=$cid==$catid?'selected':'';
                $categorys[$cid]=$r;
            }
            $str="<option value='\$catid' \$selected \$disabled>\$spacer \$catname</option>";
            
            $tree->init($categorys);
            unset($categorys);
            $string.=$tree->get_tree(0,$str);
            unset($tree);
            //搜索条件
            if(isset($_GET['start_time'])&&$_GET['start_time']){
                $start_time=strtotime($_GET['start_time']);
                $where.=" AND `inputtime` > '$start_time'";
            }
            if(isset($_GET['end_time'])&&$_GET['end_time']){
                $end_time=strtotime($_GET['end_time']);
                $where.=" AND `inputtime` < '$end_time'";
            }
            if($start_time>$end_time)
                showmessage(L('starttime_than_endtime'));
            if(isset($_GET['keyword'])&&!empty($_GET['keyword'])){
                $type_array=array('title','description','username');
                $searchtype=intval($_GET['searchtype']);
                if($searchtype<3){
                    $searchtype=$type_array[$searchtype];
                    $keyword=strip_tags(trim($_GET['keyword']));
                    $where.=" AND `$searchtype` like '%$keyword%'";
                }elseif($searchtype==3){
                    $keyword=$_GET['keyword'];
                    if(strstr($keyword,"m")){
                        $keyword = str_replace("m","", $keyword);
                        $where.=" AND `manhuaid`='$keyword'";
                    }else{
                        $keyword=intval($keyword);
                        $where.=" AND `id`='$keyword'";
                    }
                }
            }
            if(isset($_GET['posids'])&&!empty($_GET['posids'])){
                $posids=$_GET['posids']==1?intval($_GET['posids']):0;
                $where.=" AND `posids` = '$posids'";
            }
            $datas=$this->db->listinfo($where,'id desc',$_GET['page']);
            $pages=$this->db->pages;
            for($i=1;$i<=$workflow_steps;$i++){
                if($_SESSION['roleid']!=1&&!in_array($i,$admin_privs))
                    continue;
                $current=$steps==$i?'class=on':'';
                $r=$this->db->get_one(array('catid'=>$catid,'status'=>$i));
                $newimg=$r?'<img src="'.IMG_PATH.'icon/new.png" style="padding-bottom:2px" onclick="window.location.href=\'?m=content&c=content&a=&menuid='.$_GET['menuid'].'&catid='.$catid.'&steps='.$i.'\'">':'';
                $workflow_menu.='<a href="?m=content&c=content&a=&menuid='.$_GET['menuid'].'&catid='.$catid.'&steps='.$i.'" '.$current.' ><em>'.L('workflow_'.$i).$newimg.'</em></a><span>|</span>';
            }
            if($workflow_menu){
                $current=isset($_GET['reject'])?'class=on':'';
                $workflow_menu.='<a href="?m=content&c=content&a=&menuid='.$_GET['menuid'].'&catid='.$catid.'&reject=1" '.$current.' ><em>'.L('reject').'</em></a><span>|</span>';
            }
            include $this->admin_tpl('content_list');
        }else{
            include $this->admin_tpl('content_quick');
        }
    }
    public function add(){
        if(isset($_POST['dosubmit'])||isset($_POST['dosubmit_continue'])){
            $catid=$_POST['info']['catid']=intval($_POST['info']['catid']);
            if(!param::get_cookie('catid'))
                param::set_cookie('catid',$catid);
            if(trim($_POST['info']['title'])=='')
                showmessage(L('title_is_empty'));
            $category=$this->categorys[$catid];
            if($category['type']==0){
                $modelid=$this->categorys[$catid]['modelid'];
                $this->db->set_model($modelid);
                //如果该栏目设置了工作流，那么必须走工作流设定
                $setting=string2array($category['setting']);
                $workflowid=$setting['workflowid'];
                if($workflowid&&$_POST['status']!=99){
                    //如果用户是超级管理员，那么则根据自己的设置来发布
                    $_POST['info']['status']=$_SESSION['roleid']==1?intval($_POST['status']):1;
                }else{
                    $_POST['info']['status']=99;
                }
                //print_r($_POST);exit;
                $this->db->add_content($_POST['info']);
                if(isset($_POST['dosubmit'])){
                    showmessage(L('add_success').L('2s_close'),'blank','','','function set_time() {$("#secondid").html(1);}setTimeout("set_time()", 500);setTimeout("window.close()", 1200);');
                }else{
                    showmessage(L('add_success'),HTTP_REFERER);
                }
            }else{
                //单网页
                $this->page_db=pc_base::load_model('page_model');
                $style_font_weight=$_POST['style_font_weight']?'font-weight:'.strip_tags($_POST['style_font_weight']):'';
                $_POST['info']['style']=strip_tags($_POST['style_color']).';'.$style_font_weight;
                
                if($_POST['edit']){
                    $this->page_db->update($_POST['info'],array('catid'=>$catid));
                }else{
                    $catid=$this->page_db->insert($_POST['info'],1);
                }
                $this->page_db->create_html($catid,$_POST['info']);
                $forward=HTTP_REFERER;
            }
            showmessage(L('add_success'),$forward);
        }else{
            $show_header=$show_dialog=$show_validator='';
            //设置cookie 在附件添加处调用
            param::set_cookie('module','content');
            
            $modelid=intval($_GET['modelid']);
            $catid=intval($_GET['catid']);
            if($modelid||$catid){
                if($catid>0)
                    param::set_cookie('catid',$catid);
                $category=$this->categorys[$catid];
                if($category['type']==0){
                    $modelid=$catid>0?$category['modelid']:$modelid;
                    //取模型ID，依模型ID来生成对应的表单
                    require CACHE_MODEL_PATH.'content_form.class.php';
                    $content_form=new content_form($modelid,$catid,$this->categorys);
                    $forminfos=$content_form->get();
                    $formValidator=$content_form->formValidator;
                    $setting=string2array($category['setting']);
                    $workflowid=$setting['workflowid'];
                    $workflows=getcache('workflow','commons');
                    $workflows=$workflows[$workflowid];
                    $workflows_setting=string2array($workflows['setting']);
                    $nocheck_users=$workflows_setting['nocheck_users'];
                    $admin_username=param::get_cookie('admin_username');
                    if(!empty($nocheck_users)&&in_array($admin_username,$nocheck_users)){
                        $priv_status=true;
                    }else{
                        $priv_status=false;
                    }
                    include $this->admin_tpl('content_add');
                }else{
                    //单网页
                    $this->page_db=pc_base::load_model('page_model');
                    
                    $r=$this->page_db->get_one(array('catid'=>$catid));
                    
                    if($r){
                        extract($r);
                        $style_arr=explode(';',$style);
                        $style_color=$style_arr[0];
                        $style_font_weight=$style_arr[1]?substr($style_arr[1],12):'';
                    }
                    include $this->admin_tpl('content_page');
                }
            }else{
                include $this->admin_tpl('content_add');
            }
            header("Cache-control: private");
        }
    }
    
    public function edit(){
        //设置cookie 在附件添加处调用
        param::set_cookie('module','content');
        if(isset($_POST['dosubmit'])||isset($_POST['dosubmit_continue'])){
            //define('INDEX_HTML',true);
            $id=intval($_POST['id']);
            $catid=$_POST['info']['catid']=intval($_POST['info']['catid']);
            if(trim($_POST['info']['title'])=='')
                showmessage(L('title_is_empty'));
            $modelid=$this->categorys[$catid]['modelid'];
            $this->db->set_model($modelid);
            $_POST['info']['updatetime']=SYS_TIME;
            $this->db->edit_content($_POST['info'],$id);
            if(isset($_POST['dosubmit'])){
                showmessage(L('update_success').L('2s_close'),'blank','','','function set_time() {$("#secondid").html(1);}setTimeout("set_time()", 500);setTimeout("window.close()", 1200);');
            }else{
                showmessage(L('update_success'),HTTP_REFERER);
            }
        }else{
            $show_header=$show_dialog=$show_validator='';
            //从数据库获取内容
            $id=intval($_GET['id']);
            if(!isset($_GET['catid'])||!$_GET['catid'])
                showmessage(L('missing_part_parameters'));
            $catid=$_GET['catid']=intval($_GET['catid']);
            
            $this->model=getcache('model','commons');
            
            param::set_cookie('catid',$catid);
            $category=$this->categorys[$catid];
            $modelid=$category['modelid'];
            $this->db->table_name=$this->db->db_tablepre.$this->model[$modelid]['tablename'];
            $r=$this->db->get_one(array('id'=>$id));
            $r2=array();
            if($this->model[$modelid]['type']!=3){
                $this->db->table_name=$this->db->table_name.'_data';
                $r2=$this->db->get_one(array('id'=>$id));
                if(!$r2){
                    showmessage(L('subsidiary_table_datalost'),'blank');
                }
            }
            $data=array_merge($r,$r2);
            require CACHE_MODEL_PATH.'content_form.class.php';
            $content_form=new content_form($modelid,$catid,$this->categorys);
            
            $forminfos=$content_form->get($data);
            $formValidator=$content_form->formValidator;
            include $this->admin_tpl('content_edit');
        }
        header("Cache-control: private");
    }
    /**
     * 删除
     */
    public function delete(){
        if(isset($_GET['dosubmit'])){
            $catid=intval($_GET['catid']);
            $modelid=intval($_GET['modelid']);
            if(!$catid&&!$modelid)
                showmessage(L('missing_part_parameters'));
            $modelid=$modelid<1?$this->categorys[$catid]['modelid']:$modelid;
            $sethtml=$this->categorys[$catid]['sethtml'];
            
            $html_root=pc_base::load_config('system','html_root');
            if($sethtml)
                $html_root='';
            
            $setting=string2array($this->categorys[$catid]['setting']);
            $content_ishtml=$setting['content_ishtml'];
            $this->db->set_model($modelid);
            $this->hits_db=pc_base::load_model('hits_model');
            $this->queue=pc_base::load_model('queue_model');
            if(isset($_GET['ajax_preview'])){
                $ids=intval($_GET['id']);
                $_POST['ids']=array(0=>$ids);
            }
            if(empty($_POST['ids']))
                showmessage(L('you_do_not_check'));
                //附件初始化
            $attachment=pc_base::load_model('attachment_model');
            $this->content_check_db=pc_base::load_model('content_check_model');
            $this->position_data_db=pc_base::load_model('position_data_model');
            $this->search_db=pc_base::load_model('search_model');
            $this->comment=pc_base::load_app_class('comment','comment');
            $search_model=getcache('search_model','search');
            $tagindex_db=pc_base::load_model('tagindex_model');
            $typeid=$search_model[$modelid]['typeid'];
            $this->url=pc_base::load_app_class('url','content');
            
            foreach($_POST['ids'] as $id){
                $r=$this->db->get_one(array('id'=>$id));
                if($content_ishtml&&!$r['islink']){
                    $urls=$this->url->show($id,0,$r['catid'],$r['inputtime']);
                    $fileurl=$urls[1];
                    
                    //删除静态文件，排除htm/html/shtml外的文件
                    $lasttext=strrchr($fileurl,'.');
                    $len=-strlen($lasttext);
                    $path=substr($fileurl,0,$len);
                    $path=ltrim($path,'/');
                    $filelist=glob(OLCMS_PATH.$path.'*');
                    foreach($filelist as $delfile){
                        $lasttext=strrchr($delfile,'.');
                        if(!in_array($lasttext,array('.htm','.html','.shtml')))
                            continue;
                        @unlink($delfile);
                        //删除发布点队列数据
                        $delfile=str_replace(OLCMS_PATH,'/',$delfile);
                        $this->queue->add_queue('del',$delfile);
                    }
                }else{
                    $fileurl=0;
                }
                //删除内容
                $this->db->delete_content($id,$fileurl,$r['catid']);
                //删除统计表数据
                $this->hits_db->delete(array('hitsid'=>'c-'.$modelid.'-'.$id));
                //删除附件
                $attachment->api_delete('c-'.$r['catid'].'-'.$id);
                //删除审核表数据
                $this->content_check_db->delete(array('checkid'=>'c-'.$id.'-'.$modelid));
                //删除推荐位数据
                $this->position_data_db->delete(array('id'=>$id,'catid'=>$r['catid'],'module'=>'content'));
                //删除标签数据
                $tagindex_db->delete(array('aid'=>$id,'catid'=>$r['catid']));
                //删除全站搜索中数据
                $this->search_db->delete_search($typeid,$id);
                //删除相关的评论
                $commentid=id_encode('content_'.$catid,$id);
                $this->comment->del($commentid,$id,$catid);
            }
            //更新栏目统计
            $this->db->cache_items();
            showmessage(L('operation_success'),HTTP_REFERER);
        }else{
            showmessage(L('operation_failure'));
        }
    }
    /**
     * 过审内容
     */
    public function pass(){
        $admin_username=param::get_cookie('admin_username');
        $catid=intval($_GET['catid']);
        
        if(!$catid)
            showmessage(L('missing_part_parameters'));
        $category=$this->categorys[$catid];
        $setting=string2array($category['setting']);
        $workflowid=$setting['workflowid'];
        //只有存在工作流才需要审核
        if($workflowid){
            $steps=intval($_GET['steps']);
            //检查当前用户有没有当前工作流的操作权限
            $workflows=getcache('workflow','commons');
            $workflows=$workflows[$workflowid];
            $workflows_setting=string2array($workflows['setting']);
            //将有权限的级别放到新数组中
            $admin_privs=array();
            foreach($workflows_setting as $_k=>$_v){
                if(empty($_v))
                    continue;
                foreach($_v as $_value){
                    if($_value==$admin_username)
                        $admin_privs[$_k]=$_k;
                }
            }
            if($_SESSION['roleid']!=1&&$steps&&!in_array($steps,$admin_privs))
                showmessage(L('permission_to_operate'));
                //更改内容状态
            if(isset($_GET['reject'])){
                //退稿
                $status=0;
            }else{
                //工作流审核级别
                $workflow_steps=$workflows['steps'];
                
                if($workflow_steps>$steps){
                    $status=$steps+1;
                }else{
                    $status=99;
                }
            }
            
            $modelid=$this->categorys[$catid]['modelid'];
            $this->db->set_model($modelid);
            if(isset($_GET['ajax_preview'])){
                $_POST['ids']=$_GET['id'];
            }
            $this->db->status($_POST['ids'],$status);
        }
        showmessage(L('operation_success'),HTTP_REFERER);
    }
    /**
     * 排序
     */
    public function listorder(){
        if(isset($_GET['dosubmit'])){
            $catid=intval($_GET['catid']);
            $modelid=intval($_GET['modelid']);
            if(!$catid&&!$modelid)
                showmessage(L('missing_part_parameters'));
            $modelid=$modelid<1?$this->categorys[$catid]['modelid']:$modelid;
            $this->db->set_model($modelid);
            foreach($_POST['listorders'] as $id=>$listorder){
                $this->db->update(array('listorder'=>$listorder),array('id'=>$id));
            }
            showmessage(L('operation_success'));
        }else{
            showmessage(L('operation_failure'));
        }
    }
    /**
     * 显示栏目菜单列表
     */
    public function public_categorys(){
        $show_header='';
        $from=isset($_GET['from'])&&in_array($_GET['from'],array('block'))?$_GET['from']:'content';
        $tree=pc_base::load_sys_class('tree');
        if($from=='content'&&$_SESSION['roleid']!=1){
            $this->priv_db=pc_base::load_model('category_priv_model');
            $priv_result=$this->priv_db->select(array('action'=>'init','roleid'=>$_SESSION['roleid'],'is_admin'=>1));
            $priv_catids=array();
            foreach($priv_result as $_v){
                $priv_catids[]=$_v['catid'];
            }
            //if(empty($priv_catids)) return false;
        }
        $categorys=array();
        if(!empty($this->categorys)){
            foreach($this->categorys as $r){
                if($r['type']==2&&$r['child']==0)
                    continue;
                if($from=='content'&&$_SESSION['roleid']!=1&&!in_array($r['catid'],$priv_catids)){
                    $arrchildid=explode(',',$r['arrchildid']);
                    $array_intersect=array_intersect($priv_catids,$arrchildid);
                    if(empty($array_intersect))
                        continue;
                }
                if($r['type']==1||$from=='block'){
                    if($r['type']==0){
                        $r['vs_show']="<a href='?m=block&c=block_admin&a=public_visualization&menuid=".$_GET['menuid']."&catid=".$r['catid']."&type=show' target='right'>[".L('content_page')."]</a>";
                    }else{
                        $r['vs_show']='';
                    }
                    $r['icon_type']='file';
                    $r['add_icon']='';
                    $r['type']='add';
                }else{
                    $r['icon_type']=$r['vs_show']='';
                    $r['type']='init';
                    $r['add_icon']="<a target='right' href='?m=content&c=content&menuid=".$_GET['menuid']."&catid=".$r['catid']."' onclick=javascript:openwinx('?m=content&c=content&a=add&menuid=".$_GET['menuid']."&catid=".$r['catid']."','')><img src='".IMG_PATH."add_content.gif' alt='".L('add')."'></a> ";
                }
                $categorys[$r['catid']]=$r;
            }
        }
        if(!empty($categorys)){
            $tree->init($categorys);
            switch($from){
                case 'block':
                    $strs="<span class='\$icon_type'>\$add_icon<a href='?m=block&c=block_admin&a=public_visualization&menuid=".$_GET['menuid']."&catid=\$catid&type=list' target='right'>\$catname</a> \$vs_show</span>";
                    $strs2="<img src='".IMG_PATH."folder.gif'> <a href='?m=block&c=block_admin&a=public_visualization&menuid=".$_GET['menuid']."&catid=\$catid&type=category' target='right'>\$catname</a>";
                    break;
                
                default:
                    $strs="<span class='\$icon_type'>\$add_icon<a href='?m=content&c=content&a=\$type&menuid=".$_GET['menuid']."&catid=\$catid' target='right' onclick='open_list(this)'>\$catname</a></span>";
                    $strs2="<span class='folder'>\$catname</span>";
                    break;
            }
            $categorys=$tree->get_treeview(0,'category_tree',$strs,$strs2);
        }elseif($_SESSION['roleid']!=1){
            $categorys=L('please_check_category');
        }else{
            $categorys=L('please_add_category');
        }
        include $this->admin_tpl('category_tree');
        exit();
    }
    /**
     * 检查标题是否存在
     */
    public function public_check_title(){
        if($_GET['data']==''||(!$_GET['modelid']))
            return '';
        $modelid=intval($_GET['modelid']);
        $this->db->set_model($modelid);
        $title=$_GET['data'];
        if(CHARSET=='gbk')
            $title=iconv('utf-8','gbk',$title);
        $r=$this->db->get_one(array('title'=>$title));
        if($r){
            exit('1');
        }else{
            exit('0');
        }
    }
    
    /**
     * 图片裁切
     */
    public function public_crop(){
        if(isset($_GET['picurl'])&&!empty($_GET['picurl'])){
            $picurl=$_GET['picurl'];
            $modelid=intval($_GET['modelid']);
            if(isset($_GET['module'])&&!empty($_GET['module'])){
                $module=$_GET['module'];
            }
            $show_header='';
            include $this->admin_tpl('crop');
        }
    }
    /**
     * 相关文章选择
     */
    public function public_relationlist(){
        pc_base::load_sys_class('format','',0);
        $show_header='';
        $model_cache=getcache('model','commons');
        if(!isset($_GET['modelid'])){
            showmessage(L('please_select_modelid'));
        }else{
            $page=intval($_GET['page']);
            
            $modelid=intval($_GET['modelid']);
            $this->db->set_model($modelid);
            $where='';
            if($_GET['catid']){
                $catid=intval($_GET['catid']);
                $where.="catid='$catid'";
            }
            $where.=$where?' AND status=99':'status=99';
            
            if(isset($_GET['keywords'])){
                $keywords=trim($_GET['keywords']);
                $field=$_GET['field'];
                if(in_array($field,array('id','title','keywords','description'))){
                    if($field=='id'){
                        $where.=" AND `id` ='$keywords'";
                    }else{
                        $where.=" AND `$field` like '%$keywords%'";
                    }
                }
            }
            $infos=$this->db->listinfo($where,'',$page,12);
            $pages=$this->db->pages;
            include $this->admin_tpl('relationlist');
        }
    }
    public function public_getjson_ids(){
        $modelid=intval($_GET['modelid']);
        $id=intval($_GET['id']);
        $this->db->set_model($modelid);
        $tablename=$this->db->table_name;
        $this->db->table_name=$tablename.'_data';
        $r=$this->db->get_one(array('id'=>$id),'relation');
        
        if($r['relation']){
            $relation=str_replace('|',',',$r['relation']);
            $where="id IN($relation)";
            $infos=array();
            $this->db->table_name=$tablename;
            $datas=$this->db->select($where,'id,title');
            foreach($datas as $_v){
                $_v['sid']='v'.$_v['id'];
                if(strtolower(CHARSET)=='gbk')
                    $_v['title']=iconv('gbk','utf-8',$_v['title']);
                $infos[]=$_v;
            }
            echo json_encode($infos);
        }
    }
    
    //文章预览
    public function public_preview(){
        $catid=intval($_GET['catid']);
        $id=intval($_GET['id']);
        
        if(!$catid||!$id)
            showmessage(L('missing_part_parameters'),'blank');
        $page=intval($_GET['page']);
        $page=max($page,1);
        $CATEGORYS=getcache('category_content','commons');
        
        if(!isset($CATEGORYS[$catid])||$CATEGORYS[$catid]['type']!=0)
            showmessage(L('missing_part_parameters'),'blank');
        define('HTML',true);
        $CAT=$CATEGORYS[$catid];
        $MODEL=getcache('model','commons');
        $modelid=$CAT['modelid'];
        
        $this->db->table_name=$this->db->db_tablepre.$MODEL[$modelid]['tablename'];
        $r=$this->db->get_one(array('id'=>$id));
        if(!$r)
            showmessage(L('information_does_not_exist'));
        $this->db->table_name=$this->db->table_name.'_data';
        $r2=$this->db->get_one(array('id'=>$id));
        $rs=$r2?array_merge($r,$r2):$r;
        
        //再次重新赋值，以数据库为准
        $catid=$CATEGORYS[$r['catid']]['catid'];
        $modelid=$CATEGORYS[$catid]['modelid'];
        
        require_once CACHE_MODEL_PATH.'content_output.class.php';
        $content_output=new content_output($modelid,$catid,$CATEGORYS);
        $data=$content_output->get($rs);
        extract($data);
        $CAT['setting']=string2array($CAT['setting']);
        $template=$template?$template:$CAT['setting']['show_template'];
        $allow_visitor=1;
        //SEO
        $SEO=seo($catid,$title,$description);
        
        define('STYLE',$CAT['setting']['template_list']);
        if(isset($rs['paginationtype'])){
            $paginationtype=$rs['paginationtype'];
            $maxcharperpage=$rs['maxcharperpage'];
        }
        $pages=$titles='';
        if($rs['paginationtype']==1){
            //自动分页
            if($maxcharperpage<10)
                $maxcharperpage=500;
            $contentpage=pc_base::load_app_class('contentpage');
            $content=$contentpage->get_data($content,$maxcharperpage);
        }
        if($rs['paginationtype']!=0){
            //手动分页
            $CONTENT_POS=strpos($content,'[page]');
            if($CONTENT_POS!==false){
                $this->url=pc_base::load_app_class('url','content');
                $contents=array_filter(explode('[page]',$content));
                $pagenumber=count($contents);
                for($i=1;$i<=$pagenumber;$i++){
                    $pageurls[$i]=$this->url->show($id,$i,$catid,$rs['inputtime']);
                }
                $END_POS=strpos($content,'[/page]');
                if($END_POS!==false){
                    if(preg_match_all("|\[page\](.*)\[/page\]|U",$content,$m,PREG_PATTERN_ORDER)){
                        foreach($m[1] as $k=>$v){
                            $p=$k+1;
                            $titles[$p]['title']=strip_tags($v);
                            $titles[$p]['url']=$pageurls[$p][0];
                        }
                    }
                }else{
                    //当不存在 [/page]时，则使用下面分页
                    $pages=content_pages($pagenumber,$page,$pageurls);
                }
                //判断[page]出现的位置是否在第一位 
                if($CONTENT_POS<7){
                    $content=$contents[$page];
                }else{
                    $content=$contents[$page-1];
                }
                if($titles){
                    list($title,$content)=explode('[/page]',$content);
                    $content=trim($content);
                    if(strpos($content,'</p>')===0){
                        $content='<p>'.$content;
                    }
                    if(stripos($content,'<p>')===0){
                        $content=$content.'</p>';
                    }
                }
            }
        }
        include template('content',$template);
        $steps=intval($_GET['steps']);
        echo "
		<link href=\"".CSS_PATH."dialog_simp.css\" rel=\"stylesheet\" type=\"text/css\" />
		<script language=\"javascript\" type=\"text/javascript\" src=\"".JS_PATH."dialog.js\"></script>
		<script type=\"text/javascript\">art.dialog({lock:false,title:'".L('operations_manage')."',mouse:true, id:'content_m', content:'<span id=cloading ><a href=\'javascript:ajax_manage(1)\'>".L('passed_checked')."</a> | <a href=\'javascript:ajax_manage(2)\'>".L('reject')."</a> |　<a href=\'javascript:ajax_manage(3)\'>".L('delete')."</a></span>',left:'right',width:'15em', top:'bottom', fixed:true});
		function ajax_manage(type) {
			if(type==1) {
				$.get('?m=content&c=content&a=pass&ajax_preview=1&catid=".$catid."&steps=".$steps."&id=".$id."');
			} else if(type==2) {
				$.get('?m=content&c=content&a=pass&ajax_preview=1&reject=1&catid=".$catid."&steps=".$steps."&id=".$id."');
			} else if(type==3) {
				$.get('?m=content&c=content&a=delete&ajax_preview=1&dosubmit=1&catid=".$catid."&steps=".$steps."&id=".$id."');
			}
			$('#cloading').html('<font color=red>".L('operation_success')."<span id=\"secondid\">2</span>".L('after_a_few_seconds_left')."</font>');
			setInterval('set_time()', 1000);
			setInterval('window.close()', 2000);
		}
		function set_time() {
			$('#secondid').html(1);
		}
		</script>";
    }
    
    /**
     * 审核所有内容
     */
    public function public_checkall(){
        $page=isset($_GET['page'])&&intval($_GET['page'])?intval($_GET['page']):1;
        
        $show_header='';
        $workflows=getcache('workflow','commons');
        $datas=array();
        $pagesize=20;
        $sql='';
        if(in_array($_SESSION['roleid'],array('1'))){
            $super_admin=1;
            $status=isset($_GET['status'])?$_GET['status']:-1;
        }else{
            $super_admin=0;
            $status=isset($_GET['status'])?$_GET['status']:1;
            if($status==-1)
                $status=1;
        }
        if($status>4)
            $status=4;
        $this->priv_db=pc_base::load_model('category_priv_model');
        ;
        $admin_username=param::get_cookie('admin_username');
        if($status==-1){
            $sql="`status` NOT IN (99,0,-2)";
        }else{
            $sql="`status` = '$status' ";
        }
        if($status!=0&&!$super_admin){
            //以栏目进行循环
            foreach($this->categorys as $catid=>$cat){
                if($cat['type']!=0)
                    continue;
                    //查看管理员是否有这个栏目的查看权限。
                if(!$this->priv_db->get_one(array('catid'=>$catid,'roleid'=>$_SESSION['roleid'],'is_admin'=>'1'))){
                    continue;
                }
                //如果栏目有设置工作流，进行权限检查。
                $workflow=array();
                $cat['setting']=string2array($cat['setting']);
                if(isset($cat['setting']['workflowid'])&&!empty($cat['setting']['workflowid'])){
                    $workflow=$workflows[$cat['setting']['workflowid']];
                    $workflow['setting']=string2array($workflow['setting']);
                    $usernames=$workflow['setting'][$status];
                    if(empty($usernames)||!in_array($admin_username,$usernames)){ //判断当前管理，在工作流中可以审核几审
                        continue;
                    }
                }
                $priv_catid[]=$catid;
            }
            if(empty($priv_catid)){
                $sql.=" AND catid = -1";
            }else{
                $priv_catid=implode(',',$priv_catid);
                $sql.=" AND catid IN ($priv_catid)";
            }
        }
        $this->content_check_db=pc_base::load_model('content_check_model');
        $datas=$this->content_check_db->listinfo($sql,'inputtime DESC',$page);
        $pages=$this->content_check_db->pages;
        include $this->admin_tpl('content_checkall');
    }
    
    /**
     * 批量移动文章
     */
    public function remove(){
        if(isset($_POST['dosubmit'])){
            if($_POST['fromtype']==0){
                if($_POST['ids']=='')
                    showmessage(L('please_input_move_source'));
                if(!$_POST['tocatid'])
                    showmessage(L('please_select_target_category'));
                $tocatid=intval($_POST['tocatid']);
                $modelid=$this->categorys[$tocatid]['modelid'];
                if(!$modelid)
                    showmessage(L('illegal_operation'));
                $ids=array_filter(explode(',',$_POST['ids']),"intval");
                $ids=implode(',',$ids);
                $this->db->set_model($modelid);
                $this->db->update(array('catid'=>$tocatid),"id IN($ids)");
            }else{
                if(!$_POST['fromid'])
                    showmessage(L('please_input_move_source'));
                if(!$_POST['tocatid'])
                    showmessage(L('please_select_target_category'));
                $tocatid=intval($_POST['tocatid']);
                $modelid=$this->categorys[$tocatid]['modelid'];
                if(!$modelid)
                    showmessage(L('illegal_operation'));
                $fromid=array_filter($_POST['fromid'],"intval");
                $fromid=implode(',',$fromid);
                $this->db->set_model($modelid);
                $this->db->update(array('catid'=>$tocatid),"catid IN($fromid)");
            }
            showmessage(L('operation_success'),HTTP_REFERER);
            //ids
        }else{
            $show_header='';
            $catid=intval($_GET['catid']);
            $modelid=intval($_GET['modelid']);
            $modelid=$catid>0?$this->categorys[$catid]['modelid']:$modelid;
            $tree=pc_base::load_sys_class('tree');
            $tree->icon=array('&nbsp;&nbsp;│ ','&nbsp;&nbsp;├─ ','&nbsp;&nbsp;└─ ');
            $tree->nbsp='&nbsp;&nbsp;';
            $categorys=array();
            foreach($this->categorys as $cid=>$r){
                if($r['type'])
                    continue;
                if($modelid&&$modelid!=$r['modelid'])
                    continue;
                $r['disabled']=$r['child']?'disabled':'';
                $r['selected']=$cid==$catid?'selected':'';
                $categorys[$cid]=$r;
            }
            $str="<option value='\$catid' \$selected \$disabled>\$spacer \$catname</option>";
            
            $tree->init($categorys);
            $string.=$tree->get_tree(0,$str);
            
            $str="<option value='\$catid'>\$spacer \$catname</option>";
            $source_string='';
            $tree->init($categorys);
            $source_string.=$tree->get_tree(0,$str);
            $ids=empty($_POST['ids'])?'':implode(',',$_POST['ids']);
            include $this->admin_tpl('content_remove');
        }
    }
    
    /**
     * 同时发布到其他栏目
     */
    public function add_othors(){
        $show_header='';
        $sitelist=getcache('sitelist','commons');
        include $this->admin_tpl('add_othors');
    
    }
    /**
     * 同时发布到其他栏目 异步加载栏目
     */
    public function public_getsite_categorys(){
        $this->categorys=getcache('category_content','commons');
        $models=getcache('model','commons');
        $tree=pc_base::load_sys_class('tree');
        $tree->icon=array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
        $tree->nbsp='&nbsp;&nbsp;&nbsp;';
        $categorys=array();
        if($_SESSION['roleid']!=1){
            $this->priv_db=pc_base::load_model('category_priv_model');
            $priv_result=$this->priv_db->select(array('action'=>'add','roleid'=>$_SESSION['roleid'],'is_admin'=>1));
            $priv_catids=array();
            foreach($priv_result as $_v){
                $priv_catids[]=$_v['catid'];
            }
            if(empty($priv_catids))
                return '';
        }
        
        foreach($this->categorys as $r){
            if($r['type']!=0)
                continue;
            if($_SESSION['roleid']!=1&&!in_array($r['catid'],$priv_catids)){
                $arrchildid=explode(',',$r['arrchildid']);
                $array_intersect=array_intersect($priv_catids,$arrchildid);
                if(empty($array_intersect))
                    continue;
            }
            $r['modelname']=$models[$r['modelid']]['name'];
            $r['style']=$r['child']?'color:#8A8A8A;':'';
            $r['click']=$r['child']?'':"onclick=\"select_list(this,'".safe_replace($r['catname'])."',".$r['catid'].")\" class='cu' title='".L('click_to_select')."'";
            $categorys[$r['catid']]=$r;
        }
        $str="<tr \$click >
					<td align='center'>\$id</td>
					<td style='\$style'>\$spacer\$catname</td>
					<td align='center'>\$modelname</td>
				</tr>";
        $tree->init($categorys);
        $categorys=$tree->get_tree(0,$str);
        echo $categorys;
    }
    
    public function public_getjson_relation(){
        $modelid=intval($_GET['modelid']);
        $key=trim($_GET['key']);
        $field=trim($_GET['field']);
        $this->db->set_model($modelid);
        $where='';
        if(!empty($key)){
            $where=$field.' like \''.$key.'%\'';
        }
        $datas=$this->db->select($where,'id,'.$field,8);
        foreach($datas as $k=>$v){
            $datas[$k]['Item']=$v[$field];
        }
        echo json_encode($datas);
    }
    
    /**
     * 按模型管理内容
     */
    public function public_module(){
        $show_header='';
        $from=isset($_GET['from'])&&in_array($_GET['from'],array('block'))?$_GET['from']:'content';
        $db=pc_base::load_model('sitemodel_model');
        $datas=$db->listinfo('(type=0 or type=3) and disabled=0');
        //模型文章数array('模型id'=>数量);
        include $this->admin_tpl('content_module');
        exit();
    }
    /**
     * 
     */
    public function tj(){
        $db=pc_base::load_model('tj_model');
        $datas = $db->select(array(),'*','500','id desc');
        foreach ($datas as  $value) {
            $value['types'] = $value['type'] == 1?"动漫":"漫画";
            if($value['type'] == 9){$value['types'] = "所有";}
            $value['froms'] = $value['from'] == 1?"电脑端":"手机端";
            if($value[sonid]){
                $temp = explode("|", $value[sonid]);
                $value['count'] = count($temp);
                $count = $value['count']>9?"大于10":$value['count'];
                $value['res'] = "共搜索出{$count}部漫画";
            }else{
                $value['res'] = '无结果';
            }
            $list[] = $value;
        }
        include template('content', 'tj');
    }
}
?>