<?php
namespace app\model;

class Notification{
	public static $brand = BRAND, $protocol = SSL_PROTOCOL, $site = PROOT;

	public static function AccountActivated($email){
		$message = <<< __MAIL__

__MAIL__;
		self::EmailNotification($email, 'Account Activation', $message);
	}

	public static function AccountBreach($email){
		$message = <<< __MAIL__

__MAIL__;
		self::EmailNotification($email, 'Account Security Breach', $message);	
	}

	public static function AccountCreated($email, $key){
		$protocol = self::$protocol ;
		$brand = self::$brand;
		$site = self::$site;

		$message = <<< __MAIL__
		Welcome to {$brand}, Your account has been created. 
		This is an automated massage system, please do not reply.
		To activate your account, click on this link:
		{$protocol}{$site}activate/{$email}/{$key}
__MAIL__;
		self::EmailNotification($email, 'Account Creation Success', $message);
	}

	public static function AccountDeletion($email){
		$message = <<< __MAIL__

__MAIL__;
		self::EmailNotification($email, 'Account Delete Request', $message);
	}

	public static function DeviceChanged($email){
		$message = <<< __MAIL__

__MAIL__;
		self::EmailNotification($email, 'Device Changed', $message);
	}	

	public static function ForgotPassword($email, $new){
		$message = <<< __MAIL__
		A forgot password request has been issued for the account connected to this email.
		Your password is {$new} . If this is a false alarm, kindly ignore and or delete this mail.
__MAIL__;
		self::EmailNotification($email, 'Forgot Password Request', $message);
	}		

	public static function EmailChange($email){
		$message = <<< __MAIL__

__MAIL__;
		self::EmailNotification($email, 'Email Change Request', $message);
	}

	public static function messageReceived($email){
		$message = <<< __MAIL__

__MAIL__;
		self::EmailNotification($email, 'Email Change Request', $message);
	}

	final public function PWAsend(array $tokens_array, string $note){
		$msg = [ 'title' => SITE_TITLE . " Notification",
					'body'	=> $note,
					'icon'	=> ICON //this is different from your site favicon image in size and type
		];
		if (self::PWANotification(['registration_ids' => $tokens_array, 'data' => $msg ])){
			return true;
		}
		return false;
	}

	private static function PWANotification($payload){
		$header= [ 'Authorization: key='.FIREBASE_SERVER_API_KEY, 
				'Content-Type: Application/json' 
		];
		$curl= curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL 			=> "https://fcm.googleapis.com/fcm/send",
			CURLOPT_RETURNTRANSFER 	=> true,
			CURLOPT_CUSTOMREQUEST	=> "POST",
			CURLOPT_POSTFIELDS		=> json_encode($payload),
			CURLOPT_HTTPHEADER		=> $header
		));
		$response= curl_exec($curl);
		$err= curl_error($curl);
		curl_close($curl);
		
		if(!$err){
			return $response;
		}else{
			return false;
		}
	}

	private static function EmailNotification($email, $subject='', $message){
		$subject = SITE_TITLE . ' Notification: ' . $subject;
		$headers = implode("\r\n", [
		 'From: '. SITE_TITLE .' Team',
		 'Reply-To: '.SITE_EMAIL,
		 'MIME-Version: 1.0',
		 'Content-Type: text/html; charset=UTF-8',
		 'X-Priority: 3',
		 'nX-MSmail-Priority: high'
		]);
		if(mail($email, $subject, $message)){
			return true;
		}
	}
} 