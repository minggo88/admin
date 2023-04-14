<?php
/**
 * Coin 관련 Model 객체
 * @author Kenny Han
 */
class Coins extends BASIC
{

	public $dbcon = null;
	public $tpl = null;

	function __construct($tpl, $dbcon=null)
	{
		$this->dbcon = $dbcon;
		$this->tpl = $tpl;

		$config = array();
		$config['table_name'] = 'js_exchange_wallet_txn';
		$config['query_func'] = 'tradeWalletTxnQuery';
		$config['write_mode'] = 'ajax';
		/************************************/
		$config['file_dir'] = '/data/design';
		$config['thumb_dir'] = '/data/thumbnail';
		$config['temp_dir'] = '/data/editorTemp';
		$config['editor_dir'] = '/data/editor';
		/************************************/
		$config['no_tag'] = array('txnid','userno','symbol','address','regdate','txndate','address_relative','txn_type','amount','fee','tax','status','key_relative');
		$config['no_space'] = array('txnid','userno','symbol','txn_type','amount','fee','tax','status');
		$config['staple_article'] = array('txnid'=>'blank');
		/************************************/
		$config['bool_file'] = TRUE;
		$config['file_target'] = array();
		$config['file_size'] = 2;
		$config['upload_limit'] = TRUE;
		$config['bool_thumb'] = FALSE;
		$config['thumb_target'] = array();
		$config['thumb_size'] = array();
		/************************************/
		$config['bool_editor'] = FALSE;
		$config['editor_target'] = array();
		$config['limit_img_width'] = 500;

		$config['bool_editor_thumb'] = FALSE;
		$config['editor_thumb_width'] = 150;
		$config['editor_thumb_height'] = 150;
		/************************************/
		$config['bool_navi_page'] = FALSE;
		$config['loop_scale'] = 10;
		$config['page_scale'] = 10;
		$config['navi_url'] = '';
		$config['navi_pg_mode'] = 'list';
		$config['navi_qry'] = '';
		$config['navi_mode'] = 'link';
		$config['navi_load_id'] = '';

		$this->config = $config;
		$this->BASIC($config,$tpl);
	}

	function view()
	{
		if(empty($_GET['txnid'])) {
			jsonMsg(0);
		}
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['fields'] = "*";
		$query['where'] = 'where txnid=\''.$this->dbcon->escape($_GET['txnid']).'\'';
		$this->bView($query);
	}

	function editForm()
	{
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'row';
		$query['fields'] = "*";
		$query['where'] = 'where txnid=\''.$this->dbcon->escape($_GET['txnid']).'\'';
		$this->assign_default_value($this->dbcon->query($query));
	}

	function write()
	{
		$sql = "select txnid from ".$this->config['table_name']." where txnid='".$this->dbcon->escape($_POST['txnid'])."'";
		$txnid = $this->dbcon->query_unique_value($sql);
		$query = array();
		if($txnid) {
			$query['tool'] = 'update';
			$query['where'] = 'where txnid=\''.$this->dbcon->escape($_POST['txnid']).'\'';
			$r = $this->bEdit($query,$_POST);
		} else {
			$query['tool'] = 'insert';
			$r = $this->bWrite($query,$_POST);
		}
		if($r) {
			jsonMsg(1);
		} else {
			jsonMsg(0);
		}
	}

	function del()
	{
		if(empty($_POST['txnid'])) {
			if($config_basic['bool_ssl'] > 0) {
				errMsg(Lang::main_coin1);
			}
			else {
				jsonMsg(0,Lang::main_coin1);
			}
		}

		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'delete';
		$query['where'] = 'where txnid=\''.$_POST['txnid'].'\'';
		if($this->dbcon->query($query,__FILE__,__LINE__)) {
			jsonMsg(1);
		} else {
			jsonMsg(0);
		}
	}

