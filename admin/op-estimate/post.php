<?php
	/* /////////////////////////////////////////////////////
	//		見積管理　新規作成・編集・削除
	//////////////////////////////////////////////////////*/
	require_once("../common/config.php");
	require_once("../db/op-estimate/estimate.php");
	require_once("../db/op-estimate/cliant.php");
	require_once("../db/op-estimate/history.php");
	require_once("../db/op-user/user.php");
	$mydb = db_con();
	
	//インスタンス化
	$estimateObj = new Estimate();
	$cliantObj = new Cliant();
	$historyObj = new History();
	$imageObj = new Image();
	
	//print_r($_POST);
	
	//入力OKの場合
	if(!empty($_POST['cliant_id']) && !empty($_POST['title']) && !empty($_POST['price'])){
		//顧客IDチェック
		if($cliantObj->checkCliantID($_POST["cliant_id"])){
			//見積作成日の設定
			$_POST["master_date"] = date('Y-m-d', mktime(0, 0, 0, $_POST["master_date_array"][1], $_POST["master_date_array"][2], $_POST["master_date_array"][0]));
			//見積期限日の設定
			$_POST["limit_date"] = date('Y-m-d', mktime(0, 0, 0, $_POST["limit_date_array"][1], $_POST["limit_date_array"][2], $_POST["limit_date_array"][0]));
			//ファイルの保存・ファイル名設定
			$file ='';
			for($i=1; $i<=3 && !preg_match('/-ng/', $file); $i++){
				$file ='';
				//ファイルの削除処理
				if($_POST['file_delete_'.$i]){
					unlink(FILE_DIRECTORY.$_POST["file_".$i]);
					$_POST["file_".$i] ='';
				}
				//ファイルの保存処理（アップロードが成功した場合）
				if($_FILES["file_".$i]["error"]=='0'){
					$file = $imageObj->uploadImage('file_'.$i, FILE_DIRECTORY, 'uniq');
				}
				$_POST["file_".$i] = !empty($file)? $file: $_POST["file_".$i];
			}
			//新規保存の場合
			if(isset($_POST['new']) && !preg_match('/-ng/', $file)){
				$res = $estimateObj->insertEstimateMaster($_POST);
				//顧客対応履歴の保存
				$insert_id = mysql_insert_id($mydb);
				$history_post =array(
					'master_id' => $_POST["cliant_id"],
					'staff_id' => $_POST["staff_id"],
					'date' => date('Y-m-d H:i:s'),
					'category' => 'estimate',
					'title' => date('Y年m月d日作成', strtotime($_POST["master_date"])),
					'body' => '見積件名：'.$_POST["title"],
					'estimate_id' => $insert_id
				);
				
				//最新の対応履歴のアップデート	2014.06.09yk
				if($historyObj->insertCliantHistory($history_post)){
					//スタッフ名の設定
					$staffObj = new User();
					$staff_name ='スタッフ未設定';
					if(!empty($_POST["staff_id"])){
						$staff_data = $staffObj->getStaffForID($_POST["staff_id"]);
						$staff_name = $staff_data["DISPLAY_NAME"];
					}
					//アップデート
					$cliantObj->updateCliantRecentHistoryForID($_POST["cliant_id"], date('Y-m-d H:i:s'), $staff_name);
				}
			}
			//更新の場合
			else if(isset($_POST['edit']) && !preg_match('/-ng/', $file)){
				$res = $estimateObj->updateEstimateMasterForID($_POST);
			}
		}else{
			$res ='cliant-ng';
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
		$res = $estimateObj->deleteEstimateMasterForID($_GET['id']);
	}
	//ステータス一括変更の場合
	else if($_GET['action']==='status'){
		$mode = 'list';
		$res = $estimateObj->changeStatusEstimateMasterForArray($_GET['status_change'], $_GET['check_id']);
	}
	//print_r($_POST);
	//print_r($_FILES);
	
	//リダイレクトURLの生成
	$url = '/admin/op-estimate/?mode='.$mode.'&state='.$res;
	//リダイレクト処理
	header("Location: $url");
	
?>
