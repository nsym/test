<?php
	/* /////////////////////////////////////////////////////
	//		DMラベルパッケージ
	//////////////////////////////////////////////////////*/
	
	////////////////////////////////////////////////////////
	//
	//	#substance	パッケージの作成
	//  #Date		2013/11/09
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
	
	class DMlabel {
		
		///--------------------------------------------------------------------
		/// 【コンストラクター】
		///
		/// 戻り値　なし
		///
		///	#Author mukai
		/// #date	2013/11/09
		///--------------------------------------------------------------------
		function __construct(){
		}
		
		///--------------------------------------------------------------------
		/// DMラベルリストの取得
		///
		///	#Author mukai
		/// #date	2013/11/09
		///--------------------------------------------------------------------
		function getDMlabelMasterList($orderby){
			global $mydb;
			if(empty($orderby)){
				$orderby = "MASTER_KANA ASC";
			}
			$sql = "SELECT * FROM ".
				   "       DM_LABEL ".
				   "ORDER BY ".$orderby.";";
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
		/// DMラベルリストの取得
		///
		///	#Author mukai
		/// #date	2013/11/09
		///--------------------------------------------------------------------
		function searchDMlabelMasterList($field, $value, $orderby, $start, $limit_num){
			global $mydb;
			$search_sql ="";
			if(!empty($field) && !empty($value) && $value!=='all'){
				$search_sql = "AND ".$field."='".$value."' ";
			}
			if(empty($orderby)){
				$orderby = "ID DESC";
			}
			$sql = "SELECT * FROM ".
				   "       DM_LABEL ".
				   "WHERE ".
				   "       LABEL_STATUS!='trash' ".
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
		/// DMラベルリストのカウント
		///
		///	#Author mukai
		/// #date	2013/11/09
		///--------------------------------------------------------------------
		function countDMlabelMasterList($field, $value){
			global $mydb;
			$search_sql ="";
			if(!empty($field) && !empty($value) && $value!=='all'){
				$search_sql = "AND ".$field."='".$value."' ";
			}
			$sql = "SELECT COUNT(*) FROM ".
				   "       DM_LABEL ".
				   "WHERE ".
				   "       LABEL_STATUS!='trash' ".
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
		/// DMラベル情報の取得（IDから）
		///
		/// #param  $id：DMラベルID
		///
		///	#Author mukai
		/// #date	2013/11/09
		///--------------------------------------------------------------------
		function getDMlabelMasterForID($id){
			global $mydb;
			$sql = "SELECT * FROM ".
				   "       DM_LABEL ".
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
		/// DMラベル情報の新規保存
		///
		/// #param  $post：フォームの入力情報
		///
		///	#Author mukai
		/// #date	2013/11/09
		///--------------------------------------------------------------------
		function insertDMlabelMaster($post){
			global $mydb;
			$edit_date = date("Y-m-d H:i:s");
			$sql = "INSERT INTO ".
				   "       DM_LABEL ".
				   "VALUES('',".
				   "       '".$post['group_id']."', ".
				   "       '".$post['list']."', ".
				   "       '".$post['title']."', ".
				   "       '".$post['body']."', ".
				   "       '".$post['mode']."', ".
				   "       '".$post['start']."', ".
				   "       '".$post['margin_top']."', ".
				   "       '".$post['margin_left']."', ".
				   "       '".$post['padding_top']."', ".
				   "       '".$post['padding_left']."', ".
				   "       '".$post['width']."', ".
				   "       '".$post['height']."', ".
				   "       '".$post['space']."', ".
				   "       '".$post['item']."', ".
				   "       'print', ".
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
		/// DMラベル情報の更新
		///
		/// #param  $post：フォームの入力情報
		///
		///	#Author mukai
		/// #date	2013/11/14
		///--------------------------------------------------------------------
		function updateDMlabelMasterForDate($id){
			global $mydb;
			$edit_date = date("Y-m-d H:i:s");
			$sql = "UPDATE ".
				   "       DM_LABEL ".
				   "SET ".
				   "       EDIT_DATE='".$edit_date."' ".
				   "WHERE ".
				   "       ID='".$id."';";
			$res = mysql_query($sql, $mydb);
			if($res){
				return 'update-ok';
			}
			else{
				echo $sql;
				return 'update-ng';
			}
		}
		///--------------------------------------------------------------------
		/// DMラベル情報の更新
		///
		/// #param  $post：フォームの入力情報
		///
		///	#Author mukai
		/// #date	2013/11/09
		///--------------------------------------------------------------------
		function updateDMlabelMasterForID($post){
			global $mydb;
			$edit_date = date("Y-m-d H:i:s");
			$sql = "UPDATE ".
				   "       DM_LABEL ".
				   "SET ".
				   "       GROUP_ID='".$post['group_id']."', ".
				   "       CLIANT_LIST='".$post['list']."', ".
				   "       LABEL_TITLE='".$post['title']."', ".
				   "       LABEL_BODY='".$post['body']."', ".
				   "       PIECE_MODE='".$post['mode']."', ".
				   "       PIECE_START='".$post['start']."', ".
				   "       MARGIN_TOP='".$post['margin_top']."', ".
				   "       MARGIN_LEFT='".$post['margin_left']."', ".
				   "       PADDING_TOP='".$post['padding_top']."', ".
				   "       PADDING_LEFT='".$post['padding_left']."', ".
				   "       PIECE_WIDTH='".$post['width']."', ".
				   "       PIECE_HEIGHT='".$post['height']."', ".
				   "       PIECE_SPACE='".$post['space']."', ".
				   "       ITEM_SETTING='".$post['item']."', ".
				   "       EDIT_DATE='".$edit_date."' ".
				   "WHERE ".
				   "       ID='".$post['id']."';";
			$res = mysql_query($sql, $mydb);
			if($res){
				return 'update-ok';
			}
			else{
				echo $sql;
				return 'update-ng';
			}
		}
		
		///--------------------------------------------------------------------
		/// DMラベル情報の削除
		///
		/// #param  $id：DMラベルID
		///
		///	#Author yk
		/// #date	2013/11/12
		///--------------------------------------------------------------------
		function deleteDMlabelMasterForID($id){
			global $mydb;
			$edit_date = date("Y-m-d H:i:s");
			$sql = "UPDATE ".
				   "       DM_LABEL ".
				   "SET ".
				   "       LABEL_STATUS='trash', ".
				   "       EDIT_DATE='".$edit_date."' ".
				   "WHERE ".
				   "       ID='".$id."';";
			$res = mysql_query($sql, $mydb);
			if($res){
				return 'trash-ok';
			}
			else{
				echo $sql;
				return 'trash-ng';
			}
		}
	}
?>