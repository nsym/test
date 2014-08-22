<?php
	/* /////////////////////////////////////////////////////
	//		顧客管理パッケージ（対応履歴）
	//////////////////////////////////////////////////////*/
	
	////////////////////////////////////////////////////////
	//
	//	#substance	パッケージの作成
	//  #Date		2013/12/04
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	//
	//	#substance	関数の追加
	//  #Date		2014/01/10
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	//
	//	#substance	最新対応履歴の取得
	//  #Date		2014/06/09
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	//
	//	#substance	見積り依頼の取得期限設定
	//  #Date		2014/06/10
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	
//--------------------------------------------------------------------
	
	class History {
		
		///--------------------------------------------------------------------
		/// 【コンストラクター】
		///
		/// 戻り値　なし
		///
		///	#Author yk
		/// #date	2013/12/04
		///--------------------------------------------------------------------
		function __construct(){
		}
		
		///--------------------------------------------------------------------
		/// 対応履歴リストの取得
		///
		///	#Author yk
		/// #date	2013/12/04
		///--------------------------------------------------------------------
		function getCliantHistoryListForCliantID($id){
			global $mydb;
			$sql = "SELECT * FROM ".
				   "       CLIANT_HISTORY ".
				   "WHERE ".
				   "       MASTER_ID='".$id."' ".
				   "ORDER BY HISTORY_DATE DESC;";
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
		/// 対応履歴リストの取得（見積依頼のみ）
		///
		///	#Author yk
		/// #date	2013/12/14
		///	#Author yk
		/// #date	2014/06/10
		///--------------------------------------------------------------------
		function getCliantHistoryListForRequest($staff_id){
			global $mydb;
			$search_sql ="";
			if(!empty($staff_id)){
				$search_sql = "AND STAFF_ID='".$staff_id."' ";
			}
			//１ヶ月前までを取得
			$month_ago = date('Y-m-d H:i:s', strtotime('-1 month'));
			$search_sql.="AND HISTORY_DATE > '".$month_ago."' ";
			$sql = "SELECT * FROM ".
				   "       CLIANT_HISTORY ".
				   "WHERE ".
				   "       HISTORY_CATEGORY='request' ".
				   $search_sql.
				   "ORDER BY HISTORY_DATE DESC;";
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
		/// 対応履歴リストの検索
		///
		///	#Author yk
		/// #date	2014/01/10
		///--------------------------------------------------------------------
		function getCliantHistoryListForLimit($id, $reject_category, $limit_date){
			global $mydb;
			$search_sql ="";
			if(!empty($reject_category)){
				$search_sql = "AND HISTORY_CATEGORY!='".$reject_category."' ";
			}
			if(!empty($limit_date)){
				$search_sql = "AND HISTORY_DATE<='".$limit_date."' ";
			}
			$sql = "SELECT * FROM ".
				   "       CLIANT_HISTORY ".
				   "WHERE ".
				   "       MASTER_ID='".$id."' ".
				   $search_sql.
				   "ORDER BY HISTORY_DATE DESC;";
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
		/// 対応履歴リストの取得（最新１件）
		///
		///	#Author yk
		/// #date	2014/01/10
		///--------------------------------------------------------------------
		function getCliantHistoryListRecent($id){
			global $mydb;
			$sql = "SELECT * FROM ".
				   "       CLIANT_HISTORY ".
				   "WHERE ".
				   "       MASTER_ID='".$id."' ".
				   "ORDER BY HISTORY_DATE DESC ".
				   "LIMIT 1;";
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
		/// 対応履歴情報の取得（対応履歴IDから）
		///
		///	#Author yk
		/// #date	2013/12/04
		///--------------------------------------------------------------------
		function getCliantHistoryForHistoryID($history_id){
			global $mydb;
			$sql = "SELECT * FROM ".
				   "       CLIANT_HISTORY ".
				   "WHERE ".
				   "       ID='".$history_id."';";
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
		/// 対応履歴情報の新規保存
		///
		/// #param  $post：フォームの入力情報
		///
		///	#Author yk
		/// #date	2013/12/04
		///--------------------------------------------------------------------
		function insertCliantHistory($post){
			global $mydb;
			$edit_date = date("Y-m-d H:i:s");
			$sql = "INSERT INTO ".
				   "       CLIANT_HISTORY ".
				   "VALUES('',".
				   "       '".$post['master_id']."', ".
				   "       '".$post['staff_id']."', ".
				   "       '".$post['date']."', ".
				   "       '".$post['category']."', ".
				   "       '".$post['title']."', ".
				   "       '".$post['body']."', ".
				   "       '".$post['image']."', ".
				   "       '', ".
				   "       '', ".
				   "       '', ".
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
		/// 対応履歴情報の更新
		///
		/// #param  $post：フォームの入力情報
		///
		///	#Author yk
		/// #date	2013/12/04
		///--------------------------------------------------------------------
		function updateCliantHistoryForHistoryID($post){
			global $mydb;
			$edit_date = date("Y-m-d H:i:s");
			$sql = "UPDATE ".
				   "       CLIANT_HISTORY ".
				   "SET ".
				   "       MASTER_ID='".$post['master_id']."', ".
				   "       STAFF_ID='".$post['staff_id']."', ".
				   "       HISTORY_DATE='".$post['date']."', ".
				   "       HISTORY_CATEGORY='".$post['category']."', ".
				   "       HISTORY_TITLE='".$post['title']."', ".
				   "       HISTORY_BODY='".$post['body']."', ".
				   "       HISTORY_IMAGE='".$post['image']."', ".
				   "       EDIT_DATE='".$edit_date."' ".
				   "WHERE ".
				   "       ID='".$post['history_id']."';";
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
		/// 対応履歴情報の削除
		///
		/// #param  $history_id：対応履歴ID
		///
		///	#Author yk
		/// #date	2013/12/04
		///--------------------------------------------------------------------
		function deleteCliantHistoryForHistoryID($history_id){
			global $mydb;
			$sql = "DELETE FROM ".
				   "       CLIANT_HISTORY ".
				   "WHERE ".
				   "       ID='".$history_id."';";
			$res = mysql_query($sql, $mydb);
			if($res){
				return 'delete-ok';
			}
			else{
				echo $sql;
				return 'delete-ng';
			}
		}
		
		///--------------------------------------------------------------------
		/// 対応履歴情報の削除
		///
		/// #param  $id：顧客ID
		///
		///	#Author yk
		/// #date	2013/12/04
		///--------------------------------------------------------------------
		function deleteCliantHistoryForCliantID($id){
			global $mydb;
			$sql = "DELETE FROM ".
				   "       CLIANT_HISTORY ".
				   "WHERE ".
				   "       MASTER_ID='".$id."';";
			$res = mysql_query($sql, $mydb);
			if($res){
				return 'delete-ok';
			}
			else{
				echo $sql;
				return 'delete-ng';
			}
		}
		
		///--------------------------------------------------------------------
		/// 最新対応履歴の取得
		///
		///	#Author yk
		/// #date	2014/06/09
		///--------------------------------------------------------------------
		function getCliantHistoryRecent($id){
			global $mydb;
			$sql = "SELECT * FROM ".
				   "       CLIANT_HISTORY ".
				   "WHERE ".
				   "       MASTER_ID='".$id."' ".
				   "ORDER BY HISTORY_DATE DESC;";
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
	}
?>