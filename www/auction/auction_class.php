<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment :
--------------------------------------------*/
class auction extends BASIC
{
	function __construct(&$tpl)
	{
		$config = array();

		$config['table_name'] = 'js_auction_goods';
		$config['query_func'] = 'querySet';
		$config['write_mode'] = 'ajax';//ajax or link

		$config['bool_navi_page'] = TRUE;
		$config['loop_scale'] = 10;
		$config['page_scale'] = 5;
		$config['navi_url'] = '?';
		$config['navi_pg_mode'] = 'list';
		$config['navi_qry'] = '';
		$config['navi_mode'] = 'link';
		$config['navi_load_id'] = '';

		$this->BASIC($config,$tpl);
		
		require __DIR__.'/../cheditor/imageUpload/s3.php';
		$this->S3 = new S3;
		include_once __DIR__.'/../lib/GoogleDrive.class.php';
		$this->GoogleDrive = new GoogleDrive();

	}

	//옥션 관리 목록 total
	function lists_cnt()
	{
		$query = "SELECT count(g.idx) from js_auction_goods as g where 1 ".$this->srchQry();
			//echo($query);
		return $this->dbcon->query_unique_value($query,__FILE__,__LINE__);
	}

	//옥션 관리 목록
	function lists()
	{
		//$this->tpl->assign('API_RUNMODE', __API_RUNMODE__);
		//회사 계정 수정 필요
		// $com_user_id = "ara_manager";

		$query = "SELECT l.auction_idx, l.goods_idx, l.auction_title, l.start_date, l.end_date, l.start_price, l.wish_price, l.sell_price, l.finish, l.price_symbol, l.event_bn, i.userid
			, (select max(auction_price) from js_auction_apply where auction_idx=l.auction_idx) current_price
			FROM js_auction_list AS l
			INNER JOIN js_auction_inventory AS i ON i.goods_idx=l.goods_idx

			WHERE 1
			ORDER BY l.reg_date DESC ";
		$arr = $this->dbcon->query_all_array($query,__FILE__, __LINE__);
		for($i=0; $i<count($arr); $i++) {
			$row = $arr[$i];
			$row['start_price'] = $row['start_price']*1;
			$row['wish_price'] = $row['wish_price']*1;
			$row['sell_price'] = $row['sell_price']*1;
			$row['finish_str'] = $row['finish']=='Y' ? '종료' : (date('Y-m-d H:i:s') < $row['start_date'] ? '시작전' : '진행중');
			$row['event_bn'] = $row['event_bn']=='Y' ? 'Y' : 'N';

			// a.auction_idx, a.sell_price, a.start_date, a.end_date, a.auction_title
			$auction = $this->dbcon->query_unique_array("SELECT idx, title, goods_type, main_pic, sub1_pic, sub2_pic, sub3_pic, sub4_pic, animation, content FROM js_auction_goods WHERE idx='".$row['goods_idx']."' ORDER BY idx DESC LIMIT 1 ");
			if($auction) {
				$auction['current_price'] = $auction['current_price']*1;

				// 최고가 입찰자
				$max_bidder_info = $this->dbcon->query_fetch_object("SELECT auction_price, userno FROM  js_auction_apply WHERE auction_idx='{$this->dbcon->escape($row['auction_idx'])}' ORDER BY auction_price DESC, reg_date DESC LIMIT 1");
				// var_dump($max_bidder_info, "SELECT auction_price, userno FROM  js_auction_apply WHERE auction_idx='{$this->dbcon->escape($row['auction_idx'])}' ORDER BY auction_price DESC, reg_date DESC LIMIT 1");
				// exit;
				$row['max_auction_price'] = $max_bidder_info->auction_price ? $max_bidder_info->auction_price * 1 : '';
				$row['max_auction_bider'] = $max_bidder_info->userno ? $this->get_auction_member_name($max_bidder_info->userno) : '';
			} else {
				$auction = array(
					'idx'=>''
					,'title'=>''
					,'goods_type'=>''
					,'main_pic'=>''
					,'sub1_pic'=>''
					,'sub2_pic'=>''
					,'sub3_pic'=>''
					,'sub4_pic'=>''
					,'animation'=>''
					,'content'=>''
					,'current_price'=>''
					,'max_auction_price'=>''
					,'max_auction_bider'=>''
				);
			}
			$row = array_merge($row, $auction);
			$row['json_data'] = urlencode(json_encode($row));
			$arr[$i] = $row;
		}


		//----
    /*
		$query = "SELECT g.idx, g.title, g.goods_type, g.main_pic, g.sub1_pic, g.sub2_pic, g.sub3_pic, g.sub4_pic, g.animation, g.content, i.userid
			FROM js_auction_goods AS g
			INNER JOIN js_auction_inventory AS i ON i.goods_idx=g.idx
			WHERE 1
			ORDER BY g.reg_date DESC ";
		$arr = $this->dbcon->query_all_array($query,__FILE__, __LINE__);
		for($i=0; $i<count($arr); $i++) {
			$row = $arr[$i];
			$row['goods_idx'] = $row['idx'];

			// a.auction_idx, a.sell_price, a.start_date, a.end_date, a.auction_title
			$auction = $this->dbcon->query_unique_array("SELECT auction_idx, goods_idx, auction_title, start_date, end_date, start_price, wish_price, sell_price, finish FROM js_auction_list WHERE goods_idx='".$row['idx']."' ORDER BY auction_idx DESC LIMIT 1 ");
			$auction['sell_price'] = $auction['sell_price']*1;
			if($auction['auction_idx']) $auction['current_price'] = $this->dbcon->query_unique_value("SELECT auction_price FROM js_auction_apply WHERE auction_idx='".$auction['auction_idx']."' AND '".$auction['start_date']."'<= reg_date AND reg_date<'".$auction['start_date']."' ORDER BY auction_price DESC LIMIT 1 ");
			$auction['current_price'] = $auction['current_price']*1;

			// $row['main_pic'] = $row['main_pic'] ? '//'.str_replace('admin.','auction.',$_SERVER['HTTP_HOST']).'/data/'.$row['idx'].'/'.$row['main_pic'] : '';
			// $row['sub1_pic'] = $row['sub1_pic'] ? '//'.str_replace('admin.','auction.',$_SERVER['HTTP_HOST']).'/data/'.$row['idx'].'/'.$row['sub1_pic'] : '';
			// $row['sub2_pic'] = $row['sub2_pic'] ? '//'.str_replace('admin.','auction.',$_SERVER['HTTP_HOST']).'/data/'.$row['idx'].'/'.$row['sub2_pic'] : '';
			// $row['sub3_pic'] = $row['sub3_pic'] ? '//'.str_replace('admin.','auction.',$_SERVER['HTTP_HOST']).'/data/'.$row['idx'].'/'.$row['sub3_pic'] : '';
			// $row['sub4_pic'] = $row['sub4_pic'] ? '//'.str_replace('admin.','auction.',$_SERVER['HTTP_HOST']).'/data/'.$row['idx'].'/'.$row['sub4_pic'] : '';
			// $row['animation'] = $row['animation'] ? '//'.str_replace('admin.','auction.',$_SERVER['HTTP_HOST']).'/data/'.$row['idx'].'/'.$row['animation'] : '';

			$row = array_merge($row, $auction);
			$row['json_data'] = urlencode(json_encode($row));
			$arr[$i] = $row;
		}
		*/
		return $arr;
	}

