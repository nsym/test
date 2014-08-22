<?php
	/* /////////////////////////////////////////////////////
	//		グループ管理パッケージ
	//////////////////////////////////////////////////////*/
	
	////////////////////////////////////////////////////////
	//
	//	#substance	パッケージの作成
	//  #Date		2013/11/01
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
	
	class Group {
		
		///--------------------------------------------------------------------
		/// 【コンストラクター】
		///
		/// 戻り値　なし
		///
		///	#Author yk
		/// #date	2013/10/22
		///--------------------------------------------------------------------
		function __construct(){
		}
		
		///--------------------------------------------------------------------
		/// グループリストの取得
		///
		///	#Author yk
		/// #date	2013/11/04
		///--------------------------------------------------------------------
		function getCliantGroupList(){
			global $mydb;
			$sql = "SELECT * FROM ".
				   "       CLIANT_GROUP ".
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
		/// グループ情報の取得（IDから）
		///
		///	#Author yk
		/// #date	2013/11/04
		///--------------------------------------------------------------------
		function getCliantGroupForID($id){
			global $mydb;
			$sql = "SELECT * FROM ".
				   "       CLIANT_GROUP ".
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
		/// グループ情報の新規保存
		///
		/// #param  $post：フォームの入力情報
		///
		///	#Author yk
		/// #date	2013/10/22
		///	#Author yk
		/// #date	2013/11/06
		///--------------------------------------------------------------------
		function insertCliantGroup($post){
			global $mydb;
			$edit_date = date("Y-m-d H:i:s");
			$sql = "INSERT INTO ".
				   "       CLIANT_GROUP ".
				   "VALUES('',".
				   "       '".$post['name']."', ".
				   "       '".$post['color']."', ".
				   "       '".$post['note']."', ".
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
		/// グループ情報の更新
		///
		/// #param  $post：フォームの入力情報
		///
		///	#Author yk
		/// #date	2013/10/22
		///	#Author yk
		/// #date	2013/11/06
		///--------------------------------------------------------------------
		function updateCliantGroupForID($post){
			global $mydb;
			$edit_date = date("Y-m-d H:i:s");
			$sql = "UPDATE ".
				   "       CLIANT_GROUP ".
				   "SET ".
				   "       GROUP_NAME='".$post['name']."', ".
				   "       GROUP_COLOR='".$post['color']."', ".
				   "       GROUP_NOTES='".$post['note']."', ".
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
		/// グループ情報の削除
		///
		/// #param  $id：グループID
		///
		///	#Author yk
		/// #date	2013/05/24
		///--------------------------------------------------------------------
		function deleteCliantGroupForID($id){
			global $mydb;
			$sql = "DELETE FROM ".
				   "       CLIANT_GROUP ".
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