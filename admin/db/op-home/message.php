<?php
	/* /////////////////////////////////////////////////////
	//		ホームパッケージ（メッセージ）
	//////////////////////////////////////////////////////*/
	
	////////////////////////////////////////////////////////
	//
	//	#substance	パッケージの作成
	//  #Date		2013/12/13
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	
//--------------------------------------------------------------------
	
	class Message {
		
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
		/// メッセージリストの取得
		///
		///	#Author yk
		/// #date	2013/12/15
		///--------------------------------------------------------------------
		function getMessagePostForLimit($limit){
			global $mydb;
			$limit_date = date('Y-m-d H:i:s', strtotime('-'.$limit.'day'));
			$sql = "SELECT * FROM ".
				   "       MESSAGE_POST ".
				   "WHERE ".
				   "       POST_DATE>='".$limit_date."' ".
				   "ORDER BY POST_DATE ASC;";
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
		/// メッセージ情報の新規保存
		///
		/// #param  $post：フォームの入力情報
		///
		///	#Author yk
		/// #date	2013/12/15
		///--------------------------------------------------------------------
		function insertMessagePost($post){
			global $mydb;
			$edit_date = date("Y-m-d H:i:s");
			$sql = "INSERT INTO ".
				   "       MESSAGE_POST ".
				   "VALUES('',".
				   "       '".$post['staff_id']."', ".
				   "       '".$edit_date."', ".
				   "       '".$post['message']."');";
			$res = mysql_query($sql, $mydb);
			if($res){
				return 'message-ok';
			}
			else{
				echo $sql;
				return 'message-ng';
			}
		}
	}
?>