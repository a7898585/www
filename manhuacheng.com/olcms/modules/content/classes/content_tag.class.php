<?php
class content_tag{
    private $db;
    public function __construct(){
        $this->db=pc_base::load_model('content_model');
        $this->position=pc_base::load_model('position_data_model');
    }
    /**
     * 初始化模型
     * @param $catid
     */
    public function set_modelid($catid){
        
        $this->category=getcache('category_content','commons');
        if($this->category[$catid]['type']!=0)
            return false;
        $this->modelid=$this->category[$catid]['modelid'];
        $this->db->set_model($this->modelid);
        $this->tablename=$this->db->table_name;
        if(empty($this->category)){
            return false;
        }else{
            return true;
        }
    }
    /**
     * 分页统计
     * @param $data
     */
    public function count($data){
        if($data['action']=='lists'){
            $catid=intval($data['catid']);
            $modelid=intval($data['modelid']);
            if(!$catid&&!$modelid)
                return false;
            if($catid){
                $this->set_modelid($catid);
            }else{
                $this->db->set_model($modelid);
                $this->tablename=$this->db->table_name;
            }
            if(isset($data['where'])){
                $sql=$data['where'];
            }else{
                $arr=array('fields','order','limit','num','table','moreinfo','action','cache','page','urlrule','return','catid','status','thumb','modelid');
                $s='';
                foreach($data as $k=>$v){
                    if(in_array($k,$arr)||empty($v))
                        continue;
                    $b=' AND '.$k.'='.$v;
                    $s.=$b;
                }
                if($this->category[$catid]['child']){
                    $catids_str=$this->category[$catid]['arrchildid'];
                    $pos=strpos($catids_str,',')+1;
                    $catids_str=substr($catids_str,$pos);
                    $sql="status=99 AND catid IN ($catids_str)";
                }elseif($catid){
                    $sql="status=99 AND catid='$catid'";
                }else{
                    $sql="status=99 ";
                }
                $sql.=$s;
                $thumb=intval($data['thumb'])?" AND thumb !=''":'';
                $sql.=$thumb;
            }
            return $this->db->count($sql);
        }
    }
    
    /**
     * 列表页标签
     * @param $data
     */
    public function lists($data){
        $catid=intval($data['catid']);
        $modelid=intval($data['modelid']);
        if(!$catid&&!$modelid)
            return false;
        if($catid){
            $this->set_modelid($catid);
        }else{
            $this->db->set_model($modelid);
            $this->tablename=$this->db->table_name;
        }
        if(isset($data['where'])){
            $sql=$data['where'];
        }else{
            $arr=array('fields','order','limit','num','moreinfo','action','cache','page','urlrule','return','catid','status','thumb','modelid');
            foreach($data as $k=>$v){
                if(in_array($k,$arr)||empty($v))
                    continue;
                $b=' AND '.$k.'=\''.$v.'\'';
                $s.=$b;
            }
            if($this->category[$catid]['child']){
                $catids_str=$this->category[$catid]['arrchildid'];
                $pos=strpos($catids_str,',')+1;
                $catids_str=substr($catids_str,$pos);
                $sql="status=99 AND catid IN ($catids_str)";
            }elseif($catid){
                $sql="status=99 AND catid='$catid'";
            }else{
                $sql="status=99 ";
            }
            $sql.=$s;
            $thumb=intval($data['thumb'])?" AND thumb !=''":'';
            $sql.=$thumb;
        }
        $order=$data['order'];
        $fields=$data['fields']?$data['fields']:'*';
        $return=$this->db->select($sql,$fields,$data['limit'],$order,'','id');
        
        //调用副表的数据
        if(isset($data['moreinfo'])&&intval($data['moreinfo'])==1){
            $ids=array();
            foreach($return as $v){
                if(isset($v['id'])&&!empty($v['id'])){
                    $ids[]=$v['id'];
                }else{
                    continue;
                }
            }
            if(!empty($ids)){
                $this->db->table_name=$this->db->table_name.'_data';
                $ids=implode('\',\'',$ids);
                $r=$this->db->select("`id` IN ('$ids')",'*','','','','id');
                if(!empty($r)){
                    foreach($r as $k=>$v){
                        if(isset($return[$k]))
                            $return[$k]=array_merge($v,$return[$k]);
                    }
                }
            }
        }
        return $return;
    }
    
