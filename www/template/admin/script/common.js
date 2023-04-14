$(document).ready(function(){

	//gnb 클릭시
	$("#gnb > ul > li > a").click(function(){
		var w_width = $(window).width();
		var w_width1 = $(document).width();
		var $gnb_class = $("#gnb").attr("class");

		if($gnb_class == "gnb_web"){
			var open_class = $(this).attr("class");
			var td_width = $(this).siblings("ul");

			if(open_class == "open on"){
				$(this).removeClass("on");
				$("#header").stop().animate({"width":"75"},500);
				//$("#gnb").stop().animate({"width":"75"},500);
				$(".two_depth").stop().animate({"width":"0"},500);
				var $sc_width = w_width - 75;
				if ($sc_width > 1100)
				{
					$sc_width = 1100;
				} else {
					$sc_width = $sc_width;
				}
				$("#sub_container").stop().animate({"width" : $sc_width},500);
				$("#main_container").stop().animate({"width" : $sc_width},500);
			}else{	
				$(".gnb_web > ul > li > .open").removeClass("on");
				$(this).addClass("on");
				$("#header").stop().animate({"width":"202"},500);
				//$("#gnb").stop().animate({"width":"202"},500);
				$(".two_depth").stop().animate({"width":"0"},500);
				$(this).siblings(".two_depth").stop().animate({"width":"127px"},500);
				var $sc_width = w_width - 202;
				if ($sc_width > 1100)
				{
					$sc_width = 1100;
				} else {
					$sc_width = $sc_width;
				}
				$("#sub_container").stop().animate({"width" : $sc_width},500);
				$("#main_container").stop().animate({"width" : $sc_width},500);
			}
			if (document.getElementById("flexible_gallery") != null) {
				setTimeout(function(){$box.masonry()}, 500);
			}
		}else if($gnb_class == "gnb_mob"){
			var $length = $(this).siblings("ul").children("li").length;
			var $height = $length * 48;
			$(".two_depth").stop().animate({"height" : "0"},200);
			$(this).siblings("ul").stop().animate({"height" : $height},200);
			alert('test4');
		}
	});
	$(".gnb_btn").toggle(
		function(){
			$(this).parents("#gnb").stop().animate({"left":"0"},200);
		},function(){
			$(this).parents("#gnb").stop().animate({"left":"-127"},200);		
		}
	);

	//story_new_list 버튼 클릭시
	$(".btn_sn a").click(function(){
		$(".btn_sn a").removeClass("now_page");
		$(this).addClass("now_page");
	});

	$(".one_depth li a").mouseover(function(){	
		$(this).children("img").attr("src", $(this).children("img").attr("src").replace("_off.png", "_on.png"));
	});
	$(".one_depth li a").mouseout(function(){
		$(this).children("img").attr("src", $(this).children("img").attr("src").replace("_on.png", "_off.png"));
	});

	$(".one_btn li").mouseover(function(){	
		$(this).children("img").attr("src", $(this).children("img").attr("src").replace("_off.png", "_on.png"));
	});
	$(".one_btn li").mouseout(function(){
		$(this).children("img").attr("src", $(this).children("img").attr("src").replace("_on.png", "_off.png"));
	});

	$(".btn_social a").mouseover(function(){	
		$(this).children("img").attr("src", $(this).children("img").attr("src").replace(".gif", "_on.gif"));
	});
	$(".btn_social a").mouseout(function(){
		$(this).children("img").attr("src", $(this).children("img").attr("src").replace("_on.gif", ".gif"));
	});

	//location 모바일 버튼 클릭시
	$(".sms").click(function(){
		$(".web_sms").css("display", "block");
		$(".web_sms").focus();
		$(".way_list1").css("margin", "60px 0 60px")
	});
	$(".ws_close").click(function(){
		$(".web_sms").css("display", "none");
		$(".way_list1").css("margin", "15px 0 60px")
	});
	
	/*
	$(".select02").change(function(){
		var $checkbox = document.getElementsByName("category_temp");
		var $cb_leng = $checkbox.length;
		var $val = $(this).val().split("::");
		var $val3 = $val[0];
		var $val4 = $val[1];
		var $checked;
		if($val4 == ""){return false;}
		for (i=0; i < $cb_leng ; i++){
			var $val2 = $checkbox[i].value;
			if ($val4 == $val2){
				$checked = $checkbox[i].checked;
				if($checked != true){
					$checkbox[i].checked = true;
				}
			}		
		}
		$val3 = $val3 + " X";
		var $layout = "<a href='#'>"+ $val3 +"</a>"
		if($checked != true){
			$(".item_list").append($layout);
		}	
		$(".item_list a").click(function(){
			
			$("input[value="+ $val4 +"]:checkbox").each(function() {
				$(this).attr("checked", false);
			});
			$(this).remove();
		});
	});
	*/
	
	/*
	$(".top_search_type01 > ul > li > ul > li > a").click(function(){
		var $check = $(this).siblings("input").attr("checked");
		var $c_text = $(this).text();
		var $clone = $(this).clone();
		$c_text = $c_text + " X";
		$clone.text($c_text);
		if ($check == "checked"){
			return false;
		}
		else{
			$clone.appendTo(".item_list");
		}
		$(this).siblings("input").click();
		$(".item_list a").click(function(){
			var $id = $(this).attr("id");
			$(this).remove();
			$("#menu"+$id).attr("checked", false);
		});
	});

	$(".item_list a").click(function(){
		var $id = $(this).attr("id");
		$(this).remove();
		$("#menu"+$id).attr("checked", false);
	});
	$(".tst_total").click(function(){
		$(location).attr("href", "portfolio_list.asp");
	});
		
	$(".pf_list li a").mouseover(function(){
		var $now_pclass = $(this).attr("class");
		if($now_pclass == "td_on"){
		}else{
			$(this).children("img").attr("src", $(this).children("img").attr("src").replace(".jpg", "_over.jpg"));
		}
	});
	$(".pf_list li a").mouseout(function(){
		var $now_pclass = $(this).attr("class");
		if($now_pclass == "td_on"){
		}else{
			$(this).children("img").attr("src", $(this).children("img").attr("src").replace("_over.jpg", ".jpg"));
		}
	});
	*/

	//메인 비쥬얼
	$(".img03_content div").each(function(){
		$(this).children("a").click(function(){
			var src = $(this).children(".content_ico").attr("src").split("_");
			var src_gif = src[3];
			if(src_gif  ==  "on.gif"){
				$(this).children(".content_ico").attr("src", $(this).children(".content_ico").attr("src").replace("_on.gif", "_off.gif"));
				$(this).children(".content_text").attr("src", $(this).children(".content_text").attr("src").replace("_on.gif", ".gif"));
			}
		});
		$(this).children("a").mouseover(function(){
			$(this).children(".content_ico").attr("src", $(this).children(".content_ico").attr("src").replace("_off.gif", "_on.gif"));
			$(this).children(".content_text").attr("src", $(this).children(".content_text").attr("src").replace(".gif", "_on.gif"));
		});
		$(this).children("a").mouseout(function(){
			$(this).children(".content_ico").attr("src", $(this).children(".content_ico").attr("src").replace("_on.gif", "_off.gif"));
			$(this).children(".content_text").attr("src", $(this).children(".content_text").attr("src").replace("_on.gif", ".gif"));
		});
	});

	//포트폴리오 리스트 web 버전 검색
	/*
	$(".top_search_type01 > ul > li > a").click(function(){
		$(".top_search_type01 ul li ul").css("display", "none");
		$(this).siblings("ul").css("display", "block");
		$(".top_search_type01 > ul > li > a").css("color", "#626262"); 
		$(this).css("color", "#b6d4cc");
	});
	*/

});

