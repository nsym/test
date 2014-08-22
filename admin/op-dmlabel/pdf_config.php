<?php
require_once("../common/fpdf/japanese.php");
require_once("../common/config.php");

class PDF_lightbox extends PDF_japanese {
	public $bx = 0;
	public $by = 0;

	# **********************************************************
	# 新規ページ
	# **********************************************************
	function newUserPage($iid, $pdf_vec, $cliant_list, $dmlabeldata, $output_mode) {
		mb_convert_variables("SJIS","UTF-8",$cliant_list);
		
		//各1～10 or 12の基準点（左上角）の座標を配列にセット
		for($i=1; $i<=$dmlabeldata["PIECE_MODE"]; $i++){
			$retsu = ($i%2) >= 1 ? floor($i/2)+1 : floor($i/2);
			if(($i%2) >= 1){
				//偶数列
				$master_point[$i]["X"] = $dmlabeldata["MARGIN_LEFT"];
			}else{
				//奇数列
				$master_point[$i]["X"] = $dmlabeldata["MARGIN_LEFT"] + $dmlabeldata["PIECE_WIDTH"] + $dmlabeldata["PIECE_SPACE"];
			}
			$master_point[$i]["Y"] = $dmlabeldata["MARGIN_TOP"] + $dmlabeldata["PIECE_HEIGHT"] * ($retsu - 1);
		}
		//ピース内の要素の座標
		$piece_x = $dmlabeldata["PADDING_LEFT"];
		$piece_y = $dmlabeldata["PADDING_TOP"];
		$piece_zip = $piece_y;
		$piece_add_1 = $piece_y + 5;
		$piece_add_2 = $piece_y + 10;
		$piece_company = $piece_y + 14;
		$piece_post = $piece_y + 18;
		$piece_name = $piece_y + 23;
		$piece_sub_1 = $piece_y + 28;
		$piece_sub_2 = $piece_y + 31;
		
		//フォントカラー設定
		$this->SetTextColor( 0, 0, 0 );
		
		//最初のページを追加
		$this->AddPage($pdf_vec);
		
		//追加記載項目
		//$item_setting_array = explode(",", $dmlabeldata["ITEM_SETTING"] );
		//$item_setting_1 = $item_setting_array[0];
		//$item_setting_2 = $item_setting_array[1];
		
		//ループ数
		if($output_mode == 'test'){
			//テスト時
			$for_num = $dmlabeldata["PIECE_MODE"];
			$now_count = 1;
			//テストの場合に罫線を引く
			$this->AddLine($pdf_vec, $master_point, $dmlabeldata);
		}else{
			$for_num = count($cliant_list);
			$now_count = $dmlabeldata["PIECE_START"];
		}
		
		global $area_list;
		//echo date('Y-m-d H:i:s');

		for($i=0; $i<$for_num; $i++){
			//ページを追加
			if($now_count == 1 && $i != 0){
				$this->AddPage($pdf_vec);
			}
			
			//郵便番号
			$this->setXY($master_point[$now_count]["X"]+$piece_x, $master_point[$now_count]["Y"]+$piece_zip);
			$this->SetFont('SJIS-2', '', 8 );
			$this->SJISMultiCell(70, 5, $cliant_list[$i]["MASTER_ZIPCODE"], 0, 'L');
			//住所１
			$this->setXY($master_point[$now_count]["X"]+$piece_x, $master_point[$now_count]["Y"]+$piece_add_1);
			//都道府県名と検索して設定
			$area_flag = false;
			foreach($area_list as $value){
				if(in_array(mb_convert_encoding($cliant_list[$i]["MASTER_AREA"], "utf-8"), $value)){
					$area_flag = true;
					$this->SJISMultiCell(70, 4, $cliant_list[$i]["MASTER_AREA"].$cliant_list[$i]["MASTER_ADDRESS"], 0, 'L');
				}
			}
			//国外・その他の場合
			if(!$area_flag){
				$this->SJISMultiCell(70, 4, $cliant_list[$i]["MASTER_ADDRESS"], 0, 'L');
			}
			//会社名
			$this->setXY($master_point[$now_count]["X"]+$piece_x, $master_point[$now_count]["Y"]+$piece_company);
			$this->SetFont('SJIS-2', '', 9 );
			$this->SJISMultiCell(70, 7, $cliant_list[$i]["MASTER_COMPANY"], 0, 'L');
			//役職
			$this->setXY($master_point[$now_count]["X"]+$piece_x, $master_point[$now_count]["Y"]+$piece_post);
			$this->SetFont('SJIS-2', '', 9 );
			$this->SJISMultiCell(70, 7, $cliant_list[$i]["MASTER_POST"], 0, 'L');
			//顧客名
			$this->setXY($master_point[$now_count]["X"]+$piece_x, $master_point[$now_count]["Y"]+$piece_name);
			$this->SetFont('SJIS-2', '', 12 );
			$this->SJISMultiCell(70, 7, $cliant_list[$i]["MASTER_NAME"].'　'.様, 0, 'L');
			//文字列
			$this->SetFont('SJIS-2', '', 8 );
			//追加項目１
			if($dmlabeldata['ITEM_SETTING'] != ''){
				$this->setXY($master_point[$now_count]["X"]+$piece_x, $master_point[$now_count]["Y"]+$piece_sub_2);
				$this->SJISMultiCell(70, 5, '( '.$cliant_list[$i]['STAFF_ID'].' )', 0, 'R');
			}

			//カウンター
			$now_count++;
			if($now_count > $dmlabeldata["PIECE_MODE"]){
				$now_count = 1;
			}
		}
		
	}
	
	# **********************************************************
	# 罫線を引く
	# **********************************************************
	function AddLine($pdf_vec, $master_point, $dmlabeldata) {
		//線の色設定
		$this->SetDrawColor( 0, 0, 0 );
		//線の幅設定
		$this->SetLineWidth( 0.1 );
		for($j=1; $j<=$dmlabeldata["PIECE_MODE"]; $j++){
			$x = $master_point[$j]["X"];
			$y = $master_point[$j]["Y"];
			$w = $dmlabeldata["PIECE_WIDTH"];
			$h = $dmlabeldata["PIECE_HEIGHT"];
			$this->Rect($x, $y, $w, $h, 'D');
		}
	}
	

	# **********************************************************
	# 座標設定印字
	# **********************************************************
	function LocText( $x, $y, $str, $style='NONE', $size=14 ) {
	
		if ( $style != 'NONE' ) {
			$this->SetFont('SJIS-2',$style,$size);
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
