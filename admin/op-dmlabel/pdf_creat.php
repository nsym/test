<?php
	require_once("../common/config.php");
	require_once("../db/op-dmlabel/dmlabel.php");
	require_once("../db/op-dmlabel/cliant.php");
	require_once("./pdf_config.php");
	$mydb = db_con();
	
	
	//�N���X�̃C���X�^���X��
	$dmLabelObj = new DMlabel();
	$cliantObj = new Cliant();
	
	//DM���x�����̎擾
	$dmlabel_data = $dmLabelObj->getDMlabelMasterForID($_GET['id']);
	
	//�ڋq���X�g�̎擾
	$cliant_list = $cliantObj->getCliantDMlistForGroupID($dmlabel_data["GROUP_ID"], $_GET['action']);
	$count = $cliantObj->countCliantDMListForGroupID($dmlabel_data["GROUP_ID"]);
	
	
	//���C�u�����ނ̎Q��
	$sep = RUTE_PATH;
	set_include_path( ".{$sep}fpdf16" );
	//SJIS �ւ̕ϊ���( ���̃t�@�C���� SHIFT_JIS )
	mb_language( "ja" );
	mb_internal_encoding("SJIS");
	
	$output_mode = $_GET['action'];
	
	//�o�c�e�t�@�C����
	$pdf_name = $dmlabel_data['LABEL_TITLE'].'.pdf';
	$iid = 1;
	//�����ݒ�
	//$size = array(297,210);
	$pdf_size_mm = array(297,210);
	$pdf_vec = 'L';
	
	$pdf = new PDF_lightbox($pdf_vec, 'mm', $pdf_size_mm);
	
	//�������y�[�W���n�e�e�ɁB
	$pdf->SetAutoPageBreak("on") ;
	
	// ��_�̒�`
	$pdf->SetBase(0, 0);
	
	//���{���( �f�t�H���g�� MS-Mincho )
	$pdf->AddSJISFont( );
	$pdf->AddSJISFont("MS-Gothic", "SJIS-2");
	$pdf->newUserPage($iid, $pdf_vec, $cliant_list, $dmlabel_data, $output_mode);
	//PDF �� �u���E�U�ɏo��
	$pdf->Output($pdf_name,"D");
	
?>