	function dividend() {

		// KRW 같은 비암호화폐인지 확인.
		$currency_info = $GLOBALS['tradeapi']->get_currency($_POST['symbol']);
		if(!$currency_info || $currency_info[0]->symbol!=$_POST['symbol']) {
			responseFail(Lang::main_coin2);
		}
		$currency_info = $currency_info[0];

		// 암호화폐 배당
		if($currency_info->crypto_currency=='Y') {
			// 보내는 계좌 정보 확인.
			$sender_info = $GLOBALS['tradeapi']->get_member_info_by_userid($_POST['sender_userid']);
			$sernder_wallet = $GLOBALS['tradeapi']->get_wallet($sender_info->userno, $_POST['symbol']);
			if(!$sernder_wallet) {
				responseFail( str_replace('{symbol}', strtoupper($_POST['symbol']), '보내는 계좌의 정보가 없습니다. 보내는 회원의 {symbol} 계좌를 생성해주세요.'));
			}
			$sernder_wallet = $sernder_wallet[0];
		}


		// 배당 정보 확인.
		if(!$_POST['data']) {
			$_POST['data'] = array();
		}

		for($i=0; $i<count($_POST['data']); $i++) {
			$row = $_POST['data'][$i];
			// 회원번호 찾기
			$member_info = $GLOBALS['tradeapi']->get_member_info_by_userid($row['회원아이디']);
			if(!$member_info->userno) {
				$row['결과'] = '실패(회원정보 없음)';
			} else {
				// 암호화폐 배당
				if($currency_info->crypto_currency=='Y') {
					$row['결과'] = '실패';
					/* 암호화폐 배당은 실제 필요시 오픈.
					// 받는사람 지갑확인.
					$receiver_wallet = $GLOBALS['tradeapi']->get_wallet($member_info->userno, $_POST['symbol']);
					if(!$receiver_wallet) {
						$row['결과'] = '실패(지값없음)';
					} else {
						// 코인 발송 - 보내는 계정은 전달 받아서 처리하는것이 좋아 보이나 차후에 다시 생각해 보기로 함.
						$txnid = $GLOBALS['tradeapi']->send_coin($_POST['symbol'], $sernder_wallet->address, $sernder_wallet->userno, $to_address, $amount, $fee, '', $sernder_wallet->userno);
						if(trim($txnid)=='') {
							$row['결과'] = '실패(코인발송 실패)';
						} else {
							// 잔액 추가
							$r = $GLOBALS['tradeapi']->add_wallet($member_info->userno, $_POST['symbol'], $row['금액']);
							// 로그 추가
							if($r){
								$GLOBALS['tradeapi']->add_wallet_txn($member_info->userno, $receiver_wallet->address, $_POST['symbol'], $txnid, 'D', 'I', $row['금액'], 0, 0, "D"); // 입금액을 알기 때문에 잔액도 주고 완료처리합니다.
								$row['결과'] = '성공';
							} else {
								$row['결과'] = '실패';
							}
						}
					}
					*/
				}
				// 비암호화폐 배당
				if($currency_info->crypto_currency=='N') {
					$row['결과'] = '실패';
					// 잔액 추가
					$r = $GLOBALS['tradeapi']->add_wallet($member_info->userno, $_POST['symbol'], $row['금액']);
					// 로그 추가
					if($r){
						$r = $GLOBALS['tradeapi']->add_wallet_txn($member_info->userno, $currency_info->backup_address, $_POST['symbol'], $member_info->name, 'D', 'I', $row['금액'], 0, 0, "D");
						if($r){
							$row['결과'] = '성공';
						} else {
							// 로그 추가 못하면 해당 회원 롤백
							$GLOBALS['tradeapi']->del_wallet($member_info->userno, $_POST['symbol'], $row['금액']);
							$row['결과'] = '실패';
						}
					} else {
						$row['결과'] = '실패';
					}
				}
			}
			$row['날짜'] = date('Y-m-d H:i:s');
			$_POST['data'][$i] = $row;
		}

		// 결과 응답.
		responseSuccess($_POST['data']);


	}
	function airdrop() {

		// 스톡옵션 정보 확인.
		if(!$_POST['data']) {
			$_POST['data'] = array();
		}

		for($i=0; $i<count($_POST['data']); $i++) {

			$row = $_POST['data'][$i]; // '종목코드',"회원아이디","주당금액","주식수량","합계금액","보유기간(일)","보유날짜"
			// var_dump($row); exit;

			$row = array_merge(array('결과'=>''), $row); // 결과를 첫번째 항목으로 추가함.
			// $row['결과'] = '';
			
			$_symbol = $row['종목코드'];
			// KRW 같은 비암호화폐인지 확인.
			if($_symbol=='KRW') {
				$row['결과'] = '매매 종목만 스톡옵션을 지급하실 수 있습니다.'; $_POST['data'][$i] = $row; continue;
			}
			$currency_info = $GLOBALS['tradeapi']->get_currency($_symbol);
			if(!$currency_info || $currency_info[0]->symbol!=$_symbol) {
				$row['결과'] = '올바른 종목코드를 입력해주세요.'; $_POST['data'][$i] = $row; continue;
			}
			if($currency_info[0]->tradable!='Y') {
				$row['결과'] = '매매 종목만 스톡옵션을 지급하실 수 있습니다.'; $_POST['data'][$i] = $row; continue;
			}
			$currency_info = $currency_info[0];

			// 암호화폐 스톡옵션
			if($currency_info->crypto_currency=='Y') {
				// 보내는 계좌 정보 확인.
				// $sender_info = $GLOBALS['tradeapi']->get_member_info_by_userid($_POST['sender_userid']);
				// $sernder_wallet = $GLOBALS['tradeapi']->get_wallet($sender_info->userno, $_symbol);
				// if(!$sernder_wallet) {
				// 	responseFail( str_replace('{symbol}', strtoupper($_symbol), '보내는 계좌의 정보가 없습니다. 보내는 회원의 {symbol} 계좌를 생성해주세요.'));
				// }
				// $sernder_wallet = $sernder_wallet[0];
			}

			// 회원번호 찾기
			$member_info = $GLOBALS['tradeapi']->get_member_info_by_userid($row['회원아이디']);
			if(!$member_info->userno) {
				$row['결과'] = '실패(회원정보 없음)'; $_POST['data'][$i] = $row; continue;
			} else {
				// 암호화폐 스톡옵션
				if($currency_info->crypto_currency=='Y') {
					$row['결과'] = '실패(암호화폐 지급 중단)'; $_POST['data'][$i] = $row; continue;
					/* 암호화폐 스톡옵션은 실제 필요시 오픈.
					// 받는사람 지갑확인.
					$receiver_wallet = $GLOBALS['tradeapi']->get_wallet($member_info->userno, $_symbol);
					if(!$receiver_wallet) {
						$row['결과'] = '실패(지값없음)';
					} else {
						// 코인 발송 - 보내는 계정은 전달 받아서 처리하는것이 좋아 보이나 차후에 다시 생각해 보기로 함.
						$txnid = $GLOBALS['tradeapi']->send_coin($_symbol, $sernder_wallet->address, $sernder_wallet->userno, $to_address, $amount, $fee, '', $sernder_wallet->userno);
						if(trim($txnid)=='') {
							$row['결과'] = '실패(코인발송 실패)';
						} else {
							// 잔액 추가
							$r = $GLOBALS['tradeapi']->add_wallet($member_info->userno, $_symbol, $row['금액']);
							// 로그 추가
							if($r){
								$GLOBALS['tradeapi']->add_wallet_txn($member_info->userno, $receiver_wallet->address, $_symbol, $txnid, 'D', 'I', $row['금액'], 0, 0, "D"); // 입금액을 알기 때문에 잔액도 주고 완료처리합니다.
								$row['결과'] = '성공';
							} else {
								$row['결과'] = '실패';
							}
						}
					}
					*/
				}
				// 비암호화폐 스톡옵션
				if($currency_info->crypto_currency=='N') {

					if($row['보유기간(일)']) {

						$_userid = $row['회원아이디'];
						$_userno = $member_info->userno;
						$_amount = str_replace(',','',$row['주당금액']);
						$_volumn = str_replace(',','',$row['주식수량']);
						$_price = str_replace(',','',$row['합계금액']);
						$_lockup_day = $row['보유기간(일)'];
						$_lockup_date = $row['보유날짜'];
						// 스톡옵션 지급 내역에 추가()
						$r = $this->dbcon->query("INSERT INTO js_trade_airdrop SET symbol='{$this->dbcon->escape($_symbol)}', userno='{$this->dbcon->escape($_userno)}', amount='{$this->dbcon->escape($_amount)}', volumn='{$this->dbcon->escape($_volumn)}', price='{$this->dbcon->escape($_price)}', lockup_day='{$this->dbcon->escape($_lockup_day)}', lockup_date='{$this->dbcon->escape($_lockup_date)}', nickname='', regdate=NOW() ");
						if($r){
							$row['결과'] = '성공';
							$row['등록날짜'] = date('Y-m-d H:i:s');
						} else {
							$row['결과'] = '실패(Database Insert 오류)';
						}

					} else {
						// 직접 지갑에 넣어주던 방식 ... 미사용함. lockup 날짜 있음
						// // 잔액 추가
						// $r = $GLOBALS['tradeapi']->add_wallet($member_info->userno, $_symbol, $row['금액']);
						// // 로그 추가
						// if($r){
						// 	$r = $GLOBALS['tradeapi']->add_wallet_txn($member_info->userno, $currency_info->backup_address, $_symbol, $member_info->name, 'D', 'I', $row['금액'], 0, 0, "D");
						// 	if($r){
						// 		$row['결과'] = '성공';
						// 	} else {
						// 		// 로그 추가 못하면 해당 회원 롤백
						// 		$GLOBALS['tradeapi']->del_wallet($member_info->userno, $_symbol, $row['금액']);
						// 		$row['결과'] = '실패';
						// 	}
						// } else {
						// 	$row['결과'] = '실패';
						// }
					}
				}
			}
			$_POST['data'][$i] = $row;
		}

		// 결과 응답.
		responseSuccess($_POST['data']);


	}