$(window).load(function(){

	//Start
	$("#gnb > ul > li > a").each(function(){
		var $id = $(this).attr("id");
		if($id == "od_on"){
			$(this).next("ul").css("z-index", "100");
			$(this).children("img").attr("src", $(this).children("img").attr("src").replace("_off.png", "_on.png"));
		} else {
			$(this).children("img").attr("src", $(this).children("img").attr("src").replace("_on.png", "_off.png"));
		}
	});

	//Two_depth
	$(".pf_list li").each(function(){
		var $pf_class = $(this).children("a").attr("class");
		if($pf_class == "td_on"){
			$(this).children("a").children("img").attr("src", $(this).children("a").children("img").attr("src").replace(".jpg", "_on.jpg"));
		}
	});

	//List
	$("#gnb > ul > li > a").click(function(){
		if(document.getElementById("flexible_gallery") != null) {
			setTimeout(function(){$box.masonry()}, 500);
		}
	});

	

	var $box = $("#flexible_gallery");
	$(window).resize(function(){

		var w_width3 = $(window).width();
		var w_height2 = $(window).height();
		var d_height2 = $(document).height();
		if (w_height2 < d_height2){
			w_width3 = w_width3 + 17;
		}

		var cont_class = $(".contents").attr("class");
		if(w_width3 >= 1280)
		{
			var w_width4 = $(window).width();
			var w_height3 = $(window).height();
			var d_height3 = $(document).height();
			if (w_height3 < d_height3){
				w_width4 = w_width4 + 17;
			}
			var main_container_width_75 = w_width3 - 92;
			var main_container_width_202 = w_width3 - 219;

			//alert(w_width3 +'/'+w_width4 +'/'+main_container_width_75 +'/'+main_container_width_202);
			//gnb web css
			$("#gnb").removeClass("gnb_mob");
			$("#gnb").addClass("gnb_web");
			$("#gnb").css("left", "0");
			$("#od_on").siblings("ul").css("z-index", "1000");
			$(".two_depth").css("height", "100%");
			$(".two_depth").css("width", "0");
			var open_class2 = $("#header").attr("class");
			var open_flyer = $("#menu_container").attr("class");

			if(open_class2 == "main_header"){
				$("#header").css("width", "75");
				$("#gnb").css("width", "75");
				$(".two_depth").css("width", "0");
				//$("#main_container").css("width", main_container_width_75);
				//$(".flyer_header").css("display", "block");
			}else{	
				$("#header").css("width", "202");
				$("#gnb").css("width", "202");
				$(".two_depth").css("width", "127");
				//$("#main_container").css("width", main_container_width_75);
				//alert('test2');
			}
			
			/*
			if(open_flyer == "flyer_header"){
				$("#header").css("width", "75");
				$("#gnb").css("width", "75");
				$(".flyer_header").css("display", "block");
				$(".two_depth").css("width", "0");
			}else{	
				$("#header").css("width", "202");
				$("#gnb").css("width", "202");
				$(".two_depth").css("width", "127");
			}
			*/
			
			$(".main_header").css("width", "75");

			// 비쥬얼
			$(".hv_wrap div").stop().animate({'padding-top' : 0, 'padding-right' : "5.6%", 'padding-bottom' : 0, 'padding-left' : "5.6%"}, 500);

			//work 비쥬얼
			$(".dv_wrap div").stop().animate({'padding-top' : 0, 'padding-right' : "7%", 'padding-bottom' : 0, 'padding-left' : "7%"}, 500);
			$(".av_wrap div").stop().animate({'padding-top' : 0, 'padding-right' : "6.3%", 'padding-bottom' : 0, 'padding-left' : "6.3%"}, 500);
			$(".mv_wrap div").stop().animate({'padding-top' : 0, 'padding-right' : "7%", 'padding-bottom' : 0, 'padding-left' : "7%"}, 500);
			$(".cv_wrap div").stop().animate({'padding-top' : 0, 'padding-right' : "1.6%", 'padding-bottom' : 0, 'padding-left' : "1.6%"}, 500);
			$(".creative_img06").stop().animate({'padding-top' : 0, 'padding-right' : "1.8%", 'padding-bottom' : 0, 'padding-left' : "4.5%"}, 500);
				
			//윈도우 리사이즈시 레이아웃
			var $h_class = $("#header").attr("class");
			if($h_class != "policy_header"){
				var $sc_width2 = w_width4 - 202;
				if ($sc_width2 > 1100)
				{
					$sc_width2 = 1100;
				} else {
					$sc_width2 = $sc_width2;
				}

				$("#sub_container").width($sc_width2);
				$("#subtitle_image").width($sc_width2);
				$("#subtitle_image img").css("width", $sc_width2);
				$("#footer").width($sc_width2);
			}else{
				var $pc_width = w_width4 - 202;
				if ($pc_width > 1100)
				{
					$pc_width = 1100;
				} else {
					$pc_width = $pc_width;
				}
				$("#sub_container").width($pc_width);
				$("#subtitle_image").width($pc_width);
				$("#subtitle_image img").css("width", $pc_width);
				$("#footer").width($pc_width);
			}

			$("#wrap").css("visibility", "visible");

			if(w_width4 >= 1921){ // 윈도우창의 크기가 1920보다 클 경우
				var img03_width = $("#main_container").width()-75;
				var img03_img = img03_width / 100

				$("#main_container").css("width", main_container_width_75);

				$(".img03_visual img").css("width", img03_width);
				$(".img02_visual img").css("width", img03_width);
				$(".img02_visual2").css("width", img03_width);
				$(".img02_visual3 img").css("width", "1452px");
				$(".img02_visual3").css("width", img03_width);

			}else{ // 윈도우창의 크기가 1280보다 크고 1920보다 작을경우


				var img03_width = $("#main_container").width() -75;
				var img03_img = img03_width / 100;

				$("#main_container").css("width", main_container_width_75);
				
				$(".img03_visual img").css("width", img03_width);
				$(".img02_visual img").css("width", img03_width);
				$(".img02_visual2 img").css("width", img03_width);
				$(".img02_visual3").css("width", img03_width);
				$(".img02_visual3 img").css("width", img03_img * 85);

			}
		}else{

			//alert('test1');
			//gnb web css
			$("#gnb").removeClass("gnb_web");
			$("#gnb").addClass("gnb_mob");
			$("#gnb").css("left", "-202px");
			$("#gnb").css("width", "75px");
			$("#sub_container").css("width", "100%");
			$(".two_depth").css("height", "0");
			$(".two_depth").css("width", "127");
			$("#header").css("width", "100%");

				$("#main_container").css("width", main_container_width_202);

			// 비쥬얼
			$(".hv_wrap div").stop().animate({'padding-top' : 0, 'padding-right' : "5.6%", 'padding-bottom' : 0, 'padding-left' : "5.6%"}, 500);

			//work 비쥬얼
			$(".dv_wrap div").stop().animate({'padding-top' : 0, 'padding-right' : "7%", 'padding-bottom' : 0, 'padding-left' : "7%"}, 500);
			$(".av_wrap div").stop().animate({'padding-top' : 0, 'padding-right' : "6.3%", 'padding-bottom' : 0, 'padding-left' : "6.3%"}, 500);
			$(".mv_wrap div").stop().animate({'padding-top' : 0, 'padding-right' : "7%", 'padding-bottom' : 0, 'padding-left' : "7%"}, 500);
			$(".cv_wrap div").stop().animate({'padding-top' : 0, 'padding-right' : "1.6%", 'padding-bottom' : 0, 'padding-left' : "1.6%"}, 500);
			$(".creative_img06").stop().animate({'padding-top' : 0, 'padding-right' : "1.8%", 'padding-bottom' : 0, 'padding-left' : "4.5%"}, 500);
			
			var w_width5 = $(window).width();

			var w_width5 = $(window).width();
			var w_height4 = $(window).height();
			var d_height4 = $(document).height();
			if (w_height4 < d_height4){
				w_width5 = w_width5 + 17;
			}
			if(w_width5 >= 1024 && w_width5 <= 1279 ){ // 윈도우창의 크기가 1024보다 클 경우
				
				$("#main_container").css("height", "768px");
				//메인 비쥬얼
				var img03_width = $("#main_container").width();
				var img03_img = img03_width / 100
				$(".img03_visual img").css("width", img03_width);
				$(".img02_visual img").css("width", img03_width);
				$(".img02_visual2 img").css("width", img03_width);
				$(".img02_visual3 img").css("width", img03_img * 80);

			}else{
				var mobile_img = $(".mobile_img").height();
				$("#main_container").css("height", mobile_img);
			}

			$("#wrap").css("visibility", "visible");

		}
		if (document.getElementById("flexible_gallery") != null) {
			setTimeout(function(){$box.masonry()}, 500);
		}
	}).resize();
});


/* masonry */
if (document.getElementById("flexible_gallery") != null) {
	$(function(){
		$('#flexible_gallery').masonry({
			itemSelector: '.item',
			isAnimated: true
		});
	});
}

/* 로딩박스 */
function loadingBox(){
	$(".loadingBox").css("display", "block");
}
