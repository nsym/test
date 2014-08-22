<?php
	/* /////////////////////////////////////////////////////
	//		顧客管理　新規作成・編集・削除
	//////////////////////////////////////////////////////*/
	require_once("../common/config.php");
	require_once("../db/op-cliant/history.php");
	require_once("../db/op-cliant/cliant.php");
	require_once("../db/op-user/user.php");
	$mydb = db_con();
	
	//インスタンス化
	$historyObj = new History();
	$imageObj = new Image();
	
	//print_r($_POST);
	
	//入力OKの場合
	if(!empty($_POST['title'])){
		//対応日時の設定
		$_POST["date"] = date('Y-m-d H:i:s', mktime($_POST["date_array"][3], 0, 0, $_POST["date_array"][1], $_POST["date_array"][2], $_POST["date_array"][0]));
		//画像の削除処理
		if($_POST['image_delete']){
			unlink(MAIN_DIRECTORY.$_POST["image"]);
			unlink(THUM_DIRECTORY.$_POST["image"]);
			$_POST["image"] ='';
		}
		//画像アップロードが成功した場合
		$photo = '';
		if($_FILES["image"]["error"]=='0'){
			//画像の一時保存
			$photo = $imageObj->uploadImage('image', TEMP_DIRECTORY, 'uniq');
			//画像のリサイズ保存
			if($photo != "image-ng"){
				$imageObj->resizeImage(MAIN_DIRECTORY, TEMP_DIRECTORY, $photo, '640');
				$imageObj->trimmingImage(THUM_DIRECTORY, TEMP_DIRECTORY, $photo, '100', '80');
				unlink(TEMP_DIRECTORY.$photo);
			}
		}
		$_POST["image"] = !empty($photo)? $photo: $_POST["image"];
		//新規保存の場合
		if(isset($_POST['new'])){
			$res = $historyObj->insertCliantHistory($_POST);
		}
		else if(isset($_POST['edit'])){
			$res = $historyObj->updateCliantHistoryForHistoryID($_POST);
		}
		
		//最新の対応履歴のアップデート	2014.06.09yk
		if(preg_match('/-ok/', $res)){
			//スタッフ名の設定
			$staffObj = new User();
			$staff_name ='スタッフ未設定';
			if(!empty($_POST["staff_id"])){
				$staff_data = $staffObj->getStaffForID($_POST["staff_id"]);
				$staff_name = $staff_data["DISPLAY_NAME"];
			}
			//最新の対応履歴か日時を比較
			$cliantObj = new Cliant();
			$recent_history = $historyObj->getCliantHistoryRecent($_POST["master_id"]);
			if($recent_history["HISTORY_DATE"] == $_POST["date"]){
				//アップデート
				$cliantObj->updateCliantRecentHistoryForID($_POST["master_id"], $recent_history["HISTORY_DATE"], $staff_name);
			}
		}
		
		if(isset($_POST['edit'])){
			$mode = 'edit';
			$res.='&id='.$_POST['master_id'];
		}
		else if(isset($_POST['new'])){
			$mode = preg_match('/-ok/', $res)? 'edit': 'list';
			$res.='&id='.$_POST['master_id'];
		}
	}
	
	//入力NGの場合
	else if(isset($_POST['new']) || isset($_POST['edit'])){
		if(isset($_POST['new'])){
			$mode = 'edit';
		}
		else if(isset($_POST['edit'])){
			$mode = 'edit';	
		}
		$res = 'input-ng';
		$res.='&id='.$_POST['master_id'];
	}
	//削除の場合
	else if($_GET['action']==='delete'){
		$mode = 'edit';
		$res = $historyObj->deleteCliantHistoryForID($_GET['id']);
	}
	
	//リダイレクトURLの生成
	$url = '/admin/op-cliant/?mode='.$mode.'&state='.$res;
	//リダイレクト処理
	header("Location: $url");
	
?>
