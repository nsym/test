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
	
	//初期設定
	session_start();
	$_SESSION["limit_num"] = isset($_SESSION['limit_num'])? $_SESSION['limit_num']: 25;
	if(isset($_GET['limit_num'])){
		$_SESSION["limit_num"] = $_GET['limit_num'];
	}
	
	//各クラスのインスタンス化
	$adminViewObj = new AdminView();
	$opViewObj = new View();
	$opViewObj->operation = 'op-cliant';
	$opViewObj->mode = isset($_GET["mode"])? $_GET["mode"]: 'list';
	$opViewObj->cliant_id = isset($_GET["id"])? $_GET["id"]: false;
	$opViewObj->group = isset($_GET["group"])? $_GET["group"]: false;
	$opViewObj->row = isset($_GET["row"])? $_GET["row"]: 'id';
	$opViewObj->order = isset($_GET["order"])? $_GET["order"]: false;
	$opViewObj->col = isset($_GET["col"])? $_GET["col"]: false;
	$opViewObj->page = isset($_GET['page'])? $_GET['page']: 0;
	$opViewObj->limit_num = $_SESSION['limit_num'];
	
	
		
	///--------------------------------------------------------------------
	/// 顧客登録情報の取得・生成
	///
	///	#Author yk
	//  #Date	2013/10/25
	///--------------------------------------------------------------------
	
	if($_GET['action']==='detail' && $opViewObj->cliant_id){
		//顧客情報の取得
		$master_data = $opViewObj->getCliantMasterForID($opViewObj->cliant_id);
		//顧客情報HTMLの生成
		echo $opViewObj->detailContents($master_data);
	}
	
		
	///--------------------------------------------------------------------
	/// 顧客リストのタブ切り替え
	///
	///	#Author yk
	//  #Date	2013/10/25
	///--------------------------------------------------------------------
	
	else if($_GET['action']==='tab'){
		/*
		//顧客リストの取得
		$master_list = $opViewObj->searchCliantMasterList('MASTER_KANA', $opViewObj->col, '', $opViewObj->page*$opViewObj->limit_num, $opViewObj->limit_num);
		$list_count = $opViewObj->countCliantMasterList('MASTER_KANA', $opViewObj->col);
		*/
		//検索フィールド設定
		$opViewObj->field = 'MASTER_KANA';
		//顧客リストの取得
		$master_list = $opViewObj->setMasterList();
		$list_count = $opViewObj->setListCount();
		//顧客リストの生成
		if($opViewObj->mode==='keyword'){
			echo $opViewObj->resultContents($master_list, $list_count);
		}else{
			echo $opViewObj->listContents($master_list, $list_count);
		}
	}
	
		
	///--------------------------------------------------------------------
	/// 顧客リストの並び替え
	///
	///	#Author yk
	//  #Date	2013/10/25
	///	#Author yk
	//  #Date	2013/04/16
	///--------------------------------------------------------------------
	
	else if($_GET['action']=='sort'){
		//並び替えの設定
		$orderby ='';
		if($opViewObj->row && $opViewObj->order){
			$row = strtoupper($opViewObj->row);
			$order = ($opViewObj->order==='icon-sort-down')? 'DESC': 'ASC';
			if($row=='MASTER_KANA' || $row=='MASTER_COMPANY'){
				$opViewObj->orderby = 'CAST( '.$row.' AS CHAR) '.$order;
			}else{
				$opViewObj->orderby = $row.' '.$order;
			}
		}
		/*
		//顧客リストの取得
		$master_list = $opViewObj->searchCliantMasterList('MASTER_KANA', $opViewObj->col, $orderby, $opViewObj->page*$opViewObj->limit_num, $opViewObj->limit_num);
		$list_count = $opViewObj->countCliantMasterList('MASTER_KANA', $opViewObj->col);
		*/
		//顧客リストの取得
		$master_list = $opViewObj->setMasterList();
		$list_count = $opViewObj->setListCount();
		//顧客リストの生成
		if($opViewObj->mode==='keyword'){
			echo $opViewObj->resultContents($master_list, $list_count);
		}else{
			echo $opViewObj->listContents($master_list, $list_count);
		}
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
			$order = ($opViewObj->order==='icon-sort-down')? 'ASC': 'DESC';
			if($row=='MASTER_KANA'){
				$opViewObj->orderby = 'CAST( '.$row.' AS CHAR) '.$order;
			}else{
				$opViewObj->orderby = $row.' '.$order;
			}
		}
		/*
		//顧客リストの取得
		//$master_list = $opViewObj->getCliantMasterList($orderby);
		$master_list = $opViewObj->searchCliantMasterList('MASTER_KANA', $opViewObj->col, $orderby, $opViewObj->page*$opViewObj->limit_num, $opViewObj->limit_num);
		$list_count = $opViewObj->countCliantMasterList('MASTER_KANA', $opViewObj->col);
		*/
		//顧客リストの取得
		$master_list = $opViewObj->setMasterList();
		$list_count = $opViewObj->setListCount();
		//顧客リストの生成
		if($opViewObj->mode==='keyword'){
			echo $opViewObj->resultContents($master_list, $list_count);
		}else{
			echo $opViewObj->listContents($master_list, $list_count);
		}
	}
	
		
	///--------------------------------------------------------------------
	/// 顧客メタの追加
	///
	///	#Author yk
	//  #Date	2013/11/07
	///--------------------------------------------------------------------
	
	else if($_GET['action']=='meta'){
		//顧客メタ追加フォームの生成
		echo $opViewObj->editMetaContents('', $_GET["meta_num"]+1);
	}

	
	
?>