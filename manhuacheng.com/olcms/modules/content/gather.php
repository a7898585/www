<?php
defined('IN_OLCMS') or exit('No permission resources.');
//模型缓存路径
define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);
//定义在单独操作内容的时候，同时更新相关栏目页面
//define('RELATION_HTML',true);
pc_base::load_sys_class('form','',0);
pc_base::load_sys_class('format','',0);

class gather{
    private $db;
    public $categorys,$nianji_array,$ticai_array,$shuce_array;
    public function __construct(){
        $this->db=pc_base::load_model('content_model');
    }
    private function img_down($str,$folder=null){
        $ck=substr($str,0,4);
        if($ck=='http'){
            $kk=parse_url($str);
            if($fp=@fsockopen($kk['host'],empty($kk['port'])?80:$kk['port'],$error)){
                fputs($fp,"GET ".(empty($kk['path'])?'/':$kk['path'])." HTTP/1.1\r\n");
                fputs($fp,"Host:$kk[host]\r\n\r\n");
                while(!feof($fp)){
                    $tmp=fgets($fp);
                    if(trim($tmp)==''){
                        break;
                    }else if(preg_match('/Content-Length:(.*)/si',$tmp,$arr)){
                        $kkk=trim($arr[1]);
                    }
                }
            }
            if($kkk>1024){
                if(!$folder){
                    $newfolder="uploadfile/".date("Ymd");
                }else if($folder){
                    $newfolder="uploadfile/{$folder}/".date("Ymd");
                }
                if(!file_exists($newfolder)||!is_dir($newfolder)){
                    if(@mkdir($newfolder,0777)){
                        @chmod($newfolder,0777);
                    }
                }
                $b=file_get_contents($str);
                $pos=strrpos($str,".");
                $ext=substr($str,$pos);
                $name='s'.date('Ymdhis').rand(100,999);
                $m="".$newfolder."/".$name.$ext;
                $n="/".$newfolder."/".$name.$ext;
                file_put_contents($m,$b);
                return $n;
            }
        }else
            return $str;
    
    }
    private function format_content($content){
        $content=str_ireplace(array('</font>','&nbsp;','</div>','</span>','<div'),array('',' ','</p>','','<p'),$content);
        $content=preg_replace('/<font([^>]+)>/is','',$content);
        $content=preg_replace('/<!--([^>]+)-->/is','',$content);
        $content=preg_replace('/<spa([^>]+)>/is','',$content);
        $content=preg_replace('/<a([^>]+)>/is','',$content);
        $content=preg_replace('/<p([^>]+)>/is','<p>',$content);
        $content=preg_replace('/>([\n\r\t ]+)/is','>',$content);
        $content=preg_replace('/([\n\r\t ]+)</is','<',$content);
        $content=preg_replace('/([a-z0-9\.]+)\.(com|cn|net|org|info|mobi|la)/is','',$content);
        //$content = str_replace ( "\\", "", $content );
        //$content = str_replace ( "'", "\'", $content );
        $content=str_ireplace(array("<p></p>",'http://','</a>'),'',$content);
        $content=str_ireplace(array("<!--",'<p><img'),array('','<p align="center"><img'),$content);
        return $content;
    }
    //字符转义，这个是否要用，看服务配置
    private function format_str($str){
        $str=_trim($str);
        $str=str_replace("'","\'",$str);
        $str=str_replace('\\\\','\\',$str);
        return $str;
    }
    public function content(){
        $info=$_POST['info'];
        foreach($info as $key=>$v){
            $info[$key]=_trim($v);
            if(strpos($v,'[标签:'))
                unset($info[$key]);
        }
        unset($_POST['info']);
        if(!isset($info['catid']))
            exit('发布失败');
        $categorys=getcache('category_content','commons');
        if(!ctype_digit($info['catid'])){
            foreach($categorys as $v){
                if($v['catname']==$info['catid']){
                    $info['catid']=$v['catid'];
                    break;
                }
            }
        }
        $modelid=$categorys[$info['catid']]['modelid'];
        $info['content']=$this->format_str($this->format_content($info['content']));
        if(empty($info['title'])||!$info['content']{30})
            exit('发布失败');
        $info['status']=99;
        //$info ['views'] = mt_rand ( 1, 100 );		
        $info['inputtime']=strtotime($info['inputtime']);
        if(!$info['inputtime'])
            $info['inputtime']=SYS_TIME;
        $info['updatetime']=SYS_TIME;
        $_POST['add_introduce']=1; //当描述为空时，是否从内容中自动截取描述
        $_POST['introcude_length']=200; //截取描述的长度
        $_POST['auto_thumb']=1; //是否截取图片作为缩略图
        $_POST['auto_thumb_no']=1; //截取第几张图片作为缩略图
        $this->db->set_model($modelid);
        $this->db->add_content($info);
        exit('发布成功');
    }
    /*
     * 新版发布接口
     */
    public function gather_manhua(){
        if(!$_POST){
            exit('NoneData');
        }
        $info['web_url']=$_POST['manhua_url'];
        $info['title']=$_POST['title'];
        $this->db->table_name='tt_Cartoon';
        $manhua=$this->db->get_one(array('title'=>$info['title']));
        if(!$manhua){
            //二次检测，以免意外
            sleep(2);
            $manhua=$this->db->get_one(array('title'=>$info['title']));
        }
        if(!$manhua){
            $info['catid']=13;
            $info['thumb']=$_POST['img'];
            $info['content']=$_POST['info'];
            $info['posids']=1;
            $info['status']=99;
            $info['Author']=$_POST['author'];
            $info['CharIndex']=strtoupper($_POST['py'])?strtoupper($_POST['py']):substr($info['title'],0,1);
            $info['CartoonAreaID']=$_POST['area'];
            $info['tid']=$_POST['type_id'];
            $info['recnum']=1;
            $info['sab']=1;
            $info['Char_Index']=$info['CharIndex'];
            $info['username']='mhcadmin';
            switch($_POST['state']){
                case "连载":
                    $info['state']='0';
                    break;
                case "完结":
                    $info['state']='1';
                    break;
                case "已完结":
                    $info['state']='1';
                    break;
                default:
                    $info['state']='0';
            }
            switch($_POST['area']){
                case "日本":
                    $info['region']='1';
                    break;
                case "港台":
                    $info['region']='2';
                    break;
                case "内地":
                    $info['region']='3';
                    break;
                case "欧美":
                    $info['region']='4';
                    break;
                default:
                    $info['region']='5';
            }
            $info['type_id']=$_POST['type_id'];
            if($_POST['type']){
                $array['格斗']=2;
                $array['竞技']=2;
                $array['少女']=3;
                $array['爱情']=3;
                $array['战争']=1;
                $array['机战']=4;
                $array['科幻']=4;
                $array['仙侠']=4;
                $array['魔法']=4;
                $array['恐怖']=8;
                $array['侦探']=7;
                $array['悬疑']=7;
                $array['后宫']=3;
                $array['冒险']=1;
                $array['热血']=1;
                $array['少年']=1;
                $array['武侠']=2;
                $array['神鬼']=4;
                $array['魔幻']=4;
                $array['校园']=6;
                $array['喜剧']=6;
                $array['搞笑']=6;
                $array['职场']=6;
                $array['耽美']=9;
                $array['美食']=6;
                $array['励志']=6;
                $array['伪娘']=6;
                $array['治愈']=6;
                $array['东方']=6;
                $array['欢乐向']=6;
                
                if($array[$_POST['type']]){
                    $info['type_id']=$array[$_POST['type']];
                }
            }
            $info['thumb']=$this->img_down($_POST['img']);
            $info['inputtime']=$_POST['updatetime'];
            $info['updatetime']=SYS_TIME;
            $info['content']=$_POST['info'];
            $categorys=getcache('category_content','commons');
            $modelid=$categorys[$info['catid']]['modelid'];
            $this->db->set_model($modelid);
            $manhuaId=$this->db->add_content($info);
        }else{
            switch($_POST['state']){
                case "连载":
                    $state='0';
                    break;
                case "完结":
                    $state='1';
                    break;
                case "已完结":
                    $state='1';
                    break;
                default:
                    $state='0';
            }
            $this->db->table_name='tt_Cartoon';
            $this->db->update(array('updatetime'=>SYS_TIME,'state'=>$state),"id='{$manhua['id']}'");
            $manhuaId=$manhua['id'];
        }
        unset($info);
        //发布漫画详细
        $info['web_url']=$_POST['gather'];
        $this->db->table_name='tt_CartoonDetail';
        $manhua=$this->db->get_one(array('web_url'=>$info['web_url']));
        if(!$manhua){
            $info['catid']=14;
            $info['title']=str_replace('话正式版','话',$_POST['viewname']);
            preg_match('/(\d+)/is',$info['title'],$listorder);
            $info['listorder']=intval($listorder[1]);
            $info['status']=99;
            $info['username']='mhcadmin';
            $info['inputtime']=$info['updatetime']=time();
            $info['username']='mhcadmin';
            $info['manhuaid']=$manhuaId;
            $info['refer']=$_POST['refer'];
            $images=explode('|||',$_POST['imgs']);
            if($images){
                foreach($images as $key=>$list){
                    $cartonImg[]=array("url"=>$list,"alt"=>$info['title'].($key+1));
                }
            }
            $info['CartoonImage']=array2string($cartonImg);
            $this->db->set_model(13);
            $result=$this->db->add_content($info);
            exit('发布成功:'.$result);
        }
        exit('发布失败');
    }
    /*
     * 漫画接口,只存在漫画地址
     */
    public function gather_manhua_url(){
        if(!$_POST){
            exit('NoneData');
        }
        $info['web_url']=$_POST['gather'];
        $info['title']=$_POST['title'];
        $this->db->table_name='tt_Cartoon';
        $manhua=$this->db->get_one(array('title'=>$info['title']));
        if(!$manhua){
            //二次检测，以免意外
            sleep(2);
            $manhua=$this->db->get_one(array('title'=>$info['title']));
        }
        if(!$manhua){
            $info['catid']=13;
            $info['thumb']=$_POST['img'];
            $info['content']=$_POST['info'];
            $info['posids']=1;
            $info['status']=99;
            $info['Author']=$_POST['author'];
            $info['CharIndex']=strtoupper($_POST['py'])?strtoupper($_POST['py']):substr($info['title'],0,1);
            $info['CartoonAreaID']=$_POST['area'];
            $info['tid']=$_POST['type_id'];
            $info['recnum']=1;
            $info['sab']=1;
            $info['Char_Index']=$info['CharIndex'];
            $info['username']='mhcadmin';
            switch($_POST['state']){
                case "连载":
                    $info['state']='0';
                    break;
                case "完结":
                    $info['state']='1';
                    break;
                default:
                    $info['state']='0';
            }
            switch($_POST['area']){
                case "日本":
                    $info['region']='1';
                    break;
                case "港台":
                    $info['region']='2';
                    break;
                case "内地":
                    $info['region']='3';
                    break;
                case "欧美":
                    $info['region']='4';
                    break;
                default:
                    $info['region']='5';
            }
            $info['type_id']=$_POST['type_id'];
            $info['thumb']=$this->img_down($_POST['img']);
            $info['inputtime']=$_POST['updatetime'];
            $info['updatetime']=SYS_TIME;
            $info['content']=$_POST['info'];
            $categorys=getcache('category_content','commons');
            $modelid=$categorys[$info['catid']]['modelid'];
            $this->db->set_model($modelid);
            $manhuaId=$this->db->add_content($info);
        }else{
            switch($_POST['state']){
                case "连载":
                    $state='0';
                    break;
                case "完结":
                    $state='1';
                    break;
                default:
                    $state='0';
            }
            $this->db->table_name='tt_Cartoon';
            $this->db->update(array('updatetime'=>SYS_TIME,'state'=>$state),"id='{$manhua['id']}'");
            $manhuaId=$manhua['id'];
        }
        unset($info);
        //发布漫画详细
        //开始修改
        $zhangjie=$_POST['zhangjie'];
        $zhengjieList=explode('|||',$zhangjie);
        //     var_dump($zhengjieList);exit();
        if(is_array($zhengjieList)){
            $info['catid']=14;
            foreach($zhengjieList as $list){
                $mt=explode('@@',$list);
                $info['web_url']=$mt[0];
                $this->db->table_name='tt_CartoonDetail';
                $manhua=$this->db->get_one(array('web_url'=>$info['web_url']));
                if(!$manhua){
                    $info['title']=$mt[1];
                    preg_match('/(\d+)/is',$info['title'],$listorder);
                    $info['listorder']=intval($listorder[1]);
                    $info['status']=99;
                    $info['username']='mhcadmin';
                    $info['inputtime']=$info['updatetime']=time();
                    $info['username']='mhcadmin';
                    $info['manhuaid']=$manhuaId;
                    $info['CartoonImage']='';
                    $this->db->set_model(13);
                    $result=$this->db->add_content($info);
                    //   var_dump($result);
                }
            }
            exit('发布成功:'.$result);
        }
        exit('发布失败');
    }
    /*
     * 新版发布接口
     */
    public function gather_dongman(){
        if(!$_POST){
            exit('NoneData');
        }
        $info['web_url']=$_POST['dongman_url'];
        $info['title']=$_POST['title'];
        $this->db->table_name='tt_dongman';
        $dongman=$this->db->get_one(array('title'=>$info['title']));
        $img=$dongman['thumb'];
        if(!$dongman){
            //二次检测，以免意外
            sleep(2);
            $dongman=$this->db->get_one(array('title'=>$info['title']));
        }
        if(!$dongman){
            $info['catid']=$_POST['catid'];
            $info['content']=$_POST['info'];
            $info['posids']=1;
            $info['status']=99;
            $info['username']='mhcadmin';
            $info['year']=intval($_POST['shouboshijian']);
            switch($_POST['state']){
                case "连载":
                    $info['state']='0';
                    break;
                case "连载中":
                    $info['state']='0';
                    break;
                case "完结":
                    $info['state']='1';
                    break;
                default:
                    $info['state']='0';
            }
            switch($_POST['area']){
                case "日韩":
                    $info['CartoonArea']='3361';
                    break;
                case "港台":
                    $info['CartoonArea']='3362';
                    break;
                case "内地":
                    $info['CartoonArea']='3364';
                    break;
                case "欧美":
                    $info['CartoonArea']='3363';
                    break;
                default:
                    $info['CartoonArea']='3365';
            }
            if($_POST['thumb']){
                $img=$info['thumb']=$this->img_down($_POST['thumb']);
            }
            $info['description']=mb_substr(strip_tags($_POST['info']),0,30,'utf-8');
            $info['inputtime']=$_POST['updatetime'];
            $info['updatetime']=SYS_TIME;
            $info['content']=$_POST['info'];
            $categorys=getcache('category_content','commons');
            $modelid=$categorys[$info['catid']]['modelid'];
            $this->db->set_model($modelid);
            $dongmanId=$this->db->add_content($info);
        }else{
            switch($_POST['state']){
                case "连载":
                    $info['state']='0';
                    break;
                case "连载中":
                    $info['state']='0';
                    break;
                case "完结":
                    $info['state']='1';
                    break;
                default:
                    $info['state']='0';
            }
            $this->db->table_name='tt_dongman';
            $this->db->update(array('updatetime'=>SYS_TIME,'state'=>$state),"id='{$dongman['id']}'");
            $dongmanId=$dongman['id'];
        }
        unset($info);
        //发布动漫详细
        $info['web_url']=$_POST['gather'];
        $this->db->table_name='tt_dongmanDetail';
        $dongman=$this->db->get_one(array('web_url'=>$info['web_url']));
        //    var_dump($dongman);exit();
        if(!$dongman){
            $info['catid']=89;
            $info['title']=str_replace('话正式版','话',$_POST['title2']);
            $info['listorder']=intval($_POST['listorder']);
            $info['status']=99;
            $info['username']='mhcadmin';
            $info['inputtime']=$info['updatetime']=time();
            $info['username']='mhcadmin';
            $info['thumb']=$img;
            $info['url']=$_POST['play_url'];
            $info['CartoonID']=$dongmanId;
            $info['from']=$_POST['from'];
            $this->db->set_model(18);
            $result=$this->db->add_content($info);
            $this->db->table_name='tt_dongmanDetail';
            $this->db->update(array('url'=>$_POST['play_url']),"id='{$result}'");
            exit('发布成功:'.$result);
        }
        exit('发布失败');
    }
    //发布漫画
    public function gather_comic(){
        //替换地区
        switch($_POST['info']['region']){
            case "日本":
                $_POST['info']['region']='1';
                break;
            case "港台":
                $_POST['info']['region']='2';
                break;
            case "内地":
                $_POST['info']['region']='3';
                break;
            case "欧美":
                $_POST['info']['region']='4';
                break;
            default:
                $_POST['info']['region']='5';
        }
        //替换状态
        switch($_POST['info']['state']){
            case "连载":
                $_POST['info']['state']='0';
                break;
            case "完结":
                $_POST['info']['state']='1';
                break;
            default:
                $_POST['info']['state']='0';
        }
        //替换分类
        switch($_POST['info']['type_id']){
            case "少年热血":
                $_POST['info']['type_id']='1';
                break;
            case "武侠格斗":
                $_POST['info']['type_id']='2';
                break;
            case "少女爱情":
                $_POST['info']['type_id']='3';
                break;
            case "科幻魔幻":
                $_POST['info']['type_id']='4';
                break;
            case "竞技体育":
                $_POST['info']['type_id']='5';
                break;
            case "爆笑喜剧":
                $_POST['info']['type_id']='6';
                break;
            case "侦探推理":
                $_POST['info']['type_id']='7';
                break;
            case "恐怖灵异":
                $_POST['info']['type_id']='8';
                break;
            case "耽美BL":
                $_POST['info']['type_id']='9';
                break;
            case "其他类型":
                $_POST['info']['type_id']='10';
                break;
            case "H漫画":
                $_POST['info']['type_id']='11';
                break;
            default:
                $_POST['info']['type_id']='10';
        }
        $url=$_POST['cover'];
        $ext=substr(strrchr($url,"."),1);
        $path='uploadfile/'.date("Ymd",time());
        if(!file_exists()){
            mkdir($path,0755,1);
            chmod($path,0755);
        }
        $rand=rand(1,9);
        $file_name=date("YmdHis",time()).$rand.".".$ext;
        ob_start();
        readfile($url);
        $img=ob_get_contents();
        ob_end_clean();
        $size=strlen($img);
        $fp2=@fopen($path."/".$file_name,"a");
        fwrite($fp2,$img);
        fclose($fp2);
        $_POST['info']['thumb']="/".$path."/".$file_name;
        
        //转换时间戳
        $_POST['info']['inputtime']=date('Y-m-d H:i:s',$_POST['info']['inputtime']);
        //处理简介为空
        if(empty($_POST['info']['content']))
            $_POST['info']['content']='暂无简介';
        $info=$_POST['info'];
        foreach($info as $key=>$v){
            $info[$key]=_trim($v);
            if(strpos($v,'[标签:'))
                unset($info[$key]);
        }
        unset($_POST['info']);
        if(!isset($info['catid']))
            exit('发布失败');
        $categorys=getcache('category_content','commons');
        if(!ctype_digit($info['catid'])){
            foreach($categorys as $v){
                if($v['catname']==$info['catid']){
                    $info['catid']=$v['catid'];
                    break;
                }
            }
        }
        $modelid=$categorys[$info['catid']]['modelid'];
        $info['content']=$this->format_str($this->format_content($info['content']));
        if(empty($info['title'])||!$info['content']{30})
            exit('发布失败');
        $info['status']=99;
        $info['username']=' ';
        //$info ['views'] = mt_rand ( 1, 100 );		
        $info['inputtime']=strtotime($info['inputtime']);
        if(!$info['inputtime'])
            $info['inputtime']=SYS_TIME;
        $info['updatetime']=SYS_TIME;
        //$_POST ['auto_thumb'] = 1; //是否截取图片作为缩略图
        $_POST['auto_thumb_no']=1; //截取第几张图片作为缩略图
        //print_r($info);exit;
        $this->db->set_model($modelid);
        $this->db->add_content($info);
        exit('发布成功');
    }
    
