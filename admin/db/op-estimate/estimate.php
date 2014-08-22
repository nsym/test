<?php
	/* /////////////////////////////////////////////////////
	//		見積管理パッケージ
	//////////////////////////////////////////////////////*/
	
	////////////////////////////////////////////////////////
	//
	//	#substance	パッケージの作成
	//  #Date		2013/12/11
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	//
	//	#substance	期限切れアラートの追加
	//  #Date		2014/01/08
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	//
	//	#substance	期間絞り込みの追加
	//  #Date		2014/01/10
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	//
	//	#substance	もうすぐ期限切れの期間修正
	//  #Date		2014/06/10
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	
//--------------------------------------------------------------------
	
	class Estimate {
		
		///--------------------------------------------------------------------
		/// 【コンストラクター】
		///
		/// 戻り値　なし
		///
		///	#Author yk
		/// #date	2013/12/11
		///--------------------------------------------------------------------
		function __construct(){
		}
		
		///--------------------------------------------------------------------
		/// 見積リストの取得
		///
		///	#Author yk
		/// #date	2013/12/11
		///--------------------------------------------------------------------
		function searchEstimateMasterList($field, $value, $orderby, $start, $limit_num){
			global $mydb;
			$search_sql ="";
			if(!empty($field) && !empty($value) && $value!=='all'){
				$search_sql = "AND ".$field."='".$value."' ";
			}
			if(empty($orderby)){
				$orderby = "EDIT_DATE DESC";
			}
			$sql = "SELECT * FROM ".
				   "       ESTIMATE_MASTER ".
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
		/// 見積リストのカウント
		///
		///	#Author yk
		/// #date	2013/12/11
		///--------------------------------------------------------------------
		function countEstimateMasterList($field, $value){
			global $mydb;
			$search_sql ="";
			if(!empty($field) && !empty($value) && $value!=='all'){
				$search_sql = "AND ".$field."='".$value."' ";
			}
			$sql = "SELECT COUNT(*) FROM ".
				   "       ESTIMATE_MASTER ".
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
		/// 見積リストの取得（スタッフIDから）
		///
		///	#Author yk
		/// #date	2013/12/11
		///	#Author yk
		/// #date	2014/01/10
		///--------------------------------------------------------------------
		function searchEstimateMasterForstaffID($staff_id, $field, $value, $orderby, $start, $limit_num){
			global $mydb;
			$search_sql ="";
			if(!empty($staff_id)){
				$search_sql = "AND STAFF_ID ='".$staff_id."' ";
			}
			//その他フィールドの絞り込み
			if(!empty($field) && !empty($value) && $value!=='all'){
				$search_sql.= "AND ".$field."='".$value."' ";
			}
			//並び順の設定
			if(empty($orderby)){
				$orderby = "ID DESC";
			}
			$sql = "SELECT * FROM ".
				   "       ESTIMATE_MASTER ".
				   "WHERE ".
				   "       MASTER_STATUS!='trash' ".
				   "AND ".
				   "       MASTER_STATUS!='accept' ".
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
		/// 見積リストのカウント（スタッフIDから）
		///
		///	#Author yk
		/// #date	2013/12/11
		///	#Author yk
		/// #date	2014/01/10
		///--------------------------------------------------------------------
		function countEstimateMasterForstaffID($staff_id ,$field, $value){
			global $mydb;
			$search_sql ="";
			//スタッフIDの絞り込み
			if(!empty($staff_id)){
				$search_sql = "AND STAFF_ID ='".$staff_id."' ";
			}
			//その他フィールドの絞り込み
			if(!empty($field) && !empty($value) && $value!=='all'){
				$search_sql.= "AND ".$field."='".$value."' ";
			}
			$sql = "SELECT COUNT(*) FROM ".
				   "       ESTIMATE_MASTER ".
				   "WHERE ".
				   "       MASTER_STATUS!='trash' ".
				   "AND ".
				   "       MASTER_STATUS!='accept' ".
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
		/// 見積リストの条件検索
		///
		///	#Author yk
		/// #date	2014/01/10
		///--------------------------------------------------------------------
		function searchEstimateMasterForCondition($condition){
			global $mydb;
			extract($condition);
			$search_sql ="";
			//登録日の絞り込み
			if(!empty($start_date)){
				$search_sql.="AND MASTER_DATE>='".$start_date."' ";
			}
			if(!empty($end_date)){
				$search_sql.="AND MASTER_DATE<='".$end_date."' ";
			}
			//その他フィールドの絞り込み
			if(!empty($field) && !empty($col) && $col!=='all'){
				$search_sql.= "AND ".$field."='".$col."' ";
			}
			//並び順の設定
			if(empty($orderby)){
				$orderby = "ID DESC";
			}
			$sql = "SELECT * FROM ".
				   "       ESTIMATE_MASTER ".
				   "WHERE ".
				   "       MASTER_STATUS!='trash' ".
				   "AND ".
				   "       MASTER_STATUS!='accept' ".
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
		/// 見積リストの条件検索カウント
		///
		///	#Author yk
		/// #date	2014/01/10
		///--------------------------------------------------------------------
		function countEstimateMasterForCondition($condition){
			global $mydb;
			extract($condition);
			$search_sql ="";
			//登録日の絞り込み
			if(!empty($start_date)){
				$search_sql.="AND MASTER_DATE>='".$start_date."' ";
			}
			if(!empty($end_date)){
				$search_sql.="AND MASTER_DATE<='".$end_date."' ";
			}
			//その他フィールドの絞り込み
			if(!empty($field) && !empty($col) && $col!=='all'){
				$search_sql.= "AND ".$field."='".$col."' ";
			}
			$sql = "SELECT COUNT(*) FROM ".
				   "       ESTIMATE_MASTER ".
				   "WHERE ".
				   "       MASTER_STATUS!='trash' ".
				   "AND ".
				   "       MASTER_STATUS!='accept' ".
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
		/// もうすぐ期限切れ見積リストの取得
		///
		///	#Author yk
		/// #date	2013/12/14
		///	#Author yk
		/// #date	2014/06/10
		///--------------------------------------------------------------------
		function getDeadlineEstimateList($limit_num, $staff_id){
			global $mydb;
			$search_sql ="";
			if(!empty($staff_id)){
				$search_sql = "AND STAFF_ID='".$staff_id."' ";
			}
			$limit_date = date('Y-m-d', strtotime('+'.$limit_num.'day'));
			$sql = "SELECT * FROM ".
				   "       ESTIMATE_MASTER ".
				   "WHERE ".
				   "       MASTER_STATUS='wait' ".
				   "AND ".
				   "       MASTER_LIMIT_DATE<='".$limit_date."' ".
				   "AND ".
				   "       MASTER_LIMIT_DATE>='".date('Y-m-d')."' ".
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
		/// もうすぐ期限切れ見積リストのカウント
		///
		///	#Author yk
		/// #date	2014/01/08
		///	#Author yk
		/// #date	2014/06/10
		///--------------------------------------------------------------------
		function countDeadlineEstimateList($limit_num, $staff_id){
			global $mydb;
			$search_sql ="";
			if(!empty($staff_id)){
				$search_sql = "AND STAFF_ID='".$staff_id."' ";
			}
			$limit_date = date('Y-m-d', strtotime('+'.$limit_num.'day'));
			$sql = "SELECT COUNT(*) FROM ".
				   "       ESTIMATE_MASTER ".
				   "WHERE ".
				   "       MASTER_STATUS='wait' ".
				   "AND ".
				   "       MASTER_LIMIT_DATE<='".$limit_date."' ".
				   "AND ".
				   "       MASTER_LIMIT_DATE>='".date('Y-m-d')."' ".
				   $search_sql.
				   ";";
			$res = mysql_query($sql, $mydb);
			$row = mysql_array($res);
			if($res){
				return $row[0]['COUNT(*)'];
			}
			else{
				echo $sql;
				return false;
			}
		}
		
		///--------------------------------------------------------------------
		/// 見積情報の取得（IDから）
		///
		/// #param  $id：見積ID
		///
		///	#Author yk
		/// #date	2013/12/11
		///--------------------------------------------------------------------
		function getEstimateMasterForID($id){
			global $mydb;
			$sql = "SELECT * FROM ".
				   "       ESTIMATE_MASTER ".
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
		/// 見積情報の新規保存
		///
		/// #param  $post：フォームの入力情報
		///
		///	#Author yk
		/// #date	2013/12/11
		///--------------------------------------------------------------------
		function insertEstimateMaster($post){
			global $mydb;
			$edit_date = date("Y-m-d H:i:s");
			$sql = "INSERT INTO ".
				   "       ESTIMATE_MASTER ".
				   "VALUES('',".
				   "       '".$post['cliant_id']."', ".
				   "       '".$post['request_id']."', ".
				   "       '".$post['staff_id']."', ".
				   "       '".$post['master_date']."', ".
				   "       '".$post['limit_date']."', ".
				   "       '".$post['title']."', ".
				   "       '".$post['price']."', ".
				   "       '".$post['file_1']."', ".
				   "       '".$post['file_2']."', ".
				   "       '".$post['file_3']."', ".
				   "       '".$post['body']."', ".
				   "       '".$post['status']."', ".
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
		/// 見積情報の更新
		///
		/// #param  $post：フォームの入力情報
		///
		///	#Author yk
		/// #date	2013/12/11
		///--------------------------------------------------------------------
		function updateEstimateMasterForID($post){
			global $mydb;
			$edit_date = date("Y-m-d H:i:s");
			$sql = "UPDATE ".
				   "       ESTIMATE_MASTER ".
				   "SET ".
				   "       CLIANT_ID='".$post['cliant_id']."', ".
				   "       REQUEST_ID='".$post['request_id']."', ".
				   "       STAFF_ID='".$post['staff_id']."', ".
				   "       MASTER_DATE='".$post['master_date']."', ".
				   "       MASTER_LIMIT_DATE='".$post['limit_date']."', ".
				   "       MASTER_TITLE='".$post['title']."', ".
				   "       MASTER_PRICE='".$post['price']."', ".
				   "       MASTER_FILE_1='".$post['file_1']."', ".
				   "       MASTER_FILE_2='".$post['file_2']."', ".
				   "       MASTER_FILE_3='".$post['file_3']."', ".
				   "       MASTER_BODY='".$post['body']."', ".
				   "       MASTER_STATUS='".$post['status']."', ".
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
		/// 見積のステータス個別変更
		///
		///	#Author yk
		/// #date	2013/12/13
		///--------------------------------------------------------------------
		function changeStatusEstimateMaster($status, $id){
			global $mydb;
			$edit_date = date("Y-m-d H:i:s");
			$sql = "UPDATE ".
				   "       ESTIMATE_MASTER ".
				   "SET ".
				   "       MASTER_STATUS='".$status."', ".
				   "       EDIT_DATE='".$edit_date."' ".
				   "WHERE ".
				   "       ID='".$id."';";
			$res = mysql_query($sql, $mydb);
			if($res){
				return 'status-ok';
			}
			else{
				echo $sql;
				return 'status-ng';
			}
		}
		
		///--------------------------------------------------------------------
		/// 見積のステータス一括変更
		///
		///	#Author yk
		/// #date	2013/12/11
		///--------------------------------------------------------------------
		function changeStatusEstimateMasterForArray($status, $id_array){
			global $mydb;
			$res = true;
			$edit_date = date("Y-m-d H:i:s");
			for($i=0; $i<count($id_array) && $res; $i++){
				$sql = "UPDATE ".
					   "       ESTIMATE_MASTER ".
					   "SET ".
					   "       MASTER_STATUS='".$status."', ".
					   "       EDIT_DATE='".$edit_date."' ".
					   "WHERE ".
					   "       ID='".$id_array[$i]."';";
				$res = mysql_query($sql, $mydb);
			}
			if($res){
				return 'status-ok';
			}
			else{
				echo $sql;
				return 'status-ng';
			}
		}
		
		///--------------------------------------------------------------------
		/// 見積情報の削除
		///
		/// #param  $id：見積ID
		///
		///	#Author yk
		/// #date	2013/12/11
		///--------------------------------------------------------------------
		function deleteEstimateMasterForID($id){
			global $mydb;
			$edit_date = date("Y-m-d H:i:s");
			$sql = "UPDATE ".
				   "       ESTIMATE_MASTER ".
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
	}
?>