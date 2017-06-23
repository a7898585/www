<?php

//计算时间
function _time($time) {
    if ($time < 0)
        return false;
    $time = time() - $time;
    $minute = ceil($time / 60);
    if ($minute < 60)
        return $minute . '分钟前';
    $hour = floor($minute / 60);
    if ($hour < 24)
        return $hour . '小时前';
    $day = floor($hour / 24);
    if ($day < 30)
        return $day . '天前';
    $month = floor($day / 30);
    if ($month < 12)
        return $month . '月前';
    return floor($month / 12) . '年前';
}

/**
 * 获取替换文章中的图片路径
 * @param string $xstr 内容
 * @param string $keyword 创建照片的文件名
 * @param string $oriweb 网址
 * @return string
 *
 */
function replaceimg($xstr, $keyword = '', $oriweb = '') {

    //保存路径
    $d = date('Ymd', time());
    $dirslsitss = '/var/www/weblist/uploads/' . $keyword . '/' . $d; //分类是否存在
    if (!is_dir($dirslsitss)) {
        @mkdir($dirslsitss, 0777);
    }

    //匹配图片的src
    preg_match_all('#<img.*?src="([^"]*)"[^>]*>#i', $xstr, $match);

    foreach ($match[1] as $imgurl) {

        $imgurl = $imgurl;

        if (is_int(strpos($imgurl, 'http'))) {
            $arcurl = $imgurl;
        } else {
            $arcurl = $oriweb . $imgurl;
        }
        $img = file_get_contents($arcurl);


        if (!empty($img)) {

            //保存图片到服务器
            $fileimgname = time() . "-" . rand(1000, 9999) . ".jpg";
            $filecachs = $dirslsitss . "/" . $fileimgname;
            $fanhuistr = file_put_contents($filecachs, $img);
            $saveimgfile = "/uploads/$keyword" . "/" . $d . "/" . $fileimgname;
            $xstr = str_replace($imgurl, $saveimgfile, $xstr);
        }
    }
    return $xstr;
}

function defaultCategory() {
    $data = array(
        'hot' => '热点',
        'shehui' => '社会',
        'yule' => '娱乐',
        'tiyu' => '体育',
        'qiche' => '汽车',
        'caijing' => '财经',
    );

    return $data;
}

function otherCategory() {
    $data = array(
        'guoji' => '国际',
        'junshi' => '军事',
        'youxi' => '游戏',
        'lvyou' => '旅游',
        'shichang' => '时尚',
        'keji' => '科技',
        'shuma' => '数码',
        'fangchan' => '房产',
        'jiaju' => '家居',
        'gaoxiao' => '搞笑',
        'dongman' => '动漫',
        'sousuo' => '探索',
        'lishi' => '历史',
        'nvren' => '女人',
        'jiankang' => '健康',
        'dianying' => '电影',
        'dushu' => '读书',
        'yangsheng' => '养生',
        'yulu' => '语录',
        'yuer' => '育儿',
        'wenhua' => '文化',
        'xingzuo' => '星座',
        'qinggan' => '情感',
        'meishi' => '美食',
        'jianfei' => '减肥',
//        'giftu'=>'GIF图',
//        'qutu'=>'趣图',
//        'meitu'=>'美图',
    );

    return $data;
}

function ff_page($content, $page) {
    global $expert_id;
    $PageLength = 550; //每页字数   
    $CLength = strlen($content);
    $PageCount = floor(($CLength
                    / $PageLength)) + 1; //计算页数   
    $PageArray = array();
    $Seperator = array("\n",
        "\r", "。", "！", "？", "；
", "，", "”", "’"); //分隔符号   
//echo "页数：".$PageCount."< br>";   
//echo "长度：".$CLength."< br>< br>< br>";   
//strpos() 函数返回字符串在
    if ($CLength < $PageLength) {
        echo $content;
    } else {
        $PageArray[0] = 0;
        $Pos = 0;
        $i = 0;
//第一页   
        for ($j = 0; $j < sizeof($Seperator); $j++) {
//echo $Seperator[$j];   
            $Pos = strpos($content, $Seperator[$j], $PageArray[$i] + 1900);
            while ($Pos > 0 && $Pos
            < ($i + 1) * $PageLength &&
            $Pos > $i * $PageLength) {
                $PageArray[$i] = $Pos;
                $Pos = strpos($Pos + $PageLength, $content, $Seperator[$j]);
            }
            if ($PageArray[$i] > 0) {
                $j = $j + sizeof($Seperator) + 1;
            }
        }
//---   
        for ($i = 1; $i < $PageCount - 1; $i++) {
            for ($j = 0; $j < sizeof($Seperator); $j++) {
//echo $Seperator[$j];   
                $Pos = strpos($content, $Seperator
                        [$j], $PageArray[$i - 1] + 1900);
                while ($Pos > 0 && $Pos <
                ($i + 1) * $PageLength && $Pos >
                $i * $PageLength) {
                    $PageArray[$i] = $Pos;
                    $Pos = strpos($Pos + $PageLength, $content, $Seperator[$j]);
                }
                if ($PageArray[$i] > 0) {
                    $j = $j + sizeof($Seperator) + 1;
                }
            }
        }
//--PHP长文章分页函数最后一页   
        $PageArray[$PageCount - 1] = $CLength;
//$page=2;   
        if ($page == 1) {
            $output = substr($content, 0, $PageArray[$page - 1] + 2);
        }
        if($page>1 && $page<= $PageCount) {
            $output = substr($content, $PageArray
            [$page-2]+2, $PageArray[$page-1]-$PageArray[$page-2]);
            $output = " （上接第" . ($page - 1) . "页）\n" . $output;
        }
        echo str_replace("\n", "< br>&nbsp;&nbsp;&nbsp;", $output);
//if($page==$PageCount)   
//return $output=substr($content,$PageArray[$page-2]+2,$PageArray[$page-1]-$PageArray[$page-2]);
        if ($PageCount > 1) {
            echo "< br>< br>< br>< center>";
            echo "<font color='ff0000'>" . $page . "< /font>/" . $PageCount . " 页 &nbsp;";
            if ($page > 1)
                echo "< a href=$PHP_SELF?expert_id=$expert_id&page_t=" . ($page - 1) . ">上一页< /a> ";
            else
                echo "上一页 ";
            for($i = 1;$i<= $PageCount;$i++){
                echo "< a href=$PHP_SELF?expert_id=$expert_id&page_t=" . $i . ">[" . $i . "]< /a> ";
            }
            if ($page < $PageCount)
                echo " < a href=$PHP_SELF?expert_id=$expert_id&page_t=" . ($page + 1) . ">下一页< /a> ";
            else
                echo " 下一页 ";
            echo "< /center>";
        }
    }
}

?>  