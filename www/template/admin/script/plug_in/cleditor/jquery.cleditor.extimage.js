/**
 @preserve CLEditor Image Upload Plugin v1.0.0
 http://premiumsoftware.net/cleditor
 requires CLEditor v1.3.0 or later
 
 Copyright 2011, Dmitry Dedukhin
 Plugin let you either to upload image or to specify image url.
*/

(function($) {
	var hidden_frame_name = '__upload_iframe';
	// Define the image button by replacing the standard one
	$.cleditor.buttons.image = {
		name: 'image',
		title: 'Insert/Upload Image',
		command: 'insertimage',
		popupName: 'image',
		popupClass: 'cleditorPrompt',
		stripIndex: $.cleditor.buttons.image.stripIndex,
		popupContent:
			'<table cellpadding="0" cellspacing="0">' +
			'<tr><td>이미지 화일 선택 :</td></tr>' +
			'<tr><td> ' +
			'<form method="post" enctype="multipart/form-data" name="cleditorUploadform" id="cleditorUploadform" action="">' +
			'<input id="imageName" name="imageName" type="file" /></form> </td></tr>' +
			'<tr><td>또는 이미지 경로 :</td></tr>' +
			'<tr><td><input type="text" size="40" name="imageUrl" value="" /></td></tr>' +
			'</table><input type="button" value="이미지 삽입">',
		buttonClick: imageButtonClick,
		uploadUrl: '/cleditor/upload.php' // default url
	};

	function closePopup(editor) {
		editor.hidePopups();
		editor.focus();
	}

	function imageButtonClick(e, data) {
		var editor = data.editor,
			$text = $(data.popup).find(':text'),
			url = $.trim($text.val()),
			$iframe = $(data.popup).find('iframe'),
			$file = $(data.popup).find(':file');

		$('#cleditorUploadform')[0].reset();
		$text.val('');

		$(data.popup)
			.children(":button")
			.unbind("click")
			.bind("click", function(e) {
				if($file.val()) {
					$(data.popup).find('form').attr('action', $.cleditor.buttons.image.uploadUrl).ajaxSubmit({
						success: function (data, statusText) {
							if(data['bool']) {
								editor.execCommand("insertimage",data['msg'], null, null);
								editor.focus();
								closePopup(editor);
							}
							else {
								alert('파일 업로드에 실패했습니다.');
								editor.focus();
								closePopup(editor);
							}
						},
						dataType:'json',
						resetForm: false
					});					
				} else if ($text.val() != '') {
					editor.execCommand("insertimage",$text.val(), null, null);
					closePopup(editor);
				}
			});
	}
})(jQuery);