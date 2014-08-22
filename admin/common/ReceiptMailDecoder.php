<?php
	/* /////////////////////////////////////////////////////
	//		ADMIN TOOL メール解析パッケージ
	//		－Mail_mimeDecodeラップクラス－
	//////////////////////////////////////////////////////*/
	
	////////////////////////////////////////////////////////
	//
	//	#substance	パッケージの作成
	//  #Date		2008/03/11
	//	#Author 	ya--mada(http://d.hatena.ne.jp/ya--mada/20080415/1208318475)
	//
	////////////////////////////////////////////////////////
	//
	//	#substance	パッケージの再構築
	//  #Date		2013/07/02
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	
//--------------------------------------------------------------------
require_once('Mail/mimeDecode.php');
	
	class ReceiptMailDecoder {
		
		//初期化
		var $body = array( 'text'=> null , 'html'=> null );		//本文
		var $attachments = array();		//添付ファイル
		var $_decoder;		//Mail_mimeDecode オブジェクト

		///--------------------------------------------------------------------
		/// 【コンストラクター】
		///
		/// 戻り値　なし
		///
		///	#Author yk
		/// #date	2013/07/02
		///--------------------------------------------------------------------
		function __construct(&$raw_mail){
			//メールがNULLでなければ解析を行う
			if(!is_null($raw_mail)){
				$this->_decode($raw_mail);
			}
		}
		
		///--------------------------------------------------------------------
		///  Mail_mime::Decodeでメール解析
		///
		///	#Author yk
		/// #date	2013/07/02
		///--------------------------------------------------------------------
		function _decode(&$raw_mail){
			//メールがNULLならエラー
			if(is_null($raw_mail)){
				return false;
			}
			//Mail_mime::Decodeのパラメータ設定
			$params = array();
			$params['include_bodies'] = true;
			$params['decode_bodies']  = true;
			$params['decode_headers']  = true;
			
			//Mail_mime::Decodeでメール解析
			$this->_decoder = new Mail_mimeDecode( $raw_mail."\n" );
			$this->_decoder = $this->_decoder->decode($params);
			//マルチパートの場合は、本文と添付に分ける			
			$this->_decodeMultiPart($this->_decoder);
		}
		
		///--------------------------------------------------------------------
		///  指定ヘッダの取得
		///
		///	#Author yk
		/// #date	2013/07/02
		///--------------------------------------------------------------------
		function getRawHeader($header_name){
			//指定ヘッダがセットされていれば返す、なければNULL
			return isset($this->_decoder->headers["$header_name"])? $this->_decoder->headers["$header_name"]: null;
		}
		
		///--------------------------------------------------------------------
		///  ヘッダの解析取得（内部文字エンコーディング）
		///
		///	#Author yk
		/// #date	2013/07/02
		///--------------------------------------------------------------------
		function getDecodedHeader($header_name){
			//内部文字エンコーディングへエンコードして返す
			return mb_convert_encoding(mb_decode_mimeheader($this->getRawHeader($header_name)), mb_internal_encoding(), 'auto');
			//return mb_decode_mimeheader($this->getRawHeader( $header_name ));
		}
		
		
		///--------------------------------------------------------------------
		///  指定ヘッダ内のE-mailアドレスだけを抜き出して返す
		///
		///	#Author yk
		/// #date	2013/07/02
		///--------------------------------------------------------------------
		function getHeaderAddresses ( $header_name ) {
			return $this->extractionEmails($this->getRawHeader( $header_name ));
		}
		
		
		///--------------------------------------------------------------------
		///  文字列の中からemailアドレス抽出（複数あれば,(カンマ)区切り）
		///
		///	#Author yk
		/// #date	2013/07/02
		///--------------------------------------------------------------------
		function extractionEmails( $raw_string ) {
		
			//旧emailアドレスっぽい正規表現
			//$email_regex_pattern = "/[\x01-\x7F]+@(([-a-z0-9]+\.)*[a-z]+|\[\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\])/";
			
			//新emailアドレスっぽい正規表現
			$email_regex_pattern = '/(?:[^(\040)<>@,;:".\\\\\[\]\000-\037\x80-\xff]+(?![^(\040)<>@,;:".\\\\\[\]\000-\037\x80-\xff])|"[^\\\\\x80-\xff\n\015"]*(?:\\\\[^\x80-\xff][^\\\\\x80-\xff\n\015"]*)*")(?:\.(?:[^(\040)<>@,;:".\\\\\[\]\000-\037\x80-\xff]+(?![^(\040)<>@,;:".\\\\\[\]\000-\037\x80-\xff])|"[^\\\\\x80-\xff\n\015"]*(?:\\\\[^\x80-\xff][^\\\\\x80-\xff\n\015"]*)*"))*@(?:[^(\040)<>@,;:".\\\\\[\]\000-\037\x80-\xff]+(?![^(\040)<>@,;:".\\\\\[\]\000-\037\x80-\xff])|\[(?:[^\\\\\x80-\xff\n\015\[\]]|\\\\[^\x80-\xff])*\])(?:\.(?:[^(\040)<>@,;:".\\\\\[\]\000-\037\x80-\xff]+(?![^(\040)<>@,;:".\\\\\[\]\000-\037\x80-\xff])|\[(?:[^\\\\\x80-\xff\n\015\[\]]|\\\\[^\x80-\xff])*\]))*/';
			
			if(preg_match_all($email_regex_pattern, $raw_string, $matches, PREG_PATTERN_ORDER)){
				if(isset($matches[0])){
					return implode(",", $matches[0]);
				}
			}
			//抽出出来なければNULLを返す
			return null;
		}
		
		
		///--------------------------------------------------------------------
		///  本文と添付ファイルの抽出
		///
		/// 戻り値	$this->body['text']：テキスト形式の本文
		///			$this->body['html']：html形式の本文
		///			$this->attachments[$i]['mime_type']：MimeType
		///			$this->attachments[$i]['file_name']：ファイル名
		///			$this->attachments[$i]['binary']：ファイル本体
		///
		///	#Author yk
		/// #date	2013/07/02
		///--------------------------------------------------------------------
		function _decodeMultiPart(&$decoder){
		
			// マルチパートの場合 それぞれがparts配列内に再配置されているので
			// 再帰的に処理をする。
			if(!empty($decoder->parts)){
				foreach( $decoder->parts as $part){
					$this->_decodeMultiPart($part);
				}
			}
			else{
				//本文と添付ファイルが空でなければ
				if (!empty($decoder->body)){
					//シングルパートの場合
					if ('text' === strToLower($decoder->ctype_primary)){
						if('plain' === strToLower($decoder->ctype_secondary)){
							$this->body['text'] = $decoder->body;
							//$this->body['text'] = mb_convert_encoding($decoder->body, mb_internal_encoding(), 'auto');
						}
						else if('html' === strToLower($decoder->ctype_secondary)){
							$this->body['html'] = $decoder->body;
						}
						// その他のtext系マルチパート
						else{
							$this->attachments[] = array(
								'mime_type'=>$decoder->ctype_primary.'/'.$decoder->ctype_secondary,
								'file_name'=>$decoder->ctype_parameters['name'],
								'binary'=>&$decoder->body
							);
						}
					}
					//その他の場合
					else{
						$this->attachments[] = array(
							'mime_type'=>$decoder->ctype_primary.'/'.$decoder->ctype_secondary,
							'file_name'=>$decoder->ctype_parameters['name'],
							'binary'=>&$decoder->body
						);
					}
				}
			}
		}
		
		///--------------------------------------------------------------------
		///  メールが添付ファイルつきか調べる
		///
		///	#Author yk
		/// #date	2013/07/02
		///--------------------------------------------------------------------
		function isMultiPart() {
			//添付ファイルがあればTRUEを返す
			return (count($this->attachments)>0)? true: false;
		}
		
		
		///--------------------------------------------------------------------
		///  添付ファイルの数を数える
		///
		///	#Author yk
		/// #date	2013/07/02
		///--------------------------------------------------------------------
		function getNumOfAttach() {
			return count($this->attachments);
		}
		
		
		///--------------------------------------------------------------------
		///  Toヘッダからアドレスのみを取得する
		///
		///	#Author yk
		/// #date	2013/07/02
		///--------------------------------------------------------------------
		function getToAddr(){
			return $this->getHeaderAddresses('to');
		}
		
		///--------------------------------------------------------------------
		///  Fromヘッダからアドレスのみを取得する
		///
		///	#Author yk
		/// #date	2013/07/02
		///--------------------------------------------------------------------
		function getFromAddr(){
			return $this->getHeaderAddresses('from');
		}
		
		///--------------------------------------------------------------------
		///  Senderヘッダからアドレスのみを取得する
		///
		///	#Author yk
		/// #date	2013/07/02
		///--------------------------------------------------------------------
		function getSenderAddr(){
			return $this->getHeaderAddresses('sender');
		}
		
		///--------------------------------------------------------------------
		///  添付ファイルの保存処理（BASE64）
		///
		///	#Author yk
		/// #date	2013/07/02
		///--------------------------------------------------------------------
		function saveAttachFile($index, $str_path){
			/*
			//ディレクトリが存在し、書き込み可能の場合
			if(file_exists($str_path) && is_writable(dirname($str_path))){
				//添付ファイルが存在しなければ、FALSE
				if(!isset($this->attachments[$index])){
					return false;
				}
				//添付ファイルの保存処理
				if($fp = fopen($str_path, "wb")){
					fwrite($fp, $this->attachments[$index]['binary'] );
					fclose($fp);
					exec("chmod 0777 ".$str_path);
					return true;
				}
			}
			*/
			//添付ファイルの保存処理
			if($fp = fopen($str_path, "w")){
				$length = strlen($this->attachments[$index]['binary']);
				fwrite($fp, $this->attachments[$index]['binary'], $length);
				fclose($fp);
				exec("chmod 0777 ".$str_path);
				return true;
			}
			
			return false;
		}
	}
?>