<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment :
--------------------------------------------*/
	/**
     * @brief 메일 발송
     **/

class Email {
	var $sender_name = '';
	var $sender_email = '';
	var $receiptor_name = '';
	var $receiptor_email = '';
	var $title = '';
	var $content = '';
	var $content_type = 'html';

	function Email() { }

	function setSender($name, $email) {
		$this->sender_name = $name;
		$this->sender_email = $email;
	}

	function getSender() {
		if($this->sender_name) return sprintf("%s <%s>", '=?utf-8?b?'.base64_encode($this->sender_name).'?=', $this->sender_email);
		return $this->sender_email;
	}

	function setReceiptor($name, $email) {
		$this->receiptor_name = $name;
		$this->receiptor_email = $email;
	}

	function getReceiptor() {
		if($this->receiptor_name) return sprintf("%s <%s>", '=?utf-8?b?'.base64_encode($this->receiptor_name).'?=', $this->receiptor_email);
		return $this->receiptor_email;
	}

	function setTitle($title) {
		$this->title = $title;
	}

	function getTitle() {
		return '=?utf-8?b?'.base64_encode($this->title).'?=';
	}

	function setContent($content) {
		$this->content = $content;
	}

	function getPlainContent() {
		return chunk_split(base64_encode(str_replace(array("<",">","&"), array("&lt;","&gt;","&amp;"), $this->content)));
	}

	function getHTMLContent() {
		return chunk_split(base64_encode($this->content_type=='html'?$this->content:$this->content));
	}

	function setContentType($mode = 'html') {
		$this->content_type = $mode=='html'?'html':'';
	}

	function send() {

    mb_internal_encoding('EUC-KR');

		$boundary = '----=='.uniqid(rand(),true);
		//$eol = $GLOBALS['_qmail_compatibility'] == "Y" ? "\n" : "\r\n";
		$eol = "\r\n";

		$headers = sprintf(
			"From: %s".$eol.
			"MIME-Version: 1.0".$eol.
			"Content-Type: multipart/alternative;".$eol."\tboundary=\"%s\"".$eol.$eol.
			"",
			$this->getSender(),
			$boundary
		);

		$body = sprintf(
			"--%s".$eol.
			"Content-Type: text/plain; charset=utf-8; format=flowed".$eol.
			"Content-Transfer-Encoding: base64".$eol.
			"Content-Disposition: inline".$eol.$eol.
			"%s".
			"--%s".$eol.
			"Content-Type: text/html; charset=utf-8".$eol.
			"Content-Transfer-Encoding: base64".$eol.
			"Content-Disposition: inline".$eol.$eol.
			"%s".
			"--%s--".
			"",
			$boundary,
			$this->getPlainContent(),
			$boundary,
			$this->getHTMLContent(),
			$boundary
		);
		return mb_send_mail($this->getReceiptor(), $this->getTitle(), $body, $headers);
	}

	function checkMailMX($email_address) {
		if(!$this->isVaildMailAddress($email_address)) {
			return false;
		}
		list($user, $host) = explode("@", trim($email_address));
		if(function_exists('checkdnsrr')) {
			if (checkdnsrr($host, "MX") or checkdnsrr($host, "A")) {
				return true;
			}
			else {
				return false;
			}
		}
		return true;
	}

	function isVaildMailAddress($email_address) {
		if( preg_match("/([a-z0-9\_\-\.]+)@([a-z0-9\_\-\.]+)/i", $email_address) ) {
			return true;
		}
		else {
			return false;
		}
	}
}

//mailSend($mail_to,$mail_subject,$mail_body,$mail_to_name,'xxx@aaaa.co.kr','관리자')
function mailSend($mail_to,$mail_subject,$mail_body,$mail_to_name,$mail_from='',$mail_sender='')
{
	// AWS SES 이메일로 발송
	$r = mailSendAWSSES($mail_to,$mail_subject,$mail_body,$mail_to_name,$mail_from,$mail_sender);
	return $r ? true : false;

	// // 메일 발송
	// $oMail = new Email();
	// if(!$oMail->checkMailMX($mail_to)) {
	// 	return false;
	// }
	// $oMail->setTitle($mail_subject);
	// $oMail->setContent($mail_body);
	// $oMail->setSender($mail_sender,$mail_from);
	// $oMail->setReceiptor( $mail_to_name, $mail_to);
	// $ret = $oMail->send();
	// if($ret) {
	// 	return true;
	// }
	// else {
	// 	return false;
	// }
}

/*********************************************************
//UTF-8 HTML을 EUC-KR 로 변환해서 보내기
*********************************************************/
function encode_2047($subject) {
    //return '=?euc-kr?b?'.base64_encode($subject).'?=';
    //return '=?euc-kr?b?'.$subject.'?=';
}

