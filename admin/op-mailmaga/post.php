<?php
	/* /////////////////////////////////////////////////////
	//		メルマガ管理　新規作成・編集・削除
	//////////////////////////////////////////////////////*/
	require_once("../common/config.php");
	require_once("../db/op-mailmaga/mailmaga.php");
	require_once("../db/op-mailmaga/queue.php");
	require_once("../db/op-cliant/group.php");
	$mydb = db_con();
	
	//インスタンス化
	$mailmagaObj = new Mailmaga();
	$queueObj = new Queue();
	$groupObj = new Group();
	
	//print_r($_POST);
	
	//入力OKの場合
	if(!empty($_POST['post_title']) && !empty($_POST['pc_text_body']) && !empty($_POST['mb_text_body']) && ( $_POST['post_specify']==='group' || !empty($_POST['post_cliant']) )){
		//お知らせ日時の設定
		$_POST["post_date"] = date('Y-m-d H:i:s', mktime($_POST["date_array"][3], $_POST["date_array"][4], 0, $_POST["date_array"][1], $_POST["date_array"][2], $_POST["date_array"][0]));
		
		//新規保存の場合
		if(isset($_POST['new'])){
			$res = $mailmagaObj->insertMailmagaPost($_POST);
		}
		//更新の場合
		else if(isset($_POST['edit'])){
			$res = $mailmagaObj->insertMailmagaPost($_POST);
		}
		
		//リダイレクト先の設定
		if(isset($_POST['edit'])){
			$mode = 'list';
			$res.='&id='.$_POST['id'];
		}
		else if(isset($_POST['new'])){
			$mode = preg_match('/-ok/', $res)? 'list': 'new';
		}
	}
	//入力NGの場合
	else if(isset($_POST['new']) || isset($_POST['edit'])){
		if(isset($_POST['new'])){
			$mode = 'new';	
		}
		else if(isset($_POST['edit'])){
			$mode = 'edit';	
		}
		$res = 'input-ng';
		if($mode=='edit'){
			$res.='&id='.$_POST['id']	;
		}
	}
	//削除・配信停止の場合
	else if($_GET['action']==='trash' || $_GET['action']==='stop'){
		$mode = 'list';
		$res = $mailmagaObj->changeMailmagaPostForID($_GET['id'], $_GET['action']);
		/* 関連キューを削除する */
		$queueObj->deleteMailmagaQueueForPostID($_GET['id']);
	}
	//print_r($_POST);
	
	//リダイレクトURLの生成
	$url = '/admin/op-mailmaga/?mode='.$mode.'&state='.$res;
	//リダイレクト処理
	header("Location: $url");
	
?>
