<?php
	/* /////////////////////////////////////////////////////
	//		受注管理　編集・削除
	//////////////////////////////////////////////////////*/
	require_once("../common/config.php");
	require_once("../db/op-accept/accept.php");
	require_once("../db/op-accept/cliant.php");
	$mydb = db_con();
	
	//インスタンス化
	$acceptObj = new Accept();
	$cliantObj = new Cliant();
	
	//print_r($_POST);
	
	//入力OKの場合
	if(!empty($_POST['title']) && !empty($_POST['price'])){
		//受注日の設定
		$_POST["accept_date"] = date('Y-m-d', mktime(0, 0, 0, $_POST["accept_date_array"][1], $_POST["accept_date_array"][2], $_POST["accept_date_array"][0]));
		//納期の設定
		$_POST["accept_limit"] = date('Y-m-d', mktime(0, 0, 0, $_POST["accept_limit_array"][1], $_POST["accept_limit_array"][2], $_POST["accept_limit_array"][0]));
		
		//更新の場合
		if(isset($_POST['edit'])){
			$res = $acceptObj->updateAcceptMasterForID($_POST);
		}
			
		//リダイレクト先の設定
		$mode = 'edit';
		$res.='&id='.$_POST['id'];
	}
	
	//入力NGの場合
	else if(isset($_POST['edit'])){
		$mode = 'edit';	
		$res = 'input-ng&id='.$_POST['id'];
	}
	//削除の場合
	else if($_GET['action']==='trash'){
		$mode = 'list';
		$res = $acceptObj->deleteAcceptMasterForID($_GET['id']);
	}
	
	//リダイレクトURLの生成
	$url = '/admin/op-accept/?mode='.$mode.'&state='.$res;
	//リダイレクト処理
	header("Location: $url");
	
?>
