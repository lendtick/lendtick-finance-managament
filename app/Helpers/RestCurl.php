<?php

namespace App\Helpers;

Class RestCurl {
  public static function exec($method, $url, $obj = array(), $token = '', $additional = '') {
    $header = ['Accept: application/json','Content-Type: application/json'];
    if(!empty($token)){
      $authorization = 'Authorization: '.$token;
      array_push($header, $authorization);
    }
    if(!empty($additional)){
      array_push($header, $additional);
    }
    $curl = curl_init();
     
    switch($method) {
      case 'GET':
        if(strrpos($url, "?") === FALSE) {
          $url .= '?' . http_build_query($obj);
        }
        break;

      case 'POST': 
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($obj));
        break;

      case 'PUT':
      case 'DELETE':
      default:
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($obj));
    }

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header); 
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, TRUE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, TRUE);
    
     // Exec
    $response = curl_exec($curl);
    $info     = curl_getinfo($curl);
    if(curl_errno($curl)){
        throw new \Exception('Request Error: ' . curl_error($curl), $info['http_code']);   
    }

    curl_close($curl);

    // Data
    $header = trim(substr($response, 0, $info['header_size']));
    $body = substr($response, $info['header_size']);
     
    return array('status' => $info['http_code'], 'header' => $header, 'data' => json_decode($body));
  }

  public static function get($url, $obj = array(), $token = '') {
     return RestCurl::exec("GET", $url, $obj, $token);
  }

  public static function post($url, $obj = array(), $token = '') {
     return RestCurl::exec("POST", $url, $obj, $token);
  }

  public static function put($url, $obj = array(), $token = '') {
     return RestCurl::exec("PUT", $url, $obj, $token);
  }

  public static function delete($url, $obj = array(), $token = '') {
     return RestCurl::exec("DELETE", $url, $obj, $token);
  }


  public static function hit($url, $dataArray = array(), $method='GET' ){
    
  $dataPost = http_build_query($dataArray);

  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_PORT => "",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_HTTPHEADER => array(
    "content-type: application/x-www-form-urlencoded"
    ),

    CURLOPT_URL => $url,
    CURLOPT_POSTFIELDS => $dataPost,
    CURLOPT_CUSTOMREQUEST => $method,

  ));

  $response = curl_exec($curl);
  $err = curl_error($curl);

  curl_close($curl);

  return array(
    'response' => $response,
    'err' => $err,
  );
}

}

