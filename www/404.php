<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>KMCSE</title>
<meta name="keywords" content="" />
<meta name="description" content="Connecting People, Moving Earth" />
<meta name="author" content="홍길동 info@kmcse.com" />
<meta name="copyright" content="Copyright ⓒ KMCSE  2020 All right reserved" />
<meta name="build" content="2018.05.24">
<meta name="content-language"content="en">
<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta property="og:title" content="KMCSE">
<meta property="og:description" content="KMCSE">
<meta property="og:image" content="/kakao-kmcse-logo.png">
<link rel="stylesheet" href="/css/default.css?v=1649109451" />
<link rel="stylesheet" href="/css/common.css?v=1649591290" />
<link rel="icon" type="image/png" href="/img/favicon.png">
<link rel="icon" type="image/png" href="/img/favicon_16.png" sizes="16x16">
<link rel="icon" type="image/png" href="/img/favicon_24.png" sizes="24x24">
<link rel="icon" type="image/png" href="/img/favicon_64.png" sizes="64x64">
<link rel="icon" type="image/png" href="/img/favicon_72.png" sizes="72x72">
<link rel="icon" type="image/png" href="/img/favicon_128.png" sizes="128x128">
<link rel="icon" type="image/png" href="/img/favicon_180.png" sizes="180x180">
<link rel="icon" type="image/png" href="/img/favicon_196.png" sizes="196x196">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,400i,600,700|Cabin:400,600,700" rel="stylesheet">
<!-- Vendor CSS -->
<link rel="stylesheet" href="/template/admin/smc/vendor/tether/tether.min.css?t=1649109479" />
<link rel="stylesheet" href="/template/admin/smc/vendor/bootstrap/css/bootstrap.min.css?t=1649109477">
<link rel="stylesheet" href="/template/admin/smc/css/fonts/express-icons.css?t=1649109474" />
<link rel="stylesheet" href="/template/admin/smc/vendor/font-awesome/css/font-awesome.min.css?t=1649109478">
<link rel="stylesheet" href="/template/admin/trade/webfont/cryptocoins.css?t=1649109486">
<link rel="stylesheet" href="/template/admin/smc/vendor/ion-icons/css/ionicons.min.css?t=1649109478" />
<link rel="stylesheet" href="/template/admin/smc/vendor/owl-carousel/owl.theme.css?t=1649109478" />
<link rel="stylesheet" href="/template/admin/smc/vendor/owl-carousel/owl.carousel.css?t=1649109478" />
<link rel="stylesheet" href="/template/admin/smc/vendor/lite-tooltip/css/litetooltip.css?t=1649109478" />
<!-- Smart Forms CSS -->
<link href="/template/admin/smc/smartforms/Templates/css/smart-loader.css?t=1649109477" rel="stylesheet" />
<link href="/template/admin/smc/smartforms/Templates/css/smart-addons.css?t=1649109477" rel="stylesheet" />
<link href="/template/admin/smc/smartforms/Templates/css/smart-forms.css?t=1649109477" rel="stylesheet" />
<!-- Theme CSS -->
<link href="/template/admin/smc/css/main.css?t=1649109475" rel="stylesheet" />
<link href="/template/admin/smc/css/main-shortcodes.css?t=1649109475" rel="stylesheet" />
<link href="/template/admin/smc/css/header.css?t=1649109475" rel="stylesheet" />
<link href="/template/admin/smc/css/form-element.css?t=1649109475" rel="stylesheet" />
<link href="/template/admin/smc/css/animation.css?t=1649109474" rel="stylesheet" />
<link href="/template/admin/smc/css/responsive.css?t=1649109475" rel="stylesheet" />
<link href="/template/admin/smc/css/utilities.css?t=1649109475" rel="stylesheet" />
<link href="/template/admin/smc/css/skins/default.css?t=1649109475" rel="stylesheet" />
<!-- Current Page CSS -->
<link href="/template/admin/smc/smartforms/Templates/css/smart-forms.css?t=1649109477" rel="stylesheet" />
<!-- Theme Custom CSS -->
<link rel="stylesheet" href="/template/admin/smc/css/custom.css?t=1649109474">
<!-- React datepicker CSS -->
<link rel="stylesheet" href="/template/admin/smc/css/react-datepicker.css?t=1649109475">
<!-- Jquery toastr CSS -->
<link rel="stylesheet" href="/template/admin/smc/css/plugins/toastr/toastr.min.css?t=1649109475">
<!-- Style Swicher -->
<link href="/template/admin/smc/vendor/style-switcher/style-switcher.css?t=1649109479" rel="stylesheet" />
<link href="/template/admin/smc/vendor/style-switcher/bootstrap-colorpicker/css/bootstrap-colorpicker.css?t=1649109479" rel="stylesheet" />
<!-- SCC CSS -->
<link rel="stylesheet" href="/template/admin/smc/css/scc.css?t=1649109475">
<link rel="stylesheet" href="/template/admin/smc/css/coin.css?t=1649109474">
<link href="https://fonts.googleapis.com/css?family=Noto+Sans" rel="stylesheet">
</head>
<body>
<div class="wrapper">
<!--Header-->
<header id="header" class="header-narrow header-full-width" >
    <!-- 404_.html -->
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
                                <input type="text" class="form-control" name="name" id="q" placeholder="실시간 인기종목 보러가기..." required="" style="width: 100%;">
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
<a href="/home"><img alt="KMCSE" src="/template/admin/smc/img/main/kmcse-stock-logo.png"></a>
</div>
</div>
</div>
<div class="header-search">
<form id="searchForm" action="/trade/total" method="get" novalidate="novalidate">
<div class="input-group">
<input type="text" class="form-control" name="name" id="q" placeholder="Search..." required="">
<span class="input-group-btn">
<button class="btn btn-light" type="submit"><i class="fa fa-search"></i></button>
</span>
</div>
</form>
</div>
<div class="header-column justify-content-center">
<div class="header-row">
<div class="header-nav header-nav-dark-dropdown header-nav-top-line justify-content-end">
<div class="header-nav-main header-nav-main-effect-2 header-nav-main-sub-effect-1">
<nav class="gnb-menu collapse">
<ul class="nav nav-pills" id="mainNav">
<li class="dropdown">
<a class="dropdown-item dropdown-toggle " href="/home"> 전체종목 </a>
</li>
<li class="dropdown">
<a class="dropdown-item dropdown-toggle " href="/exchange" style="padding: 0 9px;"> 공모주청약</a>
</li>
<li class="dropdown">
<a class="dropdown-item dropdown-toggle" href="/trade/wallet" style="padding: 0 9px;"> 자산</a>
<ul class="dropdown-menu">
<li><a class="dropdown-item" href="/trade/wallet">전자지갑</a></li>
<li><a class="dropdown-item" href="/trade/deposit">입금</a></li>
<!--<li><a class="dropdown-item" href="/trade/exchange">Exchange</a></li>-->
<li><a class="dropdown-item" href="/trade/withdrawal">출금</a></li>
<li><a class="dropdown-item" href="/trade/analysis">종합분석</a></li>
</ul>
</li>
<li class="dropdown">
<a class="dropdown-item dropdown-toggle " href="/faq" style="padding: 0 9px;"> 고객지원</a>
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
<a class="dropdown-item dropdown-toggle " href="/certification" style="padding: 0 9px;"> 계정관리 </a>
<ul class="dropdown-menu">
<li><a class="dropdown-item" href="/edit">정보수정</a></li>
<li><a class="dropdown-item" href="/certification">인증관리</a></li>
<li><a class="dropdown-item" href="/editpin">거래보안번호 변경</a></li>
<li><a class="dropdown-item" href="/histories">거래내역</a></li>
</ul>
</li>
<li><a class="dropdown-item" href="#" style="padding: 0 9px;">|</a></li>
<li><a class="dropdown-item active" href="/login/L2xvZ2luL0wyVmthWFE9" style="padding: 0 9px;"> 로그인</a></li>
<li><a class="dropdown-item active" href="/join?ret_url=Ly9sb2Mua21jc2UuY29tL2xvZ2luL0wyVmthWFE9" style="padding: 0 9px;"> 회원가입</a></li>
</ul>
</nav>
</div>
</div>
</div>
</div>
<div class="header-column justify-content-end">
<div class="header-row">
<div class="header-nav header-nav-top-line justify-content-end">
<div class="header-nav-main header-nav-main-effect-2 header-nav-main-sub-effect-1">
<nav class="collapse">
<ul class="nav nav-pills" id="mainNav">
<li class="dropdown header-search-wrap">
<a class="dropdown-item dropdown-toggle pr-5" href="#"><i class="fa fa-search3 fs-18"></i> </a>
<ul class="dropdown-menu">
<li>
<div class="header-search">
<form id="searchForm" action="/trade/total" method="get" novalidate="novalidate">
<div class="input-group">
<input type="text" class="form-control" name="name" id="q" placeholder="Search..." required="">
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
<div class="header-nav-main header-nav-mobile" id="menu-mobile" <!--{? _GET.device_type=='mobile'}-->style="display:none  !important"<!--{/}--> >
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
<a href="/login/L2xvZ2luL0wyVmthWFE9" class="btn btn-primary"><img src="/template/admin/smc/img/login.png" class="m_menu" /> 로그인</a>
<a href="/join?ret_url=Ly9sb2Mua21jc2UuY29tL2xvZ2luL0wyVmthWFE9" class="btn btn-primary"><img src="/template/admin/smc/img/m_join.png" class="m_menu" /> 회원가입</a>
<!-- <a href="#" class="btn btn-primary downApp"><img src="/template/admin/smc/img/app.png" class="m_menu" /> App</a> -->
</div>
<div class="nav_menu">
<a class="page-scroll" href="/trade/btc"> Home</a>
<li class="dropdown header-search-wrap dropdown-li">
<a class="dropdown-item dropdown-toggle pr-5 dropdown-a" href="#"> 거래소</a>
<ul class="dropdown-menu dropdown-ul">
<li><a class="dropdown-item subMenu_m" id="ltc_area" href="/trade/ltc"><span class="icon-24 menu icon-24-ltc inactive"></span>&nbsp;&nbsp;컬리</a></li>
<li><a class="dropdown-item subMenu_m" id="eth_area" href="/trade/eth"><span class="icon-24 menu icon-24-eth inactive"></span>&nbsp;&nbsp;연대엔지니어링</a></li>
<li><a class="dropdown-item subMenu_m" id="bdc_area" href="/trade/bdc"><span class="icon-24 menu icon-24-bdc inactive"></span>&nbsp;&nbsp;BDS Coin</a></li>
<li><a class="dropdown-item subMenu_m" id="kbk_area" href="/trade/kbk"><span class="icon-24 menu icon-24-kbk inactive"></span>&nbsp;&nbsp;케이뱅크</a></li>
<li><a class="dropdown-item subMenu_m" id="bch_area" href="/trade/bch"><span class="icon-24 menu icon-24-bch inactive"></span>&nbsp;&nbsp;두나무</a></li>
<li><a class="dropdown-item subMenu_m" id="apc_area" href="/trade/apc"><span class="icon-24 menu icon-24-apc inactive"></span>&nbsp;&nbsp;루닛</a></li>
<li><a class="dropdown-item subMenu_m" id="btc_area" href="/trade/btc"><span class="icon-24 menu icon-24-btc inactive"></span>&nbsp;&nbsp;비바리퍼블리카</a></li>
<li><a class="dropdown-item subMenu_m" id="ynj_area" href="/trade/ynj"><span class="icon-24 menu icon-24-ynj inactive"></span>&nbsp;&nbsp;야놀자</a></li>
</ul>
</li>
<li class="dropdown header-search-wrap dropdown-li">
<a class="dropdown-item dropdown-toggle pr-5 dropdown-a" href="#"> 입출금</a>
<ul class="dropdown-menu dropdown-ul">
<li><a class="dropdown-item subMenu_m" href="/trade/wallet">전자지갑</a></li>
<li><a class="dropdown-item subMenu_m" href="/trade/deposit">입금</a></li>
<!--<li><a class="dropdown-item subMenu_m" href="/trade/exchange">Exchange</a></li>-->
<li><a class="dropdown-item subMenu_m" href="/trade/withdrawal">출금</a></li>
<li><a class="dropdown-item subMenu_m" href="/trade/analysis">종합분석</a></li>
</ul>
</li>
<li class="dropdown header-search-wrap dropdown-li">
<a class="dropdown-item dropdown-toggle pr-5 dropdown-a" href="#"> 고객지원 </a>
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
<a class="dropdown-item dropdown-toggle pr-5 dropdown-a" href="#"> 계정관리</a>
<ul class="dropdown-menu dropdown-ul">
<li><a class="dropdown-item subMenu_m" href="/edit">정보수정</a></li>
<li><a class="dropdown-item subMenu_m" href="/certification">인증관리</a></li>
<li><a class="dropdown-item subMenu_m" href="/editpin">거래보안번호 변경</a></li>
<li><a class="dropdown-item subMenu_m" href="/histories">거래내역</a></li>
</ul>
</li>
<!--<a class="page-scroll &lt;!&ndash;{? _SERVER.SCRIPT_NAME =='/member/memberEdit.php' || _SERVER.SCRIPT_NAME =='/account/myinfo.php'}&ndash;&gt;active&lt;!&ndash;{/}&ndash;&gt;" href="/edit">  MyAccount</a>-->
</div>
<footer>
<p class="mcopyright">Copyright © 2022 KMCSE</p>
</footer>
</nav>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<!--{/}-->
</header>
<!--End Header-->

