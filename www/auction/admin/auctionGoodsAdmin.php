<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment :
--------------------------------------------*/
include_once $_SERVER["DOCUMENT_ROOT"] . '/lib/common_admin.php';
include_once '../auction_class.php';

function getNavi()
{
	$ret = array();
	return $ret;
}

$js = new auction($tpl);
$js->dbcon = &$dbcon;
$js->json = &$json;

$interface = new ControlUserInteface();
$interface->tpl = &$tpl;
$interface->setBasicInterface('admin');
$interface->addNavi(getNavi());

if ($_POST['pg_mode']) {
	switch($_POST['pg_mode']) {
		case 'list':
			ajaxCheckAdmin();
			$_GET['sort_target'] = array('start_date');
			$_GET['sort_method'] = array('desc');
			if ($_REQUEST['order']) {
				$i = 0;
				// Query ErrorNo : 1054<br />Query Error Message : Unknown column 't1.userid' in 'order clause'<br />Query String : select count(*) from js_trade_gwskrw_order t1  WHERE 1  ORDER BY t1.userid asc<br />Source Error File : basic_class.php<br />Source Error Line : 81<br />Error Source File : orderHistoryAdmin.php
				foreach ($_REQUEST['order'] as $order) {
					$_GET['sort_target'][$i] = $_REQUEST['columns'][$order['column']]['data'];
					$_GET['sort_method'][$i] = $order['dir'];
					$i++;
				}
			}
			$_GET['searchval'] = $_REQUEST['search']['value'] ? $_REQUEST['search']['value'] : false;
			$_GET['searchval'] = $_REQUEST['searchval'] ? $_REQUEST['searchval'] : $_GET['searchval'];
			$_GET['start'] = $_REQUEST['start'] ? $_REQUEST['start'] * 1 : 0;
            $_GET['pack_info'] = $_REQUEST['pack_info'];
			$page = $_REQUEST['draw'] ? $_REQUEST['draw'] * 1 : 1;
			$js->config['loop_scale'] = $_REQUEST['length'] ? $_REQUEST['length'] * 1 : $js->config['loop_scale'];
			$js->config['bool_navi_page'] = strtoupper($_REQUEST['length']) == 'ALL' ? false : true;
			$r = $js->goodsLists();				//list
			$total = $js->goodsLists_cnt();		//list total count

			exit(json_encode(array('data' => $r, 'draw' => $page, 'recordsFiltered' => $total, 'recordsTotal' => $total)));
			break;
		case 'write':
			ajaxCheckAdmin();
			$js->write();
			break;
		case 'edit':
			ajaxCheckAdmin();
			$js->edit();
			break;
		case 'delete-goods' :
			ajaxCheckAdmin();
			$js->deleteGoods($_POST['goods_idx']);
			break;
		case 'delete_file' :
			ajaxCheckAdmin();
			$js->delete_file_data($_POST['file_url'], $_POST['idx']);
			break;
		case 'confirm' :
			ajaxCheckAdmin();
			$js->confirm($_POST['type'], $_POST['value'], $_POST['idx']);
			break;
        case 'file_upload' :

            include ROOT_DIR.'/lib/XLSXReader.php';
            include ROOT_DIR.'/lib/VirtualBrowser.class.php';
            $curl = new VirtualBrowser();

            $api_host = 'https://'.str_replace('admin.', 'api.', $_SERVER['HTTP_HOST']);  // admin.loc.kkikda.com  -> api.loc.kkikda.com

            if(strpos(__API_RUNMODE__, 'loc') !== false) {
                $api_host = str_replace('https://', 'http://', $api_host);
            }

            if ($_POST['upload_type'] == "goods_upload") {
                // 상품 업로드
                $xlsx = new XLSXReader($_FILES['goods_file_data']['tmp_name']);
                $sheets = $xlsx->getSheetNames(); // 배열키 1부터 시작 array(1) { [1]=> string(5) "Excel" }
                $data = $xlsx->getSheetData($sheets[1]);
                $cnt_row = count($data);// row 배열키 0부터 시작


                if (strpos($_FILES['goods_file_data']['type'], "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") === false) {
                    echo "<script language=javascript> alert('엑셀 파일(xlsx)만 지원합니다.'); history.back(-1); </script>";
                }

                $_goods_type_array = $dbcon->query_list_object("SELECT * FROM js_auction_goods_type ORDER BY idx ");
                foreach ($_goods_type_array as $goods_type) {
                    $goods_type_array[$goods_type->title] = $goods_type->goods_type;
                }

                $_certification_marks_array = $dbcon->query_list_object("SELECT * FROM js_auction_certification_marks ORDER BY title ");
                foreach ($_certification_marks_array as $certification_marks) {
                    $certification_marks_array[$certification_marks->title] = $certification_marks->idx;
                }

                $curl_url = "{$api_host}/v1.0/putAuction/goods.php";

                $check_list = array();
                $package_goods_idx = "";
                for($i=0; $i<$cnt_row ; $i++) {
                    $row = $data[$i];

                    if($i>=1) {
                        if ($row[0]  == "") {
                            // 마스터 상품 일 경우
                            $_data['creator_userno'] = 2;                                                              // creator_userno = 2 (고정)
                            $_data['stock_number'] = trim($row[0]);                                                    // 재고 번호
                            $_data['title'] = trim($row[1]);                                                           // 차이름
                            $_data['main_pic'] = preg_replace('/\s+/', '', $row[2]);                                               // 이미지 파일명
                            $_data['base_price'] =  preg_replace('/[^0-9.]/','', $row[3]);           // 기본가격
                            $_data['nft_file_type'] = "IMAGE";                                                         // nft_type (고정)
                            $_data['meta_certification_mark'] = $certification_marks_array[trim($row[4])];             // 마크
                            $_data['content'] = trim($row[5]);                                                         // 차 소개
                            $_data['royalty'] = preg_replace('/[^0-9.]/','', $row[6]);               // 로열티
                            $_data['goods_type'] = $goods_type_array[trim($row[7])];                                    // 카테고리
                            $_data['minting_quantity'] = preg_replace('/[^0-9.]/','', $row[8]);       // 발생수량
                            $_data['active'] = trim($row[9]);   // Y or N                                               // 사용여부
                            $_data['meta_division'] = trim($row[10]);                                                   // 구분
                            $_data['meta_type'] = trim($row[11]);                                                       // 타입
                            $_data['meta_produce'] = trim($row[12]);                                                    // 생상
                            $_data['meta_wp_class'] = trim($row[13]);                                                   // 분류
                            $_data['meta_wp_orgigin'] = trim($row[14]);                                                 // 원산지
                            $_data['meta_wp_producer'] = trim($row[15]);                                                // 생상자
                            $_data['meta_wp_production_date'] = trim($row[16]);                                         // 생상년도
                            $_data['meta_wp_scent'] = trim($row[17]);                                                   // 향
                            $_data['meta_wp_weight'] = trim($row[18]);                                                  // 중량
                            $_data['meta_wp_taste'] = trim($row[19]);                                                   // 맛
                            $_data['meta_wp_drink_method'] = trim($row[20]);                                            // 마시는 방법
                            $_data['meta_wp_keep_method'] = trim($row[21]);                                             // 보관방법/유통기한
                            $_data['meta_wp_story'] = trim($row[22]);                                                   // 스토리
                            $_data['meta_wp_teamaster_note'] = trim($row[23]);                                          // 티마스터 품평
                            $_data['meta_wp_producer_note'] = trim($row[24]);                                           // 생상자 노트
                            $_data['meta_wp_grade'] = trim($row[25]);                                                   // 평점
                            $_data['meta_wp_kind'] = trim($row[26]);                                                    // 품종
                            $_data['meta_wp_second_order'] = trim($row[27]);                                            // 차수량
                            $_data['meta_wp_price_trend'] = trim($row[28]);                                             // 가격동향
                            $_data['meta_wp_valueability'] = trim($row[29]);                                            // 가치성
                            $_data['meta_wp_scarcity'] = trim($row[30]);                                                // 희소성
                            $_data['meta_wp_popular'] = trim($row[31]);                                                 // 대중성
                            $_data['goods_grade'] = trim($row[32]);                                                 // 상품등급
                            $_data['meta_wp_pojang'] = trim($row[33]);                                                 // 상품등급
                            $_data['meta_wp_jingi'] = trim($row[34]);                                                 // 상품등급
                            $_data['meta_wp_chanamu'] = trim($row[35]);                                                 // 상품등급

                            $curl_result = json_decode($curl->get($curl_url."?".http_build_query($_data)));

                            if (empty($curl_result->success)) {
                                $package_goods_idx = "";
                                $check_list[$i]['row'] = $i;
                                $check_list[$i]['idx'] = '';
                                $check_list[$i]['stock_number'] = trim($row[0]);
                                $check_list[$i]['title'] = trim($row[1]);
                                $check_list[$i]['message'] = $curl_result->error->message;
                            } else {
                                $package_goods_idx = $curl_result->payload->goods_idx;
                                $check_list[$i]['row'] = $i;
                                $check_list[$i]['idx'] = $curl_result->payload->goods_idx;
                                $check_list[$i]['stock_number'] = trim($row[0]);
                                $check_list[$i]['title'] = trim($row[1]);
                                $check_list[$i]['message'] = "성공";
                            }

                        } else {
                            // 서브상품 일 경우
                            if ($package_goods_idx) {
                                $r = $dbcon->query_unique_object("SELECT count(`stock_number`) cnt FROM js_auction_goods WHERE `stock_number`='{$dbcon->escape(trim($row[0]))}' ");
                                if ($r->cnt < 1) {
                                    $package_goods_info = $dbcon->query_unique_object("SELECT * FROM js_auction_goods WHERE pack_info = '{$package_goods_idx}' AND stock_number='' limit 0, 1 ");
                                    $dbcon->query("UPDATE js_auction_goods SET stock_number ='{$row[0]}' WHERE idx = '{$package_goods_info->idx}' ");
                                    $check_list[$i]['row'] = $i;
                                    $check_list[$i]['idx'] = $package_goods_info->idx;
                                    $check_list[$i]['stock_number'] = trim($row[0]);
                                    $check_list[$i]['title'] = trim($row[1]);
                                    $check_list[$i]['message'] = "성공";
                                } else {
                                    $check_list[$i]['row'] = $i;
                                    $check_list[$i]['idx'] = $package_goods_idx;
                                    $check_list[$i]['stock_number'] = trim($row[0]);
                                    $check_list[$i]['title'] = trim($row[1]);
                                    $check_list[$i]['message'] = "이미 등록된 재고 번호 입니다.";
                                }

                            } else {
                                $check_list[$i]['row'] = $i;
                                $check_list[$i]['idx'] = '';
                                $check_list[$i]['stock_number'] = trim($row[0]);
                                $check_list[$i]['title'] = trim($row[1]);
                                $check_list[$i]['message'] = $curl_result->error->message;
                            }

                        }
                    }
                }

            } else if ($_POST['upload_type'] == "goods_add_uplaod") {
                // 상품 추가
                $xlsx = new XLSXReader($_FILES['add_goods_file_data']['tmp_name']);
                $sheets = $xlsx->getSheetNames(); // 배열키 1부터 시작 array(1) { [1]=> string(5) "Excel" }
                $data = $xlsx->getSheetData($sheets[1]);
                $cnt_row = count($data);// row 배열키 0부터 시작

                $curl_url = "{$api_host}/v1.0/putAuction/put_goods_inventory.php";

                $check_list = array();

                for($i=0; $i<$cnt_row; $i++) {
                    $row = $data[$i];

                    if ($i>0) {
                        $_data['creator_userno'] = 2;
                        $_data['goods_idx'] = $row[0];
                        $_data['stock_number'] = $row[1];
                        $_data['owner_userno'] = $row[2];
                        $_data['goods_grade'] = $row[3];

                        $curl_result = json_decode($curl->get($curl_url."?".http_build_query($_data)));

                        if (empty($curl_result->success)) {
                            $check_list[$i]['row'] = $i;
                            $check_list[$i]['idx'] = trim($row[0]);
                            $check_list[$i]['stock_number'] = trim($row[1]);
                            $check_list[$i]['title'] = '';
                            $check_list[$i]['message'] = $curl_result->error->message;
                        } else {
                            $check_list[$i]['row'] = $i;
                            $check_list[$i]['idx'] = $curl_result->payload->goods_idx;
                            $check_list[$i]['stock_number'] = trim($row[1]);
                            $check_list[$i]['title'] = '';
                            $check_list[$i]['message'] = "성공";
                        }
                    }
                }
            }
            $interface->layout['js_tpl_left'] = 'menu.html?main';
            $tpl->assign('check_list', $check_list);
            $interface->addScript('/template/'.getSiteCode().'/admin/auction/js/auctionGoodsWrite.js');
            $interface->layout['js_tpl_main'] = 'auction/auction_goods_excel_upload.html';
            $print = 'layout';

            $interface->display($print);
            break;

	}
} else {
	checkAdmin();
	$interface->layout['js_tpl_left'] = 'menu.html?main';

	switch($_GET['pg_mode']) {

		case 'edit':

			if(trim($_GET['start_date'])=='') { $_GET['start_date'] = date('Y-m-d'); }
			if(trim($_GET['end_date'])=='') { $_GET['end_date'] = date('Y-m-d', time() + 60*60*24*30); }
            $interface->setPlugIn('typehead');
            $interface->addScript('/template/'.getSiteCode().'/admin/auction/js/auctionGoodsWrite.js');
			$js->editForm();
			// $js->setAuction($_GET['idx']);
			
		case 'write':

			$js->setCategory();
			$js->setCertificationMark();
	
			$interface->addScript('/template/'.getSiteCode().'/admin/auction/js/auctionGoodsWrite.js');
			$interface->layout['js_tpl_main'] = 'auction/auction_goodsform.html';
			break;

        case 'excel-upload' :
            $interface->addScript('/template/' . getSiteCode() . '/admin/auction/js/auctionGoods.js');
            $interface->layout['js_tpl_main'] = 'auction/auction_goods_excel_upload.html';
            break;
		case 'goods':
		default:
		
			$interface->setPlugIn('switchery');
			$interface->setPlugIn('datatables');
			$interface->setPlugIn('bootstrap-toggle');

			$interface->addScript('/template/' . getSiteCode() . '/admin/auction/js/auctionGoods.js');
			$interface->layout['js_tpl_main'] = 'auction/auction_goodstop.html';
			$interface->layout['js_tpl_main_sub'] = 'auction/auction_goodslist.html';

	}

	$print = 'layout';
	$interface->display($print);
	
	
}
$dbcon->close();
