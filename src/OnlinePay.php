<?php
/**
 * 得到支付信息.
 * User: yishuixm
 * Email: 1830802211@qq.com
 * Date: 2016/7/30
 * Time: 16:07
 */

namespace yishuixm\onlinepay;




class OnlinePay
{
    /**
     * 设置支付宝配置
     * @param $partner
     * @param $seller_id
     * @param $key
     * @param string $sign_type
     * @param string $input_charset
     * @param string $cacert
     * @param string $transport
     * @param int $payment_type
     * @param string $service
     * @param string $anti_phishing_key
     * @param string $exter_invoke_ip
     */
    public static function setAlipayConfig($partner,$seller_id,$key,$sign_type='MD5',$input_charset='utf-8',$cacert='',$transport='http',$payment_type=1,$service='create_direct_pay_by_user',$anti_phishing_key='',$exter_invoke_ip=''){
        $alipay_config['partner']		    = $partner;
        $alipay_config['seller_id']	        = $seller_id;
        $alipay_config['key']			    = $key;
        $alipay_config['sign_type']         = strtoupper($sign_type);
        $alipay_config['input_charset']     = strtolower($input_charset);
        $alipay_config['cacert']            = empty($cacert)?getcwd().'\\cacert.pem':$cacert;
        $alipay_config['transport']         = $transport;
        $alipay_config['payment_type']      = $payment_type;
        $alipay_config['service']           = $service;
        $alipay_config['anti_phishing_key'] = $anti_phishing_key;
        $alipay_config['exter_invoke_ip']   = $exter_invoke_ip;

        $alipay = base64_encode(serialize($alipay_config));

        require_once __DIR__.'/../common/function.php';

        config_write(__DIR__.'/../config/alipay.config', $alipay);
    }

    /**
     * 生成支付宝的链接
     * @param $out_trade_no
     * @param $subject
     * @param $total_fee
     * @param $body
     * @param $return_url
     * @param $notify_url
     * @return bool|\请求的URL地址
     */
    public static function GrantAlipayUrl($out_trade_no,$subject,$total_fee,$body,$return_url,$notify_url,$service='',$show_url=''){

        if($config = @file_get_contents(__DIR__.'/../config/alipay.config')){
            $alipay_config = unserialize(base64_decode($config));
            $alipay_config['notify_url'] = $notify_url;
            $alipay_config['return_url'] = $return_url;
            $parameter = array(
                "service"       => $service?:$alipay_config['service'],
                "partner"       => $alipay_config['partner'],
                "seller_id"     => $alipay_config['seller_id'],
                "payment_type"	=> $alipay_config['payment_type'],
                "notify_url"	=> $alipay_config['notify_url'],
                "return_url"	=> $alipay_config['return_url'],
                "anti_phishing_key"=>$alipay_config['anti_phishing_key'],
                "exter_invoke_ip"=>$alipay_config['exter_invoke_ip'],
                "out_trade_no"	=> time().$out_trade_no,
                "subject"	=> $subject,
                "total_fee"	=> $total_fee,
                "body"	=> $body,
                "show_url"	=> $show_url,
                "_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
                //其他业务参数根据在线开发文档，添加参数.文档地址:https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.kiX33I&treeId=62&articleId=103740&docType=1
                //如"参数名"=>"参数值"

            );

            require_once __DIR__."/../alipay/alipay_submit.class.php";

            $alipaySubmit = new \AlipaySubmit($alipay_config);
            return  $alipaySubmit->buildRequestString($parameter);
        }else{
            return false;
        }
    }

    /**
     * 生成支付宝的表单
     * @param $out_trade_no
     * @param $subject
     * @param $total_fee
     * @param $body
     * @param $return_url
     * @param $notify_url
     * @return bool|\提交表单HTML文本
     */
    public static function GrantAlipayForm($out_trade_no,$subject,$total_fee,$body,$return_url,$notify_url,$service='',$show_url=''){

        if($config = @file_get_contents(__DIR__.'/../config/alipay.config')){
            $alipay_config = unserialize(base64_decode($config));
            $alipay_config['notify_url'] = $notify_url;
            $alipay_config['return_url'] = $return_url;
            $parameter = array(
                "service"       => $service?:$alipay_config['service'],
                "partner"       => $alipay_config['partner'],
                "seller_id"     => $alipay_config['seller_id'],
                "payment_type"	=> $alipay_config['payment_type'],
                "notify_url"	=> $alipay_config['notify_url'],
                "return_url"	=> $alipay_config['return_url'],
                "anti_phishing_key"=>$alipay_config['anti_phishing_key'],
                "exter_invoke_ip"=>$alipay_config['exter_invoke_ip'],
                "out_trade_no"	=> time().$out_trade_no,
                "subject"	=> $subject,
                "total_fee"	=> $total_fee,
                "body"	=> $body,
                "show_url"	=> $show_url,
                "_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
                //其他业务参数根据在线开发文档，添加参数.文档地址:https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.kiX33I&treeId=62&articleId=103740&docType=1
                //如"参数名"=>"参数值"

            );

            require_once __DIR__."/../alipay/alipay_submit.class.php";

            $alipaySubmit = new \AlipaySubmit($alipay_config);
            return  $alipaySubmit->buildRequestForm($parameter,'GET','立即支付');
        }else{
            return false;
        }
    }

