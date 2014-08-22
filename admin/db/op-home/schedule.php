<?php
	/* /////////////////////////////////////////////////////
	//		ホームパッケージ（スケジュール）
	//////////////////////////////////////////////////////*/
	
	////////////////////////////////////////////////////////
	//
	//	#substance	パッケージの作成
	//  #Date		2013/12/13
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	//
	//	#substance	スケジュールの仕様変更
	//  #Date		2014/01/08
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	
//--------------------------------------------------------------------
	
	class Schedule {
		
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
		/// スケジュールリストの取得
		///
		///	#Author yk
		/// #date	2013/12/18
		///	#Author yk
		/// #date	2014/01/08
		///--------------------------------------------------------------------
		function getScheduleMasterForDay($month, $day, $year, $staff_id){
			global $mydb;
			$start_date = date('Y-m-d H:i:s', mktime(0,0,0,$month, $day, $year));
			$end_date = date('Y-m-d H:i:s', mktime(23,59,59,$month, $day, $year));
			$search_sql ='';
			if(!empty($staff_id)){
				$search_sql.=
				   "AND (".
				   "       STAFF_ID='".$staff_id."' ".
				   "OR ".
				   "       MASTER_MEMBER='staff') ";
			}
			$sql = "SELECT * FROM ".
				   "       SCHEDULE_MASTER ".
				   "WHERE ".
				   "       MASTER_DATE>='".$start_date."' ".
				   "AND ".
				   "       MASTER_DATE<='".$end_date."' ".
				   $search_sql.
				   "ORDER BY MASTER_DATE ASC;";
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
		/// スケジュールの取得
		///
		///	#Author yk
		/// #date	2013/12/04
		///--------------------------------------------------------------------
		function getScheduleMasterForID($id){
			global $mydb;
			$sql = "SELECT * FROM ".
				   "       SCHEDULE_MASTER ".
				   "WHERE ".
				   "       ID='".$id."' ;";
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
		/// スケジュール情報の新規保存
		///
		/// #param  $post：フォームの入力情報
		///
		///	#Author yk
		/// #date	2013/12/18
		///	#Author yk
		/// #date	2014/01/08
		///--------------------------------------------------------------------
		function insertScheduleMaster($post){
			global $mydb;
			$edit_date = date("Y-m-d H:i:s");
			$sql = "INSERT INTO ".
				   "       SCHEDULE_MASTER ".
				   "VALUES('',".
				   "       '".$post['staff_id']."', ".
				   "       '".$post['date']."', ".
				   "       '".$post['category']."', ".
				   "       '".$post['title']."', ".
				   "       '".$post['member']."', ".
				   "       '".$edit_date."', ".
				   "       '".$edit_date."');";
			$res = mysql_query($sql, $mydb);
			if($res){
				return 'schedule-ok';
			}
			else{
				echo $sql;
				return 'schedule-ng';
			}
		}
		
		///--------------------------------------------------------------------
		/// スケジュール情報の更新
		///
		/// #param  $post：フォームの入力情報
		///
		///	#Author yk
		/// #date	2013/12/18
		///--------------------------------------------------------------------
		function updateScheduleMasterForID($post){
			global $mydb;
			$edit_date = date("Y-m-d H:i:s");
			$sql = "UPDATE ".
				   "       SCHEDULE_MASTER ".
				   "SET ".
				   "       MASTER_DATE='".$post['date']."', ".
				   "       MASTER_CATEGORY='".$post['category']."', ".
				   "       MASTER_TITLE='".$post['title']."', ".
				   "       MASTER_MEMBER='".$post['member']."', ".
				   "       EDIT_DATE='".$edit_date."' ".
				   "WHERE ".
				   "       ID='".$post['id']."';";
			$res = mysql_query($sql, $mydb);
			if($res){
				return 'schedule-ok';
			}
			else{
				echo $sql;
				return 'schedule-ng';
			}
		}
		
		///--------------------------------------------------------------------
		/// スケジュール情報の削除
		///
		///	#Author yk
		/// #date	2013/12/18
		///--------------------------------------------------------------------
		function deleteScheduleMasterForID($id){
			global $mydb;
			$sql = "DELETE FROM ".
				   "       SCHEDULE_MASTER ".
				   "WHERE ".
				   "       ID='".$id."';";
			$res = mysql_query($sql, $mydb);
			if($res){
				return 'delete-ok';
			}
			else{
				echo $sql;
				return 'delete-ng';
			}
		}
	}
?>