<?php
function config_write($file,$content){
    $cf = fopen($file,'w');
    fwrite($cf, $content);
    fclose($cf);
}