<?php
	/* /////////////////////////////////////////////////////
	//		ADMIN TOOL 共通VIEWパッケージ
	//////////////////////////////////////////////////////*/
	
	////////////////////////////////////////////////////////
	//
	//	#substance	パッケージの作成
	//  #Date		2013/05/19
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
	
	class AdminView {
		
		public $operation;
		
		///--------------------------------------------------------------------
		/// 【コンストラクター】
		///
		/// 戻り値　なし
		///
		///	#Author yk
		/// #date	2013/05/19
		///--------------------------------------------------------------------
		function __construct(){
		}
		
		///--------------------------------------------------------------------
		///  ヘッダーナビの生成
		///
		///	#Author yk
		/// #date	2013/05/19
		///--------------------------------------------------------------------
		function setHeader(){
			
			//configの設定を取得
			global $op_list, $authority_list;
			//ユーザー情報を取得
			global $authObj;
			
			$li_html ='';
			foreach ($op_list as $key=>$value){
				//ユーザー権限のチェック
				if(in_array($authObj->admin_data["ADMIN_AUTHORITY"], explode(',', $value['OP_AUTHORITY']))){
					$active = ($this->operation==$key)? 'active': '';
					$li_html.='<li class="'.$active.'"><a href="/admin'.$value["OP_FOLDER"].'"><i class="icon-'.$value["OP_ICON"].'"></i>'.$value["OP_NAME"].'</a></li>';
				}
			}
			
			$user_html = '
				<li>
					<a>
						<i class="icon-user"></i>
						<span>'.$authObj->admin_data["DISPLAY_NAME"].'（'.$authority_list[$authObj->admin_data["ADMIN_AUTHORITY"]]["AUTHORITY_NAME"].'）さん</span>
						<b class="icon-caret-down"></b>
					</a>
					<ul>
						<li>
							<a href="/admin/op-user/?mode=edit&id='.$authObj->admin_data["ID"].'"><i class="icon-cog"></i>アカウント設定</a>
						</li>
						<li>
							<a href="/admin/login/?flag=logout"><i class="icon-unlock-alt"></i>ログアウト</a>
						</li>
					</ul>
				</li>
			';
			
			$html ='
				<div id="header">
					<div class="header-inner">
						<div class="container">
							<a class="brand">
								<img src="/admin/images/header_logo_clear.png">
							</a>
							<form method="get" action="/admin/op-cliant/?mode=keyword" class="search" name="keywordSearch">
								<div>
									<input type="hidden" name="mode" value="keyword">
									<input type="text" name="keyword">
									<i class="icon-search"></i>
								</div>
							</form>
							<ul class="menu">
								'.$user_html.'
							</ul>
						</div>
					</div>
				</div>
				<div id="nav">
					<div class="nav-inner">
						<ul>
							'.$li_html.'
						</ul>
					</div>
				</div>
			';
			
			echo $html;
		}
		
		///--------------------------------------------------------------------
		///  ヘッダーナビの生成
		///
		///	#Author yk
		/// #date	2013/05/19
		///--------------------------------------------------------------------
		function setFooter(){
			
			$html ='
				<div id="footer">
					<div class="container">
						<p id="copyright">'.COPY_RIGHT.'</p>
					</div>
				</div>
			';
			
			echo $html;
		}
		
		///--------------------------------------------------------------------
		/// ページャーの生成
		///
		///	#Author yk
		/// #date	2013/05/29
		///--------------------------------------------------------------------
		function pager($count, $page, $limit_num, $url){
			
			//初期設定
			$pageMax = ceil($count/$limit_num);	
			$prevPage = $page-1;
			$nextPage = $page+1;
			//初期化
			$pager_html ='';
						
			//最初へボタンの生成
			//$pager_html.= (0<$page)? '<li class="prevPage"><a href="?page=0'.$url.'">&lt;&lt;</a></li>': '';
			//前へボタンの生成
			//$pager_html.= (0<$page)? '<li class="prevPage"><a href="?page='.$prevPage.$url.'"><i class="icon-chevron-left"></i></a></li>': '';
			$pager_html.= (0<$page)? '<li class="prevPage"><a href="javascript:ajax_pager(\''.$prevPage.'\');"><i class="icon-chevron-left"></i></a></li>': '';
			
			//ページボタンの生成
			$i = (0<$page-5)? $page-5: 0;
			while($i<=($page+5) && $i<$pageMax){
				$active = ($i==$page)? 'active': '';
				//$pager_html.= '<li class="'.$active.'"><a href="?page='.$i.$url.'">'.($i+1).'</a></li>';
				$pager_html.= '<li class="'.$active.'"><a href="javascript:ajax_pager(\''.$i.'\');">'.($i+1).'</a></li>';
				$i++;
			}
			
			//次へボタンの生成
			//$pager_html.= ($page<$pageMax-1)? '<li class="nextPage"><a href="?page='.$nextPage.$url.'"><i class="icon-chevron-right"></i></a></li>': '';
			$pager_html.= ($page<$pageMax-1)? '<li class="nextPage"><a href="javascript:ajax_pager(\''.$nextPage.'\');"><i class="icon-chevron-right"></i></a></li>': '';
			//最後へボタンの生成
			//$pager_html.= ($page<$pageMax-1)? '<li class="nextPage"><a href="?page='.($pageMax-1).$url.'">&gt;&gt;</a></li>': '';
			
				
			$html ='
				<div id="pager">
					<ul>
						'.$pager_html.'
					</ul>
				</div>
			';
			
			return $html;
		}
	}
?>