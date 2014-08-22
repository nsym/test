<?php

	/* /////////////////////////////////////////////////////
	//
	//		共通パッケージ		―画像管理―
	//
	//////////////////////////////////////////////////////*/

	class Image {
		
		public $data;
		
				
		///--------------------------------------------------------------------
		/// 【コンストラクター】
		///
		/// 戻り値　なし
		///
		///	#Author yk
		/// #date	2013/06/14
		///--------------------------------------------------------------------
		function __construct(){
			//サーバーのメモリ上限設定
			ini_set( 'memory_limit', '200M' );
		}
		
		
		///--------------------------------------------------------------------
		/// 画像のアップロード
		///
		///	#param	$target：ターゲットファイル, $directory：ディレクトリ
		///			$mode：保存モード
		///
		///	#Author yk
		/// #date	2013/01/16
		///--------------------------------------------------------------------
		function uploadImage($target, $directory, $mode){
			global $_FILES;
			
			//ファイル情報の取得
			$this->data = pathinfo($_FILES[$target]["name"]);
			//ファイルの拡張子の取得
			$ext = $this->data['extension'];
			//ファイル名の設定
			switch($mode){
				case "copy":
					$filename = $_FILES[$target]["name"];
					break;
				case "uniq":
				default:
					$filename = uniqid("").'.'.$ext;
					break;
			}
			
			//ファイルのアップロードに成功した場合
			if (is_uploaded_file(@$_FILES[$target]["tmp_name"])) {	//写真ファイルが存在する時のみファイル名を生成
				//ファイルをディレクトリに保存
				if(move_uploaded_file($_FILES[$target]["tmp_name"], $directory.$filename))
					$res = $filename;		//保存したファイル名を返す
				else
					$res = 'image-ng';
			}
			//ファイルのアップロードに失敗した場合
			else{
				$res = 'image-ng';
			}
			return $res;
		}
	
	
		///--------------------------------------------------------------------
		/// 画像のリサイズ
		///
		///	#param	$target_path：ファイルパス, $file_name：ターゲットファイル
		///			$re_width：リサイズ幅, $directory：保存ディレクトリ
		///
		///	#Author yk
		/// #date	2013/01/16
		///--------------------------------------------------------------------
		function resizeImage($directory, $target_path, $file_name, $re_width){
			
			//画像サイズの取得
			list($width, $height) = getimagesize($target_path.$file_name);
			
			//画像幅がリサイズ幅より大きい場合
			if($width > $re_width){
				if($this->data['extension']=="gif"){
					//リサイズ高さを比率から算出
					$re_height = $height * ($re_width / $width);
					//リサイズ用のTrueColor画像を作成
					$resize = imagecreatetruecolor($re_width, $re_height);
					//ターゲットファイルから新しい画像を作成
					$source = imagecreatefromgif($target_path.$file_name);
					//ソース画像をリサイズ用画像でサンプリングする（リサイズ）
					imagecopyresampled($resize, $source, 0, 0, 0, 0, $re_width, $re_height, $width, $height);
					//リサイズ画像の出力
					imagegif($resize, $directory.$file_name);
				}
				else if($this->data['extension']=="png"){
					//リサイズ高さを比率から算出
					$re_height = $height * ($re_width / $width);
					//リサイズ用のTrueColor画像を作成
					$resize = imagecreatetruecolor($re_width, $re_height);
					//ターゲットファイルから新しい画像を作成
					$source = imagecreatefrompng($target_path.$file_name);
					//ソース画像をリサイズ用画像でサンプリングする（リサイズ）
					imagecopyresampled($resize, $source, 0, 0, 0, 0, $re_width, $re_height, $width, $height);
					//リサイズ画像の出力
					imagepng($resize, $directory.$file_name);
				}
				else{
					//リサイズ高さを比率から算出
					$re_height = $height * ($re_width / $width);
					//リサイズ用のTrueColor画像を作成
					$resize = imagecreatetruecolor($re_width, $re_height);
					//ターゲットファイルから新しい画像を作成
					$source = imagecreatefromjpeg($target_path.$file_name);
					//ソース画像をリサイズ用画像でサンプリングする（リサイズ）
					imagecopyresampled($resize, $source, 0, 0, 0, 0, $re_width, $re_height, $width, $height);
					//リサイズ画像の出力
					imagejpeg($resize, $directory.$file_name);
				}
			}
			//画像幅がリサイズ幅以下の場合
			else{
				//画像をコピー保存
				copy($target_path.$file_name, $directory.$file_name);
			}
		}
		
		
		///--------------------------------------------------------------------
		/// トリミング関数
		///
		///	#param	$target_path：ファイルパス, $file_name：ターゲットファイル
		///			$new_width:横幅指定, $new_height:高さ指定, $directory：保存ディレクトリ
		///
		///	#Author yk
		/// #date	2013/06/14
		///--------------------------------------------------------------------
		function trimmingImage($directory, $target_path, $file_name, $new_width, $new_height){
			
			//画像サイズの取得
			list($width, $height) = getimagesize($target_path.$file_name);
			
			//リサイズサイズの設定
			$re_height = $height * ($new_width/$width);
			//高さに合わせてリサイズする場合
			if($re_height<$new_height){
				$re_height = $new_height;
				$re_width = $width * ($new_height/$height);
			}
			//横幅に合わせてリサイズする場合
			else{
				$re_width = $new_width;
			}
			
			//リサイズ用のTrueColor画像を作成
			$resize = imagecreatetruecolor($re_width, $re_height);
			//ターゲットファイルから新しい画像を作成
			if($this->data['extension']=="gif"){
				$source = imagecreatefromgif($target_path.$file_name);
			}else if($this->data['extension']=="png"){
				$source = imagecreatefrompng($target_path.$file_name);
			}else{
				$source = imagecreatefromjpeg($target_path.$file_name);	
			}
			//ソース画像をリサイズ用画像でサンプリングする（リサイズ）
			imagecopyresampled($resize, $source, 0, 0, 0, 0, $re_width, $re_height, $width, $height);
			
			//トリミング用のTrueColor画像を作成
			$trimming = imagecreatetruecolor( $new_width, $new_height );
			//座標原点設定
			$dst_x = $dst_y = 0;
			//トリミング原点の設定
			$src_x = floor(($re_width-$new_width) / 2);
			$src_y = floor(($re_height-$new_height) / 2);
			//トリミング画像サイズの設定
			$dst_w = $src_w = $new_width;
			$dst_h = $src_h = $new_height;
			//リサイズ画像をトリミング原点・サイズでコピーする（トリミング）
			imagecopyresized($trimming, $resize, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
			//トリミング画像の出力
			if($this->data['extension']=="gif"){
				imagegif($trimming, $directory.$file_name);
			}else if($this->data['extension']=="png"){
				imagepng($trimming, $directory.$file_name);
			}else{
				imagejpeg($trimming, $directory.$file_name);
			}
		}	
	}
	
?>