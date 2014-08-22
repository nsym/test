<?php
	/* /////////////////////////////////////////////////////
	//		顧客管理 共通VIEWパッケージ
	//////////////////////////////////////////////////////*/
	
	////////////////////////////////////////////////////////
	//
	//	#substance	パッケージの作成
	//  #Date		2013/11/04
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	//
	//	#substance	フィールド追加による変更
	//  #Date		2013/11/21
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	//
	//	#substance	CSV出力機能・顧客対応履歴追加による変更
	//  #Date		2013/12/04
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	//
	//	#substance	見積管理追加による変更
	//  #Date		2013/12/14
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	//
	//	#substance	メールアドレスを非必須へ変更
	//  #Date		2014/01/07
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	//
	//	#substance	登録内容への最新対応履歴の表示
	//				リストの対応履歴無し顧客の色分け
	//  #Date		2014/01/10
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	//
	//	#substance	登録内容への備考の表示
	//				会社名での並び替え機能追加
	//  #Date		2014/04/16
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	//
	//	#substance	最新対応履歴でのソート機能
	//  #Date		2014/06/09
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	
//--------------------------------------------------------------------
require_once("../db/op-cliant/cliant.php");
require_once("../db/op-cliant/group.php");
require_once("../db/op-cliant/history.php");
require_once("../db/op-user/user.php");
	
	class View extends Cliant{
		
		public $operation, $mode, $state, $cliant_id, $group, $row ,$order, $col, $start, $limit_num ,$orderby;
		
		///--------------------------------------------------------------------
		/// 【コンストラクター】
		///
		/// 戻り値　なし
		///
		///	#Author yk
		/// #date	2013/11/04
		///	#Author yk
		/// #date	2014/04/16
		///--------------------------------------------------------------------
		function __construct(){
			//顧客管理の初期設定
			$this->menu_list = array(
				'list' => array('MENU_NAME'=>'一覧','MENU_ICON'=>'th-list', 'MENU_AUTHORITY'=>'administrator,staff'),
				'new' => array('MENU_NAME'=>'新規作成','MENU_ICON'=>'plus', 'MENU_AUTHORITY'=>'administrator,staff'),
				'group' => array('MENU_NAME'=>'グループ管理','MENU_ICON'=>'group', 'MENU_AUTHORITY'=>'administrator,staff'),
				'export' => array('MENU_NAME'=>'CSV出力','MENU_ICON'=>'download', 'MENU_AUTHORITY'=>'administrator,staff'),
				'edit' => array('MENU_NAME'=>'情報編集','MENU_ICON'=>'th-list', 'MENU_AUTHORITY'=>'', 'PARENT_KEY'=>'list', 'PARENT_NAME'=>'一覧'),
				'detail' => array('MENU_NAME'=>'登録内容','MENU_ICON'=>'th-list', 'MENU_AUTHORITY'=>'', 'PARENT_KEY'=>'list', 'PARENT_NAME'=>'一覧'),
				'keyword' => array('MENU_NAME'=>'キーワード検索結果','MENU_ICON'=>'th-list', 'MENU_AUTHORITY'=>'', 'PARENT_KEY'=>'list', 'PARENT_NAME'=>'一覧'),
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
				'group-ok' => array('STATE_COLOR'=>'green', 'STATE_MESSAGE'=>'グループ登録が完了いたしました。'),
				'group-ng' => array('STATE_COLOR'=>'red', 'STATE_MESSAGE'=>'グループ登録処理に失敗いたしました。'),
				'mail-ng' => array('STATE_COLOR'=>'red', 'STATE_MESSAGE'=>'正しくメールアドレスを入力して下さい'),
				'duplicate-ng' => array('STATE_COLOR'=>'red', 'STATE_MESSAGE'=>'重複した登録があります'),
			);
		}
		
		///--------------------------------------------------------------------
		/// サイドバーの生成
		///
		///	#Author yk
		/// #date	2013/11/04
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
		/// #date	2013/11/04
		///	#Author yk
		/// #date	2013/12/14
		///--------------------------------------------------------------------
		function setContents(){
			
			global $op_list;
			
			$parent_bread ='';
			//ステートの設定
			$state_html = $this->stateMessage();
			
			//顧客一覧表示の場合
			if($this->mode=='list'){
				//顧客リストの取得
				$master_list = $this->setMasterList();
				$list_count = $this->setListCount();
				//顧客リストの生成
				$contents_html ='
					<div class="row-fluid r_txt">
						<a href="./?mode=new" class="newBtn"><i class="icon-plus"></i>新規顧客登録</a>
					</div>
					<div id="list-row" class="row-fluid">
						<div id="'.$this->mode.'-wrap" class="span12">
							'.$this->listContents($master_list, $list_count).'
						</div>
					</div>
				';
			}
			//顧客の新規作成・編集の場合
			else if($this->mode=='new' || $this->mode=='edit'){
				//編集の場合
				if($this->mode=='edit' && $this->cliant_id){
					//顧客情報の取得
					$master_data = parent::getCliantMasterForID($this->cliant_id);
					$parent_bread = '<li><a href="/admin/op-cliant/?mode='.$this->menu_list[$this->mode]["PARENT_KEY"].'">'.$this->menu_list[$this->mode]["PARENT_NAME"].'</a></li>';
					$estimate_btn = '<a href="/admin/op-estimate/?mode=new&cliant_id='.$master_data["ID"].'" class="newBtn"><i class="icon-jpy"></i>見積登録</a>';
				}
				//新規保存の場合
				else{
					$master_data ='';
					$estimate_btn = '';
				}
				//顧客リストの生成
				$contents_html ='
					<div class="row-fluid r_txt">
						'.$estimate_btn.'
					</div>
					<div class="row-fluid">
						'.$this->editContents($master_data).'
						'.$this->historyContents($master_data).'
					</div>
				';
			}
			//グループの新規作成・編集の場合
			else if($this->mode=='group'){
				//顧客情報の取得
				$groupObj = new Group();
				$group_list = $groupObj->getCliantGroupList();
				$group_data = array();
				if(isset($_GET["group_id"])){
					$group_data = $groupObj->getCliantGroupForID($_GET["group_id"]);
				}
				//顧客リストの生成
				$contents_html ='
					<div class="row-fluid r_txt">
						<a href="./?mode=group" class="newBtn"><i class="icon-plus"></i>グループ新規登録</a>
					</div>
					<div class="row-fluid">
						'.$this->groupEditContents($group_data).'
						'.$this->groupListContents($group_list).'
					</div>
				';
			}
			//キーワード検索の場合
			else if($this->mode=='keyword'){
				//パンくず設定
				$parent_bread = '<li><a href="/admin/op-cliant/?mode='.$this->menu_list[$this->mode]["PARENT_KEY"].'">'.$this->menu_list[$this->mode]["PARENT_NAME"].'</a></li>';
				//顧客リストの取得
				$master_list = $this->setMasterList();
				$list_count = $this->setListCount();
				//顧客リストの生成
				$contents_html ='
					<div class="row-fluid">
						<div id="list-wrap" class="span12">
							'.$this->resultContents($master_list, $list_count).'
						</div>
					</div>
				';
			}else if($this->mode=='export'){
				//CSV出力画面の生成
				$contents_html ='
					<div class="row-fluid">
						'.$this->exportContents().'
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
		/// 顧客リストの取得
		///
		///	#Author yk
		/// #date	2013/11/06
		///--------------------------------------------------------------------
		function setMasterList(){
			
				//顧客リストの取得（グループ検索）
				if(!empty($this->group)){
					$master_list = parent::searchCliantMasterForGroupID($this->group, 'MASTER_KANA', $this->col, $this->orderby, $this->page*$this->limit_num, $this->limit_num);
				}
				//顧客リストの取得（キーワード検索）
				else if($this->mode=='keyword'){
					$master_list = parent::searchCliantMasterListForKeyword($_GET["keyword"], 'MASTER_KANA', $this->col, $this->orderby, $this->page*$this->limit_num, $this->limit_num);
				}
				//顧客リストの取得（条件検索）
				else if(isset($_GET["condition"])){
					$this->condition = array(
						'staff' => $_GET["staff"],
						'rank' => $_GET["rank"],
						'exhibition' => $_GET["exhibition"],
						'kyoto' => $_GET["kyoto"],
						'ipros' => $_GET["ipros"],
						'mailmaga' => $_GET["mailmaga"],
						'area' => $_GET["area"],
						'start_date' => date('Y-m-d H:i:s', mktime(0, 0, 0, $_GET["start_month"], $_GET["start_day"], $_GET["start_year"])),
						'end_date' => date('Y-m-d H:i:s', mktime(0, 0, 0, $_GET["end_month"], $_GET["end_day"], $_GET["end_year"])),
						'field' => $this->field,
						'col' => $this->col,
						'orderby' => $this->orderby,
						'start' => $this->page*$this->limit_num,
						'limit_num' => $this->limit_num,
					);
					$master_list = parent::searchCliantMasterForCondition($this->condition);
				}
				//顧客リストの取得（通常）
				else{
					$master_list = parent::searchCliantMasterList('MASTER_KANA', $this->col, $this->orderby, $this->page*$this->limit_num, $this->limit_num);
				}
				
			return $master_list;
		}
		
		///--------------------------------------------------------------------
		/// 顧客リスト数の取得
		///
		///	#Author yk
		/// #date	2013/11/06
		///	#Author yk
		/// #date	2013/11/21
		///--------------------------------------------------------------------
		function setListCount(){
			
				//顧客リストの取得（グループ検索）
				if(!empty($this->group)){
					$list_count = parent::countCliantMasterForGroupID($this->group, 'MASTER_KANA', $this->col);
				}
				//顧客リストの取得（キーワード検索）
				else if($this->mode=='keyword'){
					$list_count = parent::countCliantMasterListForKeyword($_GET["keyword"], 'MASTER_KANA', $this->col);
				}
				//顧客リストの取得（条件検索）
				else if(isset($_GET["condition"])){
					$this->condition = array(
						'staff' => $_GET["staff"],
						'rank' => $_GET["rank"],
						'exhibition' => $_GET["exhibition"],
						'kyoto' => $_GET["kyoto"],
						'ipros' => $_GET["ipros"],
						'mailmaga' => $_GET["mailmaga"],
						'area' => $_GET["area"],
						'staff' => $_GET["staff"],
						'start_date' => date('Y-m-d H:i:s', mktime(0, 0, 0, $_GET["start_month"], $_GET["start_day"], $_GET["start_year"])),
						'end_date' => date('Y-m-d H:i:s', mktime(0, 0, 0, $_GET["end_month"], $_GET["end_day"], $_GET["end_year"])),
						'field' => $this->field,
						'col' => $this->col,
					);
					$list_count = parent::countCliantMasterForCondition($this->condition);
				}
				//顧客リストの取得（通常）
				else{
					$list_count = parent::countCliantMasterList('MASTER_KANA', $this->col);
				}
				
			return $list_count;
		}
		
		///--------------------------------------------------------------------
		/// コンテンツの生成（リスト）
		///
		///	#Author yk
		/// #date	2013/11/04
		///	#Author yk
		/// #date	2013/11/21
		///	#Author yk
		/// #date	2014/01/10
		///	#Author yk
		/// #date	2014/04/16
		///	#Author yk
		/// #date	2014/06/09
		///--------------------------------------------------------------------
		function listContents($master_list, $list_count){
			
			//グループセレクトの設定
			$groupObj = new Group();
			$group_list = $groupObj->getCliantGroupList();
			$group_select ='';
			foreach($group_list as $value){
				$group_select.='<li><a href="/admin/op-cliant/?mode=list&group='.$value["ID"].'">'.$value["GROUP_NAME"].'</a></li>';
			}
			
			$staffObj = new User();
			
			//条件検索HTMLの生成
			$condition_html = $this->conditionSearch();
			
			//検索条件の表示
			$search_text = '';
			if(isset($_GET["group"])){
				$group_data = $groupObj->getCliantGroupForID($_GET["group"]);
				$search_text = '<p class="searchText">検索条件：'.$group_data["GROUP_NAME"].'</p>';
			}else if(isset($_GET["condition"])){
				extract($this->condition);
				//主担当スタッフの表示設定
				if(!empty($staff)){
					$staff_data = $staffObj->getStaffForID($staff);
					$staff = '「'.$staff_data["DISPLAY_NAME"].'」';
				}else{
					$staff ='';
				}
				global $area_list, $mailmaga_flag_list, $ipros_flag_list, $exhibition_flag_list, $kyoto_flag_list, $rank_list;
				//エリアの表示設定
				if($area==='except') $area ='「その他・国外」';
				else if(!empty($area)) $area ='「'.$area.'」';
				else $area ='';
				$mailmaga = !empty($mailmaga)? '「'.$mailmaga_flag_list[$mailmaga].'」': '';
				$ipros = !empty($ipros)? '「'.$ipros_flag_list[$ipros].'」': '';
				$exhibition = !empty($exhibition)? '「'.$exhibition_flag_list[$exhibition].'」': '';
				$kyoto = !empty($kyoto)? '「'.$kyoto_flag_list[$kyoto].'」': '';
				$rank = !empty($rank)? '「'.$rank_list[$rank].'ランク」': '';
				$search_text = '<p class="searchText">検索条件：'.$staff.$area.$mailmaga.$ipros.$exhibition.$kyoto.$rank.'「'.date('Y年n月j日', strtotime($start_date)).'から'.date('Y年n月j日', strtotime($end_date)).'まで」</p>';
			}
			
			//リストコントロールの生成
			global $kana_list;
			$ctrl_html ='<li id="all"><a href="javascript:;">全て</a></li>';
			foreach($kana_list as $key=>$value){
				$ctrl_html.='<li id="'.$key.'"><a href="javascript:;">'.$value[0].'行</a></li>';
			}
			
			//リストHTMLの生成
			global $rank_list;
			$historyObj = new History();
			$list_html ='';
			foreach($master_list as $value){
				//顧客ランクの設定
				$rank_icon = !empty($value["MASTER_RANK"])? '<span class="'.$value["MASTER_RANK"].'">'.$rank_list[$value["MASTER_RANK"]].'</span>': '';
				//対応履歴による色分け設定（その他カテゴリ以外）
				$history_class ='';
				$history_data = $historyObj->getCliantHistoryListForLimit($value["ID"], 'except', '');
				if(strtotime('+1 month '.$value["INS_DATE"]) >= mktime()){
					$history_class ='new_cliant';
				}else if(empty($history_data)){
					$history_class ='no_deal';
				}			
				//最新対応履歴の設定
				$history_data = $historyObj->getCliantHistoryListRecent($value["ID"]);
				$history_recent ='';
				if(!empty($history_data)){
					global $history_category_list;
					$history_staff_name ='スタッフ未設定';
					if(!empty($history_data["STAFF_ID"])){
						$history_staff_data = $staffObj->getStaffForID($history_data["STAFF_ID"]);
						$history_staff_name = $history_staff_data["DISPLAY_NAME"];
					}
					$history_recent =date('Y年m月d日 H時', strtotime($history_data["HISTORY_DATE"])).'（'.$history_staff_name.')';
				}else{
					$history_recent ='対応履歴はありません';
				}
				
				$list_html.='
					<tr class="'.$history_class.'">
						<td><label><input type="checkbox" name="check_id[]" value="'.$value["ID"].'">'.sprintf('%05d', $value["ID"]).'</label></td>
						<th>'.$value["MASTER_NAME"].'（'.$value["MASTER_KANA"].'）'.$rank_icon.'</th>
						<th>'.$value["MASTER_COMPANY"].'</th>
						<th>'.$history_recent.'</th>
						<td>
							<a class="Btn detailBtn" onClick="ajax_detail(\''.$value["ID"].'\')"><i class="icon-arrow-right"></i><span>確認</span></a>
						</td>
					</tr>
				';
			}
			if(empty($master_list)){
				$list_html ='<tr><th colspan="3" class="no_list">該当する顧客情報がありません。</th></tr>';
			}
			
			//グループ追加の設定
			$group_add ='';
			foreach($group_list as $value){
				$group_add.='<option value="'.$value["ID"].'">'.$value["GROUP_NAME"].'</option>';
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
						<h4>List in<span>&nbsp'.$list_count.'&nbsp</span>cliants ( '.($start+1).' to '.$end.' )</h4>
						<button type="button" name="searchBtn" value=""><i class="icon-search"></i>条件検索</button>
						<div class="groupSelect" >
							<button type="button" class="btn"><i class="icon-group"></i>グループ検索</button>
							<button type="button" class="dropdown"><i class="icon-caret-down"></i></button>
							<ul>
								<li><a href="/admin/op-cliant/?mode=list">全て</a></li>
								'.$group_select.'
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
									<th id="id" class="sortRow">顧客ID<a href="javascript:;" class="icon-sort"></a></th>
									<th id="master_kana" class="min100 sortRow">顧客名<a href="javascript:;" class="icon-sort"></a></th>
									<th id="master_company" class="min200 sortRow">企業・団体<a href="javascript:;" class="icon-sort"></a></th>
									<th id="recent_history_date" class="min100 sortRow">最新対応日時<a href="javascript:;" class="icon-sort"></a></th>
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
								<select name="group_add">
									'.$group_add.'
								</select>
								<input type="hidden" name="action" value="group">
								<button type="button" id="groupAdd">グループ登録</button>
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
		/// #date	2013/11/06
		///	#Author yk
		/// #date	2013/11/21
		///--------------------------------------------------------------------
		function conditionSearch(){
			
			//検索条件の取得
			if(isset($this->condition)){
				extract($this->condition);
			}else{
				$area =$mailmaga =$ipros =$start_date =$end_date ='';
			}
			
			//主担当スタッフ絞り込みの設定
			$staffObj = new User();
			$staff_list = $staffObj->getStaffList();			
			$staff_select = '<option value="">全てのスタッフ</option>';
			foreach($staff_list as $value){
				//セレクトボックス初期値設定
				$selected = ($staff==$value['ID'])?'selected':'';
				$staff_select .='<option value="'.$value['ID'].'" '.$selected.'>'.$value['DISPLAY_NAME'].'</option>';
			}
			
			//エリア絞り込みの設定
			global $area_list;
			$area_select ='<option value="">全てのエリア</option>';
			foreach($area_list as $key=>$area_value){
				$area_select.='<optgroup label="'.$key.'">';
				foreach($area_value as $value){
					$selected = ($area===$value)? 'selected': '';
					if($selected==='selected') $select_flag = true;	//その他・国外用のフラグ
					$area_select.='<option value="'.$value.'" '.$selected.'>'.$value.'</option>';
				}
				$area_select.='</optgroup>';
			}
			$selected = (!empty($area) && !$select_flag)? 'selected': '';
			$area_select.='<option value="except" '.$selected.'>その他・国外</option>';
			
			//顧客ランク絞り込みの設定
			global $rank_list;		
			$rank_select = '<option value="">全てのランク</option>';
			foreach($rank_list as $key=>$value){
				//セレクトボックス初期値設定
				$selected = ($rank==$key)?'selected':'';
				$rank_select .='<option value="'.$key.'" '.$selected.'>'.$value.'ランク</option>';
			}
			
			//メルマガ配信絞り込みの設定
			global $mailmaga_flag_list;
			$checked = empty($mailmaga)? 'checked': '';
			$mailmaga_radio ='<label><input type="radio" name="mailmaga" value="" '.$checked.'>指定なし</label>';
			foreach($mailmaga_flag_list as $key=>$value){
				$checked = ($mailmaga===$key)? 'checked': '';
				$mailmaga_radio.='<label><input type="radio" name="mailmaga" value="'.$key.'" '.$checked.'>'.$value.'</label>';
			}
			
			//イプロス会員絞り込みの設定
			global $ipros_flag_list;
			$checked = empty($ipros)? 'checked': '';
			$ipros_radio ='<label><input type="radio" name="ipros" value="" '.$checked.'>指定なし</label>';
			foreach($ipros_flag_list as $key=>$value){
				$checked = ($ipros===$key)? 'checked': '';
				$ipros_radio.='<label><input type="radio" name="ipros" value="'.$key.'" '.$checked.'>'.$value.'</label>';
			}
			
			//展示会絞り込みの設定
			global $exhibition_flag_list;
			$checked = empty($exhibition)? 'checked': '';
			$exhibition_radio ='<label><input type="radio" name="exhibition" value="" '.$checked.'>指定なし</label>';
			foreach($exhibition_flag_list as $key=>$value){
				$checked = ($exhibition===$key)? 'checked': '';
				$exhibition_radio.='<label><input type="radio" name="exhibition" value="'.$key.'" '.$checked.'>'.$value.'</label>';
			}
			
			//京都試作ネット絞り込みの設定
			global $kyoto_flag_list;
			$checked = empty($kyoto)? 'checked': '';
			$kyoto_radio ='<label><input type="radio" name="kyoto" value="" '.$checked.'>指定なし</label>';
			foreach($kyoto_flag_list as $key=>$value){
				$checked = ($kyoto===$key)? 'checked': '';
				$kyoto_radio.='<label><input type="radio" name="kyoto" value="'.$key.'" '.$checked.'>'.$value.'</label>';
			}
			
			//登録日絞り込みの設定
			$year_select = $month_select = $day_select = array('');
			if(empty($start_date)){
				$start_date = date('Y-m-d', mktime(0, 0, 0, 1, 1, 1990));
			}
			if(empty($end_date)){
				$end_date = date('Y-m-d', strtotime('+1 day'));
			}
			list($start_year, $start_month, $start_day) = explode("-", $start_date);
			list($end_year, $end_month, $end_day) = explode("-", $end_date);
			for($i=date('Y'); $i>=1990; $i--){
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
						<fieldset>
							<legend>主担当スタッフ</legend>
							<div>
								<select name="staff">
									'.$staff_select.'
								</select>
							</div>
						</fieldset>
						<fieldset>
							<legend>エリア</legend>
							<div>
								<select name="area">
									'.$area_select.'
								</select>
							</div>
						</fieldset>
						<fieldset>
							<legend>顧客ランク</legend>
							<div>
								<select name="rank">
									'.$rank_select.'
								</select>
							</div>
						</fieldset>
						<fieldset>
							<legend>メルマガ配信</legend>
							<div>
								'.$mailmaga_radio.'
							</div>
						</fieldset>
						<fieldset>
							<legend>イプロス会員</legend>
							<div>
								'.$ipros_radio.'
							</div>
						</fieldset>
						<fieldset>
							<legend>展示会</legend>
							<div>
								'.$exhibition_radio.'
							</div>
						</fieldset>
						<fieldset>
							<legend>京都試作ネット</legend>
							<div>
								'.$kyoto_radio.'
							</div>
						</fieldset>
						<fieldset class="date">
							<legend>登録日</legend>
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
		/// コンテンツの生成（検索結果）
		///
		///	#Author yk
		/// #date	2013/11/07
		///--------------------------------------------------------------------
		function resultContents($master_list, $list_count){
			
			//検索条件の表示
			$search_text = '<p class="searchText">検索キーワード：「'.$_GET["keyword"].'」</p>';
			
			//リストコントロールの生成
			global $kana_list;
			$ctrl_html ='<li id="all"><a href="javascript:;">全て</a></li>';
			foreach($kana_list as $key=>$value){
				$ctrl_html.='<li id="'.$key.'"><a href="javascript:;">'.$value[0].'行</a></li>';
			}
			
			//リストHTMLの生成
			$list_html ='';
			foreach($master_list as $value){
				$list_html.='
					<tr>
						<td><label><input type="checkbox" name="check_id[]" value="'.$value["ID"].'">'.sprintf('%05d', $value["ID"]).'</label></td>
						<th>'.$value["MASTER_NAME"].'（'.$value["MASTER_KANA"].'）</th>
						<th>'.$value["MASTER_COMPANY"].'</th>
						<th>'.$value["MASTER_OFFICE"].'</th>
						<td>
							<a class="Btn detailBtn" onClick="ajax_detail(\''.$value["ID"].'\')"><i class="icon-arrow-right"></i><span>確認</span></a>
						</td>
					</tr>
				';
			}
			if(empty($master_list)){
				$list_html ='<tr><th colspan="3" class="no_list">該当する顧客情報がありません。</th></tr>';
			}
			
			//グループ追加の設定
			$groupObj = new Group();
			$group_list = $groupObj->getCliantGroupList();
			$group_add ='';
			foreach($group_list as $value){
				$group_add.='<option value="'.$value["ID"].'">'.$value["GROUP_NAME"].'</option>';
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
					<h3>'.$this->menu_list['keyword']["MENU_NAME"].'</h3>
				</div>
				<div class="contentBody">
					
					<div class="headText">
						<h4>List in<span>&nbsp'.$list_count.'&nbsp</span>cliants ( '.($start+1).' to '.$end.' )</h4>
						'.$search_text.'
					</div>
					<ul class="listCtrl">
						'.$ctrl_html.'
					</ul>
					<form action="post.php" method="get" name="myform">
						<table>
							<thead>
								<tr>
									<th id="id" class="sortRow">顧客ID<a href="javascript:;" class="icon-sort"></a></th>
									<th id="master_name" class="min100 sortRow">顧客名<a href="javascript:;" class="icon-sort"></a></th>
									<th class="min200">企業・団体</th>
									<th class="min100">事業所</th>
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
								<select name="group_add">
									'.$group_add.'
								</select>
								<input type="hidden" name="action" value="group">
								<button type="button" id="groupAdd">グループ登録</button>
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
		/// コンテンツの生成（エディット）
		///
		///	#Author mukai
		/// #date	2013/11/02
		///	#Author mukai
		/// #date	2013/11/21
		///--------------------------------------------------------------------
		function editContents($master_data){
			
			$staffObj = new User();
			$staff_list = $staffObj->getStaffList();
			
			//主担当スタッフの設定								
			$staff_select = '<option value="none">未設定</option>';
			foreach($staff_list as $value){
				//セレクトボックス初期値設定
				$selected = ($master_data['STAFF_ID']==$value['ID'])?'selected':'';
				$staff_select .='<option value="'.$value['ID'].'" '.$selected.'>'.$value['DISPLAY_NAME'].'</option>';
			}
			
			//ランクの設定
			global $rank_list;
			$checked = empty($master_data['MASTER_RANK'])? 'checked': '';
			$rank_radio ='<label><input type="radio" name="rank" value="" '.$checked.'>未設定</label>';
			foreach($rank_list as $key=>$value){
				$checked = ($master_data['MASTER_RANK']===$key)? 'checked': '';
				$rank_radio.='<label><input type="radio" name="rank" value="'.$key.'" '.$checked.'>'.$value.'ランク</label>';
			}
			
			//グループの設定
			$groupObj = new Group();
			$group_list = $groupObj->getCliantGroupList();
			$group_check= '';
			$group_id_array = explode(',', $master_data['MASTER_GROUP']);	//グループIDの分解
			foreach($group_list as $value){	
				//チェックボックスの初期値設定
				$checked = array_search($value['ID'], $group_id_array)? 'checked': '';
				$group_check.='
					<label>
						<input type="checkbox" name="group_array[]" value="'.$value['ID'].'" '.$checked.'>
						<div class="group_color '.$value['GROUP_COLOR'].'"></div>
						'.$value['GROUP_NAME'].'
					</label>';
			}
			
			//名前の設定
			$name_array = explode(' ', $master_data['MASTER_NAME']);
			//フリガナの設定
			$kana_array = explode(' ', $master_data['MASTER_KANA']);
			//電話番号の設定
			$tel_array = explode('-', $master_data['MASTER_TEL']);
			//FAX番号の設定
			$fax_array = explode('-', $master_data['MASTER_FAX']);
			
			//都道府県の設定
			global $area_list;
			$area_select ='<option value="">未設定</option>';
			foreach($area_list as $key=>$area_value){
				$area_select.='<optgroup label="'.$key.'">';
				foreach($area_value as $value){
					$selected = ($master_data['MASTER_AREA']===$value)? 'selected': '';
					if($selected==='selected') $select_flag = true;	//その他・国外用のフラグ
					$area_select.='<option value="'.$value.'" '.$selected.'>'.$value.'</option>';
				}
				$area_select.='</optgroup>';
			}
			$selected = (!empty($master_data['MASTER_AREA']) && !$select_flag)? 'selected': '';
			$area_select.='<option value="except" '.$selected.'>その他・国外</option>';

			//メルマガ配信フラグの設定
			global $mailmaga_flag_list;
			$mailmaga_radio ='';
			foreach($mailmaga_flag_list as $key=>$value){
				//チェックの設定
				$checked = ($master_data['MAILMAGA_FLAG']===$key)? 'checked': '';
				if(empty($master_data['MAILMAGA_FLAG']) && $key=='send'){
					$checked ='checked';
				}
				$mailmaga_radio.='<label><input type="radio" name="mailmaga_flag" value="'.$key.'" '.$checked.'>'.$value.'</label>';
			}

			//イプロス会員フラグの設定
			global $ipros_flag_list;
			$ipros_radio ='';
			foreach($ipros_flag_list as $key=>$value){
				$checked = ($master_data['IPROS_FLAG']===$key)? 'checked': '';
				if(empty($master_data['IPROS_FLAG']) && $key=='member'){
					$checked ='checked';
				}
				$ipros_radio.='<label><input type="radio" name="ipros_flag" value="'.$key.'" '.$checked.'>'.$value.'</label>';
			}
			
			//展示会フラグの設定
			global $exhibition_flag_list;
			$exhibition_radio ='';
			foreach($exhibition_flag_list as $key=>$value){
				$checked = ($master_data['EXHIBITION_FLAG']===$key)? 'checked': '';
				if(empty($master_data['EXHIBITION_FLAG']) && $key=='noentry'){
					$checked ='checked';
				}
				$exhibition_radio.='<label><input type="radio" name="exhibition_flag" value="'.$key.'" '.$checked.'>'.$value.'</label>';
			}
			
			//京都試作ネットフラグの設定
			global $kyoto_flag_list;
			$kyoto_radio ='';
			foreach($kyoto_flag_list as $key=>$value){
				$checked = ($master_data['KYOTO_FLAG']===$key)? 'checked': '';
				if(empty($master_data['KYOTO_FLAG']) && $key=='nonmember'){
					$checked ='checked';
				}
				$kyoto_radio.='<label><input type="radio" name="kyoto_flag" value="'.$key.'" '.$checked.'>'.$value.'</label>';
			}
			
			//ボタンの設定
			if($this->mode==='new'){
				$btn_html = '<button type="submit" name="'.$this->mode.'"><i class="icon-ok"></i>保存</button><button type="reset">リセット</button>';
			}
			else{
				$btn_html = '<button type="submit" name="'.$this->mode.'"><i class="icon-refresh"></i>更新</button><a class="deleteBtn" href="post.php?action=trash&id='.$master_data["ID"].'"><i class="icon-trash"></i>削除</a>';
			}
			
			
			//入力フォームの生成
			$html ='
				<div id="'.$this->mode.'-wrap" class="span8">
					<div class="contentTitle">
						<h3>'.$this->menu_list[$this->mode]["MENU_NAME"].'</h3>
					</div>
					<form action="post.php" method="post" name="editForm">
						<fieldset>
							<legend>主担当スタッフ</legend>
							<div>
								<select name="staffid">
									'.$staff_select.'
								</select>
							</div>
						</fieldset>
						<fieldset>
							<legend>顧客ランク</legend>
							<div>
								'.$rank_radio.'
							</div>
						</fieldset>
						<fieldset>
							<legend>顧客グループ</legend>
							<div>
								'.$group_check.'
							</div>
						</fieldset>
						<fieldset class="name">
							<legend>顧客名</legend>
							<div>
								姓<input type="text" name="name_array[]" value="'.$name_array[0].'">名<input type="text" name="name_array[]" value="'.$name_array[1].'"><span class="hissu">必須</span>
							</div>
						</fieldset>
						<fieldset class="name">
							<legend>顧客名（ふりがな）</legend>
							<div>
								姓<input type="text" name="kana_array[]" value="'.$kana_array[0].'">名<input type="text" name="kana_array[]" value="'.$kana_array[1].'"><span class="hissu">必須</span><span class="form_annotation">※ひらがなで記入してください</span>
							</div>
						</fieldset>
						<fieldset>
							<legend>企業・団体</legend>
							<div>
								<input type="text" name="company" value="'.$master_data['MASTER_COMPANY'].'">
							</div>
						</fieldset>
						<fieldset>
							<legend>ホームページURL</legend>
							<div>
								<input type="text" name="url" value="'.$master_data['MASTER_URL'].'">
							</div>
						</fieldset>
						<fieldset>
							<legend>事業所</legend>
							<div>
								<input type="text" name="office" value="'.$master_data['MASTER_OFFICE'].'">
							</div>
						</fieldset>
						<fieldset>
							<legend>部署</legend>
							<div>
								<input type="text" name="belong" value="'.$master_data['MASTER_BELONG'].'">
							</div>
						</fieldset>
						<fieldset>
							<legend>役職</legend>
							<div>
								<input type="text" name="post" value="'.$master_data['MASTER_POST'].'">
							</div>
						</fieldset>
						<fieldset>
							<legend>業種</legend>
							<div>
								<input type="text" name="business" value="'.$master_data['MASTER_BUSINESS'].'">
							</div>
						</fieldset>
						<fieldset>
							<legend>職種</legend>
							<div>
								<input type="text" name="job" value="'.$master_data['MASTER_JOB'].'">
							</div>
						</fieldset>
						<fieldset class="telnum">
							<legend>電話番号</legend>
							<div>
								<input type="text" name="tel_array[]" value="'.$tel_array[0].'">-<input type="text" name="tel_array[]" value="'.$tel_array[1].'">-<input type="text" name="tel_array[]" value="'.$tel_array[2].'">
							</div>
						</fieldset>
						<fieldset class="telnum">
							<legend>FAX番号</legend>
							<div>
								<input type="text" name="fax_array[]" value="'.$fax_array[0].'">-<input type="text" name="fax_array[]" value="'.$fax_array[1].'">-<input type="text" name="fax_array[]" value="'.$fax_array[2].'">
							</div>
						</fieldset>
						<fieldset>
							<legend>メールアドレス</legend>
							<div>
								<input type="text" name="mail" value="'.$master_data['MASTER_MAIL'].'"><br>
								<span class="form_annotation">※半角英数字で記入してください</span>
							</div>
						</fieldset>
						<fieldset>
							<legend>エリア</legend>
							<div>
								<select name="area" id="area">
									'.$area_select.'
								</select>
								<input type="text" name="area_text" value="'.$master_data['MASTER_AREA'].'">
							</div>
						</fieldset>
						<fieldset>
							<legend>郵便番号</legend>
							<div>
								<input type="text" name="zip" onKeyUp="$(\'#zip\').zip2addr({pref:\'#area\', addr:\'#address\'});" id="zip" value="'.$master_data['MASTER_ZIPCODE'].'"><br>
								<span class="form_annotation">※郵便番号を入力するとエリア・住所が入力されます</span>
							</div>
						</fieldset>
						<fieldset>
							<legend>住所</legend>
							<div>
								<textarea name="address" id="address">'.$master_data['MASTER_ADDRESS'].'</textarea>
							</div>
						</fieldset>
						<fieldset>
						<legend>メルマガ配信設定</legend>
							<div>
								'.$mailmaga_radio.'
							</div>
						</fieldset>
						<fieldset>
						<legend>イプロス会員フラグ</legend>
							<div>
								'.$ipros_radio.'
							</div>
						</fieldset>
						<fieldset>
						<legend>展示会フラグ</legend>
							<div>
								'.$exhibition_radio.'
							</div>
						</fieldset>
						<fieldset>
						<legend>京都試作ネットフラグ</legend>
							<div>
								'.$kyoto_radio.'
							</div>
						</fieldset>
						<fieldset>
							<legend>備考欄</legend>
							<div>
								<textarea name="notes">'.$master_data['MASTER_NOTES'].'</textarea>
							</div>
						</fieldset>
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
		/// #date	2013/11/04
		///	#Author yk
		/// #date	2013/11/21
		///	#Author yk
		/// #date	2014/01/10
		///	#Author yk
		/// #date	2014/04/16
		///--------------------------------------------------------------------
		function detailContents($master_data){
			
			
			global $rank_list;
			global $mailmaga_flag_list;
			global $ipros_flag_list;
			global $exhibition_flag_list;
			global $kyoto_flag_list;
			
			//グループの設定
			$groupObj = new Group();
			$group_tag ='';
			$group_array = explode(',', $master_data["MASTER_GROUP"]);
			foreach($group_array as $group_id){
				if(!empty($group_id)){
					$group_data = $groupObj->getCliantGroupForID($group_id);
					$group_tag.='<span class="'.$group_data["GROUP_COLOR"].'">'.$group_data["GROUP_NAME"].'</span>';
				}
			}
			
			//主担当スタッフの設定
			$staffObj = new User();
			$staff ='未設定';
			if(!empty($master_data["STAFF_ID"])){
				$staff_data = $staffObj->getStaffForID($master_data["STAFF_ID"]);
				$staff = $staff_data["DISPLAY_NAME"];
			}
			
			//ホームページURLの設定
			$homepage_url ='';
			if(!empty($master_data["MASTER_URL"])){
				$homepage_url = '（<a href="'.$master_data["MASTER_URL"].'" target="_blank">ホームページ</a>）';
			}
			
			//郵便番号の設定
			$zipcode ='';
			if(!empty($master_data["MASTER_ZIPCODE"])){
				$zipcode ='<p>〒'.$master_data["MASTER_ZIPCODE"].'</p>';
			}
			
			//最新対応履歴の設定
			$historyObj = new History();
			$history_data = $historyObj->getCliantHistoryListRecent($master_data["ID"]);
			$history_recent ='';
			if(!empty($history_data)){
				global $history_category_list;
				$history_staff_name ='スタッフ未設定';
				if(!empty($history_data["STAFF_ID"])){
					$history_staff_data = $staffObj->getStaffForID($history_data["STAFF_ID"]);
					$history_staff_name = $history_staff_data["DISPLAY_NAME"];
				}
				$history_recent ='
					<p>'.date('Y年m月d日 H時', strtotime($history_data["HISTORY_DATE"])).'（'.$history_staff_name.')</p>
					<p>【'.$history_category_list[$history_data["HISTORY_CATEGORY"]].'】'.$history_data["HISTORY_TITLE"].'</p>
				';
			}else{
				$history_recent ='<p>対応履歴はありません</p>';
			}
			
			$html ='
				<div id="detail-wrap" class="span4">
					<div class="contentTitle">
						<h3>登録内容</h3>
						<span>
							<a href="javascript:close_wrap(\'#detail-wrap\');" class="icon-remove"></a>
						</span>
					</div>
					<div class="groupTag">
						'.$group_tag.'
					</div>
					<form action="./" method="get">
						<fieldset>
							<legend>主担当スタッフ</legend>
							<p>'.$staff.'</p>
						</fieldset>
						<fieldset>
							<legend>顧客ランク</legend>
							<p>'.$rank_list[$master_data["MASTER_RANK"]].'ランク</p>
						</fieldset>
						<fieldset>
							<legend>顧客名</legend>
							<p>'.$master_data["MASTER_NAME"].'</p>
						</fieldset>
						<fieldset>
							<legend>企業・団体</legend>
							<p>'.$master_data["MASTER_COMPANY"].$homepage_url.'</p>
						</fieldset>
						<fieldset>
							<legend>事業所</legend>
							<p>'.$master_data["MASTER_OFFICE"].'</p>
						</fieldset>
						<fieldset>
							<legend>部署</legend>
							<p>'.$master_data["MASTER_BELONG"].'</p>
						</fieldset>
						<fieldset>
							<legend>役職</legend>
							<p>'.$master_data["MASTER_POST"].'</p>
						</fieldset>
						<fieldset>
							<legend>業種</legend>
							<p>'.$master_data["MASTER_BUSINESS"].'</p>
						</fieldset>
						<fieldset>
							<legend>職種</legend>
							<p>'.$master_data["MASTER_JOB"].'</p>
						</fieldset>
						<fieldset>
							<legend>電話番号</legend>
							<p>'.$master_data["MASTER_TEL"].'</p>
						</fieldset>
						<fieldset>
							<legend>FAX番号</legend>
							<p>'.$master_data["MASTER_FAX"].'</p>
						</fieldset>
						<fieldset>
							<legend>メールアドレス</legend>
							<p><a href="mailto:'.$master_data["MASTER_MAIL"].'">'.$master_data["MASTER_MAIL"].'</a></p>
						</fieldset>
						<fieldset>
							<legend>住所</legend>
							'.$zip_code.'
							<p>
								'.$master_data["MASTER_AREA"].''.$master_data["MASTER_ADDRESS"].'
							</p>
						</fieldset>
						<fieldset>
							<legend>メルマガ配信設定</legend>
							<p>'.$mailmaga_flag_list[$master_data["MAILMAGA_FLAG"]].'</p>
						</fieldset>
						<fieldset>
							<legend>イプロス会員</legend>
							<p>'.$ipros_flag_list[$master_data["IPROS_FLAG"]].'</p>
						</fieldset>
						<fieldset>
							<legend>展示会</legend>
							<p>'.$exhibition_flag_list[$master_data["EXHIBITION_FLAG"]].'</p>
						</fieldset>
						<fieldset>
							<legend>京都試作ネット</legend>
							<p>'.$kyoto_flag_list[$master_data["KYOTO_FLAG"]].'</p>
						</fieldset>
						<fieldset>
							<legend>備考</legend>
							<p>'.$master_data["MASTER_NOTES"].'</p>
						</fieldset>
						<fieldset>
							<legend>登録日</legend>
							<p>'.date('Y年n月j日', strtotime($master_data["INS_DATE"])).'</p>
						</fieldset>
						<fieldset>
							<legend>編集日時</legend>
							<p>'.date('Y年n月j日 H:i:s', strtotime($master_data["EDIT_DATE"])).'</p>
						</fieldset>
						<fieldset>
							<legend>最新対応履歴</legend>
							'.$history_recent.'
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
		/// コンテンツの生成（グループ編集）
		///
		///	#Author yk
		/// #date	2013/11/04
		///--------------------------------------------------------------------
		function groupEditContents($group_data){
			
			//タイトル設定
			$group_h3 = ($group_data['ID']=='')? 'グループ新規登録': 'グループ編集';
	
			//グループカラーの生成
			global $color_list;
			foreach($color_list as $color_value){
				$color_class = $color_value;
				$checked = ($group_data['GROUP_COLOR']==$color_value)? 'checked':'';
				if(empty($group_data['GROUP_COLOR'])&&$color_value==='white') $checked ='checked';
				$color_icon.='<label class="color"><input type="radio" name="color" value="'.$color_value.'" '.$checked.'><div class="'.$color_class.' color_icon">'.$color_value.'</div></label>';
			}
			
			//ボタンの設定
			if(empty($group_data)){
				$btn_html ='<button type="submit" name="'.$this->mode.'-new"><i class="icon-ok"></i>保存</button><button type="reset">リセット</button>';
			}else{
				$btn_html ='<button type="submit" name="'.$this->mode.'-edit"><i class="icon-refresh"></i>更新</button><a class="deleteBtn" href="group_post.php?action='.$this->mode.'-delete&id='.$group_data["ID"].'"><i class="icon-trash"></i>削除</a>';
			}
			
			$html ='
				<div id="new-wrap" class="span8">
					<div class="contentTitle">
						<h3>'.$group_h3.'</h3>
					</div>
					<form action="group_post.php" method="post" enctype="multipart/form-data">
						<fieldset>
							<legend>グループ名</legend>
								<div>
									<input type="text" name="name" value="'.$group_data['GROUP_NAME'].'">
									<span class="hissu">必須</span>
								</div>
							</legend>
						</fieldset>
						<fieldset>
							<legend>グループカラー</legend>
								<div>
									'.$color_icon.'
								</div>
							</legend>
						</fieldset>
						<fieldset>
							<legend>備考（管理用）</legend>
							<div>
								<textarea name="note" value="'.$group_data['GROUP_NOTES'].'">'.$group_data['GROUP_NOTES'].'</textarea>
							</div>
						</fieldset>
						<div class="formAction">
							<input type="hidden" name="id" value="'.$group_data["ID"].'">
							'.$btn_html.'
						</div>
					</form>
				</div>
			';
			
			return $html;
			
		}
		
		///--------------------------------------------------------------------
		/// コンテンツの生成（グループリスト）
		///
		///	#Author yk
		/// #date	2013/11/04
		///--------------------------------------------------------------------
		function groupListContents($group_list){
			
			//リストHTMLの生成
			$list_html ='';
			foreach($group_list as $value){
				$group_num = parent::countCliantMasterForGroupID($value["ID"], '', 'all');
				$list_html.='
					<tr>
						<th>
							<div class="group_color '.$value['GROUP_COLOR'].'"></div>
							'.$value["GROUP_NAME"].'（'.$group_num.'人）
						</th>
						<th>
							<a href="/admin/op-cliant/?mode=group&group_id='.$value["ID"].'" class="Btn detailBtn"><i class="icon-penchil"></i><span>編集</span></a>
						</th>
					</tr>
				';
			}
			
			$html ='
				<div id="list-wrap" class="span4">
					<div class="contentTitle">
						<h3>グループ一覧</h3>
					</div>
					<div class="contentBody">
						<table>
							<thead>
								<tr>
									<th colspan="2">グループ名（登録人数）</th>
								</tr>
							</thead>
							<tbody>
								'.$list_html.'
							</tbody>
						</table>
					</div>
				</div>
			';
			
			return $html;
			
		}
		
		///--------------------------------------------------------------------
		/// コンテンツの生成（対応履歴リスト）
		///
		///	#Author yk
		/// #date	2013/12/04
		///--------------------------------------------------------------------
		function historyContents($master_data){
			
			//対応履歴リストの生成
			$historyObj = new History();
			$history_list = $historyObj->getCliantHistoryListForCliantID($master_data["ID"]);
			$staffObj = new User();
			global $history_category_list;
			$list_html ='';
			foreach($history_list as $value){
			
				//対応スタッフの設定
				$staff_name ='スタッフ未設定';
				if(!empty($value["STAFF_ID"])){
					$staff_data = $staffObj->getStaffForID($value["STAFF_ID"]);
					$staff_name = $staff_data["DISPLAY_NAME"];
				}
				
				$list_html.='
					<tr>
						<td>
							'.date('Y年m月d日 H時', strtotime($value["HISTORY_DATE"])).'（'.$staff_name.')<br>
							【'.$history_category_list[$value["HISTORY_CATEGORY"]].'】'.$value["HISTORY_TITLE"].'
						</td>
						<td>
							<button type="button" name="mode" value="edit" onClick="ajax_history_detail(\''.$value["ID"].'\')"><i class="icon-pencil"></i>内容確認</button>
						</td>
					</tr>
				';
			}
			if(empty($history_list)){
				$list_html ='<tr><td colspan="2"><p class="no_history">登録されている対応履歴がありません</p></td></tr>';
			}
			
			if($this->mode==='edit'){
				$html ='
					'.$this->editHistoryContents($master_data["ID"], '').'
					<div id="detail-wrap" class="span4 historyList">
						<div class="contentTitle">
							<h3>対応履歴一覧</h3>
						</div>
						<table class="historyList">
							<thead>
								<tr>
									<td>対応履歴</td>
									<td>操作</td>
								</tr>
							</thead>
							<tbody>
								'.$list_html.'
							</tbody>
						</table>
					</div>
				';
			}else{
				$html ='
				';
			}
			
			return $html;
			
		}
		
		///--------------------------------------------------------------------
		/// コンテンツの生成（対応履歴エディット）
		///
		///	#Author yk
		/// #date	2013/11/07
		///--------------------------------------------------------------------
		function editHistoryContents($master_id, $history_id){
			
			$historyObj = new History();
			//内容確認の場合
			if(!empty($history_id)){
				$history_data = $historyObj->getCliantHistoryForHistoryID($history_id);
				$master_id = $history_data["MASTER_ID"];
				$h3_title ='対応履歴詳細';
				$btn_html ='<button type="submit" name="edit"><i class="icon-ok"></i>更新</button>';
			}
			//新規追加の場合
			else{
				$history_data = array();
				$h3_title ='対応履歴追加';
				$btn_html ='<button type="submit" name="new"><i class="icon-ok"></i>保存</button>';
			}
			
			//スタッフの設定
			global $authObj;
			$staffObj = new User();
			$staff_list = $staffObj->getStaffList();	
			$staff_select = '<option value="0">未設定</option>';
			foreach($staff_list as $value){
				//セレクトボックス初期値設定
				$selected = ($authObj->admin_data['ID']==$value['ID'])?'selected':'';
				if($history_data["STAFF_ID"]==$value['ID']){
					$selected = 'selected';
				}
				$staff_select .='<option value="'.$value['ID'].'" '.$selected.'>'.$value['DISPLAY_NAME'].'</option>';
			}
			
			//カテゴリの設定
			global $history_category_list;
			$history_select ='';
			foreach($history_category_list as $key=>$value){
				$selected = ($history_data["HISTORY_CATEGORY"]===$key)? 'selected': '';
				$history_select.='<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
			}
			
			//配信日の設定
			$year_select = $month_select = $day_select = $hour_select = '';
			if(empty($history_id)){
				$history_data["HISTORY_DATE"] = date('Y-m-d H:i:s');
			}
			$to_date = date_parse($history_data["HISTORY_DATE"]);
			for($i=date('Y')-1; $i<=date('Y'); $i++){
				$selected = ($i==$to_date["year"])? 'selected': '';
				$year_select.='<option value="'.$i.'" '.$selected.'>'.$i.'年</option>';
			}
			for($i=1; $i<=12; $i++){
				$selected = ($i==$to_date["month"])? 'selected': '';
				$month_select .='<option value="'.$i.'" '.$selected.'>'.$i.'月</option>';
			}
			for($i=1; $i<=31; $i++){
				$selected = ($i==$to_date["day"])? 'selected': '';
				$day_select .='<option value="'.$i.'" '.$selected.'>'.$i.'日</option>';
			}
			for($i=0; $i<=24; $i++){
				$selected = ($i==$to_date["hour"])? 'selected': '';
				$hour_select .='<option value="'.$i.'" '.$selected.'>'.$i.'時</option>';
			}
			
			//画像プレビューの設定
			$image_html ='';
			if(!empty($history_data["HISTORY_IMAGE"])){
				$image_html = '
					<a href="'.MAIN_FOLDER.$history_data["HISTORY_IMAGE"].'" target="_blank"><img src="'.THUM_FOLDER.$history_data["HISTORY_IMAGE"].'"></a><br>
					<label><input type="checkbox" name="image_delete">画像の削除</label>
					<input type="hidden" name="image" value="'.$history_data["HISTORY_IMAGE"].'"><br>
				';
			}
			
			$html ='
				<div id="detail-wrap" class="span4 historyEdit">
					<div class="contentTitle">
						<h3>'.$h3_title.'</h3>
						<span>
							<a href="javascript:slide_wrap(\'#detail-wrap\');" class="icon-minus"></a>
						</span>
					</div>
					<form action="history_post.php" method="post" enctype="multipart/form-data" name="historyForm">
						<fieldset>
							<legend>対応スタッフ</legend>
							<div>
								<select name="staff_id">
									'.$staff_select.'
								</select>
							</div>
						</fieldset>
						<fieldset>
							<legend>対応日時</legend>
							<div>
								<select name="date_array[]" style="width: 80px;">
									'.$year_select.'
								</select>
								<select name="date_array[]" style="width: 60px;">
									'.$month_select.'
								</select>
								<select name="date_array[]" style="width: 60px; margin-right: 10px;">
									'.$day_select.'
								</select>
								<select name="date_array[]" style="width: 60px;">
									'.$hour_select.'
								</select>
							</div>
						</fieldset>
						<fieldset>
							<legend>対応カテゴリ</legend>
							<div>
								<select name="category">
									'.$history_select.'
								</select>
							</div>
						</fieldset>
						<fieldset>
							<legend>対応タイトル</legend>
							<div>
								<input type="text" name="title" value="'.$history_data["HISTORY_TITLE"].'">
							</div>
						</fieldset>
						<fieldset>
							<legend>対応内容詳細</legend>
							<div>
								<textarea name="body" style="width:90%;">'.$history_data["HISTORY_BODY"].'</textarea>
							</div>
						</fieldset>
						<fieldset>
							<legend>関連ファイル</legend>
							<div>
								'.$image_html.'
								<input type="file" name="image">
							</div>
						</fieldset>
						<input type="hidden" name="history_id" value="'.$history_id.'">
						<input type="hidden" name="master_id" value="'.$master_id.'">
						<div class="formAction historyAdd">
							'.$btn_html.'
						</div>
					</form>
				</div>
			';
			
			return $html;
			
		}
		
		
		///--------------------------------------------------------------------
		/// コンテンツの生成（CSV出力）
		///
		///	#Author yk
		/// #date	2013/12/04
		///--------------------------------------------------------------------
		function exportContents(){
			
			//タイトルの設定
			$title = 'CLIANT'.'_'.date('Ymd');
			
			//配信グループの設定
			$groupObj = new Group();
			$group_list = $groupObj->getCliantGroupList();
			$group_select = '<option value="0">全ての顧客</option>';
			foreach($group_list as $value){
				$selected = ($master_data['GROUP_ID']==$value['ID'])? 'selected': '';
				$group_select.='<option value="'.$value['ID'].'" '.$selected.'>'.$value['GROUP_NAME'].'</option>';
			}
			
			//入力フォームの生成
			$html ='
				<div id="edit-wrap" class="span8">
					<div class="contentTitle">
						<h3>'.$this->menu_list[$this->mode]["MENU_NAME"].'</h3>
					</div>
					<form action="csv_export.php" method="post" name="editForm">
						<fieldset>
							<legend>出力ファイル名</legend>
							<div>
								<input type="text" name="title" value="'.$title.'"><br>
								<span class="form_annotation">※出力ファイル名は半角英数字で入力して下さい</span>
							</div>
						</fieldset>
						<fieldset>
							<legend>出力対象設定</legend>
							<div>
								<select name="group_id">
									'.$group_select.'
								</select>
							</div>
						</fieldset>
						<!--fieldset>
							<legend>個別出力</legend>
							<div>
								<input type="text" name="list" value="'.$master_data['CLIANT_LIST'].'">
							</div>
						</fieldset-->
						<div class="formAction">
							<button type="submit" name="download"><i class="icon-download"></i>出力</button>
						</div>
					</form>
				</div>
			';
			
			return $html;
			
		}
		
	}
?>