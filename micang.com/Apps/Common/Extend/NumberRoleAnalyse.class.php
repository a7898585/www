<?php
namespace Common\Extend;
/**
 * 号码规则分析类
 * 
 * @since 2013-06-05
 * @author Jansen<6206574@qq.com>
*/
class NumberRoleAnalyse {
    private $numberLength = array();
    public function __construct(){
        $this->numberLength = array(
            'AAAAAA' => array(
                '5' => array('normal' => 3),        //3个以上相同算对子
                '6' => array('normal' => 4),        //3个以上相同算对子
                '7' => array('normal' => 4),        //4个以上相同算对子
                '8' => array('normal' => 5),        //5个以上相同算对子
            ),
            'ABCDEF' => array(
                '5' => array('normal' => 4, 'head' => 4, 'foot' => 4),
                //正常情况下，4个及以上递增算顺子，但头尾3个以上递增算顺子
                '6' => array('normal' => 4, 'head' => 4, 'foot' => 4),
                //正常情况下，4个及以上递增算顺子，但头尾3个以上递增算顺子
                '7' => array('normal' => 5, 'head' => 5, 'foot' => 5),
                //正常情况下，5个及以上递增算顺子，但头尾3个以上递增算顺子
                '8' => array('normal' => 5, 'head' => 5, 'foot' => 5)
            ),
            'FEDCBA' => array(
                '5' => array('normal' => 4, 'head' => 4, 'foot' => 4),
                //正常情况下，4个及以上递减算顺子，但头尾3个以上递减算顺子
                '6' => array('normal' => 4, 'head' => 4, 'foot' => 4),
                //正常情况下，4个及以上递减算顺子，但头尾3个以上递减算顺子
                '7' => array('normal' => 5, 'head' => 5, 'foot' => 5),
                //正常情况下，5个及以上递减算顺子，但头尾3个以上递减算顺子
                '8' => array('normal' => 5, 'head' => 5, 'foot' => 5)
            ),
            'ABABAB' => array(
                '5' => array('normal' => 2, 'even' => 2),
                //正常情况下，3个及以上相同算，但偶数位允许2个以上相同算
                '6' => array('normal' => 2, 'even' => 2),
                //正常情况下，3个及以上相同算，但偶数位允许2个以上相同算
                '7' => array('normal' => 3, 'even' => 2),
                //正常情况下，3个及以上相同算，但偶数位允许2个以上相同算
                '8' => array('normal' => 3, 'even' => 2)
            )
        );
    }
    public function run($num){
        if (strlen($num) < 5){
            return true;
        }elseif (self::AAAAAA($num)){
            return true;
        }elseif (self::ABCDEF($num)){
            return true;
        }elseif (self::ABABAB($num)){
            return true;
        }elseif (self::AABBCC($num)){
            return true;
        }elseif (self::FEDCBA($num)){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 对子
     * @param int $num
     * @return boolean
     * @todo 根据不同的倍数增减判断对子的个数
     */
    private function AAAAAA($num){
        $serialLen = 1;
        $arr = str_split($num, 1);
        $arrLen = strlen($num);
        for ($i=0; $i<$arrLen; $i++){
    		if ($arr[$i] == $arr[$i+1]){
    			$serialLen++;
    		}else{
    			$serialLen = 1;
    		}
    		if ($serialLen >= $this->numberLength['AAAAAA'][$arrLen]['normal']){
    			return true;
    		}
    		if ($i+2 >= $arrLen){//少一次循环
    			break;
    		}
    	}
        return false;
    }
    /**
     * 递增顺子
     * @param int $num
     * @return boolean
     */
    private function ABCDEF($num){
        $serialLen = 1;
        $arr = str_split($num, 1);
        $arrLen = strlen($num);
        for ($i=0; $i<$arrLen; $i++){
            if ($arr[$i]+1 == $arr[$i+1]){
                $serialLen++;
            }else{
                $serialLen = 1;
            }
            if ($serialLen == $this->numberLength['ABCDEF'][$arrLen]['head']){
                if ($i==1)  return true;
            }if ($serialLen == $this->numberLength['ABCDEF'][$arrLen]['foot']){
                if ($i==$arrLen-2)  return true;
            }elseif ($serialLen > $this->numberLength['ABCDEF'][$arrLen]['normal']){
                return true;
            }
            if ($i+2 >= $arrLen){//少一次循环
                break;
            }
        }
        return false;
    }
    /**
     * 递减顺子
     * @param int $num
     * @return boolean
     */
    private function FEDCBA($num){
        $serialLen = 1;
        $arr = str_split($num, 1);
        $arrLen = strlen($num);
        for ($i=0; $i<$arrLen; $i++){
            if ($arr[$i]-1 == $arr[$i+1]){
                $serialLen++;
            }else{
                $serialLen = 1;
            }
            if ($serialLen == $this->numberLength['FEDCBA'][$arrLen]['head']){
                if ($i==1)  return true;
            }if ($serialLen == $this->numberLength['FEDCBA'][$arrLen]['foot']){
                if ($i==$arrLen-2)  return true;
            }elseif ($serialLen > $this->numberLength['FEDCBA'][$arrLen]['normal']){
                return true;
            }
            if ($i+2 >= $arrLen){//少一次循环
                break;
            }
        }
        return false;
    }
    /**
     * 循环复数
     * @param int $num
     * @return boolean
     */
    private function ABABAB($num){
        $numLen = strlen($num);
        $serialLen = 1;
        for ($i=0; $i<floor($numLen/2); $i++){
            $arr = str_split($num, $i+2);
            $arrLen = count($arr);
            for ($m=0; $m<$arrLen; $m++){
                if ($arr[$m] === $arr[$m+1]){
                    $serialLen++;
                }else{
                    $serialLen = 1;
                }
                if ($serialLen >= $this->numberLength['ABABAB'][$numLen]['normal']){
                    return true;
                }elseif ($numLen%2==0 && $i>0 && $serialLen>=$this->numberLength['ABABAB'][$numLen]['even']){//偶数位允许2个以上相同算
                    return true;
                }
                if ($m+2 >= $arrLen){//少一次循环
                    break;
                }
            }
        }
        return false;
    }
    /**
     * 循环对子
     * @param int $num
     * @return boolean
     */
    private function AABBCC($num){
        $numLen = strlen($num);
        if ($numLen==7 || $numLen==11)    return false;
        $serialLen = 1;
        for ($i=0; $i<floor($numLen/2); $i++){//循环次数
            $n = $i + 2;
            if ($numLen%$n > 0) continue;//总位数与截取位数不能整除则跳过
            $arr = str_split($num, $n);
            $arrLen = count($arr);
            for ($m=0; $m<$arrLen; $m++){
                $p1 = substr($arr[$m],0,1);
                $p2 = ($p1+1)>9?0:($p1+1);
                if (str_repeat($p1, $n)===$arr[$m] && str_repeat($p2,$n)===$arr[$m+1]){
                    $serialLen++;
                }else{
                    $serialLen = 1;
                }
                if ($serialLen*$n == $numLen){
                    return true;
                }
                if ($m+2 >= $arrLen){//少一次循环
                    break;
                }
            }
        }
    }
}