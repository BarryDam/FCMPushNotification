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
[More info about payload and options settings](https://firebase.google.com/docs/cloud-messaging/http-server-ref)

## other available methods
* sendToCondition($sCondition, $aPayload, $aOptions);
* sendToDevices($aDeviceTokens, $aPayload, $aOptions);
* sendToDeviceGroup($sGroupname, $aPayload, $aOptions);
* sendToTopic($sTopic, $aPayload, $aOptions)


| Parameter | Usage | Description |
| **Targets** |
| `<span>to</span>` | Optional, string | 

This parameter specifies the recipient of a message.

The value can be a device's registration token, a device group's notification key, or a single topic (prefixed with `/topics/`). To send to multiple topics, use the `condition` parameter.

 |
| `<span>registration_ids</span>`
 | Optional, array of strings | 

This parameter specifies the recipient of a multicast message, a message sent to more than one registration token.

The value should be an array of registration tokens to which to send the multicast message. The array must contain at least 1 and at most 1000 registration tokens. To send a message to a single device, use the `to` parameter.

Multicast messages are only allowed using the HTTP JSON format.

 |
| `<span>condition</span>` | Optional, string | 

This parameter specifies a logical expression of conditions that determine the message target.

Supported condition: Topic, formatted as "'yourTopic' in topics". This value is case-insensitive.

Supported operators: `&&`, `||`. Maximum two operators per topic message supported.

 |
| `<span>notification_key</span>`
**Deprecated** | Optional, string | 

This parameter is deprecated. Instead, use `to` to specify message recipients. For more information on how to send messages to multiple devices, see the documentation for your platform.

 |
| **Options** |
| `<span>collapse_key</span>` | Optional, string | 

This parameter identifies a group of messages (e.g., with `collapse_key: "Updates Available"`) that can be collapsed, so that only the last message gets sent when delivery can be resumed. This is intended to avoid sending too many of the same messages when the device comes back online or becomes active.

Note that there is no guarantee of the order in which messages get sent.

Note: A maximum of 4 different collapse keys is allowed at any given time. This means a FCM connection server can simultaneously store 4 different send-to-sync messages per client app. If you exceed this number, there is no guarantee which 4 collapse keys the FCM connection server will keep.

 |
| `<span>priority</span>` | Optional, string | 

Sets the priority of the message. Valid values are "normal" and "high." On iOS, these correspond to APNs priorities 5 and 10.

By default, notification messages are sent with high priority, and data messages are sent with normal priority. Normal priority optimizes the client app's battery consumption and should be used unless immediate delivery is required. For messages with normal priority, the app may receive the message with unspecified delay.

When a message is sent with high priority, it is sent immediately, and the app can wake a sleeping device and open a network connection to your server.

For more information, see [Setting the priority of a message](https://firebase.google.com/docs/cloud-messaging/concept-options#setting-the-priority-of-a-message).

 |
| `<span>content_available</span>` | Optional, boolean | 

On iOS, use this field to represent `content-available` in the APNs payload. When a notification or message is sent and this is set to `true`, an inactive client app is awoken. On Android, data messages wake the app by default. On Chrome, currently not supported.

 |
| `<span>mutable_content</span>` | Optional, JSON boolean | 

Currently for iOS 10+ devices only. On iOS, use this field to represent `mutable-content` in the APNS payload. When a notification is sent and this is set to `true`, the content of the notification can be modified before it is displayed, using a [Notification Service app extension](https://developer.apple.com/reference/usernotifications/unnotificationserviceextension). This parameter will be ignored for Android and web.

 |
| `<span>delay_while_idle</span>`
**Deprecated Effective Nov 15th 2016** | Optional, boolean | 

This parameter is deprecated. After Nov 15th 2016, it will be accepted by FCM, but ignored.

When this parameter is set to `true`, it indicates that the message should not be sent until the device becomes active.

The default value is `false`.

 |
| `<span>time_to_live</span>` | Optional, number | 

This parameter specifies how long (in seconds) the message should be kept in FCM storage if the device is offline. The maximum time to live supported is 4 weeks, and the default value is 4 weeks. For more information, see [Setting the lifespan of a message](https://firebase.google.com/docs/cloud-messaging/concept-options#ttl).

 |
| `<span>restricted_package_</span>
<span>name</span>` | Optional, string | This parameter specifies the package name of the application where the registration tokens must match in order to receive the message. |
| `<span>dry_run</span>` | Optional, boolean | 

This parameter, when set to `true`, allows developers to test a request without actually sending a message.

The default value is `false`.

 |
| **Payload** |
| `<span>data</span>` | Optional, object | 

This parameter specifies the custom key-value pairs of the message's payload.

For example, with `data:{"score":"3x1"}:`

On iOS, if the message is sent via APNS, it represents the custom data fields. If it is sent via FCM connection server, it would be represented as key value dictionary in `AppDelegate application:didReceiveRemoteNotification:`.

On Android, this would result in an intent extra named `score` with the string value `3x1`.

The key should not be a reserved word ("from" or any word starting with "google" or "gcm"). Do not use any of the words defined in this table (such as `collapse_key`).

Values in string types are recommended. You have to convert values in objects or other non-string data types (e.g., integers or booleans) to string.

 |
| `<span>notification</span>` | Optional, object | This parameter specifies the predefined, user-visible key-value pairs of the notification payload. See Notification payload support for detail. For more information about notification message and data message options, see [Message types](https://firebase.google.com/docs/cloud-messaging/concept-options#notifications_and_data_messages). |

## BUY ME A BEER
[![PayPayl donate button](https://img.shields.io/badge/paypal-donate-yellow.svg)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=XX68BNMVCD7YS "Donate once-off to this project using Paypal")