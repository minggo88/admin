
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>MorrowStock</title>
<meta name="keywords" content="" />
<meta name="description" content="Connecting People, Moving Earth" />
<meta name="author" content="info.teaplate@gmail.com" />
<meta name="copyright" content="Copyright ⓒ Exbds  2020 All right reserved" />
<meta name="build" content="2020.05.24">
<meta name="content-language"content="en">
<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta property="og:title" content="KMCSE">
<meta property="og:description" content="KMCSE">
<meta property="og:image" content="/kakao-kmcse-logo.png">
<link rel="stylesheet" href="/css/common.css" />
<link rel="icon" type="image/png" href="/img/favicon.png">
<link rel="icon" type="image/png" href="/img/favicon_16.png" sizes="16x16">
<link rel="icon" type="image/png" href="/img/favicon_24.png" sizes="24x24">
<link rel="icon" type="image/png" href="/img/favicon_64.png" sizes="64x64">
<link rel="icon" type="image/png" href="/img/favicon_72.png" sizes="72x72">
<link rel="icon" type="image/png" href="/img/favicon_128.png" sizes="128x128">
<link rel="icon" type="image/png" href="/img/favicon_180.png" sizes="180x180">
<link rel="icon" type="image/png" href="/template/admin/favicon/favicon_196.png" sizes="196x196">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,400i,600,700|Cabin:400,600,700" rel="stylesheet">
<!-- Vendor CSS -->
<link rel="stylesheet" href="/template/admin/smc/vendor/tether/tether.min.css" />
<link rel="stylesheet" href="/template/admin/smc/vendor/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="/template/admin/smc/css/fonts/express-icons.css" />
<link rel="stylesheet" href="/template/admin/smc/vendor/font-awesome/css/font-awesome.min.css">
<link href="/template/admin/trade/webfont/cryptocoins.css" rel="stylesheet">
<link rel="stylesheet" href="/template/admin/smc/vendor/bootstrap/css/glyphicon.css" />
<link rel="stylesheet" href="/template/admin/smc/vendor/ion-icons/css/ionicons.min.css" />
<link rel="stylesheet" href="/template/admin/smc/vendor/owl-carousel/owl.theme.css" />
<link rel="stylesheet" href="/template/admin/smc/vendor/owl-carousel/owl.carousel.css" />
<link rel="stylesheet" href="/template/admin/smc/vendor/magnific-popup/magnific-popup.css" />
<link rel="stylesheet" href="/template/admin/smc/vendor/lite-tooltip/css/litetooltip.css" />
<!-- Smart Forms CSS -->
<link href="/template/admin/smc/smartforms/Templates/css/smart-loader.css" rel="stylesheet" />
<link href="/template/admin/smc/smartforms/Templates/css/smart-addons.css" rel="stylesheet" />
<link href="/template/admin/smc/smartforms/Templates/css/smart-forms.css" rel="stylesheet" />
<!-- Theme CSS -->
<link href="/template/admin/smc/css/main.css" rel="stylesheet" />
<link href="/template/admin/smc/css/main-shortcodes.css" rel="stylesheet" />
<link href="/template/admin/smc/css/header.css" rel="stylesheet" />
<link href="/template/admin/smc/css/form-element.css" rel="stylesheet" />
<link href="/template/admin/smc/css/animation.css" rel="stylesheet" />
<link href="/template/admin/smc/css/index.css" rel="stylesheet" />
<link href="/template/admin/smc/css/streamline-icon.css" rel="stylesheet" />
<link href="/template/admin/smc/css/font-icons.css" rel="stylesheet" />
<link href="/template/admin/smc/css/responsive.css" rel="stylesheet" />
<link href="/template/admin/smc/css/utilities.css" rel="stylesheet" />
<link href="/template/admin/smc/css/skins/default.css" rel="stylesheet" />
<!-- Current Page CSS -->
<link rel="stylesheet" href="/template/admin/smc/vendor/rs-plugin/css/settings.css">
<link rel="stylesheet" href="/template/admin/smc/vendor/rs-plugin/css/layers.css">
<link rel="stylesheet" href="/template/admin/smc/vendor/rs-plugin/css/navigation.css">
<!-- Theme Custom CSS -->
<link rel="stylesheet" href="/template/admin/smc/css/custom.css">
<!-- React datepicker CSS -->
<link rel="stylesheet" href="/template/admin/smc/css/react-datepicker.css">
<!-- Jquery toastr CSS -->
<link rel="stylesheet" href="/template/admin/smc/css/plugins/toastr/toastr.min.css">
<!-- Style Swicher -->
<link href="/template/admin/smc/vendor/style-switcher/style-switcher.css" rel="stylesheet" />
<link href="/template/admin/smc/vendor/style-switcher/bootstrap-colorpicker/css/bootstrap-colorpicker.css" rel="stylesheet" />
<link href="/template/admin/script/plug_in/popup/popup.css" rel="stylesheet" >
<!-- SCC CSS -->
<link rel="stylesheet" href="/template/admin/smc/css/scc.css">
<link rel="stylesheet" href="/template/admin/smc/css/coin.css">
<link href="https://fonts.googleapis.com/css?family=Noto+Sans" rel="stylesheet">
</head>
<body>
<div class="wrapper">
<div id="popup_20190201" style="display:none;padding:0px;z-index:0;background:#fff;">
<img src="/template/admin/smc/img/pop.png">
</div>
<!--Header-->
<header id="header" class="header-narrow header-full-width" >
    
    <!-- smc/js_header_main.html _GET.device_type : <?php echo _GET.device_type ?> -->
    
    <?php if($_GET['device_type']=='app') { ?>
    <div class="header-body" id="menu-app">
        <div class="header-container container">
            <div class="header-row app">
                <div class="header-logo">
                    <a href="/home">홈</a>
                </div>
                <div class="header-column  justify-content-center" >
                    <div class="header-search search_box">
                        <form id="searchForm" action="/trade/total" method="get" novalidate="novalidate">
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <button class="btn btn-light" type="submit"><i class="fa fa-search"></i></button>
                                </span>
                                <input type="text" class="form-control" name="name" id="q" placeholder="실시간 인기종목 보러가기..." required="" style="width: 100%;" value="">
                            </div>
                        </form>
                    </div>
                </div>
                <div class="header-menu">
                    <?php if($_SESSION['USER_ID']) { ?>
                    <a href="/logout">로그아웃</a>
                    <?php } else { ?>
                    <a href="/login">로그인</a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
    <?php if($_GET['device_type']!='app') { ?>
<div class="header-body">
<div class="header-container container">
<div class="header-row">
<div class="header-column  justify-content-left" >
<div class="header-row">
<div class="header-logo">
<a href="/home"><img alt="MorrowStock" src="/template/admin/smc/img/main/morrow-logo.png"></a>
</div>
</div>
</div>
<div class="header-column justify-content-center">
<div class="header-row">
<div class="header-nav header-nav-dark-dropdown header-nav-top-line justify-content-end">
<div class="header-nav-main header-nav-main-effect-2 header-nav-main-sub-effect-1">
<nav class="gnb-menu collapse">
<ul class="nav nav-pills" id="mainNav">
<li class="dropdown">
<a class="dropdown-item dropdown-toggle active" href="/home"><img src="/template/admin/smc/img/m_home.png" class="img_menu" /> Home </a>
</li>
<li class="dropdown dropdown-mega dropdown-mega-column-3">
<a class="dropdown-item dropdown-toggle" href="/trade/jin"><img src="/template/admin/smc/img/m_trade.png" class="img_menu" />거래소</a>
<!--{
<ul class="dropdown-menu">
<li>
<div class="dropdown-mega-content">
<div class="row">
<div class="col">
<a class="dropdown-item" href="/trade/jin"><span class="icon-24 menu icon-24-jin"></span> 진페이 코인</a>
</div>
<div class="col">
<a class="dropdown-item" href="/trade/btc"><span class="icon-24 menu icon-24-btc"></span> 비트코인</a>
</div>
<div class="col">
<a class="dropdown-item" href="/trade/eth"><span class="icon-24 menu icon-24-eth"></span> 이더리움</a>
</div>
<div class="col">
<a class="dropdown-item" href="/trade/ltc"><span class="icon-24 menu icon-24-ltc"></span> 라이트코인</a>
</div>
</div>
<div class="row">
<div class="col">
<a class="dropdown-item" href="/trade/bch"><span class="icon-24 menu icon-24-bch"></span> 비트코인캐시</a>
</div>
<div class="col">
<a class="dropdown-item" href="/trade/etc"><span class="icon-24 menu icon-24-etc"></span> 이더리움클래식</a>
</div>
<div class="col">
<a class="dropdown-item" href="/trade/qtum"><span class="icon-24 menu icon-24-qtum"></span> 퀀텀</a>
</div>
<div class="col">
<a class="dropdown-item" href="/trade/apc"><span class="icon-24 menu icon-24-apc"></span> 올패스 코인</a>
</div>
</div>
</div>
</li>
</ul>
}-->
</li>
<li class="dropdown">
<a class="dropdown-item dropdown-toggle" href="/trade/wallet"><img src="/template/admin/smc/img/m_wallet.png" class="img_menu" /> 입출금</a>
<ul class="dropdown-menu">
<li><a class="dropdown-item" href="/trade/wallet">전자지갑</a></li>
<li><a class="dropdown-item" href="/trade/deposit">입금</a></li>
<!--<li><a class="dropdown-item" href="/trade/exchange">Exchange</a></li>-->
<li><a class="dropdown-item" href="/trade/withdrawal">출금</a></li>
</ul>
</li>
<li class="dropdown">
<a class="dropdown-item dropdown-toggle " href="/faq"><img src="/template/admin/smc/img/m_support.png" class="img_menu" /> 고객지원 </a>
<ul class="dropdown-menu">
<li><a class="dropdown-item" href="/faq">F.A.Q</a></li>
<li><a class="dropdown-item" href="/notice">공지사항</a></li>
<!-- <li><a class="dropdown-item" href="/mbnews">뉴스</a></li> -->
<!-- <li><a class="dropdown-item" href="/coinnews">코인뉴스</a></li> -->
<!-- <li><a class="dropdown-item" href="/gallery">포토뉴스</a></li> -->
<!-- <li><a class="dropdown-item" href="/mtom">일대일문의</a></li> -->
</ul>
</li>
<li class="dropdown">
<a class="dropdown-item dropdown-toggle " href="/certification"><img src="/template/admin/smc/img/m_myaccount.png" class="img_menu" /> 계정관리 </a>
<ul class="dropdown-menu">
<!-- <li><a class="dropdown-item" href="/edit">정보수정</a></li> -->
<li><a class="dropdown-item" href="/certification">인증관리</a></li>
<!-- <li><a class="dropdown-item" href="/editpin">거래보안번호 변경</a></li> -->
<li><a class="dropdown-item" href="/histories">거래내역</a></li>
</ul>
</li>
<li><a class="dropdown-item" href="#">|</a></li>
<li><a class="dropdown-item " href="/login"><img src="/template/admin/smc/img/m_login.png" class="img_menu" /> 로그인</a></li>
<!-- <li><a class="dropdown-item " href="/join"><img src="/template/admin/smc/img/m_join.png" class="img_menu" /> 회원가입</a></li> -->
</ul>
</nav>
</div>
</div>
</div>
</div>
<div class="header-column justify-content-end">
<div class="header-row">
<div class="header-nav header-nav-top-line justify-content-end">
<div class="clearfix pull-right">
<ul id="chlang" class="list-inline mb-0">
<li><a href="/en" data-lang="en"><div class="flag flag-us"></div></a> </li>
<li><a href="/cn" data-lang="cn"><div class="flag flag-cn"></div></a> </li>
<li><a href="/kr" data-lang="kr"><div class="flag flag-kr"></div></a> </li>
</ul>
</div>
<div class="header-nav-main header-nav-main-effect-2 header-nav-main-sub-effect-1">
<nav class="collapse">
<ul class="nav nav-pills" id="mainNav">
<li class="dropdown header-search-wrap">
<a class="dropdown-item dropdown-toggle pr-5" href="#"><i class="fa fa-search3 fs-18"></i> </a>
<ul class="dropdown-menu">
<li>
<div class="header-search">
<form id="searchForm" action="pages-search-result.html" method="get" novalidate="novalidate">
<div class="input-group">
<input type="text" class="form-control" name="q" id="q" placeholder="Search..." required="" value="">
<span class="input-group-btn">
<button class="btn btn-light" type="submit"><i class="fa fa-search"></i></button>
</span>
</div>
</form>
</div>
</li>
</ul>
</li>
<li class="dropdown dropdown-mega dropdown-mega-shop" id="headerShop"></li>
</ul>
</nav>
</div>
<div class="header-nav-main header-nav-mobile" id="menu-mobile" <!--{? _GET.device_type=='mobile'}-->style="display:none !important"<!--{/}--> >
<ul class="nav">
<li class="mega-shop" style="display:none;">
<a class="dropdown-item" href="/message">
<i class="fa fa-credit-card fs-18"></i>
<span class="cart-items"></span>
</a>
</li>
<li class="">
<div class="btn-area">
<button class="btn header-btn-collapse-nav minimalize-styl-2 navbar-toggle collapsed" id="showRight">
<i class="fa fa-bars"></i>
</button>
</div>
</li>
</ul>
<nav class="cbp-spmenu-dim" id="hideNav">
</nav>
<nav class="cbp-spmenu cbp-spmenu-vertical cbp-spmenu-right" id="cbp-spmenu-s2">
<div class="nav_close">
<h3 id="closeMenu" style="cursor:pointer"><i class="fa fa-arrow-right"></i> </h3>
</div>
<div class="nav_username">
</div>
<div class="top_button">
<a href="//trade.kmcse.com?ret_url=Ly90cmFkZS5leGJkcy5pby8=" class="btn btn-primary"><img src="/template/admin/smc/img/login.png" class="m_menu" /> login</a>

</div>
<div class="nav_menu">
<a class="page-scroll" href="/home"><img src="/template/admin/smc/img/m_home.png" class="m_menu" /> Home</a>
<li class="dropdown header-search-wrap dropdown-li">
<a class="dropdown-item dropdown-toggle pr-5 dropdown-a" href="#"><img src="/template/admin/smc/img/trade.png" class="m_menu" /> 거래소</a>

</li>
<li class="dropdown header-search-wrap dropdown-li">
<a class="dropdown-item dropdown-toggle pr-5 dropdown-a" href="#"><img src="/template/admin/smc/img/wallet.png" class="m_menu" /> 입출금</a>
<ul class="dropdown-menu dropdown-ul">
<li><a class="dropdown-item subMenu_m" href="/trade/wallet">전자지갑</a></li>
<li><a class="dropdown-item subMenu_m" href="/trade/deposit">입금</a></li>
<!--<li><a class="dropdown-item subMenu_m" href="/trade/exchange">Exchange</a></li>-->
<li><a class="dropdown-item subMenu_m" href="/trade/withdrawal">출금</a></li>
</ul>
</li>
<li class="dropdown header-search-wrap dropdown-li">
<a class="dropdown-item dropdown-toggle pr-5 dropdown-a" href="#"><img src="/template/admin/smc/img/support.png" class="m_menu" /> 고객지원 </a>
<ul class="dropdown-menu dropdown-ul">
<li><a class="dropdown-item subMenu_m" href="/faq">F.A.Q</a></li>
<li><a class="dropdown-item subMenu_m" href="/notice">공지사항</a></li>
<!-- <li><a class="dropdown-item subMenu_m" href="/mbnews">뉴스</a></li> -->
<!-- <li><a class="dropdown-item subMenu_m" href="/coinnews">코인뉴스</a></li> -->
<!-- <li><a class="dropdown-item subMenu_m" href="/gallery">포토뉴스</a></li> -->
<!-- <li><a class="dropdown-item subMenu_m" href="/mtom">일대일문의</a></li> -->
</ul>
</li>
<li class="dropdown header-search-wrap dropdown-li">
<a class="dropdown-item dropdown-toggle pr-5 dropdown-a" href="#"><img src="/template/admin/smc/img/myacc.png" class="m_menu" /> 계정관리</a>
<ul class="dropdown-menu dropdown-ul">
<!-- <li><a class="dropdown-item subMenu_m" href="/edit">정보수정</a></li> -->
<li><a class="dropdown-item subMenu_m" href="/certification">인증관리</a></li>
<!-- <li><a class="dropdown-item subMenu_m" href="/editpin">거래보안번호 변경<</a></li> -->
<li><a class="dropdown-item subMenu_m" href="/histories">거래내역</a></li>
</ul>
</li>
<!--<a class="page-scroll &lt;!&ndash;{? _SERVER.SCRIPT_NAME =='/member/memberEdit.php' || _SERVER.SCRIPT_NAME =='/account/myinfo.php'}&ndash;&gt;active&lt;!&ndash;{/}&ndash;&gt;" href="/edit"><img src="/template/admin/smc/img/myacc.png" class="m_menu" />  MyAccount</a>-->
</div>
<footer>
<p class="mcopyright">KMCSE, LTD / Copyrightⓒ 2020, KMCSE</p>
</footer>
</nav>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<?php } ?>
</header>
<!--End Header-->


<div class="page body-sign">
<script></script>

<section class="section-big" style="background:url(/template/admin/smc/img/new/corinne-kutz.jpg);background-size:cover">
<div class="container">
<div class="row">
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">


<div class="middle-box text-center animated fadeInDown">
<h1>500</h1>
<h3 class="font-bold">Internal Server Error</h3>

<div class="error-desc">
The server encountered something unexpected that didn't allow it to complete the request. We apologize.<br/>
You can go back to main page: <br/><br/>
<a href="/" class="btn btn-primary text-white">Go Main</a>
</div>
</div>

</div>
</div>
</div>
</section>

<footer class="footer stylelamas bg-block-top-shadow">
<div class="copyright">
<div class="container">
<div class="row">
<div class="col-sm-12 text-center">
Copyright  © 2022 SAM중소기업비상장거래 </a>
</div>
</div>

</div>
</div>
</footer></div></div>
<!-- Vendor -->
<script src="/template/admin/smc/vendor/jquery/jquery.js"></script>
<script src="/template/admin/smc/vendor/jquery/jquery.nav.js"></script>
<script src="/template/admin/smc/vendor/jquery/jquery.validate.js"></script>
<script src="/template/admin/smc/vendor/jquery.appear/jquery.appear.min.js"></script>
<script src="/template/admin/smc/vendor/jquery.easing/jquery.easing.min.js"></script>
<script src="/template/admin/smc/vendor/jquery-cookie/jquery-cookie.min.js"></script>
<script src="/template/admin/smc/vendor/magnific-popup/jquery.magnific-popup.js"></script>
<script src="/template/admin/smc/vendor/modernizr/modernizr.min.js"></script>
<script src="/template/admin/smc/vendor/tether/tether.min.js"></script>
<script src="/template/admin/smc/vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="/template/admin/smc/vendor/menuzord/menuzord.js"></script>
<script src="/template/admin/smc/vendor/sticky/jquery.sticky.min.js"></script>
<script src="/template/admin/smc/vendor/isotope/jquery.isotope.min.js"></script>
<script src="/template/admin/smc/vendor/respond/respond.js"></script>
<script src="/template/admin/smc/vendor/images-loaded/imagesloaded.js"></script>
<script src="/template/admin/smc/vendor/owl-carousel/owl.carousel.js"></script>
<script src="/template/admin/smc/vendor/wow/wow.min.js"></script>
<script src="/template/admin/smc/vendor/lite-tooltip/js/litetooltip.js"></script>
<script src="/template/admin/smc/vendor/smoothscroll/smooth.scroll.min.js"></script>
<script src="/template/admin/script/plug_in/cookie/jquery.cookie.js"></script>
<script src="/template/admin/script/plug_in/drag/jquery.drag.js"></script>
<script src="/template/admin/script/plug_in/popup/jquery.popup.js"></script>
<script src="/template/admin/smc/js/theme-plugins.js"></script>
<!-- Current Page Vendor and Views -->
<script src="/template/admin/smc/vendor/rs-plugin/js/jquery.themepunch.tools.min.js"></script>
<script src="/template/admin/smc/vendor/rs-plugin/js/jquery.themepunch.revolution.min.js"></script>
<!-- Theme Initialization -->
<script src="/template/admin/smc/js/theme.js"></script>
<script src="/template/admin/smc/js/custom.js"></script>
<script src="/template/admin/smc/js/cbpAnimatedHeader.js"></script>
<!--<script src="/template/admin/smc/js/ledger-react.js"></script>-->
<!-- Jquery toastr JS -->
<script src="/template/admin/smc/js/plugins/toastr/toastr.min.js"></script>
<script src="https://unpkg.com/react@16/umd/react.production.min.js"></script>
<script src="https://unpkg.com/react-dom@16/umd/react-dom.production.min.js"></script>
<!-- Sparkline -->
<script src="/template/admin/smc/js/plugins/sparkline/jquery.sparkline.min.js"></script>
<script src="/template/admin/script/plug_in/cookie/jquery.cookie.js" charset="utf-8" type="text/javascript"></script>
<script src="/template/admin/script/plug_in/drag/jquery.drag.js" charset="utf-8" type="text/javascript"></script>
<script src="/template/admin/script/plug_in/popup/jquery.popup.js" charset="utf-8" type="text/javascript"></script>
<script src="/template/admin/script/php.default.min.js" charset="utf-8" type="text/javascript"></script>
<script src="/template/admin/script/util.js" charset="utf-8" type="text/javascript"></script>
<script src="/template/admin/smc/js/js_lang.js" charset="utf-8" type="text/javascript"></script>
<script src="/template/admin/script/plug_in/select/jquery.select.js" charset="utf-8" type="text/javascript"></script>
<script src="/template/admin/script/plug_in/fixit/jquery.fixit.js" charset="utf-8" type="text/javascript"></script>
<script src="/template/admin/script/plug_in/ddlevelsmenu/ddlevelsmenu.js" charset="utf-8" type="text/javascript"></script>
<script src="/template/admin/script/plug_in/jcarousel/jcarousellite.js" charset="utf-8" type="text/javascript"></script>
<script src="/template/admin/script/plug_in/mousewheel/jquery.mousewheel.js" charset="utf-8" type="text/javascript"></script>
<script src="/template/admin/script/plug_in/easing/jquery.easing.js" charset="utf-8" type="text/javascript"></script>
<script src="/template/admin/smc/js/kmcsetrade-set.js?v=1562652029" charset="utf-8" type="text/javascript"></script>
<script src="/template/admin/smc/js/kmcsetrade.2.chunk.js?v=1562652029" charset="utf-8" type="text/javascript"></script>
<script src="/template/admin/smc/js/kmcsetrade.main.chunk.js?v=1562652029" charset="utf-8" type="text/javascript"></script>
<script type="text/javascript">
/*
<!--
jQuery(function ($) {
$('#popup_20190201').dragPopup({
popup_id: 'drag_popup_20190201',
popup_title: 'Notice - Exbds',
popup_width: 500,
popup_height: 452,
bool_today_close:true
});
showPopup('drag_popup_20190201',{kind_pos:'manual',pos_x:105,pos_y:100});
});
//-->
*/
</script>
<!-- Global site tag (gtag.js) - Google Analytics -->
<!-- <script async src="https://www.googletagmanager.com/gtag/js?id=UA-"></script> -->
<!-- <script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', 'UA-');
</script> -->
</body>
</html>