    /**
     * 支付宝同步回调验证
     * @param $return_url
     * @param $notify_url
     * @return array|bool
     */
    public static function alipayReturnVerify($return_url,$notify_url){
        if($config = @file_get_contents(__DIR__.'/../config/alipay.config')){
            $alipay_config = unserialize(base64_decode($config));
            $alipay_config['notify_url'] = $notify_url;
            $alipay_config['return_url'] = $return_url;
        }

        require_once __DIR__."/../alipay/alipay_notify.class.php";

        $alipayNotify = new \AlipayNotify($alipay_config);
        if($alipayNotify->verifyReturn()) {
            $out_trade_no = $_GET['out_trade_no'];
            $trade_no = $_GET['trade_no'];
            $trade_status = $_GET['trade_status'];
            if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {

                return [
                    'out_trade_no'      => substr($out_trade_no,10),
                    'subject'           => $_GET['subject'],
                    'trade_no'          => $trade_no,
                    'trade_status'      => $trade_status,
                    'seller_email'      => $_GET['seller_email'],
                    'buyer_email'       => $_GET['buyer_email'],
                    'seller_id'         => $_GET['seller_id'],
                    'buyer_id'          => $_GET['buyer_id'],
                    'total_fee'         => $_GET['total_fee'],
                    'body'              => $_GET['body']
                ];


            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 支付宝异步回调验证
     * @param $return_url
     * @param $notify_url
     * @return array|bool
     */
    public static function alipayNotifyVerify($return_url,$notify_url){
        if($config = @file_get_contents(__DIR__.'/../config/alipay.config')){
            $alipay_config = unserialize(base64_decode($config));
            $alipay_config['notify_url'] = $notify_url;
            $alipay_config['return_url'] = $return_url;
        }

        require_once __DIR__."/../alipay/alipay_notify.class.php";

        $alipayNotify = new \AlipayNotify($alipay_config);
        if($alipayNotify->verifyNotify()) {
            $out_trade_no = $_POST['out_trade_no'];
            $trade_no = $_POST['trade_no'];
            $trade_status = $_POST['trade_status'];
            if($_POST['trade_status'] == 'TRADE_FINISHED') {
                return [];
            }else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                return [
                    'out_trade_no' => substr($out_trade_no, 10),
                    'subject' => $_POST['subject'],
                    'trade_no' => $trade_no,
                    'trade_status' => $trade_status,
                    'seller_email' => $_POST['seller_email'],
                    'buyer_email' => $_POST['buyer_email'],
                    'seller_id' => $_POST['seller_id'],
                    'buyer_id' => $_POST['buyer_id'],
                    'total_fee' => $_POST['total_fee'],
                    'body' => $_POST['body']
                ];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @param $appid
     * @param $mchid
     * @param $key
     * @param $appsecret
     * @param $sslcert_path
     * @param $sslkey_path
     */
    public static function setWxpayConfig($appid,$mchid,$key,$appsecret,$sslcert_path,$sslkey_path){

        $file_content = "<?php
class WxPayConfig
{
	const APPID = '{$appid}';
	const MCHID = '{$mchid}';
	const KEY = '{$key}';
	const APPSECRET = '{$appsecret}';
	const SSLCERT_PATH = '{$sslcert_path}';
	const SSLKEY_PATH = '{$sslkey_path}';
	const CURL_PROXY_HOST = '0.0.0.0';
	const CURL_PROXY_PORT = 0;
	const REPORT_LEVENL = 1;
}";
        require_once __DIR__.'/../common/function.php';

        config_write(__DIR__.'/../wxpay/WxPay.Config.php', $file_content);
    }

    /**
     * 获取微支付预支付订单
     * @param $out_trade_no
     * @param $goods
     * @param $total_fee
     * @param $body
     * @param $attach
     * @param $notify_url
     * @return \成功时返回
     */
    public static function GrantWxpayUnifiedOrder($out_trade_no,$goods,$total_fee,$body,$attach,$notify_url,$return_url){
        require_once __DIR__."/../wxpay/WxPay.Api.php";
        require_once __DIR__."/../wxpay/WxPay.JsApiPay.php";
        require_once __DIR__."/../common/log.php";

        $tools = new \JsApiPay();
        $openId = $tools->GetOpenid($return_url);

        $input = new \WxPayUnifiedOrder();
        $input->SetBody($body);
        $input->SetAttach($attach);
        $input->SetOut_trade_no(time().$out_trade_no);
        $input->SetTotal_fee(intval($total_fee*100));
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag($goods);
        $input->SetNotify_url($notify_url);
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $result['order'] = \WxPayApi::unifiedOrder($input);

        $result['jsApiParameters'] = $tools->GetJsApiParameters($result['order']);

        //获取共享收货地址js函数参数
//        $result['editAddress'] = $tools->GetEditAddressParameters();


        return $result;
    }

    /**
     * 获取微支付CodeUrl
     * @param $out_trade_no
     * @param $goods
     * @param $total_fee
     * @param $body
     * @param $attach
     * @param $notify_url
     * @param string $product_id
     * @return \成功时返回
     */
    public static function GrantWxpayCodeUrl($out_trade_no,$goods,$total_fee,$body,$attach,$notify_url,$product_id=''){
        require_once __DIR__."/../wxpay/WxPay.Api.php";
        require_once __DIR__."/../wxpay/WxPay.NativePay.php";
        require_once __DIR__."/../common/log.php";

        $notify = new \NativePay();

        $input = new \WxPayUnifiedOrder();
        $input->SetBody($body);
        $input->SetAttach($attach);
        $input->SetOut_trade_no(time().$out_trade_no);
        $input->SetTotal_fee(intval($total_fee*100));
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag($goods);
        $input->SetNotify_url($notify_url);
        $input->SetTrade_type("NATIVE");
        $input->SetProduct_id($product_id);
        $result = $notify->GetPayUrl($input);

        return $result;
    }

    /**
     * 微支付通知
     * @return array|bool
     */
    public static function wxpayNotifyVerify(){
        require_once __DIR__."/../wxpay/PayNotifyCallBack.php";

        $notify = new \PayNotifyCallBack();
        $notify->Handle(false);
        
        if($data = $notify->getPostData()){
            $out_trade_no = substr($data['out_trade_no'], 10);
            return [
                'appid'                 => $data['appid'],
                'mch_id'                => $data['mch_id'],
                'device_info'           => $data['device_info'],
                'device_info'           => $data['device_info'],
                'nonce_str'             => $data['nonce_str'],
                'sign'                  => $data['sign'],
                'result_code'           => $data['result_code'],
                'err_code'              => $data['err_code'],
                'err_code_des'          => $data['err_code_des'],
                'openid'                => $data['openid'],
                'is_subscribe'          => $data['is_subscribe'],
                'trade_type'            => $data['trade_type'],
                'bank_type'             => $data['bank_type'],
                'total_fee'             => $data['total_fee'],
                'settlement_total_fee'  => $data['settlement_total_fee'],
                'fee_type'              => $data['fee_type'],
                'cash_fee'              => $data['cash_fee'],
                'cash_fee_type'         => $data['cash_fee_type'],
                'coupon_fee'            => $data['coupon_fee'],
                'coupon_count'          => $data['coupon_count'],
                'coupon_type_$n'        => $data['coupon_type_$n'],
                'coupon_id_$n'          => $data['coupon_id_$n'],
                'coupon_fee_$n'         => $data['coupon_fee_$n'],
                'transaction_id'        => $data['transaction_id'],
                'out_trade_no'          => $out_trade_no,
                'attach'                => $data['attach'],
                'time_end'              => $data['time_end'],
            ];
        }else{
            return false;
        }
        
    }

    /**
     * 配置银联商务
     * @param $merchantId 商户号
     * @param $private_key 私钥
     * @param $publick_key 公钥
     */
    public static function setChinaumsConfig($merchantId,$private_key,$publick_key,$test){
        $chinaums_config['merchantId'] = $merchantId;
        $chinaums_config['privateKey'] = $private_key;
        $chinaums_config['publickKey'] = $publick_key;
        $chinaums_config['test'] = $test==='true';

        $chinaums = base64_encode(serialize($chinaums_config));

        require_once __DIR__.'/../common/function.php';

        config_write(__DIR__.'/../config/chinaums.config', $chinaums);
    }

    /**
     * 生成银联商务WEB表单
     * @param $merOrderId 商户订单号
     * @param $amount 金额
     * @param $returnUrl 回跳地址
     * @param $notifyUrl 通知地址
     * @param $mode 模式 WEB 使用3
     * @param $agentMerchantId 可选
     * @param $merchantUserId 可选
     * @param $mobile 可选
     * @return bool
     */
    public static function GrantChinaumsForm($merOrderId,$amount,$returnUrl,$notifyUrl,$mode,$agentMerchantId,$merchantUserId,$mobile){

        if($config = @file_get_contents(__DIR__.'/../config/chinaums.config')) {
            $chinaums_config = unserialize(base64_decode($config));

            $params = [
                "agentMerchantId"   => $agentMerchantId,
                "amount"        	=> $amount,
                "merOrderId"        => $merOrderId,
                "merchantUserId"    => $merchantUserId,
                "mobile"            => $mobile,
                "mode"              => $mode,
                "notifyUrl"         => $notifyUrl,
                "returnUrl"			=> $returnUrl,
            ];

            require_once __DIR__."/../chinaums/ChinaumsSubmit.php";

            $chinaumsSubmit = new \ChinaumsSubmit($chinaums_config);
            return $chinaumsSubmit->buildRequestForm($params,'立即支付');
        }else{
            return false;
        }
    }
}