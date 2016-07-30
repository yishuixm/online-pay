<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/30
 * Time: 16:28
 */

require_once __DIR__.'/../src/OnlinePay.php';

//yishuixm\onlinepay\OnlinePay::setAlipayConfig(
//    '2088221883506003',
//    '2088221883506003',
//    'zrudqya6r8kg5mq7e8ovphi8tfpf9n47',
//    'MD5',
//    'utf-8',
//    getcwd().'\\cacert.pem',
//    'http',
//    '1',
//    'create_direct_pay_by_user',
//    '',
//    ''
//);

//$result = yishuixm\onlinepay\OnlinePay::GrantAlipayForm('OD201607300000001001','商品',0.01,'','http://','http://');
//
//print_r($result);
//
//
//$result = yishuixm\onlinepay\OnlinePay::GrantWxpayCodeUrl('OD201607300000001001','商品',0.01,'test','test','http://','');
//
//print_r($result);


//const APPID = 'wxb76452113c71d0b1';
//const MCHID = '1356321702';
//const KEY = 'e10adc3949ba59abbe56e057f20f883e';
//const APPSECRET = 'e917e9463059340391b477453d2c4dd2';
//const SSLCERT_PATH = '../cert/apiclient_cert.pem';
//const SSLKEY_PATH = '../cert/apiclient_key.pem';
//const CURL_PROXY_HOST = "0.0.0.0";//"10.152.18.220";
//const CURL_PROXY_PORT = 0;//8080;
//const REPORT_LEVENL = 1;


$result = yishuixm\onlinepay\OnlinePay::setWxpayConfig('wxb76452113c71d0b1','1356321702','e10adc3949ba59abbe56e057f20f883e','e917e9463059340391b477453d2c4dd2','../cert/apiclient_cert.pem','../cert/apiclient_key.pem');