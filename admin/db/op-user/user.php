<?php
	/* /////////////////////////////////////////////////////
	//		ユーザー管理パッケージ
	//////////////////////////////////////////////////////*/
	
	////////////////////////////////////////////////////////
	//
	//	#substance	パッケージの作成
	//  #Date		2013/10/22
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	//
	//	#substance	削除機能の変更
	//  #Date		2013/11/12
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	
//--------------------------------------------------------------------
	
	class User {
		
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
		/// ユーザーリストの取得
		///
		///	#Author yk
		/// #date	2013/10/22
		///--------------------------------------------------------------------
		function getUserMasterList($orderby){
			global $mydb;
			if(empty($orderby)){
				$orderby = "ID DESC";
			}
			$sql = "SELECT * FROM ".
				   "       ADMIN_MASTER ".
				   "WHERE ".
				   "       ADMIN_AUTHORITY!='trash' ".
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
		/// スタッフリストの取得
		///
		///	#Author yk
		/// #date	2013/11/01
		///--------------------------------------------------------------------
		function getStaffList(){
			global $mydb;
			$sql = "SELECT ID,DISPLAY_NAME FROM ".
				   "       ADMIN_MASTER ".
				   "WHERE ".
				   "       ADMIN_AUTHORITY='staff' ".
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
		/// ユーザーリストの取得
		///
		///	#Author yk
		/// #date	2013/10/22
		///--------------------------------------------------------------------
		function searchUserMasterList($field, $value, $orderby, $start, $limit_num){
			global $mydb;
			$search_sql ="";
			if(!empty($field) && !empty($value) && $value!=='all'){
				$search_sql = "AND ".$field."='".$value."' ";
			}
			if(empty($orderby)){
				$orderby = "ID ASC";
			}
			$sql = "SELECT * FROM ".
				   "       ADMIN_MASTER ".
				   "WHERE ".
				   "       ADMIN_AUTHORITY!='72web' ".
				   "AND ".
				   "       ADMIN_AUTHORITY!='trash' ".
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
		/// ユーザーリストの取得
		///
		///	#Author yk
		/// #date	2013/10/22
		///--------------------------------------------------------------------
		function countUserMasterList($field, $value){
			global $mydb;
			$search_sql ="";
			if(!empty($field) && !empty($value) && $value!=='all'){
				$search_sql = "AND ".$field."='".$value."' ";
			}
			$sql = "SELECT COUNT(*) FROM ".
				   "       ADMIN_MASTER ".
				   "WHERE ".
				   "       ADMIN_AUTHORITY!='72web' ".
				   "AND ".
				   "       ADMIN_AUTHORITY!='trash' ".
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
		/// ユーザー情報の取得（IDから）
		///
		/// #param  $id：ユーザーID
		///
		///	#Author yk
		/// #date	2013/10/22
		///--------------------------------------------------------------------
		function getUserMasterForID($id){
			global $mydb;
			$sql = "SELECT * FROM ".
				   "       ADMIN_MASTER ".
				   "WHERE ".
				   "	   ID='".$id."' ";
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
		/// ユーザー情報の取得（スタッフIDから）
		///
		/// #param  $id：ユーザーID
		///
		///	#Author yk
		/// #date	2013/10/22
		///--------------------------------------------------------------------
		function getStaffForID($id){
			global $mydb;
			$sql = "SELECT ID,DISPLAY_NAME FROM ".
				   "       ADMIN_MASTER ".
				   "WHERE ".
				   "	   ID='".$id."' ".
				   "AND ".
				   "       ADMIN_AUTHORITY='staff';";
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
		/// ユーザー情報の取得（メールアドレスから）
		///
		/// #param  $id：ユーザーID
		///
		///	#Author yk
		/// #date	2013/12/19
		///--------------------------------------------------------------------
		function getStaffForMail($mail_address){
			global $mydb;
			$sql = "SELECT ID,DISPLAY_NAME FROM ".
				   "       ADMIN_MASTER ".
				   "WHERE ".
				   "	   ADMIN_MAIL='".$mail_address."' ".
				   "AND ".
				   "       ADMIN_AUTHORITY='staff';";
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
		/// ユーザー情報の取得（アカウントから）
		///
		/// #param  $account：ユーザーID
		///
		///	#Author yk
		/// #date	2013/10/22
		///--------------------------------------------------------------------
		function getUserMasterForACCOUNT($account, $id){
			global $mydb;
			if(!empty($id)){
				$removal_sql ="AND ID!='".$id."' ";
			}else{
				$removal_sql ='';
			}
			$sql = "SELECT ID FROM ".
				   "       ADMIN_MASTER ".
				   "WHERE ".
				   "	   ADMIN_ACCOUNT='".$account."' ".
				   $removal_sql.
				   " ;";
			$res = mysql_query($sql, $mydb);
			$row = mysql_array($res);
			if($res){
				if($row){
					return $row[0]["ID"];
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
		/// ユーザー情報の入力チェック
		///
		/// #param  $post：フォームの入力情報
		///
		///	#Author yk
		/// #date	2013/05/24
		///--------------------------------------------------------------------
		function checkInputData($post){
			
			//初期設定
			$err = '';
			//IDの入力チェック
			if(!empty($post['account'])){
				//特殊文字のチェック
				if(htmlspecialchars($post['account'], ENT_QUOTES, 'utf-8') !== $post['account']){
					$err = 'account-ng';
				}
			}else{
				$err = 'account_2-ng';
			}
			//パスワードの入力チェック
			if(strcmp($post['password'], $post['password_2']) == 0){
				//特殊文字のチェック
				if(htmlspecialchars($post['password'], ENT_QUOTES, 'utf-8') !== $post['password']){
					$err = 'password-ng';
				}
			}else{
				$err = 'password_2-ng';
			}
			//メールアドレスの入力チェック
			if(strcmp($post['mail'], $post['mail_2']) == 0){
				if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $post['mail'])){
					$err = 'mail-ng';
				}
			}else{
				$err = 'mail_2-ng';
			}
			return $err;
		}
		
		///--------------------------------------------------------------------
		/// ユーザー情報の新規保存
		///
		/// #param  $post：フォームの入力情報
		///
		///	#Author yk
		/// #date	2013/10/22
		///--------------------------------------------------------------------
		function insertUserMaster($post){
			global $mydb;
			//入力チェック
			$err = $this->checkInputData($post);
			//入力エラーが無い場合
			if(!$err){
				$password = hash('md5', htmlspecialchars($post['password'], ENT_QUOTES, 'utf-8'));
				$edit_date = date("Y-m-d H:i:s");
				$sql = "INSERT INTO ".
					   "       ADMIN_MASTER ".
					   "VALUES('',".
					   "       '".$post['account']."', ".
					   "       '".$password."', ".
					   "       '".$post['mail']."', ".
					   "       '".$post['authority']."', ".
					   "       '".$post['name']."', ".
					   "       '".$post['kana']."', ".
					   "       '".$post['post']."', ".	//部署フィールド（未実装）
					   "       '".$post['body']."', ".
					   "       '".$edit_date."', ".
					   "       '".$edit_date."');";
				$res = mysql_query($sql, $mydb);
				if($res){
					//登録内容の確認メールを送信）（未実装）
					/* $this->mailUserData($post) */
					return 'insert-ok';
				}
				else{
					echo $sql;
					return 'insert-ng';
				}
			}
			//入力エラーがある場合
			else{
				return $err;
			}
		}
		
		///--------------------------------------------------------------------
		/// ユーザー情報の更新
		///
		/// #param  $post：フォームの入力情報
		///
		///	#Author yk
		/// #date	2013/05/24
		///--------------------------------------------------------------------
		function updateUserMasterForID($post){
			global $mydb;
			//入力チェック
			$err = $this->checkInputData($post);
			//入力エラーが無い場合
			if(!$err){
				if($post['password']==='no_change'){
					$password_sql ="";
				}
				else{
					$password = hash('md5', htmlspecialchars($post['password'], ENT_QUOTES, 'utf-8'));
					$password_sql ="ADMIN_PASSWORD='".$password."', ";
				}
				$edit_date = date("Y-m-d H:i:s");
				$sql = "UPDATE ".
					   "       ADMIN_MASTER ".
					   "SET ".
					   "       ADMIN_ACCOUNT='".$post['account']."', ".
					   $password_sql.
					   "       ADMIN_MAIL='".$post['mail']."', ".
					   "       ADMIN_AUTHORITY='".$post['authority']."', ".
					   "       DISPLAY_NAME='".$post['name']."', ".
					   "       DISPLAY_KANA='".$post['kana']."', ".
					   "       DISPLAY_POST='".$post['post']."', ". //部署フィールド（未実装）
					   "       DISPLAY_BODY='".$post['body']."', ".
					   "       EDIT_DATE='".$edit_date."' ".
					   "WHERE ".
					   "       ID='".$post['id']."';";
				$res = mysql_query($sql, $mydb);
				if($res){
					//登録内容の確認メールを送信）（未実装）
					/* $this->mailUserData($post) */
					return 'update-ok';
				}
				else{
					echo $sql;
					return 'update-ng';
				}
			}
			//入力エラーがある場合
			else{
				return $err;
			}
		}
		
		///--------------------------------------------------------------------
		/// ユーザー情報の削除
		///
		/// #param  $id：ユーザーID
		///
		///	#Author yk
		/// #date	2013/05/24
		///	#Author yk
		/// #date	2013/11/12
		///--------------------------------------------------------------------
		function deleteUserMasterForID($id){
			global $mydb;
			$edit_date = date("Y-m-d H:i:s");
			$sql = "UPDATE ".
				   "       ADMIN_MASTER ".
				   "SET ".
				   "       ADMIN_AUTHORITY='trash', ".
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