<?php
	/* /////////////////////////////////////////////////////
	//		ユーザー管理 共通AJAXパッケージ
	//////////////////////////////////////////////////////*/
	
	////////////////////////////////////////////////////////
	//
	//	#substance	パッケージの作成
	//  #Date		2013/10/25
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	//
	//	#substance	ー
	//  #Date		----/--/--
	//	#Author 	--
	//
	////////////////////////////////////////////////////////
	
//--------------------------------------------------------------------
	require_once("../common/config.php");
	require_once("./view.php");
	$mydb = db_con();
	
	//各クラスのインスタンス化
	$adminViewObj = new AdminView();
	$opViewObj = new View();
	$opViewObj->operation = 'op-user';
	$opViewObj->mode = isset($_GET["mode"])? $_GET["mode"]: 'list';
	$opViewObj->user_id = isset($_GET["id"])? $_GET["id"]: false;
	$opViewObj->row = isset($_GET["row"])? $_GET["row"]: 'id';
	$opViewObj->order = isset($_GET["order"])? $_GET["order"]: false;
	$opViewObj->col = isset($_GET["col"])? $_GET["col"]: false;
	$opViewObj->page = isset($_GET['page'])? $_GET['page']: 0;
	$opViewObj->limit_num = 10;
	
	
		
	///--------------------------------------------------------------------
	/// ユーザー登録情報の取得・生成
	///
	///	#Author yk
	//  #Date	2013/10/25
	///--------------------------------------------------------------------
	
	if($opViewObj->mode=='detail' && $opViewObj->user_id){
		//ユーザー情報の取得
		$master_data = $opViewObj->getUserMasterForID($opViewObj->user_id);
		//ユーザー情報HTMLの生成
		echo $opViewObj->detailContents($master_data);
	}
	
		
	///--------------------------------------------------------------------
	/// ユーザーリストのタブ切り替え
	///
	///	#Author yk
	//  #Date	2013/10/25
	///--------------------------------------------------------------------
	
	else if($opViewObj->mode=='tab'){
		//ユーザーリストの取得
		$master_list = $opViewObj->searchUserMasterList('ADMIN_AUTHORITY', $opViewObj->col, '', $opViewObj->page*$opViewObj->limit_num, $opViewObj->limit_num);
		$list_count = $opViewObj->countUserMasterList('ADMIN_AUTHORITY', $opViewObj->col);
		//ユーザーリストの生成
		echo $opViewObj->listContents($master_list, $list_count);
	}
	
		
	///--------------------------------------------------------------------
	/// ユーザーリストの並び替え
	///
	///	#Author yk
	//  #Date	2013/10/25
	///--------------------------------------------------------------------
	
	else if($opViewObj->mode=='sort'){
		//並び替えの設定
		$orderby ='';
		if($opViewObj->row && $opViewObj->order){
			$row = strtoupper($opViewObj->row);
			$order = ($opViewObj->order==='icon-sort-down')? 'ASC': 'DESC';
			$orderby = $row.' '.$order;
		}
		//ユーザーリストの取得
		//$master_list = $opViewObj->getUserMasterList($orderby);
		$master_list = $opViewObj->searchUserMasterList('ADMIN_AUTHORITY', $opViewObj->col, $orderby, $opViewObj->page*$opViewObj->limit_num, $opViewObj->limit_num);
		$list_count = $opViewObj->countUserMasterList('ADMIN_AUTHORITY', $opViewObj->col);
		//ユーザーリストの生成
		echo $opViewObj->listContents($master_list, $list_count);
	}
	
		
	///--------------------------------------------------------------------
	/// ユーザーリストの並び替え
	///
	///	#Author yk
	//  #Date	2013/10/25
	///--------------------------------------------------------------------
	
	else if($opViewObj->mode=='page'){
		//並び替えの設定
		$orderby ='';
		if($opViewObj->row && $opViewObj->order){
			$row = strtoupper($opViewObj->row);
			$order = ($opViewObj->order==='icon-sort-down')? 'DESC': 'ASC';
			$orderby = $row.' '.$order;
		}
		//ユーザーリストの取得
		//$master_list = $opViewObj->getUserMasterList($orderby);
		$master_list = $opViewObj->searchUserMasterList('ADMIN_AUTHORITY', $opViewObj->col, $orderby, $opViewObj->page*$opViewObj->limit_num, $opViewObj->limit_num);
		$list_count = $opViewObj->countUserMasterList('ADMIN_AUTHORITY', $opViewObj->col);
		//ユーザーリストの生成
		echo $opViewObj->listContents($master_list, $list_count);
	}

	
	
?>