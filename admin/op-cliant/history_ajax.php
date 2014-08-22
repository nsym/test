<?php
	/* /////////////////////////////////////////////////////
	//		顧客管理 対応履歴AJAXパッケージ
	//////////////////////////////////////////////////////*/
	
	////////////////////////////////////////////////////////
	//
	//	#substance	パッケージの作成
	//  #Date		2013/12/04
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	
//--------------------------------------------------------------------
	require_once("../common/config.php");
	require_once("./view.php");
	$mydb = db_con();
	
	//各クラスのインスタンス化
	$adminViewObj = new AdminView();
	$opViewObj = new View();
	$opViewObj->operation = 'op-cliant';
	$opViewObj->mode = isset($_GET["mode"])? $_GET["mode"]: 'list';
	$opViewObj->history_id = isset($_GET["id"])? $_GET["id"]: false;
	
	
		
	///--------------------------------------------------------------------
	/// 対応履歴情報の取得・生成
	///
	///	#Author yk
	//  #Date	2013/12/04
	///--------------------------------------------------------------------
	
	if($_GET['action']==='detail' && $opViewObj->history_id){
		//対応履歴情報HTMLの生成
		echo $opViewObj->editHistoryContents('', $opViewObj->history_id);
	}
	
	
?>