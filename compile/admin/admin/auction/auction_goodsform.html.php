<?php /* Template_ 2.2.6 2022/11/02 19:37:17 /home/ubuntu/www/admin/www/template/admin/admin/auction/auction_goodsform.html 000017607 */ 
$TPL_certification_marks_1=empty($TPL_VAR["certification_marks"])||!is_array($TPL_VAR["certification_marks"])?0:count($TPL_VAR["certification_marks"]);
$TPL_categories_1=empty($TPL_VAR["categories"])||!is_array($TPL_VAR["categories"])?0:count($TPL_VAR["categories"]);?>
<div class="row wrapper border-bottom white-bg page-heading">
<div class="col-lg-10">
<h2>경매상품 등록</h2>
<ol class="breadcrumb">
<li>
<a href="index.html">Home</a>
</li>
<li>
<a>경매 관리</a>
</li>
<li class="active">
<strong>경매상품 등록</strong>
</li>
</ol>
</div>
</div>
<div class="wrapper wrapper-content animated fadeInRight">
<div class="row">
<div class="col-lg-12 m-b-md">
<div class="ibox float-e-margins">
<div class="ibox-title">
<h5>경매상품 추가 </h5>
<div class="text-right"><a href="?" class="btn btn-info">목록</a></div>
</div>
<div class="ibox-content">
<form method="post" class="form-horizontal" name="editform" id="editform" action="">
<input type="hidden"  name="pg_mode" value="<?php if($_GET["pg_mode"]=='write'){?>write<?php }else{?>edit<?php }?>" />
<input type="hidden" name="nft_file_type" value="IMAGE" >
<input type="hidden" name="creator_userno" value="2" class="form-control" readonly>
<div class="form-group">
<label class="col-sm-2 control-label">상품코드번호</label>
<div class="col-sm-4">
<input type="text" name="goods_idx" value="<?php echo $TPL_VAR["idx"]?>" class="form-control" readonly>
</div>
<label class="col-sm-2 control-label">등급</label>
<div class="col-sm-4">
<select name="goods_grade" class="form-control">
<option value="S" <?php if($TPL_VAR["goods_grade"]=='S'){?>selected<?php }?> >S</option>
<option value="A" <?php if($TPL_VAR["goods_grade"]=='A'){?>selected<?php }?> >A</option>
<option value="B" <?php if($TPL_VAR["goods_grade"]=='B'){?>selected<?php }?> >B</option>
</select>
</div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label">상품이미지</label>
<div class="col-sm-4">
<input type="file" name="icon_file" value="" class="form-control">
<ul>
<li>이미지를 선택해주세요.</li>
<li>지원되는 파일 형식 : JPG, PNG.</li>
<li>최대 크기 : 50 MB.</li>
</ul>
</div>
<div class="col-sm-6" style="overflow-x:auto;overflow-y:none">
<div style="white-space: nowrap" id="box_image_url">
<?php if($TPL_VAR["sub1_pic"]){?>
<div style="position: relative;display: inline-block;"><i name="btn-delete-image" data-idx="<?php echo $TPL_VAR["idx"]?>" class="fa fa-times" aria-hidden="true" style="position: absolute;right: 0;color: red;font-size: 2rem;margin: 0.5rem;"></i><img src="<?php echo $TPL_VAR["sub1_pic"]?>" height="150px"><input type="hidden" name="sub1_pic" value="<?php echo $TPL_VAR["sub1_pic"]?>"></div>
<?php }?>
<?php if($TPL_VAR["sub2_pic"]){?>
<div style="position: relative;display: inline-block;"><i name="btn-delete-image" data-idx="<?php echo $TPL_VAR["idx"]?>" class="fa fa-times" aria-hidden="true" style="position: absolute;right: 0;color: red;font-size: 2rem;margin: 0.5rem;"></i><img src="<?php echo $TPL_VAR["sub2_pic"]?>" height="150px"><input type="hidden" name="sub2_pic" value="<?php echo $TPL_VAR["sub2_pic"]?>"></div>
<?php }?>
<?php if($TPL_VAR["sub3_pic"]){?>
<div style="position: relative;display: inline-block;"><i name="btn-delete-image" data-idx="<?php echo $TPL_VAR["idx"]?>" class="fa fa-times" aria-hidden="true" style="position: absolute;right: 0;color: red;font-size: 2rem;margin: 0.5rem;"></i><img src="<?php echo $TPL_VAR["sub3_pic"]?>" height="150px"><input type="hidden" name="sub3_pic" value="<?php echo $TPL_VAR["sub3_pic"]?>"></div>
<?php }?>
<?php if($TPL_VAR["sub4_pic"]){?>
<div style="position: relative;display: inline-block;"><i name="btn-delete-image" data-idx="<?php echo $TPL_VAR["idx"]?>" class="fa fa-times" aria-hidden="true" style="position: absolute;right: 0;color: red;font-size: 2rem;margin: 0.5rem;"></i><img src="<?php echo $TPL_VAR["sub4_pic"]?>" height="150px"><input type="hidden" name="sub4_pic" value="<?php echo $TPL_VAR["sub4_pic"]?>"></div>
<?php }?>
<?php if($TPL_VAR["sub5_pic"]){?>
<div style="position: relative;display: inline-block;"><i name="btn-delete-image" data-idx="<?php echo $TPL_VAR["idx"]?>" class="fa fa-times" aria-hidden="true" style="position: absolute;right: 0;color: red;font-size: 2rem;margin: 0.5rem;"></i><img src="<?php echo $TPL_VAR["sub5_pic"]?>" height="150px"><input type="hidden" name="sub5_pic" value="<?php echo $TPL_VAR["sub5_pic"]?>"></div>
<?php }?>
<?php if($TPL_VAR["sub6_pic"]){?>
<div style="position: relative;display: inline-block;"><i name="btn-delete-image" data-idx="<?php echo $TPL_VAR["idx"]?>" class="fa fa-times" aria-hidden="true" style="position: absolute;right: 0;color: red;font-size: 2rem;margin: 0.5rem;"></i><img src="<?php echo $TPL_VAR["sub6_pic"]?>" height="150px"><input type="hidden" name="sub6_pic" value="<?php echo $TPL_VAR["sub6_pic"]?>"></div>
<?php }?>
<?php if($TPL_VAR["sub7_pic"]){?>
<div style="position: relative;display: inline-block;"><i name="btn-delete-image" data-idx="<?php echo $TPL_VAR["idx"]?>" class="fa fa-times" aria-hidden="true" style="position: absolute;right: 0;color: red;font-size: 2rem;margin: 0.5rem;"></i><img src="<?php echo $TPL_VAR["sub7_pic"]?>" height="150px"><input type="hidden" name="sub7_pic" value="<?php echo $TPL_VAR["sub7_pic"]?>"></div>
<?php }?>
<?php if($TPL_VAR["sub8_pic"]){?>
<div style="position: relative;display: inline-block;"><i name="btn-delete-image" data-idx="<?php echo $TPL_VAR["idx"]?>" class="fa fa-times" aria-hidden="true" style="position: absolute;right: 0;color: red;font-size: 2rem;margin: 0.5rem;"></i><img src="<?php echo $TPL_VAR["sub8_pic"]?>" height="150px"><input type="hidden" name="sub8_pic" value="<?php echo $TPL_VAR["sub8_pic"]?>"></div>
<?php }?>
<?php if($TPL_VAR["sub9_pic"]){?>
<div style="position: relative;display: inline-block;"><i name="btn-delete-image" data-idx="<?php echo $TPL_VAR["idx"]?>" class="fa fa-times" aria-hidden="true" style="position: absolute;right: 0;color: red;font-size: 2rem;margin: 0.5rem;"></i><img src="<?php echo $TPL_VAR["sub9_pic"]?>" height="150px"><input type="hidden" name="sub9_pic" value="<?php echo $TPL_VAR["sub9_pic"]?>"></div>
<?php }?>
<?php if($TPL_VAR["sub10_pic"]){?>
<div style="position: relative;display: inline-block;max-height:150px"><i name="btn-delete-image" data-idx="<?php echo $TPL_VAR["idx"]?>" class="fa fa-times" aria-hidden="true" style="position: absolute;right: 0;color: red;font-size: 2rem;margin: 0.5rem;"></i><img src="<?php echo $TPL_VAR["sub10_pic"]?>" height="150px"><input type="hidden" name="sub10_pic" value="<?php echo $TPL_VAR["sub10_pic"]?>"></div>
<?php }?>
</div>
</div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label">소유자명</label>
<div class="col-sm-4">
<div class="input-group">
<input type="hidden" name="owner_userno" value="<?php echo $TPL_VAR["owner_userno"]?>" class="form-control">
<input type="text" name="owner_username" value="<?php echo $TPL_VAR["owner_username"]?>" class="form-control">
<span class="input-group-addon" style="background-color: #23c6c8; color: #FFFFFF; border: 0px;  cursor: pointer" name="owner_user_change"  >변경</span>
</div>
</div>
<?php if($TPL_VAR["pack_info"]!='Y'&&$TPL_VAR["pack_info"]!='N'){?>
<label class="col-sm-2 control-label">재고번호</label>
<div class="col-sm-4">
<input type="text" name="stock_number" value="<?php echo $TPL_VAR["stock_number"]?>" class="form-control">
</div>
<?php }?>
</div>
<div class="form-group">
<label class="col-sm-2 control-label">차 이름</label>
<div class="col-sm-4">
<input type="text" name="title" value="<?php echo $TPL_VAR["title"]?>" class="form-control">
</div>
<label class="col-sm-2 control-label">구분</label>
<div class="col-sm-4">
<input type="text" name="meta_division" value="<?php echo $TPL_VAR["meta_division"]?>" class="form-control">
</div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label">타입</label>
<div class="col-sm-4">
<input type="text" name="meta_type" value="<?php echo $TPL_VAR["meta_type"]?>" class="form-control">
</div>
<label class="col-sm-2 control-label">생산</label>
<div class="col-sm-4">
<input type="text" name="meta_produce" value="<?php echo $TPL_VAR["meta_produce"]?>" class="form-control">
</div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label">기본 가격</label>
<div class="col-sm-4">
<div class="input-group">
<input type="text" name="base_price" value="<?php echo $TPL_VAR["base_price"]?>" class="form-control"><span class="input-group-addon">KRW</span>
</div>
</div>
<label class="col-sm-2 control-label">
(마크)</label>
<div class="col-sm-4">
<select name="meta_certification_mark" class="form-control">
<option value="" <?php if(!$TPL_VAR["meta_certification_mark"]){?>selected<?php }?> >인증(마크)를 선택해주세요.</option>
<?php if($TPL_certification_marks_1){foreach($TPL_VAR["certification_marks"] as $TPL_V1){?>
<option value="<?php echo $TPL_V1["idx"]?>" <?php if($TPL_V1["idx"]==$TPL_VAR["meta_certification_mark"]){?>selected<?php }?> ><?php echo $TPL_V1["title"]?></option>
<?php }}?>
</select>
</div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label">차 소개</label>
<div class="col-sm-10"><textarea name="content" class="form-control"><?php echo $TPL_VAR["content"]?></textarea></div>
</div>
<hr>
<div class="form-group">
<label class="col-sm-2 control-label">로열티</label>
<div class="col-sm-4">
<div class="input-group">
<input type="text" name="royalty" value="<?php echo $TPL_VAR["royalty"]?>" class="form-control"  maxlength="10" ><span class="input-group-addon">%</span>
</div>
</div>
<label class="col-sm-2 control-label">카테고리</label>
<div class="col-sm-4">
<select name="goods_type" class="form-control">
<option value="" <?php if(!$TPL_VAR["goods_type"]){?>selected<?php }?> >카테고리를 선택해주세요.</option>
<?php if($TPL_categories_1){foreach($TPL_VAR["categories"] as $TPL_V1){?>
<option value="<?php echo $TPL_V1["goods_type"]?>" <?php if($TPL_V1["goods_type"]==$TPL_VAR["goods_type"]){?>selected<?php }?> ><?php echo $TPL_V1["title"]?></option>
<?php }}?>
</select>
</div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label">발행 수량</label>
<div class="col-sm-4"><input type="text" name="minting_quantity" value="<?php echo $TPL_VAR["minting_quantity"]?>" class="form-control"></div>
<label class="col-sm-2 control-label">사용여부</label>
<div class="col-sm-4">
<select name="active" class="form-control">
<option value="Y" <?php if($TPL_VAR["active"]=='Y'){?>selected<?php }?> >예</option>
<option value="N" <?php if($TPL_VAR["active"]!='Y'){?>selected<?php }?>>아니요</option>
</select>
</div>
</div>
<hr>
<div class="form-group">
<label class="col-sm-2 control-label">백서</label>
</div>
<div class="form-group">
<label class="col-sm-2 control-label">분류</label>
<div class="col-sm-4">
<input type="text" name="meta_wp_class" value="<?php echo $TPL_VAR["meta_wp_class"]?>" class="form-control">
</div>
<label class="col-sm-2 control-label">원산지</label>
<div class="col-sm-4">
<input type="text" name="meta_wp_origin" value="<?php echo $TPL_VAR["meta_wp_origin"]?>" class="form-control">
</div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label">생산자</label>
<div class="col-sm-4">
<input type="text" name="meta_wp_producer" value="<?php echo $TPL_VAR["meta_wp_producer"]?>" class="form-control">
</div>
<label class="col-sm-2 control-label">생산년도</label>
<div class="col-sm-4">
<input type="text" name="meta_wp_production_date" value="<?php echo $TPL_VAR["meta_wp_production_date"]?>" class="form-control">
</div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label">향</label>
<div class="col-sm-4">
<input type="text" name="meta_wp_scent" value="<?php echo $TPL_VAR["meta_wp_scent"]?>" class="form-control">
</div>
<label class="col-sm-2 control-label">중량</label>
<div class="col-sm-4">
<input type="text" name="meta_wp_weight" value="<?php echo $TPL_VAR["meta_wp_weight"]?>" class="form-control">
</div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label">맛</label>
<div class="col-sm-4">
<textarea name="meta_wp_taste" class="form-control"><?php echo $TPL_VAR["meta_wp_taste"]?></textarea>
</div>
<label class="col-sm-2 control-label">마시는 방법</label>
<div class="col-sm-4">
<textarea name="meta_wp_drink_method" class="form-control"><?php echo $TPL_VAR["meta_wp_drink_method"]?></textarea>
</div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label">보관방법/유통기한</label>
<div class="col-sm-4">
<textarea name="meta_wp_keep_method" class="form-control"><?php echo $TPL_VAR["meta_wp_keep_method"]?></textarea>
</div>
<label class="col-sm-2 control-label">스토리</label>
<div class="col-sm-4">
<textarea name="meta_wp_story" class="form-control"><?php echo $TPL_VAR["meta_wp_story"]?></textarea>
</div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label">티마스터 품평</label>
<div class="col-sm-4">
<textarea name="meta_wp_teamaster_note" class="form-control"><?php echo $TPL_VAR["meta_wp_teamaster_note"]?></textarea>
</div>
<label class="col-sm-2 control-label">생산자 노트</label>
<div class="col-sm-4">
<textarea name="meta_wp_producer_note" class="form-control"><?php echo $TPL_VAR["meta_wp_producer_note"]?></textarea>
</div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label">평점</label>
<div class="col-sm-4">
<textarea name="meta_wp_grade" class="form-control"><?php echo $TPL_VAR["meta_wp_grade"]?></textarea>
</div>
<label class="col-sm-2 control-label">품종</label>
<div class="col-sm-4">
<input type="text" name="meta_wp_kind" value="<?php echo $TPL_VAR["meta_wp_kind"]?>" class="form-control">
</div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label">입체스캔</label>
<div class="col-sm-4">
</div>
<label class="col-sm-2 control-label">차수령</label>
<div class="col-sm-4">
<input type="text" name="meta_wp_second_order" value="<?php echo $TPL_VAR["meta_wp_second_order"]?>" class="form-control">
</div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label">스캔 파일</label>
<div class="col-sm-4">
<input type="hidden" name="animation" value="<?php echo $TPL_VAR["animation"]?>" class="form-control">
<input type="text" name="animation_url" value="" class="form-control" placeholde="스캔 파일 URL을 입력해주세요.">
<input type="file" name="animation_file" value="" class="form-control" placeholde="스캔 파일을 선택해주세요.">
<ul>
<li>입체 스캔 파일을 선택해 업로드하거나 URL을 입력해주세요.</li>
<li>지원되는 파일 형식 : MP4</li> <!-- GLB 나중에 -->
<li>최대 크기 : 50 MB.</li>
</ul>
</div>
<label class="col-sm-2 control-label">포장여부</label>
<div class="col-sm-4">
<input type="text" name="meta_wp_pojang" value="<?php echo $TPL_VAR["meta_wp_pojang"]?>" class="form-control">
</div>
<label class="col-sm-2 control-label">진기</label>
<div class="col-sm-4">
<input type="text" name="meta_wp_jingi" value="<?php echo $TPL_VAR["meta_wp_jingi"]?>" class="form-control">
</div>
<label class="col-sm-2 control-label">차나무</label>
<div class="col-sm-4">
<input type="text" name="meta_wp_chanamu" value="<?php echo $TPL_VAR["meta_wp_chanamu"]?>" class="form-control">
</div>
<div class="col-sm-6" style="overflow-x:auto;overflow-y:none">
<div style="white-space: nowrap" id="box_animation">
<?php if($TPL_VAR["animation"]){?>
<div style="position: relative;display: inline-block;max-height:200px">
<i name="btn-delete-image" data-idx="<?php echo $TPL_VAR["idx"]?>" data-url="<?php echo $TPL_VAR["animation"]?>" class="fa fa-times" aria-hidden="true" style="position: absolute;right: 0;color: red;font-size: 2rem;margin: 0.5rem;z-index:10;"></i>
<?php if(strpos($TPL_VAR["animation"],'.gif')!==false){?>
<img src="<?php echo $TPL_VAR["animation"]?>" height="200px"/>
<?php }elseif(strpos($TPL_VAR["animation"],'.mp4')!==false){?>
<video controls="" controlslist="nodownload" autoplay="" loop="" preload="auto" src="<?php echo $TPL_VAR["animation"]?>" height="200px"></video>
<?php }else{?>
<iframe src="<?php echo $TPL_VAR["animation"]?>" height="200px" style="border:0;"></iframe>
<?php }?>
</div>
<?php }?>
</div>
</div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label">가격동향</label>
<div class="col-sm-4">
<input type="text" name="meta_wp_price_trend" value="<?php echo $TPL_VAR["meta_wp_price_trend"]?>" class="form-control">
</div>
<label class="col-sm-2 control-label">가치성</label>
<div class="col-sm-4">
<input type="text" name="meta_wp_valueability" value="<?php echo $TPL_VAR["meta_wp_valueability"]?>" class="form-control">
</div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label">희소성</label>
<div class="col-sm-4">
<input type="text" name="meta_wp_scarcity" value="<?php echo $TPL_VAR["meta_wp_scarcity"]?>" class="form-control">
</div>
<label class="col-sm-2 control-label">대중성</label>
<div class="col-sm-4">
<input type="text" name="meta_wp_popular" value="<?php echo $TPL_VAR["meta_wp_popular"]?>" class="form-control">
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