	function withdraw()
	{
		if(empty($_POST['txnid'])) {
			if($config_basic['bool_ssl'] > 0) {
				errMsg(Lang::main_coin1);
			}
			else {
				jsonMsg(0,Lang::main_coin1);
			}
		}

		// txn 정보 확인.
		$query = "SELECT * FROM js_exchange_wallet_txn where txnid='{$this->dbcon->escape($_POST['txnid'])}' ";
		$txn_info = $this->dbcon->query_unique_array($query,__FILE__,__LINE__);
		if(!$txn_info['txnid']) {
			jsonMsg(0, Lang::main_coin3);
		}
		if($txn_info['status']!='O') {
			jsonMsg(0, Lang::main_coin4);
		}

		// 화폐 정보 확인.
		$query = "SELECT * FROM js_trade_currency where symbol='{$this->dbcon->escape($txn_info['symbol'])}' ";
		$currency_info = $this->dbcon->query_unique_array($query,__FILE__,__LINE__);

		// 지갑 확인(항상 있어요. 하지만 수동으로 지우거나 다른오류가 있을수 있으니 확인합니다.)
		$query = "SELECT * FROM js_exchange_wallet WHERE userno='{$this->dbcon->escape($txn_info['userno'])}' AND symbol='{$this->dbcon->escape($txn_info['symbol'])}' ";
		$wallet = $this->dbcon->query_unique_array($query);
		if(!$wallet) {
			jsonMsg(0, Lang::main_coin5);
		}

		// 선차감이라 확인하지 않음.
		// // 잔액 확인 : 잔액 >= amount + fee
		// if($wallet['confirmed'] < $txn_info['amount'] + $txn_info['fee']) {
		// 	jsonMsg(0, Lang::main_coin6.$wallet['confirmed'].', '.$txn_info['amount'].', '.$txn_info['fee']);
		// }

		$this->dbcon->transaction(1);

		$txnid = '';// 일반화폐는 전송 안함.
		// JIN 암호화폐 전송.
		// JIN 은 콜드월랫이 없어 사용자 지갑에서 직접 발송했었습니다. 하지만 UKRW를 진행하면서 누락된것으로 보입니다.
		// @todo JIN은 직접 발송하는걸로 처리하기. - 테스트 필요합니다.
		// if($currency_info['crypto_currency']=='Y' && $currency_info['symbol']=='JIN') {
		// 	$sender_wallet = $wallet;
		// 	// 선차감했기때문에 잔액 차감은 안함. 블록만 보냅니다.
		// 	try {
		// 		$txnid = $GLOBALS['tradeapi']->send_coin ($currency_info['symbol'], $sender_wallet['address'], $sender_wallet['userno'], $txn_info['address_relative'], $txn_info['amount'], $txn_info['fee'], '', $sender_wallet['userno']);
		// 	} catch (Exception $e) {
		// 		// $this->dbcon->transaction(3); // rollback
		// 		jsonMsg(0, '블록체인 전송시 오류가 발생했습니다. 서버 관리자에게 문의해 주세요.');
		// 	}
		// 	if(!$txnid) {
		// 		$this->dbcon->transaction(3); // rollback
		// 		jsonMsg(0, Lang::main_coin7);
		// 	}
		// }

		// UKRW , JIN 암호화폐 전송. (콜드월렛에서 자동으로 전송하는 코인들)
		if($currency_info['crypto_currency']=='Y' && $currency_info['auto_withdrwal_userno']) {
			// 자동출금 전용 계좌 정보가 있으면 해당 계좌에서 전송
			$sender_wallet = $wallet;
			// if($currency_info['auto_withdrwal_userno']) {
				$query = "SELECT * FROM js_exchange_wallet WHERE userno='{$this->dbcon->escape($currency_info['auto_withdrwal_userno'])}' AND symbol='{$this->dbcon->escape($txn_info['symbol'])}' ";
				$auto_withdrwal_wallet = $this->dbcon->query_unique_array($query);
				if($auto_withdrwal_wallet['userno']) {
					$sender_wallet = $auto_withdrwal_wallet;
				} else {
					jsonMsg(0, $currency_info['symbol'].' 출금 전용계좌가 없습니다. 출금 전용계좌를 만들어 주세요.');
				}
				// 출금 전용 계좌 잔액 확인 : 잔액 >= 실재로 보내는 금액인 amount만 확인. 출금전용계좌에서 출금하는건 이미 신청시 차감했기때문에 확인 안한다.
				if( $wallet['userno']!=$auto_withdrwal_wallet['userno'] && $auto_withdrwal_wallet['confirmed'] < $txn_info['amount']) {
					jsonMsg(0, $sender_wallet['symbol'].' 출금 전용계좌에 잔액이 부족합니다. 출금계좌 잔액:' . $sender_wallet['confirmed'].', 전송액:'.$txn_info['amount']);
				}
				try {
					$txnid = $GLOBALS['tradeapi']->send_coin ($currency_info['symbol'], $sender_wallet['address'], $sender_wallet['userno'], $txn_info['address_relative'], $txn_info['amount'], $txn_info['fee'], '', $sender_wallet['userno']);
				} catch (Exception $e) {
					// $this->dbcon->transaction(3); // rollback
					jsonMsg(0, '블록체인 전송시 오류가 발생했습니다. 서버 관리자에게 문의해 주세요.');
				}
				if(!$txnid) {
					$this->dbcon->transaction(3); // rollback
					jsonMsg(0, Lang::main_coin7);
				}
				// walletmanager 의 출금 요청이 아니고 자동출금 전용계좌에서 발송하는 경우 로그 남기기.
				if($currency_info['auto_withdrwal_userno'] && $auto_withdrwal_wallet['userno']==$sender_wallet['userno'] && $wallet['userno']!=$auto_withdrwal_wallet['userno']) {
					// 블록체인 수수료 - 실제 전송시 발생한 블록체인수수료를 저장할때 사용합니다. 지금은 0으로 사용하지 않습니다.
					$fee = 0; // UKRW 수수료는 없다. BTC 처럼 수수료 있는 코인을 자동 출금처리할때는 수수료를 txn정보에서 받아야와 합니다.
					$send_amount = $txn_info['amount']; // 출금수수료는 이미 차감된 상태로 DB에 저장되어 있습니다.
					// 잔액 차감 : 자동출금 전용 계좌
					$GLOBALS['tradeapi']->del_wallet($sender_wallet['userno'], $txn_info['symbol'], $send_amount);
					// 출금 로그 작성
					$query = "INSERT INTO js_exchange_wallet_txn SET userno='{$this->dbcon->escape($sender_wallet['userno'])}', symbol='{$this->dbcon->escape($sender_wallet['symbol'])}', address='{$this->dbcon->escape($sender_wallet['address'])}', regdate=NOW(), txndate=NOW(), address_relative='{$this->dbcon->escape($txn_info['address_relative'])}', txn_type='W', direction='O', amount='{$this->dbcon->escape($send_amount)}', fee='{$this->dbcon->escape($fee)}', tax='0', status='D', key_relative='{$this->dbcon->escape($txnid)}' ";
					$this->dbcon->query($query);
					// 받는사람이 내부 지갑이면 바로 입금처리해준다. 입금확인스크립트에서는 이미 처리된 거래내역이라 입금처리가 않된다.
					$receiver_inner_wallet = $this->dbcon->query_unique_object("select * from js_exchange_wallet where address='{$this->dbcon->escape($txn_info['address_relative'])}' and symbol='{$this->dbcon->escape($txn_info['symbol'])}'");
					if($receiver_inner_wallet) {
						// 잔액 증가
						$this->dbcon->query("update js_exchange_wallet set confirmed=confirmed+{$send_amount} where address='{$this->dbcon->escape($receiver_inner_wallet->address)}' and symbol='{$this->dbcon->escape($receiver_inner_wallet->symbol)}' ");
						// 로그 작성
						$this->dbcon->query("insert into js_exchange_wallet_txn set userno='{$receiver_inner_wallet->userno}', symbol='{$this->dbcon->escape($receiver_inner_wallet->symbol)}', address='{$this->dbcon->escape($receiver_inner_wallet->address)}', regdate=now(), txndate=now(), address_relative='{$this->dbcon->escape($txn_info['address'])}', txn_type='R', direction='I', amount='{$send_amount}', fee='0', tax='0', status='D', key_relative='{$this->dbcon->escape($txnid)}', txn_method='COIN', service_name='WALLET', msg='' ");
					}
				}
			// } else {
				// 다른 코인은 수동 출금 후 승인처리만 진행.
				// jsonMsg(0, $currency_info['symbol'].' 출금 전용 회원이 없습니다. 출금 전용계좌를 만들어 주세요.');
			// }
		}
		// txn 상태 변경( O -> D )
		$query = "UPDATE js_exchange_wallet_txn SET status = 'D', txndate=SYSDATE(), key_relative='{$this->dbcon->escape($txnid)}' WHERE txnid='{$this->dbcon->escape($txn_info['txnid'])}' ";
		if($this->dbcon->query($query,__FILE__,__LINE__)) {

			// 수수료 계좌로 출금수수료를 전송합니다. txn_type F: 수수료
			if($currency_info['fee_save_userno'] && $txn_info['fee']) {
				$symbol = $txn_info['symbol'];
				$fee = $txn_info['fee'];
				$fee_save_userno = $currency_info['fee_save_userno'];
				$fee_save_wallet = $this->dbcon->query_unique_object("SELECT * FROM js_exchange_wallet WHERE userno='{$this->dbcon->escape($fee_save_userno)}' AND symbol='{$this->dbcon->escape($symbol)}' ");
				$this->dbcon->query("UPDATE js_exchange_wallet SET confirmed=confirmed+$fee WHERE userno='{$this->dbcon->escape($fee_save_userno)}' and symbol='{$this->dbcon->escape($symbol)}' ");
				$this->dbcon->query("INSERT INTO js_exchange_wallet_txn SET userno='{$this->dbcon->escape($fee_save_userno)}', symbol='{$this->dbcon->escape($symbol)}', address='{$this->dbcon->escape($fee_save_wallet->address)}', regdate=now(), txndate=now(), address_relative='".$this->dbcon->escape($txn_info['address'])."', txn_type='F', direction='I', amount='$fee', fee='0', tax='0', status='D', key_relative='".$this->dbcon->escape($txn_info['txnid'])."', txn_method='COIN', service_name='WALLET', msg='' ");
			}

			$this->dbcon->transaction(2); // commit
			jsonMsg(1);
		} else {
			// $this->dbcon->transaction(3); // rollback
			jsonMsg(0, '출금 상태를 변경하지 못했습니다. 다시 요청해주세요.');
		}

	}

