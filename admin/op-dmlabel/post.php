<?php
	/* /////////////////////////////////////////////////////
	//		DMラベル管理　新規作成・編集・削除
	//////////////////////////////////////////////////////*/
	require_once("../common/config.php");
	require_once("../db/op-dmlabel/dmlabel.php");
	require_once("./pdf_creat.php");
	$mydb = db_con();
	
	//インスタンス化
	$dmlabelObj = new DMlabel();
	
	//print_r($_POST);
	
	//入力OKの場合
	if(!empty($_POST['title'])){		
		//新規保存の場合
		if(isset($_POST['new'])){
			$res = $dmlabelObj->insertDMlabelMaster($_POST);
		}
		//更新の場合
		else if(isset($_POST['edit'])){
			$res = $dmlabelObj->insertDMlabelMaster($_POST);
		}
		
		//保存が成功した場合
		if(preg_match('/-ok/', $res)){
			$mode = 'list';
		}
		//保存が失敗した場合
		else{
			if(isset($_POST['edit'])){
				$mode = 'edit';
				$res.='&id='.$_POST['id'];
			}else{
				$mode = 'new';
			}
		}
	}
	//入力NGの場合
	else if(isset($_POST['new']) || isset($_POST['edit'])){
		if(isset($_POST['new'])){
			$mode = 'new';	
		}
		else if(isset($_POST['edit'])){
			$mode = 'edit';	
		}
		$res = 'input-ng';
		if($mode=='edit'){
			$res.='&id='.$_POST['id']	;
		}
	}
	//削除の場合
	else if($_GET['action']==='trash'){
		$mode = 'list';
		$res = $dmlabelObj->deleteDMlabelMasterForID($_GET['id']);
	}
	
	//リダイレクトURLの生成
	$url = '/admin/op-dmlabel/?mode='.$mode.'&state='.$res;
	//リダイレクト処理
	header("Location: $url");
	
?>