	function get_cate_list(){
		$query = 'SELECT goods_type as goods_type1 from js_auction_goods_type order by idx';
        $arr1 = $this->dbcon->query_all_array($query,__FILE__, __LINE__);
		$this->tpl->assign('loop_type', $arr1);
	}

	//상품 내역
	function goodsLists_cnt()
	{
		// $query = "SELECT count(g.idx) from js_auction_goods as g
		// 	INNER JOIN js_auction_list AS a ON a.goods_idx=g.idx
		// 	where 1 ".$this->srchQry();

		$where = "";
		if ($_GET['pack_info'] == "Y") {    // pack 상품 한개만 거래소등록
			$where = "AND g.pack_info = 'Y'";
		} else if ($_GET['pack_info'] == "N") {
			$where = "AND g.pack_info != 'Y'";
		}
		$query = "SELECT count(*)
		FROM js_auction_goods AS g
		LEFT JOIN js_auction_list AS a ON a.goods_idx=g.idx AND a.finish='N'
		LEFT JOIN js_trade_currency AS tc ON g.idx=tc.symbol
		WHERE 1 {$where} 
		".$this->srchQry();
		//echo($query);
		return $this->dbcon->query_unique_value($query,__FILE__,__LINE__);
	}

	//상품 내역
	function goodsLists()
	{

		$_GET['start'] = $_REQUEST['start'] ? $_REQUEST['start']*1 : 0;
		$page = $_REQUEST['draw'] ? $_REQUEST['draw']*1 : 1;
		$this->config['loop_scale'] = $_REQUEST['length'] ? $_REQUEST['length']*1 : $this->config['loop_scale'];
		$this->config['bool_navi_page'] = strtoupper($_REQUEST['length'])=='ALL' ? false : true;

		$_GET['sort_target'] = array('a.end_date');
		$_GET['sort_method'] = array('desc');
		if($_REQUEST['order']) {
			$i=0;
			foreach($_REQUEST['order'] as $order) {
				$_GET['sort_target'][$i] = $_REQUEST['columns'][ $order['column'] ]['data'];
				$_GET['sort_method'][$i] = $order['dir'];
				$i++;
			}
		}
		$sort = ' ORDER BY ';
		if($_GET['sort_target'] ) {
			for($i=0;$i<count($_GET['sort_target']);$i++) {
				if($i>0) {
					$sort .= ', ';
				}
				$sort .= ''.$_GET['sort_target'][$i].' '.$_GET['sort_method'][$i];
			}
		} else {
			$sort .= 'm.reg_date DESC';
		}
		$limit = ' limit '.$_GET['start'].', '.$this->config['loop_scale'];

        $where = "";
        if ($_GET['pack_info'] == "Y") {    // pack 상품 한개만 거래소등록
            $where = "AND g.pack_info = 'Y'";
        } else if ($_GET['pack_info'] == "N") {
            $where = "AND g.pack_info != 'Y'";
        }

		//회사 계정 수정 필요
		// $com_user_id = "ara_manager";
		$query = "SELECT g.*
			, a.auction_idx, a.sell_price, a.start_date, a.end_date, a.auction_title
			, tc.symbol trade_currency_symbol
			FROM js_auction_goods AS g
			LEFT JOIN js_auction_list AS a ON a.goods_idx=g.idx AND a.finish='N'
			LEFT JOIN js_trade_currency AS tc ON g.idx=tc.symbol
			WHERE 1 {$where} 
			".$this->srchQry()."
			".$sort.$limit;
			// exit($query);
		$arr = $this->dbcon->query_all_array($query,__FILE__, __LINE__);

		for($i=0; $i<count($arr); $i++) {
			$row = $arr[$i];
			$row['goods_idx'] = $row['idx'];
			$row['price'] = ($row['price']*1);

			// a.auction_idx, a.sell_price, a.start_date, a.end_date, a.auction_title
			$auction = $this->dbcon->query_unique_array("SELECT jai.userid, jam.nickname, jm.name FROM js_auction_inventory AS jai LEFT JOIN js_auction_member AS jam ON jam.userno = jai.userno LEFT JOIN js_member AS jm ON jai.userid = jm.userid  WHERE goods_idx='".$row['idx']."'");
			if($auction) {
				$auction['userid'] = $auction['userid'];
				$auction['name'] = $auction['name'];
				if($auction['nickname']) {
					$auction['name'] = $auction['nickname'];
				}

				//$auction['sell_price'] = $auction['sell_price']*1;
				$sql = "SELECT auction_price FROM js_auction_apply WHERE auction_idx='".$row['auction_idx']."' ";
				if($row['start_date']) {
					$sql.= "AND '".$row['start_date']."'<= reg_date ";
				}
				if($row['end_date']) {
					$sql.= "AND reg_date<'".$row['end_date']."'  ";
				}
				$sql.= "ORDER BY auction_price DESC LIMIT 1 ";
				$auction['current_price'] = $this->dbcon->query_unique_value($sql);
				$auction['current_price'] = $auction['current_price']*1;

				// $row['main_pic'] = $row['main_pic'] ? '//'.str_replace('admin.','auction.',$_SERVER['HTTP_HOST']).'/data/'.$row['idx'].'/'.$row['main_pic'] : '';

				// 최고가 입찰자
				$max_bidder_info = $this->dbcon->query_fetch_object("SELECT auction_price, userno FROM  js_auction_apply WHERE auction_idx='{$this->dbcon->escape($row['auction_idx'])}' ORDER BY auction_price DESC, reg_date DESC LIMIT 1");
				$row['max_auction_price'] = $max_bidder_info->auction_price ? $max_bidder_info->auction_price * 1 : '';
				$row['max_auction_bider'] = $max_bidder_info->userno ? $this->get_auction_member_name($max_bidder_info->userno) : '';

				$row = array_merge($row, $auction);
			} else {
				$row = array_merge($row, array('userid'=>'','nickname'=>'','name'=>'','current_price'=>'','current_price'=>'','max_auction_price'=>'','max_auction_bider'=>''));
			}
			//$row['json_data'] = urlencode(json_encode($row));
			$arr[$i] = $row;
		}
		// var_dump($query );exit;
		$arr = $this->dbcon->null_to_emtpy($arr);
		return $arr;
	}

