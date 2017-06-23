<?php

namespace Port\Controller;
use Common\Extend;
class IndexController extends PortCommonController {
    public function _initialize(){
        
    }

    final public function index(){
        echo 111;
        exit;
        $list = $this->importExcel('a.xls');
//        $dataAll = array();
//        foreach($list as $item){
//            $data = array();
//            $temp = explode(',',$item[2]);
//            $data['callback_url'] = 'http://port.xinwenwang.com/bos/new1?t='.$item[0];
//            foreach($temp as $temp2){
//                $__temp = explode('|--|',$temp2);
//                $data['data_list'][] = array($__temp[0],$item[1]);
//
//            }
//            $dataAll[] = $data;
//        }
//        foreach($dataAll as $item){
//            foreach($item['data_list'] as $item2){
//                if($item2[0])
//                echo $item2[0]."|--|".$item2[1].'<BR/>';
//            }
//        }
//        EXIT;
//        echo json_encode($dataAll);
//        exit;
//
//
//
//        print_r($list);exit;
        $dataAll = array();
        foreach($list as $item){
            $data = array();
            $temp = explode(',',$item[1]);
            $data['callback_url'] = 'http://port.xinwenwang.com/bos/new1?t='.$item[0];
            foreach($temp as $temp2){
                $data['data_list'][] = explode('|--|',$temp2);

            }
            $dataAll[] = $data;
        }
        foreach($dataAll as $item){
            foreach($item['data_list'] as $item2){
                if($item2[0])
                    echo $item2[0]."|--|".$item2[1].'<BR/>';
            }
        }
        exit;
        echo json_encode($dataAll);
        exit;
        $excel = new \PHPExcel();
        exit;


        $list = D('Dingyue')->getShowList();
        print_r($list);
        exit;

        $temp = post("http://port.xinwenwang.com/news/lists",array('type_id'=>1800,'page'=>1,'limit'=>20,'login_key'=>''));

        print_r(json_decode($temp,true));
        exit;
        $taobao = new TaobaoIp();
        $list = $taobao->getLocation();
        print_r($list);
        exit;
        $str = "趣图,女人,健康,房产,电影,读书,养生,语录,探索,育儿,美图,家居,文化,星座,时尚,数码,搞笑,情感,历史,美食,减肥,动漫,GIF图";
        $str = explode(',',$str);
        foreach($str as $item){
            M('NewsType')->data(array('title'=>$item,'add_time'=>time(),'is_city'=>'0','is_default'=>'0'))->add();
        }
        print_r($str);
    }
    final public function news(){
        for($i=1;$i<100;$i++){
            $start = ($i-1)*20;
            $string = file_get_contents("http://125.88.168.37:9880/solr/b2bnews/select?q=*%3A*&wt=json&indent=true&start={$start}&rows=20");
            $json = json_decode($string,true);
            $response = $json['response'];
            foreach($response['docs'] as $item){
                $data = array(
                    'title'=>$item['name'],
                    'content'=>$item['content'],
                    'html'=>$item['html'],
                    'source_type'=>'',
                    'source_url'=>$item['url'],
                    'source_name'=>$item['newsSource'],
                    'add_time'=>time(),
                );
                M('News')->add($data);
            }
        }
    }
    final public function citys(){
        exit;
        set_time_limit(0);
        $list = M('Citys')->select();
        foreach($list as $item){
            echo "    ".$item['simple']."<br/>";
            $string = file_get_contents("http://pdc.258.com/area/view/getchildren?areaid={$item['areaID']}");
            $list_ = json_decode($string,true);
            foreach($list_ as $data){
                echo "---------".$data['simple']."<br/>";
                M('Citys')->add($data);
            }
//            exit;
        }
//        exit;
    }
    final  public function daochu(){
        $list = M('Pindao')->select();
        $dataAll = array();
        foreach($list as $item){
            $str = "http://port.xinwenwang.com/bos/new{$item['show_type']}?t={$item['id']}";
            $dataAll[] = array($item['title'],$str);
        }
        exportexcel($dataAll,array('分类名称','接收链接'),'ces');
    }
    final public function readTxt(){
        exit;
        $str = file_get_contents('./ces.txt'); //获得内容
        $arr = explode("\n", $str); //分行存入数组
        foreach($arr as $item){
            M('Pindao')->data(array('title'=>$item,'is_city'=>'1','add_time'=>time()))->add();
        }
    }
    final public function randType(){
        $list = M('News')->field('id')->where(array('type_id'=>0))->select();
        foreach($list as $item){
            M('News')->save(array('id'=>$item['id'],'type_id'=>rand(1800,1834)));
        }
    }
    final public function img(){
        $temp = array('http://www.qiushibaike.com/article/98427172?list=8hr&s=4736613',
            'http://pic.qiushibaike.com/system/pictures/9842/98426200/medium/app98426200.jpg',
            'http://pic.qiushibaike.com/system/pictures/9842/98425202/medium/app98425202.jpg',
            'http://pic.qiushibaike.com/system/pictures/9842/98426458/medium/app98426458.jpg',
            'http://pic.qiushibaike.com/system/pictures/9842/98425940/medium/app98425940.jpg',
        );
        $data = array(
            'id'=>907,
            'img_list'=>serialize(array($temp[0])),
            'show_type'=>4
        );
        M('News')->save($data);
//        json_encode(array($temp[0],$temp[1],$temp[2]));
    }
    final public function dingyue_type(){
        exit;
        $str = "推荐,新闻,科技,财经,政务,时尚,娱乐,旅游";
        $temp = explode(',',$str);
        foreach($temp as $item){
            M('DingyueType')->add(array('name'=>$item,'add_time'=>time()));
        }
    }
    final public function dingyue(){
        for($i=1;$i<100;$i++){
            $data = array(
                'type_id'=>rand(1000,1007),
                'name'=>'标题名称'.$i,
                'pic'=>'http://inews.gtimg.com/newsapp_ls/0/34846956_150120/0',
                'add_time'=>time()
            );
            M('Dingyue')->add($data);
        }

    }
    protected function importExcel($filePath){
        import('Common.Extend.Excel.PHPExcel',APP_PATH,'.php');
        import('Common.Extend.Excel.PHPExcel.Writer.Excel2007.PHPExcel_Reader_Excel2007',APP_PATH,'.php');
        import('Common.Extend.Excel.PHPExcel.Writer.Excel2007.PHPExcel_Reader_Excel5',APP_PATH,'.php');
        $PHPExcel = new \PHPExcel();
        /**默认用excel2007读取excel，若格式不对，则用之前的版本进行读取*/
        $PHPReader = new \PHPExcel_Reader_Excel2007();
        if(!$PHPReader->canRead($filePath)){
            $PHPReader = new \PHPExcel_Reader_Excel5();
            if(!$PHPReader->canRead($filePath)){
                echo 'no Excel';
                return;
            }
        }

        $PHPExcel = $PHPReader->load($filePath);
        $currentSheet = $PHPExcel->getSheet(0);  //读取excel文件中的第一个工作表
        $allColumn = $currentSheet->getHighestColumn(); //取得最大的列号
        $allRow = $currentSheet->getHighestRow(); //取得一共有多少行
        $erp_orders_id = array();  //声明数组

        /**从第二行开始输出，因为excel表中第一行为列名*/
        for($currentRow = 1;$currentRow <= $allRow;$currentRow++){
            $temp = array();
            $val = $currentSheet->getCellByColumnAndRow(0,$currentRow)->getValue();/**ord()将字符转为十进制数*/
            $temp[] = $val;
//            $val = $currentSheet->getCellByColumnAndRow(1,$currentRow)->getValue();/**ord()将字符转为十进制数*/
//            $temp[] = $val;
            $val = $currentSheet->getCellByColumnAndRow(3,$currentRow)->getValue();/**ord()将字符转为十进制数*/
            $temp[] = $val;
            $erp_orders_id[] = $temp;
        }
        return $erp_orders_id;
    }

}