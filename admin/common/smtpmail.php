<?php

	///--------------------------------------------------------------------
	/// 共通パッケージ	－メルマガ関係－
	///--------------------------------------------------------------------

	class SmtpMail {
		
		public $mailDatas = array();
		public $lastError = '';		// 最終エラーメッセージ
		
		// コンストラクタ
		public function __construct() {
			global $info_mail_address;
			global $client_name;
			$this->mailDatas['from'] = MAIL_INFO;		// 送信者メアド
			$this->mailDatas['fromName'] = $client_name;			// 送信者名
			$this->mailDatas['toName'] = '';						// 送信先名
			$this->mailDatas['reply-to'] = MAIL_INFO;	// 返信先メアド
			$this->mailDatas['return-path'] = MAIL_INFO;	// エラーメール返信先メアド
		}
		
		// メイン処理
		public function process($self, $mail_mode) {
			$mailDatas = array_merge($this->mailDatas, $self );
			//print_r($mailDatas);
			// 送信
			$result = $this->sendMail($mailDatas, $mail_mode);
			if (!$result) {
				return false;
			} else {
				return true;
			}
	
		}
		
		// メール送信
		private function sendMail($mailDatas, $mail_mode) {
			switch($mail_mode){
				case 'IP':
					$original = mb_internal_encoding();
					mb_internal_encoding("SJIS");
					$headers  = "MIME-Version: 1.0 \n";
					$headers .= "From: ".mb_encode_mimeheader (mb_convert_encoding($mailDatas['send_name'],"SJIS","AUTO")).""."<".$mailDatas['from']."> \n";
					mb_internal_encoding("UTF-8");
					$headers .= 'Content-Type: text/plain;charset="Shift-JIS"'."\n";
					$headers .= 'Content-Transfer-Encoding: base64'."\n";
					mb_language("ja");
					$subject = mb_convert_encoding($mailDatas['subject'], "SJIS","UTF-8");
					$subject = $this->cnv_emoji_ver2($subject, "title", $mail_mode);
					$subject = "=?shift_jis?B?".base64_encode($subject)."?=";
					$body = mb_convert_encoding($mailDatas['body'], "SJIS","UTF-8");
					//$body = $mailDatas['body'];
					$body = mb_convert_encoding($body, "SJIS","AUTO");
					$body = $this->cnv_emoji_ver2($body, "coment", $mail_mode); 
					$body = chunk_split(base64_encode($body));
					$sendmail_params  = "";
					mb_internal_encoding($original);
				break;
				default:
					$original = mb_internal_encoding();
					mb_internal_encoding("SJIS");
					$headers  = "MIME-Version: 1.0 \n" ;
					$headers .= "From: ".mb_encode_mimeheader (mb_convert_encoding($mailDatas['send_name'],"SJIS","UTF-8")).""."<".$mailDatas['from']."> \n";
					$headers .= 'Content-Type: text/plain;charset="Shift-JIS"'."\n";
					$headers .= 'Content-Transfer-Encoding: base64'."\n";
					$body = mb_convert_encoding($mailDatas['body'], "SJIS","UTF-8");
					$body = $this->cnv_emoji_ver2($body, "coment", $mail_mode); 
					$body = chunk_split(base64_encode($body));
					$sendmail_params  = "";
					mb_language("ja");
					$subject = mb_convert_encoding($mailDatas['subject'], "SJIS","UTF-8");
					$subject = $this->cnv_emoji_ver2($subject, "title", $mail_mode);
					$subject = "=?shift_jis?B?".base64_encode($subject)."?=";
					mb_internal_encoding($original);
				break;
			}
			$result = mail($mailDatas['to'], $subject, $body, $headers, $sendmail_params);
			//$result = mail('taiking@72web.co.jp', $subject, $body, $headers, $sendmail_params);
			return $result;
		}
		
		function cnv_emoji_ver2($tar, $mode, $career){
			global $emoji;
			for($i=0;$i<count($emoji);$i++){
				if(strstr($tar, $emoji[$i]["TAG"])) {
					$e_tag = pack("H*", $emoji[$i][$career."_MAIL"]);
					$tar = str_replace($emoji[$i]["TAG"], $e_tag, $tar);
				}
			}
			return $tar;
		}
	
	}
?>