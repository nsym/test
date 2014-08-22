<?php
	/* /////////////////////////////////////////////////////
	//		受注管理 共通AJAXパッケージ
	//////////////////////////////////////////////////////*/
	
	////////////////////////////////////////////////////////
	//
	//	#substance	パッケージの作成
	//  #Date		2013/12/13
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	
//--------------------------------------------------------------------
	require_once("../common/config.php");
	require_once("./view.php");
	$mydb = db_con();
	
	//初期設定
	session_start();
	$_SESSION["limit_num"] = isset($_SESSION['limit_num'])? $_SESSION['limit_num']: 25;
	if(isset($_GET['limit_num'])){
		$_SESSION["limit_num"] = $_GET['limit_num'];
	}
	
	//各クラスのインスタンス化
	$adminViewObj = new AdminView();
	$opViewObj = new View();
	$opViewObj->operation = 'op-accept';
	$opViewObj->mode = isset($_GET["mode"])? $_GET["mode"]: 'list';
	$opViewObj->accept_id = isset($_GET["id"])? $_GET["id"]: false;
	$opViewObj->staff = isset($_GET["staff"])? $_GET["staff"]: false;
	$opViewObj->row = isset($_GET["row"])? $_GET["row"]: 'id';
	$opViewObj->order = isset($_GET["order"])? $_GET["order"]: false;
	$opViewObj->col = isset($_GET["col"])? $_GET["col"]: false;
	$opViewObj->page = isset($_GET['page'])? $_GET['page']: 0;
	$opViewObj->limit_num = $_SESSION['limit_num'];
	
	
		
	///--------------------------------------------------------------------
	/// 受注登録情報の取得・生成
	///
	///	#Author yk
	//  #Date	2013/12/13
	///--------------------------------------------------------------------
	
	if($_GET['action']==='detail' && $opViewObj->accept_id){
		//受注情報の取得
		$master_data = $opViewObj->getAcceptMasterForID($opViewObj->accept_id);
		//受注情報HTMLの生成
		echo $opViewObj->detailContents($master_data);
	}
	
		
	///--------------------------------------------------------------------
	/// 受注リストのタブ切り替え
	///
	///	#Author yk
	//  #Date	2013/12/13
	///--------------------------------------------------------------------
	
	else if($_GET['action']==='tab'){
		//検索フィールド設定
		$opViewObj->field = 'ACCEPT_STATUS';
		//受注リストの取得
		$master_list = $opViewObj->setMasterList();
		$list_count = $opViewObj->setListCount();
		//受注リストの生成
		echo $opViewObj->listContents($master_list, $list_count);
	}
	
		
	///--------------------------------------------------------------------
	/// 受注リストの並び替え
	///
	///	#Author yk
	//  #Date	2013/12/13
	///--------------------------------------------------------------------
	
	else if($_GET['action']=='sort'){
		//並び替えの設定
		$orderby ='';
		if($opViewObj->row && $opViewObj->order){
			$row = strtoupper($opViewObj->row);
			$order = ($opViewObj->order==='icon-sort-down')? 'DESC': 'ASC';
			$opViewObj->orderby = $row.' '.$order;
		}
		//受注リストの取得
		$master_list = $opViewObj->setMasterList();
		$list_count = $opViewObj->setListCount();
		//受注リストの生成
		echo $opViewObj->listContents($master_list, $list_count);
	}
	
		
	///--------------------------------------------------------------------
	/// 受注リストのページ切り替え
	///
	///	#Author yk
	//  #Date	2013/12/13
	///--------------------------------------------------------------------
	
	else if($_GET['action']=='page'){
		//並び替えの設定
		$orderby ='';
		if($opViewObj->row && $opViewObj->order){
			$row = strtoupper($opViewObj->row);
			$order = ($opViewObj->order==='icon-sort-down')? 'ASC': 'DESC';
			$opViewObj->orderby = $row.' '.$order;
		}
		//受注リストの取得
		$master_list = $opViewObj->setMasterList();
		$list_count = $opViewObj->setListCount();
		//受注リストの生成
		echo $opViewObj->listContents($master_list, $list_count);
	}

	
	
?>