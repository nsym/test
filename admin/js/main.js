// JavaScript Document
	
//共通JavaScript

$(function(){
	
	//リストへ戻るボタン
	$('button.backBtn').click(function(){
		history.back();
		return false;
	});
	
	//削除ボタンの確認アラート
	$('a.deleteBtn').live('click', function(){
		var txt = '本当に削除してもよろしいですか？';
		if(confirm(txt)){
			return true;
		}
		else{
			return false;
		}
	});
	//削除ボタンの確認アラート
	$('a.trashBtn').live('click', function(){
		var txt = '本当に削除してもよろしいですか？';
		if(confirm(txt)){
			return true;
		}
		else{
			return false;
		}
	});
	
	//ヘッダードロップダウン機能
	$('#header .menu >li >a').click(function(){
		$(this).toggleClass('active');
		$(this).next('ul').toggle();
		return false;
	});
	
	//サイドバースライド機能
	$('#sidebar .sidebarToggle').click(function(){
		$('#contents').toggleClass('slide');
		$('#sidebar').toggleClass('slide');
		return false;
	});

	//キーワード検索機能
	$('form[name=keywordSearch]').keypress(function(e){
		var keyword = document.keywordSearch.keyword.value;
		if(e.which==13 && keyword!='undefined'){
			document.keywordSearch.submit();
			return false;
		}
	});
	$('form[name=keywordSearch] i').click(function(e){
		var keyword = document.keywordSearch.keyword.value;
		if(keyword!='' && keyword!='undefined'){
			document.keywordSearch.submit();
			return false;
		}
	});
	
	
});
		
//wrapの閉じる機能
function close_wrap(target){
	$(target).remove();
	$('#list-wrap').removeClass('span8');
	$('#list-wrap').addClass('span12');
}

//wrapのスライドトグル機能
function slide_wrap(target){
	$(target).children('form').slideToggle();
}


function gmapLatLng(adrs, lat, lng){
	
	if(lat=="" || lng=="" ){
		lat = 35.009583799429251;
		lng = 135.75934269205706;
	}
		
	//GoogleMapの表示設定
	var map = new google.maps.Map(
		document.getElementById("gmap"),{
			zoom : 14,
			center : new google.maps.LatLng(lat, lng),
			mapTypeId : google.maps.MapTypeId.ROADMAP
		}
	);
	
	if(adrs!=''){
		//住所から緯度経度を取得
		var gc = new google.maps.Geocoder();
		gc.geocode({ address : adrs }, function(results, status){
			if (status == google.maps.GeocoderStatus.OK) {
				var ll = results[0].geometry.location;
				var glat = ll.lat();
				var glng = ll.lng();
				//alert(adrs+"の緯度、経度："+glat+"、"+glng);
				map.setCenter(ll);
				marker.setPosition(new google.maps.LatLng(glat, glng));
				$('#lat').attr('value', glat);
				$('#lng').attr('value', glng);
			}else{
				alert(status+" : ジオコードに失敗しました");
			}
		});
	}
	
	var marker = new google.maps.Marker({
		position: new google.maps.LatLng(lat, lng),
		map: map,
		icon : "/admin/images/cross.png"
	});
	
	//ドラッグされた場合
	google.maps.event.addListener(map, "drag", function() {
		var pos = map.getCenter();
		var lat = pos.lat();
		var lng = pos.lng();
		marker.setPosition(new google.maps.LatLng(lat, lng));
		$('#lat').attr('value', lat);
		$('#lng').attr('value', lng);
	});
	
}