	/**
	 * 옥션 회원 이름 조회
	 * 순서 : js_auction_member.nickname > js_member.nickname > js_member.name > js_member.userid
	 * @param number $userno 회원번호
	 * @return String 회원이름
	 */
	function get_auction_member_name($userno) {
		$r = $this->dbcon->query_one("SELECT IF(am.nickname='' OR am.nickname IS NULL, (IF(m.nickname='' OR m.nickname IS NULL, IF(m.name='' OR m.name IS NULL, m.userid, m.name), m.nickname)), am.nickname) FROM js_member m LEFT JOIN js_auction_member am ON am.userno=m.userno WHERE m.userno='{$this->dbcon->escape($userno)}' ");
		return $r ? $r : '';
	}

	/**
	 * active 숨김/표시 처리, best_item 처리
	 */
	 function confirm ( $type, $value, $idx ) {
		if(! in_array($type, array('active', 'goods_grade'))) {
			return false;
		}
		$sql = "update js_auction_goods set ";
		switch($type) {
            case 'goods_grade':
                $sql .= " goods_grade = '". $value . "'" ;
            break;
			case 'active':
				$sql .= " active = '". ($value=='Y' ? 'Y' : 'N') . "'" ;
			break;
		}
		$sql .= " where idx = '{$idx}' ";
		$r = $this->dbcon->query($sql);
		if($r) {
			if($type=='active') { // active 일때
				if($value=='N') { // 비표시 일때
					$sql = "update js_auction_goods set active = 'N' where idx='{$idx}' ";
					$this->dbcon->query($sql);
				}
			}
		}
		return $r;
	}

	// 상품 삭제
	function deleteGoods($goods_idx)
	{
		$r = true;
		if($goods_idx) {
			// 경매 정보 조회
			$goods_info = $this->dbcon->query_fetch_object("SELECT * FROM js_auction_goods WHERE idx='{$this->dbcon->escape($goods_idx)}'");
			$auction_info = $this->dbcon->query_fetch_object("SELECT * FROM js_auction_list WHERE goods_idx='{$this->dbcon->escape($goods_idx)}' ORDER BY auction_idx DESC LIMIT 1");
			// 경매 진행중인지 확인.
			if($auction_info && ($auction_info->finish=='N' && date('Y-m-d H:i:s')<$auction_info->end_date )) {
				jsonMsg(0,'진행중인 경매 정보가 있어 삭제할 수 없습니다.');
			}
			// 경매 정보가 있으면 삭제 불가
			// if($auction_info) {
			// 	$this->_message = '경매 정보가 있어 삭제할 수 없습니다.';
			// 	return false;
			// }
			// main_pic 삭제
			if($goods_info->main_pic)  {
				$this->delete_external_file($goods_info->main_pic);
			}
			// animation 삭제
			if($goods_info->animation)  {
				$this->delete_external_file($goods_info->animation);
			}
			// NFT 토큰URI 삭제
			if($goods_info->nft_tokeuri)  {
				$this->delete_external_file($goods_info->nft_tokeuri);
			}

			$this->dbcon->transaction(1); // set autocommit=0
			try {
				$this->dbcon->query("DELETE FROM js_auction_goods WHERE idx='{$this->dbcon->escape($goods_idx)}' ");
				$this->dbcon->query("DELETE FROM js_auction_goods_meta WHERE goods_idx='{$this->dbcon->escape($goods_idx)}' ");
				$this->dbcon->query("DELETE FROM js_auction_goods_company WHERE goods_idx='{$this->dbcon->escape($goods_idx)}' ");
				$this->dbcon->query("DELETE FROM js_auction_inventory WHERE goods_idx='{$this->dbcon->escape($goods_idx)}' ");
				$this->dbcon->query("DELETE FROM js_auction_apply_list WHERE auction_idx IN (SELECT auction_idx FROM js_auction_list WHERE goods_idx='{$this->dbcon->escape($goods_idx)}') ");
				$this->dbcon->query("DELETE FROM js_auction_apply WHERE goods_idx='{$this->dbcon->escape($goods_idx)}' ");
				$this->dbcon->query("DELETE FROM js_auction_txn WHERE goods_idx='{$this->dbcon->escape($goods_idx)}' ");
				$this->dbcon->query("DELETE FROM js_auction_list WHERE goods_idx='{$this->dbcon->escape($goods_idx)}' ");

                if ($goods_info->pack_info == "Y") {
                    $goods_inventory = $this->dbcon->query_list_object("SELECT * FROM js_auction_goods WHERE pack_info='{$this->dbcon->escape($goods_idx)}'");

                    foreach ($goods_inventory as $k => $v) {
                        $goods_inventory_idx = $goods_inventory[$k]->idx;
                        $this->dbcon->query("DELETE FROM js_auction_goods WHERE idx='{$this->dbcon->escape($goods_inventory_idx)}' ");
                        $this->dbcon->query("DELETE FROM js_auction_goods_meta WHERE goods_idx='{$this->dbcon->escape($goods_inventory_idx)}' ");
                        $this->dbcon->query("DELETE FROM js_auction_goods_company WHERE goods_idx='{$this->dbcon->escape($goods_inventory_idx)}' ");
                        $this->dbcon->query("DELETE FROM js_auction_inventory WHERE goods_idx='{$this->dbcon->escape($goods_inventory_idx)}' ");
                        $this->dbcon->query("DELETE FROM js_auction_apply_list WHERE auction_idx IN (SELECT auction_idx FROM js_auction_list WHERE goods_idx='{$this->dbcon->escape($goods_inventory_idx)}') ");
                        $this->dbcon->query("DELETE FROM js_auction_apply WHERE goods_idx='{$this->dbcon->escape($goods_inventory_idx)}' ");
                        $this->dbcon->query("DELETE FROM js_auction_txn WHERE goods_idx='{$this->dbcon->escape($goods_inventory_idx)}' ");
                        $this->dbcon->query("DELETE FROM js_auction_list WHERE goods_idx='{$this->dbcon->escape($goods_inventory_idx)}' ");
                    }
                }

				$this->dbcon->transaction(2); // commit
			} catch (Exception $e) {
				$this->dbcon->transaction(3); // rollback
				$r = false;
			}
		}
		
		// 결과리턴
		if($r) {
			jsonMsg(1);
		} else {
			jsonMsg(0,'삭제하지 못했습니다.');
		}
	}