function customer_sendmail($email_str, $subject, $message,$from_name,$from_email)
{
    mb_internal_encoding('EUC-KR');

    //$from_name = "";
    //$from_name = encode_2047(iconv("UTF-8","EUC-KR",$from_name));
    $from_name = iconv("UTF-8","EUC-KR",$from_name);

    $subject = iconv("UTF-8","EUC-KR",$subject);
    $message = iconv("UTF-8","EUC-KR",$message);

    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-Type: text/html; charset=euc-kr' . "\r\n";
		$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    $headers .= 'From: '.$from_name.'<'.$from_email.'>'. "\r\n";
    //$headers .= 'From: '.$from_name. "\r\n";
		$headers .= "Reply-To: " . $from_email . "\r\n";

		// 메일 보내기
    $result = mb_send_mail($email_str, $subject, $message, $headers) ;
    //$result = mail($email_str, $subject, $message, $headers) ;

    return $result;
}

/**
 * Amazon Simple Email Service
 * https://docs.aws.amazon.com/ko_kr/ses/latest/DeveloperGuide/send-using-smtp-php.html
 * https://docs.aws.amazon.com/ko_kr/ses/latest/DeveloperGuide/smtp-credentials.html
 * ses-smtp-user.20210712-085725.trade
 */
require __DIR__.'/vendor/autoload.php';
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * @param email $mail_from 보내는 사람 이메일을 넣습니다. 단, AWS에서 검증된 이메일이여야 합니다. 다른 사람의 이메일로는 보낼 수 없습니다.
 */
function mailSendAWSSES($mail_to,$mail_subject,$mail_body,$mail_to_name='',$mail_from='info@kmcse.com',$mail_sender='MorrowStock')
{
	// // If necessary, modify the path in the require statement below to refer to the
	// // location of your Composer autoload.php file.
	// require 'vendor/autoload.php';

	// Replace sender@example.com with your "From" address.
	// This address must be verified with Amazon SES.
	$sender = $mail_from ? $mail_from : 'info@kmcse.com';
	$senderName = $mail_sender ? $mail_sender : 'MorrowStock';

	// Replace recipient@example.com with a "To" address. If your account
	// is still in the sandbox, this address must be verified.
	$recipient = $mail_to;

	// Replace smtp_username with your Amazon SES SMTP user name.
	$usernameSmtp = 'AKIA5OX2GWBC3AAWHEX6';

	// Replace smtp_password with your Amazon SES SMTP password.
	$passwordSmtp = 'BNJ4RUIwGPkaqwdb7zyVse4HIctHMBZIMzlBnz1GWcaL';

	// Specify a configuration set. If you do not want to use a configuration
	// set, comment or remove the next line.
	// $configurationSet = 'ConfigSet';

	// If you're using Amazon SES in a region other than US West (Oregon),
	// replace email-smtp.us-west-2.amazonaws.com with the Amazon SES SMTP
	// endpoint in the appropriate region.
	$host = 'email-smtp.ap-northeast-2.amazonaws.com';
	$port = 587;

	// The subject line of the email
	$subject = $mail_subject; //'Amazon SES test (SMTP interface accessed using PHP)';

	// The plain-text body of the email
	$bodyText = strip_tags($mail_body);// "Email Test\r\nThis email was sent through the Amazon SES SMTP interface using the PHPMailer class.";

	// The HTML-formatted body of the email
	$bodyHtml = $mail_body;

	$mail = new PHPMailer(true);

	try {
		// set utf8
		$mail->ContentType = "text/html";
		$mail->CharSet = "utf-8";
		// Specify the SMTP settings.
		$mail->isSMTP();
		$mail->setFrom($sender, $senderName);
		$mail->Username   = $usernameSmtp;
		$mail->Password   = $passwordSmtp;
		$mail->Host       = $host;
		$mail->Port       = $port;
		$mail->SMTPAuth   = true;
		$mail->SMTPSecure = 'tls';
		// $mail->addCustomHeader('X-SES-CONFIGURATION-SET', $configurationSet);

		// Specify the message recipients.
		$mail->addAddress($recipient);
		// You can also add CC, BCC, and additional To recipients here.

		// Specify the content of the message.
		$mail->isHTML(true);
		$mail->Subject    = $subject;
		$mail->Body       = $bodyHtml;
		$mail->AltBody    = $bodyText;
		$mail->Send();
		// echo "Email sent!" , PHP_EOL;
		return true;
	} catch (phpmailerException $e) {
		// echo "An error occurred. {$e->errorMessage()}", PHP_EOL; //Catch errors from PHPMailer.
		return false;
	} catch (Exception $e) {
		// echo "Email not sent. {$mail->ErrorInfo}", PHP_EOL; //Catch errors from Amazon SES.
		return false;
	}
}