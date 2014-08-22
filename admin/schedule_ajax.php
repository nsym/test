<?php
	/* /////////////////////////////////////////////////////
	//		ホーム スケジュールAJAXパッケージ
	//////////////////////////////////////////////////////*/
	
	////////////////////////////////////////////////////////
	//
	//	#substance	パッケージの作成
	//  #Date		2013/12/18
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	
//--------------------------------------------------------------------
	require_once("./common/config.php");
	require_once("./db/op-home/schedule.php");
	require_once("./op-home/view.php");
	$mydb = db_con();
	
	//各クラスのインスタンス化
	$opViewObj = new View();
	$scheduleObj = new Schedule();
	
		
	///--------------------------------------------------------------------
	/// スケジュール情報の取得・生成
	///
	///	#Author yk
	//  #Date	2013/12/18
	///--------------------------------------------------------------------
	
	if($_GET['action']==='detail' && !empty($_GET["id"])){
		
		//スケジュール情報の取得
		$schedule_data = $scheduleObj->getScheduleMasterForID($_GET["id"]);
		//スケジュール情報HTMLの生成
		echo $opViewObj->setEditScheduleContents($schedule_data);
	}
	
		
	///--------------------------------------------------------------------
	/// カレンダーの取得・生成
	///
	///	#Author yk
	//  #Date	2013/12/18
	///--------------------------------------------------------------------
	
	if($_GET['action']==='calender' && !empty($_GET["date"])){
		
		//スケジュール情報HTMLの取得・生成
		echo $opViewObj->setCalenderContents($_GET["date"]);
	}
	
?>