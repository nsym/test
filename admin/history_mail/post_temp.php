#!/usr/bin/php
<?php
	$adduserHeader = "X-Mailer:PajakanoMailSystem\n";
	$adduserHeader .= "From:shame@pajakano.com";
	$subject = '【パジャマな彼女】写メ投稿';
	$body = 'パイプ確認しました';
	mb_send_mail('yokoe@side7.co.jp', $subject, $body, $adduserHeader);
?>