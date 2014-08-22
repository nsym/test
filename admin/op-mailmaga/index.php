<?php
	/* /////////////////////////////////////////////////////
	//		メルマガ管理 INDEX
	//////////////////////////////////////////////////////*/
	require_once("../common/config.php");
	require_once("./view.php");
	$mydb = db_con();
	
	//ログイン確認処理
	session_start();
	$ssid = session_id();
	$user_id = $_SESSION["user_id"];
	$authObj = new Auth($user_id);
	
	//各クラスのインスタンス化
	$adminViewObj = new AdminView();
	$opViewObj = new View();
	$adminViewObj->operation = $opViewObj->operation = 'op-mailmaga';
	$opViewObj->mode = isset($_GET["mode"])? $_GET["mode"]: 'list';
	$opViewObj->state = isset($_GET["state"])? $_GET["state"]: '';
	$opViewObj->mailmaga_id = isset($_GET["id"])? $_GET["id"]: false;
	$opViewObj->group = isset($_GET["group"])? $_GET["group"]: false;
	$opViewObj->row = isset($_GET["row"])? $_GET["row"]: 'id';
	$opViewObj->order = isset($_GET["order"])? $_GET["order"]: false;
	$opViewObj->col = isset($_GET["col"])? $_GET["col"]: false;
	$opViewObj->page = isset($_GET['page'])? $_GET['page']: 0;
	$opViewObj->limit_num = 10;

	
	
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
	<title><?= ADMIN_TITLE ?></title>
	<link rel="stylesheet" type="text/css" href="/admin/css/reset.css">
	<link rel="stylesheet" type="text/css" href="/admin/css/admin.css">
	<link rel="stylesheet" type="text/css" href="main.css">
</head>
<body>
	<? $adminViewObj->setHeader() ?>
	<div id="container">
		<div id="sidebar">
			<div class="sidebarToggle"></div>
			<ul>
				<? $opViewObj->setSidebar() ?>
			</ul>
		</div>
		<div id="contents">
			<div id="ajaxLoader"><img src="/admin/images/ajax_loader.gif"></div>
			<div class="wrapper">
				<? $opViewObj->setContents() ?>
			</div>
		</div>
	</div>
	<? $adminViewObj->setFooter() ?>
	
	<script src="/admin/js/jquery-1.7.2.min.js"></script>
	<script src="/admin/js/jquery.zip2addr.js"></script>
	<script src="/admin/js/main.js"></script>
	<script type="text/javascript">
	<!--
		$(function(){
			
			
			//読み込み中のローディング設定
			$("#ajaxLoader").ajaxStart(function() {
				$(this).show();
			}).ajaxStop(function() {
				$(this).hide();
			});
			
			//リストタブの切替機能
			$('ul.listCtrl li#all').addClass('active');
			$('ul.listCtrl li a').live('click', function(){
				var url = window.location.search.substring(1);
				var col = $(this).parent('li').attr('id');
				$.get('/admin/op-mailmaga/ajax.php?'+url+'&action=tab&col='+col,
					function(data){
						$('#list-wrap').html(data);
						$('ul.listCtrl li#'+col).addClass('active');
					},
					'html'
				);
				return false;
			});
			
			//リストの並び替え機能
			$('th.sortRow a').live('click', function(){
				var url = window.location.search.substring(1);
				var row = $(this).parent('th').attr('id');
				var order = $(this).attr('class');
				var col = $('ul.listCtrl li.active').attr('id');
				$.get('/admin/op-mailmaga/ajax.php?'+url+'&action=sort&row='+row+'&order='+order+'&col='+col,
					function(data){
						$('#list-wrap').html(data);
						if(order=='icon-sort-down'){
							$('#'+row).addClass('sorted');
							$('#'+row+' a').attr('class', 'icon-sort-up');
						}else{
							$('#'+row).addClass('sorted');
							$('#'+row+' a').attr('class', 'icon-sort-down');
						}
						$('ul.listCtrl li#'+col).addClass('active');
					},
					'html'
				);
				return false;
			});
		
			//テンプレートの本文追加機能
			$('#detail-wrap #addTemplate').live('click', function(){
				//テキスト本文（PC）
				var $pc_textarea = $('form[name=editForm] textarea[name=pc_text_body]'),
					pc_text = $pc_textarea.val(),
					pc_text_temp = $('#detail-wrap input[name=pc_text_body]').val();
				$('form[name=editForm] textarea[name=pc_text_body]').val(pc_text+pc_text_temp);
				//テキスト本文(モバイル)
				var	$mb_textarea = $('form[name=editForm] textarea[name=mb_text_body]'),
					mb_text = $mb_textarea.val(),
					mb_text_temp = $('#detail-wrap input[name=mb_text_body]').val();
				$('form[name=editForm] textarea[name=mb_text_body]').val(mb_text+mb_text_temp);
				return false;
			});
			
			
			//配信モードの切り替え表示
			$('select[name=post_mode]').change(function(){
				$('#html_mode').toggleClass('hidden');
				$('#text_mode').toggleClass('hidden');
			});
			//配信先の切り替え表示
			$('input[name=post_specify]').change(function(){
				$('#post_group').toggleClass('hidden');
				$('#post_cliant').toggleClass('hidden');
			});
		});
			
		//リストのページ切り替え機能
		function ajax_pager(page){
			var url = window.location.search.substring(1);
			var row ='', order ='';
			if($('th.sortRow').hasClass('sorted')){
				row = $('th.sortRow.sorted').attr('id');
				order = $('th.sortRow.sorted').children('a').attr('class');
			}
			var col = $('ul.listCtrl li.active').attr('id');
			$.get('/admin/op-mailmaga/ajax.php?'+url+'&action=page&row='+row+'&order='+order+'&col='+col+'&page='+page,
				function(data){
					$('#list-wrap').html(data);
					if(row && order){
						$('#'+row).addClass('sorted');
						$('#'+row+' a').attr('class', order);
					}
					$('ul.listCtrl li#'+col).addClass('active');
				},
				'html'
			);
		};
		
		//詳細情報の表示機能
		function ajax_detail(id){
			if($('#list-wrap').hasClass('span12')){
				$('#list-wrap').removeClass('span12');
				$('#list-wrap').addClass('span8');
			}else{
				$('#detail-wrap').remove();
			}
			$.get('/admin/op-mailmaga/ajax.php?action=detail&id='+id,
				function(data){
					$('#list-wrap').after(data);
				},
				'html'
			);
			return false;
		};
		
		//テンプレート内容の表示機能
		function ajax_temp_detail(id){
			$.get('/admin/op-mailmaga/ajax.php?action=temp_detail&template_id='+id,
				function(data){
					$('#list-wrap').html(data);
					$('#list-wrap').attr('id', 'detail-wrap');
				},
				'html'
			);
			return false;
		};
		
		//テンプレートリストの表示機能
		function ajax_temp_list(){
			$.get('/admin/op-mailmaga/ajax.php?action=temp_list',
				function(data){
					$('#detail-wrap').html(data);
					$('#detail-wrap').attr('id', 'list-wrap');
				},
				'html'
			);
			return false;
		};
	-->
  </script>
</body>
</html>