    /**
     * 相关文章标签
     * @param $data
     */
    public function relation($data){
        $modelid=intval($data['modelid']);
        if($modelid){
            $this->db->set_model($modelid);
            $this->tablename=$this->db->table_name;
        }else{
            $catid=intval($data['catid']);
            if(!$this->category[$catid])
                return false;
            $this->set_modelid($catid);
            $modelid=$this->modelid;
        }
        $order=$data['order'];
        $limit=$data['limit'];
        if($data['relation']){
            $relations=explode('|',$data['relation']);
            $relations=array_diff($relations,array(null));
            $relations=implode(',',$relations);
            $sql=" `id` IN ($relations)";
            $key_array=$this->db->select($sql,'*',$limit,$order,'','id');
        }elseif($data['keywords']){
            $keywords=str_replace('%','',$data['keywords']);
            $keywords_arr=str_replace(' ',',',$keywords);
            $keywords_arr=explode(',',$keywords);
            $key_array=array();
            $i=1;
            $sql1='modelid='.$modelid;
            $sql2='';
            foreach($keywords_arr as $_k=>$_v){
                $sql2.=($_k>0?' OR ':' ').'tag=\''.$_v.'\'';
            }
            $tags_db=pc_base::load_model('tags_model');
            $r=$tags_db->select($sql1.' AND ('.$sql2.')','id');
            if(!$r)
                return false;
            foreach($r as $v){
                $tags[$v['id']]=$v['id'];
            }
            $tagsids=implode(',',$tags);
            unset($tags,$r,$tags_db);
            $tagindex_db=pc_base::load_model('tagindex_model');
            $r=$tagindex_db->select(' tagid in ('.$tagsids.')','aid');
            if(!$r)
                return false;
            $data['id']=$data['id']?$data['id']:0;
            foreach($r as $v){
                if($data['id']==$v['aid'])
                    continue;
                $ids[$v['aid']]=$v['aid'];
            }
            if(!$ids)
                return false;
            $key_array=$this->db->select(' id in ('.implode(',',$ids).')','*',$limit,'','','id');
            unset($ids,$tagindex_db,$r);
        }else{
            return false;
        }
        return $key_array;
    }
    
    /**
     * 排行榜标签
     * @param $data
     */
    public function hits($data){
        $catid=intval($data['catid']);
        if(!$this->category[$catid])
            return false;
        if($this->category[$catid]['type']!=0)
            return '';
        $this->set_modelid($catid);
        
        $this->hits_db=pc_base::load_model('hits_model');
        $sql=$desc=$ids='';
        $array=$ids_array=array();
        $order=$data['order'];
        $hitsid='c-'.$this->modelid.'-%';
        $sql="hitsid LIKE '$hitsid'";
        if(isset($data['day'])){
            $updatetime=SYS_TIME-intval($data['day'])*86400;
            $sql.=" AND updatetime>'$updatetime'";
        }
        $hits=array();
        $data['limit']=10;
        $result=$this->hits_db->select($sql,'*',$data['limit'],$order);
        foreach($result as $r){
            //$pos = strpos($r['hitsid'],'-',2) + 1;
            //$ids_array[] = $id = substr($r['hitsid'],$pos);
            $pos=strrchr($r['hitsid'],'-');
            $ids_array[]=$id=substr($pos,1);
            $hits[$id]=$r;
        }
        $ids=implode(',',$ids_array);
        if($ids){
            $sql="status=99 AND id IN ($ids)";
        }else{
            $sql='';
        }
        $this->db->table_name=$this->tablename;
        $result=$this->db->select($sql,'*',$data['limit'],'','','id');
        foreach($ids_array as $id){
            if($result[$id]['title']!=''){
                $array[$id]=$result[$id];
                $array[$id]=array_merge($array[$id],$hits[$id]);
            }
        }
        return $array;
    }
    