	function deposit()
	{
		if(empty($_POST['txnid'])) {
			if($config_basic['bool_ssl'] > 0) {
				errMsg(Lang::main_coin1);
			}
			else {
				jsonMsg(0,Lang::main_coin1);
			}
		}

		// txn 정보 확인.
		$query = "SELECT * FROM js_exchange_wallet_txn where txnid='{$this->dbcon->escape($_POST['txnid'])}' ";
		$txn_info = $this->dbcon->query_unique_array($query,__FILE__,__LINE__);
		if(!$txn_info['txnid']) {
			jsonMsg(0, Lang::main_coin3);
		}
		if($txn_info['status']!='O') {
			jsonMsg(0, Lang::main_coin4);
		}

		// 화폐 정보 확인.
		$query = "SELECT * FROM js_trade_currency where symbol='{$this->dbcon->escape($txn_info['symbol'])}' ";
		$currency_info = $this->dbcon->query_unique_array($query,__FILE__,__LINE__);

		// 지갑 확인(항상 있어요. 하지만 수동으로 지우거나 다른오류가 있을수 있으니 확인합니다.)
		$query = "SELECT count(*) cnt FROM js_exchange_wallet WHERE userno='{$this->dbcon->escape($txn_info['userno'])}' AND symbol='{$this->dbcon->escape($txn_info['symbol'])}' ";
		$wallet = $this->dbcon->query_unique_value($query);
		if(!$wallet) {
			// KRW 없으면 추가
			if($txn_info['symbol']=='KRW') {
				$query = "INSERT INTO js_exchange_wallet SET userno='{$this->dbcon->escape($txn_info['userno'])}', symbol='{$this->dbcon->escape($txn_info['symbol'])}', address='{$this->dbcon->escape($txn_info['userno'])}', regdate=NOW() ";
				$this->dbcon->query($query);
			} else {
				jsonMsg(0, Lang::main_coin5);
			}
		}

		// 입급시 추가 금액계산
		$currency_info['tax_in_ratio'] = $currency_info['tax_in_ratio'] ? $currency_info['tax_in_ratio'] : 0;
		$currency_info['display_decimals'] = $currency_info['display_decimals'] ? $currency_info['display_decimals'] : 0;
		$currency_info['fee_in'] = $currency_info['fee_in'] ? $currency_info['fee_in'] : 0;
		$amount = $txn_info['amount'] - round($txn_info['amount'] * $currency_info['tax_in_ratio'], $currency_info['display_decimals']) - $currency_info['fee_in'];

		// 지갑 잔액 추가
		$query = "UPDATE js_exchange_wallet SET confirmed = confirmed + '{$this->dbcon->escape($amount)}' WHERE userno='{$this->dbcon->escape($txn_info['userno'])}' AND symbol='{$this->dbcon->escape($txn_info['symbol'])}'  ";
		$this->dbcon->query($query);

		// txn 상태 변경( O -> D )
		$query = "UPDATE js_exchange_wallet_txn SET status = 'D', txndate=NOW() WHERE txnid='{$this->dbcon->escape($txn_info['txnid'])}' ";
		if($this->dbcon->query($query,__FILE__,__LINE__)) {
			jsonMsg(1);
		} else {
			jsonMsg(0);
		}
	}

