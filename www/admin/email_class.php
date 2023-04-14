<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment :
--------------------------------------------*/

class ShopEmail extends BASIC
{
	function ShopEmail(&$tpl)
	{
		$config = array();

		$config['table_name'] = 'js_config_email';
		$config['query_func'] = 'emailQuery';
		$config['write_mode'] = 'ajax';
		/************************************/
		$config['file_dir'] = '/data/bbs';
		$config['thumb_dir'] = '/data/thumbnail';
		$config['temp_dir'] = '/data/editorTemp';
		$config['editor_dir'] = '/data/editor';
		/************************************/
		$config['no_tag'] = array();
		$config['no_space'] = array();
		$config['staple_article'] = array();
		/************************************/
		$config['bool_file'] = FALSE;
		$config['file_target'] = array();
		$config['file_size'] = 2;
		$config['upload_limit'] = TRUE;

		$config['bool_thumb'] = FALSE;
		$config['thumb_target'] = array();
		$config['thumb_width'] = 0;
		$config['thumb_height'] = 0;
		$config['thumb_size'] = array();
		/************************************/
		$config['bool_editor'] = TRUE;
		$config['editor_target'] = array();
		$config['limit_img_width'] = 500;

		$config['bool_editor_thumb'] = TRUE;
		$config['editor_thumb_width'] = 150;
		$config['editor_thumb_height'] = 150;
		/************************************/
		$config['bool_navi_page'] = FALSE;
		$config['loop_scale'] = 10;
		$config['page_scale'] = 10;
		$config['navi_url'] = '';
		$config['navi_pg_mode'] = 'list';
		$config['navi_qry'] = '';
		$config['navi_mode'] = 'link';// ajax or link
		$config['navi_load_id'] = '';

		$this->BASIC($config,$tpl);
	}

	function viewForm()
	{
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'row';
		$row = $this->dbcon->query($query,__FILE__,__LINE__);
		$this->tpl->assign($row);

		//스킨설정
		$this->setSkin('join','loop_join');
		$this->setSkin('order','loop_order');
		$this->setSkin('payment','loop_payment');
		$this->setSkin('delivery','loop_delivery');
		$this->setSkin('ordercancel','loop_ordercancel');
		$this->setSkin('passwd','loop_passwd');
		$this->setSkin('withdraw','loop_withdraw');

	}

	function setSkin($type,$loop_id)
	{
		$arr_skin = array();
		$dp = opendir(ROOT_DIR.'/template/user/mail');
		while($dir_list = readdir($dp)) {
			if($dir_list != '.' && $dir_list != '..') {
				$regexp = '/^(skinmail_'.$type.'_\w+\.html)$/';
				if(preg_match($regexp,$dir_list,$matches)) {
					$arr_skin[$dir_list] = $matches[1];
				}
			}
		}
		closedir($dp);
		$this->tpl->assign($loop_id,$arr_skin);
	}

	function edit()
	{
		$query = array();
		$query['where'] = "where code='".getSiteCode()."' ";
		if($this->bEdit($query,$_POST,'_write')) {
			jsonMsg(1);
		}
		else {
			jsonMsg(0);
		}
	}

	function _write($arr)
	{
		return $arr;
	}

	function setEmailTarget()
	{
		if($_GET['kind_target'] == 'ss') {
			if(!empty($_GET['codes']))  {
				$arr = array();
				foreach ($_GET['codes'] as $key => $val) {
					$query = array();
					$query['table_name'] = 'js_student';
					$query['tool'] = 'select_one';
					$query['fields'] = 'email';
					$query['where'] = 'where idx=\''.$val.'\'';
					$email = $this->dbcon->query($query,__FILE__,__LINE__);
					if(!empty($email)) {
						$arr[] = $email;
					}
				}
				$mail_to = implode(',',$arr);
				$this->tpl->assign('mail_to',$mail_to);
			}
		}
		else {
			if(!empty($_GET['userid']))  {
				$arr = array();
				foreach ($_GET['userid'] as $key => $val) {
					$query = array();
					$query['table_name'] = 'js_member';
					$query['tool'] = 'select_one';
					$query['fields'] = 'email';
					$query['where'] = 'where userid=\''.$val.'\'';
					$email = $this->dbcon->query($query,__FILE__,__LINE__);
					if(!empty($email)) {
						$arr[] = $email;
					}
				}
				$mail_to = implode(',',$arr);
				$this->tpl->assign('mail_to',$mail_to);
			}
		}
	}

