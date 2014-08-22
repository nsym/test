<?php
	/* /////////////////////////////////////////////////////
	//		メルマガ管理パッケージ
	//////////////////////////////////////////////////////*/
	
	////////////////////////////////////////////////////////
	//
	//	#substance	パッケージの作成
	//  #Date		2013/11/13
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	//
	//	#substance	メルマガ顧客リストの取得の仕様変更
	//  #Date		2014/01/10
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	//
	//	#substance	メルマガ顧客情報取得の追加
	//  #Date		2014/01/11
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
		/// #date	2013/11/13
		///--------------------------------------------------------------------
		function __construct(){
		}
		
		///--------------------------------------------------------------------
		/// メルマガ顧客リストの取得（グループIDから）
		///
		///	#Author yk
		/// #date	2013/11/13
		///	#Author yk
		/// #date	2014/01/10
		///--------------------------------------------------------------------
		function getCliantSendListForGroupID($group_id){
			global $mydb;
			$search_sql ="";
			if(!empty($group_id)){
				$search_sql ="AND MASTER_GROUP LIKE '%,".$group_id.",%' ";
			}
			$sql = "SELECT ".
				   "       ID, ".
				   "       MASTER_NAME, ".
				   "       MASTER_COMPANY, ".
				   "       MASTER_POST, ".
				   "       MASTER_MAIL ".
				   "FROM ".
				   "       CLIANT_MASTER ".
				   "WHERE ".
				   "       MAILMAGA_FLAG='send' ".
				   "AND ".
				   "       MASTER_STATUS!='trash' ".
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
		/// メルマガ顧客リストのカウント（グループIDから）
		///
		///	#Author yk
		/// #date	2013/11/13
		///--------------------------------------------------------------------
		function countCliantSendListForGroupID($group_id){
			global $mydb;
			$search_sql ="";
			if(!empty($group_id)){
				$search_sql ="AND MASTER_GROUP LIKE '%,".$group_id.",%' ";
			}
			$sql = "SELECT COUNT(*) ".
				   "FROM ".
				   "       CLIANT_MASTER ".
				   "WHERE ".
				   "       MAILMAGA_FLAG='send' ".
				   "AND ".
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
		/// メルマガ顧客情報の取得（顧客IDから）
		///
		///	#Author yk
		/// #date	2014/01/11
		///--------------------------------------------------------------------
		function getCliantSendDataForCliantID($cliant_id){
			global $mydb;
			$sql = "SELECT ".
				   "       ID, ".
				   "       MASTER_NAME, ".
				   "       MASTER_COMPANY, ".
				   "       MASTER_POST, ".
				   "       MASTER_MAIL ".
				   "FROM ".
				   "       CLIANT_MASTER ".
				   "WHERE ".
				   "       MAILMAGA_FLAG='send' ".
				   "AND ".
				   "       MASTER_STATUS!='trash' ".
				   "AND ".
				   "       ID='".$cliant_id."';";
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