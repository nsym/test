<?php
	///--------------------------------------------------------------------
	/// 共通パッケージ	－データベース－
	///--------------------------------------------------------------------
	
	///--------------------------------------------------------------------
	/// データベース接続関係
	///
	///
	///	#Author yk
	///	#date	2013/10/22
	///--------------------------------------------------------------------
	
	//接続
	function db_con(){
		$dhb = DB_HOST;
		$dtb = DB_NAME;
		$dnb = DB_USER;
		$dpb = DB_PASS;
		$con = mysql_connect(b6d(b6d($dhb)),b6d(b6d($dnb)),b6d(b6d($dpb))) or die("接続エラー");
		mysql_select_db(b6d(b6d($dtb))) or die("接続エラー");
		$sql = "SET NAMES utf8";
		mysql_query($sql);
		return $con;
	}
	function mysql_array($res){
		if($res != ""){
			$i = 0;
			$tararray = array();
			while($row = mysql_fetch_array($res , MYSQL_ASSOC)){
				$tararray[ $i ] = $row;
				$i++;
			}
			return $tararray;
		}
	}
	
	
?>