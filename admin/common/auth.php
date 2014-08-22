<?php
	/* /////////////////////////////////////////////////////
	//		ログイン認証パッケージ
	//////////////////////////////////////////////////////*/
	
	////////////////////////////////////////////////////////
	//
	//	#substance	パッケージの作成
	//  #Date		2013/04/25
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
	
	class Auth {
		
		public $admin_data;
		
		///--------------------------------------------------------------------
		/// 【コンストラクター】
		///
		/// 戻り値　なし
		///
		///	#Author yk
		/// #date	2013/05/16
		///--------------------------------------------------------------------
		function __construct($user_id){
			if(strcmp($user_id, 'login')!=0){
				$admin_id = $this->getAdminLoginForID($user_id);
				$this->admin_data = $this->getAdminMasterForID($admin_id);
			}
		}
		
		///--------------------------------------------------------------------
		/// ログイン認証
		///
		///	#param	String	$id		アカウント
		///	#param	String	$pass	パスワード
		///
		///	#Author yk
		/// #date	2013/05/16
		///--------------------------------------------------------------------
		function getAdminMasterForIdPass($id, $pass){
			global $mydb;
			$sql = "SELECT * FROM ".
				   "       ADMIN_MASTER ".
				   "WHERE ".
				   "       ADMIN_ACCOUNT='".$id."' ".
				   "AND ".
				   "       ADMIN_PASSWORD='".$pass."';";
			$res = mysql_query($sql, $mydb);
			$row = mysql_array($res);
			if(!empty($row)){
				return $row[0];
			}
			else{
				echo $sql;
				return false;
			}
		}
		
		///--------------------------------------------------------------------
		/// IDからメンバー情報を取得
		///
		///	#param	$id:管理者ID
		///
		///	#Author yk
		/// #date	2013/05/16
		///--------------------------------------------------------------------
		function getAdminMasterForID($admin_id){
			global $mydb;
			$sql = "SELECT ".
				   "	   ID, ".
				   "	   DISPLAY_NAME, ".
				   "	   ADMIN_AUTHORITY ".
				   "FROM ".
				   "       ADMIN_MASTER ".
				   "WHERE ".
				   "       ID='".$admin_id."' ;";
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
		/// IDからメンバー名前を取得
		///
		///	#param	$id:管理者ID
		///
		///	#Author yk
		/// #date	2013/05/27
		///--------------------------------------------------------------------
		function getDisplayNameForID($admin_id){
			global $mydb;
			$sql = "SELECT ".
				   "	   DISPLAY_NAME ".
				   "FROM ".
				   "       ADMIN_MASTER ".
				   "WHERE ".
				   "       ID='".$admin_id."' ;";
			$res = mysql_query($sql, $mydb);
			$row = mysql_array($res);
			if($res){
				return $row[0]["DISPLAY_NAME"];
			}
			else{
				echo $sql;
				return false;	
			}
		}
		
		///--------------------------------------------------------------------
		/// ログインチェック
		///
		/// 戻り値　ID
		///
		///	#Author yk
		/// #date	2013/05/16
		///--------------------------------------------------------------------
		function getAdminLoginForID($user_id){
			global $mydb;
			$ip = $_SERVER["REMOTE_ADDR"];
			$host = gethostbyaddr($ip);
			$user_agent = $_SERVER["HTTP_USER_AGENT"];
			$sql = "SELECT * FROM ".
				   "       ADMIN_LOGIN ".
				   "WHERE ".
				   "       ID='".$user_id."' ".
				   "AND ".
				   "       IP='".$ip."' ".
				   "AND ".
				   "       HOST='".$host."' ".
				   "AND ".
				   "       USER_AGENT='".$user_agent."' ".
				   "ORDER BY ID DESC;";
			$res = mysql_query($sql, $mydb );
			$row = mysql_array($res);
			//タイムアウト確認用（昨日の00:00:00）
			$timeout = date("Y-m-d H:i:s", strtotime("yesterday"));
			if(!empty($row) && $row[0]['DATE_REFRESH']>$timeout){
				//$nowts = $this->staffLogindbRefresh();
				$this->staffUpdateLoginDate($user_id);
				return $row[0]['MASTER_ID'];
			}
			else{
				$this->loginRedirect();
			}
		}
		
		///--------------------------------------------------------------------
		/// ログインDBのリフレッシュ
		///
		///
		///	#Author NATSU
		/// #date	2013/04/16
		///--------------------------------------------------------------------
		function staffLogindbRefresh(){
			global $mydb;
			$nowts = mktime();
			$borderts = $nowts - 600000;
			$sql = "DELETE FROM ".
				   "       ADMIN_LOGIN ".
				   "WHERE ".
				   "       TIMES < ".$borderts.";";
			//$res = mysql_query($sql, $mydb );
			return $nowts;
		}
		
		///--------------------------------------------------------------------
		/// ログインタイムの更新
		///
		/// 戻り値　なし
		///
		///	#Author yk
		/// #date	2013/05/16
		///--------------------------------------------------------------------
		function staffUpdateLoginDate($user_id){
			global $mydb;
			$sql = "UPDATE ".
				   "       ADMIN_LOGIN ".
				   "SET ".
				   "       DATE_REFRESH='".date("Y-m-d H:i:s")."' ".
				   "WHERE ".
				   "       ID='".$user_id."';";
			$res = mysql_query($sql, $mydb );
		}
		
		///--------------------------------------------------------------------
		/// ログイン情報をDBに保存
		///
		/// 戻り値	DBのID
		///
		///	#Author yk
		/// #date	2013/05/16
		///--------------------------------------------------------------------
		function adminInsLogin($admin_data){
			global $mydb;
			$nowts = mktime();
			$ip = $_SERVER["REMOTE_ADDR"];
			$host = gethostbyaddr($ip);
			$user_agent = $_SERVER["HTTP_USER_AGENT"];
			$sql = "INSERT INTO ".
				   "       ADMIN_LOGIN ".
				   "VALUES('',".
				   "       '".$admin_data["ID"]."', ".
				   "       '".$admin_data["DISPLAY_NAME"]."', ".	
				   "       '".$ip."', ".
				   "       '".$host."', ".
				   "       '".$user_agent."', ".
				   "       '".date("Y-m-d H:i:s", $nowts)."', ".	//DATE_LOGIN
				   "       '".date("Y-m-d H:i:s", $nowts)."', ".	//DATE_REFRESH
				   "       '".$nowts."');";
			$res = mysql_query($sql, $mydb );
			if($res){
				//$id = $this->getStaffLoginidForTS($staff_data["ID"], $nowts);
				//return $id;
				return mysql_insert_id();	//直近のクエリで生成されたIDを返す
			}
			else{
				return false;	
			}
		}
		
		///--------------------------------------------------------------------
		/// ログインページへのリダイレクト
		///
		///	#Author yk
		/// #date	2013/05/16
		///--------------------------------------------------------------------
		function loginRedirect($flag=''){
			$url = '/admin/login/?flag='.$flag;
			header("Location: $url");
		}
	}
?>