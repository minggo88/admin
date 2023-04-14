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
		$config['table_name'] = 'js_trade_currency';
		$config['query_func'] = 'tradeCurrencyQuery';
		$config['write_mode'] = 'ajax';
		/************************************/
		$config['file_dir'] = '/data/design';
		$config['thumb_dir'] = '/data/thumbnail';
		$config['temp_dir'] = '/data/editorTemp';
		$config['editor_dir'] = '/data/editor';
		/************************************/
		$config['no_tag'] = array('symbol','name','fee_in','tax_in_ratio','fee_out','tax_out_ratio','fee_buy_ratio','tax_buy_ratio','fee_sell_ratio','tax_sell_ratio','tax_income_ratio','trade_min_volume','trade_max_volume','out_min_volume','out_max_volume','display_decimals','regdate','active','creatable','crypto_currency','backup_address','sortno','menu','color','check_deposit','transaction_outlink','circulating_supply','max_supply','price');
		$config['no_space'] = array('symbol','fee_in','tax_in_ratio','fee_out','tax_out_ratio','fee_buy_ratio','tax_buy_ratio','fee_sell_ratio','tax_sell_ratio','tax_income_ratio','trade_min_volume','trade_max_volume','out_min_volume','out_max_volume','display_decimals','regdate','active','creatable','crypto_currency','sortno','menu','color','check_deposit','transaction_outlink','circulating_supply','max_supply','price');
		$config['staple_article'] = array('symbol'=>'blank','name'=>'blank');
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
		if(empty($_GET['symbol'])) {
			jsonMsg(0);
		}
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['fields'] = "*";
		$query['where'] = 'where symbol=\''.$this->dbcon->escape($_GET['symbol']).'\'';
		$this->bView($query);
	}

	function editForm()
	{
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'row';
		$query['fields'] = "*";
		$query['where'] = 'where symbol=\''.$this->dbcon->escape($_GET['symbol']).'\'';
		$row = $this->dbcon->query($query);
		if(!$row) {
			errMsg('종목을 찾을 수 없습니다.');
		}

		// 매니저 잔액
		$row['manager_wallet_balance'] = $row['manager_userno'] ? $this->dbcon->query_one("SELECT confirmed FROM js_exchange_wallet WHERE userno='".$this->dbcon->escape($row['manager_userno'])."' AND symbol='".$this->dbcon->escape($_GET['symbol'])."' ") : '';
		// // 거래소 잔액
		$row['walletmanager_wallet_balance'] = $this->dbcon->query_one("SELECT confirmed FROM js_exchange_wallet WHERE userno='2' AND symbol='".$this->dbcon->escape($_GET['symbol'])."' ") ;
		$this->assign_default_value($row);
		
		// NFT 경매 상품 정보 추가
		$auction_goods_info = $this->get_auction_goods_info($row['symbol']);
		$this->tpl->assign('auction_goods_info',$auction_goods_info);

        $query = "SELECT goods_grade FROM js_trade_price WHERE symbol='".$this->dbcon->escape($_GET['symbol'])."' and goods_grade !='' ";
        $r = $this->dbcon->query_list_array($query);

        $this->tpl->assign('currency_grade_info', $r);

	}

	function editFinanceForm()
	{
		$query = array();
		$query['table_name'] = 'js_trade_currency_finance';
		$query['tool'] = 'select';
		$query['fields'] = "*";
		$query['where'] = 'where symbol=\''.$this->dbcon->escape($_GET['symbol']).'\' order by years desc';

		$r = $this->dbcon->query_list_array($query);
		// var_dump($r);
		$this->tpl->assign('finance_list', $r);
	}

	function write()
	{
        if (!$_POST['symbol']) {
            jsonMsg(0, '종목코드을 입력해주세요.');
        }

        $r = $this->dbcon->query_list_one("SELECT * FROM js_auction_goods WHERE idx='{$_POST['symbol']}'");
        if (count($r) <1 ) {
            jsonMsg(0, '등록 되지 않은 종목코드 입니다.');
        }

        if (!$_POST['exchange']) {
            jsonMsg(0, '마켓을 입력해주세요.');
        }

		$_POST['manager_userid'] = trim($_POST['manager_userid']);
		$_POST['symbol'] = trim($_POST['symbol']);
		$sql = "SELECT * FROM ".$this->config['table_name']." WHERE symbol='".$this->dbcon->escape($_POST['symbol'])."'";
		$item_info = $this->dbcon->query_unique_array($sql);
		$query = array();
		// s3 라이브러리 호출
		require(ROOT_DIR.'/cheditor/imageUpload/s3.php');
		$S3 = new S3;
		// icon url 정식 폴더로 이동
		if($_POST['icon_url']!=$item_info['icon_url'] && strpos($_POST['icon_url'], '.s3.')!==false && strpos($_POST['icon_url'], '/tmp/')!==false ) {
			// tmp를 이도
			$new_url = $S3->copy_tmpfile_to_s3($_POST['icon_url']);
			if($new_url) {
				$S3->delete_file_to_s3($_POST['icon_url']);
				$_POST['icon_url'] = $new_url;
				// 이전 아이콘 삭제
				$S3->delete_file_to_s3($item_info['icon_url']);
			} else {
				jsonMsg(0, '종목 아이콘을 저장하지 못했습니다.');
			}
		}
		// icon url 정식 폴더로 이동
		if($_POST['info01']!=$item_info['info01'] && strpos($_POST['info01'], '.s3.')!==false && strpos($_POST['info01'], '/tmp/')!==false) {
			// tmp를 이도
			$new_url = $S3->copy_tmpfile_to_s3($_POST['info01']);
			if($new_url) {
				$S3->delete_file_to_s3($_POST['info01']);
				$_POST['info01'] = $new_url;
				// 이전 아이콘 삭제
				$S3->delete_file_to_s3($item_info['info01']);
			} else {
				jsonMsg(0, '기준가변화 이미지를 저장하지 못했습니다.');
			}
		}
		// icon url 정식 폴더로 이동
		if($_POST['info02']!=$item_info['info02'] && strpos($_POST['info02'], '.s3.')!==false && strpos($_POST['info02'], '/tmp/')!==false) {
			$old = explode(';', $item_info['info02']); // 이전 url들
			$tmp = explode(';', $_POST['info02']); // 임시 url들 또는 이전 url들
			$new = array(); // 새로 info02 컬럼에 저장할 값들
			foreach($tmp as $img) {
				if(!in_array($img, $old) && strpos($img, '/tmp/')!==false) {
					// tmp를 이도
					$new_url = $S3->copy_tmpfile_to_s3($img);
					if($new_url) {
						$new[] = $new_url;
					} else {
						jsonMsg(0, '기업정보 이미지를 저장하지 못했습니다.');
					}
				} else {
					if($img) { // 이전 이미지 값 그대로 온경우
						$new[] = $img;
					}
				}
			}
			// 임시 이미지 삭제
			foreach($tmp as $img) {
				$S3->delete_file_to_s3($img);
			}
			// 이전 아이콘 삭제
			foreach($old as $img) {
				$S3->delete_file_to_s3($img);
			}
			if($new) {
				$_POST['info02'] = implode(';',$new);
			} else {
				$_POST['info02'] = '';
			}
		}
		// icon url 정식 폴더로 이동
		if($_POST['info03']!=$item_info['info03'] && strpos($_POST['info03'], '.s3.')!==false && strpos($_POST['info03'], '/tmp/')!==false) {
			// tmp를 이도
			$new_url = $S3->copy_tmpfile_to_s3($_POST['info03']);
			if($new_url) {
				$S3->delete_file_to_s3($_POST['info03']);
				$_POST['info03'] = $new_url;
				// 이전 아이콘 삭제
				$S3->delete_file_to_s3($item_info['info03']);
			} else {
				jsonMsg(0, '투자정보 이미지를 저장하지 못했습니다.');
			}
		}
		
		// manager_userno
		if($_POST['manager_userid']!=$item_info['manager_userid']) {
			if(trim($_POST['manager_userid']!='')) {
				$_POST['manager_userno'] = $this->dbcon->query_one("select userno from js_member where userid='".$this->dbcon->escape($_POST['manager_userid'])."'");
				if(!$_POST['manager_userno']) {
					jsonMsg(0, '판매회원 아이디를 찾지 못했습니다. 올바른 아이디를 입력해주세요.');
				}
			} else {
				$_POST['manager_userno'] = '0';
			}
		}

		if($item_info) {
			$query['tool'] = 'update';
			$query['where'] = 'where symbol=\''.$this->dbcon->escape($_POST['symbol']).'\'';
			$r = $this->bEdit($query,$_POST);
		} else {
			$query['tool'] = 'insert';
			$r = $this->bWrite($query,$_POST);
			$this->install_stock_tables($_POST['symbol'], $_POST['exchange']);
		}
		if($r) {
            $goods = $this->dbcon->query_list_object("SELECT * FROM js_auction_goods WHERE idx='{$_POST['symbol']}'");
			
			$query = "SELECT * FROM js_trade_price WHERE symbol ='{$_POST['symbol']}'";
			
			// 초기 가격 저장하기 -> 변경
			/*if(! $item_info) {
				$query = "INSERT INTO js_trade_price SET symbol='".$this->dbcon->escape($_POST['symbol'])."', exchange='".$this->dbcon->escape($_POST['exchange'])."', volume='0', price_high='".$this->dbcon->escape($_POST['price'])."', price_low='".$this->dbcon->escape($_POST['price'])."', price_open='".$this->dbcon->escape($_POST['price'])."', price_close='".$this->dbcon->escape($_POST['price'])."', price_chagne_percent='0', volume_12='0', price_high_12='".$this->dbcon->escape($_POST['price'])."', price_low_12='".$this->dbcon->escape($_POST['price'])."', price_open_12='".$this->dbcon->escape($_POST['price'])."', price_close_12='".$this->dbcon->escape($_POST['price'])."', price_chagne_percent_12='0', volume_1='0', price_high_1='".$this->dbcon->escape($_POST['price'])."', price_low_1='".$this->dbcon->escape($_POST['price'])."', price_open_1='".$this->dbcon->escape($_POST['price'])."', price_close_1='".$this->dbcon->escape($_POST['price'])."', goods_grade='".$this->dbcon->escape($goods[0]->goods_grade)."' , price_chagne_percent_1='0'";
				jsonMsg(0, '이게 맞나요???'.$query.'///'.$item_info);
				$this->dbcon->query($query);
			}*/
			//jsonMsg(0, '이게 맞나요???'.$query.'///'.$this->query($query));
			if(mysqli_num_rows($query) == 0) {
				$query = "INSERT INTO js_trade_price SET symbol='{$_POST['symbol']}', exchange='".$this->dbcon->escape($_POST['exchange'])."', volume='0', price_high='".$this->dbcon->escape($_POST['price'])."', price_low='".$this->dbcon->escape($_POST['price'])."', price_open='".$this->dbcon->escape($_POST['price'])."', price_close='".$this->dbcon->escape($_POST['price'])."', price_chagne_percent='0', volume_12='0', price_high_12='".$this->dbcon->escape($_POST['price'])."', price_low_12='".$this->dbcon->escape($_POST['price'])."', price_open_12='".$this->dbcon->escape($_POST['price'])."', price_close_12='".$this->dbcon->escape($_POST['price'])."', price_chagne_percent_12='0', volume_1='0', price_high_1='".$this->dbcon->escape($_POST['price'])."', price_low_1='".$this->dbcon->escape($_POST['price'])."', price_open_1='".$this->dbcon->escape($_POST['price'])."', price_close_1='".$this->dbcon->escape($_POST['price'])."', goods_grade='".$this->dbcon->escape($goods[0]->goods_grade)."' , price_chagne_percent_1='0'";
				$this->dbcon->query($query);
				
				$exchange = $this->dbcon->escape($_POST['exchange']);
				$symbol = $this->dbcon->escape($_POST['symbol']);
				$table_chart = 'js_trade_'.strtolower($symbol).strtolower($exchange).'_chart';
				$price = $this->dbcon->escape($_POST['price']);
				$grade = $this->dbcon->escape($goods[0]->goods_grade);
				
				$sql_chart = "INSERT INTO `kkikda`.`".$table_chart."` (`term`, `date`, `goods_grade`, `open`, `high`, `low`, `close`, `volume`) VALUES ";
				$sql_chart .= "('1m', '2023-03-16 07:00:00', '".$grade."', '".$price."', '".$price."', '".$price."', '".$price."', '0'),";
				$sql_chart .= "('1h', '2023-03-16 07:00:00', '".$grade."', '".$price."', '".$price."', '".$price."', '".$price."', '0'),";
				$sql_chart .= "('12h', '2023-03-16 12:00:00', '".$grade."', '".$price."', '".$price."', '".$price."', '".$price."', '0'),";
				$sql_chart .= "('1d', '2023-03-16 00:00:00', '".$grade."', '".$price."', '".$price."', '".$price."', '".$price."', '0');";
				
				//jsonMsg(0, '444444444'.$sql_chart.'///'.$price.'///'.$grade);
				$this->dbcon->query($sql_chart);

				//jsonMsg(0, '444444444'.$sql_chart.'///'.$item_info);
			}else{
				
				//강제 차트 만들기
				$table_txn = 'js_trade_'.strtolower($symbol).strtolower($exchange).'_txn';
				$table_chart = 'js_trade_'.strtolower($symbol).strtolower($exchange).'_chart';
				
				if ($symbol && $exchange && $tradeapi->check_table_exists($table_txn) && $tradeapi->check_table_exists($table_chart)) {
					$grades = array('S', 'A', 'B') ;
					foreach($grades as $g) {
						$tradeapi->set_current_price_data ($symbol, $exchange, $g);
						$tradeapi->gen_chanrt_data ($symbol, $exchange, $g);
					}
				}
				
				$query = "UPDATE kkikda.js_trade_price SET volume=0, price_high=".$this->dbcon->escape($_POST['price']).", price_low=".$this->dbcon->escape($_POST['price']).", price_open=".$this->dbcon->escape($_POST['price']).", price_close=".$this->dbcon->escape($_POST['price']).", price_chagne_percent=".$this->dbcon->escape($_POST['price']).", volume_12='0', price_high_12=".$this->dbcon->escape($_POST['price']).", price_low_12=".$this->dbcon->escape($_POST['price']).", price_open_12=".$this->dbcon->escape($_POST['price']).", price_close_12=".$this->dbcon->escape($_POST['price']).", price_chagne_percent_12=".$this->dbcon->escape($_POST['price']).", volume_1='0', price_high_1=".$this->dbcon->escape($_POST['price']).", price_low_1=".$this->dbcon->escape($_POST['price']).", price_open_1=".$this->dbcon->escape($_POST['price']).", price_close_1=".$this->dbcon->escape($_POST['price']).", price_chagne_percent_1=".$this->dbcon->escape($_POST['price'])." WHERE symbol='{$_POST['symbol']}' AND exchange='".$this->dbcon->escape($_POST['exchange'])."' AND goods_grade='".$this->dbcon->escape($goods[0]->goods_grade)."';";
				$this->dbcon->query($query);
				//jsonMsg(0, '이2321321'.$query.'///'.$item_info);
				
			}
			
			/*$query = "SELECT * FROM ".$this->config['table_name']." WHERE symbol='".$this->dbcon->escape($_POST['symbol'])."'";
			jsonMsg(0, '이2321321'.$query.'///'.$item_info);*/

            $manager_userno = 2;
            $manager_address = $this->dbcon->query_one("SELECT address FROM js_exchange_wallet WHERE userno='{$this->dbcon->escape($manager_userno)}' AND symbol='KRW'");

			// 판매회원 계정 있으면 지갑 만들기
			$manager_userid = $_POST['manager_userid'];
			$manager_userno = $_POST['manager_userno'];
			$symbol = $_POST['symbol'];
			if($manager_userid && $manager_userno) {
				// 지갑 있나?e
				$wallet = $this->dbcon->query_one("SELECT COUNT(*) FROM js_exchange_wallet WHERE userno='{$this->dbcon->escape($manager_userno)}' AND symbol='{$this->dbcon->escape($symbol)}'");
				if(!$wallet) { 
					$this->dbcon->query("INSERT INTO js_exchange_wallet SET userno='{$this->dbcon->escape($manager_userno)}',  symbol='{$this->dbcon->escape($symbol)}', address='{$this->dbcon->escape($manager_userno)}', active='Y', locked='N', bool_sell='1', bool_buy='0', bool_deposit='0', bool_withdraw='0', confirmed='0' ");
				} else {
					$this->dbcon->query("UPDATE js_exchange_wallet SET bool_sell='1', bool_buy='0', bool_deposit='0', bool_withdraw='0' WHERE userno='{$this->dbcon->escape($manager_userno)}' AND symbol='{$this->bcon->escape($symbol)}'");
				}
				// 이전 판매회원 지갑 (출금, 구매)잠금 풀기
				if($item_info['manager_userid'] && $_POST['manager_userid']!=$item_info['manager_userid']) {
					$old_manager_userid = $item_info['manager_userid'];
					$old_manager_userno = $this->dbcon->qury_one("select userno from js_member where userid='{$this->dbcon->escape($old_manager_userid)}'");
					$this->dbcon->query("UPDATE js_exchange_wallet SET bool_withdraw='1', bool_sell='1', bool_buy='1', bool_deposit='1' WHERE userno='{$this->dbcon->escape($old_manager_userno)}' AND symbol='{$this->dbcon->escape($symbol)}' ");
				}
			}

            // NFT 상품의 보유회원 지갑에 수량만큼 넣어주기 - 끽다 전용 기능
            // js_auction_goods.owner_userno 에 회원번호가 있으면 해당 회원번호의 지갑(js_exchange_wallet)에 수량만큼 잔액을 넣어줍니다. 왜? 이미 보유하고 있는거니까. 판매하라고 ... 안 넣어주면 판매를 못하니까.

            $amount = 1;

            if ($goods[0]->owner_userno) {


                if ($goods[0]->pack_info == "Y") {
                    $owner_info = $this->get_member_by_userno($goods[0]->owner_userno);
                    $inventorys = $this->dbcon->query_list_object("SELECT * FROM js_auction_goods WHERE pack_info='{$goods[0]->idx}'");

                    $amount = count($inventorys);

                    $owner_userno = $owner_info->userno;
                    $owner_userid = $owner_info->userid;

                    foreach ($inventorys as $inventory) {
                        $this->dbcon->query("UPDATE js_auction_inventory SET userno='{$owner_userno}', userid='{$owner_userid}' WHERE goods_idx='{$inventory->idx}'");
                    }
                }

                $this->dbcon->query("INSERT INTO js_exchange_wallet SET userno='{$this->dbcon->escape($goods[0]->owner_userno)}',  symbol='{$this->dbcon->escape($_POST['symbol'])}', address='{$this->dbcon->escape($goods[0]->owner_userno)}', goods_grade='{$this->dbcon->escape($goods[0]->goods_grade)}', active='Y', locked='N', bool_sell='1', bool_buy='1', bool_deposit='1', bool_withdraw='1', confirmed='{$amount}}' ");
                $this->dbcon->query("INSERT INTO js_exchange_wallet_txn SET userno='{$this->dbcon->escape($goods[0]->owner_userno)}', symbol='{$this->dbcon->escape($_POST['symbol'])}', address='{$this->dbcon->escape($goods[0]->owner_userno)}', regdate=NOW(), txndate=NOW(), address_relative='{$manager_address}', txn_type='S',  direction='I', nft_id='', amount='{$amount}', fee='0', fee_relative='0', tax='0', status='D', service_name='', key_relative='', txn_method='COIN', app_no='',  msg=''  ");

                foreach ($inventorys as $inventory) {
                    $this->dbcon->query("INSERT INTO js_exchange_wallet_nft SET symbol='{$this->dbcon->escape($_POST['symbol'])}', tokenid='{$inventory->idx}',  userno='{$this->dbcon->escape($goods[0]->owner_userno)}', amount='1', reg_date=NOW() ");
                }

            }

			jsonMsg(1);
		} else {
			jsonMsg(0);
		}
	}

	function del()
	{
		if(empty($_POST['symbol'])) {
			if($this->config_basic['bool_ssl'] > 0) {
				errMsg(Lang::main_coin2);
			}
			else {
				jsonMsg(0,Lang::main_coin2);
			}
		}

		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'delete';
		$query['where'] = 'where symbol=\''.$_POST['symbol'].'\'';
		if($this->dbcon->query($query,__FILE__,__LINE__)) {
			$this->remove_stock_tables($_POST['symbol'], $_POST['exchange']);
			jsonMsg(1);
		} else {
			jsonMsg(0);
		}
	}

	function finance_delete()
	{
		if(empty($_POST['symbol'])) {
			if($this->config_basic['bool_ssl'] > 0) {errMsg(Lang::main_coin2);}
			else {jsonMsg(0,Lang::main_coin2);}
		}
		if(empty($_POST['years'])) {
			if($this->config_basic['bool_ssl'] > 0) {errMsg('년도값이 필요합니다.');}
			else {jsonMsg(0,'년도값이 필요합니다.');}
		}

		$query = "DELETE FROM js_trade_currency_finance WHERE symbol='".$this->dbcon->escape($_POST['symbol'])."' AND years='".$this->dbcon->escape($_POST['years'])."' ";
		if($this->dbcon->query($query,__FILE__,__LINE__)) {
			jsonMsg(1);
		} else {
			jsonMsg(0);
		}
	}
	function finance_update()
	{
		if(empty($_POST['symbol'])) {
			if($this->config_basic['bool_ssl'] > 0) {errMsg(Lang::main_coin2);}
			else {jsonMsg(0,Lang::main_coin2);}
		}
		if(empty($_POST['years'])) {
			if($this->config_basic['bool_ssl'] > 0) {errMsg('년도값이 필요합니다.');}
			else {jsonMsg(0,'년도값이 필요합니다.');}
		}

		$query = "UPDATE js_trade_currency_finance SET ";
		$query.= " sales='".$this->dbcon->escape($_POST['sales'])."' ";
		$query.= ", opt_profit='".$this->dbcon->escape($_POST['opt_profit'])."' ";
		$query.= ", net_profit='".$this->dbcon->escape($_POST['net_profit'])."' ";
		$query.= ", assets='".$this->dbcon->escape($_POST['assets'])."' ";
		$query.= ", debt='".$this->dbcon->escape($_POST['debt'])."' ";
		$query.= ", equity='".$this->dbcon->escape($_POST['equity'])."' ";
		$query.= ", debt_ratio='".$this->dbcon->escape($_POST['debt_ratio'])."' ";
		$query.= ", roe='".$this->dbcon->escape($_POST['roe'])."' ";
		$query.= "WHERE symbol='".$this->dbcon->escape($_POST['symbol'])."' AND years='".$this->dbcon->escape($_POST['years'])."' ";
		if($this->dbcon->query($query,__FILE__,__LINE__)) {
			jsonMsg(1);
		} else {
			jsonMsg(0);
		}
	}
	
	/**
	 * active 숨김/표시 처리, best_item 처리
	 */
	function confirm ( $type, $value, $idx ) {
		
		if(! in_array($type, array('active', 'creatable','crypto_currency','menu','check_deposit', 'display_grade'))) {
			return false;
		}
		$sql = "update {$this->config['table_name']} set ";
		switch($type) {
			case 'active':
				$sql .= " active = '". ($value=='Y' ? 'Y' : 'N') . "'" ;
				break;
			case 'creatable':
				$sql .= " creatable = '". ($value=='Y' ? 'Y' : 'N') . "'" ;
				break;
			case 'crypto_currency':
				$sql .= " crypto_currency = '". ($value=='Y' ? 'Y' : 'N') . "'" ;
				break;
			case 'menu':
				$sql .= " menu = '". ($value=='Y' ? 'Y' : 'N') . "'" ;
				break;
			case 'check_deposit':
				$sql .= " check_deposit = '". ($value=='Y' ? 'Y' : 'N') . "'" ;
				break;
            case 'display_grade':
                $sql .= " display_grade = '". $value . "'" ;
                break;
		}
		$sql .= " where symbol = '{$idx}' ";
		$r = $this->dbcon->query($sql);
		if($r) {
			jsonMsg(1); // 변경 성공
		} else {
			jsonMsg(0); // 변경 실패
		}
	}

	function finance_insert()
	{
		if(empty($_POST['symbol'])) {
			if($this->config_basic['bool_ssl'] > 0) {errMsg(Lang::main_coin2);}
			else {jsonMsg(0,Lang::main_coin2);}
		}
		if(empty($_POST['years'])) {
			if($this->config_basic['bool_ssl'] > 0) {errMsg('년도값이 필요합니다.');}
			else {jsonMsg(0,'년도값이 필요합니다.');}
		}
		// 중복 확인
		$prev = $this->dbcon->query_one("SELECT COUNT(*) FROM js_trade_currency_finance WHERE symbol='".$this->dbcon->escape($_POST['symbol'])."' AND years='".$this->dbcon->escape($_POST['years'])."'");
		if($prev) {
			jsonMsg(0,'이미 저장된 년도입니다.');
		}


		$query = "INSERT INTO js_trade_currency_finance SET symbol='".$this->dbcon->escape($_POST['symbol'])."' ";
		$query.= ", years='".$this->dbcon->escape($_POST['years'])."' ";
		$query.= ", sales='".$this->dbcon->escape($_POST['sales'])."' ";
		$query.= ", opt_profit='".$this->dbcon->escape($_POST['opt_profit'])."' ";
		$query.= ", net_profit='".$this->dbcon->escape($_POST['net_profit'])."' ";
		$query.= ", assets='".$this->dbcon->escape($_POST['assets'])."' ";
		$query.= ", debt='".$this->dbcon->escape($_POST['debt'])."' ";
		$query.= ", equity='".$this->dbcon->escape($_POST['equity'])."' ";
		$query.= ", debt_ratio='".$this->dbcon->escape($_POST['debt_ratio'])."' ";
		$query.= ", roe='".$this->dbcon->escape($_POST['roe'])."' ";
		if($this->dbcon->query($query,__FILE__,__LINE__)) {
			jsonMsg(1);
		} else {
			jsonMsg(0);
		}
	}
	public function get_auction_goods_info($auction_goods_idx) {
		return $this->dbcon->query_fetch_array("SELECT * FROM js_auction_goods WHERE idx='{$this->dbcon->escape($auction_goods_idx)}'");
	}
	public function assign_auction_goods_info($auction_goods_idx) {
		if($auction_goods_idx) {
			$auction_goods_info = $this->get_auction_goods_info($auction_goods_idx);
			$this->tpl->assign('auction_goods_info', $auction_goods_info);
			// 기본값에 아래 내용 추가
			$currency_info = array(
				'icon_url'=>$auction_goods_info['main_pic']
				,'name'=>$auction_goods_info['title']
				,'symbol'=>$auction_goods_info['idx']
				,'price'=>$auction_goods_info['base_price']
				,'exchange'=>'KRW'
				,'check_deposit'=>'N'
				,'menu'=>'Y'
				,'crypto_currency'=>'N'
				,'creatable'=>'N'
			);

            $query = "SELECT goods_grade FROM js_trade_price WHERE symbol='".$this->dbcon->escape($auction_goods_idx)."' and goods_grade !='' ";

            $r = $this->dbcon->query_list_array($query);

            if (!$r) {
                $r[0]['goods_grade'] = $auction_goods_info['goods_grade'];
            }

            $this->tpl->assign('currency_grade_info', $r);
            
			return $currency_info;
		}
	}
	public function assign_default_value($val='') {
		$default_value = array(
			'symbol'=>'',
			'exchange'=>'KRW',
			'name'=>'',
			'fee_in'=>'0',
			'tax_in_ratio'=>'0',
			'fee_out'=>'0.01',
			'tax_out_ratio'=>'0',
			'fee_buy_ratio'=>'0',
			'tax_buy_ratio'=>'0',
			'fee_sell_ratio'=>'0.003',
			'tax_sell_ratio'=>'0',
			'tax_income_ratio'=>'0',
			'display_decimals'=>'8',
			'trade_min_volume'=>'0.0001',
			'trade_max_volume'=>'',
			'out_min_volume'=>'1',
			'out_max_volume'=>'',
			'active'=>'N',
			'crypto_currency'=>'Y',
			'creatable'=>'Y',
			'check_deposit'=>'N',
			'menu'=>'N',
			'sortno'=>'99',
			'circulating_supply'=>'',
			'max_supply'=>'',
			'price'=>'',
			'color'=>'',
			'backup_address'=>'',
			'transaction_outlink'=>'',
			'info01'=>'',
			'info02'=>'',
			'info03'=>'',
            'diplay_grade'=>'A',
		);
		if($val) {
			$default_value = array_merge($default_value, $val);
		}
		$this->tpl->assign('currency_info', $default_value);
	}

	public function get_currency_list() {
		$sql = "SELECT * FROM js_trade_currency ";
		if($_GET['searchval']) {
			$sql .= " WHERE symbol LIKE '%".$this->dbcon->escape($_GET['searchval'])."%' OR name LIKE '%".$this->dbcon->escape($_GET['searchval'])."%' ";
		}
		if($_GET['sort_target'] ) {
			$sql .= ' order by ';
			for($i=0;$i<count($_GET['sort_target']);$i++) {
				if($i>0) {
					$sql .= ', ';
				}
				$sql .= $_GET['sort_target'][$i].' '.$_GET['sort_method'][$i];
			}
		} else {
			$sql .= ' order by regdate desc';
		}
		return $this->dbcon->query_all_object($sql);
	}

	public function get_menu_currency() {
		return $this->dbcon->query_all_array("SELECT * FROM js_trade_currency WHERE active='Y' AND menu='Y' AND symbol<>exchange ORDER BY sortno ");
	}

	public function check_duplicate_trade_currency($symbol) {
		$duplicate = $this->dbcon->query_one("select symbol from js_trade_currency where symbol='{$this->dbcon->escape($symbol)}'");
		return !!$duplicate ;
	}
	

	public function create_manager() {
		// $symbol = trim($_POST['symbol']);
		// if(!$symbol) {jsonMsg(0, '종목코드가 필요합니다.');}
		$userid = trim($_POST['userid']);
		if(!$userid) {jsonMsg(0, '판매회원 아이디를 입력해주세요.');}
		$nickname = trim($_POST['nickname']);
		if(!$nickname) {jsonMsg(0, '판매회원 닉네임을 입력해주세요.');}
		$userpw = trim($_POST['userpw']);
		if(!$userpw) {jsonMsg(0, '판매회원 비밀번호를 입력해주세요.');}
		$userpw_hash = md5($userpw);
		$duplicated = $this->dbcon->query_one("select userid from js_member where userid='{$this->dbcon->escape($userid)}'");
		if($duplicated) {jsonMsg(0, '이미 가입된 아이디입니다.');}
		// js_member
		$r = $this->dbcon->query("INSERT INTO js_member SET userid='{$this->dbcon->escape($userid)}', name='{$this->dbcon->escape($userid)}', nickname='{$this->dbcon->escape($nickname)}', userpw='{$this->dbcon->escape($userpw_hash)}', pin='{$this->dbcon->escape($userpw_hash)}', bool_confirm_email='1', regdate=UNIX_TIMESTAMP() ");
		if($r) {
			// $manager_userno = $this->dbcon->mysqli_insert_id($this->dbcon->connect);
			// $r = $this->dbcon->query("UPDATE js_trade_currency SET manager_userid='{$this->dbcon->escape($userid)}', manager_userno='{$this->dbcon->escape($manager_userno)}' WHERE symbol='{$this->dbcon->escape($symbol)}', regdate=UNIX_TIMESTAMP() ");
			// 판매자 아이디를 자동으로 저장하려했지만 판매자 아이디가 바뀔때 설정값을 바꾸는 로직이 있어서 여기서 바꾸지 않고 종목 정보 저장할때 처리하도록 안내함.
			jsonMsg(1, '종목 정보를 저장해야 판매회원으로 설정됩니다. 종목 정보를 저장해 주세요.');
		} else {
			jsonMsg(0, '시스템 오류가 발생했습니다.');
		}
	}

	public function add_krw_to_seller() {
		$userid = trim($_POST['userid']);
		if(!$userid) {jsonMsg(0, '판매회원 아이디를 입력해주세요.');}
		$symbol = trim($_POST['symbol']);
		if(!$symbol) {jsonMsg(0, '종목코드를 입력해주세요.');}
		$amount = preg_replace('/[^0-9.]/','',$_POST['amount']);
		if(!$amount) {jsonMsg(0, '판매회원에게 지급할 수량을 올바르게 입력해주세요.');}

		$seller_userno = $this->dbcon->query_one("SELECT userno FROM js_member WHERE userid='{$this->dbcon->escape($userid)}'");
		if(! $seller_userno) {jsonMsg(0, '판매회원정보를 찾지 못했습니다. 아이디를 확인해주세요');}
		$seller_wallet = $this->dbcon->query_one("SELECT * FROM js_exchange_wallet WHERE userno='{$this->dbcon->escape($seller_userno)}' AND symbol='{$this->dbcon->escape($symbol)}' ");
		if($seller_wallet) {
			$r = $this->dbcon->query("UPDATE js_exchange_wallet SET confirmed=confirmed+{$this->dbcon->escape($amount)} WHERE userno='{$this->dbcon->escape($seller_userno)}' AND symbol='{$this->dbcon->escape($symbol)}' ");
		} else {
			$r = $this->dbcon->query("INSERT INTO js_exchange_wallet SET confirmed={$this->dbcon->escape($amount)}, userno='{$this->dbcon->escape($seller_userno)}', symbol='{$this->dbcon->escape($symbol)}', regdate=NOW() ");
		}
		if($r) {
			$this->dbcon->query("INSERT INTO js_exchange_wallet_txn SET userno='{$this->dbcon->escape($seller_userno)}', symbol='{$this->dbcon->escape($symbol)}', regdate=NOW(), txndate=NOW(), txn_type='S', direction='I', amount='{$this->dbcon->escape($amount)}', fee='0', fee_relative='', tax='0', status='D', key_relative='', txn_method='', msg='관리자가 판매자에게 지급' ");
			jsonMsg(1, '');
		} else {
			jsonMsg(0, '시스템 오류가 발생했습니다.');
		}
	}
	
	/**
	 * 트랜젝션 리스트 데이터를 추출합니다.
	 * @param number 페이징용으로 사용 할 이전페이지 마지막 줄의 txnid. 첫페이지는 빈값을 사용합니다.
	 * @param number 추출 개수입니다. 기본값 20개입니다.
	 */
	public function get_txn_list($txnid='', $start=0, $rows=20) {
		$sql = "SELECT txnid, symbol, `address`, regdate, txndate, address_relative, amount, fee, key_relative,
		CASE WHEN txn_type='I' THEN 'Deposit' WHEN txn_type='O' THEN 'Withdraw' END AS `type`,
		CASE WHEN `status`='D' THEN 'Success' WHEN `status`='O' THEN 'Waiting' WHEN `status`='P' THEN 'Processing' WHEN `status`='C' THEN 'Cancel' END AS `status`
		FROM js_exchange_wallet_txn WHERE txn_type IN ('I','O') ";
		$start = preg_replace('/[^0-9]/g', '', $start);
		$start = $start ? $start : 0;
		if($txnid) {
			$sql .=" AND `txnid` < '{$this->dbcon->escape($txnid)}' ";
			$start = 0;
		}
		$sql .=" ORDER BY `txnid` DESC LIMIT {$start}, {$this->dbcon->escape($rows)} ";
		return $this->dbcon->query_all_object($sql);
	}

	public function get_wallet_by_address($address) {
		$sql = "SELECT * from js_exchange_wallet where address='{$this->dbcon->escape($address)}' limit 1 ";
		return $this->dbcon->query_unique_object($sql);
	}
	public function get_member_by_userno($userno) {
		$sql = "SELECT * from js_member where userno='{$this->dbcon->escape($userno)}' limit 1 ";
		return $this->dbcon->query_unique_object($sql);
	}
	/**
	 * 트랜젝션 리스트 데이터를 JSON 형식으로 출력합니다.
	 * @param number 페이징용으로 사용 할 이전페이지 마지막 줄의 txnid. 첫페이지는 빈값을 사용합니다.
	 * @param number 추출 개수입니다. 기본값 20개입니다.
	 */
	public function print_txn_list($txnid='', $start=0, $rows=20) {
		$r = $this->get_txn_list($txnid, $start, $rows);
		for($i=0; $i<count($r); $i++) {
			$wallet = $this->get_wallet_by_address($r[$i]->address);
			$member = $this->get_member_by_userno($wallet->userno);
			$r[$i]->userid = $member->userid;
			$r[$i]->name = $member->name;
		}
		echo $r ? json_encode( $r ) : '[]';
	}

	public function install_stock_tables($symbol, $exchange) {
		$symbol = strtolower($symbol);
		$exchange = strtolower($exchange);
		$sql = "CREATE TABLE `js_trade_{$symbol}{$exchange}_chart` (
			`term` char(3) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Chart period. ex) 1m: 1 minute, 1h: 1hour, 1d: 1day, 1w: 1week, 1M: 1month',
			`date` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'date',
			`goods_grade` char(1) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '상품 등급 (S,A,B)',
			`open` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '0' COMMENT 'open price',
			`high` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '0' COMMENT 'high price',
			`low` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '0' COMMENT 'low price',
			`close` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '0' COMMENT 'close price',
			`volume` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '0' COMMENT 'trade volume',
			PRIMARY KEY (`term`,`date`,`goods_grade`)
		  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPRESSED";
		$this->dbcon->query($sql);
		$sql = "CREATE TABLE `js_trade_{$symbol}{$exchange}_order` (
			`orderid` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'order id',
			`userno` int NOT NULL COMMENT '회원번호',
			`address` varchar(1000) DEFAULT NULL COMMENT '회원의지갑주소',
			`time_order` datetime NOT NULL COMMENT '등록날짜.',
			`trading_type` char(1) NOT NULL COMMENT '매매 종류. B: 매수, S:매도',
			`price` decimal(20,4) NOT NULL COMMENT '호가',
			`volume` decimal(20,4) DEFAULT NULL COMMENT '수량',
			`volume_remain` decimal(20,4) DEFAULT NULL COMMENT '남은 수량',
			`amount` decimal(20,2) DEFAULT NULL COMMENT '거래대금. price * volume',
			`status` char(1) DEFAULT 'O' COMMENT '매매 상태. O: 대기중, C: 완료, T: 매매중, D: 삭제(취소)',
			`goods_grade` char(1) NOT NULL DEFAULT '' COMMENT '상품 등급 (S,A,B)',
			`time_traded` datetime DEFAULT NULL COMMENT '마지막 체결 날짜.',
			PRIMARY KEY (`orderid`),
			KEY `userno` (`userno`),
			KEY `price` (`price`),
			KEY `status` (`status`)
		  ) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPRESSED COMMENT='BTC 매수매도 주문 정보'";
		$this->dbcon->query($sql);
		$sql = "CREATE TABLE `js_trade_{$symbol}{$exchange}_ordertxn` (
			`userno` bigint NOT NULL COMMENT '회원번호',
			`orderid` bigint NOT NULL COMMENT '주문번호',
			`txnid` bigint NOT NULL COMMENT '거래번호',
			`goods_grade` char(1) NOT NULL DEFAULT '' COMMENT '상품 등급 (S,A,B)',
			KEY `userno` (`userno`)
		  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPRESSED COMMENT='회원별 주문번호, 매매번호를 연결한 인덱스용 테이블'";
		$this->dbcon->query($sql);
		$sql = "CREATE TABLE `js_trade_{$symbol}{$exchange}_quote` (
			`price` decimal(20,4) NOT NULL COMMENT '호가',
			`volume` decimal(20,4) DEFAULT NULL COMMENT 'volume',
			`trading_type` char(1) DEFAULT NULL COMMENT 'trading type. B: buy, S: sell',
			`goods_grade` char(1) NOT NULL DEFAULT '' COMMENT '상품 등급 (S,A,B)',
			PRIMARY KEY (`price`)
		  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPRESSED";
		$this->dbcon->query($sql);
		$sql = "CREATE TABLE `js_trade_{$symbol}{$exchange}_txn` (
			`txnid` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'transaction id.',
			`time_traded` datetime DEFAULT NULL COMMENT '체결날짜. 검색을 위해 datetime으로 변경',
			`volume` decimal(20,4) DEFAULT NULL COMMENT '거래량',
			`price` decimal(20,2) NOT NULL COMMENT '가격',
			`goods_grade` char(1) NOT NULL DEFAULT '' COMMENT '상품 등급 (S,A,B)',
			`orderid_buy` char(32) DEFAULT NULL COMMENT '구매자주문아이디',
			`orderid_sell` char(32) DEFAULT NULL COMMENT '판매자주문아이디',
			`fee` decimal(20,2) DEFAULT NULL COMMENT '거래수수료',
			`tax_transaction` decimal(20,2) DEFAULT NULL COMMENT '거래세',
			`tax_income` decimal(20,2) DEFAULT NULL COMMENT '양도소득세',
			`price_updown` char(1) NOT NULL DEFAULT '-' COMMENT '가격 상승/하락/보합여부. - : 보합, U:상승, D: 하락',
			PRIMARY KEY (`txnid`),
			KEY `time_traded` (`time_traded`)
		  ) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPRESSED COMMENT='BTC-USD 거래내역. '";
		$this->dbcon->query($sql);
	}
	public function remove_stock_tables($symbol, $exchange) {
		$symbol = strtolower($symbol);
		$exchange = strtolower($exchange);
		$this->dbcon->query("DROP TABLE `js_trade_{$symbol}{$exchange}_chart` ");
		$this->dbcon->query("DROP TABLE `js_trade_{$symbol}{$exchange}_order`");
		$this->dbcon->query("DROP TABLE `js_trade_{$symbol}{$exchange}_ordertxn`");
		$this->dbcon->query("DROP TABLE `js_trade_{$symbol}{$exchange}_quote`");
		$this->dbcon->query("DROP TABLE `js_trade_{$symbol}{$exchange}_txn`");
		$this->dbcon->query("DELETE FROM js_trade_currency_finance WHERE symbol='{$this->dbcon->escape($symbol)}' ");
	}
	// public function get_old_member_balance() {
	// 	$symbol = urldecode($_REQUEST['symbol']); // 혹시 한글이 들어오더라도 작동되도록 수정
	// 	$sql = "SELECT IFNULL(c.name, '') name, p.nickname, p.symbol, IFNULL(p.userno, '') userno, p.balance, IFNULL(m.userid, '') userid FROM js_exchange_wallet_previous p LEFT JOIN js_member m ON p.userno=m.userno LEFT JOIN js_trade_currency c ON p.symbol=c.symbol WHERE p.symbol='{$this->dbcon->escape($symbol)}' ";
	// 	$list = $this->dbcon->query_list_object($sql);
	// 	$r = array('payload'=>$list, 'success'=>true
	// 		// , 'query'=>$sql
	// 	);
	// 	exit(json_encode($r));
	// }
	public function get_old_member_airdrop() {
		$symbol = urldecode($_REQUEST['symbol']); // 혹시 한글이 들어오더라도 작동되도록 수정
		$sql = "SELECT IFNULL(c.name, '') name, p.nickname, p.old_username, p.old_userid, p.symbol, IFNULL(p.userno, '') userno, p.volumn, p.price, p.amount, p.lockup_date, p.lockup_day, p.regdate, IFNULL(m.userid, '') userid FROM js_trade_airdrop p LEFT JOIN js_member m ON p.userno=m.userno LEFT JOIN js_trade_currency c ON p.symbol=c.symbol WHERE p.symbol='{$this->dbcon->escape($symbol)}' ";
		$list = $this->dbcon->query_list_object($sql);
		$r = array('payload'=>$list, 'success'=>true);
		exit(json_encode($r));
	}
	// public function write_old_stock_data() {
	// 	$symbol = trim($_REQUEST['symbol']);
	// 	if(!$symbol) {
	// 		exit(json_encode(array('success'=>false, 'error'=>array('code'=>'001', 'message'=>'symbol 값이 필요합니다.'))));
	// 	}
	// 	// var_dump($_FILES); 
	// 	if(strpos($_FILES['old_stock_data']['name'], '.xlsx')===false && strpos($_FILES['old_stock_data']['name'], '.xls')===false) {
	// 		exit(json_encode(array('success'=>false, 'error'=>array('code'=>'002', 'message'=>'엑셀 파일(xlsx, xls)만 지원합니다.'))));
	// 	}
	// 	$stocks = array();
	// 	$sql = array();
	// 	if(preg_match('/\.xlsx$/', $_FILES['old_stock_data']['name'])) {
	// 		// exit('xlsx');
	// 		require ROOT_DIR.'/lib/XLSXReader.php';
	// 		$xlsx = new XLSXReader($_FILES['old_stock_data']['tmp_name']);
	// 		$sheets = $xlsx->getSheetNames(); // 배열키 1부터 시작 array(1) { [1]=> string(5) "Excel" }
	// 		$data = $xlsx->getSheetData($sheets[1]); 
	// 		$cnt_row = count($data);// row 배열키 0부터 시작
	// 		// $cnt_col = count($data[0]);// col 배열키 0부터 시작
	// 		for($i=0; $i<$cnt_row ; $i++) {
	// 			$row = $data[$i];
	// 			// var_dump($row); exit;
	// 			if($row[0] && $i>1) {
	// 				$_name = trim($row[0]); // 이름
	// 				$_nickname = trim($row[1]); // 닉네임
	// 				$_phone = trim($row[2]); // 전화번호
	// 				$_balance_wallet = preg_replace('/[^0-9.]/','', $row[3]); // 지갑(보유)
	// 				$_balance_admin = preg_replace('/[^0-9.]/','', $row[4]); // 거래소(보유)
	// 				$_balance = preg_replace('/[^0-9.]/','', $row[5]); // 총주식(보유)
	// 				// `symbol`, `nickname`, `balance`, `name`, `phone`, `balance_wallet`, `balance_admin`
	// 				$sql[] = "('{$this->dbcon->escape($symbol)}','{$this->dbcon->escape($_nickname)}','{$this->dbcon->escape($_balance)}','{$this->dbcon->escape($_name)}','{$this->dbcon->escape($_phone)}','{$this->dbcon->escape($_balance_wallet)}','{$this->dbcon->escape($_balance_admin)}' )";
	// 			}
	// 		}
	// 	}
	// 	if(preg_match('/\.xls$/', $_FILES['old_stock_data']['name'])) {
	// 		// exit('xls');
	// 		require ROOT_DIR.'/lib/excel_reader2.php';
	// 		$data = new Spreadsheet_Excel_Reader($_FILES['old_stock_data']['tmp_name'],false);
	// 		$cnt_row = $data->rowcount($sheet_index=0); // row 배열키 1부터 시작함.
	// 		$cnt_col = $data->colcount($sheet_index=0); // col 배열키 1부터 시작함.
	// 		for($row=3 ; $row<$cnt_row ; $row++) { // 1은 빈줄, 2은 제목줄
	// 			$_name = trim($data->val($row,0)); // 이름
	// 			$_nickname = trim($data->val($row,1)); // 닉네임
	// 			$_phone = trim($data->val($row,2)); // 전화번호
	// 			$_balance_wallet = preg_replace('/[^0-9.]/','', $data->val($row,3)); // 지갑(보유)
	// 			$_balance_admin = preg_replace('/[^0-9.]/','', $data->val($row,4)); // 거래소(보유)
	// 			$_balance = preg_replace('/[^0-9.]/','', $data->val($row,5)); // 총주식(보유)
	// 			// `symbol`, `nickname`, `balance`, `name`, `phone`, `balance_wallet`, `balance_admin`
	// 			$sql[] = "('{$this->dbcon->escape($symbol)}','{$this->dbcon->escape($_nickname)}','{$this->dbcon->escape($_balance)}','{$this->dbcon->escape($_name)}','{$this->dbcon->escape($_phone)}','{$this->dbcon->escape($_balance_wallet)}','{$this->dbcon->escape($_balance_admin)}' )";
	// 		}
	// 	}
	// 	// delete previous data
	// 	$this->dbcon->query("DELETE FROM js_exchange_wallet_previous WHERE symbol='{$this->dbcon->escape($symbol)}' ");
	// 	$sql = "INSERT IGNORE INTO js_exchange_wallet_previous (`symbol`, `nickname`, `balance`, `name`, `phone`, `balance_wallet`, `balance_admin`) VALUES ".implode(',', $sql);
	// 	$r = $this->dbcon->query($sql);
	// 	$r = array('success'=>true
	// 		, 'query'=>$sql
	// 	);
	// 	exit(json_encode($r));
	// }
	// public function write_old_airdrop_data() {
	// 	$symbol = trim($_REQUEST['symbol']);
	// 	if(!$symbol) {
	// 		exit(json_encode(array('success'=>false, 'error'=>array('code'=>'001', 'message'=>'symbol 값이 필요합니다.'))));
	// 	}
	// 	// var_dump($_FILES); 
	// 	if(strpos($_FILES['old_airdrop_data']['name'], '.xlsx')===false && strpos($_FILES['old_airdrop_data']['name'], '.xls')===false) {
	// 		exit(json_encode(array('success'=>false, 'error'=>array('code'=>'002', 'message'=>'엑셀 파일(xlsx, xls)만 지원합니다.'))));
	// 	}
	// 	$stocks = array();
	// 	$sql = array();
	// 	if(preg_match('/\.xlsx$/', $_FILES['old_airdrop_data']['name'])) {
	// 		// exit('xlsx');
	// 		require ROOT_DIR.'/lib/XLSXReader.php';
	// 		$xlsx = new XLSXReader($_FILES['old_airdrop_data']['tmp_name']);
	// 		// $xlsx->setDateTimeFormat('Y-m-d H:i:s');
	// 		$sheets = $xlsx->getSheetNames(); // 배열키 1부터 시작 array(1) { [1]=> string(5) "Excel" }
	// 		$data = $xlsx->getSheetData($sheets[1]); 
	// 		$cnt_row = count($data);// row 배열키 0부터 시작
	// 		// var_dump($data); exit;
	// 		// $cnt_col = count($data[0]);// col 배열키 0부터 시작
	// 		for($i=7; $i<$cnt_row ; $i++) {// 윗줄에 문서이름(0),법인명(1),총주식수(2),총금액(3),입금계좌(4),빈줄(5),컬럼명(6) 이 있스빈다.
	// 			$row = $data[$i];
	// 			// var_dump($row, $xlsx->toUnixTimeStamp($row[0]), date ('Y-m-d H:i:s', $xlsx->toUnixTimeStamp($row[0]))); exit;
	// 			if($row[0]) { 
	// 				$_regdate = date ('Y-m-d', $xlsx->toUnixTimeStamp($row[0])); // 날짜(주식지급날짜) 액셀용 날짜값(숫자)로 되어 있어서 unixtimestamp로 변경후 date로 변경함.
	// 				$_username = trim($row[1]); // 회원명
	// 				$_nickname = trim($row[2]); // 닉네임
	// 				$_userid = trim($row[3]); // 아이디
	// 				$_price = preg_replace('/[^0-9.]/','', $row[4]); // 주당금액
	// 				$_volumn = preg_replace('/[^0-9.]/','', $row[5]); // 주식수
	// 				$_amount = preg_replace('/[^0-9.]/','', $row[6]); // 합계(원)	
	// 				$_lockup_day = trim($row[8]); // 보유기간(일)
	// 				$_lockup_date = date ('Y-m-d', $xlsx->toUnixTimeStamp($row[9])); // 보유날짜

	// 				$sql[] = "('{$this->dbcon->escape($symbol)}','{$this->dbcon->escape($_nickname)}','{$this->dbcon->escape($_username)}','{$this->dbcon->escape($_userid)}','{$this->dbcon->escape($_volumn)}','{$this->dbcon->escape($_price)}','{$this->dbcon->escape($_amount)}','{$this->dbcon->escape($_lockup_date)}','{$this->dbcon->escape($_lockup_day)}','{$this->dbcon->escape($_regdate)}' )";
	// 			}
	// 		}
	// 	}
	// 	// var_dump($sql); exit;
	// 	if(preg_match('/\.xls$/', $_FILES['old_airdrop_data']['name'])) {
	// 		// exit('xls');
	// 		require ROOT_DIR.'/lib/excel_reader2.php';
	// 		$data = new Spreadsheet_Excel_Reader($_FILES['old_airdrop_data']['tmp_name'],false);
	// 		$cnt_row = $data->rowcount($sheet_index=0); // row 배열키 1부터 시작함.
	// 		$cnt_col = $data->colcount($sheet_index=0); // col 배열키 1부터 시작함.
	// 		for($row=8 ; $row<$cnt_row ; $row++) { // 1은 빈줄, 2은 제목줄
	// 			// var_dump($row, $xlsx->toUnixTimeStamp($row[0]), date ('Y-m-d H:i:s', $xlsx->toUnixTimeStamp($row[0]))); exit;

	// 			$_regdate = date('Y-m-d', strtotime(trim($data->val($row,1)))); // 날짜(주식지급날짜)  07/13/2022 형식으로 나와서 변경함.
	// 			// var_dump('_regdate:',$_regdate); exit;
	// 			$_username = trim($data->val($row,2)); // 회원명
	// 			$_nickname = trim($data->val($row,3)); // 닉네임
	// 			if(!$_nickname) { continue; } // 닉네임 없으면 미사용
	// 			$_userid = trim($data->val($row,4)); // 아이디
	// 			$_price = preg_replace('/[^0-9.]/','', $data->val($row,5)); // 주당금액
	// 			$_volumn = preg_replace('/[^0-9.]/','', $data->val($row,6)); // 주식수
	// 			if(!$_volumn) { continue; } // 주식수 없으면 미사용
	// 			$_amount = preg_replace('/[^0-9.]/','', $data->val($row,7)); // 합계(원)	
	// 			$_lockup_day = trim($data->val($row,9)); // 보유기간(일)
	// 			$_lockup_date = date('Y-m-d', strtotime(trim($data->val($row,10)))); // 보유날짜 07/13/2022 형식으로 나와서 변경함.

	// 			// `symbol`, `nickname`, `balance`, `name`, `phone`, `balance_wallet`, `balance_admin`
	// 			$sql[] = "('{$this->dbcon->escape($symbol)}','{$this->dbcon->escape($_nickname)}','{$this->dbcon->escape($_username)}','{$this->dbcon->escape($_userid)}','{$this->dbcon->escape($_volumn)}','{$this->dbcon->escape($_price)}','{$this->dbcon->escape($_amount)}','{$this->dbcon->escape($_lockup_date)}','{$this->dbcon->escape($_lockup_day)}','{$this->dbcon->escape($_regdate)}' )";
	// 			// var_dump($sql); exit;
	// 		}
	// 	}
	// 	// var_dump($sql); exit;
	// 	// delete previous data
	// 	$this->dbcon->query("DELETE FROM js_trade_airdrop WHERE symbol='{$this->dbcon->escape($symbol)}' ");
	// 	$sql = "INSERT IGNORE INTO js_trade_airdrop (`symbol`, `nickname`, `old_username`, `old_userid`, `volumn`, `price`, `amount`, `lockup_date`, `lockup_day`, `regdate`) VALUES ".implode(',', $sql);
	// 	// var_dump($sql); exit;
	// 	$r = $this->dbcon->query($sql);
	// 	// var_dump($r); exit;
	// 	$r = array('success'=>true
	// 		, 'query'=>$sql
	// 	);
	// 	exit(json_encode($r));
	// }
}

