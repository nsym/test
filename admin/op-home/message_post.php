<?php
	/* /////////////////////////////////////////////////////
	//		顧客管理　新規作成・編集・削除
	//////////////////////////////////////////////////////*/
	require_once("../common/config.php");
	require_once("../db/op-home/message.php");
	$mydb = db_con();
	
	//インスタンス化
	$messageObj = new Message();
	
	//print_r($_POST);
	
	//入力OKの場合
	if(!empty($_POST['message'])){
		$_POST['message'] = htmlspecialchars($_POST['message']);
		$res = $messageObj->insertMessagePost($_POST);
	}
	//入力NGの場合
	else{
		$res = 'input-ng';
	}
	
	//リダイレクトURLの生成
	$url = '/admin/?state='.$res;
	//リダイレクト処理
	header("Location: $url");
	
?>