<div class="page body-sign">
<section class="section-big" style="background:url(/template/admin/smc/img/new/corinne-kutz.jpg);background-size:cover">
<div class="container">
<div class="row">
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">


<div class="middle-box text-center animated fadeInDown">
<h1>404</h1>
<h3 class="font-bold">Page Not Found</h3>

<div class="error-desc">
Sorry, but the page you are looking for has note been found. Try checking the URL for error, then hit the refresh button on your browser or try found something else in our app.<br/><br/>
<a href="/" class="btn btn-primary text-white">Go Main</a>
</div>
</div>


</div>
</div>
</div>
</section>

<footer class="footer stylelamas bg-block-top-shadow">
<div class="main">
<div class="container">
<div class="row">
<div class="col-md-12 col-sm-12 mb30">
<h2>SAM중소기업비상장거래</h2>
</div>
<div class="col-md-6 col-sm-12 mb10">
<section>
<div class="clearfix social-wrap">
<ul class="pl-none">
<li><p><strong>운영시간 : 평일 10시~6시 (점심시간 12시 ~13시)</strong></p></li>
<li><p><strong>대표메일 : </strong> <a class="b-link" href="mailto:info@kmcse.com">info@kmcse.com</a></p></li>
<li><p>서울특별시 강남구 논현로 637 민영빌딩 5층 </p></li>
<li><p>&nbsp;</p></li>
<li><p><strong>대표 : </strong> 홍길동 &nbsp;&nbsp;
<strong>사업자등록번호 : </strong> 123-12-12345</p></li>
</ul>
</ul>
</div>
</section>
</div>
<div class="col-md-6 col-sm-12 m10">
<section>
<div class="map-img text-right">
<ul class="pl-none">
<li><p>&nbsp;</p></li>
<li><p><a href="/">이용약관</a> &nbsp;&nbsp; <strong>개인정보 취급방칭</strong> </p></li>
<li><p><a href="/">공지사항</a> &nbsp;&nbsp; <strong>마케팅정보 이용약관</strong></p></li>
<li><p>&nbsp;</p></li>
<li><p>Copyright © 2022 KMCSE All rights reserved</p></li>
</ul>
</div>
</section>
</div>
</div>
</div>
</div>
</footer></div></div>
<!-- Vendor -->
<script src="/template/admin/smc/vendor/jquery/jquery.js?t=1649109478"></script>
<script src="/template/admin/smc/vendor/jquery/jquery-latest.js?t=1649109478"></script>
<script src="/template/admin/smc/vendor/jquery/jquery.nav.js?t=1649109478"></script>
<script src="/template/admin/smc/vendor/jquery/jquery.validate.js?t=1649109478"></script>
<script src="/template/admin/smc/vendor/jquery.appear/jquery.appear.min.js?t=1649109478"></script>
<script src="/template/admin/smc/vendor/jquery.easing/jquery.easing.min.js?t=1649109478"></script>
<script src="/template/admin/smc/vendor/jquery-cookie/jquery-cookie.min.js?t=1649109478"></script>
<script src="/template/admin/smc/vendor/magnific-popup/jquery.magnific-popup.js?t=1649109478"></script>
<script src="/template/admin/smc/vendor/modernizr/modernizr.min.js?t=1649109478"></script>
<script src="/template/admin/smc/vendor/tether/tether.min.js?t=1649109479"></script>
<script src="/template/admin/smc/vendor/bootstrap/js/bootstrap.min.js?t=1649109477"></script>
<script src="/template/admin/smc/vendor/menuzord/menuzord.js?t=1649109478"></script>
<script src="/template/admin/smc/vendor/sticky/jquery.sticky.min.js?t=1649109479"></script>
<script src="/template/admin/smc/vendor/isotope/jquery.isotope.min.js?t=1649109478"></script>
<script src="/template/admin/smc/vendor/respond/respond.js?t=1649109479"></script>
<script src="/template/admin/smc/vendor/images-loaded/imagesloaded.js?t=1649109478"></script>
<script src="/template/admin/smc/vendor/owl-carousel/owl.carousel.js?t=1649109478"></script>
<script src="/template/admin/smc/vendor/wow/wow.min.js?t=1649109479"></script>
<script src="/template/admin/smc/vendor/lite-tooltip/js/litetooltip.js?t=1649109478"></script>
<script src="/template/admin/script/plug_in/cookie/jquery.cookie.js?t=1649109466"></script>
<script src="/template/admin/script/plug_in/drag/jquery.drag.js?t=1649109466"></script>
<script src="/template/admin/script/plug_in/popup/jquery.popup.js?t=1649109474"></script>
<script src="/template/admin/smc/js/theme-plugins.js?t=1649109476"></script>
<!-- Theme Initialization -->
<script src="/template/admin/smc/js/theme.js?t=1649109476"></script>
<script src="/template/admin/smc/js/custom.js?t=1649109476"></script>
<script src="/template/admin/smc/js/cbpAnimatedHeader.js?t=1649109476"></script>
<!--<script src="/template/admin/smc/js/ledger-react.js?t=1649109476"></script>-->
<!-- Jquery toastr JS -->
<script src="/template/admin/smc/js/plugins/toastr/toastr.min.js?t=1649109476"></script>
<!-- Sparkline -->
<script src="/template/admin/smc/js/plugins/sparkline/jquery.sparkline.min.js?t=1649109476"></script>
<script src="/template/admin/script/php.default.min.js?t=1649109466" charset="utf-8" type="text/javascript"></script>
<script src="/template/admin/script/util.js?t=1649109474" charset="utf-8" type="text/javascript"></script>
<script src="/template/admin/smc/js/js_lang.js?t=1649109476" charset="utf-8" type="text/javascript"></script>
<script src="/template/admin/script/plug_in/select/jquery.select.js?t=1649109474" charset="utf-8" type="text/javascript"></script>
<script src="/template/admin/script/plug_in/fixit/jquery.fixit.js?t=1649109466" charset="utf-8" type="text/javascript"></script>
<script src="/template/admin/script/plug_in/ddlevelsmenu/ddlevelsmenu.js?t=1649109466" charset="utf-8" type="text/javascript"></script>
<script src="/template/admin/script/plug_in/jcarousel/jcarousellite.js?t=1649109466" charset="utf-8" type="text/javascript"></script>
<script src="/template/admin/script/plug_in/mousewheel/jquery.mousewheel.js?t=1649109474" charset="utf-8" type="text/javascript"></script>
<script src="/template/admin/script/plug_in/easing/jquery.easing.js?t=1649109466" charset="utf-8" type="text/javascript"></script>
<script src="/template/admin/script/plug_in/form/jquery.form.js?t=1649109466" charset="utf-8" type="text/javascript"></script>
<script src="/template/admin/script/plug_in/form/jsform.js?t=1649109466" charset="utf-8" type="text/javascript"></script>
<!-- Global site tag (gtag.js) - Google Analytics -->
<!-- <script async src="https://www.googletagmanager.com/gtag/js?id=UA-"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', 'UA-');
</script> -->
</body>
</html>