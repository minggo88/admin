<?php /* Template_ 2.2.6 2022/12/09 17:06:26 /home/ubuntu/www/admin/www/template/admin/admin/menu.html 000010638 */ ?>
<nav class="navbar-default navbar-static-side" role="navigation">
<div class="sidebar-collapse">
<ul class="nav metismenu" id="side-menu">
<li class="nav-header">
<div class="dropdown profile-element text-center">
<a href="/home"><img alt="image" src="/template/admin/admin/images/admin_logo.png" style="width:170px;" /></a>
<a data-toggle="dropdown" class="dropdown-toggle" href="#">
<span class="clear"> <span class="block m-t-md"> <strong class="font-bold">Manager : <?php echo $_SESSION["ADMIN_ID"]?></strong></span>
<span class="text-muted text-xs block m-t-xs"><?php echo $_SESSION["ADMIN_NAME"]?><b class="caret"></b></span> </span> </a>
<?php if($_SESSION["ADMIN_ID"]){?>
<ul class="dropdown-menu animated fadeInRight m-t-xs">
<!-- <li><a href="/admin/configAdmin.php?pg_mode=mypage">마이페이지</a></li> -->
<li><a href="/logout">Logout</a></li>
</ul>
<?php }?>
</div>
<div class="logo-element text-center">
<a href="/home"><img alt="image" src="/template/admin/admin/images/admin_logo.png" style="width:50px;" /></a>
</div>
</li>
<li <?php if($_SERVER["SCRIPT_NAME"]=='/admin/index.php'||$_SERVER["SCRIPT_NAME"]=='/'||$_SERVER["SCRIPT_NAME"]=='/index.php'){?>class="active"<?php }?>>
<a href="/admin/"><i class="fa fa-th-large"></i> <span class="nav-label">관리자메인</span></a>
</li>
<?php if($TPL_VAR["admin_right"]["right_basic"]=='1'){?>
<li <?php if(($_SERVER["SCRIPT_NAME"]=='/admin/configAdmin.php'&&$_SERVER["REQUEST_URI"]!='/admin/configAdmin.php?pg_mode=mypage')||$_SERVER["SCRIPT_NAME"]=='/admin/kpidataAdmin.php'){?>class="active"<?php }?>>
<a href="#"><i class="fa fa-gear"></i> <span class="nav-label">기본관리</span><span class="fa arrow"></span></a>
<ul class="nav nav-second-level collapse">
<li <?php if(($_SERVER["SCRIPT_NAME"]=='/admin/configAdmin.php'&&$_SERVER["REQUEST_URI"]!='/admin/configAdmin.php?pg_mode=mypage')){?>class="active"<?php }?>><a href="/admin/configAdmin.php">관리자설정</a></li>
</ul>
</li>
<?php }?>
<?php if($TPL_VAR["admin_right"]["right_member"]=='1'||$TPL_VAR["admin_right"]["right_point"]=='1'){?>
<li <?php if($_SERVER["SCRIPT_NAME"]=='/member/admin/memberAdmin.php'||$_SERVER["SCRIPT_NAME"]=='/member/admin/memberWithdraw.php'||$_SERVER["SCRIPT_NAME"]=='/member/admin/memberConfirm.php'){?>class="active"<?php }?>>
<a href="#"><i class="fa fa-users"></i> <span class="nav-label">회원관리</span><span class="fa arrow"></span></a>
<ul class="nav nav-second-level collapse">
<li <?php if($_SERVER["SCRIPT_NAME"]=='/member/admin/memberAdmin.php'&&($_GET["pg_mode"]=='customers')){?>class="active"<?php }?>><a href="/member/admin/memberAdmin.php?pg_mode=customers">일반회원</a></li>
<?php if($TPL_VAR["admin_right"]["right_point"]=='1'){?>
<li <?php if($_SERVER["SCRIPT_NAME"]=='/member/admin/memberConfirm.php'){?>class="active"<?php }?>><a href="/member/admin/memberConfirm.php">인증리스트</a></li>
<?php }?>
</ul>
</li>
<?php }?>
<?php if($TPL_VAR["admin_right"]["right_auction"]=='1'){?>
<li <?php if($_SERVER["SCRIPT_NAME"]=='/auction/admin/auctionAdmin.php'||$_SERVER["SCRIPT_NAME"]=='/auction/admin/auctionHistoryAdmin.php'||$_SERVER["SCRIPT_NAME"]=='/auction/admin/auctionGoodsAdmin.php'||$_SERVER["SCRIPT_NAME"]=='/auction/admin/auctionReportAdmin.php'||$_SERVER["SCRIPT_NAME"]=='/auction/admin/certificationMarksAdmin.php'){?>class="active"<?php }?>>
<a href="#"><i class="fa fa-gavel"></i> <span class="nav-label">경매 관리</span><span class="fa arrow"></span></a>
<ul class="nav nav-second-level collapse">
<li <?php if($_SERVER["SCRIPT_NAME"]=='/auction/admin/auctionGoodsAdmin.php'){?>class="active"<?php }?>><a href="/auction/admin/auctionGoodsAdmin.php">상품 목록</a></li>
<li <?php if($_SERVER["SCRIPT_NAME"]=='/auction/admin/auctionAdmin.php'){?>class="active"<?php }?>><a href="/auction/admin/auctionAdmin.php">경매 목록</a></li>
<li <?php if($_SERVER["SCRIPT_NAME"]=='/auction/admin/auctionHistoryAdmin.php'){?>class="active"<?php }?>><a href="/auction/admin/auctionHistoryAdmin.php">입찰 내역</a></li>
<li <?php if($_SERVER["SCRIPT_NAME"]=='/auction/admin/auctionReportAdmin.php'){?>class="active"<?php }?>><a href="/auction/admin/auctionReportAdmin.php">신고 내역</a></li>
<li <?php if($_SERVER["SCRIPT_NAME"]=='/auction/admin/certificationMarksAdmin.php'){?>class="active"<?php }?>><a href="/auction/admin/certificationMarksAdmin.php">인증(마크) 목록</a></li>
</ul>
</li>
<?php }?>
<?php if($TPL_VAR["admin_right"]["right_goods"]=='1'){?>
<?php }?>
<?php if($TPL_VAR["admin_right"]["right_order"]=='1'){?>
<li <?php if($_SERVER["SCRIPT_NAME"]=='/coins/admin/inoutAdmin.php'||$_SERVER["SCRIPT_NAME"]=='/coins/admin/memberAdmin.php'||$_SERVER["SCRIPT_NAME"]=='/coins/admin/tradehistoryAdmin.php'||$_SERVER["SCRIPT_NAME"]=='/coins/admin/orderHistoryAdmin.php'||$_SERVER["SCRIPT_NAME"]=='/coins/admin/coinAdmin.php'){?>class="active"<?php }?> >
<a href="#"><i class="fa fa-bitcoin"></i> <span class="nav-label">거래소 관리</span><span class="fa arrow"></span></a>
<ul class="nav nav-second-level collapse">
<?php if($TPL_VAR["admin_right"]["right_goods"]=='1'){?>
<li <?php if($_SERVER["SCRIPT_NAME"]=='/coins/admin/coinAdmin.php'){?>class="active"<?php }?>>
<a href="/coins/admin/coinAdmin.php" id="feeConfig">상품 목록</a>
</li>
<?php }?>
<!-- <li <?php if($_SERVER["SCRIPT_NAME"]=='/coins/admin/tradefeeAdmin.php'){?>class="active"<?php }?>>
<a href="/coins/admin/tradefeeAdmin.php" id="feeConfig">거래수수료관리</a>
</li> -->
<li <?php if($_SERVER["SCRIPT_NAME"]=='/coins/admin/inoutAdmin.php'&&($_GET["symbol"]=='USD'&&$_GET["txn_type"]=='R')){?>class="active"<?php }?>>
<a href="/coins/admin/inoutAdmin.php?pg_mode=list&symbol=KRW&txn_type=R">입금 내역</a>
</li>
<li <?php if($_SERVER["SCRIPT_NAME"]=='/coins/admin/inoutAdmin.php'&&($_GET["symbol"]=='USD'&&$_GET["txn_type"]=='W')){?>class="active"<?php }?>>
<a href="/coins/admin/inoutAdmin.php?pg_mode=list&symbol=KRW&txn_type=W">출금 내역</a>
</li>
<li <?php if($_SERVER["SCRIPT_NAME"]=='/coins/admin/memberAdmin.php'){?>class="active"<?php }?>>
<a href="/coins/admin/memberAdmin.php" id="memberAdmin">회원별 상품보유수량</a>
<!-- 회원 잔액 -->
</li>
<li <?php if($_SERVER["SCRIPT_NAME"]=='/coins/admin/tradehistoryAdmin.php'){?>class="active"<?php }?>><a href="/coins/admin/tradehistoryAdmin.php?pg_mode=transaction">거래내역확인 </a>
</li>
<li <?php if($_SERVER["SCRIPT_NAME"]=='/coins/admin/orderHistoryAdmin.php'){?>class="active"<?php }?>><a href="/coins/admin/orderHistoryAdmin.php?pg_mode=list">주문내역확인 </a>
</li>
</ul>
</li>
<?php }?>
<?php if($TPL_VAR["admin_right"]["right_wallet"]=='1'){?>
<li <?php if($_SERVER["SCRIPT_NAME"]=='/wallet/admin/ledgerAdmin.php'){?>class="active"<?php }?>>
<a href="#"><i class="fa fa-credit-card"></i> <span class="nav-label">지갑관리</span><span class="fa arrow"></span></a>
<ul class="nav nav-second-level collapse">
<li <?php if($_SERVER["SCRIPT_NAME"]=='/coins/admin/memberAdmin.php'){?>class="active"<?php }?>>
<a href="/member/admin/memberAdmin.php?pg_mode=balance" id="memberAdmin">회원잔액</a>
</li>
<li <?php if($_SERVER["SCRIPT_NAME"]=='/wallet/admin/ledgerAdmin.php'){?>class="active"<?php }?> >
<!-- <i class="fa fa-database"></i> -->
<a href="/wallet/admin/ledgerAdmin.php?pg_mode=transaction&symbol=BTC">거래내역 </a>
</li>
</ul>
</li>
<?php }?>
<?php if($TPL_VAR["admin_right"]["right_community"]=='1'){?>
<li <?php if($_SERVER["SCRIPT_NAME"]=='/bbs/admin/bbsAdmin.php'||$_SERVER["SCRIPT_NAME"]=='/contents/admin/contentsAdmin.php'){?>class="active"<?php }?>>
<a href="#"><i class="fa fa-desktop"></i> <span class="nav-label">커뮤니티관리</span><span class="fa arrow"></span></a>
<ul class="nav nav-second-level collapse">
<li <?php if($_SERVER["SCRIPT_NAME"]=='/bbs/admin/bbsAdmin.php'&&$_GET["bbscode"]=='NOTICE'){?>class="active"<?php }?>><a href="/bbs/admin/bbsAdmin.php?pg_mode=list&bbscode=NOTICE">공지사항</a></li>
<li <?php if($_SERVER["SCRIPT_NAME"]=='/bbs/admin/bbsAdmin.php'&&$_GET["bbscode"]=='NEWS'){?>class="active"<?php }?>><a href="/bbs/admin/bbsAdmin.php?pg_mode=list&bbscode=NEWS">뉴스</a></li>
<li <?php if($_SERVER["SCRIPT_NAME"]=='/bbs/admin/bbsAdmin.php'&&$_GET["bbscode"]=='AUCTION-NEWS'){?>class="active"<?php }?>><a href="/bbs/admin/bbsAdmin.php?pg_mode=list&bbscode=AUCTION-NEWS">공동구매 및 경매소식</a></li>
<?php if($TPL_VAR["admin_right"]["right_contents"]=='1'){?>
<li <?php if($_SERVER["SCRIPT_NAME"]=='/contents/admin/contentsAdmin.php'){?>class="active"<?php }?>>
<a href="/contents/admin/contentsAdmin.php">컨텐츠관리</a>
</li>
<?php }?>
</ul>
</li>
<?php }?>
<?php if($TPL_VAR["admin_right"]["right_cs"]=='1'){?>
<li <?php if($_SERVER["SCRIPT_NAME"]=='/cscenter/admin/faqAdmin.php'||$_SERVER["SCRIPT_NAME"]=='/mypage/admin/mtomAdmin.php'||$_SERVER["SCRIPT_NAME"]=='/cscenter/admin/requestAdmin.php'){?>class="active"<?php }?>>
<a href="#"><i class="fa fa-headphones"></i> <span class="nav-label">CS 관리</span><span class="fa arrow"></span></a>
<ul class="nav nav-second-level collapse">
<li <?php if($_SERVER["SCRIPT_NAME"]=='/cscenter/admin/faqAdmin.php'){?>class="active"<?php }?>><a href="/cscenter/admin/faqAdmin.php">FAQ 관리</a></li>
<li <?php if($_SERVER["SCRIPT_NAME"]=='/mypage/admin/mtomAdmin.php'){?>class="active"<?php }?>><a href="/mypage/admin/mtomAdmin.php">1:1 관리</a></li>
</ul>
</li>
<?php }?>
<?php if($TPL_VAR["admin_right"]["right_statistics"]=='1'){?>
<li <?php if($_SERVER["SCRIPT_NAME"]=='/laboratory/admin/labAdmin.php'){?>class="active"<?php }?>>
<a href="#"><i class="fa fa-bar-chart-o"></i> <span class="nav-label">통계</span><span class="fa arrow"></span></a>
<ul class="nav nav-second-level collapse">
<li <?php if($_SERVER["SCRIPT_NAME"]=='/laboratory/admin/labAdmin.php'&&$_GET["pg_mode"]=='stat_income'){?>class="active"<?php }?>><a href="/laboratory/admin/labAdmin.php?pg_mode=stat_income">거래소 수익</a></li>
<li <?php if($_SERVER["SCRIPT_NAME"]=='/laboratory/admin/labAdmin.php'&&$_GET["pg_mode"]=='kpi_total'){?>class="active"<?php }?>><a href="/laboratory/admin/labAdmin.php?pg_mode=kpi_total">거래소 상품별 총량</a></li>
</ul>
</li>
<?php }?>
<?php if($TPL_VAR["admin_right"]["right_shareholder"]=='1'){?>
<li <?php if($_SERVER["SCRIPT_NAME"]=='/shareholder/admin/shareholderAdmin.php'){?>class="active"<?php }?>>
<a href="/shareholder/admin/shareholderAdmin.php"><i class="fa fa-bar-chart-o"></i> <span class="nav-label">주주명부</span></a>
</li>
<?php }?>
</ul>
</div>
</nav>