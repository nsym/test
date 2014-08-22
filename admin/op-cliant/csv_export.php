<?php
	/* /////////////////////////////////////////////////////
	//		CSVダウンロード
	//	
	//	#Author mukai
	//	#date	2013/12/04
	//////////////////////////////////////////////////////*/
	require_once("../common/config.php");
	require_once("../db/op-user/user.php");
	require_once("../db/op-cliant/cliant.php");
	require_once("../db/op-cliant/group.php");
	$mydb = db_con();
	//インスタンス化
	$userObj = new User();
	$cliantObj = new Cliant();
	$groupObj = new Group();
	//フラグリストの呼び出し
	global $mailmaga_flag_list;
	global $ipros_flag_list;
	global $exhibition_flag_list;
	global $kyoto_flag_list;
	global $rank_list;
	global $filed_list;
	//ユーザー情報の呼び出し
	$staff = $userObj->getUserMasterList('');
	//グループ情報の呼び出し
	$group = $groupObj->getCliantGroupList();
	//全件出力の場合
	if($_POST['group_id'] =='0'){
		$cliant_list = $cliantObj->getCliantcsvListForGroupID('');
	}
	//グループ出力の場合
	else if($_POST['group_id'] !='0'){
		$cliant_list = $cliantObj->getCliantcsvListForGroupID($_POST['group_id']);
	}
	
	//$csv_file = "cliant_csv_". date ( "Ymd" ) .'.csv';
	header('Cache-Control: public');
	header("Pragma: public");
	header('Content-Type: application/x-csv');
	header('Content-Disposition: attachment; filename='.$_POST['title'].'.csv');

	$fopen = fopen('php://output',  'w');
	//フィールド名の書き込み
	$i =0;
	foreach($filed_list as $value){
		$csv_field[$i] = mb_convert_encoding($value, "SJIS-win", "UTF-8");
		$i++;
	}
	fputcsv($fopen,  $csv_field);
	//顧客リストの書き込み
	foreach($cliant_list as $cliant_data){
		$csv_data = array(
			mb_convert_encoding($cliant_data['ID'], "SJIS-win", "UTF-8"),
			mb_convert_encoding($staff[$cliant_data['STAFF_ID']]['DISPLAY_NAME'], "SJIS-win", "UTF-8"),
			mb_convert_encoding($rank_list[$cliant_data['MASTER_RANK']], "SJIS-win", "UTF-8"),
			mb_convert_encoding($cliant_data['MASTER_NAME'], "SJIS-win", "UTF-8"),
			mb_convert_encoding($cliant_data['MASTER_KANA'], "SJIS-win", "UTF-8"),
			mb_convert_encoding($cliant_data['MASTER_COMPANY'], "SJIS-win", "UTF-8"),
			mb_convert_encoding($cliant_data['MASTER_URL'], "SJIS-win", "UTF-8"),
			mb_convert_encoding($cliant_data['MASTER_OFFICE'], "SJIS-win", "UTF-8"),
			mb_convert_encoding($cliant_data['MASTER_BELONG'], "SJIS-win", "UTF-8"),
			mb_convert_encoding($cliant_data['MASTER_POST'], "SJIS-win", "UTF-8"),
			mb_convert_encoding($cliant_data['MASTER_BUSINESS'], "SJIS-win", "UTF-8"),
			mb_convert_encoding($cliant_data['MASTER_JOB'], "SJIS-win", "UTF-8"),
			mb_convert_encoding($cliant_data['MASTER_TEL'], "SJIS-win", "UTF-8"),
			mb_convert_encoding($cliant_data['MASTER_FAX'], "SJIS-win", "UTF-8"),
			mb_convert_encoding($cliant_data['MASTER_MAIL'], "SJIS-win", "UTF-8"),
			mb_convert_encoding($cliant_data['MASTER_AREA'], "SJIS-win", "UTF-8"),
			mb_convert_encoding($cliant_data['MASTER_ZIPCODE'], "SJIS-win", "UTF-8"),
			mb_convert_encoding($cliant_data['MASTER_ADDRESS'], "SJIS-win", "UTF-8"),
			mb_convert_encoding($mailmaga_flag_list[$cliant_data['MAILMAGA_FLAG']], "SJIS-win", "UTF-8"),
			mb_convert_encoding($ipros_flag_list[$cliant_data['IPROS_FLAG']], "SJIS-win", "UTF-8"),
			mb_convert_encoding($exhibition_flag_list[$cliant_data['EXHIBITION_FLAG']], "SJIS-win", "UTF-8"),
			mb_convert_encoding($kyoto_flag_list[$cliant_data['KYOTO_FLAG']], "SJIS-win", "UTF-8"),
			mb_convert_encoding($cliant_data['MASTER_STATUS'], "SJIS-win", "UTF-8"),
			"\n"
		);
		fputcsv($fopen,  $csv_data);
	}
	
	fclose($fopen);
	exit();
?>
