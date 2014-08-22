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
		/// #date	2013/12/11
		///--------------------------------------------------------------------
		function __construct(){
		}
		
		///--------------------------------------------------------------------
		/// 見積顧客リストの検索（キーワードから）
		///
		///	#Author yk
		/// #date	2013/12/11
		///--------------------------------------------------------------------
		function searchKeywordCliantMaster($field, $keyword){
			global $mydb;
			$search_sql ="";
			if(!empty($field) && !empty($keyword)){
				$search_sql ="AND ".$field." LIKE '%".$keyword."%' ";
			}
			$sql = "SELECT ".
				   "       ID, ".
				   "       MASTER_NAME, ".
				   "       MASTER_COMPANY ".
				   "FROM ".
				   "       CLIANT_MASTER ".
				   "WHERE ".
				   "       MASTER_STATUS!='trash' ".
				   $search_sql.
				   "LIMIT 50;";
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
		/// #date	2013/12/11
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
		/// 顧客IDのチェック（IDから）
		///
		///	#Author yk
		/// #date	2013/12/11
		///--------------------------------------------------------------------
		function checkCliantID($id){
			global $mydb;
			$sql = "SELECT * FROM ".
				   "       CLIANT_MASTER ".
				   "WHERE ".
				   "       MASTER_STATUS!='trash' ".
				   "AND ".
				   "	   ID='".$id."';";
			$res = mysql_query($sql, $mydb);
			$row = mysql_array($res);
			if($res){
				if(!empty($row[0])){
					return true;
				}else{
					return false;
				}
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