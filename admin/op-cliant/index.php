<?php
	/* /////////////////////////////////////////////////////
	//		顧客管理 INDEX
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
	$adminViewObj->operation = $opViewObj->operation = 'op-cliant';
	$opViewObj->mode = isset($_GET["mode"])? $_GET["mode"]: 'list';
	$opViewObj->state = isset($_GET["state"])? $_GET["state"]: '';
	$opViewObj->cliant_id = isset($_GET["id"])? $_GET["id"]: false;
	$opViewObj->group = isset($_GET["group"])? $_GET["group"]: false;
	$opViewObj->row = isset($_GET["row"])? $_GET["row"]: 'id';
	$opViewObj->order = isset($_GET["order"])? $_GET["order"]: false;
	$opViewObj->col = isset($_GET["col"])? $_GET["col"]: false;
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
			$('ul.listCtrl li#all').addClass('active');
			$('ul.listCtrl li a').live('click', function(){
				var url = window.location.search.substring(1);
				var col = $(this).parent('li').attr('id');
				$.get('/admin/op-cliant/ajax.php?'+url+'&action=tab&col='+col,
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
				$.get('/admin/op-cliant/ajax.php?'+url+'&action=sort&row='+row+'&order='+order+'&col='+col,
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
			//リストのグループセレクト
			$('div.groupSelect .dropdown').live('click', function() {
				$('div.groupSelect ul').toggle();
			});
			//リストの全チェック機能
			$('input[name=checkAll]').live('change', function() {
				$('#list-wrap table input[type=checkbox]').prop('checked', this.checked);
			});
			//リストのグループ登録
			$('button#groupAdd').live('click', function() {
				if($('#list-wrap table input[type=checkbox]').is(':checked')){
					document.myform.submit();
				}else{
					return false;
				}
			});
			
			/*
			//対応履歴のフォーム追加
			var history_num = 0;
			$('button#historyAdd').live('click', function() {
				var history_id ='';
				history_num++;
				var history_hidden = '<input type="hidden" name="history_caption['+history_num+']"><input type="hidden" name="history_value['+history_num+']">'
				$.get('/admin/op-cliant/ajax.php?action=history&history_id='+history_id+'&history_num'+history_num,
					function(data){
						$('#detail-wrap historyAdd').after(data);
						$('#edit-wrap form .formAction').before(history_hidden);
					},
					'html'
				);
				return false;
			});
			*/
			
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
			
			//詳細ウィンドウ位置のリセット処理
			var $window	= $(window);
			$window.scroll(function() {
				var $sidebar = $("#detail-wrap"),
					$row = $("#list-row"),
					rowOffset = $row.offset();
				if($window.scrollTop() <= rowOffset.top-30){
					$sidebar.stop().animate({marginTop:0});
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
			$.get('/admin/op-cliant/ajax.php?'+url+'&action=page&row='+row+'&order='+order+'&col='+col+'&page='+page,
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
			$.get('/admin/op-cliant/ajax.php?'+url+'&action=page&row='+row+'&order='+order+'&col='+col+'&page=0&limit_num='+limit_num,
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
			$.get('/admin/op-cliant/ajax.php?action=detail&id='+id,
				function(data){
					$('#list-wrap').after(data);
					//詳細情報の表示位置設定
					var $window	= $(window),
						rowOffset = $("#list-row").offset(),
						topPadding = 140;
					if($window.scrollTop() > rowOffset.top){
						$('#detail-wrap').css('margin-top', $window.scrollTop()-topPadding);
					}
				},
				'html'
			);
			return false;
		};
		
		//対応履歴情報の表示機能
		function ajax_history_detail(id){
			$('.historyEdit').remove();
			$.get('/admin/op-cliant/history_ajax.php?action=detail&id='+id,
				function(data){
					$('div.historyList').before(data);
				},
				'html'
			);
			return false;
		};
	-->
  </script>
</body>
</html>
