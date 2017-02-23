# Google Firebase Cloud Messaging Push Notification PHP Class

Google Firebase Cloud Messaging (FCM) Push Notifications php class to send push notifications to your mobile devices.

## Collecting your Google Firebase API-key
1. Create a Firebase project in the [Firebase console](https://console.firebase.google.com/), if you don't already have one. [Android setup](https://firebase.google.com/docs/android/setup) [iOS setup](https://firebase.google.com/docs/ios/setup) [Web setup](https://firebase.google.com/docs/web/setup).
2. Go to your "project settings" and go to the "Cloud Messaging" tab.
3. Copy your api key "Serverkey".

## Send to device example
```php
	require require 'FCMPushNotification.php'; 
	
	$FCMPushNotification = new \BD\FCMPushNotification('YOUR_GOOGLE_FIREBASE_APIKEY');

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

## other available methods
* sendToCondition($sCondition, $aPayload, $aOptions);
* sendToDevices($aDeviceTokens, $aPayload, $aOptions);
* sendToDeviceGroup($sGroupname, $aPayload, $aOptions);
* sendToTopic($sTopic, $aPayload, $aOptions)

### Payload
| Parameter | Usage | Description |
--- 				| --- 								| ---
| data | Optional, object | This parameter specifies the custom key-value pairs of the message's payload. For example, with `data:{"score":"3x1"}:` On iOS, if the message is sent via APNS, it represents the custom data fields. If it is sent via FCM connection server, it would be represented as key value dictionary in `AppDelegate application:didReceiveRemoteNotification:`. On Android, this would result in an intent extra named `score` with the string value `3x1`. The key should not be a reserved word ("from" or any word starting with "google" or "gcm"). Do not use any of the words defined in this table (such as `collapse_key`). Values in string types are recommended. You have to convert values in objects or other non-string data types (e.g., integers or booleans) to string. |
| notification | Optional, object | This parameter specifies the predefined, user-visible key-value pairs of the notification payload. See Notification payload support for detail. For more information about notification message and data message options, see [Message types](https://firebase.google.com/docs/cloud-messaging/concept-options#notifications_and_data_messages). |

### Options
| Parameter | Usage | Description |
--- 				| --- 								| ---
| collapse_key | Optional, string | This parameter identifies a group of messages (e.g., with `collapse_key: "Updates Available"`) that can be collapsed, so that only the last message gets sent when delivery can be resumed. This is intended to avoid sending too many of the same messages when the device comes back online or becomes active. Note that there is no guarantee of the order in which messages get sent. Note: A maximum of 4 different collapse keys is allowed at any given time. This means a FCM connection server can simultaneously store 4 different send-to-sync messages per client app. If you exceed this number, there is no guarantee which 4 collapse keys the FCM connection server will keep. |
| priority | Optional, string | Sets the priority of the message. Valid values are "normal" and "high." On iOS, these correspond to APNs priorities 5 and 10. By default, notification messages are sent with high priority, and data messages are sent with normal priority. Normal priority optimizes the client app's battery consumption and should be used unless immediate delivery is required. For messages with normal priority, the app may receive the message with unspecified delay. When a message is sent with high priority, it is sent immediately, and the app can wake a sleeping device and open a network connection to your server. For more information, see [Setting the priority of a  message](https://firebase.google.com/docs/cloud-messaging/concept-options#setting-the-priority-of-a-message). |
| content_available | Optional, boolean | On iOS, use this field to represent `content-available` in the APNs payload. When a notification or message is sent and this is set to `true`, an inactive client app is awoken. On Android, data messages wake the app by default. On Chrome, currently not supported. |
| mutable_content | Optional, JSON boolean | Currently for iOS 10+ devices only. On iOS, use this field to represent `mutable-content` in the APNS payload. When a notification is sent and this is set to `true`, the content of the notification can be modified before it is displayed, using a [Notification Service app extension](https://developer.apple.com/reference/usernotifications/unnotificationserviceextension). This parameter will be ignored for Android and web. |
| time_to_live | Optional, number | This parameter specifies how long (in seconds) the message should be kept in FCM storage if the device is offline. The maximum time to live supported is 4 weeks, and the default value is 4 weeks. For more information, see [Setting the lifespan of a message](https://firebase.google.com/docs/cloud-messaging/concept-options#ttl). |
| restricted_package_name| Optional, string | This parameter specifies the package name of the application where the registration tokens must match in order to receive the message. |
| dry_run | Optional, boolean | This parameter, when set to `true`, allows developers to test a request without actually sending a message.The default value is `false`. |

[More info about payload and options settings](https://firebase.google.com/docs/cloud-messaging/http-server-ref)

## BUY ME A BEER
[![PayPayl donate button](https://img.shields.io/badge/paypal-donate-yellow.svg)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=XX68BNMVCD7YS "Donate once-off to this project using Paypal")