<?php
namespace App\Helpers;

use Exception;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\CreateBlockBlobOptions;
use App\Helpers\MimeHelper;

Class BlobStorage {
  public static $client, $error=[], $data, $config;

  public function __construct(){
    // connection client
    self::_connection();
    // content data is :
    // --> source     : source path of file
    // --> path       : new path on blob
    // --> container  : container in blob
  }

  private static function _connection(){
    self::$config['Endpoint'] = env('BLOB_DEFAULT_ENDPOINTS_PROTOCOL');
    self::$config['AccountName'] = env('BLOB_ACCOUNT_NAME');
    self::$config['AccountKey'] = env('BLOB_ACCOUNT_KEY');
    self::$config['Container'] = env('BLOB_CONTAINER');

    $ConnectionString  = "DefaultEndpointsProtocol=".self::$config['Endpoint'];
    $ConnectionString .= ";AccountName=".self::$config['AccountName'];
    $ConnectionString .= ";AccountKey=".self::$config['AccountKey'];

    self::$client = BlobRestProxy::createBlobService($ConnectionString);

    // self::$client->deleteContainer(self::$config['Container']);
    try{
      if(!($tmp = self::$client->getContainerProperties(self::$config['Container'])))
        throw new Exception("error",500);
      if(is_object($tmp)){
        if($tmp->getLeaseState() !== "available")
          self::$client->createContainer(self::$config['Container']);
      } else
        self::$client->createContainer(self::$config['Container']);
    } catch(Exception $e){
      self::$client->createContainer(self::$config['Container']);
    }
  }

  public static function data($d){
    self::$data = is_array($d)?$d:[$d];
  }

  public static function upload(){
    try{
      if(!isset(self::$data['source'])) throw New Exception("Variabel konten tidak ditemukan",500);
      if(!isset(self::$data['path'])) throw New Exception("Variabel alamat tidak ditambahkan",500);
  
      if(strpos(self::$data['source'],"ata:")>0){
        $tmp = explode('base64,',self::$data['source']);
        self::$config['blob_type'] = str_replace(";","",str_replace("data:","",$tmp[0]));
        self::$config['ext_type'] = MimeHelper::extension(self::$config['blob_type']);
        $tmp_content = base64_decode($tmp[1]);
      }
      else {
        $ch = curl_init(self::$data['source']);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $header = curl_getinfo($ch);
        if($header['http_code'] == 200){
          $tmp_content = file_get_contents(self::$data['source']);
          self::$config['blob_type'] = $header['content_type'];
          self::$config['ext_type'] = MimeHelper::extension(self::$config['blob_type']);
        }
      }

      $link = "";
      if(isset(self::$config['ext_type']) && isset(self::$config['blob_type'])){
        // Add mime type
        $options = new CreateBlockBlobOptions();
        $options->setContentType(self::$config['blob_type']);

        if(!is_null(self::$config['ext_type']) && !empty(self::$config['ext_type'])){
          $name = sha1(self::$data['source']).".".self::$config['ext_type'];
          $path = self::_validatePath(self::$data['path']).$name;

          self::$client->createBlockBlob(self::$config['Container'], $path, $tmp_content,$options);
          $link = self::$config['Endpoint']."://".self::$config['AccountName'].".blob.core.windows.net/".self::$config['Container']."/".$path;
        }
      }

      return self::_resp(true,'Sukses upload', ['link'=>$link]);
    } catch(Exception $e){
      self::setError($e->getMessage(),$e->getCode());
      return false;
    }
  }

  public static function error(){
    return self::$error?
      self::_resp(false, self::$error['message'],['code'=>self::$error['code']])
      :false;
  }

  private static function _validatePath($p){
    $tmp = explode("/",$p);
    if(is_array($tmp)){
      if(strpos($tmp[count($tmp)-1],".")>0)
        $p = str_replace($tmp[count($tmp)-1],"",$p);
    } else {
      if(strpos($tmp,".")>0)
        $p = "";
    }
    return $p;
  }

  private static function _resp($status=false,$message=null,$data=null){
    return ['status'=>$status,'message'=>$message,'data'=>$data];
  }

  private static function setError($msg,$header){
    self::$error = ["message"=>$msg,"code",$header];
  }
}