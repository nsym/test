<?php
	/* /////////////////////////////////////////////////////
	//		ホーム 共通VIEWパッケージ
	//////////////////////////////////////////////////////*/
	
	////////////////////////////////////////////////////////
	//
	//	#substance	パッケージの作成
	//  #Date		2013/12/14
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	//
	//	#substance	スケジュール仕様変更と期限切れアラート
	//  #Date		2014/01/08
	//	#Author 	yk
	//
	////////////////////////////////////////////////////////
	
//--------------------------------------------------------------------
require_once("./db/op-home/message.php");
require_once("./db/op-home/schedule.php");
require_once("./db/op-estimate/estimate.php");
require_once("./db/op-cliant/cliant.php");
require_once("./db/op-cliant/history.php");
require_once("./db/op-user/user.php");
	
	class View {
		
		public $operation;
		
		///--------------------------------------------------------------------
		/// 【コンストラクター】
		///
		/// 戻り値　なし
		///
		///	#Author yk
		/// #date	2013/12/14
		///	#Author yk
		/// #date	2014/01/08
		///--------------------------------------------------------------------
		function __construct(){
			//ホームの初期設定
			$this->state_message = array(
				'input-ng' => array('STATE_COLOR'=>'red', 'STATE_MESSAGE'=>'入力されていない項目があります。'),
				'message-ok' => array('STATE_COLOR'=>'green', 'STATE_MESSAGE'=>'メッセージを送信いたしました。'),
				'message-ng' => array('STATE_COLOR'=>'red', 'STATE_MESSAGE'=>'メッセージ送信に失敗いたしました。'),
				'schedule-ok' => array('STATE_COLOR'=>'green', 'STATE_MESSAGE'=>'スケジュールを保存いたしました。'),
				'schedule-ng' => array('STATE_COLOR'=>'red', 'STATE_MESSAGE'=>'スケジュール保存に失敗いたしました。'),
				'delete-ok' => array('STATE_COLOR'=>'green', 'STATE_MESSAGE'=>'削除処理が完了いたしました。'),
				'delete-ng' => array('STATE_COLOR'=>'red', 'STATE_MESSAGE'=>'削除処理に失敗いたしました。'),
			);
			$this->deadline_limit = 10;
			$this->deadline_count = 0;
		}
		
		///--------------------------------------------------------------------
		/// コンテンツの生成
		///
		///	#Author yk
		/// #date	2013/12/14
		///--------------------------------------------------------------------
		function setContents(){
			
			global $op_list;
			
			//ステートの設定
			$state_html = $this->stateMessage();
			
			//ダッシュボードの生成
			$contents_html ='
				<div class="row-fluid">
					'.$this->setCalenderContents('').'
					'.$this->setEditScheduleContents('').'
					<div id="message-wrap" class="span5">
						'.$this->setMessageContents('').'
					</div>
				</div>
				<div class="row-fluid">
					<div id="detail-wrap" class="span7">
						'.$this->setDeadlineEstimateContents().'
					</div>
					<div id="detail-wrap" class="span5">
						'.$this->setRequestEstimateContents().'
					</div>
				</div>
			';
				
			$html ='
				<h2>'.$op_list[$this->operation]["OP_NAME"].'</h2>
				<div class="breadcrumb">
					<ul>
						<li><a href="/admin/">'.$op_list[$this->operation]["OP_NAME"].'</a></li>
						<li>ダッシュボード</li>
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
		///	#Author yk
		/// #date	2014/01/08
		///--------------------------------------------------------------------
		function stateMessage(){
			
			
			//期限切れ見積リストの生成
			$estimateObj = new Estimate();
			$this->deadline_count = $estimateObj->countDeadlineEstimateList($this->deadline_limit, '');
			//ステートメッセージHTMLの生成
			if(!empty($this->state)){
				$html ='<p class="'.$this->state_message[$this->state]["STATE_COLOR"].'">'.$this->state_message[$this->state]["STATE_MESSAGE"].'</p>';
			}else if(!empty($this->deadline_count)){
				$html ='<p class="alert">もうすぐ期限切れの見積が '.$this->deadline_count.'件 あります。</p>';
			}
			
			return $html;
			
		}
		
		///--------------------------------------------------------------------
		/// カレンダーコンテンツの生成
		///
		///	#Author yk
		/// #date	2013/12/14
		///	#Author yk
		/// #date	2014/01/08
		///--------------------------------------------------------------------
		function setCalenderContents($date){
			
			global $authObj;
			$scheduleObj = new Schedule();
			
			//カレンダーの初期設定
			if(empty($date)) $date = date('Y-m-d');
			$month = date("m", strtotime($date));
			$year = date("Y", strtotime($date));
			$day = 1-date('w',mktime(0,0,0,$month,1,$year));	//１週目の日曜の日付を設定
			$end_of_month = date('t', strtotime($date));		//月の最終日を設定
			$cal_title = date('F Y', strtotime($date));;
			//カレンダーの生成
			$cal_head = $cal_body ='';
			for($weekday=$day; $weekday<$day+7; $weekday++){
				$cal_head.= '<th>'.date("D", mktime(0,0,0,$month, $weekday, $year)).'</th>';
			}
			while($day<=$end_of_month){
				$cal_body.='<tr>';
				for($weekday=0; $weekday<7; $weekday++, $day++){
					if(0<$day && $day<=$end_of_month){
						//スケジュールの取得
						$schedule_list = $scheduleObj->getScheduleMasterForDay($month, $day, $year, $authObj->admin_data["ID"]);
						$schedule_label ='';
						foreach($schedule_list as $value){
							$schedule_date = date("H:i〜", strtotime($value["MASTER_DATE"]));
							$schedule_label.='<a href="javascript:;" class="eventLabel" onClick="ajax_schedule_detail(\''.$value["ID"].'\')">'.$schedule_date.$value["MASTER_TITLE"].'</a>';
						}
						$cal_body.='
							<td>
								<div>
									<span class="dayNum">'.$day.'</span>
									'.$schedule_label.'
								</div>
							</td>
						   ';
					}else{
						$cal_body.='<td style="background:#f0f0f0;"></td>';
					}
				}
				$cal_body.='</tr>';
			}
			
			//日時の設定
			$year_select = $month_select = $day_select = $hour_select = '';
			if(empty($schedule_data)){
				$schedule_data["MASTER_DATE"] = date('Y-m-d H:i:s');
			}
			$to_date = date_parse($schedule_data["MASTER_DATE"]);
			for($i=date('Y'); $i<=date('Y')+1; $i++){
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
				$selected = ($i==$to_date["minute"])? 'selected': '';
				$minute_select .='<option value="'.$i.'" '.$selected.'>'.$i.'分</option>';
			}
			
			//カレンダーコントロールの設定
			$prev_month = date('Y-m-d', mktime(0,0,0,$month-1,1,$year));
			$next_month = date('Y-m-d', mktime(0,0,0,$month+1,1,$year));
			
			$html ='
				<div id="calendar-wrap" class="span7">
					<div class="contentTitle">
						<h3>カレンダー</h3>
					</div>
					<div class="contentBody">
						<div id="calTitle">
							<a class="prevBtn" onClick="ajax_ctrl_calender(\''.$prev_month.'\')">◀</a>
							<a class="nextBtn" onClick="ajax_ctrl_calender(\''.$next_month.'\')">▶</a>
							<p>'.$cal_title.'</p>
						</div>
						<table>
							<thead>
								<tr>
									'.$cal_head.'
								</tr>
							</thead>
							<tbody>
								'.$cal_body	.'
							</tbody>
						</table>
						<form action="./op-home/schedule_post.php" method="post" name="scheduleForm" id="scheduleForm">
							<fieldset>
								<legend>日時</legend>
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
								<legend>参加メンバー</legend>
								<div>
									<label><input type="radio" name="member" value="personal" checked>個人のみ</label>
									<label><input type="radio" name="member" value="staff">スタッフ全体</label>
								</div>
							</fieldset>
							<fieldset>
								<legend>タイトル</legend>
								<div>
									<input type="text" name="title" value="">
									<input type="hidden" name="staff_id" value="'.$authObj->admin_data["ID"].'">
									<button type="submit" name="new"><i class="icon-ok"></i>保存</button>
								</div>
							</fieldset>
						</form>
					</div>
				</div>
			';
			
			return $html;
			
		}
		
		///--------------------------------------------------------------------
		/// スケジュール内容の生成
		///
		///	#Author yk
		/// #date	2013/12/14
		///	#Author yk
		/// #date	2014/01/08
		///--------------------------------------------------------------------
		function setEditScheduleContents($schedule_data){
			
			//スケジュールの取得
			if(!empty($schedule_data)){
				
				//参加メンバーの設定
				$member_checked = array();
				if($schedule_data["MASTER_MEMBER"]==='staff'){
					$member_checked['staff'] = 'checked';
				}else{
					$member_checked['personal'] = 'checked';
				}
			
				//日時の設定
				$year_select = $month_select = $day_select = $hour_select = '';
				$to_date = date_parse($schedule_data["MASTER_DATE"]);
				for($i=date('Y'); $i<=date('Y')+1; $i++){
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
					$selected = ($i==$to_date["minute"])? 'selected': '';
					$minute_select .='<option value="'.$i.'" '.$selected.'>'.$i.'分</option>';
				}
				
				$detail_html ='
					<form action="./op-home/schedule_post.php" method="post">
						<fieldset>
							<legend>日時</legend>
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
								<legend>参加メンバー</legend>
								<div>
									<label><input type="radio" name="member" value="personal" '.$member_checked['personal'].'>個人のみ</label>
									<label><input type="radio" name="member" value="staff" '.$member_checked['staff'].'>スタッフ全体</label>
								</div>
							</fieldset>
						<fieldset>
							<legend>タイトル</legend>
							<div>
								<input type="text" name="title" value="'.$schedule_data["MASTER_TITLE"].'">
							</div>
						</fieldset>
						<input type="hidden" name="id" value="'.$schedule_data["ID"].'">
						<div class="formAction">
							<button type="submit" name="edit"><i class="icon-pencil"></i>編集する</button>
							<a class="deleteBtn" href="./op-home/schedule_post.php?action=delete&id='.$schedule_data["ID"].'"><i class="icon-trash"></i>削除する</a>
						</div>
					</form>
				';
			}
			
			$html ='
				<div id="schedule-wrap" class="span5">
					<div class="contentTitle">
						<h3>スケジュール内容</h3>
					</div>
					'.$detail_html.'
				</div>
			';
			
			return $html;
			
		}
		
		///--------------------------------------------------------------------
		/// スタッフ間メッセージの生成
		///
		///	#Author yk
		/// #date	2013/12/14
		///--------------------------------------------------------------------
		function setMessageContents(){
			
			global $authObj;
			
			//メッセージの取得
			$messageObj = new Message();
			$message_list = $messageObj->getMessagePostForLimit('7');
			$staffObj = new User();
			foreach($message_list as $value){
				$class ='serve';
				$staff_name ='';
				if($authObj->admin_data["ID"] != $value["STAFF_ID"]){
					$class ='reserve';
					$staff_data = $staffObj->getUserMasterForID($value["STAFF_ID"]);
					$staff_name = '<span class="staff">'.$staff_data["DISPLAY_NAME"].'</span><br>';
				}
				$message_html.='
					<li class="'.$class.'">
						<div>
							<i class="icon-user avatar"></i>
						</div>
						<p class="message">
							<span class="arrow"></span>
							'.$staff_name.'
							'.$value["POST_COMMENT"].'
						</p>
						<span class="datetime">'.$value["POST_DATE"].'</span>
					</li>
				';
			}
			
			$html ='
				<div class="contentTitle">
					<h3>メッセージ</h3>
				</div>
				<ul id="messageList">
					'.$message_html.'
				</ul>
				<form action="./op-home/message_post.php" method="post" name="messageForm" id="messageForm">
					<input type="text" name="message" value="">
					<input type="hidden" name="staff_id" value="'.$authObj->admin_data["ID"].'">
					<button type="submit"><i class="icon-edit"></i>送信</button>
				</form>
			';
			
			return $html;
			
		}
		
		///--------------------------------------------------------------------
		/// もうすぐ期限切れ見積一覧の生成
		///
		///	#Author yk
		/// #date	2013/12/14
		///--------------------------------------------------------------------
		function setDeadlineEstimateContents(){
					
			//期限切れ見積リストの生成
			$estimateObj = new Estimate();
			$estimate_list = $estimateObj->getDeadlineEstimateList($this->deadline_limit, '');
			$cliantObj = new Cliant();
			$staffObj = new User();
			$list_html ='';
			foreach($estimate_list as $value){
				//顧客名の設定
				if(!empty($value["CLIANT_ID"])){
					$cliant_data = $cliantObj->getCliantMasterForID($value["CLIANT_ID"]);
					$cliant_name = $cliant_data["MASTER_NAME"].'（'.$cliant_data["MASTER_COMPANY"].'）';
				}
				//見積スタッフの設定
				$staff_name ='スタッフ未設定';
				if(!empty($value["STAFF_ID"])){
					$staff_data = $staffObj->getStaffForID($value["STAFF_ID"]);
					$staff_name = $staff_data["DISPLAY_NAME"];
				}
				$list_html.='
					<tr>
						<td>
							'.date('Y年m月d日', strtotime($value["MASTER_LIMIT_DATE"])).'
						</td>
						<td>
							'.$cliant_name.'<br>
							'.$value["MASTER_TITLE"].'<br>
						</td>
						<td>
							'.$staff_name.'
						</td>
						<td>
							<a href="/admin/op-estimate/?mode=edit&id='.$value["ID"].'" class="detailBtn"><i class="icon-pencil"></i>内容確認</a>
						</td>
					</tr>
				';
			}
			if(empty($estimate_list)){
				$list_html ='<tr><td colspan="3"><p class="no_deadline">もうすぐ期限切れの見積はありません</p></td></tr>';
			}
			
			$html ='
				<div class="contentTitle">
					<h3>もうすぐ期限切れの見積一覧</h3>
				</div>
				<table class="deadlineList">
					<thead>
						<tr>
							<td>期限日</td>
							<td>提出先／件名</td>
							<td>提出スタッフ</td>
							<td>操作</td>
						</tr>
					</thead>
					<tbody>
						'.$list_html.'
					</tbody>
				</table>
			';
			
			return $html;
			
		}
		
		///--------------------------------------------------------------------
		/// 見積依頼一覧の生成
		///
		///	#Author yk
		/// #date	2013/12/14
		///--------------------------------------------------------------------
		function setRequestEstimateContents(){
					
			//見積り依頼リストの生成
			$historyObj = new History();
			$history_list = $historyObj->getCliantHistoryListForRequest('');
			$cliantObj = new Cliant();
			$list_html ='';
			foreach($history_list as $value){
				//顧客名の設定
				$staff_name ='スタッフ未設定';
				if(!empty($value["MASTER_ID"])){
					$cliant_data = $cliantObj->getCliantMasterForID($value["MASTER_ID"]);
					$cliant_name = $cliant_data["MASTER_NAME"].'（'.$cliant_data["MASTER_COMPANY"].'）';
				}
				$list_html.='
					<tr>
						<td>
							'.date('m月d日', strtotime($value["HISTORY_DATE"])).'
						</td>
						<td>
							'.$cliant_name.'<br>
							'.$value["HISTORY_TITLE"].'
						</td>
						<td>
							<a href="/admin/op-estimate/?mode=new&cliant_id='.$value["MASTER_ID"].'" class="detailBtn"><i class="icon-jpy"></i>見積登録</a>
						</td>
					</tr>
				';
			}
			if(empty($history_list)){
				$list_html ='<tr><td colspan="3"><p class="no_request">見積依頼はありません</p></td></tr>';
			}
			
			$html ='
				<div class="contentTitle">
					<h3>見積依頼一覧</h3>
				</div>
				<table class="deadlineList">
					<thead>
						<tr>
							<td>依頼日</td>
							<td>顧客名／内容</td>
							<td>操作</td>
						</tr>
					</thead>
					<tbody>
						'.$list_html.'
					</tbody>
				</table>
			';
			
			return $html;
			
		}
		
	}
?>