<?php
	require_once("../common/config.php");
	
	/*** クッキーがあれば表示する変数に代入（チェックも初期値ONにする） ***/
	if(isset($_COOKIE)){
		$display_name　= $_COOKIE['NAME'];
		$display_pass　= $_COOKIE['PASS'];
		$checked　= 'checked';
	}
	
	
	//ログアウト処理
	session_start();
	if($_SESSION["user_id"]){
		$_SESSION["user_id"] = '';
		session_destroy();
	}
	
	function loginState(){
		
		$message = $class = '';
		if(!empty($_GET["flag"])){
			switch($_GET["flag"]){
				case 'inputerr':
					$message ='ID、パスワードを正しく入力してください';
					$class ='Err';
				break;
				case 'nomember':
					$message ='ID、パスワードが正しくありません';
					$class ='Err';
				break;
				case 'logout':
					$message ='ログアウトしました';
				break;
			}
		}
		
		echo '<p class="'.$class.'">'.$message.'</p>';
	}
	
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title><?= ADMIN_TITLE ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="/admin/css/reset.css">
	<link rel="stylesheet" type="text/css" href="/admin/css/admin.css">
	<link rel="stylesheet" type="text/css" href="./temp.css">
</head>
<body id="loginBody">
	<div id="loginWrap">
		<div id="loginBox">
			<div class="message">
				<img src="http://nsym-chemix.com/admin/images/header_logo_logo.png">
				<img src="http://nsym-chemix.com/admin/images/header_logo_name.png">				
				<h1>Admin Tool</h1>
			</div>
			<form name="loginform" id="loginform" action="login.php" method="post">
				<p class="field">
					<input type="text" name="login_name" placeholder="ログインID" value="<?=$display_name ?>"/>
					<span class="icon user"></span>
				</p>
				<p class="field">
					<input type="password" name="login_pass" placeholder="パスワード" value="<?=$display_pass ?>" />
					<span class="icon pass"></span>
				</p>
				<div class="loginBtnBox">
					<label for="rememberme"><input type="checkbox" name="rememberme" id="rememberme" value="yes" <?=$checked ?>/>入力情報を保存する</label>
					<p class="submitBtn"><button type="submit" name="login">ログイン</button></p>
				</div>
			</form>
		</div>
		<div id="loginState">
			<? loginState() ?>
		</div>
	</div>
	<script src="/admin/js/jquery-1.7.2.min.js"></script>
	<script src="/admin/js/jquery.backstretch.min.js"></script>
	<script>
	<!--
        $.backstretch("/admin/images/login_background.png");  //　背景にする画像を設定
	//-->
    </script>
</body>
</html>
