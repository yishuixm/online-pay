<?php
/**
 * 还原公钥
 *
 * @param string $mod
 * @param string $exp
 * @return string 字符串形式的公钥文件
 */
function public2file($mod, $exp = '010001') {
    $key=base64_encode ( hex2bin ( "30820122300D06092A864886F70D01010105000382010F003082010A0282010100{$mod}0203{$exp}" ) );
    $pem = chunk_split($key,64,"\n");
    $pem = "-----BEGIN PUBLIC KEY-----\n".$pem."-----END PUBLIC KEY-----\n";
    return $pem;
}
/**
 *
 * @param unknown $publicKey
 * @return string 更正公钥格式
 */
function publicKey($publicKey) {
    $pem = chunk_split($publicKey,64,"\n");
    $pem = "-----BEGIN PUBLIC KEY-----\n".$pem."-----END PUBLIC KEY-----\n";
    return $pem;
}