	function sendEmail()
	{
		$this->getConfigMail();
		$arr = array();
		$arr['mail_to'] = $_POST['mail_to'];
		$arr['mail_subject'] = $_POST['mail_subject'];
		$arr['mail_to_name'] = $_POST['mail_to_name'];
		$this->tpl->assign('contents',$_POST['mail_body']);
		$mail_body = $this->tpl->fetch('mail_contents');
		$arr['mail_body'] = $mail_body;
		if($this->sendAutoMail($arr)) {
			jsonMsg(1);
		}
		else {
			jsonMsg(0);
		}
	}


	//전체회원 문자 보내기
	function memberAllEmail()
	{
		$this->getConfigMail();
		$query = array();
		$query['table_name'] = 'js_member';
		$query['tool'] = 'select';
		$query['fields'] = 'email';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		$arr = array();
		while ($row = mysqli_fetch_assoc($result)) {
			if(strlen($row['email']) > 0) {
				$arr[] = $row['email'];
			}
		}

		if(empty($arr)) {
			jsonMsg(0);
		}
		$mail_to = implode(',',$arr);

		$arr = array();
		$arr['mail_to'] = $mail_to;
		$arr['mail_subject'] = $_POST['mail_subject'];
		$arr['mail_to_name'] = $_POST['mail_to_name'];
		$this->tpl->assign('contents',$_POST['mail_body']);
		$mail_body = $this->tpl->fetch('mail_contents');
		$arr['mail_body'] = $mail_body;

		if($this->sendAutoMail($arr)) {
			jsonMsg(1);
		}
		else {
			jsonMsg(0);
		}
	}

	/*
	메일 설정 값과 메일 스킨을 가지고 온다.
	*/
	function getConfigMail($mode='basic')
	{
		$fields = 'license_company,
			license_ceo,
			license_no,
			license_sale,
			license_address,
			license_uptae,
			license_jongmok,
			license_opendate,
			shop_name,
			shop_ename,
			shop_url,
			shop_admin_email,
			shop_phone,
			shop_fax,
			shop_private_clerk,
			shop_clerk_email,
			shop_address,
			img_logo,
			img_sub_logo';
		$config_basic = getConfig('js_config_basic',$fields);

		$arr_field = array();
		$arr_field[] = 'bool_auto_email';
		$arr_field[] = 'from_name';
		$arr_field[] = 'from_email_addr';
		if ($mode == 'confirm') {
			$arr_field[] = 'bool_email_confirm';
			$arr_field[] = 'skin_email_confirm';
		}
		else if ($mode == 'join') {
			$arr_field[] = 'bool_email_join';
			$arr_field[] = 'skin_email_join';
		}
		else if ($mode == 'order') {
			$arr_field[] = 'bool_email_order';
			$arr_field[] = 'skin_email_order';
		}
		else if ($mode == 'payment') {
			$arr_field[] = 'bool_email_payment';
			$arr_field[] = 'skin_email_payment';
		}
		else if ($mode == 'delivery') {
			$arr_field[] = 'bool_email_delivery';
			$arr_field[] = 'skin_email_delivery';
		}
		else if ($mode == 'ordercancel') {
			$arr_field[] = 'bool_email_ordercancel';
			$arr_field[] = 'skin_email_ordercancel';
		}
		else if ($mode == 'passwd') {
			$arr_field[] = 'bool_email_passwd';
			$arr_field[] = 'skin_email_passwd';
		}
		else if ($mode == 'withdraw') {
			$arr_field[] = 'bool_email_withdraw';
			$arr_field[] = 'skin_email_withdraw';
		}
		else if ($mode == 'krwwithdraw') {
			$arr_field[] = 'bool_email_krwwithdraw';
			$arr_field[] = 'skin_email_krwwithdraw';
		}
		else {
		}
		$fields = implode(', ',$arr_field);
		//if ($mode !='basic') {
			$query = array();
			$query['table_name'] = 'js_config_email';
			$query['tool'] = 'row';
			$query['fields'] = $fields;
			$query['where'] = ' where code="'.getSiteCode().'" ';
			$row = $this->dbcon->query($query,__FILE__,__LINE__);
			$this->config['config_mail'] = $row;
		//}
		$this->tpl->skin = 'user';
		$this->tpl->define('mail_contents','mail/basic_mail.html');
		if($mode == 'basic') {
			$this->tpl->define('mail_body','mail/mail_contents.html');
		}
		else {
			$this->tpl->define('mail_body','mail/'.$row['skin_email_'.$mode]);
		}
		// 주소 번역문구 있으면 번역문구로 변경.
		$Lang_main_CompanyAddress = Lang('main_CompanyAddress');
		if($Lang_main_CompanyAddress!='') {
			$config_basic['shop_address'] = $Lang_main_CompanyAddress;
		}
		$this->config['config_basic'] = $config_basic;
		$this->tpl->assign('config_basic',$config_basic);
	}


