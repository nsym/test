#!/usr/bin/php
<?php
		mb_language("ja");
		$adduserHeader = "X-Mailer:CHEMIX Mail System\n";
		$adduserHeader .= "From:history@nsym-chemix.com";
		mb_send_mail('yokoe@72web.co.jp', 'パイプ確認しました。', '', $adduserHeader);
	require_once("/home/kir016651/public_html/nsym/test.nsym-chemix.com/admin/common/config.php");
	require_once("/home/kir016651/public_html/nsym/test.nsym-chemix.com/admin/db/op-user/user.php");
	require_once("/home/kir016651/public_html/nsym/test.nsym-chemix.com/admin/db/op-cliant/cliant.php");
	require_once("/home/kir016651/public_html/nsym/test.nsym-chemix.com/admin/db/op-cliant/history.php");
	require_once("Mail/mimeDecode.php");
	$mydb = db_con();
		
	mb_language("ja");
	mb_internal_encoding('utf-8');
	
	//メール本文の読み込み
	$source = file_get_contents("php://stdin");
	//$source = file_get_contents("/home/kir016651/public_html/nsym/test.nsym-chemix.com/admin/history_mail/test_mail.txt");	//確認用メールソースの読み込み
	//print_r($source);
	
	$decoder = new ReceiptMailDecoder($source);
	//送信元アドレスの取得
	$from_mail = $decoder->getSenderAddr();
	if(empty($from_mail)){
		$from_mail = $decoder->getFromAddr();
	}
	//送信先アドレスの取得
	$to_mail = $decoder->getToAddr();
	
	//エラーフラグの設定
	$ERR = false;
	
	//メール件名の取得
	$mail_title = $decoder->getRawHeader('subject');
	$mail_title = mb_convert_encoding($mail_title, "UTF-8", "auto");
	$mail_title = mb_eregi_replace('\'', ' ', $mail_title);
	
	//スタッフ情報の取得（送信元アドレスから）
	$staffObj = new User();
	$staff_data = $staffObj->getStaffForMail($from_mail);
	if(empty($staff_data)){
		$ERR = true;
		$ERR_MESSAGE = 'スタッフ情報が見当たりません';
	}
	//print_r($staff_data);
	
	//顧客情報の取得（送信先アドレスから）
	$cliantObj = new Cliant();
	$cliant_data = $cliantObj->getCliantMasterForMail($to_mail);
	if(empty($staff_data)){
		$ERR = true;
		$ERR_MESSAGE = '顧客情報が見当たりません';
	}
	//print_r($cliant_data);
	
	//対応履歴の保存
	if(!$ERR){
		$post = array(
			'master_id' => $cliant_data["ID"],
			'staff_id' => $staff_data["ID"],
			'date' => date('Y-m-d H:i:s'),
			'category' => 'mail',
			'title' => $mail_title,
		);
		$historyObj = new History();
		$res = $historyObj->insertCliantHistory($post);
		
		//最新の対応履歴のアップデート	2014.06.09yk
		if(preg_match('/-ok/', $res)){
			//スタッフ名の設定
			$staff_name ='スタッフ未設定';
			if(!empty($staff_data["DISPLAY_NAME"])){
				$staff_name = $staff_data["DISPLAY_NAME"];
			}
			//アップデート
			$cliantObj->updateCliantRecentHistoryForID($cliant_data["ID"], date('Y-m-d H:i:s'), $staff_name);
		}
		//対応履歴保存に失敗した場合
		if(preg_match('/-ng/', $res)){
			//エラーメールの送信
			err_mail(BCC_MAIL, '対応履歴保存に失敗しました。');
		}
	}else{
		//エラーメールの送信
		err_mail(BCC_MAIL, $ERR_MESSAGE);
	}
	
	function err_mail($mail_address, $subject){		
		mb_language("ja");
		mb_internal_encoding('utf-8');
		$adduserHeader = "X-Mailer:CHEMIX Mail System\n";
		$adduserHeader .= "From:".HISTORY_MAIL;
		$body = $subject.$from_mail;
		mb_send_mail($mail_address, $subject, $body, $adduserHeader);
	}
?>