    /**
     * 排行榜标签2
     * Add By SouL_Z 2013-4-7 10:29 
     * 用来获取漫画排行榜(过滤了后台不予显示的漫画分类) 为了不影响其他地方调用原来的hits方法 所以重新复制一个出来修改
     */
    public function hits2($data){
        //return false;
        $catid=intval($data['catid']);
        /*
		if (! $this->category [$catid])
			return false;
		if ($this->category [$catid] ['type'] != 0)
			return '';
		*/
        $this->set_modelid($catid);
        $this->hits_db=pc_base::load_model('hits_model');
        $sql=$desc=$ids='';
        $array=$ids_array=array();
        $order=$data['order'];
        $hitsid='c-'.$this->modelid.'-%';
        $sql="hitsid LIKE '$hitsid'";
        if(isset($data['day'])){
            $updatetime=SYS_TIME-intval($data['day'])*86400;
            $sql.=" AND updatetime>'$updatetime'";
        }
        $hits=array();
        $hit_limit=$data['limit'];
        $start_limit=0;
        while(count($array)<$data['limit']){
            $limit=$start_limit.','.$hit_limit;
            $result=$this->hits_db->select($sql,'*',$limit,$order);
            foreach($result as $r){
                $pos=strrchr($r['hitsid'],'-');
                $ids_array[]=$id=substr($pos,1);
                $hits[$id]=$r;
            }
            $ids=implode(',',$ids_array);
            if($ids){
                $sql1="status=99 AND id IN ($ids) AND ".$data['where'];
            }else{
                $sql1='';
            }
            $this->db->table_name=$this->tablename;
            $result=$this->db->select($sql1,'*',$data['limit'],'','','id');
            foreach($ids_array as $id){
                if($result[$id]['title']!=''&&count($array)<$data['limit']){
                    $array[$id]=$result[$id];
                    $array[$id]['c_updatetime']=$result[$id]['updatetime'];
                    $array[$id]=array_merge($array[$id],$hits[$id]);
                }
            }
            $start_limit=$hit_limit;
            $hit_limit+=10;
        }
        return $array;
    }
    /**
     * 栏目标签
     * @param $data
     */
    public function category($data){
        $categorys=getcache('category_content','commons');
        $data['catid']=intval($data['catid']);
        $array=array();
        $i=1;
        foreach($categorys as $catid=>$cat){
            if($i>$data['limit'])
                break;
            if((!$cat['ismenu']))
                continue;
            if($cat['parentid']==$data['catid']){
                $array[$catid]=$cat;
                $i++;
            }
        }
        return $array;
    }
    
