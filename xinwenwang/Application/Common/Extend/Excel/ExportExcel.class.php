<?php
/**
 * Created by PhpStorm.
 * User: xmlijian
 * Date: 14-11-13
 * Time: 下午4:02
 */
class ExportExcel{
    public function aaa(){
        echo time();exit;
    }
    public function export($filename,$title,$data){
        $zimu = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','U','V','W','X','Y','Z');
        $size_count = count($title)-1;
        import("PHPExcel",dirname(__FILE__),".php");
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("ctos")
            ->setLastModifiedBy("ctos")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");
// set table header content
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '时间:'.date('Y-m-d H:i:s'));

        //set width
        foreach($title as $key=>$item){
            $temp = $zimu[$key];
            $objPHPExcel->getActiveSheet()->getColumnDimension($temp)->setWidth($item[1]);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($temp.'2', $item[0]);
        }

        //设置行高度
        $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(20);

        $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(25);

        //set font size bold
        $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(13);
        $objPHPExcel->getActiveSheet()->getStyle('A2:Z2')->getFont()->setBold(true);
//
//        $objPHPExcel->getActiveSheet()->getStyle('A2:Z2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $objPHPExcel->getActiveSheet()->getStyle('A2:Z2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        //设置水平居中
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A:Z')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //
        $objPHPExcel->getActiveSheet()->mergeCells('A1:N1');
        foreach($data as $k=>$item){
            foreach($title as $tk=>$tv){
                $objPHPExcel->getActiveSheet(0)->setCellValue($zimu[$tk].($k+3), $item[$tk]);
            }
            $objPHPExcel->getActiveSheet()->getStyle('A'.($k+3).':'.$zimu[$size_count].($k+3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('A'.($k+3).':'.$zimu[$size_count].($k+3))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getRowDimension($tk+3)->setRowHeight(16);
        }
        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('申请记录信息');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        ob_end_clean();//清除缓冲区,避免乱码
//         Redirect output to a client’s web browser (Excel5)
//        header('Content-Type: application/vnd.ms-excel');
        $filenames='a.xls';
//        header("Content-Disposition: attachment;filename={$filenames}");
//        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $a = $objWriter->save($filename);
    }




    public function index(){
        $name=$_GET['name'];
        $type=$_GET['type'];
        switch($name){
            case 'agent':{
                switch($type){
                    case 'A':$data=M('agentclient')->where(array('type'=>1))->select();$filename="代理商A类";break;
                    case 'B':$data=M('agentclient')->where(array('type'=>2))->select();$filename="代理商B类";break;
                    case 'C':$data=M('agentclient')->where(array('type'=>3))->select();$filename="代理商C类";break;
                    case 'D':$data=M('agentclient')->where(array('type'=>4))->select();$filename="代理商D类";break;
                    case 'collect':$data=M('agentclient')->where(array('type'=>5))->select();$filename="代理商收藏";break;
                    case 'black':$data=M('agentclient')->where(array('type'=>6))->select();$filename="代理商黑名单";break;
                    default:break;
                }
            }break;
            case 'manager':{
                switch($type){
                    case 'A':$data=M('managerclient')->where(array('type'=>1))->select();$filename="销售经理A类";break;
                    case 'B':$data=M('managerclient')->where(array('type'=>2))->select();$filename="销售经理B类";break;
                    case 'C':$data=M('managerclient')->where(array('type'=>3))->select();$filename="销售经理C类";break;
                    case 'D':$data=M('managerclient')->where(array('type'=>4))->select();$filename="销售经理D类";break;
                    case 'collect':$data=M('managerclient')->where(array('type'=>5))->select();$filename="销售经理收藏";break;
                    case 'black':$data=M('managerclient')->where(array('type'=>6))->select();$filename="销售经理黑名单";break;
                    default:break;
                }
            }break;
            case 'saler':{
                switch($type){
                    case 'A':$data=M('salerclient')->where(array('type'=>1))->select();$filename="业务员A类";break;
                    case 'B':$data=M('salerclient')->where(array('type'=>2))->select();$filename="业务员B类";break;
                    case 'C':$data=M('salerclient')->where(array('type'=>3))->select();$filename="业务员C类";break;
                    case 'D':$data=M('salerclient')->where(array('type'=>4))->select();$filename="业务员D类";break;
                    case 'collect':$data=M('salerclient')->where(array('type'=>5))->select();$filename="业务员收藏";break;
                    case 'black':$data=M('salerclient')->where(array('type'=>6))->select();$filename="业务员黑名单";break;
                    default:break;
                }
            }break;
            default:break;

        }
        //P($data);
        //exit;
        $OrdersData=$data;
        if(!$OrdersData){
            echo"<h1 align='center'>没有数据</h1>";
            return;
        }
        //	P($OrdersData);
        //	exit;
        Vendor('PHPExcel.PHPExcel');
        Vendor('PHPExcel.PHPExcel.IOFactory');
        Vendor('PHPExcel.PHPExcel.Reader.Excel5');
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
        // Set properties
        $objPHPExcel->getProperties()->setCreator("ctos")
            ->setLastModifiedBy("ctos")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");

        //set width
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);


        //设置行高度
        $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(22);

        $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);

        //set font size bold
        $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10);
        $objPHPExcel->getActiveSheet()->getStyle('A2:D2')->getFont()->setBold(true);

        $objPHPExcel->getActiveSheet()->getStyle('A2:D2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A2:D2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        //设置水平居中
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('C')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


        //
        $objPHPExcel->getActiveSheet()->mergeCells('A1:N1');

        // set table header content
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', $filename.'记录  时间:'.date('Y-m-d H:i:s'))
            ->setCellValue('A2', '编号')
            ->setCellValue('B2', '姓名')
            ->setCellValue('C2', '电话')
            ->setCellValue('D2', '备注') ;
        // Miscellaneous glyphs, UTF-8
        for($i=0;$i<=count($OrdersData)-1;$i++){
            $objPHPExcel->getActiveSheet(0)->setCellValue('A'.($i+3), $i+1);
            $objPHPExcel->getActiveSheet(0)->setCellValue('B'.($i+3), $OrdersData[$i]['clientname']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('C'.($i+3), $OrdersData[$i]['phone']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('D'.($i+3), $OrdersData[$i]['remark']);
            $objPHPExcel->getActiveSheet()->getStyle('A'.($i+3).':D'.($i+3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('A'.($i+3).':D'.($i+3))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getRowDimension($i+3)->setRowHeight(16);

        }
        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle($filename.'记录');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        ob_end_clean();//清除缓冲区,避免乱码
        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        $filenames=$filename.'('.date('Ymd-His').').xls';
        header("Content-Disposition: attachment;filename={$filenames}");
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');

    }
}