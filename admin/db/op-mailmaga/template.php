<?php
	/* /////////////////////////////////////////////////////
	//		メルマガ管理パッケージ（テンプレート）
	//////////////////////////////////////////////////////*/
	
	////////////////////////////////////////////////////////
	//
	//	#substance	パッケージの作成
	//  #Date		2013/11/10
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
	
	class Template {
		
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
		/// テンプレートリストの取得
		///
		///	#Author yk
		/// #date	2013/11/10
		///--------------------------------------------------------------------
		function getMailmagaTemplateList(){
			global $mydb;
			$sql = "SELECT * FROM ".
				   "       MAILMAGA_TEMPLATE ".
				   "ORDER BY ID DESC;";
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
		/// テンプレート情報の取得（IDから）
		///
		///	#Author yk
		/// #date	2013/11/10
		///--------------------------------------------------------------------
		function getMailmagaTemplateForID($id){
			global $mydb;
			$sql = "SELECT * FROM ".
				   "       MAILMAGA_TEMPLATE ".
				   "WHERE ".
				   "       ID='".$id."';";
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
		/// テンプレート情報の新規保存
		///
		/// #param  $post：フォームの入力情報
		///
		///	#Author yk
		/// #date	2013/11/10
		///--------------------------------------------------------------------
		function insertMailmagaTemplate($post){
			global $mydb;
			$edit_date = date("Y-m-d H:i:s");
			$sql = "INSERT INTO ".
				   "       MAILMAGA_TEMPLATE ".
				   "VALUES('',".
				   "       '".$post['name']."', ".
				   "       '".$post['pc_html_body']."', ".
				   "       '".$post['pc_text_body']."', ".
				   "       '".$post['mb_html_body']."', ".
				   "       '".$post['mb_text_body']."', ".
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
		/// テンプレート情報の更新
		///
		/// #param  $post：フォームの入力情報
		///
		///	#Author yk
		/// #date	2013/11/10
		///--------------------------------------------------------------------
		function updateMailmagaTemplate($post){
			global $mydb;
			$edit_date = date("Y-m-d H:i:s");
			$sql = "UPDATE ".
				   "       MAILMAGA_TEMPLATE ".
				   "SET ".
				   "       TEMPLATE_NAME='".$post['name']."', ".
				   "       PC_HTML_BODY='".$post['pc_text_body']."', ".
				   "       PC_TEXT_BODY='".$post['pc_text_body']."', ".
				   "       MB_HTML_BODY='".$post['mb_text_body']."', ".
				   "       MB_TEXT_BODY='".$post['mb_text_body']."', ".
				   "       EDIT_DATE='".$edit_date."' ".
				   "WHERE ".
				   "       ID='".$post['id']."';";
			$res = mysql_query($sql, $mydb);
			if($res){
				echo $sql;
				return 'update-ok';
			}
			else{
				echo $sql;
				return 'update-ng';
			}
		}
		
		///--------------------------------------------------------------------
		/// テンプレート情報の削除
		///
		/// #param  $meta_id：メタID
		///
		///	#Author yk
		/// #date	2013/11/10
		///--------------------------------------------------------------------
		function deleteMailmagaTemplateForID($id){
			global $mydb;
			$sql = "DELETE FROM ".
				   "       MAILMAGA_TEMPLATE ".
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