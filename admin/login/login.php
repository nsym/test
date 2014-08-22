<?php
	require_once("../common/config.php");
	$mydb = db_con();
	
	$authObj = new Auth('login');
	
	/*** ここに入力内容保存のチェック確認（チェックあればクッキー保存・なければクッキー削除） ***/
	if($_POST['rememberme'] !=''){
		$time = time() + 14 * 24 * 3600;
		setcookie('NAME', $_POST['login_name'], $time);
		setcookie('PASS', $_POST['login_pass'], $time);
	}
	else if($_POST['rememberme'] ==''){
		setcookie('NAME', '');
		setcookie('PASS', '');
	}
	//POST
	$login_name = htmlspecialchars($_POST["login_name"], ENT_QUOTES, 'utf-8');
	$login_pass = hash('md5', htmlspecialchars($_POST["login_pass"], ENT_QUOTES, 'utf-8'));
	
	//hash_pass確認用
	//echo $login_pass;
	
	if(!empty($login_name) && !empty($login_pass)){
	
		//IDとPASSから管理者情報の取得
		$admin_data = $authObj->getAdminMasterForIdPass($login_name, $login_pass);
		//IDとPASSが一致しなければ
		if(!$admin_data){
			$authObj->loginRedirect('nomember');
		}
		//IDとPASSが該当すれば、
		else{
			//ログインログを保存・ログインユーザーIDの取得
			$user_id = $authObj->adminInsLogin($admin_data);
			//ログイン処理
			if(!empty($user_id)){
				session_start();
				$ssid = session_id();
				$_SESSION["user_id"] = $user_id;
				$url = '/admin/';
				header("Location: $url");
			}else{
				$authObj->loginRedirect();
			}
		}
	}else{
		$authObj->loginRedirect('inputerr');
	}
	
	
	
?>
