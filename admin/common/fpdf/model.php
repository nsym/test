<?php
require('japanese.php');

class PDF_lightbox extends PDF_japanese
{
	public $bx = 0;
	public $by = 0;

# **********************************************************
# 新規ページ
# **********************************************************
function newUserPage($Title,$Option="") {

	// 新しいページを追加
	// P : ポートレイト( 縦 )
	// 背景色 セット
	$this->AddPage('P');
	$this->SetFillColor( 255, 255, 255 );

	// 号
	$this->SetFont( 'SJIS', '', 12 );
	$this->LocText( 150, -20, '第             号' );

	// タイトル印字
	$this->SetFont( 'SJIS', 'B', 20 );
	$this->LocText( 65, 0, $Title );

	// 個人情報
	$x = 40;
	$y = 23;

	// 氏名
	$this->SetFont( 'SJIS', '', 16 );
	$this->LocText( 40, 23, "山田 太郎" );

	$x = 40;
	$y = 60;

	$this->SetFont( 'SJIS-2', 'B', 18 );
	$Text = '上記の者は、間違い無く';
	$this->LocText( $x, $y, $Text );

	$this->SetFont( 'SJIS-2', 'I', 18 );
	$Text = '銀プログラマである事を';
	$this->LocText( $x, $y+20, $Text );

	$this->SetFont( 'SJIS-2', 'B', 18 );
	$Text = '証明致します。';
	$this->LocText( $x, $y+40, $Text );


	$dt = explode( "/", date("m/d/Y") );
	
	// 平成
	$this->SetFont( 'SJIS', 'B', 13 );
	$dt[2] = ($dt[2]+0) - 1988;
	$this->LocText( $x+7, $y+60, "平成" );

	$this->LocText( $x+20, $y+60, sprintf( "%d年", $dt[2]+0 ), 'I', 12 );
	$this->LocText( $x+37, $y+60, sprintf( "%d月", $dt[0]+0 ) );
	$this->LocText( $x+50, $y+60, sprintf( "%d日", $dt[1]+0 ) );

	$this->SetFont( 'SJIS', 'B', 13 );
	$this->LocText( $x+40, $y+95, "架空法人　システム家成育社" );
	$this->LocText( $x+40, $y+105, "SQLの窓学院" );
	$this->LocText( $x+68, $y+115, "校　長" );

	$this->LocText( $x+94, $y+115, "lightbox" );

}

# **********************************************************
# 座標設定印字
# **********************************************************
function LocText( $x, $y, $str, $style='NONE', $size=14 ) {

	if ( $style != 'NONE' ) {
		$this->SetFont('SJIS',$style,$size);
	}
	$this->Text( $this->bx + $x, $this->by + $y, $str );

}

# **********************************************************
# 印字基点座標設定
# **********************************************************
function SetBase( $x=0, $y=0 ) {

	$this->bx = $x;
	$this->by = $y;

}

}
?>
