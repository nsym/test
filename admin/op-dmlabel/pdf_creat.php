<?php
	require_once("../common/config.php");
	require_once("../db/op-dmlabel/dmlabel.php");
	require_once("../db/op-dmlabel/cliant.php");
	require_once("./pdf_config.php");
	$mydb = db_con();
	
	
	//クラスのインスタンス化
	$dmLabelObj = new DMlabel();
	$cliantObj = new Cliant();
	
	//DMラベル情報の取得
	$dmlabel_data = $dmLabelObj->getDMlabelMasterForID($_GET['id']);
	
	//顧客リストの取得
	$cliant_list = $cliantObj->getCliantDMlistForGroupID($dmlabel_data["GROUP_ID"], $_GET['action']);
	$count = $cliantObj->countCliantDMListForGroupID($dmlabel_data["GROUP_ID"]);
	
	
	//ライブラリ類の参照
	$sep = RUTE_PATH;
	set_include_path( ".{$sep}fpdf16" );
	//SJIS への変換環境( このファイルは SHIFT_JIS )
	mb_language( "ja" );
	mb_internal_encoding("SJIS");
	
	$output_mode = $_GET['action'];
	
	//ＰＤＦファイル名
	$pdf_name = $dmlabel_data['LABEL_TITLE'].'.pdf';
	$iid = 1;
	//初期設定
	//$size = array(297,210);
	$pdf_size_mm = array(297,210);
	$pdf_vec = 'L';
	
	$pdf = new PDF_lightbox($pdf_vec, 'mm', $pdf_size_mm);
	
	//自動改ページをＯＦＦに。
	$pdf->SetAutoPageBreak("on") ;
	
	// 基点の定義
	$pdf->SetBase(0, 0);
	
	//日本語環境( デフォルトの MS-Mincho )
	$pdf->AddSJISFont( );
	$pdf->AddSJISFont("MS-Gothic", "SJIS-2");
	$pdf->newUserPage($iid, $pdf_vec, $cliant_list, $dmlabel_data, $output_mode);
	//PDF を ブラウザに出力
	$pdf->Output($pdf_name,"D");
	
?>
