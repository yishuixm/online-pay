<?php
require_once 'function.php';
/**
 * 银联商务
 * Chinaums
 */
class ChinaumsSubmit {

    private $chinaums_config;

    private $chinaums_gateway_test = 'http://116.228.21.170/webpay/umsPayInfo.do';
    private $chinaums_gateway = 'https://mpos.quanminfu.com/webpay/umsPayInfo.do ';

    function __construct($config){
        $this->chinaums_config = $config;
    }
    function ChinaumsSubmit($config){
        $this->__construct($config);
    }

    private function getGateway(){
        if($this->chinaums_config['test']===true){
            return $this->chinaums_gateway_test;
        }else{
            return $this->chinaums_gateway;
        }
    }

    /**
     * 签名数据
     *
     * @param string $data
     *        	要签名的数据
     * @param string $private
     *        	私钥文件
     * @return string 签名的16进制数据
     */
    private function sign($data) {
        $p = openssl_pkey_get_private ( $this->chinaums_config['privateKey'] );
        openssl_sign ( $data, $signature, $p );
        openssl_free_key ( $p );
        return bin2hex ( $signature );
    }

    function buildRequestForm($params, $button_name) {
        $form = [];
        foreach ($params as $k => $v){
            if($v!=''){
                $form[$k] = $v;
            }
        }

        $form['merchantId'] = $this->chinaums_config['merchantId'];
        $arr_rsa = $form;
        ksort($arr_rsa); // 按字典排序
        $q_rsa = http_build_query($arr_rsa);
        $form['sign'] = $this->sign($q_rsa);
        $form['signType'] = 'RSA';


        $sHtml = "<form id='chinasubmit' action='".$this->getGateway()."' method='POST'>";
        while (list ($key, $val) = each ($form)) {
            $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
        }

        //submit按钮控件请不要含有name属性
        $sHtml = $sHtml."<button type='submit'>{$button_name}</button></form>";

        return $sHtml;
    }
}