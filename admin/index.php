<?php
	/* /////////////////////////////////////////////////////
	//		ホーム INDEX
	//////////////////////////////////////////////////////*/
	require_once("./common/config.php");
	require_once("./op-home/view.php");
	$mydb = db_con();
	
	session_start();
	$ssid = session_id();
	$user_id = $_SESSION["user_id"];
	$authObj = new Auth($user_id);
	
	//各クラスのインスタンス化
	$adminViewObj = new AdminView();
	$opViewObj = new View();
	$adminViewObj->operation = $opViewObj->operation = 'op-home';
	$opViewObj->state = isset($_GET["state"])? $_GET["state"]: '';
	
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
	<title><?= ADMIN_TITLE ?></title>
	<link rel="stylesheet" type="text/css" href="/admin/css/reset.css">
	<link rel="stylesheet" type="text/css" href="/admin/css/admin.css">
	<link rel="stylesheet" type="text/css" href="/admin/op-home/main.css">
</head>
<body>
	<? $adminViewObj->setHeader() ?>
	<div id="container">
		<div id="sidebar">
		</div>
		<div id="contents">
			<div class="wrapper">
				<? $opViewObj->setContents() ?>
			</div>
		</div>
	</div>
	
	<script src="/admin/js/jquery-1.7.2.min.js"></script>
	<script src="/admin/js/main.js"></script>
	<script type="text/javascript">
	<!--
		$(function(){
			
			//読み込み中のローディング設定
			$("#ajaxLoader").ajaxStart(function() {
				$(this).show();
			}).ajaxStop(function() {
				$(this).hide();
			});
		});
		
		//スケジュール内容の表示機能
		function ajax_ctrl_calender(date){
			$('#calendar-wrap').remove();
			$.get('/admin/schedule_ajax.php?action=calender&date='+date,
				function(data){
					$('#schedule-wrap').before(data);
				},
				'html'
			);
			return false;
		};
		
		//スケジュール内容の表示機能
		function ajax_schedule_detail(id){
			$('#schedule-wrap').remove();
			$.get('/admin/schedule_ajax.php?action=detail&id='+id,
				function(data){
					$('#calendar-wrap').after(data);
				},
				'html'
			);
			return false;
		};
	-->
  </script>
</body>
</html>
