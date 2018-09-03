<?php
/*
 * Author: Ehsan Sabet
 * Author url: https://gap.im/sabet/
 */
require_once( 'Api.php' );

$token = 'your token';
try {
	$gap = new Api( $token );
} catch ( Exception $e ) {
	throw new \Exception( 'an error was encountered' );
}

$data    = isset( $_POST['data'] ) ? $_POST['data'] : null;
$type    = isset( $_POST['type'] ) ? $_POST['type'] : null;
$chat_id = isset( $_POST['chat_id'] ) ? $_POST['chat_id'] : null;
$from    = isset( $_POST['from'] ) ? $_POST['from'] : null;

/**** Welcome ***/
if ( isset( $type ) && $type == 'join' ) {
	$message = "به ربات آزمایشی خوش آمدید.";
	$gap->sendText( $chat_id, $message );
}
/**** Main Keyboard ***/
$buttons  = [
	[
		[ 'درباره ما' => 'درباره ما' ],
		[ 'تماس با ما' => 'تماس با ما' ]
	],
	[
		['دکمه‌های شیشه‌ای' => 'دکمه‌های شیشه‌ای']
	],
	[
		[ '$location' => 'موقعیت جغرافیایی' ],
		[ '$contact' => 'ارسال شماره تلفن' ]
	]
];
$mainKeyboard = $gap->replyKeyboard( $buttons );
/**** Commands ***/
if ( isset( $type ) && $type == 'text' ) {


	switch ( $data ) {
		case 'about':
		case '/about':
		case 'درباره ما':
			$message = "در اینجا شما می‌توانید متن درباره ما را تایپ کنید تا کاربر پس از کلیک روی دکمه این متن را دریافت کند.";
			return $gap->sendText( $chat_id, $message, $mainKeyboard );
			break;
		case 'contact':
		case '/contact':
		case 'تماس با ما':
			$message = "اینجا هم میتوانید اطلاعات تماس خود را درج کنید.";
			return $gap->sendText( $chat_id, $message, $mainKeyboard );
			break;
		case 'دکمه‌های شیشه‌ای':
			$inlineKeyboard = [
				[
					['text' => 'بله', 'cb_data' => 'yes'],
					['text' => 'خیر', 'cb_data' => 'no'],
				],
				[
					['text' => 'کانال احسان ثابت', 'url' => 'https://gap.im/sabet/'],
				]
			];
			$message = "لطفا روی یکی از دکمه های ذیل کلیک کنید.";
			return $gap->sendText( $chat_id, $message,$mainKeyboard, $inlineKeyboard);
			break;
		default:
			$message = "دستور وارد شده صحیح نیست، لطفا یکی از دکمه‌های ذیل را کلیک کنید.";
			return $gap->sendText( $chat_id, $message, $mainKeyboard );
			break;
	}
}
/**** Get Contact ***/
if ( isset( $type ) && $type == 'contact' ) {
	$contactData = json_decode( $data,true);
	$message = "اطلاعات دریافتی  شماره تلفن شما: " . $contactData['name'] . " و " . $contactData['phone'];
	return $gap->sendText( $chat_id, $message, $mainKeyboard );
}

/**** Get Location ***/
if ( isset( $type ) && $type == 'location' ) {
	$locationData = json_decode( $data,true);
	$message = "اطلاعات دریافتی موقعیت شما: " . $locationData['lat'] . " و " . $locationData['long'];
	return $gap->sendText( $chat_id, $message, $mainKeyboard );
}
/**** Get Inline buttons ***/
if ( isset( $type ) && $type == 'triggerButton' ) {
	$triggerData = json_decode( $data,true);
	if($triggerData['data'] == 'yes'){
		$message = "شما روی دکمه بله کلیک کردید.";
		return $gap->sendText( $chat_id, $message, $mainKeyboard );
	}
	if($triggerData['data'] == 'no'){
		$message = "شما روی دکمه خیر کلیک کردید.";
		return $gap->answerCallback( $chat_id, $triggerData['callback_id'], $message, true );
	}
}