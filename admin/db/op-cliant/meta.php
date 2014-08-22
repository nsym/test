<?php
	/* /////////////////////////////////////////////////////
	//		顧客管理パッケージ（顧客メタ）
	//////////////////////////////////////////////////////*/
	
	////////////////////////////////////////////////////////
	//
	//	#substance	パッケージの作成
	//  #Date		2013/11/07
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
	
	class Meta {
		
		///--------------------------------------------------------------------
		/// 【コンストラクター】
		///
		/// 戻り値　なし
		///
		///	#Author yk
		/// #date	2013/11/07
		///--------------------------------------------------------------------
		function __construct(){
		}
		
		///--------------------------------------------------------------------
		/// 顧客リストの取得
		///
		///	#Author yk
		/// #date	2013/11/07
		///--------------------------------------------------------------------
		function getCliantMetaListForCliantID($id){
			global $mydb;
			$sql = "SELECT * FROM ".
				   "       CLIANT_META ".
				   "WHERE ".
				   "       MASTER_ID='".$id."' ".
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
		/// 顧客情報の取得（IDから）
		///
		///	#Author yk
		/// #date	2013/11/07
		///--------------------------------------------------------------------
		function getCliantMetaForMetaID($meta_id){
			global $mydb;
			$sql = "SELECT * FROM ".
				   "       CLIANT_META ".
				   "WHERE ".
				   "       ID='".$meta_id."';";
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
		/// 顧客情報の新規保存
		///
		/// #param  $post：フォームの入力情報
		///
		///	#Author yk
		/// #date	2013/11/07
		///--------------------------------------------------------------------
		function insertCliantMeta($post){
			global $mydb;
			$edit_date = date("Y-m-d H:i:s");
			$sql = "INSERT INTO ".
				   "       CLIANT_META ".
				   "VALUES('',".
				   "       '".$post['id']."', ".
				   "       '".$post['caption']."', ".
				   "       '".$post['key']."', ".
				   "       '".$post['value']."', ".
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
		/// 顧客情報の更新
		///
		/// #param  $post：フォームの入力情報
		///
		///	#Author yk
		/// #date	2013/11/07
		///--------------------------------------------------------------------
		function updateCliantMetaForMetaID($post){
			global $mydb;
			$edit_date = date("Y-m-d H:i:s");
			$sql = "UPDATE ".
				   "       CLIANT_META ".
				   "SET ".
				   "       MASTER_ID='".$post['id']."', ".
				   "       META_CAPTION='".$post['caption']."', ".
				   "       META_KEY='".$post['key']."', ".
				   "       META_VALUE='".$post['value']."', ".
				   "       EDIT_DATE='".$edit_date."' ".
				   "WHERE ".
				   "       ID='".$post['meta_id']."';";
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
		/// 顧客情報の削除
		///
		/// #param  $meta_id：メタID
		///
		///	#Author yk
		/// #date	2013/11/07
		///--------------------------------------------------------------------
		function deleteCliantMetaForMetaID($meta_id){
			global $mydb;
			$sql = "DELETE FROM ".
				   "       CLIANT_META ".
				   "WHERE ".
				   "       ID='".$meta_id."';";
			$res = mysql_query($sql, $mydb);
			if($res){
				return 'delete-ok';
			}
			else{
				echo $sql;
				return 'delete-ng';
			}
		}
		
		///--------------------------------------------------------------------
		/// 顧客情報の削除
		///
		/// #param  $id：顧客ID
		///
		///	#Author yk
		/// #date	2013/11/07
		///--------------------------------------------------------------------
		function deleteCliantMetaForCliantID($id){
			global $mydb;
			$sql = "DELETE FROM ".
				   "       CLIANT_META ".
				   "WHERE ".
				   "       MASTER_ID='".$id."';";
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