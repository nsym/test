<?php
require('japanese.php');

class PDF_lightbox extends PDF_japanese
{
	public $bx = 0;
	public $by = 0;

# **********************************************************
# �V�K�y�[�W
# **********************************************************
function newUserPage($Title,$Option="") {

	// �V�����y�[�W��ǉ�
	// P : �|�[�g���C�g( �c )
	// �w�i�F �Z�b�g
	$this->AddPage('P');
	$this->SetFillColor( 255, 255, 255 );

	// ��
	$this->SetFont( 'SJIS', '', 12 );
	$this->LocText( 150, -20, '��             ��' );

	// �^�C�g����
	$this->SetFont( 'SJIS', 'B', 20 );
	$this->LocText( 65, 0, $Title );

	// �l���
	$x = 40;
	$y = 23;

	// ����
	$this->SetFont( 'SJIS', '', 16 );
	$this->LocText( 40, 23, "�R�c ���Y" );

	$x = 40;
	$y = 60;

	$this->SetFont( 'SJIS-2', 'B', 18 );
	$Text = '��L�̎҂́A�ԈႢ����';
	$this->LocText( $x, $y, $Text );

	$this->SetFont( 'SJIS-2', 'I', 18 );
	$Text = '��v���O���}�ł��鎖��';
	$this->LocText( $x, $y+20, $Text );

	$this->SetFont( 'SJIS-2', 'B', 18 );
	$Text = '�ؖ��v���܂��B';
	$this->LocText( $x, $y+40, $Text );


	$dt = explode( "/", date("m/d/Y") );
	
	// ����
	$this->SetFont( 'SJIS', 'B', 13 );
	$dt[2] = ($dt[2]+0) - 1988;
	$this->LocText( $x+7, $y+60, "����" );

	$this->LocText( $x+20, $y+60, sprintf( "%d�N", $dt[2]+0 ), 'I', 12 );
	$this->LocText( $x+37, $y+60, sprintf( "%d��", $dt[0]+0 ) );
	$this->LocText( $x+50, $y+60, sprintf( "%d��", $dt[1]+0 ) );

	$this->SetFont( 'SJIS', 'B', 13 );
	$this->LocText( $x+40, $y+95, "�ˋ�@�l�@�V�X�e���Ɛ����" );
	$this->LocText( $x+40, $y+105, "SQL�̑��w�@" );
	$this->LocText( $x+68, $y+115, "�Z�@��" );

	$this->LocText( $x+94, $y+115, "lightbox" );

}

# **********************************************************
# ���W�ݒ��
# **********************************************************
function LocText( $x, $y, $str, $style='NONE', $size=14 ) {

	if ( $style != 'NONE' ) {
		$this->SetFont('SJIS',$style,$size);
	}
	$this->Text( $this->bx + $x, $this->by + $y, $str );

}

# **********************************************************
# �󎚊�_���W�ݒ�
# **********************************************************
function SetBase( $x=0, $y=0 ) {

	$this->bx = $x;
	$this->by = $y;

}

}
?>