	function delete_external_file($url) {
		try {
			
			if(strpos($url, '.amazonaws.com/')!==false)  {			
				$this->S3->delete_file_to_s3($url);
			}
			if(strpos($url, 'drive.google.com')!==false)  {
				$this->GoogleDrive->delete_google_drive_by_url($url);
			}
		} catch(Exception $e) {
			// echo $e->getMessage();
			// var_dump($e); exit;
		}
	}



	// 상품 숨김
	function hideGoods($goods_idx)
	{
		$r = false;
		if($goods_idx) {
			$query = "UPDATE js_auction_goods SET active='N' WHERE idx='{$this->dbcon->escape($goods_idx)}' ";
			$r = $this->dbcon->query($query,__FILE__,__LINE__);
		}
		return $r;
	}
	// 상품 노출
	function showGoods($goods_idx)
	{
		$r = false;
		if($goods_idx) {
			$query = "UPDATE js_auction_goods SET active='Y' WHERE idx='{$this->dbcon->escape($goods_idx)}' ";
			$r = $this->dbcon->query($query,__FILE__,__LINE__);
		}
		return $r;
	}

	// 신고상품번호 조회
	function getReportGoodsIdx($report_idx) {
		return $this->dbcon->query_one("SELECT goods_idx FROM js_auction_goods_report WHERE report_idx='{$this->dbcon->escape($report_idx)}' ",__FILE__,__LINE__);
	}
	//신고 상품 삭제
	function deleteReportGoods()
	{
		$report_idx = $_REQUEST['report_idx'];
		$goods_idx = $this->getReportGoodsIdx($report_idx);
		return $this->deleteGoods($goods_idx);;
	}
	//신고 상품 숨김
	function hideReportGoods()
	{
		$report_idx = $_REQUEST['report_idx'];
		$goods_idx = $this->getReportGoodsIdx($report_idx);
		return $this->hideGoods($goods_idx);
	}
	//신고 상품 노출
	function showReportGoods()
	{
		$report_idx = $_REQUEST['report_idx'];
		$goods_idx = $this->getReportGoodsIdx($report_idx);
		return $this->showGoods($goods_idx);
	}
	//신고 처리완료
	function deleteReport()
	{
		$report_idx = $_REQUEST['report_idx'];
		$query = "UPDATE js_auction_goods_report SET work_date=NOW() WHERE report_idx='{$this->dbcon->escape($report_idx)}' ";
		return $this->dbcon->query($query,__FILE__,__LINE__);
	}
	//신고 내역수
	function reportLists_cnt()
	{
		$query = "SELECT COUNT(*) FROM js_auction_goods_report AS r LEFT JOIN js_auction_goods g ON r.goods_idx=g.idx WHERE r.work_date IS NULL ".$this->srchQryReport();
			// echo($query);
		return $this->dbcon->query_unique_value($query,__FILE__,__LINE__);
	}
	//신고 내역
	function reportLists()
	{

		$_GET['start'] = $_REQUEST['start'] ? $_REQUEST['start']*1 : 0;
		$page = $_REQUEST['draw'] ? $_REQUEST['draw']*1 : 1;
		$this->config['loop_scale'] = $_REQUEST['length'] ? $_REQUEST['length']*1 : $this->config['loop_scale'];
		$this->config['bool_navi_page'] = strtoupper($_REQUEST['length'])=='ALL' ? false : true;

		$_GET['sort_target'] = array('m.reg_date');
		$_GET['sort_method'] = array('desc');
		if($_REQUEST['order']) {
			$i=0;
			foreach($_REQUEST['order'] as $order) {
				$_GET['sort_target'][$i] = $_REQUEST['columns'][ $order['column'] ]['data'];
				$_GET['sort_method'][$i] = $order['dir'];
				$i++;
			}
		}
		$sort = ' ORDER BY ';
		if($_GET['sort_target'] ) {
			for($i=0;$i<count($_GET['sort_target']);$i++) {
				if($i>0) {
					$sort .= ', ';
				}
				$sort .= ''.$_GET['sort_target'][$i].' '.$_GET['sort_method'][$i];
			}
		} else {
			$sort .= 'm.reg_date DESC';
		}
		$limit = ' limit '.$_GET['start'].', '.$this->config['loop_scale'];

		$query = "SELECT r.*, g.title goods_name, g.main_pic, g.active goods_active, m.userid report_userid,
			IF( r.report_type='C', '저작권이있는 아트웍의 무단 사용', IF( r.report_type='S', '노골적이고 민감한 콘텐츠', IF( r.report_type='R', '성 차별 주의자 또는 인종 차별적 표현', IF( r.report_type='A', '스팸', IF( r.report_type='E', '기타', ''))))) report_type_str,
			IF(am.nickname='' OR am.nickname IS NULL, IF(m.nickname='' OR m.nickname IS NULL, IF(m.name='' OR m.name IS NULL, m.userid, m.name), m.nickname), am.nickname) report_user_name
			FROM js_auction_goods_report AS r
			LEFT JOIN js_auction_goods AS g ON r.goods_idx=g.idx
			LEFT JOIN js_member AS m ON r.report_userno=m.userno
			LEFT JOIN js_auction_member am ON am.userno=m.userno
			WHERE r.work_date IS NULL
			".$this->srchQryReport().$sort.$limit;
			// echo($query); exit;
		$arr = $this->dbcon->query_all_array($query,__FILE__, __LINE__);

		// 추가정보
		// for($i=0; $i<count($arr); $i++) {
		// 	$row = $arr[$i];
		// 	// 경매정보
		// 	// // a.auction_idx, a.sell_price, a.start_date, a.end_date, a.auction_title
		// 	// if($row['auction_idx']) {
		// 	// 	$auction = $this->dbcon->query_unique_array("SELECT userid FROM js_auction_inventory WHERE goods_idx='".$row['idx']."'");
		// 	// 	$auction['userid'] = $auction['userid'];
		// 	// 	//$auction['sell_price'] = $auction['sell_price']*1;
		// 	// 	$auction['current_price'] = $this->dbcon->query_unique_value("SELECT auction_price FROM js_auction_apply WHERE auction_idx='".$row['auction_idx']."' AND '".$row['start_date']."'<= reg_date AND reg_date<'".$row['start_date']."' ORDER BY auction_price DESC LIMIT 1 ");
		// 	// 	$auction['current_price'] = $auction['current_price']*1;
		// 	// 	// $row['main_pic'] = $row['main_pic'] ? '//'.str_replace('admin.','auction.',$_SERVER['HTTP_HOST']).'/data/'.$row['idx'].'/'.$row['main_pic'] : '';
		// 	// 	$row = array_merge($row, $auction);
		// 	// }
		// 	// //$row['json_data'] = urlencode(json_encode($row));
		// 	$arr[$i] = $row;
		// }
		//var_dump($query );exit;
		return $arr;
	}

