<?php
	/* /////////////////////////////////////////////////////
	//		ユーザー管理 共通VIEWパッケージ
	//////////////////////////////////////////////////////*/
	
	////////////////////////////////////////////////////////
	//
	//	#substance	パッケージの作成
	//  #Date		2013/05/26
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	//
	//	#substance	ー
	//  #Date		----/--/--
	//	#Author 	--
	//
	////////////////////////////////////////////////////////
	
//--------------------------------------------------------------------
require_once("../db/op-user/user.php");
	
	class View extends User{
		
		public $operation, $mode, $state, $user_id, $row ,$order, $col, $start, $limit_num;
		
		///--------------------------------------------------------------------
		/// 【コンストラクター】
		///
		/// 戻り値　なし
		///
		///	#Author yk
		/// #date	2013/05/26
		///--------------------------------------------------------------------
		function __construct(){
			//ユーザー管理の初期設定
			$this->menu_list = array(
				'list' => array('MENU_NAME'=>'一覧','MENU_ICON'=>'th-list', 'MENU_AUTHORITY'=>'72web,administrator,staff'),
				'new' => array('MENU_NAME'=>'新規作成','MENU_ICON'=>'plus', 'MENU_AUTHORITY'=>'72web,administrator'),
				'edit' => array('MENU_NAME'=>'情報編集','MENU_ICON'=>'th-list', 'MENU_AUTHORITY'=>'', 'PARENT_KEY'=>'list', 'PARENT_NAME'=>'一覧'),
				'detail' => array('MENU_NAME'=>'登録情報','MENU_ICON'=>'th-list', 'MENU_AUTHORITY'=>'', 'PARENT_KEY'=>'list', 'PARENT_NAME'=>'一覧'),
			);
			$this->state_message = array(
				'insert-ok' => array('STATE_COLOR'=>'green', 'STATE_MESSAGE'=>'保存処理が完了いたしました。'),
				'insert-ng' => array('STATE_COLOR'=>'red', 'STATE_MESSAGE'=>'保存処理に失敗いたしました。'),
				'update-ok' => array('STATE_COLOR'=>'green', 'STATE_MESSAGE'=>'更新処理が完了いたしました。'),
				'update-ng' => array('STATE_COLOR'=>'red', 'STATE_MESSAGE'=>'更新処理に失敗いたしました。'),
				'trash-ok' => array('STATE_COLOR'=>'green', 'STATE_MESSAGE'=>'削除処理が完了いたしました。'),
				'trash-ng' => array('STATE_COLOR'=>'red', 'STATE_MESSAGE'=>'削除処理に失敗いたしました。'),
				'input-ng' => array('STATE_COLOR'=>'red', 'STATE_MESSAGE'=>'入力されていない項目があります'),
				'account-ng' => array('STATE_COLOR'=>'red', 'STATE_MESSAGE'=>'ログインIDは半角英数字のみ使用して下さい'),
				'account_2-ng' => array('STATE_COLOR'=>'red', 'STATE_MESSAGE'=>'ログインIDを入力してください'),
				'password-ng' => array('STATE_COLOR'=>'red', 'STATE_MESSAGE'=>'パスワードは半角英数字のみ使用して下さい'),
				'password_2-ng' => array('STATE_COLOR'=>'red', 'STATE_MESSAGE'=>'確認用パスワードと一致しません'),
				'mail-ng' => array('STATE_COLOR'=>'red', 'STATE_MESSAGE'=>'正しくメールアドレスを入力して下さい'),
				'mail_2-ng' => array('STATE_COLOR'=>'red', 'STATE_MESSAGE'=>'確認用パスワードと一致しません'),
			);
		}
		
		///--------------------------------------------------------------------
		/// サイドバーの生成
		///
		///	#Author yk
		/// #date	2013/05/26
		///	#Author yk
		/// #date	2013/11/17
		///--------------------------------------------------------------------
		function setSideBar(){
			
			//ユーザー情報の取得
			global $authObj;
			
			foreach ($this->menu_list as $key => $value){
				if($value["MENU_AUTHORITY"]!='' && strstr($value["MENU_AUTHORITY"], $authObj->admin_data["ADMIN_AUTHORITY"])){
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
		/// #date	2013/05/26
		///--------------------------------------------------------------------
		function setContents(){
			
			global $op_list;
			
			$parent_bread ='';
			//ステートの設定
			$state_html = $this->stateMessage();
			
			//ユーザー一覧表示の場合
			if($this->mode=='list'){
				//ユーザーリストの取得
				$master_list = parent::searchUserMasterList('ADMIN_AUTHORITY', $this->col, '', $this->page*$this->limit_num, $this->limit_num);
				//$master_list = parent::getUserMasterList($orderby);
				$list_count = parent::countUserMasterList('ADMIN_AUTHORITY', $this->col);
				//$contents_html = $this->listContents($master_list);
				//ユーザーリストの生成
				$contents_html ='
					<div class="row-fluid r_txt">
						<a href="./?mode=new" class="newBtn"><i class="icon-plus"></i>新規ユーザー登録</a>
					</div>
					<div class="row-fluid">
						<div id="'.$this->mode.'-wrap" class="span12">
							'.$this->listContents($master_list, $list_count).'
						</div>
					</div>
				';
			}
			//ユーザーの新規作成・編集の場合
			else if($this->mode=='new' || $this->mode=='edit'){
				//編集の場合
				if($this->mode=='edit' && $this->user_id){
					//ユーザー情報の取得
					$master_data = parent::getUserMasterForID($this->user_id);
					$parent_bread = '<li><a href="/admin/op-user/?mode='.$this->menu_list[$this->mode]["PARENT_KEY"].'">'.$this->menu_list[$this->mode]["PARENT_NAME"].'</a></li>';
				}
				//新規保存の場合
				else{
					$master_data ='';
				}
				$contents_html = $this->editContents($master_data);
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
		/// #date	2013/05/22
		///--------------------------------------------------------------------
		function listContents($master_list, $list_count){
			
			global $authority_list;
			
			//リストコントロールの生成
			$ctrl_html ='<li id="all"><a href="javascript:;">全て</a></li>';
			foreach($authority_list as $key=>$value){
				if($key!=='72web'){ $ctrl_html.='<li id="'.$key.'"><a href="javascript:;">'.$value["AUTHORITY_NAME"].'</a></li>';}
			}
			
			//リストHTMLの生成
			$list_html ='';
			foreach($master_list as $value){
				$display_authority = $authority_list[$value["ADMIN_AUTHORITY"]]['AUTHORITY_NAME'];
				$list_html.='
					<tr>
						<th>'.$display_authority.'</th>
						<th>'.$value["DISPLAY_NAME"].'</th>
						<td>
							<a class="Btn detailBtn" onClick="ajax_detail(\''.$value["ID"].'\')"><i class="icon-arrow-right"></i><span>確認</span></a>
						</td>
					</tr>
				';
			}
			if(empty($master_list)){
				$list_html ='<tr><th colspan="3" class="no_list">該当するユーザー情報がありません。</th></tr>';
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
					<h4>List in<span>&nbsp'.$list_count.'&nbsp</span>users ( '.($start+1).' to '.$end.' )</h4>
					<ul class="listCtrl">
						'.$ctrl_html.'
					</ul>
					<table>
						<thead>
							<tr>
								<th id="admin_authority" class="min100 sortRow">ユーザー権限<a href="javascript:;" class="icon-sort"></a></th>
								<th class="title">スタッフ名</th>
								<td>操作</td>
							</tr>
						</thead>
						<tbody>
							'.$list_html.'
						</tbody>
					</table>
					'.$pager_html.'
				</div>
			';
			
			
			return $html;
			
		}
		
		///--------------------------------------------------------------------
		/// コンテンツの生成（エディット）
		///
		///	#Author yk
		/// #date	2013/05/26
		///--------------------------------------------------------------------
		function editContents($master_data){
			
			//ユーザー情報の取得
			global $authObj;
			
			//ユーザー権限の設定
			global $authority_list;
			$authority_option ='';
			foreach($authority_list as $key=>$value){
				$selected = ($master_data["ADMIN_AUTHORITY"]===$key)? 'selected': '';
				if(empty($master_data["ADMIN_AUTHORITY"]) && $key==='staff') $selected ='selected';
				if($key!=='72web'){ $authority_option.='<option value="'.$key.'" '.$selected.'>'.$value["AUTHORITY_NAME"].'</option>';}
			}
			
			//パスワードの初期表示
			$password_display = '';
			if($this->mode==='edit'){
				$password_display ='no_change';
			}
			
			if($this->mode==='new'){
				$btn_html = '<button type="submit" name="'.$this->mode.'"><i class="icon-ok"></i>保存</button><button type="reset">リセット</button>';
			}
			else{
				$btn_html = '
					<button type="button" class="backBtn"><i class="icon-arrow-left"></i>戻る</button>
					<button type="submit" name="'.$this->mode.'"><i class="icon-refresh"></i>更新</button>
				';
				if($authObj->admin_data["ADMIN_AUTHORITY"]!=='staff'){
					$btn_html.= '<a class="deleteBtn" href="post.php?action=trash&id='.$master_data["ID"].'"><i class="icon-trash"></i><span>削除<span></a>';
				}
			}
			
			if($authObj->admin_data["ADMIN_AUTHORITY"]!=='staff' || $authObj->admin_data["ID"]==$master_data["ID"]){
				$html ='
					<div class="row-fluid">
						<div id="'.$this->mode.'-wrap" class="span10">
							<div class="contentTitle">
								<h3>'.$this->menu_list[$this->mode]["MENU_NAME"].'</h3>
							</div>
							<form action="post.php" method="post">
								<fieldset>
									<legend>ユーザー権限</legend>
									<div>
										<select name="authority">
											'.$authority_option.'
										</select>
									</div>
								</fieldset>
								<fieldset>
									<legend>ログインID</legend>
									<div>
										<input type="text" name="account" value="'.$master_data["ADMIN_ACCOUNT"].'"><span class="hissu">必須</span>
									</div>
								</fieldset>
								<fieldset>
									<legend>パスワード</legend>
									<div>
										<input type="password" name="password" value="'.$password_display.'"><span class="hissu">必須</span>
									</div>
								</fieldset>
								<fieldset>
									<legend>パスワード（確認）</legend>
									<div>
										<input type="password" name="password_2" value="'.$password_display.'"><span class="hissu">必須</span>
									</div>
								</fieldset>
								<fieldset>
									<legend>メールアドレス</legend>
									<div>
										<input type="text" name="mail" value="'.$master_data["ADMIN_MAIL"].'"><span class="hissu">必須</span>
									</div>
								</fieldset>
								<fieldset>
									<legend>メールアドレス（確認）</legend>
									<div>
										<input type="text" name="mail_2" value="'.$master_data["ADMIN_MAIL"].'"><span class="hissu">必須</span>
									</div>
								</fieldset>
								<fieldset>
									<legend>スタッフ名</legend>
									<div>
										<input type="text" name="name" value="'.$master_data["DISPLAY_NAME"].'"><span class="hissu">必須</span>
									</div>
								</fieldset>
								<!--fieldset>
									<legend>スタッフ名（フリガナ）</legend>
									<div>
										<input type="text" name="kana" value="'.$master_data["DISPLAY_KANA"].'">
									</div>
								</fieldset-->
								<fieldset>
									<legend>スタッフ所属先</legend>
									<div>
										<input type="text" name="post" value="'.$master_data["DISPLAY_POST"].'">
									</div>
								</fieldset>
								<input type="hidden" name="id" value="'.$master_data["ID"].'">
								<div class="formAction">
									'.$btn_html.'
								</div>
							</form>
						</div>
					</div>
				';
			}else{
				$html ='
					<div class="row-fluid">
						<div id="'.$this->mode.'-wrap" class="span10">
							<div class="contentTitle">
								<h3>'.$this->menu_list[$this->mode]["MENU_NAME"].'</h3>
							</div>
							<p class="no_authority">編集権限がありません。</p>
						</div>
					</div>
				';
			}
			
			return $html;
			
		}
		
		///--------------------------------------------------------------------
		/// コンテンツの生成（内容確認）
		///
		///	#Author yk
		/// #date	2013/05/26
		///--------------------------------------------------------------------
		function detailContents($master_data){
			
			global $authority_list;
			$display_authority = $authority_list[$master_data["ADMIN_AUTHORITY"]]['AUTHORITY_NAME'];
			
			$html ='
				<div id="detail-wrap" class="span4">
					<div class="contentTitle">
						<h3>'.$this->menu_list[$this->mode]["MENU_NAME"].'</h3>
						<span>
							<a href="javascript:close_wrap(\'#detail-wrap\');" class="icon-remove"></a>
						</span>
					</div>
					<form action="./" method="get">
						<fieldset>
							<legend>ユーザー権限</legend>
							<p>'.$display_authority.'</p>
						</fieldset>
						<fieldset>
							<legend>ユーザー名</legend>
							<p>'.$master_data["DISPLAY_NAME"].'</p>
						</fieldset>
						<fieldset>
							<legend>ログインID</legend>
							<p>'.$master_data["ADMIN_ACCOUNT"].'</p>
						</fieldset>
						<fieldset>
							<legend>メールアドレス</legend>
							<p><a href="mailto:'.$master_data["ADMIN_MAIL"].'">'.$master_data["ADMIN_MAIL"].'</a></p>
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
	}
?>