	function cancel()
	{
		if(empty($_POST['txnid'])) {
			if($config_basic['bool_ssl'] > 0) {
				errMsg(Lang::main_coin1);
			}
			else {
				jsonMsg(0,Lang::main_coin1);
			}
		}

		// txn 정보 확인.
		$query = "SELECT * FROM js_exchange_wallet_txn where txnid='{$this->dbcon->escape($_POST['txnid'])}' ";
		$txn_info = $this->dbcon->query_unique_array($query,__FILE__,__LINE__);
		if(!$txn_info['txnid']) {
			jsonMsg(0, Lang::main_coin3);
		}
		if($txn_info['status']!='O') {
			jsonMsg(0, Lang::main_coin4);
		}

		$this->dbcon->transaction(1);

		// txn 상태 변경( O -> C )
		try {
			$query = "UPDATE js_exchange_wallet_txn SET status = 'C', txndate = NOW() WHERE txnid='{$this->dbcon->escape($txn_info['txnid'])}' AND status='O' ";
			$this->dbcon->query($query,__FILE__,__LINE__);
			// 출금일경우 지갑 잔액 추가
			if($txn_info['txn_type']=='W') {
				$query = "UPDATE js_exchange_wallet SET confirmed = confirmed + '{$this->dbcon->escape($txn_info['amount']+$txn_info['fee'])}' WHERE userno='{$this->dbcon->escape($txn_info['userno'])}' AND symbol='{$this->dbcon->escape($txn_info['symbol'])}'  ";
				$this->dbcon->query($query);
			}
		} catch (Exception $e) {
			$this->dbcon->transaction(3); // rollback
			jsonMsg(0);
		}
		$this->dbcon->transaction(2); // commit
		jsonMsg(1);
	}