	//옥션 내역
	function historyLists_cnt()
	{
		$query = "SELECT count(g.idx) from js_auction_goods as g
			INNER JOIN js_auction_list AS a ON a.goods_idx=g.idx
			where 1 ".$this->srchQry();
			//echo($query);
		return $this->dbcon->query_unique_value($query,__FILE__,__LINE__);
	}

	//옥션 내역
	function historyLists()
	{
		$_GET['sort_target'] = array('a.end_date');
		$_GET['sort_method'] = array('desc');
		if($_REQUEST['order']) {
			$i=0;
			foreach($_REQUEST['order'] as $order) {
				$_GET['sort_target'][$i] = $_REQUEST['columns'][ $order['column'] ]['data'];
				$_GET['sort_method'][$i] = $order['dir'];
				$i++;
			}
		}
		$sort = ' ORDER BY ';
		if($_GET['sort_target'] ) {
			for($i=0;$i<count($_GET['sort_target']);$i++) {
				if($i>0) {
					$sort .= ', ';
				}
				$sort .= ''.$_GET['sort_target'][$i].' '.$_GET['sort_method'][$i];
			}
		} else {
			$sort .= 'm.reg_date DESC';
		}

		//회사 계정 수정 필요
		// $com_user_id = "ara_manager";
		$query = "SELECT g.idx, g.title, g.goods_type, g.main_pic, g.animation, g.content, a.auction_idx, a.sell_price, a.start_date, a.end_date, a.auction_title
			FROM js_auction_goods AS g
			INNER JOIN js_auction_list AS a ON a.goods_idx=g.idx
			WHERE 1
			".$this->srchQry()."
			".$sort;
			//echo($query);
		$arr = $this->dbcon->query_all_array($query,__FILE__, __LINE__);

		for($i=0; $i<count($arr); $i++) {
			$row = $arr[$i];
			$row['goods_idx'] = $row['idx'];


			// a.auction_idx, a.sell_price, a.start_date, a.end_date, a.auction_title
			if($row['auction_idx']) {
				$auction = $this->dbcon->query_unique_array("SELECT userid FROM js_auction_inventory WHERE goods_idx='".$row['idx']."'");
				$auction['userid'] = $auction['userid'];
				//$auction['sell_price'] = $auction['sell_price']*1;
				$auction['current_price'] = $this->dbcon->query_unique_value("SELECT auction_price FROM js_auction_apply WHERE auction_idx='".$row['auction_idx']."' AND '".$row['start_date']."'<= reg_date AND reg_date<'".$row['start_date']."' ORDER BY auction_price DESC LIMIT 1 ");
				$auction['current_price'] = $auction['current_price']*1;
				// $row['main_pic'] = $row['main_pic'] ? '//'.str_replace('admin.','auction.',$_SERVER['HTTP_HOST']).'/data/'.$row['idx'].'/'.$row['main_pic'] : '';
				$row = array_merge($row, $auction);
			}
			//$row['json_data'] = urlencode(json_encode($row));
			$arr[$i] = $row;
		}
		//var_dump($query );exit;
		return $arr;
	}

	//옥션 입찰 내역
	function historyApplyLists()
	{
		$loop_scale = 12;
		if($_GET['page']==""){$page = 0;}
		else{$page = $_GET['page'];}

		// $query = "SELECT count(*) from js_auction_apply_list WHERE auction_idx='".$this->dbcon->escape($_GET['auction_idx'])."'";
		// $total_row = $this->dbcon->query_unique_value($query,__FILE__,__LINE__);

		// if($total_row != 0){
		// 	$total_page = (ceil($total_row/$loop_scale)-1);
		// 	if($page > $total_page){$page = $total_page;}

			// $query = "SELECT * FROM js_auction_apply_list WHERE auction_idx='{$this->dbcon->escape($_GET['auction_idx'])}' ORDER BY auction_price desc limit $page, $loop_scale";
			$query = "SELECT aal.*
			, IF(aal.apply_type='B', '입찰', IF(aal.apply_type='D', '구매', '')) apply_type_str
			, IF(am.profile_img='' OR am.profile_img IS NULL, '/template/admin/admin/images/account/basic_profile.png', am.profile_img) profile_img
			, IF(am.nickname='' OR am.nickname IS NULL, IF(m.nickname='' OR m.nickname IS NULL, IF(m.name='' OR m.name IS NULL, m.userid, m.name), m.nickname), am.nickname) bider_name
			, m.userid bider_userid
			FROM js_auction_apply_list aal
			LEFT JOIN js_auction_member am ON am.userno=aal.userno
			LEFT JOIN js_member m ON m.userno=aal.userno
			WHERE aal.auction_idx='{$this->dbcon->escape($_GET['auction_idx'])}' ORDER BY aal.bid_idx DESC ";
			$arr = $this->dbcon->query_all_array($query,__FILE__, __LINE__);
			// var_dump('loop_apply_auction', $arr);
			$this->tpl->assign('loop_apply_auction', $arr);
			$auction_info = $this->dbcon->query_fetch_array("SELECT al.*, ag.main_pic, ag.title goods_title FROM js_auction_list al LEFT JOIN js_auction_goods ag on ag.idx=al.goods_idx WHERE al.auction_idx='{$this->dbcon->escape($_GET['auction_idx'])}' ");
			// var_dump('auction_info', $auction_info); exit;
			$this->tpl->assign('auction_info', $auction_info);
			// for($i=0;$i <= $total_page; $i++){
			// 	$navi_page .= "<a href='".$_SERVER['PHP_SELF']."?pg_mode=historyApplyLists&auction_idx=".$this->dbcon->escape($_GET['auction_idx'])."&page=".$i."'>[".($i+1)."]</a> ";
			// }
			// $this->tpl->assign('navi_page',$navi_page);
		// }
	}

