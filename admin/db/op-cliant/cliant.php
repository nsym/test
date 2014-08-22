<?php
	/* /////////////////////////////////////////////////////
	//		顧客管理パッケージ
	//////////////////////////////////////////////////////*/
	
	////////////////////////////////////////////////////////
	//
	//	#substance	パッケージの作成
	//  #Date		2013/11/04
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	//
	//	#substance	フィールド追加による変更
	//  #Date		2013/11/21
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	//
	//	#substance	メールアドレスを非必須へ変更
	//  #Date		2014/01/07
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	//
	//	#substance	重複登録チェックの追加
	//  #Date		2014/04/16
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	//
	//	#substance	最新対応履歴のフィールド追加
	//  #Date		2014/06/09
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	
//--------------------------------------------------------------------
	
	class Cliant {
		
		///--------------------------------------------------------------------
		/// 【コンストラクター】
		///
		/// 戻り値　なし
		///
		///	#Author yk
		/// #date	2013/11/04
		///--------------------------------------------------------------------
		function __construct(){
		}
		
		///--------------------------------------------------------------------
		/// 顧客リストの取得
		///
		///	#Author yk
		/// #date	2013/11/04
		///--------------------------------------------------------------------
		function searchCliantMasterList($field, $value, $orderby, $start, $limit_num){
			global $mydb;
			$search_sql ="";
			if(!empty($field) && !empty($value) && $value!=='all'){
				if($field==='MASTER_KANA'){
					global $kana_list;
					$search_sql = "AND (MASTER_KANA LIKE '".$kana_list[$value][0]."%' ";
					for($i=1; $i<count($kana_list[$value]); $i++){
						$search_sql.= "OR MASTER_KANA LIKE '".$kana_list[$value][$i]."%' ";
					}
					$search_sql.= ") ";
				}else{
					$search_sql = "AND ".$field."='".$value."' ";
				}
			}
			if(empty($orderby)){
				$orderby = "EDIT_DATE DESC";
			}
			$sql = "SELECT * FROM ".
				   "       CLIANT_MASTER ".
				   "WHERE ".
				   "       MASTER_STATUS!='trash' ".
				   $search_sql.
				   "ORDER BY ".$orderby." ".
				   "LIMIT ".$start.",".$limit_num.";";
			$res = mysql_query($sql, $mydb);
			$row = mysql_array($res);
			if($res){
				return $row;
			}
			else{
				echo $sql;
				return false;
			}
		}
		
		///--------------------------------------------------------------------
		/// 顧客リストのカウント
		///
		///	#Author yk
		/// #date	2013/11/04
		///--------------------------------------------------------------------
		function countCliantMasterList($field, $value){
			global $mydb;
			$search_sql ="";
			if(!empty($field) && !empty($value) && $value!=='all'){
				if($field==='MASTER_KANA'){
					global $kana_list;
					$search_sql = "AND (MASTER_KANA LIKE '".$kana_list[$value][0]."%' ";
					for($i=1; $i<count($kana_list[$value]); $i++){
						$search_sql.= "OR MASTER_KANA LIKE '".$kana_list[$value][$i]."%' ";
					}
					$search_sql.= ") ";
				}else{
					$search_sql = "AND ".$field."='".$value."' ";
				}
			}
			$sql = "SELECT COUNT(*) FROM ".
				   "       CLIANT_MASTER ".
				   "WHERE ".
				   "       MASTER_STATUS!='trash' ".
				   $search_sql.
				   ";";
			$res = mysql_query($sql, $mydb);
			$row = mysql_array($res);
			if($res){
				return $row[0]["COUNT(*)"];
			}
			else{
				echo $sql;
				return false;
			}
		}
		
		///--------------------------------------------------------------------
		/// 顧客リストの取得（グループIDから）
		///
		///	#Author yk
		/// #date	2013/11/04
		///--------------------------------------------------------------------
		function searchCliantMasterForGroupID($group_id, $field, $value, $orderby, $start, $limit_num){
			global $mydb;
			$search_sql ="";
			if(!empty($group_id)){
				$search_sql = "AND MASTER_GROUP LIKE '%,".$group_id.",%' ";
			}
			//その他フィールドの絞り込み
			if(!empty($field) && !empty($value) && $value!=='all'){
				if($field==='MASTER_KANA'){
					global $kana_list;
					$search_sql.= "AND (MASTER_KANA LIKE '".$kana_list[$value][0]."%' ";
					for($i=1; $i<count($kana_list[$value]); $i++){
						$search_sql.= "OR MASTER_KANA LIKE '".$kana_list[$value][$i]."%' ";
					}
					$search_sql.= ") ";
				}else{
					$search_sql.= "AND ".$field."='".$value."' ";
				}
			}
			//並び順の設定
			if(empty($orderby)){
				$orderby = "ID DESC";
			}
			$sql = "SELECT * FROM ".
				   "       CLIANT_MASTER ".
				   "WHERE ".
				   "       MASTER_STATUS!='trash' ".
				   $search_sql.
				   "ORDER BY ".$orderby." ".
				   "LIMIT ".$start.",".$limit_num.";";
			$res = mysql_query($sql, $mydb);
			$row = mysql_array($res);
			if($res){
				return $row;
			}
			else{
				echo $sql;
				return false;
			}
		}
		
		///--------------------------------------------------------------------
		/// 顧客リストのカウント（グループIDから）
		///
		///	#Author yk
		/// #date	2013/11/04
		///--------------------------------------------------------------------
		function countCliantMasterForGroupID($group_id ,$field, $value){
			global $mydb;
			$search_sql ="";
			//グループIDの絞り込み
			if(!empty($group_id)){
				$search_sql = "AND MASTER_GROUP LIKE '%,".$group_id.",%' ";
			}
			//その他フィールドの絞り込み
			if(!empty($field) && !empty($value) && $value!=='all'){
				if($field==='MASTER_KANA'){
					global $kana_list;
					$search_sql.= "AND (MASTER_KANA LIKE '".$kana_list[$value][0]."%' ";
					for($i=1; $i<count($kana_list[$value]); $i++){
						$search_sql.= "OR MASTER_KANA LIKE '".$kana_list[$value][$i]."%' ";
					}
					$search_sql.= ") ";
				}else{
					$search_sql.= "AND ".$field."='".$value."' ";
				}
			}
			$sql = "SELECT COUNT(*) FROM ".
				   "       CLIANT_MASTER ".
				   "WHERE ".
				   "       MASTER_STATUS!='trash' ".
				   $search_sql.
				   ";";
			$res = mysql_query($sql, $mydb);
			$row = mysql_array($res);
			if($res){
				return $row[0]["COUNT(*)"];
			}
			else{
				echo $sql;
				return false;
			}
		}
		
		///--------------------------------------------------------------------
		/// 顧客リストの条件検索
		///
		///	#Author yk
		/// #date	2013/11/06
		///	#Author yk
		/// #date	2013/11/21
		///--------------------------------------------------------------------
		function searchCliantMasterForCondition($condition){
			global $mydb;
			extract($condition);
			$search_sql ="";
			//メルマガフラグの絞り込み
			if(!empty($mailmaga)){
				$search_sql.="AND MAILMAGA_FLAG='".$mailmaga."' ";
			}
			//イプロス会員フラグの絞り込み
			if(!empty($ipros)){
				$search_sql.="AND IPROS_FLAG='".$ipros."' ";
			}
			//展示会フラグの絞り込み
			if(!empty($exhibition)){
				$search_sql.="AND EXHIBITION_FLAG='".$exhibition."' ";
			}
			//京都試作ネットフラグの絞り込み
			if(!empty($kyoto)){
				$search_sql.="AND KYOTO_FLAG='".$kyoto."' ";
			}
			//主担当スタッフの絞り込み
			if(!empty($staff)){
				$search_sql.="AND STAFF_ID='".$staff."' ";
			}
			//顧客ランクの絞り込み
			if(!empty($rank)){
				$search_sql.="AND MASTER_RANK='".$rank."' ";
			}
			//エリアの絞り込み
			if(!empty($area)){
				if($area!=='except'){
					$search_sql.="AND MASTER_AREA='".$area."' ";
				}else{
					global $area_list;
					$search_sql.="AND ( MASTER_AREA!='' ";
					foreach($area_list as $prefecture){
						foreach($prefecture as $value){
							$search_sql.="AND MASTER_AREA!='".$value."' ";
						}
					}
					$search_sql.=") ";
				}
			}
			//登録日の絞り込み
			if(!empty($start_date)){
				$search_sql.="AND INS_DATE>='".$start_date."' ";
			}
			if(!empty($end_date)){
				$search_sql.="AND INS_DATE<='".$end_date."' ";
			}
			//その他フィールドの絞り込み
			if(!empty($field) && !empty($col) && $col!=='all'){
				if($field==='MASTER_KANA'){
					global $kana_list;
					$search_sql.= "AND (MASTER_KANA LIKE '".$kana_list[$col][0]."%' ";
					for($i=1; $i<count($kana_list[$col]); $i++){
						$search_sql.= "OR MASTER_KANA LIKE '".$kana_list[$col][$i]."%' ";
					}
					$search_sql.= ") ";
				}else{
					$search_sql.= "AND ".$field."='".$col."' ";
				}
			}
			//並び順の設定
			if(empty($orderby)){
				$orderby = "ID DESC";
			}
			$sql = "SELECT * FROM ".
				   "       CLIANT_MASTER ".
				   "WHERE ".
				   "       MASTER_STATUS!='trash' ".
				   $search_sql.
				   "ORDER BY ".$orderby." ".
				   "LIMIT ".$start.",".$limit_num.";";
			$res = mysql_query($sql, $mydb);
			$row = mysql_array($res);
			if($res){
				return $row;
			}
			else{
				echo $sql;
				return false;
			}
		}
		
		///--------------------------------------------------------------------
		/// 顧客リストの条件検索カウント
		///
		///	#Author yk
		/// #date	2013/11/06
		///	#Author yk
		/// #date	2013/11/21
		///--------------------------------------------------------------------
		function countCliantMasterForCondition($condition){
			global $mydb;
			extract($condition);
			$search_sql ="";
			//メルマガフラグの絞り込み
			if(!empty($mailmaga)){
				$search_sql.="AND MAILMAGA_FLAG='".$mailmaga."' ";
			}
			//イプロス会員フラグの絞り込み
			if(!empty($ipros)){
				$search_sql.="AND IPROS_FLAG='".$ipros."' ";
			}
			//展示会フラグの絞り込み
			if(!empty($exhibition)){
				$search_sql.="AND EXHIBITION_FLAG='".$exhibition."' ";
			}
			//京都試作ネットフラグの絞り込み
			if(!empty($kyoto)){
				$search_sql.="AND KYOTO_FLAG='".$kyoto."' ";
			}
			//主担当スタッフの絞り込み
			if(!empty($staff)){
				$search_sql.="AND STAFF_ID='".$staff."' ";
			}
			//顧客ランクの絞り込み
			if(!empty($rank)){
				$search_sql.="AND MASTER_RANK='".$rank."' ";
			}
			//エリアの絞り込み
			if(!empty($area)){
				if($area!=='except'){
					$search_sql.="AND MASTER_AREA='".$area."' ";
				}else{
					global $area_list;
					$search_sql.="AND ( MASTER_AREA!='' ";
					foreach($area_list as $prefecture){
						foreach($prefecture as $value){
							$search_sql.="AND MASTER_AREA!='".$value."' ";
						}
					}
					$search_sql.=") ";
				}
			}
			//登録日の絞り込み
			if(!empty($start_date)){
				$search_sql.="AND INS_DATE>='".$start_date."' ";
			}
			if(!empty($end_date)){
				$search_sql.="AND INS_DATE<='".$end_date."' ";
			}
			//その他フィールドの絞り込み
			if(!empty($field) && !empty($col) && $col!=='all'){
				if($field==='MASTER_KANA'){
					global $kana_list;
					$search_sql.= "AND (MASTER_KANA LIKE '".$kana_list[$col][0]."%' ";
					for($i=1; $i<count($kana_list[$col]); $i++){
						$search_sql.= "OR MASTER_KANA LIKE '".$kana_list[$col][$i]."%' ";
					}
					$search_sql.= ") ";
				}else{
					$search_sql.= "AND ".$field."='".$col."' ";
				}
			}
			if(empty($orderby)){
				$orderby = "ID DESC";
			}
			$sql = "SELECT COUNT(*) FROM ".
				   "       CLIANT_MASTER ".
				   "WHERE ".
				   "       MASTER_STATUS!='trash' ".
				   $search_sql.
				   ";";
			$res = mysql_query($sql, $mydb);
			$row = mysql_array($res);
			if($res){
				return $row[0]["COUNT(*)"];
			}
			else{
				echo $sql;
				return false;
			}
		}
		
		///--------------------------------------------------------------------
		/// 顧客リストのキーワード検索
		///
		///	#Author yk
		/// #date	2013/11/07
		///--------------------------------------------------------------------
		function searchCliantMasterListForKeyword($keyword, $field, $value, $orderby, $start, $limit_num){
			global $mydb;
			$search_sql ="";
			//キーワード検索
			if(!empty($keyword)){
				$search_sql.= "AND (MASTER_NAME LIKE '%".$keyword."%' ".
				              "OR MASTER_KANA LIKE '%".$keyword."%' ".
							  "OR MASTER_COMPANY LIKE '%".$keyword."%' ".
							  "OR MASTER_POST LIKE '%".$keyword."%' ".
							  "OR MASTER_BUSINESS LIKE '%".$keyword."%' ".
							  "OR MASTER_JOB LIKE '%".$keyword."%' ".
							  "OR MASTER_TEL LIKE '%".$keyword."%' ".
							  "OR MASTER_FAX LIKE '%".$keyword."%' ".
							  "OR MASTER_MAIL LIKE '%".$keyword."%') ";
			}
			//その他フィールドの絞り込み
			if(!empty($field) && !empty($value) && $value!=='all'){
				if($field==='MASTER_KANA'){
					global $kana_list;
					$search_sql.= "AND (MASTER_KANA LIKE '".$kana_list[$value][0]."%' ";
					for($i=1; $i<count($kana_list[$value]); $i++){
						$search_sql.= "OR MASTER_KANA LIKE '".$kana_list[$value][$i]."%' ";
					}
					$search_sql.= ") ";
				}else{
					$search_sql.= "AND ".$field."='".$value."' ";
				}
			}
			if(empty($orderby)){
				$orderby = "ID DESC";
			}
			$sql = "SELECT * FROM ".
				   "       CLIANT_MASTER ".
				   "WHERE ".
				   "       MASTER_STATUS!='trash' ".
				   $search_sql.
				   "ORDER BY ".$orderby." ".
				   "LIMIT ".$start.",".$limit_num.";";
			$res = mysql_query($sql, $mydb);
			$row = mysql_array($res);
			if($res){
				return $row;
			}
			else{
				echo $sql;
				return false;
			}
		}
		
		///--------------------------------------------------------------------
		/// 顧客リストのキーワード検索カウント
		///
		///	#Author yk
		/// #date	2013/11/07
		///--------------------------------------------------------------------
		function countCliantMasterListForKeyword($keyword, $field, $value){
			global $mydb;
			$search_sql ="";
			//キーワード検索
			if(!empty($keyword)){
				$search_sql.= "AND (MASTER_NAME LIKE '%".$keyword."%' ".
				              "OR MASTER_KANA LIKE '%".$keyword."%' ".
							  "OR MASTER_COMPANY LIKE '%".$keyword."%' ".
							  "OR MASTER_BUSINESS LIKE '%".$keyword."%' ".
							  "OR MASTER_JOB LIKE '%".$keyword."%' ".
							  "OR MASTER_TEL LIKE '%".$keyword."%' ".
							  "OR MASTER_FAX LIKE '%".$keyword."%' ".
							  "OR MASTER_MAIL LIKE '%".$keyword."%') ";
			}
			//その他フィールドの絞り込み
			if(!empty($field) && !empty($value) && $value!=='all'){
				if($field==='MASTER_KANA'){
					global $kana_list;
					$search_sql.= "AND (MASTER_KANA LIKE '".$kana_list[$value][0]."%' ";
					for($i=1; $i<count($kana_list[$value]); $i++){
						$search_sql.= "OR MASTER_KANA LIKE '".$kana_list[$value][$i]."%' ";
					}
					$search_sql.= ") ";
				}else{
					$search_sql.= "AND ".$field."='".$value."' ";
				}
			}
			$sql = "SELECT COUNT(*) FROM ".
				   "       CLIANT_MASTER ".
				   "WHERE ".
				   "       MASTER_STATUS!='trash' ".
				   $search_sql.
				   ";";
			$res = mysql_query($sql, $mydb);
			$row = mysql_array($res);
			if($res){
				return $row[0]["COUNT(*)"];
			}
			else{
				echo $sql;
				return false;
			}
		}
		
		///--------------------------------------------------------------------
		/// メルマガ顧客リストの取得（グループIDから）
		///
		///	#Author yk
		/// #date	2013/11/13
		///--------------------------------------------------------------------
		function getCliantcsvListForGroupID($group_id){
			global $mydb;
			$search_sql ="";
			if(!empty($group_id)){
				$search_sql ="WHERE MASTER_GROUP LIKE '%,".$group_id.",%' ";
			}
			$sql = "SELECT * FROM ".
				   "       CLIANT_MASTER ".
				   $search_sql.
				   ";";
			$res = mysql_query($sql, $mydb);
			$row = mysql_array($res);
			if($res){
				return $row;
			}
			else{
				echo $sql;
				return false;
			}
		}
		
		///--------------------------------------------------------------------
		/// 顧客情報の取得（IDから）
		///
		/// #param  $id：顧客ID
		///
		///	#Author yk
		/// #date	2013/11/04
		///--------------------------------------------------------------------
		function getCliantMasterForID($id){
			global $mydb;
			$sql = "SELECT * FROM ".
				   "       CLIANT_MASTER ".
				   "WHERE ".
				   "	   ID='".$id."';";
			$res = mysql_query($sql, $mydb);
			$row = mysql_array($res);
			if($res){
				return $row[0];
			}
			else{
				echo $sql;
				return false;
			}
		}
		
		///--------------------------------------------------------------------
		/// 顧客情報の取得（メールアドレスから）
		///
		/// #param  $mail_address：顧客メールアドレス
		///
		///	#Author yk
		/// #date	2013/12/19
		///--------------------------------------------------------------------
		function getCliantMasterForMail($mail_address){
			global $mydb;
			$sql = "SELECT * FROM ".
				   "       CLIANT_MASTER ".
				   "WHERE ".
				   "	   MASTER_MAIL='".$mail_address."';";
			$res = mysql_query($sql, $mydb);
			$row = mysql_array($res);
			if($res){
				return $row[0];
			}
			else{
				echo $sql;
				return false;
			}
		}
		
		///--------------------------------------------------------------------
		/// 顧客情報の重複チェック（姓とメールアドレス）
		///
		///	#Author yk
		/// #date	2014/04/16
		///--------------------------------------------------------------------
		function checkCliantDuplicate($post){
			global $mydb;
			$reject_sql ="";
			if(!empty($post["id"])){
				$reject_sql.= "AND ID!='".$post["id"]."' ";
			}
			$sql = "SELECT * FROM ".
				   "       CLIANT_MASTER ".
				   "WHERE ".
				   "	   MASTER_NAME LIKE '%".$post["name_array"][0]."%' ".
				   "AND ".
				   "	   MASTER_MAIL='".$post["mail"]."' ".
				   $reject_sql.
				   ";";
			$res = mysql_query($sql, $mydb);
			$row = mysql_array($res);
			if($res){
				if(!empty($row)){
					return true;
				}else{
					echo $sql;
					return false;
				}
			}
			else{
				echo $sql;
				return false;
			}
		}
		
		///--------------------------------------------------------------------
		/// 顧客情報の新規保存
		///
		/// #param  $post：フォームの入力情報
		///
		///	#Author yk
		/// #date	2013/11/04
		///	#Author mukai
		/// #date	2013/11/21
		///	#Author yk
		/// #date	2014/01/07
		///	#Author yk
		/// #date	2014/04/16
		///	#Author yk
		/// #date	2014/06/09
		///--------------------------------------------------------------------
		function insertCliantMaster($post){
			global $mydb;
			//メールアドレスチェック
			if(!empty($post['mail']) && !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $post['mail'])){
				return 'mail-ng';
			}
			//重複登録チェック
			if($this->checkCliantDuplicate($post)){
				return 'duplicate-ng';
			}
			$edit_date = date("Y-m-d H:i:s");
			$sql = "INSERT INTO ".
				   "       CLIANT_MASTER ".
				   "VALUES('',".
				   "       '".$post['staffid']."', ".
				   "       '".$post['group']."', ".
				   "       '".$post['name']."', ".
				   "       '".$post['kana']."', ".
				   "       '".$post['rank']."', ".
				   "       '".$post['company']."', ".
				   "       '".$post['url']."', ".
				   "       '".$post['office']."', ".
				   "       '".$post['belong']."', ".
				   "       '".$post['post']."', ".
				   "       '".$post['business']."', ".
				   "       '".$post['job']."', ".
				   "       '".$post['tel']."', ".
				   "       '".$post['fax']."', ".
				   "       '".$post['mail']."', ".
				   "       '".$post['zip']."', ".
				   "       '".$post['area']."', ".
				   "       '".$post['address']."', ".
				   "       '".$post['notes']."', ".
				   "       '".$post['ipros_flag']."', ".
				   "       '".$post['mailmaga_flag']."', ".
				   "       '".$post['exhibition_flag']."', ".
				   "       '".$post['kyoto_flag']."', ".
				   "       '0000-00-00 00:00:00', ".	//最新対応日時
				   "       '', ".						//最新対応スタッフ
				   "       '', ".
				   "       '".$edit_date."', ".
				   "       '".$edit_date."');";
			$res = mysql_query($sql, $mydb);
			if($res){
				return 'insert-ok';
			}
			else{
				echo $sql;
				return 'insert-ng';
			}
		}
		
		///--------------------------------------------------------------------
		/// 顧客情報の更新
		///
		/// #param  $post：フォームの入力情報
		///
		///	#Author yk
		/// #date	2013/11/04
		///	#Author mukai
		/// #date	2013/11/21
		///	#Author yk
		/// #date	2014/01/07
		///	#Author yk
		/// #date	2014/04/16
		///--------------------------------------------------------------------
		function updateCliantMasterForID($post){
			global $mydb;
			//メールアドレスチェック
			if(!empty($post['mail']) && !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $post['mail'])){
				return 'mail-ng';
			}
			//重複登録チェック
			if($this->checkCliantDuplicate($post)){
				return 'duplicate-ng';
			}
			$edit_date = date("Y-m-d H:i:s");
			$sql = "UPDATE ".
				   "       CLIANT_MASTER ".
				   "SET ".
				   "       STAFF_ID='".$post['staffid']."', ".
				   "       MASTER_GROUP='".$post['group']."', ".
				   "       MASTER_NAME='".$post['name']."', ".
				   "       MASTER_KANA='".$post['kana']."', ".
				   "       MASTER_RANK='".$post['rank']."', ".
				   "       MASTER_COMPANY='".$post['company']."', ".
				   "       MASTER_URL='".$post['url']."', ".
				   "       MASTER_OFFICE='".$post['office']."', ".
				   "       MASTER_BELONG='".$post['belong']."', ".
				   "       MASTER_POST='".$post['post']."', ".
				   "       MASTER_BUSINESS='".$post['business']."', ".
				   "       MASTER_JOB='".$post['job']."', ".
				   "       MASTER_TEL='".$post['tel']."', ".
				   "       MASTER_FAX='".$post['fax']."', ".
				   "       MASTER_MAIL='".$post['mail']."', ".
				   "       MASTER_ZIPCODE='".$post['zip']."', ".
				   "       MASTER_AREA='".$post['area']."', ".
				   "       MASTER_ADDRESS='".$post['address']."', ".
				   "       MASTER_NOTES='".$post['notes']."', ".
				   "       IPROS_FLAG='".$post['ipros_flag']."', ".
				   "       MAILMAGA_FLAG='".$post['mailmaga_flag']."', ".
				   "       EXHIBITION_FLAG='".$post['exhibition_flag']."', ".
				   "       KYOTO_FLAG='".$post['kyoto_flag']."', ".
				   "       EDIT_DATE='".$edit_date."' ".
				   "WHERE ".
				   "       ID='".$post['id']."';";
			$res = mysql_query($sql, $mydb);
			if($res){
				return 'update-ok';
			}
			else{
				echo $sql;
				return 'update-ng';
			}
		}
		
		///--------------------------------------------------------------------
		/// 顧客のグループ一括登録
		///
		///	#Author yk
		/// #date	2013/11/06
		///--------------------------------------------------------------------
		function addGroupCliantMaster($group_id, $id_array){
			global $mydb;
			$res = true;
			$edit_date = date("Y-m-d H:i:s");
			for($i=0; $i<count($id_array) && $res; $i++){
				$cliant_data = $this->getCliantMasterForID($id_array[$i]);
				if(!empty($cliant_data) && !preg_match('/,'.$group_id.',/', $cliant_data["MASTER_GROUP"])){
					$setGroupText = !empty($cliant_data["MASTER_GROUP"])? $cliant_data["MASTER_GROUP"].$group_id.',': ','.$cliant_data["MASTER_GROUP"].$group_id.',';
					$sql = "UPDATE ".
						   "       CLIANT_MASTER ".
						   "SET ".
						   "       MASTER_GROUP='".$setGroupText."', ".
						   "       EDIT_DATE='".$edit_date."' ".
						   "WHERE ".
						   "       ID='".$id_array[$i]."';";
					$res = mysql_query($sql, $mydb);
					echo $sql;
				}
			}
			if($res){
				return 'group-ok';
			}
			else{
				echo $sql;
				return 'group-ng';
			}
		}
		
		///--------------------------------------------------------------------
		/// 顧客情報の削除
		///
		/// #param  $id：顧客ID
		///
		///	#Author yk
		/// #date	2013/11/04
		///	#Author yk
		/// #date	2013/11/12
		///--------------------------------------------------------------------
		function deleteCliantMasterForID($id){
			global $mydb;
			$edit_date = date("Y-m-d H:i:s");
			$sql = "UPDATE ".
				   "       CLIANT_MASTER ".
				   "SET ".
				   "       MASTER_STATUS='trash', ".
				   "       EDIT_DATE='".$edit_date."' ".
				   "WHERE ".
				   "       ID='".$id."';";
			$res = mysql_query($sql, $mydb);
			if($res){
				return 'trash-ok';
			}
			else{
				echo $sql;
				return 'trash-ng';
			}
		}
		
		///--------------------------------------------------------------------
		/// 対応履歴のフィールド挿入
		///
		///	#Author yk
		/// #date	2014/06/09
		///--------------------------------------------------------------------
		function updateCliantRecentHistoryForID($id, $recent_history_date, $recent_history_staff){
			global $mydb;
			$sql = "UPDATE ".
				   "       CLIANT_MASTER ".
				   "SET ".
				   "       RECENT_HISTORY_DATE='".$recent_history_date."', ".
				   "       RECENT_HISTORY_STAFF='".$recent_history_staff."' ".
				   "WHERE ".
				   "       ID='".$id."';";
			$res = mysql_query($sql, $mydb);
			if($res){
				return true;
			}
			else{
				echo $sql;
				return false;
			}
		}
	}
?>