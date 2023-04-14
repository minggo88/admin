<?php /* Template_ 2.2.6 2022/10/27 11:14:34 /home/ubuntu/www/admin/www/template/admin/admin/laboratory/kpi_total.html 000003230 */ 
$TPL_loop_tradestate_1=empty($TPL_VAR["loop_tradestate"])||!is_array($TPL_VAR["loop_tradestate"])?0:count($TPL_VAR["loop_tradestate"]);?>
<div class="row wrapper border-bottom white-bg page-heading">
<div class="col-lg-10">
<h2>거래소 상품별 총량</h2>
<ol class="breadcrumb">
<li>
<a href="/">Home</a>
</li>
<li>
<a>통계</a>
</li>
<li class="active">
<strong>거래소 상품별 총량</strong>
</li>
</ol>
</div>
<div class="col-lg-2">
</div>
</div>
<div class="wrapper wrapper-content animated fadeInRight">
<?php if($TPL_loop_tradestate_1){foreach($TPL_VAR["loop_tradestate"] as $TPL_V1){?>
<div class="row m-b-md">
<div class="col-lg-12">
<h3>[<?php echo $TPL_V1["symbol"]?>] <?php echo $TPL_V1["name"]?></h3>
</div>
<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
<div class="ibox float-e-margins m-b-md">
<div class="ibox-title">
<h5>거래소 <strong class="text-danger"><?php echo $TPL_V1["name"]?></strong> 총량</h5>
</div>
<div class="ibox-content" style="padding-bottom: 20px;">
<h1 class="no-margins"><small style="font-size:20px;"><small style="font-size:20px;color:red"><?php echo number_format($TPL_V1["cnt_total_trade_member"]+$TPL_V1["cnt_total_trade_unsell"])?></small>  = <small style="font-size:20px;color:rgb(13, 109, 235);"><?php echo number_format($TPL_V1["cnt_total_trade_member"])?></small> + <?php echo number_format($TPL_V1["cnt_total_trade_unsell"])?> <?php if($TPL_V1["cnt_total_exchange"]> 0){?>+ <?php echo number_format($TPL_V1["cnt_total_exchange"])?><?php }?></small> </h1>
<small>거래소 <?php echo $TPL_V1["name"]?> 총량 <br />= 거래소지갑 + 미체결 매도량(거래소) <?php if($TPL_V1["cnt_total_exchange"]> 0){?>+ 미체결 매도량(교환소)<?php }?>  </small>
</div>
</div>
</div>
<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
<div class="ibox float-e-margins m-b-md">
<div class="ibox-title">
<h5>거래소 <strong class="text-danger"><?php echo $TPL_V1["name"]?></strong> 미체결 매도, 매수 총량(대기)</h5>
</div>
<div class="ibox-content" style="padding-bottom: 20px;">
<h1 class="no-margins"><small style="font-size:20px;"> <?php echo number_format($TPL_V1["cnt_total_trade_sell_wait"])?> /  <small style="font-size:20px;color:green;"><?php echo number_format($TPL_V1["cnt_total_trade_buy_wait"])?></small></small> </h1>
<small>거래소 <?php echo $TPL_V1["name"]?> <br />=미체결 매도 총량 / 거래소 미체결 메수 총량</small>
</div>
</div>
</div>
<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
<div class="ibox float-e-margins m-b-md">
<div class="ibox-title">
<h5>회원분 <strong class="text-danger"><?php echo $TPL_V1["name"]?></strong> 총량</h5>
</div>
<div class="ibox-content" style="padding-bottom: 20px;">
<h1 class="no-margins"><small style="font-size:20px;"> <small style="font-size:20px;color:red"><?php echo number_format($TPL_V1["cnt_total_trade_member"])?></small></small> </h1>
<small>회원분 <?php echo $TPL_V1["name"]?> 총량<br />-</small>
</div>
</div>
</div>
</div>
<?php }}else{?>
<div class="row">
상장된 상품이 없습니다.
</div>
<?php }?>
</div>