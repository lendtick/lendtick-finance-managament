<?php

namespace App\Repositories\Finance;

use App\Models\Finance\Doku;
use Illuminate\Database\QueryException;
use DB;

class PaymentDokuRepo {

	public static function list($request, $d=null,$st=0,$l=10,$sr=null,$mw=null){

		if(is_numeric($st) && is_numeric($l)){

			$field = ["b.name", "d.name_company", "b.id_koperasi", "b.phone_number", "c.employee_starting_date", "e.name_grade", "b.npwp", "b.email", "b.loan_plafond", "b.microloan_plafond", "a.id_workflow_status", "e.transaction_date"];
			$m_field = [
				"nama"           => ["b.name", "like"],
				"tgl_masuk"      => ["b.date_become_member", "between"],
				"tgl_pengajuan"  => ["a.created_at", "between"],
				"status"         => ["a.id_workflow_status", "=="],
				"company"        => ["c.id_company", "=="]
			];
			// where indication
			$where = [];
			$where_not_in = "";

			$c_all = DB::select(DB::raw("
				SELECT COUNT(a.id_user) AS cnt 
				FROM [user].[user] a
				INNER JOIN [finance].[doku] b ON a.id_user=b.id_user
				WHERE 1=1
				AND b.billertrx is null"
			))[0]->cnt;

			if(count((array)$mw) == 0){ // mw = manual filter
				if(!is_null($d) && !empty($d))
					foreach($field AS $i => $row){
						$where[] = $row." LIKE '%".$d."%'";
					}
				} else {
					foreach($mw AS $fl => $vl){
						if(isset($m_field[$fl])){
							if($vl != "" && !is_null($vl)){
								if(in_array($m_field[$fl][1],array("==", "like")))
									$operand = $m_field[$fl][1] == "=="? "='".$vl."'":($m_field[$fl][1] == "like"? "LIKE '%".$vl."%'":NULL);
								else if($m_field[$fl][1] == "between"){
									$dte = explode(" - ", $vl);
									if(count((array)$dte) == 2){
										$operand = "BETWEEN '".$dte[0]."' AND '".$dte[1]."'";
									}
								}

								$where[] = $m_field[$fl][0]." ".$operand;
							}
						}
					}
				}
				// counting filter
				$c_fil = DB::select(DB::raw("
					SELECT COUNT(a.id_user) AS cnt 
					from [finance].[doku] e
					inner join [user].[user] a on a.id_user=e.id_user
					LEFT JOIN [user].[user_profile] b ON a.id_user=b.id_user
					LEFT JOIN [user].[user_company] c ON c.id_user_profile=b.id_user_profile
					INNER JOIN [user].[master_company] d ON c.id_company=d.id_company
					INNER JOIN [user].[master_grade] f ON f.id_grade=c.id_grade
					where e.billertrx is null
					".$where_not_in.(count((array)$where)>0?"AND (".implode(' OR ', $where).")":"")
				))[0]->cnt;

			// order by
				$order = "";
				if(!is_null($sr)){
					$sort = explode(",",$sr);
					if(count($sort) > 1)
						$order .= "ORDER BY ".$sort[0]." ".$sort[1];
					else
						$order .= "ORDER BY ".$sr." asc";
				}

			// get length
				$length = [
					"OFFSET ".$st." ROWS",
					"FETCH NEXT ".$l." ROWS ONLY"
				];

				// data
				$data = DB::select(DB::raw("
					SELECT a.id_user, b.id_user_profile, b.name, b.id_koperasi, c.id_company, d.name_company, b.phone_number, b.id_koperasi, b.npwp, b.email, b.loan_plafond, b.microloan_plafond, b.personal_photo, b.personal_identity_path, e.id,
					e.transidmerchant,
					e.totalamount,
					e.payment_channel,
					e.paymentcode, 
					e.transaction_date,
					a.username,
					a.password,
					a.id_role_master,
					a.id_workflow_status,
					a.android_device_token,
					a.ios_device_token,
					a.is_new_user,
					f.name_grade,
					c.id_employee
					from [finance].[doku] e
					inner join [user].[user] a on a.id_user=e.id_user
					LEFT JOIN [user].[user_profile] b ON a.id_user=b.id_user
					LEFT JOIN [user].[user_company] c ON c.id_user_profile=b.id_user_profile
					INNER JOIN [user].[master_company] d ON c.id_company=d.id_company
					INNER JOIN [user].[master_grade] f ON f.id_grade=c.id_grade
					where e.billertrx is null
					".$where_not_in.(count($where)>0?"AND (".implode(' OR ', $where).")":"")." ".$order." ".$length[0]." ".$length[1]
				));

				return ['count_all'=>$c_all,'count_filter'=>$c_fil,'data'=>$data];
			}
			return [];
		}
	}