    /**
     * 推荐位
     * @param $data
     */
    public function position($data){
        $sql='';
        $array=array();
        $posid=intval($data['posid']);
        $order=$data['order'];
        $thumb=(empty($data['thumb'])||intval($data['thumb'])==0)?0:1;
        $catid=(empty($data['catid'])||$data['catid']==0)?'':intval($data['catid']);
        //if(!$this->category[$catid]) return false;
        if($catid&&$this->category[$catid]['child']){
            $catids_str=$this->category[$catid]['arrchildid'];
            $pos=strpos($catids_str,',')+1;
            $catids_str=substr($catids_str,$pos);
            $sql="`catid` IN ($catids_str) AND ";
        }elseif($catid&&!$this->category[$catid]['child']){
            $sql="`catid` = '$catid' AND ";
        }
        if($thumb)
            $sql.="`thumb` = '1' AND ";
        $sql.="`posid` = '$posid'";
        $linkageid=intval($data['linkageid']);
        if($linkageid>0)
            $sql.=' AND linkageid='.$linkageid;
        $pos_arr=$this->position->select($sql,'*',$data['limit'],$order);
        if(!empty($pos_arr)){
            foreach($pos_arr as $info){
                $key=$info['catid'].'-'.$info['id'];
                $array[$key]=string2array($info['data']);
                $array[$key]['url']=go($info['catid'],$info['id']);
                $array[$key]['id']=$info['id'];
                $array[$key]['catid']=$info['catid'];
                $array[$key]['listorder']=$info['listorder'];
            }
        }
        return $array;
    }
    /**
     * 可视化标签
     */
    public function pc_tag(){
        $positionlist=getcache('position','commons');
        foreach($positionlist as $_v)
            $poslist[$_v['posid']]=$_v['name'];
        return array('action'=>array('lists'=>L('list','','content'),'position'=>L('position','','content'),'category'=>L('subcat','','content'),'relation'=>L('related_articles','','content'),'hits'=>L('top','','content')),'lists'=>array('catid'=>array('name'=>L('catid','','content'),'htmltype'=>'input_select_category','data'=>array('type'=>0),'validator'=>array('min'=>1)),'order'=>array('name'=>L('sort','','content'),'htmltype'=>'select','data'=>array('id DESC'=>L('id_desc','','content'),'updatetime DESC'=>L('updatetime_desc','','content'),'listorder ASC'=>L('listorder_asc','','content'))),'thumb'=>array('name'=>L('thumb','','content'),'htmltype'=>'radio','data'=>array('0'=>L('all_list','','content'),'1'=>L('thumb_list','','content'))),'moreinfo'=>array('name'=>L('moreinfo','','content'),'htmltype'=>'radio','data'=>array('1'=>L('yes'),'0'=>L('no')))),'position'=>array('posid'=>array('name'=>L('posid','','content'),'htmltype'=>'input_select','data'=>$poslist,'validator'=>array('min'=>1)),'catid'=>array('name'=>L('catid','','content'),'htmltype'=>'input_select_category','data'=>array('type'=>0),'validator'=>array('min'=>0)),'thumb'=>array('name'=>L('thumb','','content'),'htmltype'=>'radio','data'=>array('0'=>L('all_list','','content'),'1'=>L('thumb_list','','content'))),'order'=>array('name'=>L('sort','','content'),'htmltype'=>'select','data'=>array('listorder DESC'=>L('listorder_desc','','content'),'listorder ASC'=>L('listorder_asc','','content'),'id DESC'=>L('id_desc','','content')))),'category'=>array('catid'=>array('name'=>L('catid','','content'),'htmltype'=>'input_select_category','data'=>array('type'=>0))),'relation'=>array('catid'=>array('name'=>L('catid','','content'),'htmltype'=>'input_select_category','data'=>array('type'=>0),'validator'=>array('min'=>1)),'order'=>array('name'=>L('sort','','content'),'htmltype'=>'select','data'=>array('id DESC'=>L('id_desc','','content'),'updatetime DESC'=>L('updatetime_desc','','content'),'listorder ASC'=>L('listorder_asc','','content'))),'relation'=>array('name'=>L('relevant_articles_id','','content'),'htmltype'=>'input'),'keywords'=>array('name'=>L('key_word','','content'),'htmltype'=>'input')),'hits'=>array('catid'=>array('name'=>L('catid','','content'),'htmltype'=>'input_select_category','data'=>array('type'=>0),'validator'=>array('min'=>1)),'day'=>array('name'=>L('day_select','','content'),'htmltype'=>'input','data'=>array('type'=>0))));
    
    }
    //联动菜单
    public function linkage($data){
        $linkageid=intval($data['linkageid']);
        $keyid=intval($data['keyid']);
        if(!$keyid)
            return '';
        if($data['linkages']){
            $infos=$data['linkages'];
        }else{
            $datas=getcache($keyid,'linkage');
            $infos=$datas['data'];
            unset($datas);
        }
        if(!$infos)
            return '';
        if($linkageid>0&&$infos[$linkageid]['arrchildid']){
            $r['linkageids']=explode(',',$infos[$linkageid]['arrchildid']);
            $r['title']=$infos[$linkageid]['name'];
        }elseif($linkageid>0&&!$infos[$linkageid]['arrchildid']&&$infos[$linkageid]['parentid']>0){
            $r['linkageids']=explode(',',$infos[$infos[$linkageid]['parentid']]['arrchildid']);
            $r['title']=$infos[$infos[$linkageid]['parentid']]['name'];
        }else{
            $db=pc_base::load_model('linkage_model');
            $rs=$db->select(array('keyid'=>$keyid,'parentid'=>0),$data='linkageid');
            foreach($rs as $v){
                $r['linkageids'][]=$v['linkageid'];
            }
            unset($rs);
            $r['title']='';
        }
        return $r;
    }
    
    //标签调用
    public function tags($data){
        $modelid=intval($data['modelid']);
        if($modelid<1){
            $catid=intval($data['catid']);
            if(!$this->category[$catid])
                return false;
            $modelid=$this->category[$catid]['modelid'];
        }
        $tags_db=pc_base::load_model('tags_model');
        $r=$tags_db->select(' modelid = '.$modelid,'id,tag,hits',$data['limit'],$data['order']);
        return $r;
    }
    
