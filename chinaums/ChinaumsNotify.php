<?php
require_once 'function.php';
/**
 * 银联商务
 * Chinaums
 */
class ChinaumsNotify{
    private $chinaums_config;


    function __construct($config){
        $this->chinaums_config = $config;
    }
    function ChinaumsNotify($config){
        $this->__construct($config);
    }

    /**
     * 验签
     *
     * @param string $data
     * @param string $sign
     * @param string $pem
     * @return bool 验签状态
     */
    private function verify($data, $sign) {
        $p = openssl_pkey_get_public ( file_get_contents ( $this->chinaums_config['publickKey'] ) );
        $verify = openssl_verify ( $data, hex2bin ( $sign ), $p );
        openssl_free_key ( $p );
        return $verify > 0;
    }
}