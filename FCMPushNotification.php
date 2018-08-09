<?php
	namespace BD;

	class FCMPushNotification  {

		private $URL = 'https://fcm.googleapis.com/fcm/send'; // URL To Firebase Cloud Messaging API
		private $APIKey; // Firebase project Serverkey
		private static $Options = array( // all optional
			/*
				collapse_key: String
				This parameter identifies a group of messages (e.g., with collapse_key: "Updates Available") that can be collapsed, so that only the last message gets sent when delivery can be resumed. This is intended to avoid sending too many of the same messages when the device comes back online or becomes active.

				Note that there is no guarantee of the order in which messages get sent.

				Note: A maximum of 4 different collapse keys is allowed at any given time. This means a FCM connection server can simultaneously store 4 different send-to-sync messages per client app. If you exceed this number, there is no guarantee which 4 collapse keys the FCM connection server will keep.
			 */
			'collapse_key',
			/*
				priority: String
				Sets the priority of the message. Valid values are "normal" and "high." On iOS, these correspond to APNs priorities 5 and 10.

				By default, notification messages are sent with high priority, and data messages are sent with normal priority. Normal priority optimizes the client app's battery consumption and should be used unless immediate delivery is required. For messages with normal priority, the app may receive the message with unspecified delay.

				When a message is sent with high priority, it is sent immediately, and the app can wake a sleeping device and open a network connection to your server.

				more info: https://firebase.google.com/docs/cloud-messaging/concept-options#setting-the-priority-of-a-message
			 */
			'priority',
			/*
				content_available: Boolean
				On iOS, use this field to represent content-available in the APNs payload. When a notification or message is sent and this is set to true, an inactive client app is awoken. On Android, data messages wake the app by default. On Chrome, currently not supported.
			 */
			'content_available' ,
			/*
				mutable_content: Boolean
				Currently for iOS 10+ devices only. On iOS, use this field to represent mutable-content in the APNS payload. When a notification is sent and this is set to true, the content of the notification can be modified before it is displayed, using a Notification Service app extension. This parameter will be ignored for Android and web.
			 */
			'mutable_content',
			/*
				time_to_live: number
				This parameter specifies how long (in seconds) the message should be kept in FCM storage if the device is offline. The maximum time to live supported is 4 weeks, and the default value is 4 weeks. For more information, see https://firebase.google.com/docs/cloud-messaging/concept-options#ttl
			 */
			'time_to_live',
			/*
				restricted_package_name: string
				This parameter specifies the package name of the application where the registration tokens must match in order to receive the message.
			 */
			'restricted_package_name',
			/*
				dry_run: boolean
				This parameter, when set to true, allows developers to test a request without actually sending a message.
				The default value is false.
			 */
			'dry_run'
		);

		public function __construct($sAPIKey) {
			if (! $sAPIKey) {
					throw new FCMPushNotificationException("API Key not set in constructor");
			}
			$this->APIKey = $sAPIKey;
		}

		/**
		 * Parses the payload
		 * @param  array $aPayload can only have the keys 'data' and 'notification'
		 * more info: https://firebase.google.com/docs/cloud-messaging/http-server-ref#downstream-http-messages-json
		 * @return array with only data an notification
		 */
		private static function _parsePayload($aPayload) 
		{
			if (
				(! is_array($aPayload) || 
				 
				 ($aPayload) === 0)
				|| (! (array_key_exists("data", $aPayload) || array_key_exists("notification", $aPayload)))
			) {
				throw new FCMPushNotificationException("Invalid Payload");
			}
			$aReturn = array();
			// Payload 'data'
			if (array_key_exists("data", $aPayload) && is_array($aPayload['data']) && count($aPayload['data'])) {
				$aReturn['data'] = array();
				//	The key should not be a reserved word ("from" or any word starting with "google" or "gcm"). 
				foreach($aPayload['data'] as $key => $value) {
					if ($key == 'from' || stripos('google', $key) || stripos('gcm', $key)) {
						throw new FCMPushNotificationException('Invalid Payload: "data" key "'.$key.'" is a reserved keyword');
					} else if (in_array($key, self::$Options)) {
						throw new FCMPushNotificationException('Invalid Payload: "data" key "'.$key.'" is a reserved keyword for Options');
					}
					$aReturn['data'][$key] = $value;
				}
			}
			// Payload 'notification'
			if (array_key_exists("notification", $aPayload) && is_array($aPayload['notification']) && count($aPayload['notification'])) {
				$aReturn['notification'] = $aPayload['notification'];
			}
			return $aReturn;
		}

		/**
		 * Parses the options
		 * @param  array $aOptions
		 * @return array with only keys that can be used (self::$Options)
		 * more info: https://firebase.google.com/docs/cloud-messaging/http-server-ref#downstream-http-messages-json
		 */
		private static function _parseOptions($aOptions) 
		{
			if (! is_array($aOptions) || ! count($aOptions)) {
				return array();
			}
			$arrReturn = array();
			foreach ($aOptions as $key => $value) {
				if (in_array($key, self::$Options)) {
					$arrReturn[$key] = $value;
				} else {
					throw new FCMPushNotificationException('Invalid Options: unkown key "'.$key.'"');
				}
			}
			return $arrReturn;
		}

		/**
		 * private send method
		 * @param  array 	$aData send data
		 * @return array 	server response data
		 */
		private function _send($aData)
		{
			// Prepare headers
			$aHeaders = array(
				'Authorization: key='.$this->APIKey,
				'Content-Type: application/json'
			);
			// Curl request
			$ch				= curl_init();
			curl_setopt($ch, CURLOPT_URL, $this->URL);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeaders);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($aData));
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$result 		= curl_exec($ch);
			$httpcode		= curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			// Process result
			if ($httpcode == "401") { // unauthorized
				throw new FCMPushNotificationException("	There was an error authenticating the sender account.", 401);
			} else if ($httpcode == "200") { // OK
				return json_decode($result, true);	
			} else { // Unkown
				throw new FCMPushNotificationException($result, $httpcode);				
			}
		}

		/**
		 * Condition based push notification
		 * @param  string $sCondition topic condition
		 * @param  [type] $aPayload   [description]
		 * @param  [type] $aOptions   [description]
		 * @return [type]             [description]
		 */
		public function sendToCondition($sCondition, $aPayload, $aOptions = null) 
		{
			if (! is_string($sCondition)) {
				throw new FCMPushNotificationException("Invalid Condition");
			}
			$aData					= self::_parsePayload($aPayload);
			$aData['condition'] 	= $sCondition;
			$aOptions				= self::_parseOptions($aOptions);
			return $this->_send(array_merge($aData, $aOptions));
		}

		/**
		 * Send push notification to single device
		 * @param  string	$sRegistrationToken The registation token comes from the client FCM SDKs
		 * @param  array	$aPayload           see https://firebase.google.com/docs/cloud-messaging/http-server-ref#downstream-http-messages-json
		 * @param  array	$aOptions           see https://firebase.google.com/docs/cloud-messaging/http-server-ref#downstream-http-messages-json
		 * @return array 	server response data
		 */
		public function sendToDevice($sRegistrationToken, $aPayload, $aOptions = null)
		{
			if (! is_string($sRegistrationToken)) {
				throw new FCMPushNotificationException("Invalid RegistrationToken");
			}
			$aData			= self::_parsePayload($aPayload);
			$aData['to'] 	= $sRegistrationToken;
			$aOptions		= self::_parseOptions($aOptions);
			return $this->_send(array_merge($aData, $aOptions));
		}

		/**
		 * Send push notification to multiple devices
		 * @param  array 	$aRegistrationTokens array with devices RegistrationTokens 
		 * @param  array	$aPayload           see https://firebase.google.com/docs/cloud-messaging/http-server-ref#downstream-http-messages-json
		 * @param  array	$aOptions           see https://firebase.google.com/docs/cloud-messaging/http-server-ref#downstream-http-messages-json
		 * @return array 	server response data
		 */
		public function sendToDevices($aRegistrationTokens, $aPayload, $aOptions = null)
		{
			if (! is_array($aRegistrationTokens) || count($aRegistrationTokens) === 0) {
				throw new FCMPushNotificationException("Invalid RegistrationTokens");
			}
			$aData						= self::_parsePayload($aPayload);
			$aData['registration_ids'] 	= $aRegistrationTokens;
			$aOptions					= self::_parseOptions($aOptions);
			return $this->_send(array_merge($aData, $aOptions));
		}

		/**
		 * Send push notification to group
		 * @param  string	$sNotificationKey  group id
		 * @param  array	$aPayload           see https://firebase.google.com/docs/cloud-messaging/http-server-ref#downstream-http-messages-json
		 * @param  array	$aOptions           see https://firebase.google.com/docs/cloud-messaging/http-server-ref#downstream-http-messages-json
		 * @return array 	server response data
		 */
		public function sendToDeviceGroup($sNotificationKey, $aPayload, $aOptions = null) {
			if (! is_string($sNotificationKey)) {
				throw new FCMPushNotificationException("Invalid NotificationKey");
			}
			$aData			= self::_parsePayload($aPayload);
			$aData['to'] 	= $sNotificationKey;
			$aOptions		= self::_parseOptions($aOptions);
			return $this->_send(array_merge($aData, $aOptions));
		}

		/**
		 * send to topic
		 * @param  string	 $sTopic  			topic which devices can subscribe to
		 * @param  array	$aPayload           see https://firebase.google.com/docs/cloud-messaging/http-server-ref#downstream-http-messages-json
		 * @param  array	$aOptions           see https://firebase.google.com/docs/cloud-messaging/http-server-ref#downstream-http-messages-json
		 * @return array 	server response data
		 */
		public function sendToTopic($sTopic, $aPayload, $aOptions = null)
		{
			if (! is_string($sTopic)) {
				throw new FCMPushNotificationException("Invalid Topic");
			}
			$aData			= self::_parsePayload($aPayload);
			$aData['to'] 	= '/topics/'.$sTopic;
			$aOptions		= self::_parseOptions($aOptions);
			return $this->_send(array_merge($aData, $aOptions));
		}		
	}
	/**
	 * Exception class for FCMPushNotification
	 */
	class FCMPushNotificationException extends \Exception {};
?>
