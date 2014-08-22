<?php
	/* /////////////////////////////////////////////////////
	//		メルマガ管理 共通AJAXパッケージ
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
	$templateObj = new Template();
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
	$opViewObj->limit_num = 10;
	
	
		
	///--------------------------------------------------------------------
	/// メルマガ登録情報の取得・生成
	///
	///	#Author yk
	//  #Date	2013/11/12
	///--------------------------------------------------------------------
	
	if($_GET['action']==='detail' && $opViewObj->cliant_id){
		//メルマガ情報の取得
		$master_data = $opViewObj->getMailmagaPostForID($opViewObj->cliant_id);
		//メルマガ情報HTMLの生成
		echo $opViewObj->detailContents($master_data);
	}
	
		
	///--------------------------------------------------------------------
	/// メルマガリストのタブ切り替え
	///
	///	#Author yk
	//  #Date	2013/11/12
	///--------------------------------------------------------------------
	
	else if($_GET['action']==='tab'){
		
		//メルマガリストの取得
		$master_list = $opViewObj->searchMailmagaPostList('POST_STATUS', $opViewObj->col, '', $opViewObj->page*$opViewObj->limit_num, $opViewObj->limit_num);
		$list_count = $opViewObj->countMailmagaPostList('POST_STATUS', $opViewObj->col);
		//メルマガリストの生成
		if($opViewObj->mode==='keyword'){
			echo $opViewObj->resultContents($master_list, $list_count);
		}else{
			echo $opViewObj->listContents($master_list, $list_count);
		}
	}
	
		
	///--------------------------------------------------------------------
	/// メルマガリストの並び替え
	///
	///	#Author yk
	//  #Date	2013/11/12
	///--------------------------------------------------------------------
	
	else if($_GET['action']=='sort'){
		//並び替えの設定
		$orderby ='';
		if($opViewObj->row && $opViewObj->order){
			$row = strtoupper($opViewObj->row);
			$order = ($opViewObj->order==='icon-sort-down')? 'ASC': 'DESC';
			$opViewObj->orderby = $row.' '.$order;
		}
		//メルマガリストの取得
		$master_list = $opViewObj->searchMailmagaPostList('POST_STATUS', $opViewObj->col, $opViewObj->orderby, $opViewObj->page*$opViewObj->limit_num, $opViewObj->limit_num);
		$list_count = $opViewObj->countMailmagaPostList('POST_STATUS', $opViewObj->col);
		//メルマガリストの生成
		if($opViewObj->mode==='keyword'){
			echo $opViewObj->resultContents($master_list, $list_count);
		}else{
			echo $opViewObj->listContents($master_list, $list_count);
		}
	}
	
		
	///--------------------------------------------------------------------
	/// メルマガリストのページ切り替え
	///
	///	#Author yk
	//  #Date	2013/11/12
	///--------------------------------------------------------------------
	
	else if($_GET['action']=='page'){
		//並び替えの設定
		$orderby ='';
		if($opViewObj->row && $opViewObj->order){
			$row = strtoupper($opViewObj->row);
			$order = ($opViewObj->order==='icon-sort-down')? 'DESC': 'ASC';
			$opViewObj->orderby = $row.' '.$order;
		}
		//メルマガリストの取得
		$master_list = $opViewObj->searchMailmagaPostList('POST_STATUS', $opViewObj->col, $orderby, $opViewObj->page*$opViewObj->limit_num, $opViewObj->limit_num);
		$list_count = $opViewObj->countMailmagaPostList('POST_STATUS', $opViewObj->col);
		//メルマガリストの生成
		if($opViewObj->mode==='keyword'){
			echo $opViewObj->resultContents($master_list, $list_count);
		}else{
			echo $opViewObj->listContents($master_list, $list_count);
		}
	}
	
		
	///--------------------------------------------------------------------
	/// メルマガテンプレートの詳細表示
	///
	///	#Author yk
	//  #Date	2013/11/12
	///--------------------------------------------------------------------
	
	else if($_GET['action']=='temp_detail'){
		$template_data = $templateObj->getMailmagaTemplateForID($_GET["template_id"]);
		echo $opViewObj->templateDetailContents($template_data);
	}
	
		
	///--------------------------------------------------------------------
	/// メルマガテンプレートのリスト表示
	///
	///	#Author yk
	//  #Date	2013/11/12
	///--------------------------------------------------------------------
	
	else if($_GET['action']=='temp_list'){
		$template_list = $templateObj->getMailmagaTemplateList();
		echo $opViewObj->templateListContents($template_list);
	}

	
	
?>