	function srchQry()
	{
		//g.idx, g.title, g.goods_type, g.main_pic, g.animation, g.content, a.auction_idx, a.sell_price, a.start_date, a.end_date, a.auction_title
		$arr = array();
		if(!empty($_POST['auction_idx'])) $arr[] = 'a.auction_idx like \'%'.$this->dbcon->escape($_POST['auction_idx']).'%\' ';
		if(!empty($_POST['title'])) $arr[] = 'g.title like \'%'.$this->dbcon->escape($_POST['title']).'%\' ';
		if(!empty($_POST['auction_title'])) $arr[] = 'a.auction_title like \'%'.$this->dbcon->escape($_POST['auction_title']).'%\' ';
		// if(!empty($_POST['goods_owner'])) $arr[] = 'a.auction_title like \'%'.$this->dbcon->escape($_POST['auction_title']).'%\' ';
		if(!empty($_POST['goods_idx'])) $arr[] = 'g.idx like \'%'.$this->dbcon->escape($_POST['goods_idx']).'%\' ';
		if(!empty($_POST['start_date']) && !empty($_POST['end_date'])) $arr[] = "a.start_date >= '{$this->dbcon->escape($_POST['start_date'])}' AND a.end_date <= '{$this->dbcon->escape($_POST['end_date'])}'";
		$ret = (sizeof($arr) > 0) ? ' && ('.implode(' || ',$arr).') ' : '';

		return $ret;
	}

	function srchQryReport()
	{
		//g.idx, g.title, g.goods_type, g.main_pic, g.animation, g.content, a.auction_idx, a.sell_price, a.start_date, a.end_date, a.auction_title
		$arr = array();
		if(!empty($_POST['report_idx'])) $arr[] = 'r.report_idx = \'%'.$this->dbcon->escape($_POST['report_idx']).'%\' ';
		if(!empty($_POST['report_type'])) $arr[] = 'r.report_type = "'.$this->dbcon->escape($_POST['report_type']).'" ';
		if(!empty($_POST['report_desc'])) $arr[] = 'r.report_desc like \'%'.$this->dbcon->escape($_POST['report_desc']).'%\' ';
		if(!empty($_POST['goods_name'])) $arr[] = 'g.title like \'%'.$this->dbcon->escape($_POST['goods_name']).'%\' ';
		$ret = (sizeof($arr) > 0) ? ' && ('.implode(' || ',$arr).') ' : '';
		return $ret;
	}

	function srchUrl($idx='',$start=TRUE)
	{
		$arr = array();
		//$arr[] = empty($_GET['start']) ? 'start=0' : 'start='.$_GET['start'];
		if(!empty($idx)) { $arr[] = 'idx='.$idx; }
		if(!empty($_GET['s_val'])) {
			if(!empty($_GET['author'])) { $arr[] = 'author='.$_GET['author']; }
			if(!empty($_GET['subject'])) { $arr[] = 'subject='.$_GET['subject']; }
			if(!empty($_GET['contents'])) { $arr[] = 'contents='.$_GET['contents']; }
			if(!empty($_GET['s_val'])) { $arr[] = 's_val='.$_GET['s_val']; }
		}
		$ret = '&'.implode('&',$arr);
		return $ret;
	}


	/**
	 * 경매 상품 폼에서 기존 상품정보를 탬플릿에서 사용할 수 있게 설정합니다.
	 *
	 * @return void
	 * 
	 */
	function editForm()
	{
		// get goods info.
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'row';
		$query['where'] = "where idx='{$this->dbcon->escape($_GET['idx'])}'";
		$row = $this->dbcon->query($query,__FILE__,__LINE__);

        $sql = 'select * from js_member where userno="'.$row['owner_userno'].'" limit 1';
        $memrber_info = $this->dbcon->query_fetch_object($sql);
        $row['owner_username'] = $memrber_info->name;

		// add meta info.
		$row = $this->addAuctionGoodsMeta($_GET['idx'], $row);
		// set data to template
		$this->tpl->assign($row);
	}
	/**
	 * 경매 상품의 확장정보를 추가합니다.
	 *
	 * @param String $goods_idx 상품번호
	 * @param Array $goods_data 상품정보
	 * 
	 * @return Array 확장정보가 추가된 상품정보
	 * 
	 */
	function addAuctionGoodsMeta($goods_idx, $goods_data) {
		$meta = $this->dbcon->query_list_object("SELECT * FROM js_auction_goods_meta WHERE goods_idx='{$this->dbcon->escape($goods_idx)}' ", 'key');
		foreach($meta as $row) {
			$goods_data[$row->meta_key] = $row->meta_val;
		}
		return $goods_data;
	}

	/**
	 * 카테고리 정보를 탬플릿 변수(categories)로 설정합니다. 
	 * 템플릿에서 카테고리 정보를 사용할때 실행 시킵니다.
	 *
	 * @return void
	 * 
	 */
	function setCategory()
	{
		$query = "SELECT * FROM js_auction_goods_type ORDER BY idx ";
		$categories = $this->dbcon->query_list_array($query);
		$this->tpl->assign('categories', $categories);
	}

	/**
	 * 인증마크 정보를 탬플릿 변수(categories)로 설정합니다. 
	 *
	 * @return void
	 * 
	 */
	function setCertificationMark()
	{
		$query = "SELECT * FROM js_auction_certification_marks ORDER BY title ";
		$r = $this->dbcon->query_list_array($query);
		$this->tpl->assign('certification_marks', $r);
	}

	/**
	 * 진행중 경매 정보 탬플릿 변수(auction)로 설정.
	 *
	 * @param String $goods_idx
	 * 
	 * @return void
	 * 
	 */
	function setAuction($goods_idx)
	{
		$query = "SELECT * FROM js_auction_list WHERE goods_idx='{$this->dbcon->escape($goods_idx)}' AND finish_date IS NULL AND finish='N' ORDER BY auction_idx ";
		$r = $this->dbcon->query_fetch_array($query);
		$this->tpl->assign('auction', $r);
	}

