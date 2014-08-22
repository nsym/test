<?php
	/* /////////////////////////////////////////////////////
	//		メルマガ管理　新規作成・編集・削除
	//////////////////////////////////////////////////////*/
	require_once("../common/config.php");
	require_once("../db/op-mailmaga/template.php");
	$mydb = db_con();
	
	//インスタンス化
	$templateObj = new Template();
	
	//print_r($_GET);
	
	//入力OKの場合
	if(!empty($_POST['name'])){
		//新規保存の場合
		if(isset($_POST['template-new'])){
			$res = $templateObj->insertMailmagaTemplate($_POST);
		}
		else if(isset($_POST['template-edit'])){
			$res = $templateObj->updateMailmagaTemplate($_POST);
		}
		if(isset($_POST['template-edit'])){
			$mode = 'template';
			$res.='&template_id='.$_POST['id'];
		}
		else if(isset($_POST['template-new'])){
			$mode = 'template';
		}
	}
	
	//入力NGの場合
	else if(isset($_POST['template-new']) || isset($_POST['template-edit'])){
		if(isset($_POST['template-new'])){
			$mode = 'template';
		}
		else if(isset($_POST['template-edit'])){
			$mode = 'template';	
		}
		$res = 'input-ng';
		if($mode=='edit'){
			$res.='&template_id='.$_POST['id'];
		}
	}
	//削除の場合
	else if($_GET['action']==='template-delete'){
		$mode = 'template';
		$res = $templateObj->deleteMailmagaTemplateForID($_GET['id']);
	}
	
	//リダイレクトURLの生成
	$url = '/admin/op-mailmaga/?mode='.$mode.'&state='.$res;
	//リダイレクト処理
	header("Location: $url");
	
?>
