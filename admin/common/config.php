<?php
	///--------------------------------------------------------------------
	/// 共通パッケージ	－コア・ロジック－
	///--------------------------------------------------------------------
		
	ini_set("display_errors", 1 );

	///--------------------------------------------------------------------
	/// 初期設定
	///
	///
	///	#Author yk
	/// #date	2013/10/22
	///--------------------------------------------------------------------
	define("AUTHER", "72web.co.jp");
	define("HP_URL", "http://test.nsym-chemix.com/");
	define("RUTE_PATH", "/home/kir016651/public_html/nsym/test.nsym-chemix.com");
	define("MAILMAGA_MAIL_NAME", "【開発環境】（株）西山ケミックス　メールマガジン");
	define("MAILMAGA_MAIL", "noreply@nsym-chemix.com");
	define("HISTORY_MAIL", "history@nsym-chemix.com");
	define("BCC_MAIL", "yokoe@side7.co.jp");
	
	define("ADMIN_TITLE", "【開発環境】西山ケミックス｜グループウェア");
	define("COPY_RIGHT", "2013 &copy; 72web.co.,ltd All Rights Reserved.");

	///--------------------------------------------------------------------
	/// データベース設定
	///
	///
	///	#Author yk
	/// #date	2013/10/22
	///--------------------------------------------------------------------
	define("DB_HOST", "TWpFd0xqRXpOQzQwT0M0MU9RPT0");
	define("DB_NAME", "Ym5ONWJWOTBaWE4w");
	define("DB_USER", "YTJseU1ERTJOalV4");
	define("DB_PASS", "YldsdVlXTXdNR3c9");
	
	
	///--------------------------------------------------------------------
	/// クラスの読み込み
	///
	///
	///	#Author yk
	/// #date	2013/10/22
	///	#Author yk
	/// #date	2013/12/19
	///--------------------------------------------------------------------
	require_once( RUTE_PATH."/admin/common/convert.php");
	require_once( RUTE_PATH."/admin/common/db.php");
	require_once( RUTE_PATH."/admin/common/auth.php");
	require_once( RUTE_PATH."/admin/common/image.php");
	require_once( RUTE_PATH."/admin/common/ReceiptMailDecoder.php");
	require_once( RUTE_PATH."/admin/common/view.php");
	
	
	///--------------------------------------------------------------------
	/// 保存フォルダ設定
	///
	///
	///	#Author yk
	/// #date	2013/10/22
	///	#Author yk
	/// #date	2013/12/11
	///--------------------------------------------------------------------
	define("TEMP_FOLDER", "/img_temp/");
	define("MAIN_FOLDER", "/img_main/");
	define("THUM_FOLDER", "/img_thum/");
	define("FILE_FOLDER", "/file_save/");
	define("TEMP_DIRECTORY", RUTE_PATH.TEMP_FOLDER);
	define("MAIN_DIRECTORY", RUTE_PATH.MAIN_FOLDER);
	define("THUM_DIRECTORY", RUTE_PATH.THUM_FOLDER);
	define("FILE_DIRECTORY", RUTE_PATH.FILE_FOLDER);
	
	
	///--------------------------------------------------------------------
	/// オペレーション設定
	///
	///
	///	#Author yk
	/// #date	2013/10/22
	///	#Author yk
	/// #date	2013/12/18
	///--------------------------------------------------------------------
	$op_list = array(
		'op-home' => array('OP_FOLDER'=>'/', 'OP_ICON'=>'home', 'OP_NAME'=>'HOME', 'OP_AUTHORITY'=>'72web,staff'),
		'op-cliant' => array('OP_FOLDER'=>'/op-cliant/', 'OP_ICON'=>'user', 'OP_NAME'=>'顧客管理', 'OP_AUTHORITY'=>'72web,administrator,staff'),
		'op-mailmaga' => array('OP_FOLDER'=>'/op-mailmaga/', 'OP_ICON'=>'envelope', 'OP_NAME'=>'メルマガ管理', 'OP_AUTHORITY'=>'72web,administrator,staff'),
		'op-dmlabel' => array('OP_FOLDER'=>'/op-dmlabel/', 'OP_ICON'=>'th-large', 'OP_NAME'=>'DMラベル管理', 'OP_AUTHORITY'=>'72web,administrator,staff'),
		'op-estimate' => array('OP_FOLDER'=>'/op-estimate/', 'OP_ICON'=>'jpy', 'OP_NAME'=>'見積管理', 'OP_AUTHORITY'=>'72web,administrator,staff'),
		'op-accept' => array('OP_FOLDER'=>'/op-accept/', 'OP_ICON'=>'signin', 'OP_NAME'=>'受注管理', 'OP_AUTHORITY'=>'72web,administrator,staff'),
		'op-user' => array('OP_FOLDER'=>'/op-user/', 'OP_ICON'=>'cog', 'OP_NAME'=>'スタッフ管理', 'OP_AUTHORITY'=>'72web,administrator'),
	);
	
	
	///--------------------------------------------------------------------
	/// ユーザー権限設定
	///
	///	#Author yk
	/// #date	2013/10/22
	///--------------------------------------------------------------------
	$authority_list = array(
		'72web' => array('AUTHORITY_NAME'=>'製作者'),
		'administrator' => array('AUTHORITY_NAME'=>'管理者'),
		'staff' => array('AUTHORITY_NAME'=>'スタッフ'),
	);
	
	
	///--------------------------------------------------------------------
	/// 五十音設定
	///
	///	#Author yk
	/// #date	2013/11/04
	///--------------------------------------------------------------------
	$kana_list = array(
		'A' => array('あ', 'い', 'う', 'え', 'お'),
		'KA' => array('か', 'き', 'く', 'け', 'こ', 'が', 'ぎ', 'ぐ', 'げ', 'ご'),
		'SA' => array('さ', 'し', 'す', 'せ', 'そ', 'ざ', 'じ', 'ず', 'ぜ', 'ぞ'),
		'TA' => array('た', 'ち', 'つ', 'て', 'と', 'だ', 'ぢ', 'づ', 'で', 'ど'),
		'NA' => array('な', 'に', 'ぬ', 'ね', 'の'),
		'HA' => array('は', 'ひ', 'ふ', 'へ', 'ほ', 'ば', 'び', 'ぶ', 'べ', 'ぼ'),
		'MA' => array('ま', 'み', 'む', 'め', 'も'),
		'YA' => array('や', 'ゆ', 'よ'),
		'RA' => array('ら', 'り', 'る', 'れ', 'ろ'),
		'WA' => array('わ', 'を', 'ん'),
	);
	
	
	///--------------------------------------------------------------------
	/// メルマガ配信フラグ設定
	///
	///	#Author yk
	/// #date	2013/11/04
	///--------------------------------------------------------------------
	$mailmaga_flag_list = array(
		'send' => '配信する',
		'unsend' => '配信しない'
	);
	
	
	///--------------------------------------------------------------------
	/// イプロス会員フラグ設定
	///
	///	#Author yk
	/// #date	2013/11/06
	///--------------------------------------------------------------------
	$ipros_flag_list = array(
		'member' => '会員',
		'nonmember' => '非会員'
	);
	
	
	///--------------------------------------------------------------------
	/// 展示会フラグ設定
	///
	///	#Author mukai
	/// #date	2013/11/20
	///	#Author yk
	/// #date	2013/11/21
	///--------------------------------------------------------------------
	$exhibition_flag_list = array(
		'entry' => '展示会来場',
		'noentry' => '来場なし'
	);
	
	
	///--------------------------------------------------------------------
	/// 京都試作ネットフラグ設定
	///
	///	#Author mukai
	/// #date	2013/11/20
	///--------------------------------------------------------------------
	$kyoto_flag_list = array(
		'member' => '京都試作ネット',
		'nonmember' => '京都試作ネット以外'
	);


	///--------------------------------------------------------------------
	/// エリア設定
	///
	///	#Author mukai
	/// #date	2013/11/02
	///	#Author yk
	/// #date	2013/11/05
	///--------------------------------------------------------------------
	$area_list = array(
		'北海道・東北' => array('北海道', '青森県', '秋田県', '岩手県', '山形県', '宮城県', '福島県'),
		'甲信越・北陸' => array('山梨県', '長野県', '新潟県', '富山県', '石川県', '福井県'),
		'関東' => array('茨城県', '栃木県', '群馬県', '埼玉県', '千葉県', '東京都', '神奈川県'),
		'東海' => array('愛知県', '静岡県', '岐阜県', '三重県'),
		'関西' => array('大阪府', '兵庫県', '京都府', '滋賀県', '奈良県', '和歌山県'),
		'中国' => array('岡山県', '広島県', '鳥取県', '島根県', '山口県'),
		'四国' => array('徳島県', '香川県', '愛媛県', '高知県'),
		'九州・沖縄' => array('福岡県', '佐賀県', '長崎県', '熊本県', '大分県', '宮崎県', '鹿児島県', '沖縄県')
	);

	///--------------------------------------------------------------------
	/// グループカラー設定
	///
	///	#Author mukai
	/// #date	2013/11/06
	///--------------------------------------------------------------------
	$color_list = array(
		'white', 'sandybrown', 'seagreen', 'slategray', 'springgreen', 'steelblue', 'tan', 'teal', 'thistle', 'tomato', 'turquoise', 'violet', 'wheat', 'whitesmoke', 'yellow', 'yellowgreen'
	);
	
	///--------------------------------------------------------------------
	/// ランク設定
	///
	///	#Author mukai
	/// #date	2013/11/20
	///	#Author yk
	/// #date	2013/11/21
	///--------------------------------------------------------------------
	$rank_list = array(
		'a_rank' => 'Ａ',
		'b_rank' => 'Ｂ',
		'soku_rank' => '即'
	);
	
	///--------------------------------------------------------------------
	/// 対応履歴日カテゴリ設定
	///
	///	#Author yk
	/// #date	2013/12/04
	///	#Author yk
	/// #date	2013/12/18
	///--------------------------------------------------------------------
	$history_category_list = array(
		'mail' => 'メール送信',
		'tel' => '電話',
		'visit' => '訪問・お打ち合わせ',
		'exhibition' => '展示会',
		'sample' => '試作・サンプル',
		'request' => '見積り依頼',
		'estimate' => 'お見積り',
		'accept' => '受注',
		'except' => 'その他',
	);
	
	///--------------------------------------------------------------------
	/// CSVフィールド設定
	///
	///	#Author mukai
	/// #date	2013/12/04
	///--------------------------------------------------------------------
	$filed_list = array(
		"顧客ID",
		"主担当スタッフ",
		"顧客ランク",
		"顧客名",
		"顧客名（ふりがな）",
		"企業・団体",
		"ホームページURL",
		"事業所",
		"部署",
		"役職",
		"業種",
		"職種",
		"電話番号",
		"FAX番号",
		"メールアドレス",
		"エリア",
		"郵便番号",
		"住所",
		"メルマガ配信設定",
		"イプロス会員フラグ",
		"展示会フラグ",
		"京都試作ネットフラグ",
		"備考",
		"\n"
	);
	
	///--------------------------------------------------------------------
	/// 見積ステータス設定
	///
	///	#Author yk
	/// #date	2013/12/11
	///	#Author yk
	/// #date	2014/01/07
	///--------------------------------------------------------------------
	$estimate_status_list = array(
		'wait' => '提出中',
		'consider' => '検討中',
		'revision' => '再見積',
		'dismissed' => '失注'
	);

?>