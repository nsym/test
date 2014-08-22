<?php
	/* /////////////////////////////////////////////////////
	//		ホーム　スケジュールの新規保存・編集
	//////////////////////////////////////////////////////*/
	require_once("../common/config.php");
	require_once("../db/op-home/schedule.php");
	$mydb = db_con();
	
	//インスタンス化
	$scheduleObj = new Schedule();
	
	//print_r($_POST);
	
	//入力OKの場合
	if(!empty($_POST['title'])){
		
		//スケジュール日時の設定	--「分」の保存機能追加　2014.01.08yk-- 
		$_POST["date"] = date('Y-m-d H:i:s', mktime($_POST["date_array"][3], $_POST["date_array"][4], 0, $_POST["date_array"][1], $_POST["date_array"][2], $_POST["date_array"][0]));
		$_POST['title'] = htmlspecialchars($_POST['title']);
		
		//新規保存の場合
		if(isset($_POST['new'])){
			$res = $scheduleObj->insertScheduleMaster($_POST);
		}
		//編集の場合
		else if(isset($_POST['edit'])){
			$res = $scheduleObj->updateScheduleMasterForID($_POST);
		}
	}
	//入力NGの場合
	else if(isset($_POST['new']) || isset($_POST['edit'])){
		$res = 'input-ng';
	}
	//削除の場合
	else if($_GET['action']==='delete'){
		$mode = 'edit';
		$res = $scheduleObj->deleteScheduleMasterForID($_GET['id']);
	}
	
	//リダイレクトURLの生成
	$url = '/admin/?state='.$res;
	//リダイレクト処理
	header("Location: $url");
	
?>
