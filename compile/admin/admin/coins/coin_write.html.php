<?php /* Template_ 2.2.6 2023/01/03 09:56:32 /home/ubuntu/www/admin/www/template/admin/admin/coins/coin_write.html 000010768 */ 
$TPL_currency_grade_info_1=empty($TPL_VAR["currency_grade_info"])||!is_array($TPL_VAR["currency_grade_info"])?0:count($TPL_VAR["currency_grade_info"]);?>
<div class="row wrapper border-bottom white-bg page-heading">
<div class="col-lg-10">
<h2>상품상장관리</h2>
<ol class="breadcrumb">
<li>
<a href="index.html">Home</a>
</li>
<li>
<a>상품상장관리</a>
</li>
<li class="active">
<strong>상품상장관리</strong>
</li>
</ol>
</div>
</div>
<div class="wrapper wrapper-content animated fadeInRight">
<div class="row">
<div class="col-lg-12 m-b-md">
<div class="ibox float-e-margins">
<div class="ibox-title">
<h5>종목 추가 </h5>
<div class="text-right"><a href="?" class="btn btn-info">목록</a></div>
</div>
<div class="ibox-content">
<form method="post" class="form-horizontal" name="editform" id="editform" action="">
<input type="hidden"  name="pg_mode" value="write" />
<div class="form-group">
<label class="col-sm-2 control-label">NFT 상품</label>
<div class="col-sm-4">
<input type="hidden" name="nft_goods_idx" value="<?php echo $TPL_VAR["auction_goods_info"]["idx"]?>"/>
<input type="text" name="nft_goods_title" placeholder="상품 이름을 입력해주세요." class="form-control" value="<?php echo $TPL_VAR["auction_goods_info"]["title"]?>" <?php if($TPL_VAR["auction_goods_info"]["idx"]){?>readonly<?php }?> />
<!-- <select name="active" class="form-control">
<option value="" ></option>
</select> -->
<p>거래소에서 판매할 NFT 상품을 선택해주세요.</p>
</div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label">종목 아이콘</label>
<div class="col-sm-4">
<input type="file" name="icon_file" value="" class="form-control">
<input type="hidden" name="icon_url" id="icon_url" value="<?php echo $TPL_VAR["currency_info"]["icon_url"]?>" >
</div>
<label class="col-sm-2 control-label"></label>
<div class="col-sm-4">
<img src="<?php echo $TPL_VAR["currency_info"]["icon_url"]?>" height="34px" id="icon_image">
</div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label">이름</label>
<div class="col-sm-4"><input type="text" name="name" value="<?php echo $TPL_VAR["currency_info"]["name"]?>" class="form-control"></div>
<label class="col-sm-2 control-label">소숫점 표시 자릿수</label>
<div class="col-sm-4"><input type="text" name="display_decimals" value="<?php echo $TPL_VAR["currency_info"]["display_decimals"]?>" class="form-control"></div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label">종목코드</label>
<div class="col-sm-4"><input type="text" name="symbol" value="<?php echo $TPL_VAR["currency_info"]["symbol"]?>" class="form-control" <?php if($TPL_VAR["currency_info"]["symbol"]!=''){?> readonly="" <?php }?> maxlength="10" ></div>
<label class="col-sm-2 control-label">마켓</label>
<div class="col-sm-4">
<?php if($TPL_VAR["currency_info"]["symbol"]!=''){?>
<input type="text" name="exchange" value="<?php echo $TPL_VAR["currency_info"]["exchange"]?>" class="form-control" placeholder="KRW" <?php if($TPL_VAR["currency_info"]["symbol"]!=''){?> readonly="" <?php }?> >
<?php }else{?>
<input type="text" name="exchange" value="KRW" class="form-control" placeholder="KRW"  readonly  >
<?php }?>
</div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label">출금 수수료율</label>
<div class="col-sm-4">
<input type="text" name="fee_out" value="<?php echo $TPL_VAR["currency_info"]["fee_out"]?>" class="form-control input_percent" style="width: 50%;display: inline-block;">
( = <span class="text_percent"><?php echo number_format($TPL_VAR["currency_info"]["fee_out"]* 100, 2)?></span>% )
</div>
<label class="col-sm-2 control-label">매도 수수료율</label>
<div class="col-sm-4">
<input type="text" name="fee_sell_ratio" value="<?php echo $TPL_VAR["currency_info"]["fee_sell_ratio"]?>" class="form-control input_percent" style="width: 50%;display: inline-block;">
( = <span class="text_percent"><?php echo number_format($TPL_VAR["currency_info"]["fee_sell_ratio"]* 100, 2)?></span>% )
</div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label">최소 매매 가능 수량</label>
<div class="col-sm-4"><input type="text" name="trade_min_volume" value="<?php echo $TPL_VAR["currency_info"]["trade_min_volume"]?>" class="form-control"></div>
<label class="col-sm-2 control-label">최대 매매 가능 수량</label>
<div class="col-sm-4"><input type="text" name="trade_max_volume" value="<?php echo $TPL_VAR["currency_info"]["trade_max_volume"]?>" class="form-control"></div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label">최소 출금 가능 수량</label>
<div class="col-sm-4"><input type="text" name="out_min_volume" value="<?php echo $TPL_VAR["currency_info"]["out_min_volume"]?>" class="form-control"></div>
<label class="col-sm-2 control-label">최대 출금 가능 수량</label>
<div class="col-sm-4"><input type="text" name="out_max_volume" value="<?php echo $TPL_VAR["currency_info"]["out_max_volume"]?>" class="form-control"></div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label">사용여부</label>
<div class="col-sm-4">
<select name="active" class="form-control">
<option value="Y" <?php if($TPL_VAR["currency_info"]["active"]=='Y'){?>selected<?php }?> >예</option>
<option value="N" <?php if($TPL_VAR["currency_info"]["active"]!='Y'){?>selected<?php }?>>아니요</option>
</select>
</div>
<label class="col-sm-2 control-label">가격</label>
<div class="col-sm-4"><input type="text" name="price" value="<?php echo $TPL_VAR["currency_info"]["price"]?>" class="form-control"></div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label">표시등급</label>
<div class="col-sm-4">
<select name="display_grade" class="form-control">
<?php if($TPL_currency_grade_info_1){foreach($TPL_VAR["currency_grade_info"] as $TPL_V1){?>
<option value="<?php echo $TPL_V1["goods_grade"]?>" <?php if($TPL_VAR["currency_info"]["display_grade"]==$TPL_V1["goods_grade"]){?>selected<?php }?> ><?php echo $TPL_V1["goods_grade"]?></option>
<?php }}?>
</select>
</div>
</div>
<div class="form-group hide">
<label class="col-sm-2 control-label">주소생성가능여부</label>
<div class="col-sm-4">
<select name="creatable" class="form-control">
<option value="Y" <?php if($TPL_VAR["currency_info"]["creatable"]=='Y'){?>selected<?php }?> >예</option>
<option value="N" <?php if($TPL_VAR["currency_info"]["creatable"]!='Y'){?>selected<?php }?>>아니요</option>
</select>
</div>
<label class="col-sm-2 control-label">암호화폐여부</label>
<div class="col-sm-4">
<select name="crypto_currency" class="form-control">
<option value="Y" <?php if($TPL_VAR["currency_info"]["crypto_currency"]=='Y'){?>selected<?php }?> >예</option>
<option value="N" <?php if($TPL_VAR["currency_info"]["crypto_currency"]!='Y'){?>selected<?php }?>>아니요</option>
</select>
</div>
</div>
<div class="form-group hide">
<label class="col-sm-2 control-label">매뉴여부</label>
<div class="col-sm-4">
<select name="menu" class="form-control">
<option value="Y" <?php if($TPL_VAR["currency_info"]["menu"]=='Y'){?>selected<?php }?> >예</option>
<option value="N" <?php if($TPL_VAR["currency_info"]["menu"]!='Y'){?>selected<?php }?>>아니요</option>
</select>
</div>
<label class="col-sm-2 control-label">매뉴정렬순서</label>
<div class="col-sm-4"><input type="text" name="sortno" value="<?php echo $TPL_VAR["currency_info"]["sortno"]?>" class="form-control"></div>
</div>
<hr class=" hide">
<div class="form-group hide">
<label class="col-sm-2 control-label">판매회원 아이디</label>
<div class="col-sm-4">
<input type="text" name="manager_userid" value="<?php echo $TPL_VAR["currency_info"]["manager_userid"]?>" class="form-control" placeholder="판매회원 아이디를 입력해주세요.">
</div>
<label class="col-sm-2 control-label ">판매회원 만들기</label>
<div class="col-sm-4">
<div class=" input-group" style="padding: 0;width: 100%;">
<input type="password" name="manager_userpw" value="" class="form-control col-sm-9" placeholder="비밀번호를 입력해주세요.">
<input type="text" name="manager_nickname" value="" class="form-control col-sm-9" placeholder="닉네임을 입력해주세요.">
<div class="input-group-addon btn btn-success" name="btn-create-manager"  style="color: #FFF;background-color: #1c84c6;">만들기</div>
</div>
</div>
</div>
<div class="form-group hide">
<label class="col-sm-2 control-label">판매회원 보유수량</label>
<div class="col-sm-4">
<input type="text" name="company_address" value="<?php echo $TPL_VAR["currency_info"]["manager_wallet_balance"]* 1?>" class="form-control" readonly >
<div class="input-group" style="padding: 0;width: 100%;">
<input type="text" name="add_balance_to_manager" value="" placeholder="판매회원에게 추가할 수량을 입력해주세요." class="form-control text-right" >
<div name="btn-add-balance" class="input-group-addon btn btn-success" style="color: #FFF;background-color: #1c84c6;">추가</div>
</div>
</div>
<label class="col-sm-2 control-label">거래소 보유수량</label>
<div class="col-sm-4">
<input type="text" name="company_hompage" value="<?php echo $TPL_VAR["currency_info"]["walletmanager_wallet_balance"]* 1?>" class="form-control" readonly >
</div>
</div>
<hr class=" hide">
<div class="form-group hide">
<label class="col-sm-2 control-label">출하량</label>
<div class="col-sm-4"><input type="text" name="circulating_supply" value="<?php echo $TPL_VAR["currency_info"]["circulating_supply"]?>" class="form-control"></div>
<label class="col-sm-2 control-label">총발행량</label>
<div class="col-sm-4"><input type="text" name="max_supply" value="<?php echo $TPL_VAR["currency_info"]["max_supply"]?>" class="form-control"></div>
</div>
<div class="form-group hide">
<label class="col-sm-2 control-label">NFT 대표색상</label>
<div class="col-sm-4"><input type="text" name="color" value="<?php echo $TPL_VAR["currency_info"]["color"]?>" class="form-control"></div>
<label class="col-sm-2 control-label hide">입금확인 작동여부</label>
<div class="col-sm-4 hide">
<select name="check_deposit" class="form-control">
<option value="Y" <?php if($TPL_VAR["currency_info"]["check_deposit"]=='Y'){?>selected<?php }?> >예</option>
<option value="N" <?php if($TPL_VAR["currency_info"]["check_deposit"]!='Y'){?>selected<?php }?>>아니요</option>
</select>
</div>
</div>
<hr>
<div class="form-group text-center">
<input type="submit" class="btn btn-default" value="저장"/>
<input type="reset" class="btn btn-warning" value="초기화"/>
</div>
</form>
</div>
</div>
</div>
</div>
</div>