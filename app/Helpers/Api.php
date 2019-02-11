<?php

namespace App\Helpers;

Class Api {
  public static function response($s=true,$m=null,$d=[]){
    return ['status'=>is_bool($s)?($s?1:0):($s==1?1:0),'message'=>is_string($m)?$m:null,'data'=>$d];
  }

  public static function rstring($length = 10, $chars = '1234567890') {
    
    // Alpha lowercase
    if ($chars == 'alphalower') {
        $chars = 'abcdefghijklmnopqrstuvwxyz';
    }

    // Numeric
    if ($chars == 'numeric') {
        $chars = '1234567890';
    }

    // Alpha Numeric
    if ($chars == 'alphanumeric') {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    }

    // Hex
    if ($chars == 'hex') {
        $chars = 'ABCDEF1234567890';
    }

    $charLength = strlen($chars)-1;

    $randomString = "";
    for($i = 0 ; $i < $length ; $i++)
        {
            $randomString .= $chars[mt_rand(0,$charLength)];
        }

    return $randomString;
  }
}

