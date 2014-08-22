<?php
	/* /////////////////////////////////////////////////////
	//		顧客最新対応履歴の挿入　
	//////////////////////////////////////////////////////*/
	require_once("/home/kir016651/public_html/nsym/test.nsym-chemix.com/admin/common/config.php");
	$mydb = db_con();
	
	//顧客一覧の取得
	$cliant_list = getCliantMasterList();
	//print_r($cliant_list);
	
	//顧客の数だけループ
	$res = true;
	foreach($cliant_list as $value){
		//最新の対応履歴の取得
		$recent_history = getCliantHistoryRecent($value["ID"]);
		//対応履歴があれば
		if(!empty($recent_history)){
			//スタッフ情報の取得
			$staff_data = getStaffForID($recent_history["STAFF_ID"]);
			$staff_name ='スタッフ未設定';
			if(!empty($staff_data["DISPLAY_NAME"])){
				$staff_name = $staff_data["DISPLAY_NAME"];
			}
			//print_r($staff_data);
			//対応履歴のフィールド挿入
			$res = updateCliantRecentHistoryForID($value["ID"], $recent_history["HISTORY_DATE"], $staff_name);
		}
		//失敗したら停止
		if(!$res) break;
	}
	
	
		
	///--------------------------------------------------------------------
	/// 顧客リストの取得
	///
	///	#Author yk
	/// #date	2014/06/09
	///--------------------------------------------------------------------
	function getCliantMasterList(){
		global $mydb;
		$sql = "SELECT * FROM ".
			   "       CLIANT_MASTER ".
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
		
	///--------------------------------------------------------------------
	/// ユーザー情報の取得（スタッフIDから）
	///
	/// #param  $id：ユーザーID
	///
	///	#Author yk
	/// #date	2014/06/09
	///--------------------------------------------------------------------
	function getStaffForID($id){
		global $mydb;
		$sql = "SELECT ID,DISPLAY_NAME FROM ".
			   "       ADMIN_MASTER ".
			   "WHERE ".
			   "	   ID='".$id."' ".
			   "AND ".
			   "       ADMIN_AUTHORITY='staff';";
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
	
?>