	/*************************************
	** mode **
	join
	order
	payment
	delivery
	ordercancel
	passwd
	withdraw
	krwwithdraw
	** arr_mail **
	mail_to
	mail_subject
	mail_body
	mail_to_name
	*************************************/
	function sendAutoMail($arr_mail,$mode='basic')
	{
		//메일 전송 옵션이 1값인지 확인한다.
		if($mode != 'basic') {
			if(!$this->config['config_mail']['bool_auto_email']) {
				return false;
			}
			if(empty($this->config['config_mail']['bool_email_'.$mode])) {
				return false;
			}
		}
		if(empty($arr_mail)) {
			return FALSE;
		}
		else {
			if(empty($arr_mail['mail_to']) || empty($arr_mail['mail_subject']) || empty($arr_mail['mail_body'])) {
				return FALSE;
			}
		}
		$mail_to_name = isset($arr_mail['mail_to_name']) ? $arr_mail['mail_to_name'] : '';
		$from_name = empty($this->config['config_mail']['from_name']) ? $this->config['config_basic']['shop_name'] : $this->config['config_mail']['from_name'];
		$from_email = empty($this->config['config_mail']['from_email_addr']) ? $this->config['config_basic']['shop_admin_email'] : $this->config['config_mail']['from_email_addr'];

		// 발신전용 구글이메일로 이메일 발송.
		$ret = $this->send_email_from_gmail($arr_mail['mail_to'], $arr_mail['mail_subject'], $arr_mail['mail_body']);

		// $ret = mailSend($arr_mail['mail_to'],$arr_mail['mail_subject'],$arr_mail['mail_body'],$mail_to_name,$from_email,$from_name);
		// print_r($arr_mail);
		// $ret = customer_sendmail($arr_mail['mail_to'],$arr_mail['mail_subject'],$arr_mail['mail_body'],$from_name,$from_email);
		return $ret;
	}

	function send_email_from_gmail($receiver_email, $subject, $body) {
		$this->send_email_error_msg = '';
		include ROOT_DIR.'/lib/phpmailer/PHPMailerAutoload.php';
		$mail = new PHPMailer(true);
		$mail->isSMTP();
		$mail->Mailer = "smtp";
		
		try {
			// $mail->SMTPDebug  = __API_RUNMODE__=='loc' ? 1 : 0;  
			// SMTP 암호화 여부
			$mail->SMTPAuth = true;
			// SMTP 포트
			$mail->Port = 587;
			// SMTP 보안 프초트콜
			$mail->SMTPSecure = "tls"; // tls 587, ssl 465
			// 구글 smtp 설정
			$mail->Host = 'smtp.gmail.com'; //"smtp.gmail.com";
			// SMTP 계정 이메일
			$sender_email = __GOOGLE_GMAIL_USERNAME__; //"nowonbun@gmail.com";
			// gmail 유저 아이디
			$mail->Username = substr($sender_email, 0, strpos($sender_email, '@'));; //"nowonbun";
			// gmail 패스워드
			$mail->Password = __GOOGLE_GMAIL_PASSWORD__; //"***password**"; naver - 애플리케이션 비밀번호 생성 필요(내정보 > 보안설정 > 2단계 인증 > 관리하기)
			// 인코딩 셋
			$mail->CharSet = 'utf-8';
			$mail->Encoding = "base64";
			
			// 보내는 사람
			$mail->setFrom($sender_email, $mail->Username); //('nowonbun@gmail.com', 'Tester');
			// 받는 사람
			$mail->AddAddress($receiver_email); //("nowonbun@gmail.com", "nowonbun");

			// 본문 html 타입 설정
			$mail->isHTML(true);
			// 제목
			//$mail->Subject = 'Contact US - '.$_SERVER['HTTP_HOST'];//'Here is the subject';
			$mail->Subject = $subject;
			// 본문 (HTML 전용)
			//$mail->Body    = $_POST['message'];//'This is the HTML message body <b>in bold!</b>';
			$mail->MsgHTML($body);
			// 본문 (non-HTML 전용)
			//$mail->AltBody = strip_tags($_POST['message']);//'This is the body in plain text for non-HTML mail clients';
			// // 첨부파일
			// if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
			//     $mail->AddAttachment($_FILES['attachment']['tmp_name'], $_FILES['attachment']['name']);
			// }
			// 이메일 발송
			$mail->Send();
			//echo "Message has been sent";
			return true;
		} catch (phpmailerException $e) {
			$this->send_email_error_msg = $e->errorMessage();
			return false;
		} catch (Exception $e) {
			$this->send_email_error_msg = $e->getMessage();
			return false;
		}

	}


}

