<?php
	/* /////////////////////////////////////////////////////
	//		受注管理 共通VIEWパッケージ
	//////////////////////////////////////////////////////*/
	
	////////////////////////////////////////////////////////
	//
	//	#substance	パッケージの作成
	//  #Date		2013/12/13
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
require_once("../db/op-accept/accept.php");
require_once("../db/op-accept/cliant.php");
require_once("../db/op-user/user.php");
	
	class View extends Accept{
		
		public $operation, $mode, $state, $accept_id, $staff, $row ,$order, $col, $start, $limit_num ,$orderby;
		
		///--------------------------------------------------------------------
		/// 【コンストラクター】
		///
		/// 戻り値　なし
		///
		///	#Author yk
		/// #date	2013/12/13
		///--------------------------------------------------------------------
		function __construct(){
			//受注管理の初期設定
			$this->menu_list = array(
				'list' => array('MENU_NAME'=>'受注一覧','MENU_ICON'=>'th-list', 'MENU_AUTHORITY'=>'administrator,staff'),
				'new' => array('MENU_NAME'=>'受注登録','MENU_ICON'=>'plus', 'MENU_AUTHORITY'=>''),
				'edit' => array('MENU_NAME'=>'受注情報編集','MENU_ICON'=>'th-list', 'MENU_AUTHORITY'=>'', 'PARENT_KEY'=>'list', 'PARENT_NAME'=>'一覧'),
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
			);
		}
		
		///--------------------------------------------------------------------
		/// サイドバーの生成
		///
		///	#Author yk
		/// #date	2013/12/13
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
		/// #date	2013/12/13
		///--------------------------------------------------------------------
		function setContents(){
			
			global $op_list;
			
			$parent_bread ='';
			//ステートの設定
			$state_html = $this->stateMessage();
			
			//受注一覧表示の場合
			if($this->mode=='list'){
				//受注リストの取得
				$master_list = $this->setMasterList();
				$list_count = $this->setListCount();
				//受注リストの生成
				$contents_html ='
					<div class="row-fluid">
						<div id="'.$this->mode.'-wrap" class="span12">
							'.$this->listContents($master_list, $list_count).'
						</div>
					</div>
				';
			}
			//受注の新規作成・編集の場合
			else if($this->mode=='new' || $this->mode=='edit'){
				//編集の場合
				if($this->mode=='edit' && $this->accept_id){
					//受注情報の取得
					$master_data = parent::getAcceptMasterForID($this->accept_id);
					$parent_bread = '<li><a href="/admin/op-accept/?mode='.$this->menu_list[$this->mode]["PARENT_KEY"].'">'.$this->menu_list[$this->mode]["PARENT_NAME"].'</a></li>';
				}
				//新規保存の場合
				else{
					$master_data ='';
				}
				//受注リストの生成
				$contents_html ='
					<div class="row-fluid">
						'.$this->editContents($master_data).'
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
		/// 受注リストの取得
		///
		///	#Author yk
		/// #date	2013/12/13
		///	#Author yk
		/// #date	2014/01/10
		///--------------------------------------------------------------------
		function setMasterList(){
			
				//受注リストの取得（スタッフ検索）
				if(!empty($this->staff)){
					$master_list = parent::searchAcceptMasterForstaffID($this->staff, 'ACCEPT_STATUS', $this->col, $this->orderby, $this->page*$this->limit_num, $this->limit_num);
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
					$master_list = parent::searchAcceptMasterForCondition($this->condition);
				}
				//受注リストの取得（通常）
				else{
					$master_list = parent::searchAcceptMasterList('ACCEPT_STATUS', $this->col, $this->orderby, $this->page*$this->limit_num, $this->limit_num);
				}
				
			return $master_list;
		}
		
		///--------------------------------------------------------------------
		/// 受注リスト数の取得
		///
		///	#Author yk
		/// #date	2013/12/13
		///	#Author yk
		/// #date	2014/01/10
		///--------------------------------------------------------------------
		function setListCount(){
			
				//受注リストの取得（スタッフ検索）
				if(!empty($this->staff)){
					$list_count = parent::countAcceptMasterForstaffID($this->staff, 'ACCEPT_STATUS', $this->col);
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
					$list_count = parent::countAcceptMasterForCondition($this->condition);
				}
				//受注リストの取得（通常）
				else{
					$list_count = parent::countAcceptMasterList('ACCEPT_STATUS', $this->col);
				}
				
			return $list_count;
		}
		
		///--------------------------------------------------------------------
		/// コンテンツの生成（リスト）
		///
		///	#Author yk
		/// #date	2013/12/13
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
				$staff_select.='<li><a href="/admin/op-accept/?mode=list&staff='.$value["ID"].'">'.$value["DISPLAY_NAME"].'</a></li>';
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
			$ctrl_html ='<li id="all"><a>全て</a></li>';
			
			//リストHTMLの生成
			$list_html ='';
			foreach($master_list as $value){
				//受注期限の設定
				$deadline ='';
				if($this->col==='wait'){
					$limit_day = ceil((strtotime($value["ACCEPT_LIMIT"]) - strtotime('today')) / (60*60*24));
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
						<td>'.date('Y年m月j日', strtotime($value["ACCEPT_DATE"])).'</td>
						<th>'.$staff_name.'</th>
						<th>'.$value["ACCEPT_TITLE"].'</th>
						<th>'.$cliant_name.'</th>
						<th>'.number_format($value["ACCEPT_PRICE"]).'円</th>
						<td>
							<a class="Btn detailBtn" onClick="ajax_detail(\''.$value["ID"].'\')"><i class="icon-arrow-right"></i><span>確認</span></a>
						</td>
					</tr>
				';
			}
			if(empty($master_list)){
				$list_html ='<tr><th colspan="3" class="no_list">該当する受注情報がありません。</th></tr>';
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
						<h4>List in<span>&nbsp'.$list_count.'&nbsp</span>accepts ( '.($start+1).' to '.$end.' )</h4>
						<button type="button" name="searchBtn" value=""><i class="icon-search"></i>期間検索</button>
						<div class="staffSelect" >
							<button type="button" class="btn"><i class="icon-user"></i>スタッフ検索</button>
							<button type="button" class="dropdown"><i class="icon-caret-down"></i></button>
							<ul>
								<li><a href="/admin/op-accept/?mode=list">全て</a></li>
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
									<th id="accept_date" class="sortRow">受注日<a href="javascript:;" class="icon-sort"></a></th>
									<th id="staff_id" class="min100 sortRow">担当スタッフ<a href="javascript:;" class="icon-sort"></a></th>
									<th class="min100">受注件名</th>
									<th class="min100">提出先</th>
									<th class="min100">受注金額</th>
									<td>操作</td>
								</tr>
							</thead>
							<tbody>
								'.$list_html.'
							</tbody>
						</table>
						<div style="overflow:hidden">
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
			
			//受注日絞り込みの設定
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
							<legend>受注日</legend>
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
		/// #date	2013/12/13
		///--------------------------------------------------------------------
		function editContents($master_data){
			
			$staffObj = new User();
			$staff_list = $staffObj->getStaffList();
			
			//担当スタッフの設定								
			$staff_select = '<option value="0">未設定</option>';
			foreach($staff_list as $value){
				//セレクトボックス初期値設定
				$selected = ($master_data['STAFF_ID']==$value['ID'])? 'selected': '';
				$staff_select.='<option value="'.$value['ID'].'" '.$selected.'>'.$value['DISPLAY_NAME'].'</option>';
			}
			
			//受注提出先の設定
			$cliant_name ='';
			if(!empty($master_data['CLIANT_ID'])){
				$cliantObj = new Cliant();
				$cliant_data = $cliantObj->getCliantMasterForID($master_data["CLIANT_ID"]);
				$cliant_name = $cliant_data["MASTER_NAME"].'（'.$cliant_data["MASTER_COMPANY"].'）';
				$master_data['CLIANT_ID'] = sprintf('%05d', $master_data["CLIANT_ID"]);
			}
			
			//受注作成日の設定
			$year_select = $month_select = $day_select = array();
			if(empty($master_data)){
				$master_data["ACCEPT_DATE"] = date('Y-m-d');
				$master_data["ACCEPT_LIMIT"] = date('Y-m-d', strtotime('+1 month'));
			}
			$accept_date = date_parse($master_data["ACCEPT_DATE"]);
			$accept_limit = date_parse($master_data["ACCEPT_LIMIT"]);
			
			for($i=date('Y'); $i<=date('Y')+1; $i++){
				$selected = ($i==$accept_date["year"])? 'selected': '';
				$year_select[0].='<option value="'.$i.'" '.$selected.'>'.$i.'年</option>';
				$selected = ($i==$accept_limit["year"])? 'selected': '';
				$year_select[1].='<option value="'.$i.'" '.$selected.'>'.$i.'年</option>';
			}
			for($i=1; $i<=12; $i++){
				$selected = ($i==$accept_date["month"])? 'selected': '';
				$month_select[0].='<option value="'.$i.'" '.$selected.'>'.$i.'月</option>';
				$selected = ($i==$accept_limit["month"])? 'selected': '';
				$month_select[1].='<option value="'.$i.'" '.$selected.'>'.$i.'月</option>';
			}
			for($i=1; $i<=31; $i++){
				$selected = ($i==$accept_date["day"])? 'selected': '';
				$day_select[0].='<option value="'.$i.'" '.$selected.'>'.$i.'日</option>';
				$selected = ($i==$accept_limit["day"])? 'selected': '';
				$day_select[1].='<option value="'.$i.'" '.$selected.'>'.$i.'日</option>';
			}
			
			//ボタンの設定
			if($this->mode==='new'){
				$btn_html = '<button type="submit" name="'.$this->mode.'" class="grayBtn"><i class="icon-ok"></i>保存</button><button type="reset">リセット</button>';
			}
			else{
				$btn_html = '<button type="submit" name="'.$this->mode.'"><i class="icon-refresh"></i>更新</button><a class="deleteBtn" href="post.php?action=trash&id='.$master_data["ID"].'"><i class="icon-trash"></i>削除</a>';
			}
			
			
			//入力フォームの生成
			$html ='
				<div id="'.$this->mode.'-wrap" class="span12">
					<div class="contentTitle">
						<h3>'.$this->menu_list[$this->mode]["MENU_NAME"].'</h3>
					</div>
					<form action="post.php" method="post"enctype="multipart/form-data" name="editForm" >
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
								<select name="accept_date_array[]" style="width: 60px;">
									'.$day_select[0].'
								</select>
							</div>
						</fieldset>
						<fieldset>
							<legend>納期</legend>
							<div>
								<select name="accept_limit_array[]" style="width: 80px;">
									'.$year_select[1].'
								</select>
								<select name="accept_limit_array[]" style="width: 60px;">
									'.$month_select[1].'
								</select>
								<select name="accept_limit_array[]" style="width: 60px;">
									'.$day_select[1].'
								</select>
							</div>
						</fieldset>
						<fieldset>
							<legend>受注提出先</legend>
							<div>
								<p class="cliantText">
									顧客ID：'.$master_data["CLIANT_ID"].'<br>
									顧客名：'.$cliant_name.'
								</p>
							</div>
						</fieldset>
						<fieldset>
							<legend>受注件名</legend>
							<div>
								<input type="text" name="title" value="'.$master_data["ACCEPT_TITLE"].'"><span class="hissu">必須</span>
							</div>
						</fieldset>
						<fieldset>
							<legend>受注金額</legend>
							<div>
								<input type="text" name="price" value="'.$master_data["ACCEPT_PRICE"].'">円<span class="hissu">必須</span><br>
								<span class="form_annotation">※半角数字・コンマなしで記入してください</span>
							</div>
						</fieldset>
						<fieldset>
							<legend>受注に関する備考</legend>
							<div>
								<textarea name="notes">'.$master_data['ACCEPT_NOTES'].'</textarea>
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
		/// #date	2013/12/13
		///--------------------------------------------------------------------
		function detailContents($master_data){
			
			global $accept_status_list;
			
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
							<legend>受注スタッフ</legend>
							<p>'.$staff.'</p>
						</fieldset>
						<fieldset>
							<legend>受注日</legend>
							<p>'.date('Y年n月j日', strtotime($master_data["ACCEPT_DATE"])).'</p>
						</fieldset>
						<fieldset>
							<legend>納期</legend>
							<p>'.date('Y年n月j日', strtotime($master_data["ACCEPT_LIMIT"])).'</p>
						</fieldset>
						<fieldset>
							<legend>受注先</legend>
							<p>'.$cliant_name.'</p>
						</fieldset>
						<fieldset>
							<legend>受注件名</legend>
							<p>'.$master_data["ACCEPT_TITLE"].'</p>
						</fieldset>
						<fieldset>
							<legend>受注金額</legend>
							<p>'.number_format($master_data["ACCEPT_PRICE"]).'円</p>
						</fieldset>
						<fieldset>
							<legend>受注に関する備考</legend>
							<p>'.nl2br($master_data["ACCEPT_NOTES"]).'</p>
						</fieldset>
						<fieldset>
							<legend>編集日時</legend>
							<p>'.date('Y年n月j日 H:i:s', strtotime($master_data["EDIT_DATE"])).'</p>
						</fieldset>
						<input type="hidden" name="id" value="'.$master_data["ID"].'">
						<div class="formAction">
							<button type="submit" name="mode" value="edit"><i class="icon-pencil"></i>修正する</button>
						</div>
					</form>
				</div>
			';
			
			return $html;
			
		}
	}
?>