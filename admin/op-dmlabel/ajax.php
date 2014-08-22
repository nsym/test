<?php
	/* /////////////////////////////////////////////////////
	//		顧客管理 共通AJAXパッケージ
	//////////////////////////////////////////////////////*/
	
	////////////////////////////////////////////////////////
	//
	//	#substance	パッケージの作成
	//  #Date		2013/11/04
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	//
	//	#substance	パッケージの作成
	//  #Date		2013/11/06
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
	$opViewObj->operation = 'op-dmlabel';
	$opViewObj->mode = isset($_GET["mode"])? $_GET["mode"]: 'list';
	$opViewObj->dmlabel_id = isset($_GET["id"])? $_GET["id"]: false;
	$opViewObj->group = isset($_GET["group"])? $_GET["group"]: false;
	$opViewObj->row = isset($_GET["row"])? $_GET["row"]: 'id';
	$opViewObj->order = isset($_GET["order"])? $_GET["order"]: false;
	$opViewObj->col = isset($_GET["col"])? $_GET["col"]: false;
	$opViewObj->page = isset($_GET['page'])? $_GET['page']: 0;
	$opViewObj->limit_num = 10;
	
	
		
	///--------------------------------------------------------------------
	/// 顧客登録情報の取得・生成
	///
	///	#Author yk
	//  #Date	2013/10/25
	///--------------------------------------------------------------------
	
	if($_GET['action']==='detail' && $opViewObj->dmlabel_id){
		//顧客情報の取得
		$master_data = $opViewObj->getDMlabelMasterForID($opViewObj->dmlabel_id);
		//顧客情報HTMLの生成
		echo $opViewObj->detailContents($master_data);
	}
	
		
	///--------------------------------------------------------------------
	/// 顧客リストの並び替え
	///
	///	#Author yk
	//  #Date	2013/10/25
	///--------------------------------------------------------------------
	
	else if($_GET['action']=='sort'){
		//並び替えの設定
		$orderby ='';
		if($opViewObj->row && $opViewObj->order){
			$row = strtoupper($opViewObj->row);
			$order = ($opViewObj->order==='icon-sort-down')? 'ASC': 'DESC';
			$opViewObj->orderby = $row.' '.$order;
		}
		
		//顧客リストの取得
		$master_list = $opViewObj->searchDMlabelMasterList('', $opViewObj->col, $opViewObj->orderby, $opViewObj->page*$opViewObj->limit_num, $opViewObj->limit_num);
		$list_count = $opViewObj->countDMlabelMasterList('', $opViewObj->col);
		
		//顧客リストの取得
		//$master_list = $opViewObj->setMasterList();
		//$list_count = $opViewObj->setListCount();
		//顧客リストの生成
		echo $opViewObj->listContents($master_list, $list_count);
	}
	
		
	///--------------------------------------------------------------------
	/// 顧客リストのページ切り替え
	///
	///	#Author yk
	//  #Date	2013/10/25
	///--------------------------------------------------------------------
	
	else if($_GET['action']=='page'){
		//並び替えの設定
		$orderby ='';
		if($opViewObj->row && $opViewObj->order){
			$row = strtoupper($opViewObj->row);
			$order = ($opViewObj->order==='icon-sort-down')? 'DESC': 'ASC';
			$opViewObj->orderby = $row.' '.$order;
		}
		/*
		//顧客リストの取得
		//$master_list = $opViewObj->getDMlabelMasterList($orderby);
		$master_list = $opViewObj->searchDMlabelMasterList('MASTER_KANA', $opViewObj->col, $orderby, $opViewObj->page*$opViewObj->limit_num, $opViewObj->limit_num);
		$list_count = $opViewObj->countDMlabelMasterList('MASTER_KANA', $opViewObj->col);
		*/
		//顧客リストの取得
		$master_list = $opViewObj->searchDMlabelMasterList('', $opViewObj->col, $opViewObj->orderby, $opViewObj->page*$opViewObj->limit_num, $opViewObj->limit_num);
		$list_count = $opViewObj->countDMlabelMasterList('', $opViewObj->col);
		//顧客リストの生成
		if($opViewObj->mode==='keyword'){
			echo $opViewObj->resultContents($master_list, $list_count);
		}else{
			echo $opViewObj->listContents($master_list, $list_count);
		}
	}
	
		
?>