<?php
require_once 'function.php';
/**
 * 银联商务
 * Chinaums
 */
class ChinaumsSubmit {

    private $chinaums_config;

    private $chinaums_gateway = 'https://mpos.quanminfu.com/webpay/umsPayInfo.do';

    function __construct($config){
        $this->chinaums_config = $config;
    }
    function ChinaumsSubmit($config){
        $this->__construct($config);
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
        $p = openssl_pkey_get_private ( file_get_contents ( $this->chinaums_config['privateKey'] ) );
        openssl_sign ( $data, $signature, $p );
        openssl_free_key ( $p );
        return bin2hex ( $signature );
    }

    function buildRequestForm($params, $button_name) {

        foreach ($params as $k => $v){
            if(empty($v)){
                unset($params[$k]);
            }
        }

        $params['merchantId'] = $this->chinaums_config['merchantId'];
        ksort($params); // 按字典排序
        $params['sign'] = $this->sign($params);
        $params['signType'] = 'RSA';


        $sHtml = "<form id='chinasubmit' action='".$this->chinaums_gateway."' method='POST'>";
        while (list ($key, $val) = each ($params)) {
            $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
        }

        //submit按钮控件请不要含有name属性
        $sHtml = $sHtml."<button type='submit'>{$button_name}</button></form>";

        return $sHtml;
    }
}