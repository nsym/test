<?php
	/* /////////////////////////////////////////////////////
	//		メルマガ管理パッケージ（キュー操作用）
	//////////////////////////////////////////////////////*/
	
	////////////////////////////////////////////////////////
	//
	//	#substance	パッケージの作成
	//  #Date		2013/11/13
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	//
	//	#substance	フィールド追加による変更
	//  #Date		2014/01/10
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	//
	//	#substance	関数追加
	//  #Date		2014/08/18
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	
//--------------------------------------------------------------------
	
	class Queue {
		
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
		/// メルマガキューの取得（キュー生成の重複チェック用）
		///
		/// #param  $post_id：メルマガID
		///
		///	#Author yk
		/// #date	2013/11/13
		///--------------------------------------------------------------------
		function checkMailmagaQueueForPostID($post_id){
			global $mydb;
			$sql = "SELECT ID FROM ".
				   "       MAILMAGA_QUEUE ".
				   "WHERE ".
				   "	   POST_ID='".$post_id."' ".
				   "LIMIT 0,1;";
			$res = mysql_query($sql, $mydb);
			$row = mysql_array($res);
			if($res){
				if(empty($row)){
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
		/// メルマガキューの取得（配信用キューの取得）
		///
		///	#Author yk
		/// #date	2013/11/14
		///--------------------------------------------------------------------
		function getMailmagaQueueLimit($post_id, $time, $limit_num){
			global $mydb;
			$sql = "SELECT * FROM ".
				   "       MAILMAGA_QUEUE ".
				   "WHERE ".
				   "	   POST_ID='".$post_id."' ".
				   "AND ".
				   "       POST_DATE<='".$time."' ".
				   "ORDER BY POST_DATE ASC ".
				   "LIMIT 0,".$limit_num.";";
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
		/// メルマガキューのカウント（配信用キューの件数取得）
		///
		///	#Author yk
		/// #date	2013/11/14
		///--------------------------------------------------------------------
		function countMailmagaQueueLimit($post_id, $time){
			global $mydb;
			$sql = "SELECT COUNT(*) FROM ".
				   "       MAILMAGA_QUEUE ".
				   "WHERE ".
				   "	   POST_ID='".$post_id."' ".
				   "AND ".
				   "       POST_DATE<='".$time."' ;";
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
		/// メルマガキューのカウント（配信用キューの全件数取得）
		///
		///	#Author yk
		/// #date	2013/11/14
		///--------------------------------------------------------------------
		function countMailmagaQueueForMailmagaListTime($post_list, $time){
			global $mydb;
			$search_sql = "";
			if(is_array($post_list)){
				$search_sql.= "AND ( POST_ID='".$post_list[0]["ID"]."' ";
				for($i=1; $i<count($post_list); $i++){
					$search_sql.= "OR POST_ID='".$post_list[$i]["ID"]."' ";
				}
				$search_sql.= ") ";
			}
			$sql = "SELECT COUNT(*) FROM ".
				   "       MAILMAGA_QUEUE ".
				   "WHERE ".
				   "	   POST_DATE<='".$time."' ".
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
		/// メルマガキューの新規保存
		///
		///	#Author yk
		/// #date	2013/11/13
		///	#Author yk
		/// #date	2014/01/10
		///--------------------------------------------------------------------
		function insertMailmagaQueue($cliant_data, $mailmaga_post, $post_body){
			global $mydb;
			$edit_date = date("Y-m-d H:i:s");
			$sql = "INSERT INTO ".
				   "       MAILMAGA_QUEUE ".
				   "VALUES('',".
				   "       '".$mailmaga_post['ID']."', ".
				   "       '".$mailmaga_post['STAFF_ID']."', ".
				   "       '".$cliant_data['ID']."', ".
				   "       '".$cliant_data['MASTER_NAME']."', ".
				   "       '".$cliant_data['MASTER_COMPANY']."', ".
				   "       '".$cliant_data['MASTER_POST']."', ".
				   "       '".$cliant_data['MASTER_MAIL']."', ".
				   "       '".$mailmaga_post['POST_MODE']."', ".
				   "       '".$mailmaga_post['POST_DATE']."', ".
				   "       '".$mailmaga_post['POST_TITLE']."', ".
				   "       '".$post_body['HTML']."', ".
				   "       '".$post_body['TEXT']."', ".
				   "       '".$edit_date."', ".
				   "       '".$edit_date."');";
			$res = mysql_query($sql, $mydb);
			if($res){
				return false;
			}
			else{
				return $sql;
			}
		}
		
		///--------------------------------------------------------------------
		/// メルマガキューのログ保存
		///
		///	#Author yk
		/// #date	2013/11/14
		///	#Author yk
		/// #date	2014/01/10
		///--------------------------------------------------------------------
		function logingMailmagaQueue($queue_data){
			global $mydb;
			$edit_date = date("Y-m-d H:i:s");
			$sql = "INSERT INTO ".
				   "       MAILMAGA_QUEUE_LOG ".
				   "VALUES('',".
				   "       '".$queue_data['POST_ID']."', ".
				   "       '".$queue_data['STAFF_ID']."', ".
				   "       '".$queue_data['CLIANT_ID']."', ".
				   "       '".$queue_data['CLIANT_NAME']."', ".
				   "       '".$queue_data['CLIANT_COMPANY']."', ".
				   "       '".$queue_data['CLIANT_POST']."', ".
				   "       '".$queue_data['CLIANT_MAIL']."', ".
				   "       '".$queue_data['POST_MODE']."', ".
				   "       '".$queue_data['POST_DATE']."', ".
				   "       '".$queue_data['POST_TITLE']."', ".
				   "       '".$queue_data['POST_HTML_BODY']."', ".
				   "       '".$queue_data['POST_TEXT_BODY']."', ".
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
		/// メルマガ情報の削除
		///
		/// #param  $id：メルマガID
		///
		///	#Author yk
		/// #date	2013/11/13
		///--------------------------------------------------------------------
		function deleteMailmagaQueueForID($id){
			global $mydb;
			$sql = "DELETE FROM ".
				   "       MAILMAGA_QUEUE ".
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
		
		///--------------------------------------------------------------------
		/// メルマガ情報の削除（メルマガ予約削除の場合）
		///
		/// #param  $post_id：メルマガ予約ID
		///
		///	#Author yk
		/// #date	2014/08/18
		///--------------------------------------------------------------------
		function deleteMailmagaQueueForPostID($post_id){
			global $mydb;
			$sql = "DELETE FROM ".
				   "       MAILMAGA_QUEUE ".
				   "WHERE ".
				   "       POST_ID='".$post_id."';";
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