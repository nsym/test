#!/user/bin/php
<?php
	/* /////////////////////////////////////////////////////
	//	メルマガ管理　キューの生成（cron）
	//	#Author yk
	//	#date	2013/11/13
	//	#Author yk
	//	#date	2014/08/18
	//////////////////////////////////////////////////////*/
	require_once("/home/kir016651/public_html/nsym/test.nsym-chemix.com/admin/common/config.php");
	require_once("/home/kir016651/public_html/nsym/test.nsym-chemix.com/admin/db/op-mailmaga/mailmaga.php");
	require_once("/home/kir016651/public_html/nsym/test.nsym-chemix.com/admin/db/op-mailmaga/queue.php");
	require_once("/home/kir016651/public_html/nsym/test.nsym-chemix.com/admin/db/op-mailmaga/cliant.php");
	$mydb = db_con();
	
	//初期設定
	$MOBILE_DOMAIN = array(
	  'docomo.ne.jp',
	  'ezweb.ne.jp',
	  'softbank.ne.jp',
	  'vodafone.ne.jp',
	  'willcom.com',
	  'pdx.ne.jp',
	  'disney.ne.jp',
	  'emnet.ne.jp',
	  'i.softbank.jp'
	);
	
	//インスタンス化
	$mailmagaObj = new Mailmaga();
	$queueObj = new Queue();
	$cliantObj = new Cliant();
	
	//配信予約中のメルマガリストの取得
	$mailmaga_list = $mailmagaObj->getMailmagaPostListForStatusInsTime('reserving', date('Y-m-d H:i:s'));
	//print_r($mailmaga_list);
	//echo date('Y-m-d H:i:s');
	
	//配信予約するメルマガリスト数だけループ処理
	foreach($mailmaga_list as $mailmaga_post){
		//キュー生成の重複チェック
		if($queueObj->checkMailmagaQueueForPostID($mailmaga_post["ID"])){
			
			//配信先リストの生成
			if($mailmaga_post["POST_SPECIFY"]==='group'){
				//グループIDから配信先リストの取得（全配信も取得可）
				$send_list = $cliantObj->getCliantSendListForGroupID($mailmaga_post["POST_GROUP"]);
			}else{
				$send_list = array();
				//個別配信先IDの数だけループ
				$post_cliant_array = explode(',', $mailmaga_post["POST_CLIANT"]);
				foreach($post_cliant_array as $post_cliant_id){
					if(!empty($post_cliant_id)){	//空のIDの回避
						//顧客IDから配信先情報の取得（情報取得時のみ配信先リストの追加）
						$cliant_data = $cliantObj->getCliantSendDataForCliantID($post_cliant_id);
						if(!empty($cliant_data)){
							$send_list[] = $cliant_data;
						}
					}
				}
			}
			
			//送信先数だけループ処理
			$ERR = false;
			$send_num =0;
			foreach($send_list as $cliant_data){
				//メールキャリアの設定・アドレスチェック（PC or MB）
				$post_carrer = checkCarrerForMail($cliant_data["MASTER_MAIL"]);
				//メールアドレスチェックがOKなら
				if($post_carrer){
					//テキストメールの場合
					if($mailmaga_post["POST_MODE"]==='TEXT'){
						//メールキャリアによるテキスト本文設定
						$text_body = $mailmaga_post[$post_carrer."_TEXT_BODY"];
						//独自タグの変換
						$post_body["TEXT"] = cnvOriginalTag($text_body, $cliant_data);
					}
					//HTMLメールの場合	←PCとモバイルの切り替え必要？？
					else{
						//HTMLメール本文の設定
						$html_body = $mailmaga_post["PC_HTML_BODY"];
						//代替テキスト本文設定
						$text_body = $mailmaga_post["PC_TEXT_BODY"];
						//独自タグの変換
						$post_body["HTML"] = cnvOriginalTag($html_body, $cliant_data);
						$post_body["TEXT"] = cnvOriginalTag($text_body, $cliant_data);
					}
							
					//キューの生成処理(エラー時にSQL文が返り値)
					$ERR = $queueObj->insertMailmagaQueue($cliant_data, $mailmaga_post, $post_body);
					//キュー生成エラーの処理
					if($ERR){
						/*** ここにエラーSQLを72WEB宛に送信する処理 ***/
						$err_subject = '【西ケミ案件管理】キュー生成でインサートエラーが発生しました。';
						errorMail(BCC_MAIL, $err_subject, $ERR);
					}else{
						//生成したキュー数のカウント
						$send_num++;
					}
				}
			}
			//配信予約済へステータス変更
			$res = $mailmagaObj->changeMailmagaPostForID($mailmaga_post["ID"], 'reserved');
			//配信人数の保存
			$mailmagaObj->updateMailmagaPostSumForID($mailmaga_post["ID"], $send_num);
			
		}else{
			/*** ここに重複発生を72WEB宛に送信する処理 ***/
			$err_subject = '【西ケミ案件管理】キュー生成で重複が発生しました。';
			$err_body = '重複が発生したメルマガ予約は、「メルマガID：'.$mailmaga_post["ID"].'　件名：'.$mailmaga_post["POST_TITLE"].'」';
			errorMail(BCC_MAIL, $err_subject, $err_body);
		}
	}
	
		
	///--------------------------------------------------------------------
	/// 携帯メールアドレスのチェック
	///
	///	#Author yk
	/// #date	2013/11/13
	///--------------------------------------------------------------------
	function checkCarrerForMail($mail_address){
		//メールアドレスの正規表現チェック
		if(!preg_match('/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/iD', $mail_address)){
			return false;
		}
		//携帯メールドメインの取得
		global $MOBILE_DOMAIN;
		//メールアドレスの正規表現比較
		foreach($MOBILE_DOMAIN as $domain){
			if(preg_match('/(@|\.)'.preg_quote($domain).'$/', $mail_address)){
				return 'MB';
			}
		}
		return 'PC';
	}
	
		
	///--------------------------------------------------------------------
	/// 独自タグの変換
	///
	///	#Author yk
	/// #date	2013/11/13
	///--------------------------------------------------------------------
	function cnvOriginalTag($mailbody, $cliant_data){
		/*** 独自タグ対応DBによる変換に修正必要 ***/
		$mailbody = preg_replace('/'.preg_quote('<nw:name></nw:name>', '/').'/', $cliant_data["MASTER_NAME"], $mailbody);
		$mailbody = preg_replace('/'.preg_quote('<nw:post></nw:post>', '/').'/', $cliant_data["MASTER_POST"], $mailbody);
		$mailbody = preg_replace('/'.preg_quote('<nw:company></nw:company>', '/').'/', $cliant_data["MASTER_COMPANY"], $mailbody);
		
		return $mailbody;
	}
	
		
	///--------------------------------------------------------------------
	/// エラーメール送信
	///
	///	#Author yk
	/// #date	2013/11/14
	///--------------------------------------------------------------------
	function errorMail($mail_address, $subject, $body){
		//言語設定及び内部エンコーディング
		mb_language("japanese");
		mb_internal_encoding("UTF-8");
		mb_send_mail($mail_address, $subject, $body, "From:".MAIL_INFO);
	}
	
	
?>
