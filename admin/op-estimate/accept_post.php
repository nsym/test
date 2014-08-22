<?php
	/* /////////////////////////////////////////////////////
	//		見積管理　受注処理
	//////////////////////////////////////////////////////*/
	require_once("../common/config.php");
	require_once("../db/op-estimate/cliant.php");
	require_once("../db/op-estimate/accept.php");
	require_once("../db/op-estimate/estimate.php");
	require_once("../db/op-estimate/history.php");
	require_once("../db/op-user/user.php");
	$mydb = db_con();
	
	//インスタンス化
	$acceptObj = new Accept();
	$estimateObj = new Estimate();
	$cliantObj = new Cliant();
	$historyObj = new History();
	
	//print_r($_POST);
	
	//入力OKの場合
	if(!empty($_POST['estimate_id']) && !empty($_POST['title']) && !empty($_POST['price'])){
		if(isset($_POST['accept'])){
			//受注日の設定
			$_POST["accept_date"] = date('Y-m-d', mktime(0, 0, 0, $_POST["accept_date_array"][1], $_POST["accept_date_array"][2], $_POST["accept_date_array"][0]));
			//納期の設定
			$_POST["limit_date"] = date('Y-m-d', mktime(0, 0, 0, $_POST["limit_date_array"][1], $_POST["limit_date_array"][2], $_POST["limit_date_array"][0]));
			//見積の受注処理（受注DBへの新規保存）
			$res = $acceptObj->insertEstimateAccept($_POST);
			if(!preg_match('/-ng/', $res)){
				//インサートIDの取得
				$insert_id = mysql_insert_id($mydb);
				//見積のステータス変更
				$res = $estimateObj->changeStatusEstimateMaster('accept', $_POST['estimate_id']);
				//顧客対応履歴の保存
				$history_post =array(
					'master_id' => $_POST["cliant_id"],
					'staff_id' => $_POST["staff_id"],
					'date' => date('Y-m-d H:i:s'),
					'category' => 'accept',
					'title' => date('Y年m月d日受注', strtotime($_POST["accept_date"])),
					'body' => '受注件名：'.$_POST["title"],
					'accept_id' => $insert_id
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
			//リダイレクト先の設定
			$mode = 'list&id='.$_POST['estimate_id'];
			if(!preg_match('/-ng/', $res)){
				$res ='accept-ok';
			}else{
				$res ='accept-ng';
			}
			$res.='&id='.$_POST['estimate_id'];
		}
	}
	//入力NGの場合
	else {
		$mode = 'edit';	
		$res = 'input-ng&id='.$_POST['estimate_id'];
	}
	//print_r($_POST);
	
	//リダイレクトURLの生成
	$url = '/admin/op-estimate/?mode='.$mode.'&state='.$res;
	//リダイレクト処理
	header("Location: $url");
	
?>
