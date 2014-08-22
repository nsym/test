#!/user/bin/php
<?php
	/* /////////////////////////////////////////////////////
	//	メルマガ管理　メール送信処理（cron）
	//
	//	#Author yk
	//	#date	2013/11/14
	//	#Author yk
	//	#date	2014/08/18
	//////////////////////////////////////////////////////*/
	require_once("/home/kir016651/public_html/nsym/test.nsym-chemix.com/admin/common/config.php");
	//require_once("../../common/smtpmail.php");
	require_once("/home/kir016651/public_html/nsym/test.nsym-chemix.com/admin/common/jphpmailer.php");
	require_once("/home/kir016651/public_html/nsym/test.nsym-chemix.com/admin/db/op-mailmaga/mailmaga.php");
	require_once("/home/kir016651/public_html/nsym/test.nsym-chemix.com/admin/db/op-mailmaga/queue.php");
	require_once("/home/kir016651/public_html/nsym/test.nsym-chemix.com/admin/db/op-mailmaga/history.php");
	require_once("/home/kir016651/public_html/nsym/test.nsym-chemix.com/admin/db/op-mailmaga/cliant.php");
	require_once("/home/kir016651/public_html/nsym/test.nsym-chemix.com/admin/db/op-user/user.php");
	$mydb = db_con();
	
	//初期設定
	$time = date('Y-m-d H:i:s');
	$send_limit = 1000;
	
	//インスタンス化
	$mailmagaObj = new Mailmaga();
	$queueObj = new Queue();
	$historyObj = new History();
	$cliantObj = new Cliant();
	//$smtpMailObj = new SmtpMail();
	
	//予約メルマガの取得
	$mailmaga_list = $mailmagaObj->getMailmagaPostListForStatusPostTime('reserved', $time);
	$mailmaga_list = array_merge($mailmaga_list, $mailmagaObj->getMailmagaPostListForStatusPostTime('sending', $time));
	//メルマガキューの全件数をカウント
	$count_queue_all = $queueObj->countMailmagaQueueForMailmagaListTime($mailmaga_list, $time);
	
	//予約メルマガの数だけループ処理（配信用キューの取得処理-ここから-）
	$queue_list =array();
	foreach($mailmaga_list as $value){
		//キューの取得数の算出
		$count_queue = $queueObj->countMailmagaQueueLimit($value["ID"], $time);
		//配信キューがある場合
		if($count_queue>0){
			//予約メルマガのステータスを変更
			$res = $mailmagaObj->changeMailmagaPostForID($value["ID"], 'sending');
			//キューの総数とキューの数から計算レートを算出
			$rate = $count_queue / $count_queue_all;
			//取得・配信するキューの数を設定
			$limit_num = floor($send_limit*$rate);
			//キューの取得・追加（配信日時、メルマガIDから）
			$queue_list = array_merge($queue_list, $queueObj->getMailmagaQueueLimit($value["ID"], $time, $limit_num));
		}
		//配信キューがない場合
		else{
			//予約メルマガのステータスを変更
			$res = $mailmagaObj->changeMailmagaPostForID($value["ID"], 'complete');
		}
	}
	
	//取得したキューの数だけループ処理（メルマガの配信処理-ここから-）
	foreach($queue_list as $value){
		//PHPMailer初期設定
		mb_language("japanese");
		mb_internal_encoding("UTF-8");
		$phpMailObj = new JPHPMailer();
		
		//メール配信設定
		$phpMailObj->addTo($value["CLIANT_MAIL"]);
		$phpMailObj->setFrom(MAILMAGA_MAIL, MAILMAGA_MAIL_NAME);
		$phpMailObj->setSubject($value["POST_TITLE"]);
		
		//テキストメールの本文設定
		if($value["POST_MODE"]==='TEXT'){
			$phpMailObj->setBody($value["POST_TEXT_BODY"]);
		}
		//HTMLメールの場合
		else{
			$phpMailObj->setHtmlBody($value["POST_HTML_BODY"]);
			$phpMailObj->setAltBody($value["POST_TEXT_BODY"]);
		}
		
		//配信処理
		//if(false){	//テスト用に配信処理をコメントアウト　稼働時に要修正！！//
		if(!$phpMailObj->send()){
			$err_subject = '【西ケミ案件管理】メール配信でエラーが発生しました。';
			$err_body = 'メールが送信できませんでした。エラー:'.$phpMailObj->getErrorMessage();
			errorMail(BCC_MAIL, $subject, $err_body);
			/* 該当キューのステータス変更処理 */
			
		}else{
			//キューの削除・ログの保存処理・対応履歴の保存処理-- 2014.01.10yk --
			if($queueObj->logingMailmagaQueue($value)){
				$queueObj->deleteMailmagaQueueForID($value["ID"]);
				//対応履歴の保存
				$history_post =array(
					'master_id' => $value["CLIANT_ID"],
					'staff_id' => $value["STAFF_ID"],
					'date' => date('Y-m-d H:i:s'),
					'category' => 'except',
					'title' => 'メルマガ配信しました',
					'body' => '件名：'.$value["POST_TITLE"]
				);
				
				//最新の対応履歴のアップデート	2014.06.09yk
				if($historyObj->insertCliantHistory($history_post)){
					//スタッフ名の設定
					$staffObj = new User();
					$staff_name ='スタッフ未設定';
					if(!empty($value["STAFF_ID"])){
						$staff_data = $staffObj->getStaffForID($value["STAFF_ID"]);
						$staff_name = $staff_data["DISPLAY_NAME"];
					}
					//アップデート
					$cliantObj->updateCliantRecentHistoryForID($value["CLIANT_ID"], date('Y-m-d H:i:s'), $staff_name);
				}
			}
		}
	}
	
		
	///--------------------------------------------------------------------
	/// エラーメール送信
	///
	///	#Author yk
	/// #date	2013/11/14
	///	#Author yk
	/// #date	2014/08/18
	///--------------------------------------------------------------------
	function errorMail($mail_address, $subject, $body){
		//言語設定及び内部エンコーディング
		mb_language("japanese");
		mb_internal_encoding("UTF-8");
		mb_send_mail($mail_address, $subject, $body, "From:".MAILMAGA_MAIL);
	}
?>