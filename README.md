# Google Firebase Cloud Messaging Push Notification PHP Class

Google Firebase Cloud Messaging (FCM) Push Notifications php class to send push notifications to your mobile devices.

## Collecting your Google Firebase API-key
1. Create a Firebase project in the [Firebase console](https://console.firebase.google.com/), if you don't already have one. [Android setup](https://firebase.google.com/docs/android/setup) [iOS setup](https://firebase.google.com/docs/ios/setup) [Web setup](https://firebase.google.com/docs/web/setup).
2. Go to your "project settings" and go to the "Cloud Messaging" tab.
3. Copy your api key "Serverkey".

## Starting
```php
	require require 'FCMPushNotification.php'; 
	$FCMPushNotification = new \BD\FCMPushNotification('YOUR_GOOGLE_FIREBASE_APIKEY');
```

## Send to device example
```php
	$sDeviceToken = "DEVICE_TOKEN_ID";
	$aPayload = array(
		'data' => array("test"=>123),
		'notification' => array(
			'title' => 'Example app',
			'body'=> 'This is an example message',
			'sound'=> 'default'
		)
	);
	$aOptions = array(
		'time_to_live' => 0 //means messages that can't be delivered immediately are discarded. 
	);

	$aResult = $FCMPushNotification->sendToDevice(
		$sDeviceToken,		
		$aPayload,
		$aOptions // optional
	);
	var_dump($aResult);
```
[More info about payload and options settings](https://firebase.google.com/docs/cloud-messaging/http-server-ref)

## other available methods
* sendToCondition($sCondition, $aPayload, $aOptions);
* sendToDevices($aDeviceTokens, $aPayload, $aOptions);
* sendToDeviceGroup($sGroupname, $aPayload, $aOptions);
* sendToTopic($sTopic, $aPayload, $aOptions)

## BUY ME A BEER
[![PayPayl donate button](https://img.shields.io/badge/paypal-donate-yellow.svg)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=XX68BNMVCD7YS "Donate once-off to this project using Paypal")