function emailQuery($arr)
{
	$qry = array();
	if(!empty($arr['code']))  { $qry[] = 'code=\''.$arr['code'].'\''; }
	if(isset($arr['bool_auto_email']))  { $qry[] = 'bool_auto_email=\''.$arr['bool_auto_email'].'\''; }
	if(isset($arr['from_name']))  { $qry[] = 'from_name=\''.$arr['from_name'].'\''; }
	if(isset($arr['from_email_addr']))  { $qry[] = 'from_email_addr=\''.$arr['from_email_addr'].'\''; }
	if(isset($arr['bool_email_confirm']))  { $qry[] = 'bool_email_confirm=\''.$arr['bool_email_confirm'].'\''; }
	if(!empty($arr['skin_email_confirm']))  { $qry[] = 'skin_email_confirm=\''.$arr['skin_email_confirm'].'\''; }
	if(isset($arr['bool_email_join']))  { $qry[] = 'bool_email_join=\''.$arr['bool_email_join'].'\''; }
	if(!empty($arr['skin_email_join']))  { $qry[] = 'skin_email_join=\''.$arr['skin_email_join'].'\''; }
	if(isset($arr['bool_email_order']))  { $qry[] = 'bool_email_order=\''.$arr['bool_email_order'].'\''; }
	if(!empty($arr['skin_email_order']))  { $qry[] = 'skin_email_order=\''.$arr['skin_email_order'].'\''; }
	if(isset($arr['bool_email_payment']))  { $qry[] = 'bool_email_payment=\''.$arr['bool_email_payment'].'\''; }
	if(!empty($arr['skin_email_payment']))  { $qry[] = 'skin_email_payment=\''.$arr['skin_email_payment'].'\''; }
	if(isset($arr['bool_email_delivery']))  { $qry[] = 'bool_email_delivery=\''.$arr['bool_email_delivery'].'\''; }
	if(!empty($arr['skin_email_delivery']))  { $qry[] = 'skin_email_delivery=\''.$arr['skin_email_delivery'].'\''; }
	if(isset($arr['bool_email_ordercancel']))  { $qry[] = 'bool_email_ordercancel=\''.$arr['bool_email_ordercancel'].'\''; }
	if(!empty($arr['skin_email_ordercancel']))  { $qry[] = 'skin_email_ordercancel=\''.$arr['skin_email_ordercancel'].'\''; }
	if(isset($arr['bool_email_passwd']))  { $qry[] = 'bool_email_passwd=\''.$arr['bool_email_passwd'].'\''; }
	if(!empty($arr['skin_email_passwd']))  { $qry[] = 'skin_email_passwd=\''.$arr['skin_email_passwd'].'\''; }
	if(isset($arr['bool_email_withdraw']))  { $qry[] = 'bool_email_withdraw=\''.$arr['bool_email_withdraw'].'\''; }
	if(!empty($arr['skin_email_withdraw']))  { $qry[] = 'skin_email_withdraw=\''.$arr['skin_email_withdraw'].'\''; }
	return implode(',',$qry);
}

?>