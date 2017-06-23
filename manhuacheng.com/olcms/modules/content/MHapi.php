<?php
defined('IN_OLCMS') or exit('No permission resources.');
//模型缓存路径
define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);
define('DOWNLOADURL','http://www.manhuacheng.com/download/MHCPoint/ManHuaCheng.rar');
pc_base::load_app_func('util','content');
function _urlencode($url){
    $cn="/([x{4e00}-\x{9fa5}]*)/u";
    preg_match_all($cn,$url,$return);
    if($return[1]){
        foreach($return[1] as $list){
            if($list){
                $url=str_replace($list,urlencode($list),$url);
            }
        }
    }
    return $url;
}
class MHapi{
    private $db,$xmlstr,$page,$modelid;
    function __construct(){
        $this->xmlstr='<?xml version="1.0" encoding="utf-8"?>';
        $this->db=pc_base::load_model('content_model');
        $this->categorys=getcache('category_content','commons');
        $this->pages=intval($_GET['pages'])+1;
        $this->modelid=intval($_GET['modelid']);
        $this->db->set_model($this->modelid);
    }
    //$datas = $this->db->listinfo($where,'id desc',$_GET['page']);
    //	http://t.manhuacheng.com/index.php?m=content&c=MHapi&a=newbook&UID=2&Page=0&modelid=12
    public function newbook(){
        $nbdata=$this->db->listinfo('','UpdateTime DESC',$this->pages);
        $ids=array();
        
        if($nbdata){
            foreach($nbdata as $v){
                if(isset($v['id'])&&!empty($v['id'])){
                    $ids[]=$v['id'];
                }else{
                    continue;
                }
            }
            
            //将附表数据也取出来
            if(!empty($ids)){
                $this->db->table_name=$this->db->table_name.'_data';
                $ids=implode('\',\'',$ids);
                $r=$this->db->select("`id` IN ('$ids')",'*','','','','id');
                
                if(!empty($r)){
                    foreach($nbdata as $k=>$v){
                        //剔除h漫画
                        if(isset($r[$v['id']])){
                            if(($_GET['ParentID']==2||$_GET['ParentID']==6||$_GET['ParentID']==7)&&$r[$v['id']]['tid']==11){
                                unset($nbdata[$k]); // = array_merge ( $r [$v ['id']], $nbdata [$k] );
                            }else{
                                $nbdata[$k]=array_merge($r[$v['id']],$nbdata[$k]);
                            }
                        }
                    }
                }
            }
            
            foreach($nbdata as $v){
                preg_match('/[\w-]+\.(com|net|org|gov|cc|biz|info|cn)(\.(cn|hk))*/',$v['thumb'],$matches);
                if($matches[0])
                    $list.='<Book id="'.$v['id'].'" Name="'.$v['title'].'" Author="'.$v['Author'].'" UpdateTime="'.date("Y-m-d H:i:s",$v['updatetime']).'" State="'.$v['state'].'" Image="'._urlencode($v['thumb']).'"><url></url></Book>';
                else
                    $list.='<Book id="'.$v['id'].'" Name="'.$v['title'].'" Author="'.$v['Author'].'" UpdateTime="'.date("Y-m-d H:i:s",$v['updatetime']).'" State="'.$v['state'].'" Image="'.'http://www.manhuacheng.com'._urlencode($v['thumb']).'"><url></url></Book>';
            }
        }else
            echo $this->xmlstr.'<BookList Count="0"></BookList>';
        echo $this->xmlstr.'<BookList Count="100" Page="'.($this->pages-1).'">'.$list.'</BookList>';
    }
    //	http://t.manhuacheng.com/index.php?m=content&c=MHapi&a=chapter&id=33&UID=2&NO=%d&typeid=11&modelid=13
    //	http://t.manhuacheng.com/index.php?m=content&c=MHapi&a=chapter&UID=2&Page=0&modelid=12&id=23&typeid=11
    public function chapter(){
        
        $id=$_GET['id'];
        $userid=$_GET['UID'];
        $typeid=$_GET['typeid'];
        if($id){
            if($typeid==11){
                if($userid){
                    $this->member_db=pc_base::load_model('member_model');
                    $up=$this->member_db->get_one('userid='.$userid);
                    //						
                    if($up['amount']>1&&$_GET['NO']){
                        //判断分数是否够
                        $this->subpoint($userid,2);
                        $cdata=$this->db->select('id='.$id);
                    }elseif(!$_GET['NO']){
                        $cdata=$this->db->select('id='.$id);
                    }else{
                        //凑成一张图片
                        echo '<?xml version="1.0" encoding="utf-8"?>
		<Chapter id="" Point="" Name="" UpdateTime="" Url="">
			<Page id="1" Url="http://www.manhuacheng.com/statics/images/noenoughpoint.jpg" />
		</Chapter>';
                        exit();
                    }
                }else{
                    echo '<?xml version="1.0" encoding="utf-8"?>
		<Chapter id="" Point="" Name="" UpdateTime="" Url="">
			<Page id="1" Url="http://www.manhuacheng.com/statics/images/noenoughpoint.jpg" />
		</Chapter>';
                    exit();
                }
            }else{
                $cdata=$this->db->select('typeid<11 and id='.$id);
            }
            if($cdata){
                //将附表数据也取出来
                $this->db->table_name=$this->db->table_name.'_data';
                $r=$this->db->select("`id` = '$id'",'*','','','','id');
                if(!empty($r)){
                    if($cdata[0]['id']==$r[$id]['id'])
                        $cdata=array_merge($r[$id],$cdata[0]);
                }
                //构造章节
                $l='<Chapter Point="2" id="'.$cdata['id'].'" Name="'.$cdata['title'].'" UpdateTime="'.$cdata['updatetime'].'" Url="'.$cdata['Url'].'">';
                $cdata['CartoonImage']=eval("return ".stripcslashes($cdata['CartoonImage']).";");
                foreach($cdata['CartoonImage'] as $key=>$v){
                    preg_match('/[\w-]+\.(com|net|org|gov|cc|biz|info|cn|io)(\.(cn|hk))*/',$v['url'],$matches);
                    if($matches[0])
                        $l.='<Page id="'.$key.'" Url="'._urlencode($v['url']).'" Referer="'._urlencode($v['url']).'" />';
                    else
                        $l.='<Page id="'.$key.'" Url="'."http://manhuacheng.com".$v['url'].'" Referer="'."http://manhuacheng.com".$v['url'].'" />';
                }
                echo $this->xmlstr.$l.'</Chapter>';
            }else
                echo $this->xmlstr.'<Chapter Count="0"></Chapter>';
        }else{
            echo $this->xmlstr.'<Chapter></Chapter>';
        }
    }
    public function char(){
        if(ord($_GET['letter'])>64&&ord($_GET['letter'])<91||ord($_GET['letter'])==48){
            $tablename=$this->db->table_name;
            $this->db->table_name=$this->db->table_name.'_data';
            //渠道用的
            if($_GET['ParentID']==2||$_GET['ParentID']==6||$_GET['ParentID']==7){
                $num=$this->db->count('tid<11 and CharIndex=\''.$_GET['letter'].'\'');
                $chardata=$this->db->listinfo('tid<11 and CharIndex=\''.$_GET['letter'].'\'','id DESC',$this->pages);
            }else{
                $num=$this->db->count('CharIndex=\''.$_GET['letter'].'\'');
                $chardata=$this->db->listinfo('CharIndex=\''.$_GET['letter'].'\'','id DESC',$this->pages);
            }
            if($chardata){
                $ids=array();
                
                foreach($chardata as $v){
                    if(isset($v['id'])&&!empty($v['id'])){
                        $ids[]=$v['id'];
                    }else{
                        continue;
                    }
                }
                
                //将附表数据也取出来
                if(!empty($ids)){
                    $this->db->table_name=$tablename;
                    $ids=implode('\',\'',$ids);
                    $r=$this->db->select("`id` IN ('$ids')",'*','','','','id');
                    
                    if(!empty($r)){
                        foreach($chardata as $k=>$v){
                            if(isset($r[$v['id']]))
                                $chardata[$k]=array_merge($r[$v['id']],$chardata[$k]);
                        }
                    }
                }
                
                foreach($chardata as $key=>$v){
                    preg_match('/[\w-]+\.(com|net|org|gov|cc|biz|info|cn)(\.(cn|hk))*/',$v['thumb'],$matches);
                    if($key==0){
                        if($matches[0])
                            $list.='<Book NO="1" id="'.$v['id'].'" Name="'.$v['title'].'" Author="'.$v['Author'].'" UpdateTime="'.date("Y-m-d H:i:s",$v['updatetime']).'" State="'.$v['state'].'" Image="'._urlencode($v['thumb']).'"><url></url></Book>';
                        else
                            $list.='<Book NO="1" id="'.$v['id'].'" Name="'.$v['title'].'" Author="'.$v['Author'].'" UpdateTime="'.date("Y-m-d H:i:s",$v['updatetime']).'" State="'.$v['state'].'" Image="'."http://manhuacheng.com"._urlencode($v['thumb']).'"><url></url></Book>';
                        //							$list .= '<Book id="' . $v ['id'] . '" Name="' . $v ['title'] . '" Author="' . $v ['Author'] . '" UpdateTime="' . date ( "Y-m-d H:i:s", $v ['updatetime'] ) . '" State="' . $v ['state'] . '" Image="' . "http://manhuacheng.com".$v ['thumb'] . '">';
                    }else{
                        if($matches[0])
                            $list.='<Book  id="'.$v['id'].'" Name="'.$v['title'].'" Author="'.$v['Author'].'" UpdateTime="'.date("Y-m-d H:i:s",$v['updatetime']).'" State="'.$v['state'].'" Image="'._urlencode($v['thumb']).'"><url></url></Book>';
                        else
                            $list.='<Book  id="'.$v['id'].'" Name="'.$v['title'].'" Author="'.$v['Author'].'" UpdateTime="'.date("Y-m-d H:i:s",$v['updatetime']).'" State="'.$v['state'].'" Image="'."http://manhuacheng.com"._urlencode($v['thumb']).'"><url></url></Book>';
                    
                    }
                    //						$list .= '<Book id="' . $v ['id'] . '" Name="' . $v ['title'] . '" Author="' . $v ['Author'] . '" UpdateTime="' . date ( "Y-m-d H:i:s", $v ['updatetime'] ) . '" State="' . $v ['state'] . '" Image="' . $v ['thumb'] . '"><url></url></Book>';
                

                }
                echo $this->xmlstr.'<BookList Count="'.$num.'" Page="'.($this->pages-1).'">'.$list.'</BookList>';
            }else
                echo $this->xmlstr.'<BookList></BookList>';
        }elseif(!$_GET['letter']){
            echo 'error';
        }
    
    }
    //http://t.manhuacheng.com/index.php?m=content&c=MHapi&a=mhlist&UID=2&Page=0&type=11&modelid=12
    public function mhlist(){
        $tablename=$this->db->table_name;
        if(intval($_GET['type'])<11&&intval($_GET['type'])>-1){
            $num=$this->db->count('type_id=\''.$_GET['type'].'\'');
            $mhdata=$this->db->listinfo('type_id=\''.$_GET['type'].'\'','updatetime desc',$this->pages);
            $ids=array();
            if($mhdata){
                foreach($mhdata as $v){
                    if(isset($v['id'])&&!empty($v['id'])){
                        $ids[]=$v['id'];
                    }else{
                        continue;
                    }
                }
                //将附表数据也取出来
                if(!empty($ids)){
                    $this->db->table_name=$this->db->table_name.'_data';
                    //  $this->db->table_name=$tablename;
                    $ids=implode('\',\'',$ids);
                    $r=$this->db->select("`id` IN ('$ids')",'*','','','','id');
                    if(!empty($r)){
                        foreach($mhdata as $k=>$v){
                            if(isset($r[$v['id']]))
                                $mhdata[$k]=array_merge($r[$v['id']],$mhdata[$k]);
                        }
                    }
                }
                foreach($mhdata as $v){
                    preg_match('/[\w-]+\.(com|net|org|gov|cc|biz|info|cn)(\.(cn|hk))*/',$v['thumb'],$matches);
                    if($matches[0])
                        $list.='<Book id="'.$v['id'].'" Name="'.$v['title'].'" Author="'.$v['Author'].'" UpdateTime="'.date("Y-m-d H:i:s",$v['updatetime']).'" State="'.$v['state'].'" Image="'._urlencode($v['thumb']).'">';
                    else
                        $list.='<Book id="'.$v['id'].'" Name="'.$v['title'].'" Author="'.$v['Author'].'" UpdateTime="'.date("Y-m-d H:i:s",$v['updatetime']).'" State="'.$v['state'].'" Image="'."http://manhuacheng.com"._urlencode($v['thumb']).'">';
                        //					$list .= '<Book id="' . $v ['id'] . '" Name="' . $v ['title'] . '" Author="' . $v ['Author'] . '" UpdateTime="' . date ( "Y-m-d H:i:s", $v ['updatetime'] ) . '" State="' . $v ['state'] . '" Image="' . $v ['thumb'] . '">';
                    $list.='<url></url></Book>';
                }
                echo $this->xmlstr.'<BookList Count="'.$num.'" Page="'.($this->pages-1).'">'.$list.'</BookList>';
            }
            echo $this->xmlstr.'<BookList></BookList>';
        }elseif(intval($_GET['type'])==11&&$_GET['UID']){
            $num=$this->db->count('tid=11');
            $mhdata=$this->db->listinfo('tid=11','',$this->pages);
            $ids=array();
            if($mhdata){
                foreach($mhdata as $v){
                    if(isset($v['id'])&&!empty($v['id'])){
                        $ids[]=$v['id'];
                    }else{
                        continue;
                    }
                }
                //将附表数据也取出来
                if(!empty($ids)){
                    $this->db->table_name=$tablename;
                    $ids=implode('\',\'',$ids);
                    $r=$this->db->select("`id` IN ('$ids')",'*','','','','id');
                    //      var_dump($ids);
                    //     var_dump($r);
                    if(!empty($r)){
                        foreach($mhdata as $k=>$v){
                            if(isset($r[$v['id']]))
                                $mhdata[$k]=array_merge($r[$v['id']],$mhdata[$k]);
                        }
                    }
                }
                foreach($mhdata as $v){
                    preg_match('/[\w-]+\.(com|net|org|gov|cc|biz|info|cn)(\.(cn|hk))*/',$v['thumb'],$matches);
                    if($matches[0])
                        $list.='<Book id="'.$v['id'].'" Name="'.$v['title'].'" Author="'.$v['Author'].'" UpdateTime="'.date("Y-m-d H:i:s",$v['updatetime']).'" State="'.$v['state'].'" Image="'._urlencode($v['thumb']).'">';
                    else
                        $list.='<Book id="'.$v['id'].'" Name="'.$v['title'].'" Author="'.$v['Author'].'" UpdateTime="'.date("Y-m-d H:i:s",$v['updatetime']).'" State="'.$v['state'].'" Image="'."http://manhuacheng.com"._urlencode($v['thumb']).'">';
                        
                    //$list .= '<Book id="' . $v ['id'] . '" Name="' . $v ['title'] . '" Author="' . $v ['Author'] . '" UpdateTime="' . date ( "Y-m-d H:i:s", $v ['updatetime'] ) . '" State="' . $v ['state'] . '" Image="' . $v ['thumb'] . '">';
                    $list.='<url></url></Book>';
                }
                echo $this->xmlstr.'<BookList Count="'.$num.'" Page="'.($this->pages-1).'">'.$list.'</BookList>';
            }
            echo $this->xmlstr.'<BookList></BookList>';
        }else{
            echo 'error';
        }
    }
    public function search(){
        if($_GET['title']&&!empty($_GET['title'])){
            $tablename=$this->db->table_name;
            $title=iconv("gbk","utf-8",$_GET['title']);
            if($_GET['type']==1){
                $this->db->table_name=$this->db->table_name.'_data';
                if($_GET['ParentID']==2||$_GET['ParentID']==6||$_GET['ParentID']==7){
                    $num=$this->db->count("tid<11 and Author like '%$title%'");
                    $searchdata=$this->db->listinfo("tid<11 and Author like '%$title%'",'',$this->pages);
                }else{
                    $num=$this->db->count("Author like '%$title%'");
                    $searchdata=$this->db->listinfo("Author like '%$title%'",'',$this->pages);
                
                }
                if($searchdata){
                    
                    $this->db->table_name=$tablename;
                    foreach($searchdata as $v){
                        if(isset($v['id'])&&!empty($v['id'])){
                            $ids[]=$v['id'];
                        }else{
                            continue;
                        }
                    }
                    //将附表数据也取出来
                    if(!empty($ids)){
                        
                        $ids=implode('\',\'',$ids);
                        $r=$this->db->select("`id` IN ('$ids')",'*','','','','id');
                        if(!empty($r)){
                            foreach($searchdata as $k=>$v){
                                if(isset($r[$v['id']]))
                                    $searchdata[$k]=array_merge($r[$v['id']],$searchdata[$k]);
                            }
                        }
                    }
                    
                    foreach($searchdata as $v){
                        preg_match('/[\w-]+\.(com|net|org|gov|cc|biz|info|cn)(\.(cn|hk))*/',$v['thumb'],$matches);
                        if($matches[0])
                            $list.='<Book id="'.$v['id'].'" Name="'.$v['title'].'" Author="'.$v['Author'].'" UpdateTime="'.date("Y-m-d H:i:s",$v['updatetime']).'" State="'.$v['state'].'" Image="'._urlencode($v['thumb']).'"/>';
                        else
                            $list.='<Book id="'.$v['id'].'" Name="'.$v['title'].'" Author="'.$v['Author'].'" UpdateTime="'.date("Y-m-d H:i:s",$v['updatetime']).'" State="'.$v['state'].'" Image="'."http://manhuacheng.com"._urlencode($v['thumb']).'"/>';
                        
                    //						$list .= '<Book id="' . $v ['id'] . '" Name="' . $v ['title'] . '" Author="' . $v ['Author'] . '" UpdateTime="' . date ( "Y-m-d H:i:s", $v ['updatetime'] ) . '" State="' . $v ['state'] . '" Image="' . $v ['thumb'] . '"><url></url></Book>';
                    }
                    echo $this->xmlstr.'<BookList Count="'.$num.'" Page="'.($this->pages-1).'">'.$list.'</BookList>';
                
                }else
                    echo $this->xmlstr.'<BookList></BookList>';
            }elseif($_GET['type']==0){
                $num=$this->db->count("title like '%$title%'");
                $searchdata=$this->db->listinfo("title like '%$title%'",'',$this->pages);
                
                if($searchdata){
                    $this->db->table_name=$this->db->table_name.'_data';
                    
                    foreach($searchdata as $v){
                        if(isset($v['id'])&&!empty($v['id'])){
                            $ids[]=$v['id'];
                        }else{
                            continue;
                        }
                    }
                    //将附表数据也取出来
                    if(!empty($ids)){
                        
                        $ids=implode('\',\'',$ids);
                        $r=$this->db->select("`id` IN ('$ids')",'*','','','','id');
                        if(!empty($r)){
                            foreach($searchdata as $k=>$v){
                                if(($_GET['ParentID']==2||$_GET['ParentID']==6||$_GET['ParentID']==7)&&$r[$v['id']]['tid']==11){
                                    unset($searchdata[$k]); // = array_merge ( $r [$v ['id']], $nbdata [$k] );
                                }else{
                                    $searchdata[$k]=array_merge($r[$v['id']],$searchdata[$k]);
                                }
                                //								if (isset ( $r [$v ['id']] ))
                            //									$searchdata [$k] = array_merge ( $r [$v ['id']], $searchdata [$k] );
                            }
                        }
                    }
                    foreach($searchdata as $v){
                        preg_match('/[\w-]+\.(com|net|org|gov|cc|biz|info|cn)(\.(cn|hk))*/',$v['thumb'],$matches);
                        if($matches[0])
                            $list.='<Book id="'.$v['id'].'" Name="'.$v['title'].'" Author="'.$v['Author'].'" UpdateTime="'.date("Y-m-d H:i:s",$v['updatetime']).'" State="'.$v['state'].'" Image="'._urlencode($v['thumb']).'"><url></url></Book>';
                        else
                            $list.='<Book id="'.$v['id'].'" Name="'.$v['title'].'" Author="'.$v['Author'].'" UpdateTime="'.date("Y-m-d H:i:s",$v['updatetime']).'" State="'.$v['state'].'" Image="'."http://manhuacheng.com"._urlencode($v['thumb']).'"><url></url></Book>';
                        //$list .= '<Book id="' . $v ['id'] . '" Name="' . $v ['title'] . '" Author="' . $v ['Author'] . '" UpdateTime="' . date ( "Y-m-d H:i:s", $v ['updatetime'] ) . '" State="' . $v ['state'] . '" Image="' . $v ['thumb'] . '"><url></url></Book>';
                    }
                    echo $this->xmlstr.'<BookList Count="'.$num.'" Page="'.($this->pages-1).'">'.$list.'</BookList>';
                
                }else
                    echo $this->xmlstr.'<BookList></BookList>';
            
            }
        }
    }
    public function manhua(){
        if($_GET['id']){
            $id=intval($_GET['id']);
            $this->db->set_model(12);
            $mhdata=$this->db->get_one('id='.$_GET['id']);
            
            if($mhdata){
                $this->db->table_name=$this->db->table_name.'_data';
                $r=$this->db->select("`id` = ".$_GET['id'],'*','','','','id');
                //      var_dump($r);
                if(!empty($r)){
                    if($mhdata['id']==$r[$_GET['id']]['id'])
                        $mhdata=array_merge($r[$_GET['id']],$mhdata);
                }
                $this->db->set_model(13);
                $r2=$this->db->select("`manhuaid` = ".$mhdata['id'],'*','','listorder ASC','title','id');
                //         var_dump($r2);
                preg_match('/[\w-]+\.(com|net|org|gov|cc|biz|info|cn)(\.(cn|hk))*/',$mhdata['thumb'],$matches);
                if($matches[0])
                    $l='<Book Typeid="'.$mhdata['tid'].'" id="'.$mhdata['id'].'" Author="'.$mhdata['Author'].'" Area="'.$mhdata['CartoonAreaID'].'" State="'.$mhdata['state'].'" Rem="'.$mhdata['description'].'" Name="'.$mhdata['title'].'" UpdateTime="'.$mhdata['updatetime'].'" Image="'.$mhdata['thumb'].'">';
                else
                    $l='<Book Typeid="'.$mhdata['tid'].'" id="'.$mhdata['id'].'" Author="'.$mhdata['Author'].'" Area="'.$mhdata['CartoonAreaID'].'" State="'.$mhdata['state'].'" Rem="'.$mhdata['description'].'" Name="'.$mhdata['title'].'" UpdateTime="'.$mhdata['updatetime'].'" Image="'."http://www.manhuacheng.com".$mhdata['thumb'].'">';
                
                $r3=array_slice($r2,0,count($r2));
                foreach($r3 as $key=>$v){
                    if($key==0)
                        $l1.='<Chapter NO="0" id="'.$v['id'].'" Point="2" Name="'.$v['title'].'" UpdateTime="'.$v['updatetime'].'" Url="" />';
                    else
                        $l1.='<Chapter NO="1" id="'.$v['id'].'" Point="2" Name="'.$v['title'].'" UpdateTime="'.$v['updatetime'].'" Url="" />';
                
                }
                echo $this->xmlstr.$l.$l1.'</Book>';
            
            }else
                echo $this->xmlstr.'<Chapter Count="0"></Chapter>';
        
        }
    
    }
    public function recommand(){
        if($_GET['id']){
            $id=intval($_GET['id']);
            $this->db->set_model(12);
            $this->db->table_name=$this->db->table_name.'_data';
            $this->db->update(array('recnum'=>'+=1'),array('id'=>$id));
            $r=$this->db->get_one('id='.$id,'id,recnum');
            if($r)
                echo $r['recnum'];
        }else
            'error';
    }
    
