<?php
	/* /////////////////////////////////////////////////////
	//		メルマガ管理 共通VIEWパッケージ
	//////////////////////////////////////////////////////*/
	
	////////////////////////////////////////////////////////
	//
	//	#substance	パッケージの作成
	//  #Date		2013/11/10
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	//
	//	#substance	個別配信機能の追加
	//  #Date		2014/01/11
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	
//--------------------------------------------------------------------
require_once("../db/op-mailmaga/mailmaga.php");
require_once("../db/op-mailmaga/template.php");
require_once("../db/op-cliant/group.php");
require_once("../db/op-user/user.php");
	
	class View extends Mailmaga{
		
		public $operation, $mode, $state, $mailmaga_id, $group, $row ,$order, $col, $start, $limit_num ,$orderby;
		
		///--------------------------------------------------------------------
		/// 【コンストラクター】
		///
		/// 戻り値　なし
		///
		///	#Author yk
		/// #date	2013/11/10
		///	#Author yk
		/// #date	2014/01/11
		///--------------------------------------------------------------------
		function __construct(){
			//メルマガ管理の初期設定
			$this->menu_list = array(
				'list' => array('MENU_NAME'=>'メルマガ一覧','MENU_ICON'=>'th-list', 'MENU_AUTHORITY'=>'administrator,staff'),
				'new' => array('MENU_NAME'=>'配信予約','MENU_ICON'=>'plus', 'MENU_AUTHORITY'=>'administrator,staff'),
				'template' => array('MENU_NAME'=>'テンプレート編集','MENU_ICON'=>'quote-right', 'MENU_AUTHORITY'=>'administrator,staff'),
				'edit' => array('MENU_NAME'=>'コピー編集','MENU_ICON'=>'th-list', 'MENU_AUTHORITY'=>'', 'PARENT_KEY'=>'list', 'PARENT_NAME'=>'一覧'),
				'detail' => array('MENU_NAME'=>'登録内容','MENU_ICON'=>'th-list', 'MENU_AUTHORITY'=>'', 'PARENT_KEY'=>'list', 'PARENT_NAME'=>'一覧'),
			);
			$this->state_message = array(
				'insert-ok' => array('STATE_COLOR'=>'green', 'STATE_MESSAGE'=>'保存処理が完了いたしました。'),
				'insert-ng' => array('STATE_COLOR'=>'red', 'STATE_MESSAGE'=>'保存処理に失敗いたしました。'),
				'update-ok' => array('STATE_COLOR'=>'green', 'STATE_MESSAGE'=>'更新処理が完了いたしました。'),
				'update-ng' => array('STATE_COLOR'=>'red', 'STATE_MESSAGE'=>'更新処理に失敗いたしました。'),
				'stop-ok' => array('STATE_COLOR'=>'green', 'STATE_MESSAGE'=>'配信停止処理が完了いたしました。'),
				'stop-ng' => array('STATE_COLOR'=>'red', 'STATE_MESSAGE'=>'配信停止処理に失敗いたしました。'),
				'trash-ok' => array('STATE_COLOR'=>'green', 'STATE_MESSAGE'=>'履歴削除処理が完了いたしました。'),
				'trash-ng' => array('STATE_COLOR'=>'red', 'STATE_MESSAGE'=>'履歴削除処理に失敗いたしました。'),
				'input-ng' => array('STATE_COLOR'=>'red', 'STATE_MESSAGE'=>'入力されていない項目があります'),
			);
			$this->mode_list = array(
				'TEXT' => 'テキストメール', 
				'HTML' => 'HTMLメール', 
			);
			$this->specify_list = array(
				'group' => 'グループ', 
				'cliant' => '個別', 
			);
			$this->status_list = array(
				'reserving' => '予約処理中', 
				'reserved' => '配信予約済み', 
				'sending' => '配信中', 
				'complete' => '配信完了',
				'stop' => '配信停止'
			);
		}
		
		///--------------------------------------------------------------------
		/// サイドバーの生成
		///
		///	#Author yk
		/// #date	2013/11/10
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
		/// #date	2013/11/10
		///--------------------------------------------------------------------
		function setContents(){
			
			global $op_list;
			
			$parent_bread ='';
			//ステートの設定
			$state_html = $this->stateMessage();
			
			//メルマガ一覧表示の場合
			if($this->mode=='list'){
				//メルマガリストの取得
				$master_list = parent::searchMailmagaPostList('POST_STATUS', $this->col, $this->orderby, $this->page*$this->limit_num, $this->limit_num);
				$list_count = parent::countMailmagaPostList('POST_STATUS', $this->col);
				//メルマガリストの生成
				$contents_html ='
					<div class="row-fluid r_txt">
						<a href="./?mode=new" class="newBtn"><i class="icon-plus"></i>新規メルマガ登録</a>
					</div>
					<div class="row-fluid">
						<div id="'.$this->mode.'-wrap" class="span12">
							'.$this->listContents($master_list, $list_count).'
						</div>
					</div>
				';
			}
			//メルマガの新規作成・編集の場合
			else if($this->mode=='new' || $this->mode=='edit'){
				//編集の場合
				if($this->mode=='edit' && $this->mailmaga_id){
					//メルマガ情報の取得
					$master_data = parent::getMailmagaPostForID($this->mailmaga_id);
					$parent_bread = '<li><a href="/admin/op-mailmaga/?mode='.$this->menu_list[$this->mode]["PARENT_KEY"].'">'.$this->menu_list[$this->mode]["PARENT_NAME"].'</a></li>';
				}
				//新規保存の場合
				else{
					$master_data ='';
				}
				//テンプレート情報の取得
				$templateObj = new Template();
				$template_list = $templateObj->getMailmagaTemplateList();
				//メルマガ編集の生成
				$contents_html ='
					<div class="row-fluid">
						'.$this->editContents($master_data).'
						<div id="list-wrap" class="span4">
							'.$this->templateListContents($template_list).'
						</div>
					</div>
				';
			}
			//テンプレートの新規作成・編集の場合
			else if($this->mode=='template'){
				//テンプレート情報の取得
				$templateObj = new Template();
				$template_list = $templateObj->getMailmagaTemplateList();
				$template_data = array();
				if(isset($_GET["template_id"])){
					$template_data = $templateObj->getMailmagaTemplateForID($_GET["template_id"]);
				}
				//テンプレートリストの生成
				$contents_html ='
					<div class="row-fluid r_txt">
						<a href="./?mode=template" class="newBtn"><i class="icon-plus"></i>テンプレート新規登録</a>
					</div>
					<div class="row-fluid">
						'.$this->templateEditContents($template_data).'
						<div id="list-wrap" class="span4">
							'.$this->templateListContents($template_list).'
						</div>
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
		/// コンテンツの生成（リスト）
		///
		///	#Author yk
		/// #date	2013/11/10
		///--------------------------------------------------------------------
		function listContents($master_list, $list_count){
			
			$groupObj = new Group();
			
			//リストコントロールの生成
			$ctrl_html ='<li id="all"><a href="javascript:;">全て</a></li>';
			foreach($this->status_list as $key=>$value){
				$ctrl_html.='<li id="'.$key.'"><a href="javascript:;">'.$value.'</a></li>';
			}
			
			//リストHTMLの生成
			$list_html ='';
			foreach($master_list as $value){
				//配信先の設定
				$post_group ='全配信';
				if($value["POST_SPECIFY"]==='cliant'){
					$post_group ='個別配信';
				}else if($value["POST_GROUP"]!='0'){
					$group_data = $groupObj->getCliantGroupForID($value["POST_GROUP"]);
					$post_group = '<div class="group_color '.$group_data['GROUP_COLOR'].'"></div>'.$group_data["GROUP_NAME"];
				}
				
				//配信人数の設定
				$post_group.=(!empty($value["POST_SUM"]))? ' ('.$value["POST_SUM"].'人)': '';
				$list_html.='
					<tr>
						<th>'.date('Y年n月j日 H時i分', strtotime($value["POST_DATE"])).'</th>
						<th>'.mb_strimwidth($value["POST_TITLE"], 0, 50, '...', 'utf-8').'</th>
						<th>'.$post_group.'</th>
						<th><span class="'.$value["POST_STATUS"].'">'.$this->status_list[$value["POST_STATUS"]].'</span></th>
						<td>
							<a class="Btn detailBtn" onClick="ajax_detail(\''.$value["ID"].'\')"><i class="icon-arrow-right"></i><span>確認</span></a>
						</td>
					</tr>
				';
			}
			if(empty($master_list)){
				$list_html ='<tr><th colspan="5" class="no_list">該当するメルマガ情報がありません。</th></tr>';
			}
			
			//ページャーの生成
			global $adminViewObj;
			$url ='&row='.$this->row.'&order='.$this->order.'&col='.$this->col;
			$pager_html = $adminViewObj->pager($list_count, $this->page, $this->limit_num, $url);
			$start = $this->page*$this->limit_num;
			$end = $start + count($master_list);
			
			//コンテンツHTMLの生成
			$html ='
				<div class="contentTitle">
					<h3>'.$this->menu_list['list']["MENU_NAME"].'</h3>
				</div>
				<div class="contentBody">
					<h4>List in<span>&nbsp'.$list_count.'&nbsp</span>mailmagas ( '.($start+1).' to '.$end.' )</h4>
					<ul class="listCtrl">
						'.$ctrl_html.'
					</ul>
					<form action="post.php" method="get" name="myform">
						<table>
							<thead>
								<tr>
									<th id="post_date" class="sortRow">配信予約日時<a href="javascript:;" class="icon-sort"></a></th>
									<th id="post_title" class="min100 sortRow">メルマガ件名<a href="javascript:;" class="icon-sort"></a></th>
									<th class="min200">配信先</th>
									<th class="">配信ステータス</th>
									<td>操作</td>
								</tr>
							</thead>
							<tbody>
								'.$list_html.'
							</tbody>
						</table>
						<div style="overflow:hidden">
							'.$pager_html.'
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
		///	#Author yk
		/// #date	2014/01/10
		///--------------------------------------------------------------------
		function editContents($master_data){
			
			$staffObj = new User();
			$staff_list = $staffObj->getStaffList();
			
			//配信モードの設定					
			$mode_select = '';
			foreach($this->mode_list as $key=>$value){
				$selected = ($master_data['POST_MODE']===$key)? 'selected': '';
				if(empty($master_data['POST_MODE']) && $key==='TEXT'){ $selected='selected'; }
				$mode_select.='<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
				$hidden_class[$key] = ($selected!=='selected')? 'hidden': '';
			}
			
			//配信先指定の設定					
			$specify_radio = '';
			foreach($this->specify_list as $key=>$value){
				$checked = ($master_data['POST_SPECIFY']===$key)? 'checked': '';
				if(empty($master_data['POST_SPECIFY']) && $key==='group'){ $checked='checked'; }
				$specify_radio.='<label><input type="radio" name="post_specify" value="'.$key.'" '.$checked.'>'.$value.'</label>';
				$hidden_class[$key] = ($checked!=='checked')? 'hidden': '';
			}
			
			//配信グループの設定	
			$groupObj = new Group();
			$group_list = $groupObj->getCliantGroupList();							
			$group_select = '<option value="0">全配信</option>';
			foreach($group_list as $value){
				$selected = ($master_data['GROUP_ID']==$value['ID'])? 'selected': '';
				$group_select.='<option value="'.$value['ID'].'" '.$selected.'>'.$value['GROUP_NAME'].'</option>';
			}
			
			//配信日の設定
			$year_select = $month_select = $day_select = $hour_select = $minute_select = '';
			if($this->mode==='new'){
				$post_data["POST_DATE"] = date('Y-m-d H:i:s');
			}
			$to_date = date_parse($post_data["POST_DATE"]);
			for($i=2013; $i<=date('Y')+1; $i++){
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
			for($i=0; $i<=50; $i=$i+10){
				$selected = ($i==round($to_date["minute"],-1)+10)? 'selected': '';
				$minute_select .='<option value="'.$i.'" '.$selected.'>'.$i.'分</option>';
			}
			//配信登録スタッフの設定
			global $authObj;
			$staff_id = $authObj->admin_data["ID"];
			
			//ボタンの設定
			if($this->mode==='new'){
				$btn_html = '<button type="submit" name="'.$this->mode.'"><i class="icon-ok"></i>保存</button><button type="reset">リセット</button>';
			}else{
				$btn_html = '<button type="submit" name="'.$this->mode.'"><i class="icon-refresh"></i>更新</button><a class="deleteBtn" href="post.php?action=delete&id='.$master_data["ID"].'"><i class="icon-trash"></i>削除</a>';
			}
			
			
			
			//入力フォームの生成
			$html ='
				<div id="'.$this->mode.'-wrap" class="span8">
					<div class="contentTitle">
						<h3>'.$this->menu_list[$this->mode]["MENU_NAME"].'</h3>
					</div>
					<form action="post.php" method="post" name="editForm">
						<fieldset>
							<legend>配信予約日</legend>
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
								<select name="date_array[]" style="width: 60px;">
									'.$minute_select.'
								</select>
							</div>
						</fieldset>
						<fieldset>
							<legend>配信モード</legend>
							<div>
								<select name="post_mode">
									'.$mode_select.'
								</select>
							</div>
						</fieldset>
						<fieldset>
							<legend>配信先指定</legend>
							<div>
								'.$specify_radio.'
							</div>
						</fieldset>
						<fieldset id="post_group" class="'.$hidden_class['group'].'">
							<legend>配信先グループ</legend>
							<div>
								<select name="post_group">
									'.$group_select.'
								</select>
							</div>
						</fieldset>
						<fieldset id="post_cliant" class="'.$hidden_class['cliant'].'">
							<legend>個別配信先</legend>
							<div>
								<input type="text" name="post_cliant" value="'.$master_data['POST_CLIANT'].'"><span class="hissu">必須</span><br>
								<span class="form_annotation">※顧客IDをコンマ区切りで入力してください。（例：00175,00176,00252）</span>
							</div>
						</fieldset>
						<fieldset>
							<legend>メルマガ件名</legend>
							<div>
								<input type="text" name="post_title" value="'.$master_data['POST_TITLE'].'"><span class="hissu">必須</span>
							</div>
						</fieldset>
						<fieldset id="html_mode" class="'.$hidden_class['HTML'].'">
							<legend>HTML本文<br></legend>
							<div>
								<textarea name="pc_html_body">'.$master_data['PC_HTML_BODY'].'</textarea><span class="hissu">必須</span>
							</div>
						</fieldset>
						<fieldset>
							<legend>テキスト本文<br></legend>
							<div>
								<textarea name="pc_text_body">'.$master_data['PC_TEXT_BODY'].'</textarea><span class="hissu">必須</span>
							</div>
						</fieldset>
						<fieldset id="text_mode" class="'.$hidden_class['TEXT'].'">
							<legend>テキスト本文<br>（モバイル）</legend>
							<div>
								<textarea name="mb_text_body">'.$master_data['MB_TEXT_BODY'].'</textarea><span class="hissu">必須</span>
							</div>
						</fieldset>
						<input type="hidden" name="id" value="'.$master_data["ID"].'">
						<input type="hidden" name="staff_id" value="'.$staff_id.'">
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
		///--------------------------------------------------------------------
		function detailContents($master_data){
			
			//配信予約スタッフの設定
			$staff_data["DISPLAY_NAME"] = '未設定';
			if(!empty($master_data["STAFF_ID"])){
				$staffObj = new User();
				$staff_data = $staffObj->getStaffForID($master_data["STAFF_ID"]);
			}
			
			//配信グループの設定
			$post_group ='個別配信';
			if($master_data["POST_GROUP"]!='0'){
				$groupObj = new Group();
				$group_data = $groupObj->getCliantGroupForID($master_data["POST_GROUP"]);
				$post_group = '<span class="group_color '.$group_data['GROUP_COLOR'].'"></span>'.$group_data["GROUP_NAME"];
			}else{
				$post_group ='全配信';
			}
			//配信人数の設定
			$post_group.=(!empty($master_data["POST_SUM"]))? ' ('.$master_data["POST_SUM"].'人)': '';
			
			if($master_data["POST_STATUS"]==='complete' || $master_data["POST_STATUS"]==='stop'){
				$btn_html ='
					<button type="submit" name="mode" value="edit"><i class="icon-copy"></i>コピー作成</button>
					<a href="/admin/op-mailmaga/post.php?action=trash&id='.$master_data["ID"].'" class="Btn trashBtn"><i class="icon-remove"></i>履歴削除</a>
				';
			}else{
				$btn_html ='
					<a href="/admin/op-mailmaga/post.php?action=stop&id='.$master_data["ID"].'" class="Btn stopBtn"><i class="icon-minus"></i>配信停止</a>
					<button type="submit" name="mode" value="edit"><i class="icon-copy"></i>コピー作成</button>
				';
			}
			
			$html ='
				<div id="detail-wrap" class="span4">
					<div class="contentTitle">
						<h3>予約内容</h3>
						<span>
							<a href="javascript:close_wrap(\'#detail-wrap\');" class="icon-remove"></a>
						</span>
					</div>
					<form action="./" method="get">
						<fieldset>
							<legend>配信予約スタッフ</legend>
							<p>'.$staff_data["DISPLAY_NAME"].'</p>
						</fieldset>
						<fieldset>
							<legend>配信予約日時</legend>
							<p>'.date('Y年n月j日 H時i分', strtotime($master_data["POST_DATE"])).'</p>
						</fieldset>
						<fieldset>
							<legend>配信モード</legend>
							<p>'.$this->mode_list[$master_data["POST_MODE"]].'</p>
						</fieldset>
						<fieldset>
							<legend>配信先</legend>
							<p>'.$post_group.'</p>
						</fieldset>
						<fieldset>
							<legend>メルマガ件名</legend>
							<p>'.$master_data["POST_TITLE"].'</p>
						</fieldset>
						<fieldset class="text">
							<legend>テキスト本文(PC)</legend>
							<p>'.nl2br(htmlspecialchars($master_data["PC_TEXT_BODY"], ENT_QUOTES, 'UTF-8', false)).'</p>
						</fieldset>
						<fieldset class="text">
							<legend>テキスト本文(モバイル)</legend>
							<p>'.nl2br(htmlspecialchars($master_data["MB_TEXT_BODY"], ENT_QUOTES, 'UTF-8', false)).'</p>
						</fieldset>
						<fieldset>
							<legend>登録日時</legend>
							<p>'.date('Y年n月j日 H時i分', strtotime($master_data["INS_DATE"])).'</p>
						</fieldset>
						<div class="formAction">
							<input type="hidden" name="id" value="'.$master_data["ID"].'">
							'.$btn_html.'
						</div>
					</form>
				</div>
			';
			
			return $html;
			
		}
		
		///--------------------------------------------------------------------
		/// コンテンツの生成（テンプレート編集）
		///
		///	#Author yk
		/// #date	2013/11/10
		///--------------------------------------------------------------------
		function templateEditContents($template_data){
			
			//タイトル設定
			$template_h3 = ($template_data['ID']=='')? 'テンプレート新規登録': 'テンプレート編集';
			
			//ボタンの設定
			if(empty($template_data)){
				$btn_html ='<button type="submit" name="'.$this->mode.'-new"><i class="icon-ok"></i>保存</button><button type="reset">リセット</button>';
			}else{
				$btn_html ='<button type="submit" name="'.$this->mode.'-edit"><i class="icon-refresh"></i>更新</button><a class="deleteBtn" href="template_post.php?action='.$this->mode.'-delete&id='.$template_data["ID"].'"><i class="icon-trash"></i>削除</a>';
			}
			
			$html ='
				<div id="new-wrap" class="span8">
					<div class="contentTitle">
						<h3>'.$template_h3.'</h3>
					</div>
					<form action="template_post.php" method="post" enctype="multipart/form-data">
						<fieldset>
							<legend>テンプレート名</legend>
								<div>
									<input type="text" name="name" value="'.$template_data['TEMPLATE_NAME'].'">
									<span class="hissu">必須</span>
								</div>
							</legend>
						</fieldset>
						<fieldset>
							<legend>テキスト本文(PC)</legend>
							<div>
								<textarea name="pc_text_body" value="'.$template_data['PC_TEXT_BODY'].'">'.$template_data['PC_TEXT_BODY'].'</textarea>
							</div>
						</fieldset>
						<fieldset>
							<legend>テキスト本文(モバイル)</legend>
							<div>
								<textarea name="mb_text_body" value="'.$template_data['MB_TEXT_BODY'].'">'.$template_data['MB_TEXT_BODY'].'</textarea>
							</div>
						</fieldset>
						<div class="formAction">
							<input type="hidden" name="id" value="'.$template_data["ID"].'">
							'.$btn_html.'
						</div>
					</form>
				</div>
			';
			
			return $html;
			
		}
		
		///--------------------------------------------------------------------
		/// コンテンツの生成（テンプレートリスト）
		///
		///	#Author yk
		/// #date	2013/11/10
		///--------------------------------------------------------------------
		function templateListContents($template_list){
			
			//リストHTMLの生成
			$list_html ='';
			foreach($template_list as $value){
				if($this->mode!=='template'){
					$btn_html = '<a class="Btn detailBtn" onClick="ajax_temp_detail(\''.$value["ID"].'\')"><span>内容確認</span></a>';
				}else{
					$btn_html = '<a href="/admin/op-mailmaga/?mode=template&template_id='.$value["ID"].'" class="Btn editBtn"><i class="icon-pencil"></i><span>編集</span></a>';
				}
				
				$list_html.='
					<tr>
						<th>
							'.$value["TEMPLATE_NAME"].'
						</th>
						<th>
							'.$btn_html.'
						</th>
					</tr>
				';
			}
			if(empty($template_list)){
				$list_html.='<tr><td colspan="2"><p class="no_list">登録されているテンプレートがありません</p></td></tr>';
			}
			
			//HTMLの生成
			$html ='
				<div class="contentTitle">
					<h3>テンプレート一覧</h3>
				</div>
				<div class="contentBody">
					<table>
						<thead>
							<tr>
								<th colspan="2">テンプレートタイトル</th>
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
		/// コンテンツの生成（テンプレート内容確認）
		///
		///	#Author yk
		/// #date	2013/11/12
		///--------------------------------------------------------------------
		function templateDetailContents($template_data){
			
			$html ='
				<div class="contentTitle">
					<h3>テンプレート内容</h3>
				</div>
				<form action="./" method="get">
					<!--fieldset>
						<legend>テンプレートタイトル</legend>
						<p>'.$template_data["TEMPLATE_NAME"].'</p>
					</fieldset-->
					<fieldset class="text">
						<legend>テキスト本文（PC）</legend>
						<p>'.nl2br(htmlspecialchars($template_data["PC_TEXT_BODY"], ENT_QUOTES, 'UTF-8', false)).'</p>
					</fieldset>
					<fieldset class="text">
						<legend>テキスト本文（モバイル）</legend>
						<p>'.nl2br(htmlspecialchars($template_data["MB_TEXT_BODY"], ENT_QUOTES, 'UTF-8', false)).'</p>
					</fieldset>
					<div class="formAction">
						<input type="hidden" name="pc_text_body" value="'.$template_data["PC_TEXT_BODY"].'">
						<input type="hidden" name="mb_text_body" value="'.$template_data["MB_TEXT_BODY"].'">
						<a class="Btn addBtn" id="addTemplate"><i class="icon-share-square"></i><span>本文へ追加</span></a>
						<a class="Btn backBtn" onClick="ajax_temp_list()"><i class="icon-penchil"></i><span>リストへ戻る</span></a>
					</div>
				</form>
			';
			
			return $html;
			
		}
	}
?>