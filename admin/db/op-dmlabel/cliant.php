<?php
	/* /////////////////////////////////////////////////////
	//		メルマガ管理パッケージ
	//////////////////////////////////////////////////////*/
	
	////////////////////////////////////////////////////////
	//
	//	#substance	パッケージの作成
	//  #Date		2013/11/13
	//	#Author 	mukai
	//
	////////////////////////////////////////////////////////
	//
	//	#substance	ー
	//  #Date		----/--/--
	//	#Author 	--
	//
	////////////////////////////////////////////////////////
	
//--------------------------------------------------------------------
	
	class Cliant {
		
		///--------------------------------------------------------------------
		/// 【コンストラクター】
		///
		/// 戻り値　なし
		///
		///	#Author mukai
		/// #date	2013/11/13
		///--------------------------------------------------------------------
		function __construct(){
		}
		
		///--------------------------------------------------------------------
		/// DMラベル顧客リストの取得（グループIDから）
		///
		///	#Author mukai
		/// #date	2013/11/13
		///--------------------------------------------------------------------
		function getCliantDMlistForGroupID($group_id){
			global $mydb;
			$search_sql = '';
			if($group_id != 0){
				 $search_sql = "AND MASTER_GROUP LIKE '%,".$group_id.",%' ";
			}
			$sql = "SELECT ".
				   "	   STAFF_ID, ".
				   "       MASTER_NAME, ".
				   "       MASTER_COMPANY, ".
				   "       MASTER_POST, ".
				   "       MASTER_AREA, ".
				   "       MASTER_ZIPCODE, ".
				   "       MASTER_ADDRESS ".
				   "FROM ".
				   "       CLIANT_MASTER ".
				   "WHERE ".
				   "       MASTER_ADDRESS!='' ".
				   "AND ".
				   "       MASTER_STATUS!='trash' ".
				   $search_sql.
				   "ORDER BY ".
				   "       STAFF_ID ASC, ".
				   "       CAST(MASTER_KANA AS CHAR) ASC;";
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
		/// DMラベル顧客リストのカウント（グループIDから）
		///
		///	#Author mukai
		/// #date	2013/11/13
		///--------------------------------------------------------------------
		function countCliantDMListForGroupID($group_id){
			global $mydb;
			$group_sql = "";
			if($group_id !=0){
				$group_sql ="WHERE MASTER_GROUP LIKE '%,".$group_id.",%' ";
			}
			$sql = "SELECT COUNT(*) ".
				       "FROM ".
			     	   "       CLIANT_MASTER ".
					   "WHERE ".
					   "       MASTER_ADDRESS!='' ".
					   "AND ".
					   "       MASTER_STATUS!='trash' ".
				    	$group_sql.
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
	}
?>