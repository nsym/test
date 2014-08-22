<?php
	/* /////////////////////////////////////////////////////
	//		DMラベル管理 共通VIEWパッケージ
	//////////////////////////////////////////////////////*/
	
	////////////////////////////////////////////////////////
	//
	//	#substance	パッケージの作成
	//  #Date		2013/11/10
	//	#Author 	mukai
	//
	////////////////////////////////////////////////////////
	//
	//	#substance	ー
	//  #Date		----/--/--
	//	#Author 	--
	//
	////////////////////////////////////////////////////////
	
//--------------------------------------------------------------------
require_once("../db/op-dmlabel/dmlabel.php");
require_once("../db/op-dmlabel/cliant.php");
require_once("../db/op-cliant/group.php");
require_once("../db/op-user/user.php");
	
	class View extends DMlabel{
		
		public $operation, $mode, $state, $dmlabel_id, $group, $row ,$order, $col, $start, $limit_num ,$orderby;
		
		///--------------------------------------------------------------------
		/// 【コンストラクター】
		///
		/// 戻り値　なし
		///
		///	#Author mukai
		/// #date	2013/11/10
		///--------------------------------------------------------------------
		function __construct(){
			//DMラベル管理の初期設定
			$this->menu_list = array(
				'list' => array('MENU_NAME'=>'一覧','MENU_ICON'=>'th-list', 'MENU_AUTHORITY'=>'administrator,staff'),
				'new' => array('MENU_NAME'=>'新規作成','MENU_ICON'=>'plus', 'MENU_AUTHORITY'=>'administrator,staff'),
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
				'input-ng' => array('STATE_COLOR'=>'red', 'STATE_MESSAGE'=>'入力されていない項目があります'),
			);
		}
		
		///--------------------------------------------------------------------
		/// サイドバーの生成
		///
		///	#Author mukai
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
		///	#Author mukai
		/// #date	2013/11/10
		///--------------------------------------------------------------------
		function setContents(){
			
			global $op_list;
			
			$parent_bread ='';
			//ステートの設定
			$state_html = $this->stateMessage();
			
			//DMラベル一覧表示の場合
			if($this->mode=='list'){
				//DMラベルリストの取得
				$master_list = parent::searchDMlabelMasterList('EDIT_DATE', $this->col, $this->orderby, $this->page*$this->limit_num, $this->limit_num);
				$list_count = $this->countDMlabelMasterList('', $this->col);
				//DMラベルリストの生成
				$contents_html ='
					<div class="row-fluid r_txt">
						<a href="./?mode=new" class="newBtn"><i class="icon-plus"></i>新規DMラベル登録</a>
					</div>
					<div class="row-fluid">
						<div id="'.$this->mode.'-wrap" class="span12">
							'.$this->listContents($master_list, $list_count).'
						</div>
					</div>
				';
			}
			//DMラベルの新規作成・編集の場合
			else if($this->mode=='new' || $this->mode=='edit'){
				//編集の場合
				if($this->mode=='edit' && $this->dmlabel_id){
					//DMラベル情報の取得
					$master_data = parent::getDMlabelMasterForID($this->dmlabel_id);
					$parent_bread = '<li><a href="/admin/op-dmlabel/?mode='.$this->menu_list[$this->mode]["PARENT_KEY"].'">'.$this->menu_list[$this->mode]["PARENT_NAME"].'</a></li>';
				}
				//新規保存の場合
				else{
					$master_data ='';
				}
				//DMラベルリストの生成
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
		/// コンテンツの生成（リスト）
		///
		///	#Author mukai
		/// #date	2013/11/10
		///--------------------------------------------------------------------
		function listContents($master_list, $list_count){
			//グループのインスタンス化
			$groupObj = new Group();
			//リストHTMLの生成
			$list_html ='';
			foreach($master_list as $value){
				//出力先の表示設定
				if($value['GROUP_ID']!=0){
					$group_list = $groupObj->getCliantGroupForID($value['GROUP_ID']);
					$group_icon = '<div class="group_color '.$group_list['GROUP_COLOR'].'"></div><span>'.$group_list['GROUP_NAME'].'</span>';
				}else{
					$group_icon = '全ての顧客';
				}
				$list_html.='
					<tr>
						<th>'.date('Y年m月d日 H時i分', strtotime($value["EDIT_DATE"])).'</th>
						<th>'.$value["LABEL_TITLE"].'</th>
						<th>'.$group_icon.'</th>
						<!--th>'.$value["CLIANT_LIST"].'</th-->
						<td>
							<a href="pdf_creat.php?action=test&id='.$value["ID"].'" class="Btn"><i class="icon-file"></i><span>テスト出力</span></a>
							<a class="Btn detailBtn" onClick="ajax_detail(\''.$value["ID"].'\')"><i class="icon-arrow-right"></i><span>確認</span></a>
						</td>
					</tr>
				';
			}
			if(empty($master_list)){
				$list_html ='<tr><th colspan="3" class="no_list">該当するDMラベル情報がありません。</th></tr>';
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
					<h4>List in<span>&nbsp'.$list_count.'&nbsp</span>item ( '.($start+1).' to '.$end.' )</h4>
					<form action="post.php" method="get" name="myform">
						<table>
							<thead>
								<tr>
									<th id="edit_date" class="min100 sortRow">最終出力日時<a href="javascript:;" class="icon-sort"></a></th>
									<th id="label_title" class="min100 sortRow">DMラベル名<a href="javascript:;" class="icon-sort"></a></th>
									<th class="min100">出力対象設定</th>
									<!--th class="min100">個別出力</th-->
									<td>操作</td>
								</tr>
							</thead>
							<tbody>
								'.$list_html.'
							</tbody>
						</table>					
						'.$pager_html.'
					</from>
				</div>
			';
			
			
			return $html;
			
		}
		
		
		///--------------------------------------------------------------------
		/// コンテンツの生成（エディット）
		///
		///	#Author mukai
		/// #date	2013/11/10
		///--------------------------------------------------------------------
		function editContents($master_data){
			//タイトルの設定
			$title = ($this->mode=='new')?'DMラベル（'.date('Y年m月d日').'作成）':''.$master_data['LABEL_TITLE'].'コピー';
			//配信グループの設定
			$groupObj = new Group();
			$group_list = $groupObj->getCliantGroupList();
			$group_select = '<option value="0">全ての顧客</option>';
			foreach($group_list as $value){
				$selected = ($master_data['GROUP_ID']==$value['ID'])? 'selected': '';
				$group_select.='<option value="'.$value['ID'].'" '.$selected.'>'.$value['GROUP_NAME'].'</option>';
			}
			//用紙面数設定
			$mode_selected['A'] = ($master_data['PIECE_MODE'] =='10')?' selected':'';
			$mode_selected['B'] = ($master_data['PIECE_MODE'] =='12' || $master_data['PIECE_MODE'] =='')?' selected':'';
			$print_num ='
				<option value="10"'.$mode_selected['A'].'>10</option>
				<option value="12"'.$mode_selected['B'].'>12</option>
			';

			//用紙スタート位置の設定
			for($i=1; $i<=12; $i++){
				$selected = ($master_data['PIECE_START'] == $i)? ' selected': '';
				$ten_hidden = ($i>10)? 'ten_hidden': '';
				$start_select .='
					<option value="'.$i.'" class="'.$ten_hidden.'" '.$selected.'>'.$i.'</option>
				';	
			}
			
			//用紙設定の初期値
			if($this->mode=='new'){
				$master_data = array(
					"MARGIN_TOP"=>"22.5",
					"MARGIN_LEFT"=>"19.0",
					"PADDING_TOP"=>"3.0",
					"PADDING_LEFT"=>"7.0",
					"PIECE_WIDTH"=>"84.0",
					"PIECE_HEIGHT"=>"42.0",
					"PIECE_SPACE"=>"4.0"
				);
			}
			
			//項目ヘルプの表示設定
			$help_img_class[10] = ($master_data['PIECE_MODE'] =='10')? '': 'hidden';
			$help_img_class[12] = ($master_data['PIECE_MODE'] =='12' || $master_data['PIECE_MODE'] =='')? '': 'hidden';
			
			
			//ボタンの設定
			if($this->mode==='new'){
				$btn_html = '<button type="submit" name="'.$this->mode.'"><i class="icon-ok"></i>保存</button><button type="reset">リセット</button>';
			}
			else{
				$btn_html = '<button type="submit" name="'.$this->mode.'"><i class="icon-refresh"></i>保存</button><a class="deleteBtn" href="post.php?action=delete&id='.$master_data["ID"].'"><i class="icon-trash"></i>削除</a>';
			}
			
			
			//入力フォームの生成
			$html ='
				<div id="'.$this->mode.'-wrap" class="span8">
					<div class="contentTitle">
						<h3>'.$this->menu_list[$this->mode]["MENU_NAME"].'</h3>
					</div>
					<form action="post.php" method="post" name="editForm">
						<fieldset>
							<legend>DMラベル名</legend>
							<div>
								<input type="text" name="title" value="'.$title.'">
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
						<fieldset class="telnum">
							<legend>①用紙面数</legend>
							<div>
								<select name="mode">
									'.$print_num.'
								</select>
							</div>
						</fieldset>
						<fieldset class="telnum">
							<legend>②用紙上部余白</legend>
							<div>
								<input type="text" name="margin_top" value="'.$master_data['MARGIN_TOP'].'">mm
							</div>
						</fieldset>
						<fieldset class="telnum">
							<legend>③用紙左部余白</legend>
							<div>
								<input type="text" name="margin_left" value="'.$master_data['MARGIN_LEFT'].'">mm
							</div>
						</fieldset>
						<fieldset class="telnum">
							<legend>④ラベル内上部余白</legend>
							<div>
								<input type="text" name="padding_top" value="'.$master_data['PADDING_TOP'].'">mm
							</div>
						</fieldset>
						<fieldset class="telnum">
							<legend>⑤ラベル内左部余白</legend>
							<div>
								<input type="text" name="padding_left" value="'.$master_data['PADDING_LEFT'].'">mm
							</div>
						</fieldset>
						<fieldset class="telnum">
							<legend>⑥ラベル横幅</legend>
							<div>
								<input type="text" name="width" value="'.$master_data['PIECE_WIDTH'].'">mm
							</div>
						</fieldset>
						<fieldset class="telnum">
							<legend>⑦ラベル縦幅</legend>
							<div>
								<input type="text" name="height" value="'.$master_data['PIECE_HEIGHT'].'">mm
							</div>
						</fieldset>
						<fieldset class="telnum">
							<legend>⑧ラベル横間隔</legend>
							<div>
								<input type="text" name="space" value="'.$master_data['PIECE_SPACE'].'">mm
							</div>
						</fieldset>
						<fieldset>
							<legend>⑨追加記載項目</legend>
							<div>
								<select name="item">
									<option value="">追加しない</option>
									<option value="STAFF_ID">スタッフID</option>
								</select>
							</div>
						</fieldset>
						<fieldset>
							<legend>⑩面開始位置</legend>
							<div>
								<select name="start">
									'.$start_select.'
								</select>
							</div>
						</fieldset>
						<fieldset>
							<legend>備考（管理用）</legend>
							<div>
								<textarea name="body">'.$master_data['LABEL_BODY'].'</textarea>
							</div>
						</fieldset>
						<input type="hidden" name="id" value="'.$master_data["ID"].'">
						<div class="formAction">
							'.$btn_html.'
						</div>
					</form>
				</div>
				<div id="'.$this->mode.'-wrap" class="span4">
					<div class="contentTitle">
						<h3>項目ヘルプ</h3>
					</div>
					<img src="dm-sheet-10.png" class="'.$help_img_class[10].' ten_hidden"/>
					<img src="dm-sheet-12.png" class="'.$help_img_class[12].' ten_hidden"/>
				</div>
			';
			
			return $html;
			
		}
		
		///--------------------------------------------------------------------
		/// コンテンツの生成（内容確認）
		///
		///	#Author mukai
		/// #date	2013/11/10
		///--------------------------------------------------------------------
		function detailContents($master_data){
			//グループをID呼び出し
			//グループのインスタンス化
			$groupObj = new Group();
			$group_list = $groupObj->getCliantGroupForID($master_data['GROUP_ID']);
			if($master_data['GROUP_ID']==$group_list['ID']){
				$group_icon = '
					<div class="group_color '.$group_list['GROUP_COLOR'].'"></div><span>'.$group_list['GROUP_NAME'].'</span>;
				';	
			}
			else if($master_data['GROUP_ID'] == 0){
				$group_icon = '全ての顧客';
			}
			//クライアントリストの取得
			$cliantObj = new Cliant();
			$cliant_list = $cliantObj->countCliantDMListForGroupID($master_data['GROUP_ID']);

			//print_r($cliant_list);		
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
							<legend>最終出力日時</legend>
							<div>
								<p>'.date('Y年m月d日 H時i分', strtotime($master_data["EDIT_DATE"])).'</p>
							</div>
						</fieldset>
						<fieldset>
							<legend>DMラベル名</legend>
							<div>
								<p>'.$master_data['LABEL_TITLE'].'</p>
							</div>
						</fieldset>
						<fieldset>
							<legend>出力対象設定</legend>
							<div>
								<p>'.$group_icon.'（'.$cliant_list.'人）</p>
							</div>
						</fieldset>
						<!--fieldset>
							<legend>個別出力</legend>
							<div>
								<p>'.$master_data['CLIANT_LIST'].'</p>
							</div>
						</fieldset-->
						<fieldset class="telnum">
							<legend>用紙面数</legend>
							<div>
								<p>'.$master_data['PIECE_MODE'].'</p>
							</div>
						</fieldset>
						<fieldset class="telnum">
							<legend>用紙上部余白</legend>
							<div>
								<p>'.$master_data['MARGIN_TOP'].'mm</p>
							</div>
						</fieldset>
						<fieldset class="telnum">
							<legend>用紙左部余白</legend>
							<div>
								<p>'.$master_data['MARGIN_LEFT'].'mm</p>
							</div>
						</fieldset>
						<fieldset class="telnum">
							<legend>ラベル内上部余白</legend>
							<div>
								<p>'.$master_data['PADDING_TOP'].'mm</p>
							</div>
						</fieldset>
						<fieldset class="telnum">
							<legend>ラベル内左部余白</legend>
							<div>
								<p>'.$master_data['PADDING_LEFT'].'mm</p>
							</div>
						</fieldset>
						<fieldset class="telnum">
							<legend>ラベル横幅</legend>
							<div>
								<p>'.$master_data['PIECE_WIDTH'].'mm</p>
							</div>
						</fieldset>
						<fieldset class="telnum">
							<legend>ラベル縦幅</legend>
							<div>
								<p>'.$master_data['PIECE_HEIGHT'].'mm</p>
							</div>
						</fieldset>
						<fieldset class="telnum">
							<legend>ラベル横間隔</legend>
							<div>
								<p>'.$master_data['PIECE_SPACE'].'mm</p>
							</div>
						</fieldset>
						<fieldset>
							<legend>面開始位置</legend>
							<div>
								<p>'.$master_data['PIECE_START'].'</p>
							</div>
						</fieldset>
						<!--fieldset>
							<legend>追加記載項目</legend>
							<div>
								<p></p>
							</div>
						</fieldset-->
						<fieldset>
							<legend>備考（管理用）</legend>
							<div>
								<p>'.$master_data['LABEL_BODY'].'</p>
							</div>
						</fieldset>
						<input type="hidden" name="id" value="'.$master_data["ID"].'">
						<div class="formAction">
							<a class="deleteBtn" href="post.php?action=trash&id='.$master_data["ID"].'"><i class="icon-trash"></i><span>削除<span></a>
							<button type="submit" name="mode" value="edit"><i class="icon-copy"></i>内容をコピー</button>
							<a href="pdf_creat.php?action=output&id='.$master_data["ID"].'" class="Btn"><i class="icon-file"></i><span>PDF出力</span></a>
						</div>
					</form>
				</div>
			';
			
			return $html;
			
		}
				
	
	}
?>