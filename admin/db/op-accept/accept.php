<?php
	/* /////////////////////////////////////////////////////
	//		受注管理パッケージ
	//////////////////////////////////////////////////////*/
	
	////////////////////////////////////////////////////////
	//
	//	#substance	パッケージの作成
	//  #Date		2013/12/13
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	//
	//	#substance	期間絞り込みの追加
	//  #Date		2014/01/10
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	
//--------------------------------------------------------------------
	
	class Accept {
		
		///--------------------------------------------------------------------
		/// 【コンストラクター】
		///
		/// 戻り値　なし
		///
		///	#Author yk
		/// #date	2013/12/13
		///--------------------------------------------------------------------
		function __construct(){
		}
		
		///--------------------------------------------------------------------
		/// 受注リストの取得
		///
		///	#Author yk
		/// #date	2013/12/13
		///--------------------------------------------------------------------
		function searchAcceptMasterList($field, $value, $orderby, $start, $limit_num){
			global $mydb;
			$search_sql ="";
			if(!empty($field) && !empty($value) && $value!=='all'){
				$search_sql = "AND ".$field."='".$value."' ";
			}
			if(empty($orderby)){
				$orderby = "EDIT_DATE DESC";
			}
			$sql = "SELECT * FROM ".
				   "       ACCEPT_MASTER ".
				   "WHERE ".
				   "       ACCEPT_STATUS!='trash' ".
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
		/// 受注リストのカウント
		///
		///	#Author yk
		/// #date	2013/12/13
		///--------------------------------------------------------------------
		function countAcceptMasterList($field, $value){
			global $mydb;
			$search_sql ="";
			if(!empty($field) && !empty($value) && $value!=='all'){
				$search_sql = "AND ".$field."='".$value."' ";
			}
			$sql = "SELECT COUNT(*) FROM ".
				   "       ACCEPT_MASTER ".
				   "WHERE ".
				   "       ACCEPT_STATUS!='trash' ".
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
		/// 受注リストの取得（スタッフIDから）
		///
		///	#Author yk
		/// #date	2013/12/13
		///--------------------------------------------------------------------
		function searchAcceptMasterForstaffID($staff_id, $field, $value, $orderby, $start, $limit_num){
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
				   "       ACCEPT_MASTER ".
				   "WHERE ".
				   "       ACCEPT_STATUS!='trash' ".
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
		/// 受注リストのカウント（スタッフIDから）
		///
		///	#Author yk
		/// #date	2013/12/13
		///--------------------------------------------------------------------
		function countAcceptMasterForstaffID($staff_id ,$field, $value){
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
				   "       ACCEPT_MASTER ".
				   "WHERE ".
				   "       ACCEPT_STATUS!='trash' ".
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
		/// 受注リストの条件検索
		///
		///	#Author yk
		/// #date	2014/01/10
		///--------------------------------------------------------------------
		function searchAcceptMasterForCondition($condition){
			global $mydb;
			extract($condition);
			$search_sql ="";
			//登録日の絞り込み
			if(!empty($start_date)){
				$search_sql.="AND ACCEPT_DATE>='".$start_date."' ";
			}
			if(!empty($end_date)){
				$search_sql.="AND ACCEPT_DATE<='".$end_date."' ";
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
				   "       ACCEPT_MASTER ".
				   "WHERE ".
				   "       ACCEPT_STATUS!='trash' ".
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
		/// 受注リストの条件検索カウント
		///
		///	#Author yk
		/// #date	2014/01/10
		///--------------------------------------------------------------------
		function countAcceptMasterForCondition($condition){
			global $mydb;
			extract($condition);
			$search_sql ="";
			//登録日の絞り込み
			if(!empty($start_date)){
				$search_sql.="AND ACCEPT_DATE>='".$start_date."' ";
			}
			if(!empty($end_date)){
				$search_sql.="AND ACCEPT_DATE<='".$end_date."' ";
			}
			//その他フィールドの絞り込み
			if(!empty($field) && !empty($col) && $col!=='all'){
				$search_sql.= "AND ".$field."='".$col."' ";
			}
			$sql = "SELECT COUNT(*) FROM ".
				   "       ACCEPT_MASTER ".
				   "WHERE ".
				   "       ACCEPT_STATUS!='trash' ".
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
		/// 受注情報の取得（IDから）
		///
		/// #param  $id：受注ID
		///
		///	#Author yk
		/// #date	2013/12/13
		///--------------------------------------------------------------------
		function getAcceptMasterForID($id){
			global $mydb;
			$sql = "SELECT * FROM ".
				   "       ACCEPT_MASTER ".
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
		/// 受注情報の更新
		///
		/// #param  $post：フォームの入力情報
		///
		///	#Author yk
		/// #date	2013/12/13
		///--------------------------------------------------------------------
		function updateAcceptMasterForID($post){
			global $mydb;
			$edit_date = date("Y-m-d H:i:s");
			$sql = "UPDATE ".
				   "       ACCEPT_MASTER ".
				   "SET ".
				   "       STAFF_ID='".$post['staff_id']."', ".
				   "       ACCEPT_DATE='".$post['accept_date']."', ".
				   "       ACCEPT_LIMIT='".$post['accept_limit']."', ".
				   "       ACCEPT_TITLE='".$post['title']."', ".
				   "       ACCEPT_PRICE='".$post['price']."', ".
				   "       ACCEPT_NOTES='".$post['notes']."', ".
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
		///  受注情報の削除
		///
		/// #param  $id：受注ID
		///
		///	#Author yk
		/// #date	2013/12/14
		///--------------------------------------------------------------------
		function deleteAcceptMasterForID($id){
			global $mydb;
			$edit_date = date("Y-m-d H:i:s");
			$sql = "UPDATE ".
				   "       ACCEPT_MASTER ".
				   "SET ".
				   "       ACCEPT_STATUS='trash', ".
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