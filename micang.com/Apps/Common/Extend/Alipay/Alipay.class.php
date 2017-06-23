<?php
/**
 * AlipayApi.class.php
 * Author: Jansen
 * QQ: 6206574
 * Date: 2015-10-03
 */

namespace Common\Extend\Alipay;


class Alipay{
    /**
     * 签名方式，值固定大写
     */
    const SIGN_TYPE = 'MD5';
    /**
     * 字符编码 固定小写
     */
    const CHARSET = 'utf-8';
    /**
     * 网络协议 可选 https和http
     */
    const PROTOCOL = 'http';
    /**
     * 支付类型 固定为1
     */
    const PAYMENT_TYPE = '1';
    /**
     * 支付网关
     */
    const GATEWAY = 'https://mapi.alipay.com/gateway.do?';
    /**
     * HTTPS形式消息验证地址
     */
    const VERIFY_URL_FOR_HTTPS = 'https://mapi.alipay.com/gateway.do?service=notify_verify&';
    /**
     * HTTP形式消息验证地址
     */
    const VERIFY_URL_FOR_HTTP = 'http://notify.alipay.com/trade/notify_query.do?';
    /**
     * @var $cacert ca证书路径地址，用于curl中ssl校验
     */
    private static $cacert;
    /**
     * @var $partner 合作身份者id，以2088开头的16位纯数字
     */
    private static $partner;
    /**
     * @var $seller 收款支付宝账号
     */
    private static $seller;
    /**
     * @var $verifyKey 安全检验码，以数字和字母组成的32位字符
     */
    private static $verifyKey;
    /**
     * @var $notifyUrl 异步通知URL
     */
    private static $notifyUrl;
    /**
     * @var $callbackUrl 同步通知URL
     */
    private static $returnUrl;
    public function __construct($partner, $seller, $key, $notifyUrl='', $returnUrl=''){
        self::$partner = trim($partner);
        self::$seller = trim($seller);
        self::$verifyKey = $key;
        //初始化CA证书路径
        self::$cacert = getcwd().'\\cacert.pem';
        self::$notifyUrl = $notifyUrl;
        self::$returnUrl = $returnUrl;
    }
    /**
     * @param $no 订单号
     * @param $subject 订单标题
     * @param $money 订单付款金额
     * @param $body 订单描述
     * @param string $url 商品展示URL 需要完整URL
     */
    public function pay($no, $subject, $money, $body='', $extend=null){
        $query['service'] = 'create_direct_pay_by_user';
        $query['partner'] = self::$partner;
        $query['seller_email'] = self::$seller;
        $query['payment_type'] = self::PAYMENT_TYPE;
        $query['notify_url'] = self::$notifyUrl;
        $query['return_url'] = self::$returnUrl;
        $query['out_trade_no'] = $no;
        $query['subject'] = $subject;
        $query['total_fee'] = $money;
        $query['body'] = $body;
        if (!is_null($extend)) {
            $query['extra_common_param'] = $extend;
        }
        $query['_input_charset'] = self::CHARSET;
        exit(self::buildRequestForm($query));
    }
    /**
     * 针对notify_url和return_url验证消息是否是支付宝发出的合法消息
     * @param array $data GET或POST数据
     * @return 验证结果
     */
    public function notify(array $data){
        if(empty($data)) {//判断POST来的数组是否为空
            return false;
        } else {
            //生成签名结果
            $isSign = $this->getSignVeryfy($data, $data["sign"]);
            //获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）
            $responseTxt = 'true';
            if (! empty($data["notify_id"])) {
                $responseTxt = $this->getResponse($data["notify_id"]);
            }
            //验证
            //$responsetTxt的结果不是true，与服务器设置问题、合作身份者ID、notify_id一分钟失效有关
            //isSign的结果不是true，与安全校验码、请求时的参数格式（如：带自定义参数等）、编码格式有关
            return (preg_match("/true$/i",$responseTxt) && $isSign) ? true : false;
        }
    }
    /**
     * 生成签名结果
     * @param $para_sort 已排序要签名的数组
     * return 签名结果字符串
     */
    private function buildRequestMysign($para_sort) {
        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = $this->createLinkstring($para_sort);
        $mysign = '';
        if (self::SIGN_TYPE == 'MD5'){
            $mysign = $this->md5Sign($prestr, self::$verifyKey);
        }
        return $mysign;
    }
    /**
     * 生成要请求给支付宝的参数数组
     * @param $para_temp 请求前的参数数组
     * @return 要请求的参数数组
     */
    private function buildRequestPara($para_temp) {
        //除去待签名参数数组中的空值和签名参数
        $para_filter = $this->paraFilter($para_temp);
        //对待签名参数数组排序
        $para_sort = $this->argSort($para_filter);
        //生成签名结果
        $mysign = $this->buildRequestMysign($para_sort);
        //签名结果与签名方式加入请求提交参数组中
        $para_sort['sign'] = $mysign;
        $para_sort['sign_type'] = self::SIGN_TYPE;
        return $para_sort;
    }
    /**
     * 建立请求，以表单HTML形式构造（默认）
     * @param $para_temp 请求参数数组
     * @param $method 提交方式。两个值可选：post、get
     * @return 提交表单HTML文本
     */
    private function buildRequestForm($para_temp, $method='post') {
        //待请求参数数组
        $para = $this->buildRequestPara($para_temp);
        $sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='".self::GATEWAY."_input_charset=".self::CHARSET."' method='".$method."'>";
        while (list ($key, $val) = each ($para)) {
            $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
        }
        $sHtml = $sHtml."<script>document.forms['alipaysubmit'].submit();</script>";
        return $sHtml;
    }
    /**
     * 获取返回时的签名验证结果
     * @param $para_temp 通知返回来的参数数组
     * @param $sign 返回的签名结果
     * @return 签名验证结果
     */
    private function getSignVeryfy($para_temp, $sign) {
        //除去待签名参数数组中的空值和签名参数
        $para_filter = $this->paraFilter($para_temp);
        //对待签名参数数组排序
        $para_sort = $this->argSort($para_filter);
        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = $this->createLinkstring($para_sort);
        $isSgin = false;
        if (self::SIGN_TYPE == 'MD5'){
            $isSgin = $this->md5Verify($prestr, $sign, self::$verifyKey);
        }
        return $isSgin;
    }
    /**
     * 获取远程服务器ATN结果,验证返回URL
     * @param $notify_id 通知校验ID
     * @return 服务器ATN结果
     * 验证结果集：
     * invalid命令参数不对 出现这个错误，请检测返回处理中partner和key是否为空
     * true 返回正确信息
     * false 请检查防火墙或者是服务器阻止端口问题以及验证时间是否超过一分钟
     */
    private function getResponse($notify_id) {
        $veryfy_url = (self::PROTOCOL=='https')?self::VERIFY_URL_FOR_HTTPS:self::VERIFY_URL_FOR_HTTP;
        $veryfy_url .= "partner=" . self::$partner . "&notify_id=" . $notify_id;
        $responseTxt = $this->getHttpResponseGET($veryfy_url, self::$cacert);
        return $responseTxt;
    }
    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
     * @param $para 需要拼接的数组
     * return 拼接完成以后的字符串
     */
    private function createLinkstring($para) {
        $arg  = "";
        while (list ($key, $val) = each ($para)) {
            $arg .= $key."=".$val."&";
        }
        //去掉最后一个&字符
        $arg = substr($arg,0,strlen($arg)-1);
        return $arg;
    }
    /**
     * 除去数组中的空值和签名参数
     * @param $para 签名参数组
     * return 去掉空值与签名参数后的新签名参数组
     */
    private function paraFilter($para) {
        $para_filter = array();
        while (list ($key, $val) = each ($para)) {
            if($key == "sign" || $key == "sign_type" || $val == "")continue;
            else	$para_filter[$key] = $para[$key];
        }
        return $para_filter;
    }
    /**
     * 对数组排序
     * @param $para 排序前的数组
     * return 排序后的数组
     */
    private function argSort($para) {
        ksort($para);
        reset($para);
        return $para;
    }
    /**
     * 远程获取数据，GET模式
     * 注意：
     * 1.使用Crul需要修改服务器中php.ini文件的设置，找到php_curl.dll去掉前面的";"就行了
     * 2.文件夹中cacert.pem是SSL证书请保证其路径有效，目前默认路径是：getcwd().'\\cacert.pem'
     * @param $url 指定URL完整路径地址
     * @param $cacert_url 指定当前工作目录绝对路径
     * return 远程输出的数据
     */
    private function getHttpResponseGET($url,$cacert_url) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
        curl_setopt($curl, CURLOPT_CAINFO,$cacert_url);//证书地址
        $responseText = curl_exec($curl);
        //var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
        curl_close($curl);

        return $responseText;
    }
    /**
     * 签名字符串
     * @param $prestr 需要签名的字符串
     * @param $key 私钥
     * return 签名结果
     */
    private function md5Sign($prestr, $key) {
        return md5($prestr.$key);
    }
    /**
     * 验证签名
     * @param $prestr 需要签名的字符串
     * @param $sign 签名结果
     * @param $key 私钥
     * return 签名结果
     */
    private function md5Verify($prestr, $sign, $key) {
        return (md5($prestr.$key)==$sign)?true:false;
    }
}