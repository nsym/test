<?php
	/* /////////////////////////////////////////////////////
	//		メルマガ管理パッケージ
	//////////////////////////////////////////////////////*/
	
	////////////////////////////////////////////////////////
	//
	//	#substance	パッケージの作成
	//  #Date		2013/11/10
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	//
	//	#substance	フィールドの追加よる変更
	//  #Date		2014/01/11
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	
//--------------------------------------------------------------------
	
	class Mailmaga {
		
		///--------------------------------------------------------------------
		/// 【コンストラクター】
		///
		/// 戻り値　なし
		///
		///	#Author yk
		/// #date	2013/11/10
		///--------------------------------------------------------------------
		function __construct(){
		}
		
		///--------------------------------------------------------------------
		/// メルマガリストの取得
		///
		///	#Author yk
		/// #date	2013/11/10
		///--------------------------------------------------------------------
		function searchMailmagaPostList($field, $value, $orderby, $start, $limit_num){
			global $mydb;
			$search_sql ="";
			if(!empty($field) && !empty($value) && $value!=='all'){
				if($field==='POST_KANA'){
					global $kana_list;
					$search_sql = "AND (POST_KANA LIKE '".$kana_list[$value][0]."%' ";
					for($i=1; $i<count($kana_list[$value]); $i++){
						$search_sql.= "OR POST_KANA LIKE '".$kana_list[$value][$i]."%' ";
					}
					$search_sql.= ") ";
				}else{
					$search_sql = "AND ".$field."='".$value."' ";
				}
			}
			if(empty($orderby)){
				$orderby = "ID DESC";
			}
			$sql = "SELECT * FROM ".
				   "       MAILMAGA_POST ".
				   "WHERE ".
				   "       POST_STATUS!='trash' ".
				   $search_sql.
				   "ORDER BY ".$orderby." ".
				   "LIMIT ".$start.",".$limit_num.";";
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
		/// メルマガリストのカウント
		///
		///	#Author yk
		/// #date	2013/11/10
		///--------------------------------------------------------------------
		function countMailmagaPostList($field, $value){
			global $mydb;
			$search_sql ="";
			if(!empty($field) && !empty($value) && $value!=='all'){
				if($field==='POST_KANA'){
					global $kana_list;
					$search_sql = "AND (POST_KANA LIKE '".$kana_list[$value][0]."%' ";
					for($i=1; $i<count($kana_list[$value]); $i++){
						$search_sql.= "OR POST_KANA LIKE '".$kana_list[$value][$i]."%' ";
					}
					$search_sql.= ") ";
				}else{
					$search_sql = "AND ".$field."='".$value."' ";
				}
			}
			$sql = "SELECT COUNT(*) FROM ".
				   "       MAILMAGA_POST ".
				   "WHERE ".
				   "       POST_STATUS!='trash' ".
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
		/// メルマガリストの取得（キュー生成用）
		///
		///	#Author yk
		/// #date	2013/11/13
		///--------------------------------------------------------------------
		function getMailmagaPostListForStatusInsTime($status, $ins_time){
			global $mydb;
			$sql = "SELECT * FROM ".
				   "       MAILMAGA_POST ".
				   "WHERE ".
				   "       POST_STATUS='".$status."' ".
				   "AND ".
				   "       INS_DATE<='".$ins_time."' ;";
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
		/// メルマガリストの取得（キュー生成用）
		///
		///	#Author yk
		/// #date	2013/11/13
		///--------------------------------------------------------------------
		function getMailmagaPostListForStatusPostTime($status, $post_time){
			global $mydb;
			$sql = "SELECT * FROM ".
				   "       MAILMAGA_POST ".
				   "WHERE ".
				   "       POST_STATUS='".$status."' ".
				   "AND ".
				   "       POST_DATE<='".$post_time."' ;";
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
		/// メルマガ情報の取得（IDから）
		///
		/// #param  $id：メルマガID
		///
		///	#Author yk
		/// #date	2013/11/10
		///--------------------------------------------------------------------
		function getMailmagaPostForID($id){
			global $mydb;
			$sql = "SELECT * FROM ".
				   "       MAILMAGA_POST ".
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
		/// メルマガ情報の新規保存
		///
		/// #param  $post：フォームの入力情報
		///
		///	#Author yk
		/// #date	2013/11/10
		///	#Author yk
		/// #date	2013/11/15
		///	#Author yk
		/// #date	2014/01/11
		///--------------------------------------------------------------------
		function insertMailmagaPost($post){
			global $mydb;
			//特殊文字エスケープ処理--
			$post['pc_html_body'] = htmlspecialchars($post['pc_html_body'], ENT_QUOTES, "ISO-8859-1");
			$post['pc_html_body'] = str_replace("\\","",$post['pc_html_body']);
			$post['pc_text_body'] = str_replace("'","’",$post['pc_text_body']);
			$post['mb_html_body'] = htmlspecialchars($post['mb_html_body'], ENT_QUOTES, "ISO-8859-1");
			$post['mb_html_body'] = str_replace("\\","",$post['mb_html_body']);
			$post['mb_text_body'] = str_replace("'","’",$post['mb_text_body']);
			//--ここまで
			$edit_date = date("Y-m-d H:i:s");
			$sql = "INSERT INTO ".
				   "       MAILMAGA_POST ".
				   "VALUES('',".
				   "       '".$post['staff_id']."', ".
				   "       '".$post['post_date']."', ".
				   "       '".$post['post_mode']."', ".
				   "       '".$post['post_specify']."', ".
				   "       '".$post['post_group']."', ".
				   "       '".$post['post_cliant']."', ".
				   "       '".$post['post_title']."', ".
				   "       '".$post['pc_html_body']."', ".
				   "       '".$post['pc_text_body']."', ".
				   "       '".$post['mb_html_body']."', ".
				   "       '".$post['mb_text_body']."', ".
				   "       '".$post['post_sum']."', ".
				   "       'reserving', ".	//配信ステータス
				   "       '".$edit_date."', ".
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
		/// メルマガ情報の配信人数更新
		///
		///	#Author yk
		/// #date	2013/11/16
		///--------------------------------------------------------------------
		function updateMailmagaPostSumForID($id, $count_num){
			global $mydb;
			$edit_date = date("Y-m-d H:i:s");
			$sql = "UPDATE ".
				   "       MAILMAGA_POST ".
				   "SET ".
				   "       POST_SUM='".$count_num."' ".
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
		/// メルマガのグループ一括登録
		///
		///	#Author yk
		/// #date	2013/11/06
		///--------------------------------------------------------------------
		function addGroupMailmagaPost($group_id, $id_array){
			global $mydb;
			$res = true;
			$edit_date = date("Y-m-d H:i:s");
			for($i=0; $i<count($id_array) && $res; $i++){
				$mailmaga_data = $this->getMailmagaPostForID($id_array[$i]);
				if(!empty($mailmaga_data) && !preg_match('/,'.$group_id.',/', $mailmaga_data["POST_GROUP"])){
					$setGroupText = !empty($mailmaga_data["POST_GROUP"])? $mailmaga_data["POST_GROUP"].$group_id.',': ','.$mailmaga_data["POST_GROUP"].$group_id.',';
					$sql = "UPDATE ".
						   "       MAILMAGA_POST ".
						   "SET ".
						   "       POST_GROUP='".$setGroupText."', ".
						   "       EDIT_DATE='".$edit_date."' ".
						   "WHERE ".
						   "       ID='".$id_array[$i]."';";
					$res = mysql_query($sql, $mydb);
					echo $sql;
				}
			}
			if($res){
				return 'group-ok';
			}
			else{
				echo $sql;
				return 'group-ng';
			}
		}
		
		///--------------------------------------------------------------------
		/// メルマガ情報のステータス変更
		///
		/// #param  $id：メルマガID
		///
		///	#Author yk
		/// #date	2013/11/12
		///--------------------------------------------------------------------
		function changeMailmagaPostForID($id, $status){
			global $mydb;
			$edit_date = date("Y-m-d H:i:s");
			$sql = "UPDATE ".
				   "       MAILMAGA_POST ".
				   "SET ".
				   "       POST_STATUS='".$status."', ".
				   "       EDIT_DATE='".$edit_date."' ".
				   "WHERE ".
				   "       ID='".$id."';";
			$res = mysql_query($sql, $mydb);
			if($res){
				return $status.'-ok';
			}
			else{
				echo $sql;
				return $status.'-ng';
			}
		}
		
		///--------------------------------------------------------------------
		/// メルマガ情報の削除
		///
		/// #param  $id：メルマガID
		///
		///	#Author yk
		/// #date	2013/11/10
		///--------------------------------------------------------------------
		function deleteMailmagaPostForID($id){
			global $mydb;
			$sql = "DELETE FROM ".
				   "       MAILMAGA_POST ".
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