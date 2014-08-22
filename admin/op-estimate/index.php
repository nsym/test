<?php
	/* /////////////////////////////////////////////////////
	//		見積管理 INDEX
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
	$adminViewObj->operation = $opViewObj->operation = 'op-estimate';
	$opViewObj->mode = isset($_GET["mode"])? $_GET["mode"]: 'list';
	$opViewObj->state = isset($_GET["state"])? $_GET["state"]: '';
	$opViewObj->estimate_id = isset($_GET["id"])? $_GET["id"]: false;
	$opViewObj->cliant_id = isset($_GET["cliant_id"])? $_GET["cliant_id"]: false;
	$opViewObj->staff = isset($_GET["staff"])? $_GET["staff"]: false;
	$opViewObj->row = isset($_GET["row"])? $_GET["row"]: 'id';
	$opViewObj->order = isset($_GET["order"])? $_GET["order"]: false;
	$opViewObj->col = isset($_GET["col"])? $_GET["col"]: 'wait';
	$opViewObj->page = isset($_GET['page'])? $_GET['page']: 0;
	$opViewObj->limit_num = isset($_SESSION['limit_num'])? $_SESSION['limit_num']: 25;

	
	
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
			$('ul.listCtrl li#wait').addClass('active');
			$('ul.listCtrl li a').live('click', function(){
				var url = window.location.search.substring(1);
				var col = $(this).parent('li').attr('id');
				$.get('/admin/op-estimate/ajax.php?'+url+'&action=tab&col='+col,
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
				$.get('/admin/op-estimate/ajax.php?'+url+'&action=sort&row='+row+'&order='+order+'&col='+col,
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
			
			//リストの絞り込み検索表示
			$('button[name=searchBtn]').live('click', function() {
				$('#list-wrap #searchWrap form').slideToggle();
			});
			//リストのスタッフセレクト
			$('div.staffSelect .dropdown').live('click', function() {
				$('div.staffSelect ul').toggle();
			});
			//リストの全チェック機能
			$('input[name=checkAll]').live('change', function() {
				$('#list-wrap table input[type=checkbox]').prop('checked', this.checked);
			});
			//リストの一括ステータス変更
			$('button#statusChange').live('click', function() {
				if($('#list-wrap table input[type=checkbox]').is(':checked')){
					document.myform.submit();
				}else{
					return false;
				}
			});
			
			//エリアのテキストボックス表示
			var area_value = $('select[name=area] option:selected').val();
			if(area_value!='except'){
				$('input[name=area_text]').hide();
			}
			$('select[name=area]').change(function(){
				area_value = $(this).val();
				if(area_value=='except'){
					$('input[name=area_text]').show();
				}else{
					$('input[name=area_text]').hide();
				}
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
			$.get('/admin/op-estimate/ajax.php?'+url+'&action=page&row='+row+'&order='+order+'&col='+col+'&page='+page,
				function(data){
					$('#list-wrap').html(data);
					if(row!='' && order!=''){
						$('#'+row).addClass('sorted');
						$('#'+row+' a').attr('class', order);
					}
					$('ul.listCtrl li#'+col).addClass('active');
				},
				'html'
			);
		};
			
		//リストのページ表示数切り替え機能
		function ajax_pagelimit(){
			var limit_num = $('#limit_select').val();
			var url = window.location.search.substring(1);
			var row ='', order ='';
			if($('th.sortRow').hasClass('sorted')){
				row = $('th.sortRow.sorted').attr('id');
				order = $('th.sortRow.sorted').children('a').attr('class');
			}
			var col = $('ul.listCtrl li.active').attr('id');
			$.get('/admin/op-estimate/ajax.php?'+url+'&action=page&row='+row+'&order='+order+'&col='+col+'&page=0&limit_num='+limit_num,
				function(data){
					$('#list-wrap').html(data);
					if(row!='' && order!=''){
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
			$.get('/admin/op-estimate/ajax.php?action=detail&id='+id,
				function(data){
					$('#list-wrap').after(data);
				},
				'html'
			);
			return false;
		};
		
		//顧客ID検索の検索機能
		function ajax_search_cliant(){
			var search_field = $('select[name=search_field]').val();
			var search_keyword = $('input[name=search_keyword]').val();
			if(search_keyword=='' || search_keyword=='undefined'){
				alert('キーワードを入力してください');
			}else{
				$('#detail-wrap').remove();
				$.get('/admin/op-estimate/ajax.php?action=search&search_field='+search_field+'&search_keyword='+search_keyword,
					function(data){
						$('#new-wrap').after(data);
					},
					'html'
				);
			}
			return false;
		};
		
		function set_cid(cid){
			$('#new-wrap input[name=cliant_id]').val(cid);
		}
		
		//受注処理ボタンの確認アラート
		$('button[name=accept]').live('click', function(){
			var txt = '受注処理をしてもよろしいですか？';
			if(confirm(txt)){
				return true;
			}
			else{
				return false;
			}
		});
	-->
  </script>
</body>
</html>
