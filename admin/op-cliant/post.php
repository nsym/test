<?php
	/* /////////////////////////////////////////////////////
	//		顧客管理　新規作成・編集・削除
	//////////////////////////////////////////////////////*/
	require_once("../common/config.php");
	require_once("../db/op-cliant/cliant.php");
	require_once("../db/op-cliant/group.php");
	$mydb = db_con();
	
	//インスタンス化
	$cliantObj = new Cliant();
	$groupObj = new Group();
	
	//print_r($_POST);
	
	//入力OKの場合
	if(!empty($_POST['name_array'][0]) && !empty($_POST['kana_array'][0])){
		//名前の設定
		$_POST['name_array'][0] = trim(mb_convert_kana($_POST['name_array'][0], 'rs', 'utf-8'));
		$_POST['name_array'][1] = trim(mb_convert_kana($_POST['name_array'][1], 'rs', 'utf-8'));
		$_POST['name'] = implode(' ', $_POST['name_array']);
		//ふりがなの設定
		if(!empty($_POST['kana_array'])){
			$_POST['kana_array'][0] = trim(mb_convert_kana($_POST['kana_array'][0], 'HVcs', 'utf-8'));
			$_POST['kana_array'][1] = trim(mb_convert_kana($_POST['kana_array'][1], 'HVcs', 'utf-8'));
			$_POST['kana'] = implode(' ', $_POST['kana_array']);
		}
		//グループの設定
		if(!empty($_POST['group_array'])){
			$_POST['group'] = ','.implode(',', $_POST['group_array']).',';
		}
		//電話番号の設定
		if(!empty($_POST['tel_array'])){
			$_POST['tel'] = implode('-', $_POST['tel_array']);
			$_POST['tel'] = mb_convert_kana($_POST['tel'], "n", "UTF-8");
		}else{
			$_POST['tel'] = '未設定';
		}
		//FAX番号の設定
		if(!empty($_POST['fax_array'])){
			$_POST['fax'] = implode('-', $_POST['fax_array']);
			$_POST['fax'] = mb_convert_kana($_POST['fax'], "n", "UTF-8");
		}else{
			$_POST['fax'] = '未設定';
		}
		//エリアの設定
		if($_POST['area']==='except'){
			$_POST['area'] = $_POST['area_text'];
		}
		
		//新規保存の場合
		if(isset($_POST['new'])){
			$res = $cliantObj->insertCliantMaster($_POST);
		}
		//更新の場合
		else if(isset($_POST['edit'])){
			$res = $cliantObj->updateCliantMasterForID($_POST);
		}
		
		//リダイレクト先の設定
		if(isset($_POST['edit'])){
			$mode = 'edit';
			$res.='&id='.$_POST['id'];
		}
		else if(isset($_POST['new'])){
			$mode = preg_match('/-ok/', $res)? 'list': 'new';
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
		$res = $cliantObj->deleteCliantMasterForID($_GET['id']);
	}
	//グループ一括登録の場合
	else if($_GET['action']==='group'){
		$mode = 'list';
		$res = $cliantObj->addGroupCliantMaster($_GET['group_add'], $_GET['check_id']);
	}
	//print_r($_POST);
	//print_r($_FILES);
	
	//リダイレクトURLの生成
	$url = '/admin/op-cliant/?mode='.$mode.'&state='.$res;
	//リダイレクト処理
	header("Location: $url");
	
?>
