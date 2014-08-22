<?php
	/* /////////////////////////////////////////////////////
	//		見積管理 共通VIEWパッケージ
	//////////////////////////////////////////////////////*/
	
	////////////////////////////////////////////////////////
	//
	//	#substance	パッケージの作成
	//  #Date		2013/12/11
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	//
	//	#substance	期間絞り込みの追加
	//  #Date		2014/01/10
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	
//--------------------------------------------------------------------
require_once("../db/op-estimate/estimate.php");
require_once("../db/op-estimate/cliant.php");
require_once("../db/op-user/user.php");
	
	class View extends Estimate{
		
		public $operation, $mode, $state, $estimate_id, $cliant_id, $staff, $row ,$order, $col, $start, $limit_num ,$orderby;
		
		///--------------------------------------------------------------------
		/// 【コンストラクター】
		///
		/// 戻り値　なし
		///
		///	#Author yk
		/// #date	2013/12/11
		///--------------------------------------------------------------------
		function __construct(){
			//見積管理の初期設定
			$this->menu_list = array(
				'list' => array('MENU_NAME'=>'見積一覧','MENU_ICON'=>'th-list', 'MENU_AUTHORITY'=>'administrator,staff'),
				'new' => array('MENU_NAME'=>'見積登録','MENU_ICON'=>'plus', 'MENU_AUTHORITY'=>'administrator,staff'),
				'edit' => array('MENU_NAME'=>'見積情報編集','MENU_ICON'=>'th-list', 'MENU_AUTHORITY'=>'', 'PARENT_KEY'=>'list', 'PARENT_NAME'=>'一覧'),
				'detail' => array('MENU_NAME'=>'登録内容','MENU_ICON'=>'th-list', 'MENU_AUTHORITY'=>'', 'PARENT_KEY'=>'list', 'PARENT_NAME'=>'一覧'),
			);
			$this->state_message = array(
				'insert-ok' => array('STATE_COLOR'=>'green', 'STATE_MESSAGE'=>'保存処理が完了いたしました。'),
				'insert-ng' => array('STATE_COLOR'=>'red', 'STATE_MESSAGE'=>'保存処理に失敗いたしました。'),
				'update-ok' => array('STATE_COLOR'=>'green', 'STATE_MESSAGE'=>'更新処理が完了いたしました。'),
				'update-ng' => array('STATE_COLOR'=>'red', 'STATE_MESSAGE'=>'更新処理に失敗いたしました。'),
				'trash-ok' => array('STATE_COLOR'=>'green', 'STATE_MESSAGE'=>'削除処理が完了いたしました。'),
				'trash-ng' => array('STATE_COLOR'=>'red', 'STATE_MESSAGE'=>'削除処理に失敗いたしました。'),
				'delete-ok' => array('STATE_COLOR'=>'green', 'STATE_MESSAGE'=>'削除処理が完了いたしました。'),
				'delete-ng' => array('STATE_COLOR'=>'red', 'STATE_MESSAGE'=>'削除処理に失敗いたしました。'),
				'input-ng' => array('STATE_COLOR'=>'red', 'STATE_MESSAGE'=>'入力されていない項目があります'),
				'cliant-ng' => array('STATE_COLOR'=>'red', 'STATE_MESSAGE'=>'登録されていない顧客IDです'),
				'status-ok' => array('STATE_COLOR'=>'green', 'STATE_MESSAGE'=>'ステータス変更処理が完了いたしました。'),
				'status-ng' => array('STATE_COLOR'=>'red', 'STATE_MESSAGE'=>'ステータス変更処理に失敗いたしました。'),
				'image-ng' => array('STATE_COLOR'=>'red', 'STATE_MESSAGE'=>'ファイルの保存処理に失敗いたしました。'),
				'accept-ok' => array('STATE_COLOR'=>'green', 'STATE_MESSAGE'=>'受注処理が完了いたしました。'),
				'accept-ng' => array('STATE_COLOR'=>'red', 'STATE_MESSAGE'=>'受注処理に失敗いたしました。'),
			);
		}
		
		///--------------------------------------------------------------------
		/// サイドバーの生成
		///
		///	#Author yk
		/// #date	2013/12/11
		///--------------------------------------------------------------------
		function setSideBar(){
			
			foreach ($this->menu_list as $key => $value){
				if($value["MENU_AUTHORITY"]!=''){
					$active = ($this->mode==$key)? 'active': '';
					if(!$this->menu_list[$this->mode]["MENU_AUTHORITY"] && $key==$this->menu_list[$this->mode]["PARENT_KEY"]){ $active ='active'; }
					$li_html.='<li class="'.$active.'"><a href="?mode='.$key.'"><i class="icon-'.$value["MENU_ICON"].'"></i>'.$value["MENU_NAME"].'</a></li>';
				}
			}
			
			echo $li_html;
		}
		
		///--------------------------------------------------------------------
		/// コンテンツの生成
		///
		///	#Author yk
		/// #date	2013/12/11
		///--------------------------------------------------------------------
		function setContents(){
			
			global $op_list;
			
			$parent_bread ='';
			//ステートの設定
			$state_html = $this->stateMessage();
			
			//見積一覧表示の場合
			if($this->mode=='list'){
				//見積リストの取得
				$master_list = $this->setMasterList();
				$list_count = $this->setListCount();
				//見積リストの生成
				$contents_html ='
					<div class="row-fluid r_txt">
						<a href="./?mode=new" class="newBtn"><i class="icon-plus"></i>新規見積登録</a>
					</div>
					<div class="row-fluid">
						<div id="'.$this->mode.'-wrap" class="span12">
							'.$this->listContents($master_list, $list_count).'
						</div>
					</div>
				';
			}
			//見積の新規作成・編集の場合
			else if($this->mode=='new' || $this->mode=='edit'){
				//編集の場合
				if($this->mode=='edit' && $this->estimate_id){
					//見積情報の取得
					$master_data = parent::getEstimateMasterForID($this->estimate_id);
					$parent_bread = '<li><a href="/admin/op-estimate/?mode='.$this->menu_list[$this->mode]["PARENT_KEY"].'">'.$this->menu_list[$this->mode]["PARENT_NAME"].'</a></li>';
					$assist_wrap = $this->acceptEstimateContents($master_data);
				}
				//新規保存の場合
				else{
					$master_data ='';
					$assist_wrap = $this->searchCliantContents($search_field, $search_keyword);
				}
				//見積リストの生成
				$contents_html ='
					<div class="row-fluid">
						'.$this->editContents($master_data).'
						'.$assist_wrap.'
					</div>
				';
			}
				
			$html ='
				<h2>'.$op_list[$this->operation]["OP_NAME"].'</h2>
				<div class="breadcrumb">
					<ul>
						<li><a href="/admin'.$op_list[$this->operation]["OP_FOLDER"].'">'.$op_list[$this->operation]["OP_NAME"].'</a></li>
						'.$parent_bread.'
						<li><span>'.$this->menu_list[$this->mode]["MENU_NAME"].'</span></li>
					</ul>
				</div>
				<div class="stateMessage">
					'.$state_html.'
				</div>
				'.$contents_html.'
			';

			echo $html;
			
		}
		
		///--------------------------------------------------------------------
		/// ステートメッセージの生成
		///
		///	#Author yk
		/// #date	2013/05/24
		///--------------------------------------------------------------------
		function stateMessage(){
			
			//ステートメッセージHTMLの生成
			if(!empty($this->state)){
				$html ='<p class="'.$this->state_message[$this->state]["STATE_COLOR"].'">'.$this->state_message[$this->state]["STATE_MESSAGE"].'</p>';
			}
			
			return $html;
			
		}
		
		///--------------------------------------------------------------------
		/// 見積リストの取得
		///
		///	#Author yk
		/// #date	2013/12/11
		///	#Author yk
		/// #date	2014/01/10
		///--------------------------------------------------------------------
		function setMasterList(){
			
				//見積リストの取得（スタッフ検索）
				if(!empty($this->staff)){
					$master_list = parent::searchEstimateMasterForstaffID($this->staff, 'MASTER_STATUS', $this->col, $this->orderby, $this->page*$this->limit_num, $this->limit_num);
				}
				//見積リストの取得（条件検索）
				else if(isset($_GET["condition"])){
					$this->condition = array(
						'start_date' => date('Y-m-d H:i:s', mktime(0, 0, 0, $_GET["start_month"], $_GET["start_day"], $_GET["start_year"])),
						'end_date' => date('Y-m-d H:i:s', mktime(0, 0, 0, $_GET["end_month"], $_GET["end_day"], $_GET["end_year"])),
						'field' => 'MASTER_STATUS',
						'col' => $this->col,
						'orderby' => $this->orderby,
						'start' => $this->page*$this->limit_num,
						'limit_num' => $this->limit_num,
					);
					$master_list = parent::searchEstimateMasterForCondition($this->condition);
				}
				//見積リストの取得（通常）
				else{
					$master_list = parent::searchEstimateMasterList('MASTER_STATUS', $this->col, $this->orderby, $this->page*$this->limit_num, $this->limit_num);
				}
				
			return $master_list;
		}
		
		///--------------------------------------------------------------------
		/// 見積リスト数の取得
		///
		///	#Author yk
		/// #date	2013/12/11
		///	#Author yk
		/// #date	2014/01/10
		///--------------------------------------------------------------------
		function setListCount(){
			
				//見積リストの取得（スタッフ検索）
				if(!empty($this->staff)){
					$list_count = parent::countEstimateMasterForstaffID($this->staff, 'MASTER_STATUS', $this->col);
				}
				//見積リストの取得（条件検索）
				else if(isset($_GET["condition"])){
					$this->condition = array(
						'start_date' => date('Y-m-d H:i:s', mktime(0, 0, 0, $_GET["start_month"], $_GET["start_day"], $_GET["start_year"])),
						'end_date' => date('Y-m-d H:i:s', mktime(0, 0, 0, $_GET["end_month"], $_GET["end_day"], $_GET["end_year"])),
						'field' => 'MASTER_STATUS',
						'col' => $this->col,
						'orderby' => $this->orderby,
						'start' => $this->page*$this->limit_num,
						'limit_num' => $this->limit_num,
					);
					$list_count = parent::countEstimateMasterForCondition($this->condition);
				}
				//見積リストの取得（通常）
				else{
					$list_count = parent::countEstimateMasterList('MASTER_STATUS', $this->col);
				}
				
			return $list_count;
		}
		
		///--------------------------------------------------------------------
		/// コンテンツの生成（リスト）
		///
		///	#Author yk
		/// #date	2013/12/11
		///	#Author yk
		/// #date	2014/01/10
		///--------------------------------------------------------------------
		function listContents($master_list, $list_count){
			
			$cliantObj = new Cliant();
			
			//スタッフセレクトの設定
			$staffObj = new User();
			$staff_list = $staffObj->getStaffList();
			$staff_select ='';
			foreach($staff_list as $value){
				$staff_select.='<li><a href="/admin/op-estimate/?mode=list&staff='.$value["ID"].'">'.$value["DISPLAY_NAME"].'</a></li>';
			}
			
			//条件検索HTMLの生成
			$condition_html = $this->conditionSearch();
			
			//検索条件の表示
			$search_text = '';
			if(isset($_GET["staff"])){
				$staff_data = $staffObj->getStaffForID($_GET["staff"]);
				$search_text = '<p class="searchText">検索条件：'.$staff_data["DISPLAY_NAME"].'</p>';
			}else if(isset($_GET["condition"])){
				extract($this->condition);
				$search_text = '<p class="searchText">検索条件：「'.date('Y年n月j日', strtotime($start_date)).'から'.date('Y年n月j日', strtotime($end_date)).'まで」</p>';
			}
			
			//リストコントロールの生成
			global $estimate_status_list;
			$ctrl_html ='';
			foreach($estimate_status_list as $key=>$value){
				$ctrl_html.='<li id="'.$key.'"><a href="javascript:;">'.$value.'</a></li>';
			}
			
			//リストHTMLの生成
			$list_html ='';
			foreach($master_list as $value){
				//見積期限の設定
				$deadline ='';
				if($this->col==='wait'){
					$limit_day = ceil((strtotime($value["MASTER_LIMIT_DATE"]) - strtotime('today')) / (60*60*24));
					if($limit_day<0){
						$deadline ='<span class="expiration">期限切れ</span>';
					}else if($limit_day==0){
						$deadline ='<span class="deadline">本日まで</span>';
					}else if($limit_day<10){
						$deadline ='<span class="deadline">期限まで '.$limit_day.'日</span>';
					}
				}
				//提出先の設定
				$cliant_data = $cliantObj->getCliantMasterForID($value["CLIANT_ID"]);
				$cliant_name = $cliant_data["MASTER_NAME"].'（'.$cliant_data["MASTER_COMPANY"].'）';
				//スタッフの設定
				if(!empty($value["STAFF_ID"])){
					$staff_data = $staffObj->getStaffForID($value["STAFF_ID"]);
					$staff_name = $staff_data["DISPLAY_NAME"];
				}else{
					$staff_name = '未設定';
				}
				$list_html.='
					<tr>
						<td><label><input type="checkbox" name="check_id[]" value="'.$value["ID"].'">'.date('Y年m月j日', strtotime($value["MASTER_DATE"])).'</label></td>
						<th>'.$deadline.$value["MASTER_TITLE"].'</th>
						<th>'.$cliant_name.'</th>
						<th>'.$staff_name.'</th>
						<td>
							<a class="Btn detailBtn" onClick="ajax_detail(\''.$value["ID"].'\')"><i class="icon-arrow-right"></i><span>確認</span></a>
						</td>
					</tr>
				';
			}
			if(empty($master_list)){
				$list_html ='<tr><th colspan="3" class="no_list">該当する見積情報がありません。</th></tr>';
			}
			
			//ステータス変更の設定
			$status_change ='';
			foreach($estimate_status_list as $key=>$value){
				$status_change.='<option value="'.$key.'">'.$value.'</option>';
			}
			
			//ページャーの生成
			global $adminViewObj;
			$url ='&row='.$this->row.'&order='.$this->order.'&col='.$this->col;
			$pager_html = $adminViewObj->pager($list_count, $this->page, $this->limit_num, $url);
			$start = $this->page*$this->limit_num;
			$end = $start + count($master_list);
			
			//表示件数切り替えの生成
			$limit_select ='';
			for($i=25; $i<=100; $i=$i*2){
				$selected = ($this->limit_num==$i)? 'selected': '';
				$limit_select.='<option value="'.$i.'" '.$selected.'>'.$i.'件表示</option>';
			}
			
			//コンテンツHTMLの生成
			$html ='
				<div class="contentTitle">
					<h3>'.$this->menu_list['list']["MENU_NAME"].'</h3>
				</div>
				<div class="contentBody">
					
					<div class="headText">
						<h4>List in<span>&nbsp'.$list_count.'&nbsp</span>estimates ( '.($start+1).' to '.$end.' )</h4>
						<button type="button" name="searchBtn" value=""><i class="icon-search"></i>期間検索</button>
						<div class="staffSelect" >
							<button type="button" class="btn"><i class="icon-user"></i>スタッフ検索</button>
							<button type="button" class="dropdown"><i class="icon-caret-down"></i></button>
							<ul>
								<li><a href="/admin/op-estimate/?mode=list">全て</a></li>
								'.$staff_select.'
							</ul>
						</div>
						'.$search_text.'
					</div>
					'.$condition_html.'
					<ul class="listCtrl">
						'.$ctrl_html.'
					</ul>
					<form action="post.php" method="get" name="myform">
						<table>
							<thead>
								<tr>
									<th id="master_date" class="sortRow">見積作成日<a href="javascript:;" class="icon-sort"></a></th>
									<th class="min200">見積件名</th>
									<th class="min100">提出先</th>
									<th id="staff_id" class="min100 sortRow">担当スタッフ<a href="javascript:;" class="icon-sort"></a></th>
									<td>操作</td>
								</tr>
							</thead>
							<tbody>
								'.$list_html.'
							</tbody>
						</table>
						<div style="overflow:hidden">
							<div class="checkWrap">
								<label><input type="checkbox" name="checkAll">全てチェックする</label>
								<select name="status_change">
									'.$status_change.'
								</select>
								<input type="hidden" name="action" value="status">
								<button type="button" id="statusChange">ステータス変更</button>
							</div>
							'.$pager_html.'							
							<select id="limit_select" name="limit_select" onChange="ajax_pagelimit();">
								'.$limit_select.'
							</select>
						</div>
					</from>
				</div>
			';
			
			
			return $html;
			
		}
		
		///--------------------------------------------------------------------
		/// コンテンツの生成（条件検索部分）
		///
		///	#Author yk
		/// #date	2014/01/10
		///--------------------------------------------------------------------
		function conditionSearch(){
			
			//検索条件の取得
			if(isset($this->condition)){
				extract($this->condition);
			}else{
				$area =$mailmaga =$ipros =$start_date =$end_date ='';
			}
			
			//見積作成日絞り込みの設定
			$year_select = $month_select = $day_select = array('');
			if(empty($start_date)){
				$start_date = date('Y-m-d', mktime(0, 0, 0, 1, 1, 2013));
			}
			if(empty($end_date)){
				$end_date = date('Y-m-d', strtotime('+1 day'));
			}
			list($start_year, $start_month, $start_day) = explode("-", $start_date);
			list($end_year, $end_month, $end_day) = explode("-", $end_date);
			for($i=date('Y'); $i>=2013; $i--){
				$selected = ($i==$start_year)? 'selected': '';
				$year_select["start"].='<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
				$selected = ($i==$end_year)? 'selected': '';
				$year_select["end"].='<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
			}
			for($i=1; $i<=12; $i++){
				$selected = ($i==$start_month)? 'selected': '';
				$month_select["start"].='<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
				$selected = ($i==$end_month)? 'selected': '';
				$month_select["end"].='<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
			}
			for($i=1; $i<=31; $i++){
				$selected = ($i==$start_day)? 'selected': '';
				$day_select["start"].='<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
				$selected = ($i==$end_day)? 'selected': '';
				$day_select["end"].='<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
			}
			
			$html ='
				<div id="searchWrap">			
					<form action="" method="get">
						<fieldset class="date">
							<legend>見積作成日</legend>
							<div class="mb10">
								<select name="start_year">'.$year_select["start"].'</select>年<select name="start_month">'.$month_select["start"].'</select>月<select name="start_day">'.$day_select["start"].'</select>日 から
							</div>
							<div>
								<select name="end_year">'.$year_select["end"].'</select>年<select name="end_month">'.$month_select["end"].'</select>月<select name="end_day">'.$day_select["end"].'</select>日 まで
							</div>
						</fieldset>
						<div class="searchAction">
							<button type="submit" name="condition" value="search">絞り込み検索</button>
						</div>
					</form>
				</div>
			';
			
			return $html;
		}
		
		///--------------------------------------------------------------------
		/// コンテンツの生成（エディット）
		///
		///	#Author yk
		/// #date	2013/12/11
		///--------------------------------------------------------------------
		function editContents($master_data){
			
			
			//担当スタッフの設定		
			global $authObj;
			$staffObj = new User();
			$staff_list = $staffObj->getStaffList();
			$staff_select = '<option value="0">未設定</option>';
			foreach($staff_list as $value){
				//セレクトボックス初期値設定
				if($this->mode==='new' && $authObj->admin_data["ID"]==$value['ID']){
					$selected ='selected';
				}else if($master_data['STAFF_ID']==$value['ID']){
					$selected ='selected';
				}else{
					$selected ='';
				}
				$staff_select.='<option value="'.$value['ID'].'" '.$selected.'>'.$value['DISPLAY_NAME'].'</option>';
			}
			
			//見積ステータスの設定
			global $estimate_status_list;
			$status_select ='';
			foreach($estimate_status_list as $key=>$value){
				$selected = ($master_data['MASTER_STATUS']==$key)? 'selected': '';
				if(empty($master_data['MASTER_STATUS']) && $key=='wait'){
					$selected ='selected';
				}
				$status_select.='<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
			}
			
			//見積提出先の設定
			if(!empty($master_data['CLIANT_ID'])){
				$this->cliant_id = $master_data['CLIANT_ID'];
			}
			$cliant_name ='';
			if(!empty($this->cliant_id)){
				$cliantObj = new Cliant();
				$cliant_data = $cliantObj->getCliantMasterForID($this->cliant_id);
				$cliant_name = $cliant_data["MASTER_NAME"].'（'.$cliant_data["MASTER_COMPANY"].'）';
				$cliant_id = sprintf('%05d', $this->cliant_id);
			}
			
			//見積作成日の設定
			$year_select = $month_select = $day_select = array();
			if(empty($master_data)){
				$master_data["MASTER_DATE"] = date('Y-m-d');
				$master_data["MASTER_LIMIT_DATE"] = date('Y-m-d', strtotime('+1 month'));
			}
			$master_date = date_parse($master_data["MASTER_DATE"]);
			$limit_date = date_parse($master_data["MASTER_LIMIT_DATE"]);
			
			for($i=date('Y'); $i<=date('Y')+1; $i++){
				$selected = ($i==$master_date["year"])? 'selected': '';
				$year_select[0].='<option value="'.$i.'" '.$selected.'>'.$i.'年</option>';
				$selected = ($i==$limit_date["year"])? 'selected': '';
				$year_select[1].='<option value="'.$i.'" '.$selected.'>'.$i.'年</option>';
			}
			for($i=1; $i<=12; $i++){
				$selected = ($i==$master_date["month"])? 'selected': '';
				$month_select[0].='<option value="'.$i.'" '.$selected.'>'.$i.'月</option>';
				$selected = ($i==$limit_date["month"])? 'selected': '';
				$month_select[1].='<option value="'.$i.'" '.$selected.'>'.$i.'月</option>';
			}
			for($i=1; $i<=31; $i++){
				$selected = ($i==$master_date["day"])? 'selected': '';
				$day_select[0].='<option value="'.$i.'" '.$selected.'>'.$i.'日</option>';
				$selected = ($i==$limit_date["day"])? 'selected': '';
				$day_select[1].='<option value="'.$i.'" '.$selected.'>'.$i.'日</option>';
			}
			
			//見積書ファイルの設定（プレビュー）
			$file_html =array();
			for($i=1; $i<=3; $i++){
				if(!empty($master_data["MASTER_FILE_".$i])){
					$file_html[$i] = '
						<a href="'.FILE_FOLDER.$master_data["MASTER_FILE_".$i].'" target="_blank" class="previewLink"><i class="icon-file"></i>ファイル確認</a>
						<label><input type="checkbox" name="file_delete_'.$i.'">ファイルの削除</label>
						<input type="hidden" name="file_'.$i.'" value="'.$master_data["MASTER_FILE_".$i].'"><br>
					';
				}
			}
			
			//ボタンの設定
			if($this->mode==='new'){
				$btn_html = '<button type="submit" name="'.$this->mode.'" class="grayBtn"><i class="icon-ok"></i>保存</button><button type="reset">リセット</button>';
			}
			else{
				$btn_html = '<button type="submit" name="'.$this->mode.'"><i class="icon-refresh"></i>更新</button><!--button type="submit" name="signed"><i class="icon-check"></i>受注処理</button-->';
			}
			
			
			//入力フォームの生成
			$html ='
				<div id="'.$this->mode.'-wrap" class="span8">
					<div class="contentTitle">
						<h3>'.$this->menu_list[$this->mode]["MENU_NAME"].'</h3>
					</div>
					<form action="post.php" method="post"enctype="multipart/form-data" name="editForm" >
						<fieldset>
							<legend>見積状況</legend>
							<div>
								<select name="status">
									'.$status_select.'
								</select>
							</div>
						</fieldset>
						<fieldset>
							<legend>見積スタッフ</legend>
							<div>
								<select name="staff_id">
									'.$staff_select.'
								</select>
							</div>
						</fieldset>
						<fieldset>
							<legend>見積作成日</legend>
							<div>
								<select name="master_date_array[]" style="width: 80px;">
									'.$year_select[0].'
								</select>
								<select name="master_date_array[]" style="width: 60px;">
									'.$month_select[0].'
								</select>
								<select name="master_date_array[]" style="width: 60px;">
									'.$day_select[0].'
								</select>
							</div>
						</fieldset>
						<fieldset>
							<legend>見積期限日</legend>
							<div>
								<select name="limit_date_array[]" style="width: 80px;">
									'.$year_select[1].'
								</select>
								<select name="limit_date_array[]" style="width: 60px;">
									'.$month_select[1].'
								</select>
								<select name="limit_date_array[]" style="width: 60px;">
									'.$day_select[1].'
								</select>
							</div>
						</fieldset>
						<fieldset>
							<legend>見積提出先</legend>
							<div>
								顧客ID：<input type="text" name="cliant_id" value="'.$cliant_id.'">　'.$cliant_name.'<span class="hissu">必須</span><br>
								<span class="form_annotation">※顧客IDを半角数字で入力してください</span><br>
							</div>
						</fieldset>
						<fieldset>
							<legend>見積件名</legend>
							<div>
								<input type="text" name="title" value="'.$master_data["MASTER_TITLE"].'"><span class="hissu">必須</span>
							</div>
						</fieldset>
						<fieldset>
							<legend>見積金額</legend>
							<div>
								<input type="text" name="price" value="'.$master_data["MASTER_PRICE"].'">円<span class="hissu">必須</span><br>
								<span class="form_annotation">※半角数字・コンマなしで記入してください</span>
							</div>
						</fieldset>
						<fieldset>
							<legend>見積書ファイル(1)</legend>
							<div>
								'.$file_html[1].'
								<input type="file" name="file_1">
							</div>
						</fieldset>
						<fieldset>
							<legend>見積書ファイル(2)</legend>
							<div>
								'.$file_html[2].'
								<input type="file" name="file_2">
							</div>
						</fieldset>
						<fieldset>
							<legend>見積書ファイル(3)</legend>
							<div>
								'.$file_html[3].'
								<input type="file" name="file_3">
							</div>
						</fieldset>
						<fieldset>
							<legend>見積に関する備考</legend>
							<div>
								<textarea name="body">'.$master_data['MASTER_BODY'].'</textarea>
							</div>
						</fieldset>
						<input type="hidden" name="request_id" value="'.$master_data["REQUEST_ID"].'">
						<input type="hidden" name="id" value="'.$master_data["ID"].'">
						<div class="formAction">
							'.$btn_html.'
						</div>
					</form>
				</div>
			';
			
			return $html;
			
		}
		
		///--------------------------------------------------------------------
		/// コンテンツの生成（内容確認）
		///
		///	#Author yk
		/// #date	2013/12/11
		///--------------------------------------------------------------------
		function detailContents($master_data){
			
			global $estimate_status_list;
			
			//担当スタッフの設定
			$staffObj = new User();
			$staff ='未設定';
			if(!empty($master_data["STAFF_ID"])){
				$staff_data = $staffObj->getStaffForID($master_data["STAFF_ID"]);
				$staff = $staff_data["DISPLAY_NAME"];
			}
			
			//顧客の設定
			$cliantObj = new Cliant();
			$cliant_data = $cliantObj->getCliantMasterForID($master_data["CLIANT_ID"]);
			$cliant_name = $cliant_data["MASTER_NAME"].'（'.$cliant_data["MASTER_COMPANY"].'）';
			
			$html ='
				<div id="detail-wrap" class="span4">
					<div class="contentTitle">
						<h3>登録内容</h3>
						<span>
							<a href="javascript:close_wrap(\'#detail-wrap\');" class="icon-remove"></a>
						</span>
					</div>
					<form action="./" method="get">
						<fieldset>
							<legend>見積状況</legend>
							<p>'.$estimate_status_list[$master_data["MASTER_STATUS"]].'</p>
						</fieldset>
						<fieldset>
							<legend>見積スタッフ</legend>
							<p>'.$staff.'</p>
						</fieldset>
						<fieldset>
							<legend>見積作成日</legend>
							<p>'.date('Y年n月j日', strtotime($master_data["MASTER_DATE"])).'</p>
						</fieldset>
						<fieldset>
							<legend>見積期限日</legend>
							<p>'.date('Y年n月j日', strtotime($master_data["MASTER_LIMIT_DATE"])).'</p>
						</fieldset>
						<fieldset>
							<legend>見積提出先</legend>
							<p>'.$cliant_name.'</p>
						</fieldset>
						<fieldset>
							<legend>見積件名</legend>
							<p>'.$master_data["MASTER_TITLE"].'</p>
						</fieldset>
						<fieldset>
							<legend>見積金額</legend>
							<p>'.number_format($master_data["MASTER_PRICE"]).'円</p>
						</fieldset>
						<fieldset>
							<legend>見積に関する備考</legend>
							<p>'.nl2br($master_data["MASTER_BODY"]).'</p>
						</fieldset>
						<fieldset>
							<legend>編集日時</legend>
							<p>'.date('Y年n月j日 H:i:s', strtotime($master_data["EDIT_DATE"])).'</p>
						</fieldset>
						<input type="hidden" name="id" value="'.$master_data["ID"].'">
						<div class="formAction">
							<button type="submit" name="mode" value="edit"><i class="icon-pencil"></i>編集する</button>
						</div>
					</form>
				</div>
			';
			
			return $html;
			
		}
		
		///--------------------------------------------------------------------
		/// コンテンツの生成（顧客ID検索）
		///
		///	#Author yk
		/// #date	2013/12/11
		///--------------------------------------------------------------------
		function searchCliantContents($search_field, $search_keyword){
			
			$cliantObj = new Cliant();
			$list_html ='';
			if(!empty($search_field) && !empty($search_keyword)){
				$cliant_list = $cliantObj->searchKeywordCliantMaster($search_field, $search_keyword);
				foreach($cliant_list as $value){
					$list_html.='
						<tr>
							<td>'.sprintf('%05d', $value["ID"]).'</td>
							<td>'.$value["MASTER_NAME"].'</td>
							<td>'.$value["MASTER_COMPANY"].'</td>
							<td><button type="button" Onclick="set_cid(\''.sprintf('%05d', $value["ID"]).'\');">ID入力</button></td>
						</tr>
					';
				}
			}
			else{
				$list_html ='<tr><td colspan="3"><p class="no_keyword">キ−ワードから顧客IDが検索できます。</p></td></tr>';
			}
			
			
			$html ='
				<div id="detail-wrap" class="span4">
					<div class="contentTitle">
						<h3>顧客ID検索</h3>
					</div>
					<form action="" method="get" id="searchCliant">
						<select name="search_field">
							<option value="MASTER_NAME">顧客名</option>
							<option value="MASTER_COMPANY">企業・団体</option>
						</select>
						<input type="text" name="search_keyword" value="'.$search_keyword.'">
						<button type="button" onClick="ajax_search_cliant()">検索</button>
					</form>
					<table class="cliantList">
						<thead>
							<tr>
								<td>顧客ID</td>
								<td>顧客名</td>
								<td>企業・団体</td>
								<td></td>
							</tr>
						</thead>
						<tbody>
							'.$list_html.'
						</tbody>
					</table>
				</div>
			';
			
			return $html;
			
		}
		
		///--------------------------------------------------------------------
		/// コンテンツの生成（受注処理）
		///
		///	#Author yk
		/// #date	2013/12/13
		///--------------------------------------------------------------------
		function acceptEstimateContents($master_data){
			
			global $estimate_status_list;
			
			//受注スタッフの設定
			$staffObj = new User();
			$staff_list = $staffObj->getStaffList();								
			$staff_select = '<option value="0">未設定</option>';
			foreach($staff_list as $value){
				//セレクトボックス初期値設定
				$selected = ($master_data['STAFF_ID']==$value['ID'])? 'selected': '';
				$staff_select.='<option value="'.$value['ID'].'" '.$selected.'>'.$value['DISPLAY_NAME'].'</option>';
			}
			
			//受注日の設定
			$year_select = $month_select = $day_select = array();
			$accept_date = date_parse(date('Y-m-d'));
			$limit_date = date_parse(date('Y-m-d', strtotime('+1 month')));
			
			for($i=date('Y'); $i<=date('Y')+1; $i++){
				$selected = ($i==$accept_date["year"])? 'selected': '';
				$year_select[0].='<option value="'.$i.'" '.$selected.'>'.$i.'年</option>';
				$selected = ($i==$limit_date["year"])? 'selected': '';
				$year_select[1].='<option value="'.$i.'" '.$selected.'>'.$i.'年</option>';
			}
			for($i=1; $i<=12; $i++){
				$selected = ($i==$accept_date["month"])? 'selected': '';
				$month_select[0].='<option value="'.$i.'" '.$selected.'>'.$i.'月</option>';
				$selected = ($i==$limit_date["month"])? 'selected': '';
				$month_select[1].='<option value="'.$i.'" '.$selected.'>'.$i.'月</option>';
			}
			for($i=1; $i<=31; $i++){
				$selected = ($i==$accept_date["day"])? 'selected': '';
				$day_select[0].='<option value="'.$i.'" '.$selected.'>'.$i.'日</option>';
				$selected = ($i==$limit_date["day"])? 'selected': '';
				$day_select[1].='<option value="'.$i.'" '.$selected.'>'.$i.'日</option>';
			}
			
			//顧客の設定
			$cliantObj = new Cliant();
			$cliant_data = $cliantObj->getCliantMasterForID($master_data["CLIANT_ID"]);
			$cliant_name = $cliant_data["MASTER_NAME"].'（'.$cliant_data["MASTER_COMPANY"].'）';
			
			$html ='
				<div id="detail-wrap" class="span4">
					<div class="contentTitle">
						<h3>受注処理</h3>
					</div>
					<form action="./accept_post.php" method="post">
						<fieldset>
							<legend>受注スタッフ</legend>
							<div>
								<select name="staff_id">
									'.$staff_select.'
								</select>
							</div>
						</fieldset>
						<fieldset>
							<legend>受注日</legend>
							<div>
								<select name="accept_date_array[]" style="width: 80px;">
									'.$year_select[0].'
								</select>
								<select name="accept_date_array[]" style="width: 60px;">
									'.$month_select[0].'
								</select>
								<select name="accept_date_array[]" style="width: 60px;>
									'.$day_select[0].'
								</select>
							</div>
						</fieldset>
						<fieldset>
							<legend>受注先</legend>
							<p>'.$cliant_name.'</p>
						</fieldset>
						<fieldset>
							<legend>受注件名</legend>
							<div>
								<input type="text" name="title" value="'.$master_data["MASTER_TITLE"].'" style="width:80%;"><span class="hissu">必須</span>
							</div>
						</fieldset>
						<fieldset>
							<legend>納期日</legend>
							<div>
								<select name="limit_date_array[]" style="width: 80px;">
									'.$year_select[1].'
								</select>
								<select name="limit_date_array[]" style="width: 60px;">
									'.$month_select[1].'
								</select>
								<select name="limit_date_array[]" style="width: 60px;>
									'.$day_select[1].'
								</select><span class="hissu">必須</span>
							</div>
						</fieldset>
						<fieldset>
							<legend>受注金額</legend>
							<div>
								<input type="text" name="price" value="'.$master_data["MASTER_PRICE"].'">円<span class="hissu">必須</span><br>
								<span class="form_annotation">※半角数字・コンマなしで記入してください</span>
							</div>
						</fieldset>
						<fieldset>
							<legend>受注に関する備考</legend>
							<textarea name="notes" style="width:96%;height:50px;resize:vertical;">'.$master_data['MASTER_BODY'].'</textarea>
						</fieldset>
						<input type="hidden" name="cliant_id" value="'.$master_data["CLIANT_ID"].'">
						<input type="hidden" name="estimate_id" value="'.$master_data["ID"].'">
						<div class="formAction">
							<button type="submit" name="accept"><i class="icon-check"></i>受注処理</button>
						</div>
					</form>
				</div>
			';
			
			return $html;
			
		}
	}
?>