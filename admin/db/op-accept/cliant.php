<?php
	/* /////////////////////////////////////////////////////
	//		見積受注管理パッケージ（顧客）
	//////////////////////////////////////////////////////*/
	
	////////////////////////////////////////////////////////
	//
	//	#substance	パッケージの作成
	//  #Date		2013/12/13
	//	#Author 	yk
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
		///	#Author yk
		/// #date	2013/12/11
		///--------------------------------------------------------------------
		function __construct(){
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
	}
?>