    //上一篇 下一篇 调用
    public function previous_next($data){
        $id=intval($data['id']);
        if($id<1)
            return false;
        if(isset($data['modelid'])&&$data['modelid']>0){
            $modelid=intval($data['modelid']);
            $this->db->set_model($modelid);
        }elseif(isset($data['catid'])){
            $catid=intval($data['catid']);
            if(!$this->category[$catid])
                return false;
            $this->set_modelid($catid);
            $modelid=$this->modelid;
        }elseif(isset($data['table'])){
            $this->db->table_name=$this->db->db_tablepre.$data['table'];
        }else{
            return false;
        }
        if(isset($data['catid'])){
            $where=' AND catid = '.$data['catid'];
        }
        $previousid=$id-1;
        $nextid=$id+1;
        $fields=empty($data['fields'])?'title,url':$data['fields'];
        $page['previous']=$this->db->get_one(" `id`=$previousid  $where",$fields);
        if(!$page['previous']){
            $page['previous']=$this->db->get_one("`id`>'$id' $where",'title,url','id DESC');
        }
        $page['next']=$this->db->get_one("`id`=$nextid $where",$fields);
        //   var_dump($page['next']);
        if(!$page['next']){
            $page['next']=$this->db->get_one("`id`<'$id' $where",'title,url,id');
        }
        if(empty($page['previous'])){
            $page['previous']=array('title'=>L('first_page'),'thumb'=>IMG_PATH.'nopic_small.gif','url'=>'javascript:alert(\''.L('first_page').'\');');
        }
        if(empty($page['next'])){
            $page['next']=array('title'=>L('last_page'),'thumb'=>IMG_PATH.'nopic_small.gif','url'=>'javascript:alert(\''.L('last_page').'\');');
        }
        //    var_dump($page['next']);
        return $page;
    }
    
    public function moods($data){
        $catid=intval($data['catid']);
        $modelid=intval($data['modelid']);
        if(!$catid&&!$modelid)
            return false;
        if($catid){
            $sql='catid='.$catid;
            $this->set_modelid($catid);
        }else{
            $sql='modelid='.$modelid;
            $this->db->set_model($modelid);
        }
        $mood_db=pc_base::load_model('mood_model');
        $rs=$mood_db->select($sql,'contentid',$data['limit'],$data['order'],'','contentid');
        $ids=implode(',',array_keys($rs));
        $sql=" `id` IN ($ids)";
        $fields=$data['fields']?$data['fields']:'*';
        return $this->db->select($sql,$fields);
    }
    
    //根据表名调取表中的数据
    public function table_list($data){
        if(empty($data['table']))
            return false;
        if(isset($data['where'])){
            $sql=$data['where'];
        }else{
            $arr=array('fields','order','table','limit','num','moreinfo','action','cache','page','urlrule','return','count');
            foreach($data as $k=>$v){
                if(in_array($k,$arr)||$v==='')
                    continue;
                $b=' AND '.$k.'=\''.$v.'\'';
                $s.=$b;
            }
            $sql="1=1";
            $sql.=$s;
        }
        $order=$data['order'];
        $fields=$data['fields']?$data['fields']:'*';
        $this->db->table_name=$this->db->db_tablepre.$data['table'];
        $return=$this->db->select($sql,$fields,$data['limit'],$order,'','id');
        
        //调用副表的数据
        if(isset($data['moreinfo'])&&intval($data['moreinfo'])==1){
            $ids=array();
            foreach($return as $v){
                if(isset($v['id'])&&!empty($v['id'])){
                    $ids[]=$v['id'];
                }else{
                    continue;
                }
            }
            if(!empty($ids)){
                
                $this->db->table_name=$this->db->db_tablepre.$data['table'].'_data';
                $ids=implode('\',\'',$ids);
                $r=$this->db->select("`id` IN ('$ids')",'*','','','','id');
                if(!empty($r)){
                    foreach($r as $k=>$v){
                        if(isset($return[$k]))
                            $return[$k]=array_merge($v,$return[$k]);
                    }
                }
            }
        }
        return $return;
    }
    
    /**
     * 搜索关键词
     * @param $data
     * Add by SouL_Z 2013-4-19 10:00
     */
    public function search_keyword($data){
        $sql='';
        $array=array();
        $keyword=$data['keyword'];
        $pingyin=$data['pingyin'];
        $order=$data['order'];
        $fields=$data['fields']?$data['fields']:'*';
        $search_type=(empty($data['type'])||$data['type']==0)?'':intval($data['type']);
        if($search_type>0){
            $sqlarr[]=" search_type = '$search_type'";
        }
        if(!empty($keyword)){
            $sqlarr[]="keyword = '$keyword'";
        }
        if(!empty($pingyin)){
            $sqlarr[]="pingyin = '$pingyin'";
        }
        if(!empty($sqlarr)){
            $sql=implode(' AND ',$sqlarr);
        }
        $search_keyword_db=pc_base::load_model('search_keyword_model');
        $kw_arr=$search_keyword_db->select($sql,$fields,$data['limit'],$order);
        
        return $kw_arr;
    }

}