function tradeCurrencyQuery($arr)
{
	global $dbcon;
	$qry = array();
	if(!empty($arr['symbol']))  { $qry[] = 'symbol=\''.$dbcon->escape($arr['symbol']).'\''; }
    if(!empty($arr['exchange']))  { $qry[] = 'exchange=\''.$dbcon->escape($arr['exchange']).'\''; }
	if(!empty($arr['name']))  { $qry[] = 'name=\''.$dbcon->escape($arr['name']).'\''; }
	if(!empty($arr['fee_in']))  { $qry[] = 'fee_in=\''.$dbcon->escape($arr['fee_in']).'\''; }
	if(!empty($arr['tax_in_ratio']))  { $qry[] = 'tax_in_ratio=\''.$dbcon->escape($arr['tax_in_ratio']).'\''; }
	if(!empty($arr['fee_out']))  { $qry[] = 'fee_out=\''.$dbcon->escape($arr['fee_out']).'\''; }
	if(!empty($arr['tax_out_ratio']))  { $qry[] = 'tax_out_ratio=\''.$dbcon->escape($arr['tax_out_ratio']).'\''; }
	if(!empty($arr['fee_buy_ratio']))  { $qry[] = 'fee_buy_ratio=\''.$dbcon->escape($arr['fee_buy_ratio']).'\''; }
	if(!empty($arr['tax_buy_ratio']))  { $qry[] = 'tax_buy_ratio=\''.$dbcon->escape($arr['tax_buy_ratio']).'\''; }
	if(!empty($arr['fee_sell_ratio']))  { $qry[] = 'fee_sell_ratio=\''.$dbcon->escape($arr['fee_sell_ratio']).'\''; }
	if(!empty($arr['tax_sell_ratio']))  { $qry[] = 'tax_sell_ratio=\''.$dbcon->escape($arr['tax_sell_ratio']).'\''; }
	if(!empty($arr['tax_income_ratio']))  { $qry[] = 'tax_income_ratio=\''.$dbcon->escape($arr['tax_income_ratio']).'\''; }
	if(!empty($arr['trade_min_volume']))  { $qry[] = 'trade_min_volume=\''.$dbcon->escape($arr['trade_min_volume']).'\''; }
	if(!empty($arr['trade_max_volume']))  { $qry[] = 'trade_max_volume=\''.$dbcon->escape($arr['trade_max_volume']).'\''; }
	if(!empty($arr['out_min_volume']))  { $qry[] = 'out_min_volume=\''.$dbcon->escape($arr['out_min_volume']).'\''; }
	if(!empty($arr['out_max_volume']))  { $qry[] = 'out_max_volume=\''.$dbcon->escape($arr['out_max_volume']).'\''; }
	if(!empty($arr['display_decimals']))  { $qry[] = 'display_decimals=\''.$dbcon->escape($arr['display_decimals']).'\''; }
	if(!empty($arr['regdate']))  { $qry[] = 'regdate=\''.$dbcon->escape($arr['regdate']).'\''; }
	if(!empty($arr['active']))  { $qry[] = 'active=\''.$dbcon->escape($arr['active']).'\''; }
	if(!empty($arr['creatable']))  { $qry[] = 'creatable=\''.$dbcon->escape($arr['creatable']).'\''; }
	if(!empty($arr['crypto_currency']))  { $qry[] = 'crypto_currency=\''.$dbcon->escape($arr['crypto_currency']).'\''; }
	if(!empty($arr['backup_address']))  { $qry[] = 'backup_address=\''.$dbcon->escape($arr['backup_address']).'\''; }
	if(!empty($arr['sortno']))  { $qry[] = 'sortno=\''.$dbcon->escape($arr['sortno']).'\''; }
	if(!empty($arr['menu']))  { $qry[] = 'menu=\''.$dbcon->escape($arr['menu']).'\''; }
	if(!empty($arr['color']))  { $qry[] = 'color=\''.$dbcon->escape($arr['color']).'\''; }
	if(isset($arr['check_deposit']))  { $qry[] = 'check_deposit=\''.$dbcon->escape($arr['check_deposit']).'\''; }
	if(!empty($arr['transaction_outlink']))  { $qry[] = 'transaction_outlink=\''.$dbcon->escape($arr['transaction_outlink']).'\''; }
	if(!empty($arr['circulating_supply']))  { $qry[] = 'circulating_supply=\''.$dbcon->escape($arr['circulating_supply']).'\''; }
	if(isset($arr['max_supply']))  { $qry[] = 'max_supply=\''.$dbcon->escape($arr['max_supply']).'\''; }
	if(isset($arr['icon_url']))  { $qry[] = 'icon_url=\''.$dbcon->escape($arr['icon_url']).'\''; }
	if(!empty($arr['price']))  { $qry[] = 'price=\''.$dbcon->escape($arr['price']).'\''; }

	if(isset($arr['info01']))  { $qry[] = 'info01=\''.$dbcon->escape($arr['info01']).'\''; }
	if(isset($arr['info02']))  { $qry[] = 'info02=\''.$dbcon->escape($arr['info02']).'\''; }
	if(isset($arr['info03']))  { $qry[] = 'info03=\''.$dbcon->escape($arr['info03']).'\''; }

	if(isset($arr['ceo_name']))  { $qry[] = 'ceo_name=\''.$dbcon->escape($arr['ceo_name']).'\''; }
	if(isset($arr['ceo_country']))  { $qry[] = 'ceo_country=\''.$dbcon->escape($arr['ceo_country']).'\''; }
	if(isset($arr['company_type']))  { $qry[] = 'company_type=\''.$dbcon->escape($arr['company_type']).'\''; }
	if(isset($arr['stock_type']))  { $qry[] = 'stock_type=\''.$dbcon->escape($arr['stock_type']).'\''; }
	if(isset($arr['business_number']))  { $qry[] = 'business_number=\''.$dbcon->escape($arr['business_number']).'\''; }
	if(isset($arr['company_tel']))  { $qry[] = 'company_tel=\''.$dbcon->escape($arr['company_tel']).'\''; }
	if(isset($arr['company_address']))  { $qry[] = 'company_address=\''.$dbcon->escape($arr['company_address']).'\''; }
	if(isset($arr['company_hompage']))  { $qry[] = 'company_hompage=\''.$dbcon->escape($arr['company_hompage']).'\''; }
	if(isset($arr['manager_userid']))  { $qry[] = 'manager_userid=\''.$dbcon->escape($arr['manager_userid']).'\''; }
	if(isset($arr['manager_userno']))  { $qry[] = 'manager_userno=\''.$dbcon->escape($arr['manager_userno']).'\''; }
    if(isset($arr['display_grade']))  { $qry[] = 'display_grade=\''.$dbcon->escape($arr['display_grade']).'\''; }

	if($_POST['pg_mode'] == 'write') {
		$qry[] = 'regdate=NOW()';
	}
	return implode(',',$qry);
}