    public function gather_comic_content(){ //发布漫画内容
        $urls=explode(",",$_POST['imageurl']);
        if(is_array($urls)&&!empty($urls)){
            $i=1;
            foreach($urls as $url){
                $_POST['CartoonImage_url'][]=$url;
                $_POST['CartoonImage_alt'][]=$i;
                $_POST['CartoonImage_'][]=$i;
                $i++;
            }
        }
        $info=$_POST['info'];
        foreach($info as $key=>$v){
            $info[$key]=_trim($v);
            if(strpos($v,'[标签:'))
                unset($info[$key]);
        }
        unset($_POST['info']);
        if(!isset($info['catid']))
            exit('发布失败');
        $categorys=getcache('category_content','commons');
        if(!ctype_digit($info['catid'])){
            foreach($categorys as $v){
                if($v['catname']==$info['catid']){
                    $info['catid']=$v['catid'];
                    break;
                }
            }
        }
        $modelid=$categorys[$info['catid']]['modelid'];
        $info['content']=$this->format_str($this->format_content($info['content']));
        if(empty($info['title']))
            exit('发布失败');
        $info['status']=99;
        $info['username']=' ';
        //$info ['views'] = mt_rand ( 1, 100 );		
        $info['inputtime']=strtotime($info['inputtime']);
        if(!$info['inputtime'])
            $info['inputtime']=SYS_TIME;
        $info['updatetime']=SYS_TIME;
        //$_POST ['auto_thumb'] = 1; //是否截取图片作为缩略图
        $_POST['auto_thumb_no']=1; //截取第几张图片作为缩略图
        //print_r($info);exit;
        $this->db->set_model($modelid);
        $this->db->add_content($info);
        exit('发布成功');
    }

}
?>