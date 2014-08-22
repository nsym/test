<?php
	require_once("../common/config.php");
	
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
	<link rel="stylesheet" type="text/css" href="main.css">
</head>
<body id="loginBody">
	<div id="loginWrap">
		<div id="loginBox">
			<div class="message">
				<h1>Admin Tool</h1>
			</div>
			<form name="loginform" id="loginform" action="login.php" method="post">
				<p class="field">
					<input type="text" name="login_name" placeholder="ログインID"/>
					<span class="icon user"></span>
				</p>
				<p class="field">
					<input type="password" name="login_pass" placeholder="パスワード" />
					<span class="icon pass"></span>
				</p>
				<div class="loginBtnBox">
					<!--label for="rememberme"><input type="checkbox" name="rememberme" id="rememberme" value="yes"/>入力情報を保存する</label-->
					<p class="submitBtn"><button type="submit" name="login">ログイン</button></p>
				</div>
			</form>
		</div>
		<div id="loginState">
			<? loginState() ?>
		</div>
	</div>
</body>
</html>
