<?php

namespace App\Helpers;

class MimeHelper {
    public static $mime = [
        'text/x-comma-separated-values' => 'csv',
        'text/comma-separated-values' => 'csv',
        'application/x-csv' => 'csv',
        'text/x-csv' => 'csv',
        'text/csv' => 'csv',
        'application/csv' => 'csv',
        'application/octet-stream' => 'exe',
        'application/x-msdownload' => 'exe',
        'application/x-photoshop' => 'psd',
        'image/vnd.adobe.photoshop' => 'psd',
        'application/pdf' => 'pdf',
        'application/vnd.ms-excel' => 'xls',
        'application/msexcel' => 'xls',
        'application/x-msexcel' => 'xls',
        'application/x-ms-excel' => 'xls',
        'application/x-excel' => 'xls',
        'application/x-dos_ms_excel' => 'xls',
        'application/xls' => 'xls',
        'application/x-xls' => 'xls',
        'application/excel' => 'xls',
        'application/x-zip' => 'zip',
        'application/zip' => 'zip',
        'application/x-zip-compressed' => 'zip',
        'multipart/x-zip' => 'zip',
        'image/bmp' => 'bmp',
        'image/x-bmp' => 'bmp',
        'image/x-bitmap' => 'bmp',
        'image/x-xbitmap' => 'bmp',
        'image/x-win-bitmap' => 'bmp',
        'image/x-windows-bmp' => 'bmp',
        'image/ms-bmp' => 'bmp',
        'image/x-ms-bmp' => 'bmp',
        'application/bmp' => 'bmp',
        'application/x-bmp' => 'bmp',
        'application/x-win-bitmap' => 'bmp',
        'image/gif' => 'gif',
        'image/jpeg' => 'jpg',
        'image/pjpeg' => 'jpeg',
        'image/jp2' => 'jp2',
        'image/jpm' => 'jpm',
        'image/jpx' => 'jpx',
        'image/png' => 'png',
        'image/x-png' => 'png',
        'application/xml' => 'xml',
        'application/msword' => 'doc',
        'application/vnd.ms-office' => 'doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        'text/xml' => 'docx',
        'text/xml' => 'docx',
        'text/xml' => 'docx',
    ];

    public static function extension($m=null){
        if(is_string($m)){
            return isset(self::$mime[$m])?self::$mime[$m]:null;
        }
        return null;
    }
}
