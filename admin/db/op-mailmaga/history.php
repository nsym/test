<?php
	/* /////////////////////////////////////////////////////
	//		メルマガ管理パッケージ（対応履歴）
	//////////////////////////////////////////////////////*/
	
	////////////////////////////////////////////////////////
	//
	//	#substance	パッケージの作成
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
	
//--------------------------------------------------------------------
	
	class History {
		
		///--------------------------------------------------------------------
		/// 【コンストラクター】
		///
		/// 戻り値　なし
		///
		///	#Author yk
		/// #date	2014/01/10
		///--------------------------------------------------------------------
		function __construct(){
		}
		
		///--------------------------------------------------------------------
		/// 対応履歴情報の新規保存
		///
		/// #param  $post：フォームの入力情報
		///
		///	#Author yk
		/// #date	2014/01/10
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
				return true;
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
	}
?>