<?php
	/* /////////////////////////////////////////////////////
	//		顧客管理　新規作成・編集・削除
	//////////////////////////////////////////////////////*/
	require_once("../common/config.php");
	require_once("../db/op-cliant/group.php");
	$mydb = db_con();
	
	//インスタンス化
	$groupObj = new Group();
	
	//print_r($_GET);
	
	//入力OKの場合
	if(!empty($_POST['name'])){
		//新規保存の場合
		if(isset($_POST['group-new'])){
			$res = $groupObj->insertCliantGroup($_POST);
		}
		else if(isset($_POST['group-edit'])){
			$res = $groupObj->updateCliantGroupForID($_POST);
		}
		if(isset($_POST['group-edit'])){
			$mode = 'group';
			$res.='&group_id='.$_POST['id'];
		}
		else if(isset($_POST['group-new'])){
			$mode = preg_match('/-ok/', $res)? 'group': 'new';
		}
	}
	
	//入力NGの場合
	else if(isset($_POST['group-new']) || isset($_POST['group-edit'])){
		if(isset($_POST['group-new'])){
			$mode = 'group';
		}
		else if(isset($_POST['group-edit'])){
			$mode = 'group';	
		}
		$res = 'input-ng';
		if($mode=='edit'){
			$res.='&group_id='.$_POST['id'];
		}
	}
	//削除の場合
	else if($_GET['action']==='group-delete'){
		$mode = 'group';
		$res = $groupObj->deleteCliantGroupForID($_GET['id']);
	}
	
	//リダイレクトURLの生成
	$url = '/admin/op-cliant/?mode='.$mode.'&state='.$res;
	//リダイレクト処理
	header("Location: $url");
	
?>
