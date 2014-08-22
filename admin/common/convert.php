<?php
	///--------------------------------------------------------------------
	/// 共通パッケージ	ー変換関係ー
	///--------------------------------------------------------------------
	
	///--------------------------------------------------------------------
	/// ベース６４エンコード
	///
	///	#param	String	$tar	文字列
	///
	///	#Author NATSU
	/// #date	2009/06/24
	///--------------------------------------------------------------------
	function b6e($tar){
		$str = "";
		$str = base64_encode($tar);
		return $str;
	}
	
	///--------------------------------------------------------------------
	/// ベース６４デコード
	///
	///	#param	String	$tar	文字列
	///
	///	#Author NATSU
	/// #date	2009/06/24
	///--------------------------------------------------------------------
	function b6d($tar){
		$str = "";
		$str = base64_decode($tar);
		return $str;
	}
	
	///--------------------------------------------------------------------
	/// 文字コード変換　ＥＵＣ→ＳｈｉｆｔＪＩＳ
	///
	///	#param	String	$tar	文字列
	///
	///	#Author NATSU
	/// #date	2009/06/24
	///--------------------------------------------------------------------
	function cnv($tar){
		$tar = mb_convert_encoding($tar , "SJIS" , "EUC");
		return $tar;
	}
	
	///--------------------------------------------------------------------
	/// 文字コード変換　ＳｈｉｆｔＪＩＳ→ＥＵＣ
	///
	///	#param	String	$tar	文字列
	///
	///	#Author NATSU
	/// #date	2009/06/24
	///--------------------------------------------------------------------
	function cnv_e_s($tar){
		$tar = mb_convert_encoding($tar , "EUC" , "SJIS");
		return $tar;
	}
	
	///--------------------------------------------------------------------
	/// 文字コード変換　ＥＵＣ→ＵＴＦ－８
	///
	///	#param	String	$tar	文字列
	///
	///	#Author NATSU
	/// #date	2009/06/24
	///--------------------------------------------------------------------
	function cnv_u_e($tar){
		$tar = mb_convert_encoding($tar , "UTF-8" , "EUC");
		return $tar;
	}
	
	///--------------------------------------------------------------------
	/// ＨＴＭＬエンコード
	///
	///	#param	String	$tar	文字列
	///
	///	#Author NATSU
	/// #date	2009/06/24
	///--------------------------------------------------------------------
	function enc_html($tar){
		$restr = "";
		$restr = htmlentities( $tar , ENT_QUOTES);
		return $restr;
	}
	
	///--------------------------------------------------------------------
	/// ＨＴＭＬデコード
	///
	///	#param	String	$tar	文字列
	///
	///	#Author NATSU
	/// #date	2009/06/24
	///--------------------------------------------------------------------
	function dec_html($tar){
		$restr = "";
		$restr = html_entity_decode($tar);
		return $restr;
	}
	
	///--------------------------------------------------------------------
	/// ブラウザおよびキャリア振り分け
	///
	///	#param	String	$agent	エージェント情報
	///
	///	戻り値　String
	///
	///	#Author NATSU
	/// #date	2009/06/24
	///--------------------------------------------------------------------
	function cnv_browser($agent){
		if(preg_match("/DoCoMo/" , $agent )){
			$retxt = "IM";
		}elseif(preg_match("/UP.Browser/" , $agent)){
			$retxt = "EZ";
		}elseif(preg_match("/J-PHONE/" , $agent)){
			$retxt = "VO";
		}elseif(preg_match("/Vodafone/" , $agent)){
			$retxt = "VO";
		}elseif(preg_match("/SoftBank/" , $agent)){
			$retxt = "VO";
		}elseif(preg_match("/iPhone/" , $agent)){
			$retxt = "IP";
		}elseif(preg_match("/Android/" , $agent)){
			$retxt = "AR";
		}elseif(preg_match("/BlackBerry/" , $agent)){
			$retxt = "BB";
		}else{
			$retxt = "PC";
		}
		return $retxt;
	}
	
	///--------------------------------------------------------------------
	/// 曜日を日本語変換
	///
	///	#param	String	$youbi	曜日（アルファベット３文字）
	///
	///	戻り値　String
	///
	///	#Author NATSU
	/// #date	2010/01/18
	///--------------------------------------------------------------------
	function cnv_youbi_txt($youbi){
		$re = '';
		switch($youbi){
			case 'Sun':
				$re = '日';
			break;
			case 'Mon':
				$re = '月';
			break;
			case 'Tue':
				$re = '火';
			break;
			case 'Wed':
				$re = '水';
			break;
			case 'Thu':
				$re = '木';
			break;
			case 'Fri':
				$re = '金';
			break;
			case 'Sat':
				$re = '土';
			break;
		}
		return $re;
	}
	
	///--------------------------------------------------------------------
	/// 性別を日本語変換
	///
	///	#param	NUmber	$sex	性別番号
	///
	///	戻り値　String
	///
	///	#Author NATSU
	/// #date	2013/03/16
	///--------------------------------------------------------------------
	function cnv_sex_txt($sex){
		$re = '';
		switch($sex){
			case '0':
				$re = '不明';
			break;
			case '1':
				$re = '女性';
			break;
			case '2':
				$re = '男性';
			break;
		}
		return $re;
	}
			
	
?>