	public function assign_default_value($val='') {
		$default_value = array();
		if($val) {
			$default_value = array_merge($default_value, $val);
		}
		$this->tpl->assign('currency_info', $default_value);
	}

	/**
	 * 트랜젝션 리스트 데이터를 추출합니다.
	 * @param number 페이징용으로 사용 할 이전페이지 마지막 줄의 txnid. 첫페이지는 빈값을 사용합니다.
	 * @param number 추출 개수입니다. 기본값 20개입니다.
	 */
	public function lists() {
		$query = array();
		$query['table_name'] = $this->config['table_name'].' t1 left join js_member t2 on t1.userno=t2.userno ';
		$query['tool'] = 'select';

		$query['fields'] = " txnid, symbol, `address`, t1.regdate, txndate, address_relative, amount, fee, key_relative, t2.name,
		CASE WHEN txn_type='R' THEN address_relative ELSE '' END AS `sender`,
		CASE WHEN txn_type='W' THEN address_relative ELSE '' END AS `receiver`,
		CASE WHEN txn_type='R' THEN 'Deposit' WHEN txn_type='D' THEN 'Dividend' WHEN txn_type='W' THEN 'Withdraw' WHEN txn_type='B' THEN 'Backup' END AS `txn_type`,
		CASE WHEN `status`='D' THEN 'Success' WHEN `status`='O' THEN 'Waiting' WHEN `status`='P' THEN 'Processing' WHEN `status`='C' THEN 'Cancel' END AS `status`, userid, (select confirmed from js_exchange_wallet where userno=t1.userno and symbol=t1.symbol limit 1) balance, (select locked from js_exchange_wallet where userno=t1.userno and symbol=t1.symbol limit 1) locked, (select autolocked from js_exchange_wallet where userno=t1.userno and symbol=t1.symbol limit 1) autolocked ";
		$query['where'] = " WHERE txn_type IN ('R','W','D') ";
		if($_GET['searchval']) {
			$query['where'] .= " AND ( symbol LIKE '%".$this->dbcon->escape($_GET['searchval'])."%' OR address LIKE '%".$this->dbcon->escape($_GET['searchval'])."%' OR key_relative LIKE '%".$this->dbcon->escape($_GET['searchval'])."%' OR userid LIKE '%".str_replace('82010', '8210', $this->dbcon->escape($_GET['searchval']))."%' OR t1.address_relative LIKE '%".$this->dbcon->escape($_GET['searchval'])."%' ) ";
		}
		if($_REQUEST['symbol']) {
			$query['where'] .= " AND t1.symbol='{$this->dbcon->escape($_REQUEST['symbol'])}' ";
		}
		if($_REQUEST['txn_type']) {
			$query['where'] .= " AND t1.txn_type='{$this->dbcon->escape($_REQUEST['txn_type'])}' ";
		}
		//날짜 검색
		if($_POST['s_year'] && $_POST['s_month'] && $_POST['s_div']){
			if($_POST['s_day']){
				$s_yearmonthday = $this->dbcon->escape($_POST['s_year']).$this->dbcon->escape($_POST['s_month']).$this->dbcon->escape($_POST['s_day']);
				$query['where'] .= " AND DATE_FORMAT(t1.{$this->dbcon->escape($_POST['s_div'])}, '%Y%m%d')='{$s_yearmonthday}'";

			}else{
				$s_yearmonth = $this->dbcon->escape($_POST['s_year']).$this->dbcon->escape($_POST['s_month']);
				$query['where'] .= " AND DATE_FORMAT(t1.{$this->dbcon->escape($_POST['s_div'])}, '%Y%m')='{$s_yearmonth}'";
			}
		}
		//날짜 기간 검색
		$_s_div = trim($_POST['s_div']);
		if($_POST['start_date']){
			$t = $_POST['start_date'];
			$query['where'] .= " AND '{$this->dbcon->escape($t)}' <= t1.{$this->dbcon->escape($_s_div)} ";
		}
		if($_POST['end_date']){
			$t = $_POST['end_date'];
			$query['where'] .= " AND t1.{$this->dbcon->escape($_s_div)} <= '{$this->dbcon->escape($t)} 23:59:59' ";
		}

        if($_POST['name']) {
            $t = $_POST['name'];

            if($_REQUEST['txn_type'] == "W") {
                $query['where'] .= " AND t2.name like '%{$t}%'";
            } else {
                $query['where'] .= " AND t1.address_relative like '%{$t}%'";
            }
        }
		
		if($_GET['sort_target'] ) {
			$query['where'] .= ' ORDER BY ';
			for($i=0;$i<count($_GET['sort_target']);$i++) {
				if($i>0) {
					$query['where'] .= ', ';
				}
				$query['where'] .= 't1.'.$_GET['sort_target'][$i].' '.$_GET['sort_method'][$i];
			}
		} else {
			$query['where'] .= ' ORDER BY t1.regdate DESC';
		}
		// $_GET['start'] = preg_replace('/[^0-9]/g', '', $_GET['start']);
		// $_GET['start'] = $_GET['start'] ? $_GET['start'] : 0;
		// $sql .=" LIMIT {$_GET['start']}, {$this->dbcon->escape($rows)} ";
		// var_dump($query); exit;
		$list = $this->bList($query,'loop','_lists',true);
		return $list;
	}
	/**
	 * 트랜젝션 리스트 데이터를 JSON 형식으로 출력합니다.
	 * @param number 페이징용으로 사용 할 이전페이지 마지막 줄의 txnid. 첫페이지는 빈값을 사용합니다.
	 * @param number 추출 개수입니다. 기본값 20개입니다.
	 */
	public function _lists($row) {
		// $wallet = $this->get_wallet_by_address($row['address']);
		$row['amount'] = $row['amount'] * 1;
		$row['fee'] = $row['fee'] * 1;
		$row['txndate'] = strtolower($row['status'])=='waiting' ? '' : $row['txndate'];
		// $member = $this->get_member_by_userno($row['userno']);
		// $row['userid'] = $member->userid;
		return $row;
	}

