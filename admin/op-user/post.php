<?php
	/* /////////////////////////////////////////////////////
	//		ユーザー管理　新規作成・編集・削除
	//////////////////////////////////////////////////////*/
	require_once("../common/config.php");
	require_once("../db/op-user/user.php");
	$mydb = db_con();
	
	//インスタンス化
	$userObj = new User();
	
	//print_r($_POST);
	
	//入力OKの場合
	if(!empty($_POST['name'])){
		//新規保存の場合
		if(isset($_POST['new'])){
			$res = $userObj->insertUserMaster($_POST);
		}
		else if(isset($_POST['edit'])){
			$res = $userObj->updateUserMasterForID($_POST);
		}
		if(isset($_POST['edit'])){
			$mode = 'list';
			$res.='&id='.$_POST['id'];
		}
		else if(isset($_POST['new'])){
			$mode = preg_match('/-ok/', $res)? 'list': 'new';
		}
	}
	//入力NGの場合
	else if(isset($_POST['new']) || isset($_POST['edit'])){
		$mode = isset($_POST['new'])? 'new': 'edit';
		$res = 'input-ng';
		if($mode=='edit'){
			$res.='&id='.$_POST['id']	;
		}
	}
	//削除の場合
	else if($_GET['action']==='trash'){
		$mode = 'list';
		$res = $userObj->deleteUserMasterForID($_GET['id']);
	}
	
	//リダイレクトURLの生成
	$url = '/admin/op-user/?mode='.$mode.'&state='.$res;
	
	//リダイレクト処理
	header("Location: $url");
	
?>
