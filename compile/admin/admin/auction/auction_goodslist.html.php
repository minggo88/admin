<?php /* Template_ 2.2.6 2022/10/27 11:14:34 /home/ubuntu/www/admin/www/template/admin/admin/auction/auction_goodslist.html 000002618 */ ?>
<div class="wrapper search-wrapper-content animated fadeInRight">
<div class="row">
<div class="col-lg-12">
<div class="ibox ">
<div class="ibox-title">
<h5 id="page_title"> Auction 상품 목록</h5>
<div class="float-right">
<a href="?pg_mode=excel-upload" class="btn btn-primary btn-xs" name="btn-add">상품대량등록</a>
<a href="?pg_mode=write" class="btn btn-primary btn-xs" name="btn-add">상품등록</a>
</div>
</div>
<div class="ibox-content">
<div id="box_list" class="">
<div>
<table class="table table-striped table-bordered table-hover dataTables-auctionGoods">
<!-- <colgroup>
<col width="70"></col>
<col width="80"></col>
<col width="50"></col>
<col width="*"></col>
<col width="70"></col>
<col width="70"></col>
<col width="70"></col>
<col width="80"></col>
<col width="80"></col>
<col width="60"></col>
<col width="60"></col>
<col width="100"></col>
</colgroup> -->
<thead>
<tr>
<th class="text-center">상품번호</th>
<th class="text-center">종류</th>
<th class="text-center">상품이미지</th>
<th class="text-center">상품종류</th>
<th class="text-center">상품등급</th>
<th class="text-center">상품이름</th>
<th class="text-center">상품가격</th>
<th class="text-center">소유자명</th>
<th class="text-center">경매이름</th>
<th class="text-center">최고입찰가격</th>
<th class="text-center">최고입찰회원</th>
<th class="text-center">시작날짜</th>
<th class="text-center">종료날짜</th>
<th class="text-center">기능</th>
</tr>
</thead>
<tfoot>
<tr>
<th class="text-center">상품번호</th>
<th class="text-center">종류</th>
<th class="text-center">상품종류</th>
<th class="text-center">상품이미지</th>
<th class="text-center">상품등급</th>
<th class="text-center">상품이름</th>
<th class="text-center">상품가격</th>
<th class="text-center">소유자명</th>
<th class="text-center">경매이름</th>
<th class="text-center">최고입찰가격</th>
<th class="text-center">최고입찰회원</th>
<th class="text-center">시작날짜</th>
<th class="text-center">종료날짜</th>
<th class="text-center">기능</th>
</tr>
</tfoot>
</table>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<script type="text/javascript">
function popup_detail(auction_idx, auction_title){
var win = window.open("?pg_mode=historyApplyLists&auction_idx="+auction_idx+"&auction_title="+auction_title, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,top=100,left=650,width=600,height=800");
}
</script>