	public function get_wallet_by_address($address) {
		$sql = "SELECT * from js_exchange_wallet where address='{$this->dbcon->escape($address)}' limit 1 ";
		return $this->dbcon->query_unique_object($sql);
	}
	public function get_member_by_userno($userno) {
		$sql = "SELECT * from js_member where userno='{$this->dbcon->escape($userno)}' limit 1 ";
		return $this->dbcon->query_unique_object($sql);
	}
}

function tradeWalletTxnQuery($arr)
{
	$qry = array();
	if(!empty($arr['symbol']))  { $qry[] = 'symbol=\''.$arr['symbol'].'\''; }
	if(!empty($arr['name']))  { $qry[] = 'name=\''.$arr['name'].'\''; }
	if(!empty($arr['fee_in']))  { $qry[] = 'fee_in=\''.$arr['fee_in'].'\''; }
	if(!empty($arr['tax_in_ratio']))  { $qry[] = 'tax_in_ratio=\''.$arr['tax_in_ratio'].'\''; }
	if(!empty($arr['fee_out']))  { $qry[] = 'fee_out=\''.$arr['fee_out'].'\''; }
	if(!empty($arr['tax_out_ratio']))  { $qry[] = 'tax_out_ratio=\''.$arr['tax_out_ratio'].'\''; }
	if(!empty($arr['fee_buy_ratio']))  { $qry[] = 'fee_buy_ratio=\''.$arr['fee_buy_ratio'].'\''; }
	if(!empty($arr['tax_buy_ratio']))  { $qry[] = 'tax_buy_ratio=\''.$arr['tax_buy_ratio'].'\''; }
	if(!empty($arr['fee_sell_ratio']))  { $qry[] = 'fee_sell_ratio=\''.$arr['fee_sell_ratio'].'\''; }
	if(!empty($arr['tax_sell_ratio']))  { $qry[] = 'tax_sell_ratio=\''.$arr['tax_sell_ratio'].'\''; }
	if(!empty($arr['tax_income_ratio']))  { $qry[] = 'tax_income_ratio=\''.$arr['tax_income_ratio'].'\''; }
	if(!empty($arr['trade_min_volume']))  { $qry[] = 'trade_min_volume=\''.$arr['trade_min_volume'].'\''; }
	if(!empty($arr['trade_max_volume']))  { $qry[] = 'trade_max_volume=\''.$arr['trade_max_volume'].'\''; }
	if(!empty($arr['out_min_volume']))  { $qry[] = 'out_min_volume=\''.$arr['out_min_volume'].'\''; }
	if(!empty($arr['out_max_volume']))  { $qry[] = 'out_max_volume=\''.$arr['out_max_volume'].'\''; }
	if(!empty($arr['display_decimals']))  { $qry[] = 'display_decimals=\''.$arr['display_decimals'].'\''; }
	if(!empty($arr['regdate']))  { $qry[] = 'regdate=\''.$arr['regdate'].'\''; }
	if(!empty($arr['active']))  { $qry[] = 'active=\''.$arr['active'].'\''; }
	if(!empty($arr['creatable']))  { $qry[] = 'creatable=\''.$arr['creatable'].'\''; }
	if(!empty($arr['crypto_currency']))  { $qry[] = 'crypto_currency=\''.$arr['crypto_currency'].'\''; }
	if(!empty($arr['backup_address']))  { $qry[] = 'backup_address=\''.$arr['backup_address'].'\''; }
	if(!empty($arr['sortno']))  { $qry[] = 'sortno=\''.$arr['sortno'].'\''; }
	if(!empty($arr['menu']))  { $qry[] = 'menu=\''.$arr['menu'].'\''; }
	if(!empty($arr['color']))  { $qry[] = 'color=\''.$arr['color'].'\''; }
	if(isset($arr['check_deposit']))  { $qry[] = 'check_deposit=\''.$arr['check_deposit'].'\''; }
	if(!empty($arr['transaction_outlink']))  { $qry[] = 'transaction_outlink=\''.$arr['transaction_outlink'].'\''; }
	if(!empty($arr['circulating_supply']))  { $qry[] = 'circulating_supply=\''.$arr['circulating_supply'].'\''; }
	if(isset($arr['max_supply']))  { $qry[] = 'max_supply=\''.$arr['max_supply'].'\''; }
	if(!empty($arr['price']))  { $qry[] = 'price=\''.$arr['price'].'\''; }
	if($_POST['pg_mode'] == 'write') {
		$qry[] = 'regdate=NOW()';
	}
	return implode(',',$qry);
}