	function delete_file_data($file_url, $goods_idx='') {
		$this->delete_external_file($file_url);
		if($goods_idx) {
			$goods_data = (object) $this->get_goods_data($goods_idx);
			$sql_sub = array();
			foreach($goods_data as $key => $val)  {
				if( $val==$file_url ) { $sql_sub[] = "{$key}='' "; }
			}
			if( count($sql_sub)>0 ) {
				$sql = "update {$this->config['table_name']} SET ";
				$sql .= implode(',', $sql_sub);
				$sql .= " WHERE idx='{$this->dbcon->escape($goods_idx)}' "; 
				$this->dbcon->query($sql);
			}
		}
		jsonMsg(1);
	}

	/**
	 * 상품정보 추출
	 *
	 * @param String $goods_idx
	 * 
	 * @return Array
	 * 
	 */
	function get_goods_data($goods_idx) {
		$goods_data = $this->dbcon->query_fetch_array("SELECT * FROM {$this->config['table_name']} WHERE idx='{$this->dbcon->escape($goods_idx)}' ");
		$goods_data = $this->addAuctionGoodsMeta($goods_idx, $goods_data);
		return $goods_data;
	}

	/**
	 * 경매 상품 추가
	 *
	 * @return boid
	 * 
	 */
	function write() {

		// api 호출 방식으로 변경
		jsonMsg(0,'API를 이용해주세요.');
		exit;

		// $gen_idx = true;
		// $n = 1;

		// while($gen_idx) {
		// 	if($n>5) {
		// 		jsonMsg(0,'접속량이 많습니다. 잠시후 다시 시도해주세요.');
		// 	}
		// 	$_POST['idx'] = gen_id();
		// 	$gen_idx = $this->dbcon->query_one("SELECT COUNT(*) FROM {$this->config['table_name']} WHERE idx = '{$this->dbcon->escape($_POST['idx'])}' ");
		// 	if($gen_idx) sleep(1);
		// 	$n++;
		// }

		// // 이미지 저장
		// $image_columns = array('main_pic', 'sub1_pic', 'sub2_pic', 'sub3_pic', 'sub4_pic', 'sub5_pic', 'sub6_pic', 'sub7_pic', 'sub8_pic', 'sub9_pic', 'sub10_pic', 'animation');
		// foreach($image_columns as $c) {
		// 	if($_POST[$c] && strpos($_POST[$c], '/tmp/')!==false) {
		// 		$new_url = $this->S3->copy_tmpfile_to_s3($_POST[$c]) ;
		// 		$this->S3->delete_file_to_s3($_POST[$c]) ;
		// 		$_POST[$c] = $new_url;
		// 	}
		// }
		// // var_dump($_POST); //exit;

		// // main_pic 없으면 sub이미지 중에서 하나 선택하기.
		// if(!$_POST['main_pic']) {
		// 	// $image_columns = array('sub1_pic', 'sub2_pic', 'sub3_pic', 'sub4_pic', 'sub5_pic', 'sub6_pic', 'sub7_pic', 'sub8_pic', 'sub9_pic', 'sub10_pic'); // 'main_pic', , 'animation'
		// 	foreach($image_columns as $c) {
		// 		if($_POST[$c]) {
		// 			$_POST['main_pic'] = $_POST[$c];
		// 			break;
		// 		}
		// 	}
		// }

		// // price = base price
		// if(!$_POST['price']) {
		// 	$_POST['price'] = $_POST['base_price'];
		// }

		// // 정보 저장
		// $query = array();
		// if($this->bWrite($query, $_POST)) {
		// 	// meta정보 저장
		// 	$this->saveGoodsMetaData($_POST['idx'], $_POST);
		// 	jsonMsg(1);
		// } else {
		// 	jsonMsg(0,'저장하지 못했습니다.');
		// }
	}
	
	function edit() {

		// api 호출 방식으로 변경
		jsonMsg(0,'API를 이용해주세요.');
		exit;

		// $goods_data = $this->get_goods_data($_POST['idx']);

		// // 삭제해야할 이전 파일
		// $to_be_delete = array();

		// // 이미지 저장
		// $image_columns = array('main_pic', 'sub1_pic', 'sub2_pic', 'sub3_pic', 'sub4_pic', 'sub5_pic', 'sub6_pic', 'sub7_pic', 'sub8_pic', 'sub9_pic', 'sub10_pic', 'animation');
		// foreach($image_columns as $c) {
		// 	if($_POST[$c] && strpos($_POST[$c], '/tmp/')!==false) {
		// 		$new_url = $this->S3->copy_tmpfile_to_s3($_POST[$c]) ;
		// 		$this->S3->delete_file_to_s3($_POST[$c]) ;
		// 		$_POST[$c] = $new_url;
		// 		if($goods_data[$c]) $to_be_delete[] = $goods_data[$c];
		// 		if($goods_data[$c]==$goods_data['main_pic']) $_POST['main_pic'] = '';
		// 	}
		// }
		// // var_dump($_POST); //exit;

		// // main_pic 없으면 sub이미지 중에서 하나 선택하기.
		// if(!$_POST['main_pic']) {
		// 	$image_columns = array('sub1_pic', 'sub2_pic', 'sub3_pic', 'sub4_pic', 'sub5_pic', 'sub6_pic', 'sub7_pic', 'sub8_pic', 'sub9_pic', 'sub10_pic'); // 'main_pic', , 'animation'
		// 	foreach($image_columns as $c) {
		// 		if($_POST[$c]) {
		// 			$_POST['main_pic'] = $_POST[$c];
		// 			break;
		// 		}
		// 	}
		// }

		// // price = base price
		// $_POST['price'] = $_POST['base_price'];

		// // 정보 저장
		// $query = array();
		// $query['where'] = "where idx='{$this->dbcon->escape($_POST['idx'])}'";
		// if($this->bEdit($query,$_POST)) {
		// 	foreach($to_be_delete as $row) {
		// 		$this->S3->delete_file_to_s3($row) ;
		// 	}
		// 	$this->saveGoodsMetaData($_POST['idx'], $_POST);
		// 	jsonMsg(1);
		// }
		// else {
		// 	jsonMsg(0,'저장하지 못했습니다.');
		// }

	}