    /*
	  * 邀请下载链接
	  */
    public function InvitedUserRegistration(){
        if($_GET['u'])
            $userid=$_GET['u'];
        if($userid){
            $ip=ip();
            $this->ic_db=pc_base::load_model('invitclick_model');
            $url=isset($_COOKIE['sYQDUGqqzHcode_url'])?$_COOKIE['sYQDUGqqzHcode_url']:'auto?999';
            $time=time();
            $this->m_db=pc_base::load_model('member_model');
            $r=$this->ic_db->get_one('userid='.$userid.' and ip=\''.$ip.'\'','ip,inputime','inputime DESC');
            if($r){
                if(($time-$r['inputime']>3600)&&strpos($url,'auto?')===FALSE){
                    $this->m_db->update(array('amount'=>'+=1'),array('userid'=>$userid));
                    $this->ic_db->insert(array('userid'=>$userid,'ip'=>$ip,'inputime'=>$time,'url'=>$url));
                    header("Location:".DOWNLOADURL);
                    exit();
                }else{
                    header("Location:".DOWNLOADURL);
                    exit();
                }
            
            }elseif(strpos($url,'auto?')===FALSE){
                //并给userid加金币
                $this->m_db->update(array('amount'=>'+=1'),array('userid'=>$userid));
                $this->ic_db->insert(array('userid'=>$userid,'ip'=>$ip,'inputime'=>$time,'url'=>$url));
                //			
                header("Location:".DOWNLOADURL);
                exit();
            }else{
                header("Location:".DOWNLOADURL);
                exit();
            }
        }else{
            header("Location:".DOWNLOADURL);
            exit();
        }
    }
    public function addpoint(){
        //如有uname加积分
        $this->member_db=pc_base::load_model('member_model');
        if(isset($_GET['u']))
            $userid=$_GET['u'];
        $p=intval($_GET['p']);
        if($p>0){
            if(isset($userid)){
                //会员积分+5点
                //加一步要在这个登录日志中查询到用户
                $this->member_db->update(array('point'=>'+='.$p,'amount'=>'+='.$p),array('userid'=>$userid));
            }
        }
    
    }
    public function subpoint($uid,$point){
        //如有uname加积分
        $this->member_db=pc_base::load_model('member_model');
        $this->member_db->update(array('amount'=>'-='.$point),array('userid'=>$uid));
    }
    public function getMembers(){
        $username=$_GET['u'];
        $password=$_GET['p'];
        $this->member_db=pc_base::load_model('member_model');
        $r=$this->member_db->get_one(array('username'=>$username));
        $password=md5(strtolower($password).$r['encrypt']);
        if($r['password']==$password){
            echo $this->xmlstr.'<User ID="'.$r['userid'].'" Name="'.$r['nickname'].'" Img="http://manhuacheng.com'.get_memberavatar($r['userid']).'" Money="'.$r['amount'].'" Groupname="'.$r['groupname'].'" Point="'.$r['point'].'">'.'</User>';
        }else{
            echo 'error';
        }
    
    }
    
//	public function 


}
?>