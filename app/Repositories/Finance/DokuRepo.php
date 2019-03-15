<?php

namespace App\Repositories\Finance;

use App\Models\Finance\Doku as Doku;
use Illuminate\Database\QueryException; 
use DB;

class DokuRepo {

	public static function create(array $data){
		try {
			return Doku::create($data);
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}

	public static function getByParam($column, $value){
		try {
			return Doku::where($column, $value)->get();
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	} 

	public static function update($id, array $data){
		try {
			return Doku::where('transidmerchant','=',$id)->update($data);
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}	
	} 

	public static function getTransID($value){
		try {
			return DB::select(DB::raw("select * from [finance].[doku] where transidmerchant='".$value."'and trxstatus='Requested'"));
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}

}