	function saveGoodsMetaData($goods_idx, $data) {
		// meta정보 저장
		foreach($data as $key => $val) {
			if(strpos($key, 'meta_')===0) {
				$this->dbcon->query("INSERT INTO js_auction_goods_meta SET goods_idx='{$this->dbcon->escape($goods_idx)}', meta_key='{$this->dbcon->escape($key)}', meta_val='{$this->dbcon->escape($val)}' ON DUPLICATE KEY UPDATE meta_val='{$this->dbcon->escape($val)}' ");
			}
		}
	}
}

function querySet($arr)
{
	global $dbcon;
	$qry = array();
	if(isset($arr['active']))  { $qry[] = "active='{$dbcon->escape($arr['active'])}'"; }
	if(isset($arr['pack_info']))  { $qry[] = "pack_info='{$dbcon->escape($arr['pack_info'])}'"; }
	if(isset($arr['explicit_content']))  { $qry[] = "explicit_content='{$dbcon->escape($arr['explicit_content'])}'"; }
	if(isset($arr['goods_type']))  { $qry[] = "goods_type='{$dbcon->escape($arr['goods_type'])}'"; }
	if(isset($arr['creator_userno']))  { $qry[] = "creator_userno='{$dbcon->escape($arr['creator_userno'])}'"; }
	if(isset($arr['owner_userno']))  { $qry[] = "owner_userno='{$dbcon->escape($arr['owner_userno'])}'"; }
	if(isset($arr['title']))  { $qry[] = "title='{$dbcon->escape($arr['title'])}'"; }
	if(isset($arr['main_pic']))  { $qry[] = "main_pic='{$dbcon->escape($arr['main_pic'])}'"; }
	if(isset($arr['sub1_pic']))  { $qry[] = "sub1_pic='{$dbcon->escape($arr['sub1_pic'])}'"; }
	if(isset($arr['sub2_pic']))  { $qry[] = "sub2_pic='{$dbcon->escape($arr['sub2_pic'])}'"; }
	if(isset($arr['sub3_pic']))  { $qry[] = "sub3_pic='{$dbcon->escape($arr['sub3_pic'])}'"; }
	if(isset($arr['sub4_pic']))  { $qry[] = "sub4_pic='{$dbcon->escape($arr['sub4_pic'])}'"; }
	if(isset($arr['sub5_pic']))  { $qry[] = "sub5_pic='{$dbcon->escape($arr['sub5_pic'])}'"; }
	if(isset($arr['sub6_pic']))  { $qry[] = "sub6_pic='{$dbcon->escape($arr['sub6_pic'])}'"; }
	if(isset($arr['sub7_pic']))  { $qry[] = "sub7_pic='{$dbcon->escape($arr['sub7_pic'])}'"; }
	if(isset($arr['sub8_pic']))  { $qry[] = "sub8_pic='{$dbcon->escape($arr['sub8_pic'])}'"; }
	if(isset($arr['sub9_pic']))  { $qry[] = "sub9_pic='{$dbcon->escape($arr['sub9_pic'])}'"; }
	if(isset($arr['sub10_pic']))  { $qry[] = "sub10_pic='{$dbcon->escape($arr['sub10_pic'])}'"; }
	if(isset($arr['animation']))  { $qry[] = "animation='{$dbcon->escape($arr['animation'])}'"; }
	if(isset($arr['content']))  { $qry[] = "content='{$dbcon->escape($arr['content'])}'"; }
	if(isset($arr['nft_symbol']))  { $qry[] = "nft_symbol='{$dbcon->escape($arr['nft_symbol'])}'"; }
	if(isset($arr['nft_blockchain']))  { $qry[] = "nft_blockchain='{$dbcon->escape($arr['nft_blockchain'])}'"; }
	if(isset($arr['nft_id']))  { $qry[] = "nft_id='{$dbcon->escape($arr['nft_id'])}'"; }
	if(isset($arr['nft_unlockable_contents']))  { $qry[] = "nft_unlockable_contents='{$dbcon->escape($arr['nft_unlockable_contents'])}'"; }
	if(isset($arr['nft_max_supply']))  { $qry[] = "nft_max_supply='{$dbcon->escape($arr['nft_max_supply'])}'"; }
	if(isset($arr['nft_buildable']))  { $qry[] = "nft_buildable='{$dbcon->escape($arr['nft_buildable'])}'"; }
	if(isset($arr['nft_tokenuri']))  { $qry[] = "nft_tokenuri='{$dbcon->escape($arr['nft_tokenuri'])}'"; }
	if(isset($arr['nft_txnid']))  { $qry[] = "nft_txnid='{$dbcon->escape($arr['nft_txnid'])}'"; }
	if(isset($arr['nft_file_type']))  { $qry[] = "nft_file_type='{$dbcon->escape($arr['nft_file_type'])}'"; }
	if(isset($arr['minting_quantity']))  { $qry[] = "minting_quantity='{$dbcon->escape($arr['minting_quantity'])}'"; }
	if(isset($arr['base_price']))  { $qry[] = "base_price='{$dbcon->escape($arr['base_price'])}'"; }
	if(isset($arr['price_symbol']))  { $qry[] = "price_symbol='{$dbcon->escape($arr['price_symbol'])}'"; }
	if(isset($arr['price']))  { $qry[] = "price='{$dbcon->escape($arr['price'])}'"; }
	if(isset($arr['cnt_view']))  { $qry[] = "cnt_view='{$dbcon->escape($arr['cnt_view'])}'"; }
	if(isset($arr['cnt_like']))  { $qry[] = "cnt_like='{$dbcon->escape($arr['cnt_like'])}'"; }
	if(isset($arr['best_item']))  { $qry[] = "best_item='{$dbcon->escape($arr['best_item'])}'"; }
	if(isset($arr['event_bn']))  { $qry[] = "event_bn='{$dbcon->escape($arr['event_bn'])}'"; }
	if(isset($arr['royalty']))  { $qry[] = "royalty='{$dbcon->escape($arr['royalty'])}'"; }
	if(isset($arr['goods_grade']))  { $qry[] = "goods_grade='{$dbcon->escape($arr['goods_grade'])}'"; }

	if($arr['pg_mode'] == 'write' && isset($arr['idx'])) { $qry[] = "idx='{$dbcon->escape($arr['idx'])}'"; }
	if($arr['pg_mode'] == 'write') { $qry[] = "reg_date=NOW()"; }
	if($arr['pg_mode'] == 'edit') { $qry[] = "mod_date=NOW()"; }
	$qry = implode(',',$qry);
	return $qry;
}
